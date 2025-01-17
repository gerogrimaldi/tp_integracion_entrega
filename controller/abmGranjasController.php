<?php
require_once 'model/granjaModel.php';

if (isset($_GET['ajax']))
{
    switch ($_GET['ajax']) {
    // ------------------------------------
    // SOLICITUDES AJAX - GRANJAS
    // ------------------------------------
        case 'getGranjas':
            header('Content-Type: application/json');
            try {
                $oGranja = new Granja();
                $granjas = $oGranja->getall();
                   
                if ($granjas) {
                    http_response_code(200);
                    echo json_encode($granjas);
                }else{
                    http_response_code(200);
                    echo '[]';
                }
            } catch (RuntimeException $e) {
                    http_response_code(400);
                    echo json_encode(['msg' => $e->getMessage()]);
            }
            exit();

        case 'delGranja': 
            header('Content-Type: application/json');
            try {
                if( (!isset($_GET['idGranja']) || $_GET['idGranja'] === '') )
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: granja no seleccionada.']);
                    exit();
                }
                $oGranja = new Granja();
                $idGranja = (int)$_GET['idGranja'];

                if ($oGranja->deleteGranjaPorId($idGranja)) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Eliminado correctamente.']);
                }
            } catch (RuntimeException $e) {
                http_response_code(400);
                //No pasar los errores el JS, enviar uno personalizado.
                //echo json_encode(['msg' => $e->getMessage()]);
                echo json_encode(['msg' => 'Error al eliminar, tiene registros asociados']);
            }
            exit();
        break;

        case 'addGranja':
            header('Content-Type: application/json');
            try {

                if( empty($_POST['nombre']) || empty($_POST['habilitacion']) || 
                    empty($_POST['metrosCuadrados']) || empty($_POST['ubicacion']))
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: hay campos vacíos.']);
                    exit();
                }
                $oGranja = new Granja();
                $oGranja->setMaxID();
                $oGranja->setNombre($_POST['nombre']);
                $oGranja->setHabilitacionSenasa($_POST['habilitacion']);
                $oGranja->setMetrosCuadrados($_POST['metrosCuadrados']);
                $oGranja->setUbicacion($_POST['ubicacion']);
                // Respuesta al frontend
                if ($oGranja->save()) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Granja agregada correctamente']);
                } 
            } catch (RuntimeException $e) {
                    http_response_code(400);
                    // echo json_encode(['error' => $e->getMessage()]);
                    echo json_encode(['msg' => 'Error al añadir.']);
            }
            exit();
        break;

        case 'editGranja':
            header('Content-Type: application/json');
            try {
                if( empty($_POST['nombre']) || (!isset($_POST['idGranja']) || $_POST['idGranja'] === '') 
                || empty($_POST['habilitacion']) ||  empty($_POST['metrosCuadrados']) || empty($_POST['ubicacion']))
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: hay campos vacíos.']);
                    exit();
                }
                $oGranja = new Granja();
                $oGranja->setIdGranja ($_POST['idGranja']);
                $oGranja->setNombre($_POST['nombre']);
                $oGranja->setHabilitacionSenasa($_POST['habilitacion']);
                $oGranja->setMetrosCuadrados($_POST['metrosCuadrados']);
                $oGranja->setUbicacion($_POST['ubicacion']);
                // Respuesta al frontend
                if ($oGranja->update()) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Cambios guardados correctamente']);
                    //echo json_encode(['msg' => $e->getMessage()]);
                } 
            } catch (RuntimeException $e) {
                    http_response_code(400);
                    // echo json_encode(['msg' => $e->getMessage()]);
                    echo json_encode(['msg' => 'Error al editar.']);
            }
            exit();
        break;

        default:
            exit();
        break;
    }
}