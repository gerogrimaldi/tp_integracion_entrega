<?php
$mensaje = '';
class tipoMantenimiento{
    private $idTipoMantenimiento;
    private $nombreMantenimiento;
    private $mysqli;

    public function __construct()
    {
        $this->mysqli = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        if ($this->mysqli->connect_error) { 
            die("Error de conexión a la base de datos: " . $this->mysqli->connect_error); 
        }
    }

    public function __destruct()
    {
        if ($this->mysqli !== null) {
            $this->mysqli->close();
        }
    }

    public function setMaxIDTipoMant()
    {
        try{
            if ($this->mysqli === null) { 
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            // Leer datos de la tabla 'granjas',
            $sql = "SELECT MAX(idTipoMantenimiento) AS maxID FROM TipoMantenimiento";
            $result = $this->mysqli->query($sql);
            if(!$result){
                throw new RuntimeException('Error al consultar el máximo idGranja: ' . $this->mysqli->error);
            }
            $data = []; // Array para almacenar los datos
            //La consulta devuelve un solo resultado.
            if ($result && $row = $result->fetch_assoc()) {
                $maxID = $row['maxID'] ?? 0; // Si no hay registros, maxID será 0
                $this->idTipoMantenimiento = $maxID + 1; // Incrementa el ID máximo en 1
            }else {
                throw new RuntimeException("Error al obtener el máximo idGranja: " . $this->mysqli->error);
            }
            return true;
        }catch(RuntimeException $e) {
            throw $e;
        }
    }

    public function setIDTipoMant($idTipoMantenimiento)
    {
        if ( ctype_digit($idTipoMantenimiento)==true )
        {
            $this->idTipoMantenimiento = $idTipoMantenimiento; 
        }
    }

    public function setNombreMantenimiento($nombreMantenimiento)
    {
        $this->nombreMantenimiento = htmlspecialchars(strip_tags(trim($nombreMantenimiento)), ENT_QUOTES, 'UTF-8'); 
    }

    public function getTipoMantenimientos()
    {
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            //Consulta sin preparación ya que no trae datos externos
            $sql = "SELECT idTipoMantenimiento, nombre FROM tipoMantenimiento";
            $result = $this->mysqli->query($sql);
            if (!$result) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $this->mysqli->error);
            }
            $data = [];
            if ($result->num_rows > 0) { 
                while($row = $result->fetch_assoc()) { 
                    $data[] = $row; 
                } 
            }
            return $data;
        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function save(){
        try{
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
        // Verificar si ya existe el tipo de mantenimiento
            $sqlCheck = "SELECT tipoMantenimiento.nombre FROM tipoMantenimiento WHERE nombre = ?";
            $stmtCheck = $this->mysqli->prepare($sqlCheck);
            if (!$stmtCheck) { 
                throw new RuntimeException("Error en la preparación de la consulta de verificación: " . $this->mysqli->error); 
            }
            $stmtCheck->bind_param("s", $this->nombreMantenimiento);
            $stmtCheck->execute();
            $stmtCheck->store_result();
            if ($stmtCheck->num_rows > 0) {
                $stmtCheck->close();
                throw new RuntimeException('Error, ya existe: ' . $this->mysqli->error);
            }
            $stmtCheck->close();
        // Insertar
            $sql = "INSERT INTO tipoMantenimiento (idTipoMantenimiento, nombre) VALUES (?, ?)";
            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException('Error al preparar consulta: ' . $this->mysqli->error);
            }
        // Enlaza los parámetros y ejecuta la consulta
            $stmt->bind_param("is", $this->idTipoMantenimiento, $this->nombreMantenimiento);
            if (!$stmt->execute()){
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }
            $stmt->close();
            return true;

        }catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function update()
    {
        try{
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            $sql = "UPDATE tipoMantenimiento SET nombre = ? WHERE idTipoMantenimiento = ?";
            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException("Error en la preparación de la consulta de actualización: " . $this->mysqli->error);
            }
            $stmt->bind_param("si", $this->nombreMantenimiento, $this->idTipoMantenimiento);
            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }
            $stmt->close();
            return true;
        }catch (RuntimeException $e) {
            throw $e;
        }
        
    }

    public function deleteTipoMantID($idTipoMantenimiento)
    {
        try{
            if ($this->mysqli === null) { 
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            $sql = "DELETE FROM tipoMantenimiento WHERE idTipoMantenimiento = ?";
            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) { 
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error); 
            }
            $stmt->bind_param('i', $idTipoMantenimiento);
            if (!$stmt->execute()) { 
                // Verificar si es un error de clave foránea
                if ($this->mysqli->errno == 1451) {
                    throw new RuntimeException('El tipo de mantenimiento tiene registros asociados.');
                }else{
                    throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error); 
                }
            }
            $stmt->close(); 
            return true;
        }catch (RuntimeException $e){
            throw $e;
        }
    }
}

class mantenimientoGranja{
    private $idMantenimientoGranja;
    private $fecha;
	private $idGranja;
    private $idTipoMantenimiento;
    private $mysqli;
    
