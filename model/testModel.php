<?php
class test{
    private $mysqli;
    private $backupFile;

    public function __construct()
    {
    }

    public function testConnect()
    {
        $this->mysqli = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        if ($this->mysqli->connect_error) {
            die("Error de conexión a la base de datos: " . $this->mysqli->connect_error);
        }else{
            echo "<h1 class='bg-white'>Conexion Correcta</h1>";
        }
    }

    public function crearBD()
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $this->mysqli = new mysqli(DB_HOST,DB_USER,DB_PASS);;
            $this->mysqli->set_charset("utf8mb4");
            $sql="CREATE DATABASE IF NOT EXISTS granjas";
            echo "<h1 class='bg-white'>BD creada con éxito.</h1>";
            $this->mysqli->query($sql);
        } catch (mysqli_sql_exception $e) {
            echo("Error: " . $e->getMessage());
            exit; 
        }
    }

    public function borrarBD()
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $this->mysqli = new mysqli(DB_HOST,DB_USER,DB_PASS);
            $this->mysqli->set_charset("utf8mb4");
            $sql="DROP DATABASE granjas";
            echo "<h1 class='bg-white'>BD borrada con éxito.</h1>";
            $this->mysqli->query($sql);
        } catch (mysqli_sql_exception $e) {
            echo("Error: " . $e->getMessage());
            exit; 
        }
    }

    public function cargarDatos()
    {
        try {
            // Inicializar la conexión
            $this->mysqli = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
            // Verificar si la conexión fue exitosa
            if ($this->mysqli->connect_error) {
                die("Error de conexión a la base de datos: " . $this->mysqli->connect_error);
            }
            $sql = file_get_contents('db/Datos_granjas.sql');
            $this->mysqli->multi_query($sql);
            echo "<h1 class='bg-white'>Datos cargados con éxito.</h1>";
        } catch (mysqli_sql_exception $e) {
            echo("Error: " . $e->getMessage());
            exit; 
        }
    }

    public function crearTablas()
    {
        try {
            // Inicializar la conexión
            $this->mysqli = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
            // Verificar si la conexión fue exitosa
            if ($this->mysqli->connect_error) {
                die("Error de conexión a la base de datos: " . $this->mysqli->connect_error);
            }
            $sql = file_get_contents('db/Tablas_granjas.sql');
            $this->mysqli->multi_query($sql);
            echo "<h1 class='bg-white'>Tablas creadas con éxito.</h1>";
        } catch (mysqli_sql_exception $e) {
            echo("Error: " . $e->getMessage());
            exit; 
        }
    }

    public function backupDB()
    {
        /*Para agregar mysqldump al PATH en Windows:
    1-Copia la ruta donde está mysqldump.exe (por ejemplo, C:\xampp\mysql\bin).
    2-Ve a Panel de control → Sistema → Configuración avanzada del sistema → Variables de entorno.
    3-En “Variables del sistema”, busca y selecciona la variable Path, haz clic en Editar.
    4-Agrega una nueva entrada con la ruta copiada.
    5-Hacer lo mismo en variables de entorno de usuario para evitar rproblemas
    5-Aceptar y reiniciar la PC.*/
        $this->backupFile = __DIR__ . '/../db/backup_granjas_' . date('Ymd_His') . '.sql';
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s 2>&1',
            escapeshellarg(DB_USER),
            escapeshellarg(DB_PASS),
            escapeshellarg(DB_HOST),
            escapeshellarg(DB_NAME),
            escapeshellarg($this->backupFile)
        );
        ob_start();
        system($command, $result);
        ob_end_clean(); // Clean the buffer
        return ($result === 0 && file_exists($this->backupFile) && filesize($this->backupFile) > 0);
    }

    public function descargarBackupBD()
    {
        if (file_exists($this->backupFile)) {
            // Clear any previous output
            if (ob_get_level()) {
                ob_end_clean();
            }
            // Set headers
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($this->backupFile) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($this->backupFile));
            // Send the file
            readfile($this->backupFile);
        } else {
            // This shouldn't be reached if backupDB() returned true
            die("El archivo de backup no existe.");
        }
    }

public function restaurarBackupBD($rutaArchivo) {
    if (!file_exists($rutaArchivo) || !is_readable($rutaArchivo)) {
        error_log("Archivo de backup no encontrado o sin permisos.");
        return false;
    }
    $host = DB_HOST;
    $user = DB_USER;
    $pass = DB_PASS;
    $db   = DB_NAME;
    $file = escapeshellarg($rutaArchivo);
    // Si hay password, lo incluimos. Si no, no ponemos -p (porque queda esperando).
    if ($pass !== '') {
        $comando = "mysql -h $host -u $user -p$pass $db < $file";
    } else {
        $comando = "mysql -h $host -u $user $db < $file";
    }
    error_log("Ejecutando comando: restaurar backup");
    // Ejecutar el comando y capturar salida
    exec($comando . " 2>&1", $output, $resultado);
    error_log("Resultado: $resultado");
    error_log("Output: " . implode("\n", $output));
    return $resultado === 0;
}


    public function guardarFechaBackup()
    {
        $fechaFile = __DIR__ . '/../db/ultimo_backup.txt';
        $fechaActual = date('Y-m-d H:i:s');
        file_put_contents($fechaFile, $fechaActual);
    }

    public static function obtenerUltimaFechaBackup()
    {
        $fechaFile = __DIR__ . '/../db/ultimo_backup.txt';
        if (file_exists($fechaFile)) {
            return file_get_contents($fechaFile);
        }
        return 'Nunca';
    }

    public function destruirBackup()
    {
        if (file_exists($this->backupFile)) {
            unlink($this->backupFile);
        }
    }
}