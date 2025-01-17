<?php
$mensaje = '';

class tipoAves{
    /*INSERT INTO tipoAve (idTipoAve, nombre) VALUES (0, 'Ponedora ligera')*/
    private $idTipoAve;
    private $nombre;
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

    public function setIdTipoAve($idTipoAve){$this->idTipoAve = $idTipoAve;}
    public function setNombre($nombre){$this->nombre = htmlspecialchars(strip_tags(trim($nombre)), ENT_QUOTES, 'UTF-8');}

    public function setMaxID()
    {
        try{
            if ($this->mysqli === null) { 
                    throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            $sql = "SELECT MAX(idTipoAve) AS maxID FROM tipoAve";
            $result = $this->mysqli->query($sql);
            if(!$result){
                throw new RuntimeException('Error al consultar el máximo idTipoAve: ' . $this->mysqli->error);
            }
            if ($result && $row = $result->fetch_assoc()) {
                $maxID = $row['maxID'] ?? 0;
                $this->idTipoAve = $maxID + 1;
            }else {
                throw new RuntimeException("Error al obtener el máximo idTipoAve: " . $this->mysqli->error);
            }
        }catch(RuntimeException $e) {
            throw $e;
        }
    }

    public function agregarNuevo($nombre)
    {
        $this->setNombre($nombre);
        $this->setMaxID();
        try{
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            $sqlCheck = "SELECT idTipoAve, nombre FROM tipoAve WHERE nombre = ?";
            $stmtCheck = $this->mysqli->prepare($sqlCheck);
            if (!$stmtCheck) {
                throw new RuntimeException("Error en la preparación de la consulta de verificación: " . $this->mysqli->error);
            }
            $stmtCheck->bind_param("s", $this->nombre);
            $stmtCheck->execute();
            $stmtCheck->store_result();
            if ($stmtCheck->num_rows > 0) {
                $stmtCheck->close();
                throw new RuntimeException("Error, ya existe: " . $this->mysqli->error);
            }
            $stmtCheck->close();
            $sql = "INSERT INTO tipoAve (idTipoAve, nombre) 
                    VALUES (?, ?)";
            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException("Error en la preparación de la consulta de inserción: " . $this->mysqli->error);
            }
            // Enlaza los parámetros y ejecuta la consulta
            $stmt->bind_param("is", $this->idTipoAve, $this->nombre);
            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }
            $stmt->close();
            return true;
        }catch(RuntimeException $e) {
            throw $e;
        }
    }

    public function deleteTipoAve($idTipoAve)
    {
        try {
            if ($this->mysqli === null) { 
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            if (!is_numeric($idTipoAve) || $idTipoAve <= 0) {
                throw new RuntimeException('El ID debe ser un número válido.');
            }
            $this->setIdTipoAve($idTipoAve);

            $sql = "DELETE FROM tipoAve WHERE idTipoAve = ?";
            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error);
            }
            $stmt->bind_param('i', $this->idTipoAve);
            if (!$stmt->execute()) {
                if ($this->mysqli->errno == 1451) {
                    throw new RuntimeException('El tipo de ave tiene registros asociados.');
                } else {
                    throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error); 
                }
            }
            // VERIFICAR SI REALMENTE SE ELIMINÓ ALGO
            $affectedRows = $stmt->affected_rows;
            $stmt->close();
            if ($affectedRows === 0) {
                error_log('No se encontró el tipo de ave con el ID especificado.');
                error_log($this->idTipoAve);
            }
            return true;
        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function updateTipoAve($idTipo, $nombre)
    {
        $this->setIdTipoAve($idTipo);
        $this->setNombre($nombre);
        try{
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            $sql = "UPDATE tipoAve SET nombre = ? WHERE idtipoAve = ?";
            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException("Error en la preparación de la consulta de actualización: " . $this->mysqli->error);
            }
            // Enlazar parámetros y ejecutar la consulta
            $stmt->bind_param("si", $this->nombre, $this->idTipoAve);
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

    public function getall()
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

    public function instanciarTipo($idTipoAve)
    {
        $this->setIdTipoAve($idTipoAve);
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            $sql = "SELECT idTipoAve, nombre FROM tipoAve WHERE idTipoAve = ?";
            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException("Error en la preparación de la consulta: " . $this->mysqli->error);
            }
            $stmt->bind_param("i", $idTipoAve);
            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }
            $result = $stmt->get_result();
            if ($result === false) {
                throw new RuntimeException('Error al obtener el resultado: ' . $stmt->error);
            }
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $this->setNombre($row['nombre']);
            } else {
                throw new RuntimeException('No se encontró el tipo de ave con ID: ' . $idTipoAve);
            }
            $stmt->close();
            return true;
        } catch (RuntimeException $e) {
            throw $e;
        }
    }
}

