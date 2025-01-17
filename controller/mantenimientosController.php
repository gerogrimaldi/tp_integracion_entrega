<?php
require_once 'model/mantenimientosModel.php';

if (isset($_GET['ajax']))
{
    switch ($_GET['ajax']) {
    // ------------------------------------
    // SOLICITUDES AJAX - TIPO DE MANTENIMIENTOS
    // ------------------------------------
        case 'addTipoMant':
            header('Content-Type: application/json');
            try {
                if( empty($_POST['nombreMant']) )
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: el campo está vacío.']);
                    exit();
                }
                $oTipoMantenimiento = new tipoMantenimiento();
                $oTipoMantenimiento->setMaxIDTipoMant();
                $oTipoMantenimiento->setNombreMantenimiento( $_POST['nombreMant']);
                // Respuesta a JS
                if ($oTipoMantenimiento->save()) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Insertado correctamente']);
                } 
            } catch (RuntimeException $e) {
                    http_response_code(400);
                    // echo json_encode(['error' => $e->getMessage()]);
                    echo json_encode(['msg' => 'Error al añadir. Ya existe.']);
            }
            exit();
        break;

        case 'delTipoMant': 
            header('Content-Type: application/json');
            try {
                if( (!isset($_GET['idTipoMant']) || $_GET['idTipoMant'] === '') )
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: no se seleccionó tipo de mantenimiento.']);
                    exit();
                }
                $oTipoMantenimiento = new tipoMantenimiento();
                $idTipoMant = (int)$_GET['idTipoMant'];
                if ($oTipoMantenimiento->deleteTipoMantID($idTipoMant)) {
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
    
        case 'editTipoMant':
            header('Content-Type: application/json');
            try {
                
                if( (!isset($_POST['idTipoMant']) || $_POST['idTipoMant'] === '') 
                || empty($_POST['nombreMantEdit']))
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: hay campos vacíos.']);
                    exit();
                }
                $oTipoMantenimiento = new tipoMantenimiento();
                $oTipoMantenimiento->setIDTipoMant($_POST['idTipoMant']);
                $oTipoMantenimiento->setNombreMantenimiento( $_POST['nombreMantEdit']);
                
                if ($oTipoMantenimiento->update()) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Cambios guardados correctamente']);
                } 
            } catch (RuntimeException $e) {
                    http_response_code(400);
                    //echo json_encode(['error' => $e->getMessage()]);
                    echo json_encode(['msg' => 'Error al guardar los cambios']);
            }
            exit();
        break;
    
        case 'getTipoMant':
            header('Content-Type: application/json');
            try {
                $oTipoMantenimiento = new tipoMantenimiento();
                $tiposMant = $oTipoMantenimiento->getTipoMantenimientos();
                if ($tiposMant) {
                    http_response_code(200);
                    echo json_encode($tiposMant);
                }else{
                    echo '[]';
                }
            } catch (RuntimeException $e) {
                    http_response_code(400);
                    echo json_encode(['error' => $e->getMessage()]);
            }
            exit();
        break;

    // ------------------------------------
    // SOLICITUDES AJAX - MANTENIMIENTOS DE GRANJA
    // ------------------------------------

        case 'newMantGranja':
            header('Content-Type: application/json');
            try {
                if( empty($_POST['fechaMant']) || (!isset($_POST['idGranja']) || $_POST['idGranja'] === '') || 
                (!isset($_POST['tipoMantenimiento']) || $_POST['tipoMantenimiento'] === ''))
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: hay campos vacíos.']);
                    exit();
                }
                $oMantenimientoGranja = new mantenimientoGranja();
                $oMantenimientoGranja->setMaxIDMantGranja();
                $oMantenimientoGranja->setFecha( $_POST['fechaMant']);
                $oMantenimientoGranja->setIdGranja( $_POST['idGranja'] );
                $oMantenimientoGranja->setIdTipoMantenimiento( $_POST['tipoMantenimiento'] );
                if ($oMantenimientoGranja->save()) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Mantenimiento agregado correctamente']);
                }
            }catch (RuntimeException $e) {
                    http_response_code(400);
                    //echo json_encode(['msg' => $e->getMessage()]);
                    echo json_encode(['msg' => 'Error al ingresar mantenimiento']);
            }
            exit();
        break;

        case 'getMantGranja':
            header('Content-Type: application/json');
            try {
                if( !isset($_GET['idGranja']) || $_GET['idGranja'] === '' )
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: no se ha seleccionado una granja.']);
                    exit();
                }
                If (!isset($_GET['desde']) || $_GET['desde'] === '' || !isset($_GET['hasta']) || $_GET['hasta'] === '') {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: rango de fechas no válido.']);
                    exit();
                }
                $oMantenimientoGranja = new MantenimientoGranja();
                if ($mantGranjas = $oMantenimientoGranja->getMantGranjas($_GET['idGranja'], $_GET['desde'], $_GET['hasta'])){
                    http_response_code(200);
                    echo json_encode($mantGranjas);
                }else{
                    http_response_code(200);
                    echo '[]';
                }
            } catch (RuntimeException $e) {
                    http_response_code(400);
                    //echo json_encode(['msg' => $e->getMessage()]);
                    echo json_encode(['msg' => 'Error al obtener mantenimientos.']);
            }
            exit();

        case 'delMantGranja': 
            header('Content-Type: application/json');
            try {
                if( !isset($_GET['idMantenimientoGranja']) || $_GET['idMantenimientoGranja'] === '' ){
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: mantenimiento no seleccionado.']);
                    exit();
                }
                $oMantenimientoGranja = new mantenimientoGranja();
                if ($oMantenimientoGranja->deleteMantenimientoGranjaId($_GET['idMantenimientoGranja'])) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Eliminado correctamente.']);
                }
            } catch (RuntimeException $e) {
                http_response_code(400);
                //echo json_encode(['msg' => $e->getMessage()]);
                echo json_encode(['msg' => 'Error al eliminar']);
            }
            exit();
        break;

    // ------------------------------------
    // SOLICITUDES AJAX - MANTENIMIENTOS GALPONES
    // ------------------------------------

        case 'newMantGalpon':
            header('Content-Type: application/json');
            try {
                if( empty($_POST['fechaMant']) || ( !isset($_POST['idGalpon']) || $_POST['idGalpon'] === '' ) || 
                (!isset($_POST['tipoMantenimiento']) || $_POST['tipoMantenimiento'] === ''))
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: hay campos vacíos.']);
                    exit();
                }
                $oMantenimientoGalpon = new mantenimientoGalpon();
                $oMantenimientoGalpon->setMaxIDMantGalpon();
                $oMantenimientoGalpon->setFecha( $_POST['fechaMant']);
                $oMantenimientoGalpon->setIdGalpon( $_POST['idGalpon'] );
                $oMantenimientoGalpon->setIdTipoMantenimiento( $_POST['tipoMantenimiento'] ); 
                if ($oMantenimientoGalpon->save()) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Mantenimiento agregado correctamente']);
                }
            }catch (RuntimeException $e) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Error al ingresar mantenimiento']);
            }
            exit();
        break;
        
        case 'getMantGalpon':
            header('Content-Type: application/json');
            try {
                if( !isset($_GET['idGalpon']) || $_GET['idGalpon'] === '' )
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: no se ha seleccionado un galpón.']);
                    exit();
                }
                If (!isset($_GET['desde']) || $_GET['desde'] === '' || !isset($_GET['hasta']) || $_GET['hasta'] === '') {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: rango de fechas no válido.']);
                    exit();
                }
                $oMantenimientoGalpon = new mantenimientoGalpon();
                if ($mantGalpon = $oMantenimientoGalpon->getMantGalpon($_GET['idGalpon'], $_GET['desde'], $_GET['hasta'])) {
                    http_response_code(200);
                    echo json_encode($mantGalpon);
                }else{
                    http_response_code(200);
                    echo '[]';
                }
            } catch (RuntimeException $e) {
                    http_response_code(400);
                    //echo json_encode(['msg' => $e->getMessage()]);
                    echo json_encode(['msg' => 'Error al obtener mantenimientos.']);
            }
            exit();

        case 'delMantGalpon': 
            header('Content-Type: application/json');
            try {
                if( !isset($_GET['idMantenimientoGalpon']) || $_GET['idMantenimientoGalpon'] === '' )
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: mantenimiento no seleccionado.']);
                    exit();
                }
                $oMantenimientoGalpon = new mantenimientoGalpon();
                if ($oMantenimientoGalpon->deleteMantenimientoGalponId($_GET['idMantenimientoGalpon'])) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Eliminado correctamente.']);
                }
            } catch (RuntimeException $e) {
                http_response_code(400);
                //echo json_encode(['msg' => $e->getMessage()]);
                echo json_encode(['msg' => 'Error al eliminar']);
            }
            exit();
        break;

        default:
            exit();
        break;
    }
}