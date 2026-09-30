using System;
using System.Diagnostics;
using System.Drawing;
using System.IO;
using System.Net;
using System.Net.Sockets;
using System.Runtime.InteropServices;
using System.Threading.Tasks;
using System.Windows.Forms;

class Launcher : Form
{
    bool smoke = Array.IndexOf(Environment.GetCommandLineArgs(), "--smoke-test") >= 0;
    Process server;
    IntPtr job;
    string url;
    string data;
    Button open = new Button { Text = "Abrir juego", Enabled = false, Dock = DockStyle.Top, Height = 48 };
    Label status = new Label { Text = "Preparando la mazmorra...", Dock = DockStyle.Fill, TextAlign = ContentAlignment.MiddleCenter };

    [STAThread]
    static void Main()
    {
        Application.EnableVisualStyles();
        Application.SetCompatibleTextRenderingDefault(false);
        using (var form = new Launcher()) Application.Run(form);
    }
    Launcher()
    {
        Text = "Goblin Dungeon";
        ClientSize = new Size(440, 190);
        Controls.Add(status);
        Controls.Add(open);
        var close = new Button { Text = "Cerrar juego y servidor", Dock = DockStyle.Bottom, Height = 44 };
        Controls.Add(close);
        close.Click += (s, e) => Close();
        open.Click += (s, e) => OpenBrowser();
        Shown += async (s, e) => await StartGame();
        FormClosed += (s, e) => StopServer();
    }
    void OpenBrowser()
    {
        try { Process.Start(new ProcessStartInfo(url) { UseShellExecute = true }); }
        catch { MessageBox.Show("Abre esta dirección en tu navegador:\n" + url); }
    }
    async Task StartGame()
    {
        try
        {
            string root = AppDomain.CurrentDomain.BaseDirectory;
            data = Path.Combine(Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData), "GoblinDungeon");
            Directory.CreateDirectory(data);
            Directory.CreateDirectory(Path.Combine(data, "sessions"));
            string ready = Guid.NewGuid().ToString("N");
            var socket = new TcpListener(IPAddress.Loopback, 0);
            socket.Start();
            int port = ((IPEndPoint)socket.LocalEndpoint).Port;
            socket.Stop();
            url = "http://127.0.0.1:" + port + "/";
            string php = Path.Combine(root, "runtime", "php.exe");
            var info = new ProcessStartInfo(php) {
                WorkingDirectory = root, UseShellExecute = false, CreateNoWindow = true,
                Arguments = "-c \"" + Path.Combine(root, "runtime", "php.ini") + "\""
                  + " -S 127.0.0.1:" + port + " -t \"" + Path.Combine(root, "GoblinDungeon") + "\""
                  + " \"" + Path.Combine(root, "router.php") + "\""
            };
            info.EnvironmentVariables["GOBLIN_LOCAL"] = "1";
            info.EnvironmentVariables["GOBLIN_DATA_DIR"] = data;
            info.EnvironmentVariables["GOBLIN_EXTENSION_DIR"] = Path.Combine(root, "runtime", "ext");
            info.EnvironmentVariables["GOBLIN_SESSION_DIR"] = Path.Combine(data, "sessions");
            info.EnvironmentVariables["GOBLIN_ERROR_LOG"] = Path.Combine(data, "php-error.log");
            info.EnvironmentVariables["GOBLIN_READY_TOKEN"] = ready;
            job = CreateJobObject(IntPtr.Zero, null);
            if (job == IntPtr.Zero) throw new Exception("No se pudo crear el supervisor del servidor.");
            var limits = new JOBOBJECT_EXTENDED_LIMIT_INFORMATION();
            limits.BasicLimitInformation.LimitFlags = 0x2000;
            int length = Marshal.SizeOf(limits);
            IntPtr buffer = Marshal.AllocHGlobal(length);
            try {
                Marshal.StructureToPtr(limits, buffer, false);
                if (!SetInformationJobObject(job, 9, buffer, (uint)length))
                    throw new Exception("No se pudo configurar el supervisor.");
            } finally { Marshal.FreeHGlobal(buffer); }
            server = Process.Start(info);
            if (!AssignProcessToJobObject(job, server.Handle))
                throw new Exception("No se pudo supervisar PHP.");
            bool ok = false;
            for (int i = 0; i < 60; i++)
            {
                if (server.HasExited) throw new Exception("PHP no ha arrancado. Consulta php-error.log.");
                try
                {
                    var request = (HttpWebRequest)WebRequest.Create(url + "__ready");
                    request.Proxy = null;
                    request.Timeout = 500;
                    using (var response = await request.GetResponseAsync())
                    using (var reader = new StreamReader(response.GetResponseStream()))
                        ok = (await reader.ReadToEndAsync()) == ready;
                }
                catch (WebException) {}
                if (ok) break;
                await Task.Delay(150);
            }
            if (!ok || server.HasExited) throw new Exception("El servidor no responde. Vuelve a abrir el juego.");
            open.Enabled = true;
            status.Text = "El juego está abierto en tu navegador.\nMantén esta ventana abierta mientras juegas.\nTus datos se guardan en " + data;
            if (smoke) File.WriteAllText(Environment.GetEnvironmentVariable("GOBLIN_SMOKE_REPORT"), url + "\n" + server.Id);
            else OpenBrowser();
        }
        catch (Exception ex)
        {
            StopServer();
            status.Text = "No se ha podido arrancar el juego.";
            MessageBox.Show(ex.Message, "Goblin Dungeon", MessageBoxButtons.OK, MessageBoxIcon.Error);
        }
    }
    void StopServer()
    {
        if (server != null)
        {
            try { if (!server.HasExited) { server.Kill(); server.WaitForExit(3000); } } catch {}
            server.Dispose(); server = null;
        }
        if (job != IntPtr.Zero) { CloseHandle(job); job = IntPtr.Zero; }
    }
    [StructLayout(LayoutKind.Sequential)]
    struct JOBOBJECT_BASIC_LIMIT_INFORMATION {
        public long PerProcessUserTimeLimit, PerJobUserTimeLimit;
        public uint LimitFlags;
        public UIntPtr MinimumWorkingSetSize, MaximumWorkingSetSize;
        public uint ActiveProcessLimit;
        public UIntPtr Affinity;
        public uint PriorityClass, SchedulingClass;
    }
    [StructLayout(LayoutKind.Sequential)]
    struct IO_COUNTERS { public ulong ReadOperationCount, WriteOperationCount, OtherOperationCount, ReadTransferCount, WriteTransferCount, OtherTransferCount; }
    [StructLayout(LayoutKind.Sequential)]
    struct JOBOBJECT_EXTENDED_LIMIT_INFORMATION {
        public JOBOBJECT_BASIC_LIMIT_INFORMATION BasicLimitInformation;
        public IO_COUNTERS IoInfo;
        public UIntPtr ProcessMemoryLimit, JobMemoryLimit, PeakProcessMemoryUsed, PeakJobMemoryUsed;
    }
    [DllImport("kernel32.dll", CharSet = CharSet.Unicode)] static extern IntPtr CreateJobObject(IntPtr attributes, string name);
    [DllImport("kernel32.dll")] static extern bool SetInformationJobObject(IntPtr job, int type, IntPtr info, uint length);
    [DllImport("kernel32.dll")] static extern bool AssignProcessToJobObject(IntPtr job, IntPtr process);
    [DllImport("kernel32.dll")] static extern bool CloseHandle(IntPtr handle);
}