class LoteAves{
    private $idLoteAves;
    private $idTipoAve;
    private $identificador;
    private $fechaNacimiento;
    private $fechaCompra;
    private $cantidadAves;
    private $tipoAves; //Los datos del tipo de aves lo maneja ese objeto. Uso -> para obtenerlos.
    private $idGalpon_loteAve; // Para manejar la relación con galpon_loteAves
    private $precioCompra;
    private $mysqli;

    public function __construct()
    {
        $this->mysqli = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        if ($this->mysqli->connect_error) {
            die("Error de conexión a la base de datos: " . $this->mysqli->connect_error);
        }
        $this->tipoAves = new tipoAves(); // Inicializar la instancia de tipoAves
    }
    public function __destruct()
    {
        if ($this->mysqli !== null) {
            $this->mysqli->close();
        }
    }
    public function setIdTipoAve($idTipoAve){
        $this->idTipoAve = $idTipoAve;
        $this->tipoAves->instanciarTipo($idTipoAve);
    }
    public function setIdentificador($identificador){$this->identificador = htmlspecialchars(strip_tags(trim($identificador)), ENT_QUOTES, 'UTF-8');}
    public function setFechaNac($fecha){
        $this->fechaNacimiento = new DateTime($fecha);
        $this->fechaNacimiento = $this->fechaNacimiento->format('Y-m-d H:i:s');
    }
    public function setFechaCompra($fecha){
        $this->fechaCompra = new DateTime($fecha);
        $this->fechaCompra = $this->fechaCompra->format('Y-m-d H:i:s');
    }
    public function setCantAves($cantidadAves){$this->cantidadAves = $cantidadAves;}
    public function setIdLoteAves($idLoteAves){$this->idLoteAves = $idLoteAves;}
    public function setPrecioCompra($precio){$this->precioCompra = $precio;}