    public function __construct()
    {
    require_once 'includes/config.php';
        $this->mysqli = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        if ($this->mysqli->connect_error) { die("Error de conexión a la base de datos: " . $this->mysqli->connect_error); }
    }

    public function __destruct()
    {
        if ($this->mysqli !== null) {
            $this->mysqli->close();
        }
    }
    
    public function setIdMantGranja($idMantenimiento)
    {
        if ( ctype_digit($idMantenimiento)==true )
        {
            $this->idMantenimientoGranja = $idMantenimiento;
        }
    }

    public function setIdGranja($idGranja)
    {
        if ( ctype_digit($idGranja)==true )
        {
            $this->idGranja = $idGranja;
        }
    }

    public function setFecha($fecha)
    {
        $dateTime = DateTime::createFromFormat('Y-m-d\TH:i', $fecha);
        if ($dateTime) {
            $this->fecha = $dateTime->format('Y-m-d H:i:s');
        } else {
            throw new RuntimeException('Formato de fecha inválido.');
        }
    }

    public function setIdTipoMantenimiento($idTipoMantenimiento)
    {
        if ( ctype_digit($idTipoMantenimiento)==true ){
            $this->idTipoMantenimiento = $idTipoMantenimiento;
        }  
    }

    public function setMaxIDMantGranja()
    {
        try{
            if ($this->mysqli === null) { 
                    throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            $sql = "SELECT MAX(idMantenimientoGranja) AS maxID FROM mantenimientoGranja  ";
            $result = $this->mysqli->query($sql);
            if(!$result){
                throw new RuntimeException('Error al consultar el máximo:' . $this->mysqli->error);
            }
            $data = [];
            if ($result && $row = $result->fetch_assoc()) {
                $maxID = $row['maxID'] ?? 0;
                $this->idMantenimientoGranja = $maxID + 1;
                return true;
            }else {
                throw new RuntimeException('Error al obtener el máximo: ' . $this->mysqli->error);
            }
        }catch(RuntimeException $e) {
            throw $e;
        }
    }

    public function getMantGranjas($idGranja, $desde, $hasta)
    {
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            if (!is_numeric($idGranja)) {
                throw new RuntimeException('El ID de la granja debe ser un número.');
            }
            // Validar fechas
            $fechaDesde = DateTime::createFromFormat('Y-m-d', $desde);
            $fechaHasta = DateTime::createFromFormat('Y-m-d', $hasta);
            if (!$fechaDesde || !$fechaHasta) {
                throw new RuntimeException('Formato de fecha inválido. Se espera YYYY-MM-DD.');
            }
            // Preparar la consulta con rango de fechas
            $sql = "SELECT mg.idMantenimientoGranja, mg.fecha, mg.idGranja, mg.idTipoMantenimiento, tm.nombre
                    FROM mantenimientoGranja mg
                    INNER JOIN tipoMantenimiento tm ON tm.idTipoMantenimiento = mg.idTipoMantenimiento
                    WHERE mg.idGranja = ?
                    AND DATE(mg.fecha) BETWEEN ? AND ?
                    ORDER BY mg.fecha ASC";

            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException("Error en la preparación de la consulta: " . $this->mysqli->error);
            }
            // Pasar parámetros (idGranja: int, fechas: string)
            $desdeStr = $fechaDesde->format('Y-m-d');
            $hastaStr = $fechaHasta->format('Y-m-d');
            $stmt->bind_param('iss', $idGranja, $desdeStr, $hastaStr);
            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }
            $result = $stmt->get_result();
            if ($result === false) {
                throw new RuntimeException('Error al obtener el resultado: ' . $stmt->error);
            }
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $stmt->close();
            return $data;
        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function save()
    {
        try{
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            // Inserción de mantenimiento, no es necesario chequear existencia
            $sql = "INSERT INTO mantenimientoGranja (idMantenimientoGranja, fecha, idGranja, idTipoMantenimiento) VALUES (?, ?, ?, ?)";
            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException("Error en la preparación de la consulta de inserción: " . $this->mysqli->error);
            }
            // Enlaza los parámetros y ejecuta la consulta
            $stmt->bind_param("isii", $this->idMantenimientoGranja, $this->fecha, $this->idGranja, $this->idTipoMantenimiento);
            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }
            // Cerrar la consulta
            $stmt->close();
            return true;
        }catch(RuntimeException $e) {
            throw $e;
        }
    }

    public function deleteMantenimientoGranjaId($idMantenimientoGranja)
    {
        try{
            if ($this->mysqli === null) { 
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            if (!is_numeric($idMantenimientoGranja)) {
                throw new RuntimeException('El ID del galpón debe ser un número.');
            }
            $sql = "DELETE FROM mantenimientoGranja WHERE idMantenimientoGranja = ?";
            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error);
            }
            $stmt->bind_param('i', $idMantenimientoGranja);
            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error); 
            }
            $stmt->close();
            return true;
        } catch (runtimeException $e) {
            throw $e;
        }
    }
}

class mantenimientoGalpon{
    private $idMantenimientoGalpon;
    private $fecha;
    private $idGalpon;
    private $idTipoMantenimiento;
    private $mysqli;

