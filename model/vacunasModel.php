<?php

class vacuna{
	//(idVacuna, nombre, idViaAplicacion, marca, enfermedad)
    private $idVacuna;
    private $nombre;
    private $idViaAplicacion;
    private $marca;
    private $enfermedad;
    private $mysqli;
    
    public function __construct()
    {
        $this->mysqli = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        if ($this->mysqli->connect_error) {
            die("Error de conexión a la base de datos: " . $this->mysqli->connect_error);
        }
    }

    public function setIdVacuna($idVacuna)
    {
        if ( ctype_digit($idVacuna)==true )
        {
            $this->idVacuna = $idVacuna;
        }
    }

    public function __destruct()
    {
        if ($this->mysqli !== null) {
            $this->mysqli->close();
        }
    }

    public function setIdViaAplicacion($idViaAplicacion)
    {
        if ( ctype_digit($idViaAplicacion)==true )
        {
            $this->idViaAplicacion = $idViaAplicacion;
        }
    }

    public function setNombre($nombre)
    {
        $this->nombre = htmlspecialchars(strip_tags(trim($nombre)), ENT_QUOTES, 'UTF-8'); 
    }

    public function setMarca($marca)
    {
        $this->marca = htmlspecialchars(strip_tags(trim($marca)), ENT_QUOTES, 'UTF-8'); 
    }

    public function setEnfermedad($enfermedad)
    {
        $this->enfermedad = htmlspecialchars(strip_tags(trim($enfermedad)), ENT_QUOTES, 'UTF-8'); 
    }

