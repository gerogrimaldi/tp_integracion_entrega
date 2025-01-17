<?php
require_once 'model/vacunasModel.php';

if (isset($_GET['ajax']))
{
    switch ($_GET['ajax']) {
    // ------------------------------------
    // SOLICITUDES AJAX - VACUNAS
    // ------------------------------------
        case 'getVacunas':
            header('Content-Type: application/json');
            try {
                $oVacuna = new vacuna();
                if ($vacunas = $oVacuna->getall()) {
                    http_response_code(200);
                    echo json_encode($vacunas);
                } else {
                    // Si no hay registros
                    http_response_code(200);
                    echo '[]';
                }
            } catch (RuntimeException $e) {
                http_response_code(400);
                // echo json_encode(['msg' => $e->getMessage()]);
                echo json_encode(['msg' => 'Error al obtener los galpones y sus granjas.']);
            }
            exit();

        case 'getViasAplicacion':
            header('Content-Type: application/json');
            try {
                $oVacuna = new vacuna();
                if ($viaAplicacion = $oVacuna->getAllViaAplicacion()) {
                    http_response_code(200);
                    echo json_encode($viaAplicacion);
                } else {
                    // Si no hay registros
                    http_response_code(200);
                    echo '[]';
                }
            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['msg' => 'Error al obtener vías de aplicación.']);
            }
            exit();
            
        case 'delVacuna': 
            header('Content-Type: application/json');
            try {
                if (!isset($_GET['idVacuna']) || $_GET['idVacuna'] === '') {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: no se ha seleccionado una vacuna.']);
                    exit();
                }
                $oVacuna = new vacuna();
                if ($oVacuna->deleteVacunaPorId($_GET['idVacuna'])) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Eliminado correctamente.']);
                }
            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['msg' => 'Error al eliminar, tiene registros asociados']);
            }
            exit();
        break;

        case 'addVacuna':
            header('Content-Type: application/json');
            try {
                if (empty($_POST['nombre']) || (!isset($_POST['viaAplicacion']) || $_POST['viaAplicacion'] === '') || 
                    empty($_POST['marca']) || empty($_POST['enfermedad'])) {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: hay campos vacíos.']);
                    exit();
                }
                $oVacuna = new vacuna();
                $oVacuna->setMaxIDVacuna();
                $oVacuna->setIdViaAplicacion($_POST['viaAplicacion']);
                $oVacuna->setNombre($_POST['nombre']);
                $oVacuna->setMarca($_POST['marca']);
                $oVacuna->setEnfermedad($_POST['enfermedad']);

                // Respuesta al frontend
                if ($oVacuna->save()) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Vacuna agregada correctamente']);
                } 
            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['msg' => 'Error al añadir.']);
            }
            exit();
        break;

        case 'editVacuna':
            header('Content-Type: application/json');
            try {
                if (empty($_POST['nombre']) || (!isset($_POST['idVacuna']) || $_POST['idVacuna'] === '') || 
                    empty($_POST['marca']) || (!isset($_POST['viaAplicacion']) || $_POST['viaAplicacion'] === '') || 
                    empty($_POST['enfermedad'])) {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: hay campos vacíos.']);
                    exit();
                }
                $oVacuna = new vacuna();
                $oVacuna->setIdVacuna($_POST['idVacuna']);
                $oVacuna->setIdViaAplicacion($_POST['viaAplicacion']);
                $oVacuna->setNombre($_POST['nombre']);
                $oVacuna->setMarca($_POST['marca']);
                $oVacuna->setEnfermedad($_POST['enfermedad']);
                // Respuesta al frontend
                if ($oVacuna->update()) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Cambios guardados correctamente']);
                    // echo json_encode(['msg' => $e->getMessage()]);
                } 
            } catch (RuntimeException $e) {
                http_response_code(400);
                //echo json_encode(['msg' => $e->getMessage()]);
                echo json_encode(['msg' => 'Error al editar.']);
            }
            exit();
        break;

    // ------------------------------------
    // SOLICITUDES AJAX - LOTE DE VACUNAS
    // ------------------------------------
        case 'getLotesVacuna':
            header('Content-Type: application/json');
            try {
                if (!isset($_GET['idVacuna']) || $_GET['idVacuna'] === '') {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: no se ha seleccionado una vacuna.']);
                    exit();
                }
                $oLoteVacuna = new loteVacuna();
                if ($lote = $oLoteVacuna->getLotes($_GET['idVacuna'])) {
                    http_response_code(200);
                    echo json_encode($lote);
                } else {
                    // Si no hay registros
                    http_response_code(200);
                    echo '[]';
                }
            } catch (RuntimeException $e) {
                http_response_code(400);
                // echo json_encode(['msg' => $e->getMessage()]);
                echo json_encode(['msg' => 'Error al obtener los lotes.']);
            }
            exit();

        case 'delLoteVacuna': 
            header('Content-Type: application/json');
            try {
                if (!isset($_GET['idLoteVacuna']) || $_GET['idLoteVacuna'] === '') {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: no se ha seleccionado un lote de vacunas.']);
                    exit();
                }
                $oLoteVacuna = new loteVacuna();
                if ($oLoteVacuna->deleteLoteVacunaPorId($_GET['idLoteVacuna'])) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Eliminado correctamente.']);
                }
            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['msg' => 'Error al eliminar, tiene registros asociados']);
            }
            exit();
        break;

        case 'addLoteVacuna':
            header('Content-Type: application/json');
            try {
                if (empty($_POST['numeroLote']) || (!isset($_POST['idVacuna']) || $_POST['idVacuna'] === '') || 
                    empty($_POST['fechaCompra']) || empty($_POST['cantidad']) || empty($_POST['fechaVencimiento'])) {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: hay campos vacíos.']);
                    exit();
                }
                $oLoteVacuna = new loteVacuna();
                $oLoteVacuna->setMaxIDLoteVacuna();
                $oLoteVacuna->setNumeroLote($_POST['numeroLote']);
                $oLoteVacuna->setFechaCompra($_POST['fechaCompra']);
                $oLoteVacuna->setCantidad($_POST['cantidad']);
                $oLoteVacuna->setVencimiento($_POST['fechaVencimiento']);
                $oLoteVacuna->setIdVacuna($_POST['idVacuna']);
                // Respuesta al frontend
                if ($oLoteVacuna->save()) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Lote de vacunas agregada correctamente']);
                } 
            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['msg' => 'Error al añadir.']);
            }
            exit();
        break;

        case 'editLoteVacuna':
            header('Content-Type: application/json');
            try {
                if (empty($_POST['numeroLote']) || (!isset($_POST['idLoteVacuna']) || $_POST['idLoteVacuna'] === '') || 
                    empty($_POST['fechaCompra']) || 
                    empty($_POST['cantidad']) || empty($_POST['fechaVencimiento'])) {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: hay campos vacíos.']);
                    exit();
                }
                $oLoteVacuna = new loteVacuna();
                $oLoteVacuna->setIdLoteVacuna($_POST['idLoteVacuna']);
                $oLoteVacuna->setNumeroLote($_POST['numeroLote']);
                $oLoteVacuna->setFechaCompra($_POST['fechaCompra']);
                $oLoteVacuna->setCantidad($_POST['cantidad']);
                $oLoteVacuna->setVencimiento($_POST['fechaVencimiento']);
                // Respuesta al frontend
                if ($oLoteVacuna->update()) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Cambios guardados correctamente']);
                    // echo json_encode(['msg' => $e->getMessage()]);
                } 
            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['msg' => $e->getMessage()]);
                //echo json_encode(['msg' => 'Error al editar.']);
            }
            exit();
        break;

        default:
            exit();
        break;
    }
}