    public function setMaxID()
    {
        try{
            if ($this->mysqli === null) { 
                    throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            $sql = "SELECT MAX(idLoteAves) AS maxID FROM loteAves";
            $result = $this->mysqli->query($sql);
            if(!$result){
                throw new RuntimeException('Error al consultar el máximo idLoteAves: ' . $this->mysqli->error);
            }
            if ($result && $row = $result->fetch_assoc()) {
                $maxID = $row['maxID'] ?? 0;
                $this->idLoteAves = $maxID + 1;
            }else {
                throw new RuntimeException("Error al obtener el máximo idLoteAves: " . $this->mysqli->error);
            }
        }catch(RuntimeException $e) {
            throw $e;
        }
    }

    public function setMaxIDGalponLoteAve()
    {
        $result = $this->mysqli->query("SELECT MAX(idGalpon_loteAve) AS maxID FROM galpon_loteAves");
        $row = $result->fetch_assoc();
        $this->idGalpon_loteAve = ($row['maxID'] !== null) ? $row['maxID'] + 1 : 1;
    }
    
    public function agregarNuevo($identificador, $fechaNac, $fechaCompra, $cantidadAves, $idTipoAve, $idGalpon, $precioCompra)
    {
        $this->setPrecioCompra($precioCompra);
        $this->setIdentificador($identificador);
        $this->setFechaNac($fechaNac);
        $this->setFechaCompra($fechaCompra);
        $this->setCantAves($cantidadAves);
        $this->setIdTipoAve($idTipoAve);
        $this->setMaxID(); 
        $this->setMaxIDGalponLoteAve(); // <- Nuevo

        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }

            // === Verificación duplicados ===
            $sqlCheck = "SELECT idLoteAves FROM loteAves WHERE identificador = ?";
            $stmtCheck = $this->mysqli->prepare($sqlCheck);
            if (!$stmtCheck) throw new RuntimeException("Error en la preparación de verificación: " . $this->mysqli->error);
            $stmtCheck->bind_param("s", $this->identificador);
            $stmtCheck->execute();
            $stmtCheck->store_result();
            if ($stmtCheck->num_rows > 0) {
                $stmtCheck->close();
                throw new RuntimeException("Error: ya existe un lote con identificador '{$this->identificador}'.");
            }
            $stmtCheck->close();

            // === Inserción del nuevo lote ===
            $sql = "INSERT INTO loteAves (idLoteAves, identificador, fechaNacimiento, fechaCompra, cantidadAves, idTipoAve, precioCompra)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) throw new RuntimeException("Error en la preparación de inserción: " . $this->mysqli->error);
            $stmt->bind_param("isssdid", $this->idLoteAves, $this->identificador, $this->fechaNacimiento, $this->fechaCompra, $this->cantidadAves, $this->idTipoAve, $this->precioCompra);
            if (!$stmt->execute()) throw new RuntimeException('Error al ejecutar la inserción: ' . $stmt->error);
            $stmt->close();

            $sqlGalpon = "INSERT INTO galpon_loteAves (idGalpon_loteAve, idLoteAves, idGalpon, fechaInicio) VALUES (?, ?, ?, ?)";
            $stmtGalpon = $this->mysqli->prepare($sqlGalpon);
            if (!$stmtGalpon) throw new RuntimeException("Error en preparación de galpon_loteAves: " . $this->mysqli->error);
            $stmtGalpon->bind_param("iiis", $this->idGalpon_loteAve, $this->idLoteAves, $idGalpon, $this->fechaCompra);
            if (!$stmtGalpon->execute()) throw new RuntimeException('Error al insertar en galpon_loteAves: ' . $stmtGalpon->error);
            $stmtGalpon->close();

            return true;

        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function deleteLoteAves($idLoteAves)
    {
        try {
            if ($this->mysqli === null) { 
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            if (!is_numeric($idLoteAves) || $idLoteAves <= 0) {
                throw new RuntimeException('El ID debe ser un número válido.');
            }
            $this->setIdLoteAves($idLoteAves);

            // Verificar registros en galpon_loteAves
            $sql = "SELECT COUNT(*) AS cnt, idGalpon_loteAve FROM galpon_loteAves WHERE idLoteAves = ?";
            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) throw new RuntimeException('Error al preparar consulta de galpon_loteAves: ' . $this->mysqli->error);
            $stmt->bind_param("i", $this->idLoteAves);
            $stmt->execute();
            $result = $stmt->get_result();
            $galpones = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            if (count($galpones) > 1) {
                throw new RuntimeException('No se puede eliminar: el lote tiene más de un galpón asociado.');
            }

            // Verificar registros en otras tablas asociadas
            $tablas = ['mortandadAves', 'pesajeLoteAves', 'bajaloteaves'];
            foreach ($tablas as $tabla) {
                $sql = "SELECT COUNT(*) AS cnt FROM $tabla WHERE idLoteAves = ?";
                $stmt = $this->mysqli->prepare($sql);
                if (!$stmt) throw new RuntimeException("Error al preparar consulta en $tabla: " . $this->mysqli->error);
                $stmt->bind_param("i", $this->idLoteAves);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $stmt->close();

                if ($row['cnt'] > 0) {
                    throw new RuntimeException("No se puede eliminar: hay registros en $tabla.");
                }
            }

            // Eliminar registro único en galpon_loteAves
            if (count($galpones) === 1) {
                $idGalpon_loteAve = $galpones[0]['idGalpon_loteAve'];
                $sql = "DELETE FROM galpon_loteAves WHERE idGalpon_loteAve = ?";
                $stmt = $this->mysqli->prepare($sql);
                if (!$stmt) throw new RuntimeException('Error al preparar la eliminación de galpon_loteAves: ' . $this->mysqli->error);
                $stmt->bind_param("i", $idGalpon_loteAve);
                if (!$stmt->execute()) {
                    throw new RuntimeException('Error al eliminar el registro de galpon_loteAves: ' . $stmt->error);
                }
                $stmt->close();
            }

            // Eliminar el lote
            $sql = "DELETE FROM loteAves WHERE idLoteAves = ?";
            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) throw new RuntimeException('Error al preparar la consulta de eliminación del lote: ' . $this->mysqli->error);
            $stmt->bind_param('i', $this->idLoteAves);
            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la eliminación del lote: ' . $stmt->error);
            }
            $affectedRows = $stmt->affected_rows;
            $stmt->close();

            if ($affectedRows === 0) {
                throw new RuntimeException('No se encontró el lote de aves con el ID especificado.');
            }

            return true;

        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function cambiarUbicacion($idLoteAves, $idNuevoGalpon, $fechaDesdeNueva)
    {
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }

            if (!is_numeric($idLoteAves) || $idLoteAves < 0) {
                throw new RuntimeException('ID de lote inválido.');
            }

            // 1. Obtener la ubicación actual del lote
            $sqlActual = "SELECT idGalpon_loteAve, fechaInicio, fechaFin 
                        FROM galpon_loteAves 
                        WHERE idLoteAves = ? 
                        ORDER BY fechaInicio DESC 
                        LIMIT 1";
            $stmtActual = $this->mysqli->prepare($sqlActual);
            if (!$stmtActual) throw new RuntimeException('Error al preparar consulta de ubicación actual: ' . $this->mysqli->error);
            $stmtActual->bind_param("i", $idLoteAves);
            $stmtActual->execute();
            $result = $stmtActual->get_result();
            $ubicacionActual = $result->fetch_assoc();
            $stmtActual->close();

            if (!$ubicacionActual) {
                throw new RuntimeException('No se encontró la ubicación actual del lote.');
            }

            // 2. Validar fecha
            $fechaActualDesde = $ubicacionActual['fechaInicio'];
            if ($fechaDesdeNueva < $fechaActualDesde) {
                throw new RuntimeException('La fecha de inicio de la nueva ubicación no puede ser anterior a la fecha de la ubicación actual.');
            }

            // 3. Actualizar la fechaHasta del registro actual
            $sqlUpdate = "UPDATE galpon_loteAves SET fechaFin = ? WHERE idGalpon_loteAve = ?";
            $stmtUpdate = $this->mysqli->prepare($sqlUpdate);
            if (!$stmtUpdate) throw new RuntimeException('Error al preparar actualización de ubicación: ' . $this->mysqli->error);
            $stmtUpdate->bind_param("si", $fechaDesdeNueva, $ubicacionActual['idGalpon_loteAve']);
            if (!$stmtUpdate->execute()) {
                throw new RuntimeException('Error al actualizar fechaHasta: ' . $stmtUpdate->error);
            }
            $stmtUpdate->close();

            // 4. Insertar el nuevo registro con fechaHasta NULL
            $this->setMaxIDGalponLoteAve(); // Asegurarse de obtener el nuevo ID
            $sqlInsert = "INSERT INTO galpon_loteAves (idGalpon_loteAve, idLoteAves, idGalpon, fechaInicio, fechaFin)
                        VALUES (?, ?, ?, ?, NULL)";
            $stmtInsert = $this->mysqli->prepare($sqlInsert);
            if (!$stmtInsert) throw new RuntimeException('Error al preparar inserción de nueva ubicación: ' . $this->mysqli->error);
            $stmtInsert->bind_param("iiis", $this->idGalpon_loteAve, $idLoteAves, $idNuevoGalpon, $fechaDesdeNueva);
            if (!$stmtInsert->execute()) {
                throw new RuntimeException('Error al insertar nueva ubicación: ' . $stmtInsert->error);
            }
            $stmtInsert->close();

            return true;

        } catch (RuntimeException $e) {
            error_log($e);
            throw $e;
        }
    }

    public function getCambiosUbicacion($idLoteAves)
    {
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            if (!is_numeric($idLoteAves) || $idLoteAves < 0) {
                throw new RuntimeException('ID de lote inválido.');
            }
            $sql = "SELECT 
                        gl.idGalpon_loteAve,
                        gl.idLoteAves,
                        gl.idGalpon,
                        g.identificacion AS galponIdentificacion,
                        gl.fechaInicio,
                        gl.fechaFin,
                        gr.nombre as nombreGranja
                    FROM galpon_loteAves gl
                    INNER JOIN galpon g ON gl.idGalpon = g.idGalpon
                    INNER JOIN granja gr ON g.idGranja = gr.idGranja
                    WHERE gl.idLoteAves = ?
                    ORDER BY gl.fechaInicio, gl.idGalpon_loteAve ASC ";

            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error);
            }
            $stmt->bind_param('i', $idLoteAves);
            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }
            $result = $stmt->get_result();
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

    public function updateLoteAves($idLoteAves, $identificador, $fechaNac, $fechaCompra, $cantidadAves, $idTipoAve, $precioCompra)
    {
        $this->setPrecioCompra($precioCompra);
        $this->setIdLoteAves($idLoteAves);
        $this->setIdentificador($identificador);
        $this->setFechaNac($fechaNac);
        $this->setFechaCompra($fechaCompra);
        $this->setCantAves($cantidadAves);
        $this->setIdTipoAve($idTipoAve);
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            $sql = "UPDATE loteAves
                    SET identificador = ?, fechaNacimiento = ?, fechaCompra = ?, cantidadAves = ?, idTipoAve = ?, precioCompra = ?
                    WHERE idLoteAves = ?";
            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException("Error en la preparación de la consulta de actualización: " . $this->mysqli->error);
            }
            $stmt->bind_param("sssiidi", $this->identificador, $this->fechaNacimiento, $this->fechaCompra, $this->cantidadAves, $this->idTipoAve, $this->precioCompra, $this->idLoteAves);

            $stmt->bind_param(
                "sssiidi",
                $this->identificador,
                $this->fechaNacimiento,
                $this->fechaCompra,
                $this->cantidadAves,
                $this->idTipoAve,
                $this->precioCompra,
                $this->idLoteAves
            );
            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }
            $stmt->close();
            return true;
        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function getAllFiltro($idGranja, $desde, $hasta)
    //Getall seria muy bestia para el ABM. Se aplicaron 3 filtros básicos. 
    //Se podría aplicar filtro por galpón, pero en la datatable
    //con escribirse el nombre del galpon basta para filtrar por eso.
    {
        try {
            if ($this->mysqli === null) { 
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }

            $sql = "SELECT 
                        l.idLoteAves,
                        l.identificador,
                        l.fechaNacimiento,
                        l.fechaCompra,
                        l.cantidadAves,
                        t.nombre AS tipoAveNombre,
                        g.idGalpon,
                        g.identificacion AS galponIdentificacion,
                        gr.idGranja,
                        l.precioCompra,
                        gr.nombre AS granjaNombre
                    FROM loteAves l
                    INNER JOIN tipoAve t ON l.idTipoAve = t.idTipoAve
                    INNER JOIN galpon_loteAves gl 
                        ON l.idLoteAves = gl.idLoteAves AND gl.fechaFin IS NULL
                    INNER JOIN galpon g ON gl.idGalpon = g.idGalpon
                    INNER JOIN granja gr ON g.idGranja = gr.idGranja
                    WHERE gr.idGranja = ?
                    AND l.fechaNacimiento BETWEEN ? AND ?
                    ORDER BY l.idLoteAves ASC";

            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error);
            }

            $stmt->bind_param('iss', $idGranja, $desde, $hasta);

            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $result = $stmt->get_result();
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

    public function getAll()
    //Getall para el select de los registros sobre lotes como mortandad, vacunas, movimientos de galpones.
    //Tiene en cuenta los dados de baja para NO MOSTRARLOS
    {
        try {
            if ($this->mysqli === null) { 
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            $sql = "SELECT 
                    l.idLoteAves,
                    l.identificador,
                    l.fechaNacimiento,
                    l.fechaCompra,
                    l.precioCompra,
                    l.cantidadAves,
                    t.nombre AS tipoAveNombre,
                    g.idGalpon,
                    g.identificacion AS galponIdentificacion,
                    gr.idGranja,
                    gr.nombre AS granjaNombre
                FROM loteAves l
                INNER JOIN tipoAve t ON l.idTipoAve = t.idTipoAve
                INNER JOIN galpon_loteAves gl 
                    ON l.idLoteAves = gl.idLoteAves AND gl.fechaFin IS NULL
                INNER JOIN galpon g ON gl.idGalpon = g.idGalpon
                INNER JOIN granja gr ON g.idGranja = gr.idGranja
                WHERE NOT EXISTS (
                    SELECT 1 
                    FROM bajaLoteAves b 
                    WHERE b.idLoteAves = l.idLoteAves
                )
                ORDER BY l.idLoteAves ASC";
            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error);
            }
            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }
            $result = $stmt->get_result();
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

    public function getAllWithBajas()
    //Getall para el select de los registros sobre lotes como mortandad, vacunas, movimientos de galpones.
    //Tiene en cuenta los dados de baja para MOSTRARLOS
    {
        try {
            if ($this->mysqli === null) { 
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            $sql = "SELECT 
                        l.idLoteAves,
                        l.identificador,
                        l.fechaNacimiento,
                        l.fechaCompra,
                        l.precioCompra,
                        l.cantidadAves,
                        t.nombre AS tipoAveNombre,
                        g.idGalpon,
                        g.identificacion AS galponIdentificacion,
                        gr.idGranja,
                        gr.nombre AS granjaNombre
                    FROM loteAves l
                    INNER JOIN tipoAve t ON l.idTipoAve = t.idTipoAve
                    INNER JOIN galpon_loteAves gl 
                        ON l.idLoteAves = gl.idLoteAves AND gl.fechaFin IS NULL
                    INNER JOIN galpon g ON gl.idGalpon = g.idGalpon
                    INNER JOIN granja gr ON g.idGranja = gr.idGranja
                    ORDER BY l.idLoteAves ASC";
            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error);
            }
            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }
            $result = $stmt->get_result();
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

    public function getById($idLoteAves)
    {
        try {
            if ($this->mysqli === null) { 
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }

            $sql = "
                SELECT 
                    l.idLoteAves,
                    l.identificador,
                    l.fechaNacimiento,
                    l.fechaCompra,
                    l.cantidadAves,
                    l.precioCompra,
                    t.nombre AS tipoAveNombre,
                    g.idGalpon,
                    g.identificacion AS galponIdentificacion,
                    gr.idGranja,
                    gr.nombre AS granjaNombre,
                    -- Cantidad actual = cantidad inicial - suma de mortandades
                    (l.cantidadAves - IFNULL(
                        (SELECT SUM(m.cantidad) 
                        FROM mortandadAves m 
                        WHERE m.idLoteAves = l.idLoteAves), 0)
                    ) AS cantidadActual,
                    -- Último peso registrado
                    (SELECT p.peso 
                    FROM pesajeLoteAves p 
                    WHERE p.idLoteAves = l.idLoteAves 
                    ORDER BY p.fecha DESC, p.idPesaje DESC
                    LIMIT 1) AS ultimoPeso
                FROM loteAves l
                INNER JOIN tipoAve t ON l.idTipoAve = t.idTipoAve
                INNER JOIN galpon_loteAves gl 
                    ON l.idLoteAves = gl.idLoteAves AND gl.fechaFin IS NULL
                INNER JOIN galpon g ON gl.idGalpon = g.idGalpon
                INNER JOIN granja gr ON g.idGranja = gr.idGranja
                WHERE l.idLoteAves = ?
            ";

            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error);
            }

            $stmt->bind_param('i', $idLoteAves);

            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();

            if (!$data) {
                throw new RuntimeException("No se encontró un lote de aves con el ID especificado.");
            }

            return $data;

        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    // ============================
    // MORTANDAD AVES
    // ============================
    public function agregarMortandad(int $idLoteAves, string $fecha, string $causa, int $cantidad): bool
    {
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }

            $sql = "INSERT INTO mortandadAves (fecha, causa, cantidad, idLoteAves)
                    VALUES (?, ?, ?, ?)";

            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error);
            }

            $stmt->bind_param('ssii', $fecha, $causa, $cantidad, $idLoteAves);

            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $stmt->close();
            return true;

        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function editarMortandad(int $idMortandad, string $fecha, string $causa, int $cantidad): bool
    {
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }

            $sql = "UPDATE mortandadAves
                    SET fecha = ?, causa = ?, cantidad = ?
                    WHERE idMortandad = ?";

            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error);
            }

            $stmt->bind_param('ssii', $fecha, $causa, $cantidad, $idMortandad);

            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $stmt->close();
            return true;

        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function deleteMortandad(int $idMortandad): bool
    {
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            $sql = "DELETE FROM mortandadAves WHERE idMortandad = ?";
            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error);
            }
            $stmt->bind_param('i', $idMortandad);
            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }
            $affectedRows = $stmt->affected_rows;
            $stmt->close();
            if ($affectedRows === 0) {
                throw new RuntimeException("No se encontró un registro de mortandad con el ID especificado.");
            }
            return true;
        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function getMortandad(int $idLoteAves): array
    {
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }

            $sql = "SELECT idMortandad, fecha, causa, cantidad, idLoteAves
                    FROM mortandadAves
                    WHERE idLoteAves = ?
                ORDER BY fecha DESC";

            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error);
            }

            $stmt->bind_param('i', $idLoteAves);

            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);

            $stmt->close();
            return $data;

        } catch (RuntimeException $e) {
            throw $e;
        }
    }
    // ============================
    // PESAJES AVES
    // ============================
    public function agregarPesaje(int $idLoteAves, string $fecha, float $peso): bool
    {
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }

            $sql = "INSERT INTO pesajeLoteAves (fecha, peso, idLoteAves)
                    VALUES (?, ?, ?)";

            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error);
            }

            $stmt->bind_param('sdi', $fecha, $peso, $idLoteAves);

            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $stmt->close();
            return true;

        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function editarPesaje(int $idPesaje, string $fecha, float $peso): bool
    {
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }

            $sql = "UPDATE pesajeLoteAves
                    SET fecha = ?, peso = ?
                    WHERE idPesaje = ?";

            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error);
            }

            $stmt->bind_param('sdi', $fecha, $peso, $idPesaje);

            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $stmt->close();
            return true;

        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function deletePesaje(int $idPesaje): bool
    {
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }

            $sql = "DELETE FROM pesajeLoteAves WHERE idPesaje = ?";
            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error);
            }

            $stmt->bind_param('i', $idPesaje);

            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $affectedRows = $stmt->affected_rows;
            $stmt->close();

            if ($affectedRows === 0) {
                throw new RuntimeException("No se encontró un registro de pesaje con el ID especificado.");
            }

            return true;

        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function getPesaje(int $idLoteAves): array
    {
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }

            $sql = "SELECT idPesaje, fecha, peso, idLoteAves
                    FROM pesajeLoteAves
                    WHERE idLoteAves = ?
                    ORDER BY fecha DESC";

            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error);
            }

            $stmt->bind_param('i', $idLoteAves);

            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);

            $stmt->close();
            return $data;

        } catch (RuntimeException $e) {
            throw $e;
        }
    }
    // ============================
    // BAJAS DE LOTES DE AVES
    // ============================
    public function addBaja($idLoteAves, $fechaBaja, $precioVenta, $motivo)
    {
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }

            // === Verificar que el lote existe y no tenga ya una baja ===
            $sqlCheck = "SELECT idBajaLoteAves FROM bajaLoteAves WHERE idLoteAves = ?";
            $stmtCheck = $this->mysqli->prepare($sqlCheck);
            if (!$stmtCheck) throw new RuntimeException("Error en la preparación de verificación: " . $this->mysqli->error);
            $stmtCheck->bind_param("i", $idLoteAves);
            $stmtCheck->execute();
            $stmtCheck->store_result();
            if ($stmtCheck->num_rows > 0) {
                $stmtCheck->close();
                throw new RuntimeException("Error: el lote $idLoteAves ya fue dado de baja.");
            }
            $stmtCheck->close();

            // === Insertar en bajaLoteAves ===
            $sql = "INSERT INTO bajaLoteAves (fechaBaja, precioVenta, idLoteAves, motivo)
                    VALUES (?, ?, ?, ?)";
            $stmt = $this->mysqli->prepare($sql);
            if (!$stmt) throw new RuntimeException("Error en la preparación de inserción: " . $this->mysqli->error);
            $stmt->bind_param("sdis", $fechaBaja, $precioVenta, $idLoteAves, $motivo);
            if (!$stmt->execute()) throw new RuntimeException('Error al ejecutar la inserción: ' . $stmt->error);
            $stmt->close();

            // === Opcional: cerrar galpon_loteAves ===
            $sqlUpdate = "UPDATE galpon_loteAves 
                        SET fechaFin = ? 
                        WHERE idLoteAves = ? AND fechaFin IS NULL";
            $stmtUpdate = $this->mysqli->prepare($sqlUpdate);
            if (!$stmtUpdate) throw new RuntimeException("Error en preparación de cierre de galpón: " . $this->mysqli->error);
            $stmtUpdate->bind_param("si", $fechaBaja, $idLoteAves);
            if (!$stmtUpdate->execute()) throw new RuntimeException('Error al cerrar galpon_loteAves: ' . $stmtUpdate->error);
            $stmtUpdate->close();

            return true;

        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function deleteBaja($idBajaLoteAves)
    {
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }

            // === Obtener idLoteAves antes de borrar ===
            $sqlGet = "SELECT idLoteAves, fechaBaja 
                    FROM bajaLoteAves 
                    WHERE idBajaLoteAves = ?";
            $stmtGet = $this->mysqli->prepare($sqlGet);
            if (!$stmtGet) throw new RuntimeException("Error en preparación de búsqueda: " . $this->mysqli->error);
            $stmtGet->bind_param("i", $idBajaLoteAves);
            $stmtGet->execute();
            $result = $stmtGet->get_result();
            $row = $result->fetch_assoc();
            $stmtGet->close();

            if (!$row) {
                throw new RuntimeException("Error: no se encontró la baja con ID $idBajaLoteAves.");
            }

            $idLoteAves = $row['idLoteAves'];

            // === Eliminar la baja ===
            $sqlDel = "DELETE FROM bajaLoteAves WHERE idBajaLoteAves = ?";
            $stmtDel = $this->mysqli->prepare($sqlDel);
            if (!$stmtDel) throw new RuntimeException("Error en preparación de borrado: " . $this->mysqli->error);
            $stmtDel->bind_param("i", $idBajaLoteAves);
            if (!$stmtDel->execute()) throw new RuntimeException("Error al eliminar baja: " . $stmtDel->error);
            $stmtDel->close();

            // === Reabrir solo el último galpón asignado ===
            $sqlUpdate = "UPDATE galpon_loteAves 
                        SET fechaFin = NULL 
                        WHERE idLoteAves = ? 
                        AND fechaFin IS NOT NULL
                        ORDER BY fechaFin DESC 
                        LIMIT 1";
            $stmtUpdate = $this->mysqli->prepare($sqlUpdate);
            if (!$stmtUpdate) throw new RuntimeException("Error en preparación de reapertura: " . $this->mysqli->error);
            $stmtUpdate->bind_param("i", $idLoteAves);
            if (!$stmtUpdate->execute()) throw new RuntimeException('Error al reabrir galpon_loteAves: ' . $stmtUpdate->error);
            $stmtUpdate->close();

            return true;

        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    public function getBajas()
    {
        try {
            if ($this->mysqli === null) { 
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }
            $sql = "SELECT 
                        l.idLoteAves,
                        l.identificador,
                        l.fechaNacimiento,
                        l.fechaCompra,
                        l.precioCompra,
                        l.cantidadAves,
                        t.nombre AS tipoAveNombre,
                        bl.idBajaLoteAves,
                        bl.fechaBaja,
                        bl.precioVenta,
                        bl.motivo
                    FROM loteAves l
                    INNER JOIN tipoAve t ON l.idTipoAve = t.idTipoAve
                    INNER JOIN bajaLoteAves bl ON l.idLoteAves = bl.idLoteAves
                    ORDER BY l.idLoteAves ASC";
            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error);
            }
            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }
            $result = $stmt->get_result();
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

    // --- Aplicación de vacunas ---
    // Agregar aplicación de vacuna a un lote de aves
    public function agregarVacuna(int $idLoteAves, int $idLoteVacuna, string $fecha, int $cantidad): bool
    {
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }

            $sql = "INSERT INTO loteVacuna_loteAve (idLoteAves, idLoteVacuna, fecha, cantidad)
                    VALUES (?, ?, ?, ?)";

            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error);
            }

            $stmt->bind_param('iisi', $idLoteAves, $idLoteVacuna, $fecha, $cantidad);

            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $stmt->close();
            return true;

        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    // Editar aplicación de vacuna
    public function editarVacuna(int $idAplicacion, int $idLoteVacuna, string $fecha, int $cantidad): bool
    {
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }

            $sql = "UPDATE loteVacuna_loteAve
                    SET idLoteVacuna = ?, fecha = ?, cantidad = ?
                    WHERE idloteVacuna_loteAve = ?";

            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error);
            }

            $stmt->bind_param('isii', $idLoteVacuna, $fecha, $cantidad, $idAplicacion);

            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $stmt->close();
            return true;

        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    // Eliminar aplicación de vacuna
    public function deleteVacuna(int $idAplicacion): bool
    {
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }

            $sql = "DELETE FROM loteVacuna_loteAve WHERE idloteVacuna_loteAve = ?";
            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error);
            }

            $stmt->bind_param('i', $idAplicacion);
            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $affectedRows = $stmt->affected_rows;
            $stmt->close();
            if ($affectedRows === 0) {
                throw new RuntimeException("No se encontró un registro de aplicación con el ID especificado.");
            }
            return true;

        } catch (RuntimeException $e) {
            throw $e;
        }
    }

    // Obtener aplicaciones de vacunas de un lote de aves
    public function getVacunas(int $idLoteAves): array
    {
        try {
            if ($this->mysqli === null) {
                throw new RuntimeException('La conexión a la base de datos no está inicializada.');
            }

            $sql = "SELECT lva.idloteVacuna_loteAve, lva.idLoteVacuna, lv.numeroLote, lv.idVacuna, v.nombre AS vacunaNombre,
                        lva.fecha, lva.cantidad
                    FROM loteVacuna_loteAve lva
                    INNER JOIN loteVacuna lv ON lva.idLoteVacuna = lv.idLoteVacuna
                    INNER JOIN vacuna v ON lv.idVacuna = v.idVacuna
                    WHERE lva.idLoteAves = ?
                    ORDER BY lva.fecha DESC";

            $stmt = $this->mysqli->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('Error al preparar la consulta: ' . $this->mysqli->error);
            }

            $stmt->bind_param('i', $idLoteAves);

            if (!$stmt->execute()) {
                throw new RuntimeException('Error al ejecutar la consulta: ' . $stmt->error);
            }

            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);

            $stmt->close();
            return $data;

        } catch (RuntimeException $e) {
            throw $e;
        }
    }
}