    public function __construct()
    {
    require_once 'includes/config.php';
        $this->mysqli = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        if ($this->mysqli->connect_error) { die("Error de conexión a la base de datos: " . $this->mysqli->connect_error); }
    }

    public function __destruct()
    {
        if ($this->mysqli !== null) {
            $this->mysqli->close();
        }
    }

    public function setIdMantGalpon($idMantenimiento)
    {
        if ( ctype_digit($idMantenimiento)==true )
        {
            $this->idMantenimientoGalpon = $idMantenimiento;
        }
    }

    public function setIdGalpon($idGalpon)
    {
        if ( ctype_digit($idGalpon)==true )
        {
            $this->idGalpon = $idGalpon;
        }
    }

    public function setFecha($fecha)
    {
        //Convierte la fecha desde el frontend a un formato válido para MySQL
        $dateTime = DateTime::createFromFormat('Y-m-d\TH:i', $fecha);
        if ($dateTime) {
            $this->fecha = $dateTime->format('Y-m-d H:i:s');
        } else {
            throw new RuntimeException('Formato de fecha inválido.');
        }
    }

    public function setIdTipoMantenimiento($idTipoMantenimiento)
    {
        if ( ctype_digit( $idTipoMantenimiento )==true ){
            $this->idTipoMantenimiento = $idTipoMantenimiento;
        }  
    }

    public function setMaxIDMantGalpon()
    {
        try{
            if ($this->mysqli === null) { 
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            $sql = "SELECT MAX(idMantenimientoGalpon) AS maxID FROM mantenimientoGalpon  ";
            $result = $this->mysqli->query($sql);
            if(!$result){
                throw new RuntimeException('Error al consultar el máximo:' . $this->mysqli->error);
            }
            $data = [];
            if ($result && $row = $result->fetch_assoc()) {
                $maxID = $row['maxID'] ?? 0;
                $this->idMantenimientoGalpon = $maxID + 1;
                return true;
            }else {
                throw new RuntimeException('Error al obtener el máximo: ' . $this->mysqli->error);
            }
        }catch(RuntimeException $e) {
            throw $e;
        }
    }

    public function getMantGalpon($idGalpon, $desde, $hasta)
    {
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            if (!is_numeric($idGalpon)) {
                throw new RuntimeException('El ID del galpón debe ser un número.');
            }
            // Validar fechas
            $fechaDesde = DateTime::createFromFormat('Y-m-d', $desde);
            $fechaHasta = DateTime::createFromFormat('Y-m-d', $hasta);
            if (!$fechaDesde || !$fechaHasta) {
                throw new RuntimeException('Formato de fecha inválido. Se espera YYYY-MM-DD.');
            }
            // Preparar la consulta con rango de fechas
            $sql = "SELECT mg.idMantenimientoGalpon, mg.fecha, mg.idGalpon, mg.idTipoMantenimiento, tm.nombre
                    FROM mantenimientoGalpon mg
                    INNER JOIN tipoMantenimiento tm ON tm.idTipoMantenimiento = mg.idTipoMantenimiento
                    WHERE mg.idGalpon = ?
                    AND DATE(mg.fecha) BETWEEN ? AND ?
                    ORDER BY mg.fecha ASC";

            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException("Error en la preparación de la consulta: " . $this->mysqli->error);
            }
            // Pasar parámetros (idGranja: int, fechas: string)
            $desdeStr = $fechaDesde->format('Y-m-d');
            $hastaStr = $fechaHasta->format('Y-m-d');
            $stmt->bind_param('iss', $idGalpon, $desdeStr, $hastaStr);
            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }
            $result = $stmt->get_result();
            if ($result === false) {
                throw new RuntimeException('Error al obtener el resultado: ' . $stmt->error);
            }
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $stmt->close();
            return $data;
        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function save()
    {
        try{
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            // Inserción de mantenimiento, no es necesario chequear existencia
            $sql = "INSERT INTO mantenimientoGalpon (idMantenimientoGalpon, fecha, idGalpon, idTipoMantenimiento) VALUES (?, ?, ?, ?)";
            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException("Error en la preparación de la consulta de inserción: " . $this->mysqli->error);
            }
            // Enlaza los parámetros y ejecuta la consulta
            $stmt->bind_param("isii", $this->idMantenimientoGalpon, $this->fecha, $this->idGalpon, $this->idTipoMantenimiento);
            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }
            // Cerrar la consulta
            $stmt->close();
            return true;
        }catch(RuntimeException $e) {
            throw $e;
        }
    }

    public function deleteMantenimientoGalponId($idMantenimientoGalpon)
    {
        try{
            if ($this->mysqli === null) { 
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            if (!is_numeric($idMantenimientoGalpon)) {
                throw new RuntimeException('El ID del galpón debe ser un número.');
            }
            $sql = "DELETE FROM mantenimientoGalpon WHERE idMantenimientoGalpon = ?";
            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error);
            }
            $stmt->bind_param('i', $idMantenimientoGalpon);
            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error); 
            }
            $stmt->close();
            return true;
        } catch (RuntimeException $e) {
            throw $e;
        }
    }
}