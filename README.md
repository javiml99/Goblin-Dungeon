# Goblin Dungeon

Videojuego web de combates por turnos creado por **Francisco Javier Muiños López**,
con PHP, clases de personajes y enemigos, encuentros aleatorios e inventario.
Conserva las mecánicas e imágenes del proyecto original de 2022.

## Jugar en Windows

La distribución preparada por GitHub Actions se llama **GoblinDungeon-Windows.zip**.

1. Descarga el ZIP de una [Release](https://github.com/javiml99/Goblin-Dungeon/releases).
2. Extrae **todos** los archivos.
3. Abre **GoblinDungeon.exe**. El juego se abre en tu navegador.
4. Mantén el lanzador abierto mientras juegas; ciérralo al terminar.

Requiere Windows 10/11 x64 con su .NET Framework incluido. El paquete lleva PHP,
SQLite y las DLL necesarias: el jugador no instala XAMPP, PHP ni MySQL.
Durante la revisión de esta versión, los ZIP de prueba están en la sección
**Artifacts** de [Actions](https://github.com/javiml99/Goblin-Dungeon/actions).
Esos artefactos requieren iniciar sesión en GitHub y caducan; una Release pública
es la descarga definitiva.

Se entra directamente como jugador local. Las cuentas opcionales y victorias
están en `%LOCALAPPDATA%\GoblinDungeon`, separadas de los archivos del juego.
Las partidas en curso dependen de la sesión; aún no hay guardado completo
independiente del navegador. Las cuentas locales y web no se sincronizan.

## Desarrollo

PHP 8.4 con PDO SQLite y Python 3 para las pruebas HTTP.

```sh
php -S 127.0.0.1:8765 -t GoblinDungeon router.php
```

Abre http://127.0.0.1:8765 y registra una cuenta nueva.
No se importan las cuentas de prueba antiguas. Para convertir una cuenta existente
en administradora desde la consola:

```sh
php tools/admin.php nombre_de_usuario
```

Para activar el acceso local automático, establece `GOBLIN_LOCAL=1` en el entorno.
No actives esa variable en un servidor público.

```sh
php tests/regression.php
python3 tests/http_smoke.py
```

## Arquitectura y cambios

- **Xogador / Goblin / Partida:** clases originales y reglas del combate.
- **funciones.php:** transiciones del juego. Los eventos se aplican una vez por turno.
- **GoblinDungeon.php:** formularios POST, redirección y presentación; GET no da premios.
- **DAO:** PDO SQLite para cuentas y victorias; consultas preparadas e inventario JSON.
- **bootstrap.php:** sesión, CSRF, permisos y escape HTML.
- **desktop:** lanzador C# y compilación reproducible con PHP verificado por SHA256.
- **tests:** combate, victoria, inventario, cuentas, HTTP, permisos y repetición de peticiones.

Se corrigen contraataques de enemigos muertos, defensa que curaba al jugador,
la armadura mejorada, eventos repetidos al refrescar, rutas rotas, salidas HTML sin
escapar y borrado sin permisos. Se conservan avatares y preferencias.
Las contraseñas usan `password_hash`/`password_verify`; no se publican cuentas de
demostración en las distribuciones nuevas. Los CSV antiguos siguen en el historial
Git, pero no se usan ni se incluyen en la nueva versión.

## Compilar y publicar

El workflow comprueba Linux y Windows y produce el ZIP. En Windows con Visual
Studio Build Tools (C++), Python y acceso a Internet: `./desktop/build.ps1`.
El script descarga la versión de PHP fijada y compila el lanzador.

Al crear una etiqueta `v*`, el workflow crea una **Release en borrador** con el ZIP
y su SHA256. Revisar/probar el ZIP antes de publicar la Release.
El ejecutable no está firmado comercialmente.

## Web y dominio

Consulta [la guía de despliegue](docs/DEPLOYMENT.md).
[Componentes y recursos](docs/THIRD-PARTY.md).
