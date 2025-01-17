<?php
$mensaje = '';
class granja{
	//(idGranja, nombre, habilitacionSenasa, metrosCuadrados, ubicacion)
    private $idGranja;
    private $nombre;
	private $habilitacionSenasa;
    private $metrosCuadrados;
    private $ubicacion;
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
    
    public function setIdGranja($idGranja)
    {
        if ( ctype_digit($idGranja)==true ) // Evalua que el ID sea positivo y entero
        {
            $this->idGranja = $idGranja;
        }
    }

    public function setNombre($nombre)
    {
        $this->nombre = htmlspecialchars(strip_tags(trim($nombre)), ENT_QUOTES, 'UTF-8');
        // Eliminar espacios en blanco al inicio y al final y asignarlo al objeto.
    }

    public function setHabilitacionSenasa($habilitacionSenasa)
    {
        $this->habilitacionSenasa = htmlspecialchars(strip_tags(trim($habilitacionSenasa)), ENT_QUOTES, 'UTF-8'); 
    }

    public function setMetrosCuadrados($metrosCuadrados)
    {
        /* Todo lo que entre, evaluar:
            - Que no contenga elementos HTML/JS/PHP
            - Que no sea nulo
            - Que sea entero positivo
            Con ctype ya está, deben ser todos dígitos,
            y si está vacío, da false.
        */
        if ( ctype_digit($metrosCuadrados)==true ) 
        {
            $this->metrosCuadrados = $metrosCuadrados;
        }
    }

    public function setUbicacion($ubicacion)
    {
        $this->ubicacion = htmlspecialchars(strip_tags(trim($ubicacion)), ENT_QUOTES, 'UTF-8');
    }

    public function setMaxID()
    {
        try{
            if ($this->mysqli === null) { 
                    throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            // Leer datos de la tabla 'granjas',
            $sql = "SELECT MAX(idGranja) AS maxID FROM granja";
            $result = $this->mysqli->query($sql);
            if(!$result){
                throw new RuntimeException('Error al consultar el máximo idGranja: ' . $this->mysqli->error);
            }
            $data = []; // Array para almacenar los datos
            //La consulta devuelve un solo resultado.
            if ($result && $row = $result->fetch_assoc()) {
                $maxID = $row['maxID'] ?? 0; // Si no hay registros, maxID será 0
                $this->idGranja = $maxID + 1; // Incrementa el ID máximo en 1
            }else {
                throw new RuntimeException("Error al obtener el máximo idGranja: " . $this->mysqli->error);
            }
        }catch(RuntimeException $e) {
            throw $e;
        }
    }

    public function getall()
    {
        try{
            if ($this->mysqli === null) { 
                    throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            // Leer datos de la tabla 'granjas',
            $sql = "SELECT idGranja, nombre, habilitacionSenasa, metrosCuadrados, ubicacion FROM granja";
            $result = $this->mysqli->query($sql);
            if ($result === false) {
                //Este error se da si falla el SQL. Si devuelve 0 columnas, no se activa.
                throw new RuntimeException('Error al ejecutar la consulta: ' . $this->mysqli->error);
            }
            $data = []; // Array para almacenar los datos
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
            // Consulta para verificar si el Granja ya existe
            $sqlCheck = "SELECT idGranja FROM granja WHERE idGranja = ?";
            $stmtCheck = $this->mysqli->prepare($sqlCheck);
            if (!$stmtCheck) {
                throw new RuntimeException("Error en la preparación de la consulta de verificación: " . $this->mysqli->error);
            }
            // Enlazar parametro
            $stmtCheck->bind_param("i", $this->idGranja);
            $stmtCheck->execute();
            $stmtCheck->store_result();
            // Verificar
            if ($stmtCheck->num_rows > 0) {
                $stmtCheck->close();
                throw new RuntimeException("Error, ya existe: " . $this->mysqli->error);
            }
            $stmtCheck->close();
            // Inserción de Granja
            $sql = "INSERT INTO granja (idGranja, nombre, habilitacionSenasa, metrosCuadrados, ubicacion) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException("Error en la preparación de la consulta de inserción: " . $this->mysqli->error);
            }
            // Enlaza los parámetros y ejecuta la consulta
            $stmt->bind_param("issis", $this->idGranja, $this->nombre, $this->habilitacionSenasa, $this->metrosCuadrados, $this->ubicacion);
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

    public function update()
    {
        try{
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            // Preparar la consulta para actualizar los datos del Granja
            $sql = "UPDATE granja SET nombre = ?, habilitacionSenasa = ?, metrosCuadrados = ?, ubicacion = ? WHERE idGranja = ?";
            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException("Error en la preparación de la consulta de actualización: " . $this->mysqli->error);
            }
            // Enlazar parámetros y ejecutar la consulta
            $stmt->bind_param("ssisi", $this->nombre, $this->habilitacionSenasa, $this->metrosCuadrados, $this->ubicacion, $this->idGranja);
            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }
            // Cerrar la consulta
            $stmt->close();
            return true;
        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function deleteGranjaPorId($idGranja)
    {
        try{
            if ($this->mysqli === null) { 
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            // Verificar que $idGranja sea un entero
            if (!is_numeric($idGranja)) {
                throw new RuntimeException('El ID de la granja debe ser un número.');
            }

            $sql = "DELETE FROM granja WHERE idGranja = ?";
            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error);
            }
            $stmt->bind_param('i', $idGranja);
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