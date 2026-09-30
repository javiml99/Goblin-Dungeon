"""Exercise real HTTP, cookies, POST redirects, CSRF and packaged PHP."""
import argparse
import html
import http.cookiejar
import os
from pathlib import Path
import re
import socket
import subprocess
import tempfile
import time
import urllib.error
import urllib.parse
import urllib.request

args = argparse.ArgumentParser()
args.add_argument("--php", default="php")
args.add_argument("--ini")
args.add_argument("--ext")
args.add_argument("--root")
opt = args.parse_args()
root = Path(opt.root).resolve() if opt.root else Path(__file__).resolve().parents[1]

def exercise(local):
    with tempfile.TemporaryDirectory(prefix="goblin ~ http ") as tmp:
        with socket.socket() as s:
            s.bind(("127.0.0.1", 0))
            port = s.getsockname()[1]
        base = f"http://127.0.0.1:{port}"
        env = os.environ.copy()
        env.update(GOBLIN_LOCAL="1" if local else "0", GOBLIN_DATA_DIR=tmp,
                   GOBLIN_SESSION_DIR=tmp)
        env.pop("GOBLIN_ERROR_LOG", None)
        cmd = [opt.php]
        if opt.ini: cmd += ["-c", opt.ini]
        if opt.ext: env["GOBLIN_EXTENSION_DIR"] = opt.ext
        cmd += ["-d", "display_errors=0", "-d", "log_errors=1",
                "-d", "error_reporting=32767",
                "-S", f"127.0.0.1:{port}", "-t", str(root / "GoblinDungeon"), str(root / "router.php")]
        logpath = Path(tmp) / "server.log"
        with logpath.open("w+") as log:
            proc = subprocess.Popen(cmd, cwd=root, env=env, stdout=log, stderr=log)
            jar = http.cookiejar.CookieJar()
            browser = urllib.request.build_opener(urllib.request.ProxyHandler({}), urllib.request.HTTPCookieProcessor(jar))
            def req(path, data=None, headers=None):
                body = None if data is None else urllib.parse.urlencode(data).encode()
                try:
                    response = browser.open(urllib.request.Request(base + path, data=body, headers=headers or {}), timeout=5)
                except urllib.error.HTTPError as error:
                    response = error
                with response:
                    return response.status, response.read().decode("utf-8", errors="replace"), response.geturl()
            def fields(page):
                return {k: html.unescape(v) for k, v in re.findall(r'name="(csrf|turn_token)" value="([^"]*)"', page)}
            try:
                for _ in range(80):
                    if proc.poll() is not None: raise RuntimeError("PHP exited: " + logpath.read_text())
                    try:
                        status, page, url = req("/")
                        break
                    except (OSError, urllib.error.URLError):
                        time.sleep(.1)
                else: raise RuntimeError("PHP did not start")
                assert status == 200
                assert req("/Modelos/DAO.class.php")[0] == 404
                assert req("/csv/autenticacion2.csv")[0] == 404
                assert req("/../var/goblin.sqlite")[0] == 404
                assert req("/bootstrap.php")[0] == 404
                assert req("/estilos/maquetacio.css")[0] == 200
                assert req("/GoblinDungeon.php", {"accion": "1"})[0] == 403
                if local:
                    assert "Entra en Goblin Dungeon" in page
                    assert req("/", headers={"Host": "evil.example"})[0] == 403
                else:
                    assert url.endswith("/login.php")
                    status, page, _ = req("/rexistro.php")
                    data = fields(page)
                    data.update(nombre="httpuser", contrasena="Pass & <123>", contrasena2="Pass & <123>", email="")
                    status, page, url = req("/rexistro.php", data)
                    assert status == 200 and url.endswith("/GoblinDungeon.php")
                assert req("/usuarios.php")[0] == 403
                assert req("/borrar.php", dict(fields(page), nombre="httpuser"))[0] == 403
                data = dict(fields(page), enviar="1", nombre="<script>boom</script>", clase="picaro")
                status, page, _ = req("/GoblinDungeon.php", data)
                assert status == 200 and "&lt;script&gt;boom&lt;/script&gt;" in page
                assert "<script>boom</script>" not in page
                data = dict(fields(page), accion="1")
                status, page, _ = req("/GoblinDungeon.php", data)
                assert "Goblin Novato" in page
                # Replay exact same POST: same state, no second turn.
                repeated = req("/GoblinDungeon.php", data)[1]
                assert repeated == page
                page = req("/GoblinDungeon.php", dict(fields(page), accion="1"))[1]
                assert "Has vencido al Goblin Novato" in page
                page = req("/GoblinDungeon.php", dict(fields(page), accion="1"))[1]
                # Events and stats must stay unchanged across refresh.
                assert req("/GoblinDungeon.php")[1] == page
                assert req("/GoblinDungeon.php")[1] == page
                status, profile, _ = req("/perfil.php")
                assert status == 200 and "personajes ganadores" in profile
                assert req("/cerrar.php")[0] == 405
                if not local:
                    req("/cerrar.php", fields(profile))
                    page = req("/login.php")[1]
                    status, page, url = req("/login.php", dict(fields(page), nombre="httpuser", contrasena="Pass & <123>"))
                    assert status == 200 and url.endswith("/GoblinDungeon.php")
                    # Login clears previous account's in-session character.
                    assert "Entra en Goblin Dungeon" in page
                print("PASS HTTP", "local" if local else "web")
            finally:
                proc.terminate()
                proc.wait(timeout=10)
                log.flush()
                logs = logpath.read_text(errors="replace")
                assert not re.search(r"PHP(?::| (Warning|Fatal error|Deprecated|Parse error))", logs), logs
exercise(True)
exercise(False)
