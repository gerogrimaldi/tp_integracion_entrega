<?php
$mensaje = '';
class galpon{
	/*
    Tabla SQL al momento de crear este Model:
    (idGalpon, identificacion, idTipoAve, capacidad, idGranja)
    */
    private $idGalpon;
    private $identificacion;
	private $idTipoAve;
    private $capacidad;
    private $idGranja;
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
    
    public function setIdGalpon($idGalpon)
    {
        // Evalua que el ID sea positivo, entero y que no esté vacía
        if ( ctype_digit($idGalpon)==true ) 
        {
            $this->idGalpon = $idGalpon;
        }
    }

    public function setIdGranja($idGranja)
    {
        if ( ctype_digit($idGranja)==true )
        {
            $this->idGranja = $idGranja;
        }
    }

    public function setIdentificacion($identificacion)
    {
        $this->identificacion = htmlspecialchars(strip_tags(trim($identificacion)), ENT_QUOTES, 'UTF-8'); 
    }

    public function setIdTipoAve($idTipoAve)
    {
        if ( ctype_digit( $idTipoAve )==true ){
            $this->idTipoAve = $idTipoAve;
        }  
    }

    public function setCapacidad($capacidad)
    {
        if ( ctype_digit( $capacidad )==true ){
            $this->capacidad = $capacidad; 
        }
    }

    public function setMaxID()
    {
        try{
            if ($this->mysqli === null) { 
                    throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            $sql = "SELECT MAX(idGalpon) AS maxID FROM galpon";;
            $result = $this->mysqli->query($sql);
            if(!$result){
                throw new RuntimeException('Error al consultar el máximo idGranja: ' . $this->mysqli->error);
            }
            $data = [];
            if ($result && $row = $result->fetch_assoc()) {
                $maxID = $row['maxID'] ?? 0;
                $this->idGalpon = $maxID + 1; 
            }else {
                throw new RuntimeException("Error al obtener el máximo idGranja: " . $this->mysqli->error);
            }
        }catch(RuntimeException $e) {
            throw $e;
        }
    }

    public function getall($idGranja)
    {
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            // Preparar la consulta
            $sql = "SELECT galpon.idGalpon, galpon.identificacion, galpon.idTipoAve, galpon.capacidad, 
                    galpon.idGranja, tipoave.nombre 
                    FROM galpon 
                    INNER JOIN tipoave ON (tipoave.idTipoAve = galpon.idTipoAve) 
                    WHERE galpon.idGranja = ?";
            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException("Error en la preparación de la consulta: " . $this->mysqli->error);
            }
            // Enlazar el parámetro y ejecutar la consulta
            $stmt->bind_param("i", $idGranja);
            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }
            // Obtener el resultado de la consulta
            $result = $stmt->get_result();
            if ($result === false) {
                //Se activa con error, del SQL. Si 0 columnas, sigue sin error.
                throw new RuntimeException('Error al obtener el resultado: ' . $stmt->error);
            }
            $data = []; // Array para almacenar los datos
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            }
            $stmt->close();
            return $data;
        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function getGalponesMasGranjas()
    {
        try{
            if ($this->mysqli === null) { 
                    throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            $sql = "SELECT galpon.idGalpon, galpon.identificacion, galpon.idTipoAve, 
                    galpon.capacidad, galpon.idGranja, granja.nombre FROM galpon
                    INNER JOIN granja ON (galpon.idGranja = granja.idGranja)";
            $result = $this->mysqli->query($sql);
            if ($result === false) {
                //Este error se da si falla el SQL. Si devuelve 0 columnas, no se activa.
                throw new RuntimeException('Error al ejecutar la consulta: ' . $this->mysqli->error);
            }
            $data = [];
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            }
            return $data;
        }catch(RuntimeException $e) {
            throw $e;
        }
    }

    public function getTiposAves()
    {
        try{
            if ($this->mysqli === null) { 
                    throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            $sql = "SELECT idTipoAve, nombre FROM tipoAve";
            $result = $this->mysqli->query($sql);
            if ($result === false) {
                //Este error se da si falla el SQL. Si devuelve 0 columnas, no se activa.
                throw new RuntimeException('Error al ejecutar la consulta: ' . $this->mysqli->error);
            }
            $data = [];
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            }
            return $data;
        }catch(RuntimeException $e) {
            throw $e;
        }
    }

    public function save()
    {
        try{
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            $sqlCheck = "SELECT idGalpon FROM galpon WHERE idGalpon = ?";
            $stmtCheck = $this->mysqli->prepare($sqlCheck);
            if (!$stmtCheck) {
                throw new RuntimeException("Error en la preparación de la consulta de verificación: " . $this->mysqli->error);
            }
            $stmtCheck->bind_param("i", $this->idGalpon);
            $stmtCheck->execute();
            $stmtCheck->store_result();
            if ($stmtCheck->num_rows > 0) {
                $stmtCheck->close();
                throw new RuntimeException("Error, ya existe: " . $this->mysqli->error);
            }
            $stmtCheck->close();
            $sql = "INSERT INTO galpon (idGalpon, identificacion, idTipoAve, capacidad, idGranja) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException("Error en la preparación de la consulta de inserción: " . $this->mysqli->error);
            }
            $stmt->bind_param("isiii", $this->idGalpon, $this->identificacion, $this->idTipoAve, $this->capacidad, $this->idGranja);
            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }
            $stmt->close();
            return true;
        }catch(RuntimeException $e) {
            throw $e;
        }
    }

    public function update()
    {
        try{
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            $sql = "UPDATE galpon SET identificacion = ?, idTipoAve = ?, capacidad = ?, idGranja = ? WHERE idGalpon = ?";
            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException("Error en la preparación de la consulta de actualización: " . $this->mysqli->error);
            }
            $stmt->bind_param("siiii", $this->identificacion, $this->idTipoAve, $this->capacidad, $this->idGranja, $this->idGalpon);
            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }
            $stmt->close();
            return true;
        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function deleteGalponPorId($idGalpon)
    {
        try{
            if ($this->mysqli === null) { 
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            if (!is_numeric($idGalpon)) {
                throw new RuntimeException('El ID del galpón debe ser un número.');
            }
            $sql = "DELETE FROM galpon WHERE idGalpon = ?";
            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error);
            }
            $stmt->bind_param('i', $idGalpon);
            if (!$stmt->execute()) {
                // Verificar si es un error de clave foránea
                if ($this->mysqli->errno == 1451) {
                    throw new RuntimeException('El tipo de mantenimiento tiene registros asociados.');
                }else{
                    throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error); 
                }
            }
            // Cerrar el statement
            $stmt->close();
            return true;
        } catch (runtimeException $e) {
            throw $e;
        }
    }
}