    public function setMaxIDVacuna()
    {
        try{
            if ($this->mysqli === null) { 
                    throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            $sql = "SELECT MAX(idVacuna) AS maxID FROM vacuna";
            $result = $this->mysqli->query($sql);
            if(!$result){
                throw new RuntimeException('Error al consultar el máximo idVacuna: ' . $this->mysqli->error);
            }
            $data = []; 
            if ($result && $row = $result->fetch_assoc()) {
                $maxID = $row['maxID'] ?? 0; 
                $this->idVacuna = $maxID + 1; 
            }else {
                throw new RuntimeException("Error al obtener el máximo idVacuna: " . $this->mysqli->error);
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
            $sql = "SELECT vacuna.idVacuna, vacuna.nombre, vacuna.idViaAplicacion, 
                    vacuna.marca, vacuna.enfermedad, viaAplicacion.idViaAplicacion, viaAplicacion.via 
                    FROM vacuna 
                    INNER JOIN viaAplicacion ON (vacuna.idViaAplicacion = viaAplicacion.idViaAplicacion)";
            $result = $this->mysqli->query($sql);
            if ($result === false) {
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

    public function getAllViaAplicacion()
    {
        try{
            if ($this->mysqli === null) { 
                    throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            $sql = "SELECT viaAplicacion.idViaAplicacion, viaAplicacion.via FROM viaAplicacion";
            $result = $this->mysqli->query($sql);
            if ($result === false) {
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
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            $sqlCheck = "SELECT idVacuna FROM vacuna WHERE nombre = ? AND marca = ?";
            $stmtCheck = $this->mysqli->prepare($sqlCheck);
            if (!$stmtCheck) {
                throw new RuntimeException("Error en la preparación de la consulta de verificación: " . $this->mysqli->error);
            }
            // Enlazar parametros
            $stmtCheck->bind_param("ss", $this->nombre, $this->marca);
            $stmtCheck->execute();
            $stmtCheck->store_result();
            // Verificar
            if ($stmtCheck->num_rows > 0) {
                $stmtCheck->close();
                throw new RuntimeException("Error: La vacuna ya existe.");
            }
            $stmtCheck->close();
            // Inserción
            $sql = "INSERT INTO vacuna (idVacuna, nombre, idViaAplicacion, marca, enfermedad) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException("Error en la preparación de la consulta de inserción: " . $this->mysqli->error);
            }
            $stmt->bind_param("isiss", $this->idVacuna, $this->nombre, $this->idViaAplicacion, $this->marca, $this->enfermedad);
            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }
            $stmt->close();
            return true;
        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function update()
    {
        try{
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            $sql = "UPDATE vacuna SET nombre = ?, idViaAplicacion = ?, marca = ?, enfermedad = ? WHERE idVacuna = ?";
            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException("Error en la preparación de la consulta de actualización: " . $this->mysqli->error);
            }
            // Enlazar parámetros y ejecutar la consulta
            $stmt->bind_param("sissi", $this->nombre, $this->idViaAplicacion, $this->marca, $this->enfermedad, $this->idVacuna);
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

    public function deleteVacunaPorId($idVacuna)
    {
        try{
            if ($this->mysqli === null) { 
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            if (!is_numeric($idVacuna)) {
                throw new RuntimeException('El ID del galpón debe ser un número.');
            }
            $sql = "DELETE FROM vacuna WHERE idVacuna = ?";
            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error);
            }
            $stmt->bind_param('i', $idVacuna);
            if (!$stmt->execute()) {
                // Verificar si es un error de clave foránea
                if ($this->mysqli->errno == 1451) {
                    throw new RuntimeException('La vacuna tiene registros asociados.');
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

class loteVacuna{
	//(idLoteVacuna, numeroLote, fechaCompra, cantidad, vencimiento, idVacuna)
    private $idLoteVacuna;
    private $numeroLote;
    private $fechaCompra;
    private $cantidad;
    private $vencimiento;
    private $idVacuna;
    private $mysqli;
    
    public function __construct()
    {
        // Inicializar conexión a base de datos
    require_once 'includes/config.php';
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

    public function setIdLoteVacuna($idLoteVacuna)
    {
        if ( ctype_digit($idLoteVacuna)==true )
        {
            $this->idLoteVacuna = $idLoteVacuna;
        }
    }

    public function setIdVacuna($idVacuna)
    {
        if ( ctype_digit($idVacuna)==true )
        {
            $this->idVacuna = $idVacuna;
        }
    }

    public function setNumeroLote($numeroLote)
    {
        $this->numeroLote = htmlspecialchars(strip_tags(trim($numeroLote)), ENT_QUOTES, 'UTF-8'); 
    }

    public function setCantidad($cantidad)
    {
        $this->cantidad = htmlspecialchars(strip_tags(trim($cantidad)), ENT_QUOTES, 'UTF-8'); 
    }

    public function setFechaCompra($fecha){
        $this->fechaCompra = new DateTime($fecha);
        $this->fechaCompra = $this->fechaCompra->format('Y-m-d H:i:s');
    }

    public function setVencimiento($fecha){
        $this->vencimiento = new DateTime($fecha);
        $this->vencimiento = $this->vencimiento->format('Y-m-d H:i:s');
    }
    
    public function setMaxIDLoteVacuna()
    {
        try{
            if ($this->mysqli === null) { 
                    throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            // Leer datos de la tabla 'granjas',
            $sql = "SELECT MAX(idLoteVacuna) AS maxID FROM loteVacuna";
            $result = $this->mysqli->query($sql);
            if(!$result){
                throw new RuntimeException('Error al consultar el máximo idLoteVacuna: ' . $this->mysqli->error);
            }
            $data = []; // Array para almacenar los datos
            //La consulta devuelve un solo resultado.
            if ($result && $row = $result->fetch_assoc()) {
                $maxID = $row['maxID'] ?? 0; // Si no hay registros, maxID será 0
                $this->idLoteVacuna = $maxID + 1; // Corrected property assignment
            }else {
                throw new RuntimeException("Error al obtener el máximo idLoteVacuna: " . $this->mysqli->error);
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
            $sql = "SELECT loteVacuna.idLoteVacuna, loteVacuna.numeroLote, loteVacuna.fechaCompra, 
                loteVacuna.cantidad, loteVacuna.vencimiento, loteVacuna.idVacuna, vacuna.nombre,
                vacuna.marca FROM loteVacuna INNER JOIN vacuna ON (vacuna.idVacuna = loteVacuna.idVacuna)";
            $result = $this->mysqli->query($sql);
            if ($result === false) {
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

    public function getLotes($idVacuna)
    {
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            // Preparar la consulta
        $sql = "SELECT lv.idLoteVacuna, lv.numeroLote, lv.fechaCompra, lv.cantidad, lv.vencimiento, lv.idVacuna, v.nombre, v.marca,
                    (lv.cantidad - IFNULL(SUM(lvla.cantidad),0)) AS cantidadDisponible
                FROM loteVacuna lv
                INNER JOIN vacuna v ON v.idVacuna = lv.idVacuna
                LEFT JOIN loteVacuna_loteAve lvla ON lvla.idLoteVacuna = lv.idLoteVacuna
                WHERE lv.idVacuna = ?
                GROUP BY lv.idLoteVacuna";
        $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException("Error en la preparación de la consulta: " . $this->mysqli->error);
            }
            // Enlazar el parámetro y ejecutar la consulta
            $stmt->bind_param("i", $idVacuna);
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

    public function save()
    {
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            // Verificar si ya existe el número de lote para el mismo idVacuna
            $sqlCheck = "SELECT idLoteVacuna FROM loteVacuna WHERE numeroLote = ? AND idVacuna = ?";
            $stmtCheck = $this->mysqli->prepare($sqlCheck);
            if (!$stmtCheck) {
                throw new RuntimeException("Error en la preparación de la consulta de verificación: " . $this->mysqli->error);
            }
            // Enlazar parámetros
            $stmtCheck->bind_param("si", $this->numeroLote, $this->idVacuna);
            $stmtCheck->execute();
            $stmtCheck->store_result();
            // Verificar
            if ($stmtCheck->num_rows > 0) {
                $stmtCheck->close();
                throw new RuntimeException("Error: El número de lote ya existe para esta vacuna.");
            }
            $stmtCheck->close();
            // Inserción
            $sql = "INSERT INTO loteVacuna (idLoteVacuna, numeroLote, fechaCompra, cantidad, vencimiento, idVacuna) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException("Error en la preparación de la consulta de inserción: " . $this->mysqli->error);
            }
            $stmt->bind_param("issisi", $this->idLoteVacuna, $this->numeroLote, $this->fechaCompra, $this->cantidad, $this->vencimiento, $this->idVacuna);
            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }
            $stmt->close();
            return true;
        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function update()
    {
        try{
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            $sql = "UPDATE loteVacuna SET  numeroLote = ?, fechaCompra = ?, cantidad = ?, vencimiento = ? WHERE idLoteVacuna = ?";
            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException("Error en la preparación de la consulta de actualización: " . $this->mysqli->error);
            }
            // Enlazar parámetros y ejecutar la consulta
            $stmt->bind_param("ssisi", $this->numeroLote, $this->fechaCompra, $this->cantidad, $this->vencimiento, $this->idLoteVacuna);
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

    public function deleteLoteVacunaPorId($idLoteVacuna)
    {
        try{
            if ($this->mysqli === null) { 
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            if (!is_numeric($idLoteVacuna)) {
                throw new RuntimeException('El ID del lote de vacuna debe ser un número.'); // Corrected error message
            }
            $sql = "DELETE FROM loteVacuna WHERE idLoteVacuna = ?";
            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error);
            }
            $stmt->bind_param('i', $idLoteVacuna);
            if (!$stmt->execute()) {
                // Verificar si es un error de clave foránea
                if ($this->mysqli->errno == 1451) {
                    throw new RuntimeException('El lote de vacuna tiene registros asociados.');
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