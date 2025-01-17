<?php
require_once 'model/compuestosModel.php';

if (isset($_GET['ajax']))
{
    switch ($_GET['ajax']) {
    // ------------------------------------
    // SOLICITUDES AJAX - COMPUESTOS
    // ------------------------------------
        case 'addCompuesto':
            header('Content-Type: application/json');
            try {
                if(empty($_POST['nombre']) || empty($_POST['proveedor']))
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: hay campos vacíos.']);
                    exit();
                }
                $oCompuesto = new compuesto();
                // Respuesta a JS
                if ($oCompuesto->agregarNuevo($_POST['nombre'], $_POST['proveedor'])){
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

        case 'delCompuesto': 
            header('Content-Type: application/json');
            try {
                if( (!isset($_GET['idCompuesto']) || $_GET['idCompuesto'] === '') )
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: no se seleccionó un compuesto.']);
                    exit();
                }
                $oCompuesto = new compuesto();
                if ($oCompuesto->deleteCompuesto($_GET['idCompuesto'])) {
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
    
        case 'editCompuesto':
            header('Content-Type: application/json');
            try {
                if( (!isset($_POST['idCompuesto']) || $_POST['idCompuesto'] === '') 
                || empty($_POST['nombre']) || empty($_POST['proveedor']))
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: hay campos vacíos.']);
                    exit();
                }
                $oCompuesto = new compuesto();
                if ($oCompuesto->update($_POST['idCompuesto'], $_POST['nombre'], $_POST['proveedor'])) {
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
    
        case 'getCompuestos':
            header('Content-Type: application/json');
            try {
                $oCompuesto = new compuesto();
                $compuestos = $oCompuesto->getCompuestos();
                if ($compuestos) {
                    http_response_code(200);
                    echo json_encode($compuestos);
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
    // SOLICITUDES AJAX - COMPRAS COMPUESTOS
    // ------------------------------------

        case 'addCompra':
            header('Content-Type: application/json');
            try {
                if( !isset($_POST['idGranja']) || $_POST['idGranja'] === '' || 
                    !isset($_POST['idcompuesto']) || $_POST['idcompuesto'] === ''|| 
                    !isset($_POST['cantidad']) || $_POST['cantidad'] === ''|| 
                    !isset($_POST['preciocompra']) || $_POST['preciocompra'] === ''||
                    !isset($_POST['fechaCompra']) || $_POST['fechaCompra'] === ''
                ){
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: hay campos vacíos.']);
                    exit();
                }
                $oComprasCompuesto = new ComprasCompuesto();
                if ($oComprasCompuesto->save(
                    $_POST['idGranja'],
                    $_POST['idcompuesto'],
                    $_POST['cantidad'],
                    $_POST['preciocompra'],
                    $_POST['fechaCompra']
                )) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Compra cargada correctamente']);
                }
            }catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['msg' => 'Error al ingresar compra de compuesto.']);
            }
            exit();
        break;

        case 'getComprascompuesto':
            header('Content-Type: application/json');
            try {
                if( !isset($_GET['idGranja']) || $_GET['idGranja'] === '' )
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: no se ha seleccionado una granja.']);
                    exit();
                }
                $oComprasCompuesto = new comprascompuesto();
                $listadoComprasComp = $oComprasCompuesto->getComprasCompuesto($_GET['idGranja']);
                if ($listadoComprasComp){
                    http_response_code(200);
                    echo json_encode($listadoComprasComp);
                } else {
                    echo '[]';
                }
            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
                //echo json_encode(['msg' => 'Error al obtener compras.']);
            }
            exit();
        break;


        case 'delCompra': 
            header('Content-Type: application/json');
            try {
                if( !isset($_GET['idCompraCompuesto']) || $_GET['idCompraCompuesto'] === '' ){
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: compra no seleccionada.']);
                    exit();
                }
                $oComprasCompuesto = new comprascompuesto();
                if ($oComprasCompuesto->deleteComprasCompuestoId($_GET['idCompraCompuesto'])) {
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