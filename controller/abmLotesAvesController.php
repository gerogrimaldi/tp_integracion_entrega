<?php
require_once 'model/lotesAvesModel.php';

if (isset($_GET['ajax']))
{
    switch ($_GET['ajax']) {
    // ------------------------------------
    // SOLICITUDES AJAX - GRANJAS
    // ------------------------------------
        case 'getTipoAve':
            header('Content-Type: application/json');
            try {
                $oTipoAves = new tipoAves();
                $TipoAves = $oTipoAves->getall();
                if ($TipoAves) {
                    http_response_code(200);
                    echo json_encode($TipoAves);
                }else{
                    http_response_code(200);
                    echo '[]';
                }
            } catch (RuntimeException $e) {
                    http_response_code(400);
                    echo json_encode(['msg' => $e->getMessage()]);
            }
            exit();

        case 'delTipoAve': 
            header('Content-Type: application/json');
            try {
                if (!isset($_GET['idTipoAve']) || $_GET['idTipoAve'] === '')
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: tipo de ave no seleccionado.']);
                    exit();
                }
                $oTipoAves = new tipoAves();
                $idTipoAve = (int)$_GET['idTipoAve'];

                if ($oTipoAves->deleteTipoAve($idTipoAve)) {
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

        case 'addTipoAve':
            header('Content-Type: application/json');
            try {
                if(empty($_POST['nombre']) )
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: hay campos vacíos.']);
                    exit();
                }
                $oTipoAves = new tipoAves();
                // Respuesta al frontend
                if ($oTipoAves->agregarNuevo($_POST['nombre'])) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Tipo de ave agregada correctamente']);
                } 
            } catch (RuntimeException $e) {
                    http_response_code(400);
                    // echo json_encode(['error' => $e->getMessage()]);
                    echo json_encode(['msg' => 'Error al añadir.']);
            }
            exit();
        break;

        case 'editTipoAve':
            header('Content-Type: application/json');
            try {
                if(!isset($_POST['nombre']) || $_POST['nombre'] === '' || !isset($_POST['idTipoAve']) || $_POST['idTipoAve'] === '')
                {
                    error_log($_POST['nombre'] . ' - ' . $_POST['idTipoAve']);
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: hay campos vacíos.']);
                    exit();
                }
                $oTipoAves = new tipoAves();
                if ($oTipoAves->updateTipoAve($_POST['idTipoAve'], $_POST['nombre'])) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Cambios guardados correctamente']);
                } 
            }catch (RuntimeException $e) {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error al editar.']);
            }
            exit();
        break;
    // ------------------------------------
    // SOLICITUDES AJAX - LOTE DE AVES
    // ------------------------------------
        // === Obtener todos los lotes filtrados por granja y fecha de nacimiento ===
        case 'getLotesAves':
            header('Content-Type: application/json');
            //index.php?opt=lotesAves&ajax=getLotes&idGranja=0&desde=2023-07-20&hasta=2025-08-20
            if (!isset($_GET['idGranja']) || $_GET['idGranja'] === '' ||
                !isset($_GET['desde']) || $_GET['desde'] === '' ||
                !isset($_GET['hasta']) || $_GET['hasta'] === ''){
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: falta aplicar filtros.']);
                    exit();
                }
            try {
                $oLotes = new LoteAves();
                $lotes = $oLotes->getAllFiltro($_GET['idGranja'], $_GET['desde'], $_GET['hasta']);
                if ($lotes) {
                    http_response_code(200);
                    echo json_encode($lotes);
                } else {
                    http_response_code(200);
                    echo '[]';
                }
            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['msg' => $e->getMessage()]);
            }
            exit();

        case 'getAllLotesAves':
            header('Content-Type: application/json');
            try {
                $oLotes = new LoteAves();
                $lotes = $oLotes->getAll();
                if ($lotes) {
                    http_response_code(200);
                    echo json_encode($lotes);
                } else {
                    http_response_code(200);
                    echo '[]';
                }
            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['msg' => $e->getMessage()]);
            }
            exit();

        // === Obtener un lote por ID ===
        case 'getLoteAvesById':
            header('Content-Type: application/json');
            try {
                if (!isset($_GET['idLoteAves']) || $_GET['idLoteAves'] === '') {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: lote no seleccionado.']);
                    exit();
                }
                $oLotes = new LoteAves();
                $id = (int)$_GET['idLoteAves'];
                $lote = $oLotes->getById($id);

                http_response_code(200);
                echo json_encode($lote);
            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['msg' => $e->getMessage()]);
            }
            exit();
        break;

        // === Eliminar un lote ===
        //Solo se puede eliminar un lote si este tiene un solo galpón asociados,
        //caso de tener más registros o de alguna otra cosa, dará error.
        //de ser necesario se implementará el "activo" por ejemplo al vender un lote.
        case 'delLoteAves': 
            header('Content-Type: application/json');
            try {
                if (!isset($_GET['idLoteAves']) || $_GET['idLoteAves'] === '') {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: lote no seleccionado.']);
                    exit();
                }
                $oLotes = new LoteAves();
                $id = (int)$_GET['idLoteAves'];
                if ($oLotes->deleteLoteAves($id)) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Eliminado correctamente.']);
                }
            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['msg' => $e->getMessage()]);
                //echo json_encode(['msg' => 'Error al eliminar, tiene registros asociados']);
            }
            exit();
        break;

        // === Agregar un nuevo lote ===
        case 'addLoteAves':
            header('Content-Type: application/json');
            try {
                if (!isset($_POST['identificador']) || $_POST['identificador'] === '' ||
                    !isset($_POST['fechaNac']) || $_POST['fechaNac'] === '' ||
                    !isset($_POST['fechaCompra']) || $_POST['fechaCompra'] === '' ||
                    !isset($_POST['cantidadAves']) || $_POST['cantidadAves'] === '' ||
                    !isset($_POST['idTipoAve']) || $_POST['idTipoAve'] === '' ||
                    !isset($_POST['idGalpon']) || $_POST['idGalpon'] === '' ||
                    !isset($_POST['precioCompra']) || $_POST['precioCompra'] === '')
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: hay campos vacíos.']);
                    exit();
                }
                $oLotes = new LoteAves();
                if ($oLotes->agregarNuevo(
                        $_POST['identificador'],
                        $_POST['fechaNac'],
                        $_POST['fechaCompra'],
                        (int)$_POST['cantidadAves'],
                        (int)$_POST['idTipoAve'],
                        (int)$_POST['idGalpon'],
                        $_POST['precioCompra']
                )) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Lote agregado correctamente']);
                } 
            } catch (RuntimeException $e) {
                http_response_code(400);
                error_log($e);
                echo json_encode(['msg' => 'Error al añadir.']);
            }
            exit();
        break;

        // === Editar un lote existente ===
        case 'editLoteAves':
            header('Content-Type: application/json');
            try {
                if (!isset($_POST['idLoteAves']) || $_POST['idLoteAves'] === '' ||
                    !isset($_POST['identificador']) || $_POST['identificador'] === '' ||
                    !isset($_POST['fechaNac']) || $_POST['fechaNac'] === '' ||
                    !isset($_POST['fechaCompra']) || $_POST['fechaCompra'] === '' ||
                    !isset($_POST['cantidadAves']) || $_POST['cantidadAves'] === '' ||
                    !isset($_POST['idTipoAve']) || $_POST['idTipoAve'] === '' ||
                    !isset($_POST['precioCompra']) || $_POST['precioCompra'] === '')
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: hay campos vacíos.']);
                    exit();
                }
                $oLotes = new LoteAves();
                if ($oLotes->updateLoteAves(
                        (int)$_POST['idLoteAves'],
                        $_POST['identificador'],
                        $_POST['fechaNac'],
                        $_POST['fechaCompra'],
                        (int)$_POST['cantidadAves'],
                        (int)$_POST['idTipoAve'],
                        $_POST['precioCompra']
                )) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Cambios guardados correctamente']);
                } 
            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['msg' => 'Error al editar.']);
            }
            exit();
        break;

        //Editar ubicacion (galpon)
        case 'editUbicacionAve':
            header('Content-Type: application/json');
            try {
                if (!isset($_POST['idLoteAves']) || $_POST['idLoteAves'] === '' ||
                    !isset($_POST['idGalpon']) || $_POST['idGalpon'] === '' ||
                    !isset($_POST['fechaInicio']) || $_POST['fechaInicio'] === '')
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: hay campos vacíos.']);
                    exit();
                }
                $oLotes = new LoteAves();
                if ($oLotes->cambiarUbicacion(
                        (int)$_POST['idLoteAves'],
                        (int)$_POST['idGalpon'],
                        $_POST['fechaInicio'],
                )) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Cambios guardados correctamente']);
                } 
            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['msg' => 'Error al editar. Recuerde: la fecha debe ser mayor a la del último cambio.']);
            }
            exit();
        break;

        //Obtener los cambios de ubicacion (galpon)
        case 'getUbicacionAve':
            header('Content-Type: application/json');
            try {
                if (!isset($_GET['idLoteAves']) || $_GET['idLoteAves'] === '') {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: lote no seleccionado.']);
                    exit();
                }
                $oLotes = new LoteAves();
                $lote = $oLotes->getCambiosUbicacion((int)$_GET['idLoteAves']);
                http_response_code(200);
                echo json_encode($lote);
            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['msg' => $e->getMessage()]);
            }
            exit();
        break;

    // ------------------------------------
    // AJAX - LOTE DE AVES - MORTANDAD
    // ------------------------------------
        case 'addMuertes':
            header('Content-Type: application/json');
            try {
                if (!isset($_POST['idLoteAves']) || $_POST['idLoteAves'] === '' ||
                    !isset($_POST['fecha']) || $_POST['fecha'] === '' ||
                    !isset($_POST['causa']) || $_POST['causa'] === '' ||
                    !isset($_POST['cantidad']) || $_POST['cantidad'] === '')
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: hay campos vacíos.']);
                    exit();
                }
                $oLotes = new LoteAves();
                if ($oLotes->agregarMortandad(
                        (int)$_POST['idLoteAves'],
                        $_POST['fecha'],
                        $_POST['causa'],
                        (int)$_POST['cantidad']
                    )) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Registro cargado correctamente']);
                } 
            } catch (RuntimeException $e) {
                http_response_code(400);
                error_log($e);
                echo json_encode(['msg' => 'Error al añadir.']);
            }
            exit();
        break;

        case 'editMuertes':
            header('Content-Type: application/json');
            try {
                if (!isset($_POST['idMortandad']) || $_POST['idMortandad'] === '' ||
                    !isset($_POST['fecha']) || $_POST['fecha'] === '' ||
                    !isset($_POST['causa']) || $_POST['causa'] === '' ||
                    !isset($_POST['cantidad']) || $_POST['cantidad'] === '')
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: hay campos vacíos.']);
                    exit();
                }
                $oLotes = new LoteAves();
                if ($oLotes->editarMortandad(
                        (int)$_POST['idMortandad'],
                        $_POST['fecha'],
                        $_POST['causa'],
                        (int)$_POST['cantidad']
                )) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Cambios guardados correctamente']);
                } 
            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['msg' => 'Error al editar.']);
            }
            exit();
        break;

        case 'delMuertes': 
            header('Content-Type: application/json');
            try {
                if (!isset($_GET['idMortandad']) || $_GET['idMortandad'] === '') {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: registro no seleccionado.']);
                    exit();
                }
                $oLotes = new LoteAves();
                if ($oLotes->deleteMortandad((int)$_GET['idMortandad'])) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Eliminado correctamente.']);
                }
            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['msg' => 'Error al eliminar, tiene registros asociados']);
            }
            exit();
        break;

        case 'getMuertes':
            header('Content-Type: application/json');
            try {
                if (!isset($_GET['idLoteAves']) || $_GET['idLoteAves'] === '') {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: lote no seleccionado.']);
                    exit();
                }
                $oLotes = new LoteAves();
                $lotes = $oLotes->getMortandad((int)$_GET['idLoteAves']);
                if ($lotes){
                    http_response_code(200);
                    echo json_encode($lotes);
                }else {
                    http_response_code(200);
                    echo '[]';
                }
            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['msg' => $e->getMessage()]);
            }
            exit();
        break;

    // ------------------------------------
    // AJAX - LOTE DE AVES - PESAJES
    // ------------------------------------
        case 'addPesaje':
            header('Content-Type: application/json');
            try {
                if (!isset($_POST['idLoteAves']) || $_POST['idLoteAves'] === '' ||
                    !isset($_POST['fecha']) || $_POST['fecha'] === '' ||
                    !isset($_POST['peso']) || $_POST['peso'] === '')
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: hay campos vacíos.']);
                    exit();
                }
                $oLotes = new LoteAves();
                if ($oLotes->agregarPesaje(
                        (int)$_POST['idLoteAves'],
                        $_POST['fecha'],
                        $_POST['peso']
                    )) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Pesaje cargado correctamente']);
                } 
            } catch (RuntimeException $e) {
                http_response_code(400);
                error_log($e);
                echo json_encode(['msg' => 'Error al añadir.']);
            }
            exit();
        break;

        case 'editPesaje':
            header('Content-Type: application/json');
            try {
                if (!isset($_POST['idPesaje']) || $_POST['idPesaje'] === '' ||
                    !isset($_POST['fecha']) || $_POST['fecha'] === '' ||
                    !isset($_POST['peso']) || $_POST['peso'] === '')
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: hay campos vacíos.']);
                    exit();
                }
                $oLotes = new LoteAves();
                if ($oLotes->editarPesaje(
                        (int)$_POST['idPesaje'],
                        $_POST['fecha'],
                        $_POST['peso']
                )) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Cambios guardados correctamente']);
                } 
            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['msg' => 'Error al editar.']);
            }
            exit();
        break;

        case 'delPesaje': 
            header('Content-Type: application/json');
            try {
                if (!isset($_GET['idPesaje']) || $_GET['idPesaje'] === '') {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: registro no seleccionado.']);
                    exit();
                }
                $oLotes = new LoteAves();
                if ($oLotes->deletePesaje((int)$_GET['idPesaje'])) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Eliminado correctamente.']);
                }
            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['msg' => 'Error al eliminar, tiene registros asociados']);
            }
            exit();
        break;

        case 'getPesaje':
            header('Content-Type: application/json');
            try {
                if (!isset($_GET['idLoteAves']) || $_GET['idLoteAves'] === '') {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: lote no seleccionado.']);
                    exit();
                }
                $oLotes = new LoteAves();
                $lotes = $oLotes->getPesaje((int)$_GET['idLoteAves']);
                if ($lotes){
                    http_response_code(200);
                    echo json_encode($lotes);
                }else {
                    http_response_code(200);
                    echo '[]';
                }
            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['msg' => $e->getMessage()]);
            }
            exit();
        break;

        //Baja de lotes de aves
        case 'delBaja': 
            header('Content-Type: application/json');
            try {
                if (!isset($_GET['idBajaLoteAves']) || $_GET['idBajaLoteAves'] === '')
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: registro no seleccionado.']);
                    exit();
                }
                $oLotes = new LoteAves();
                if ( $oLotes->deleteBaja($_GET['idBajaLoteAves'])) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Baja revertida correctamente.']);
                }
            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['msg' => 'Error al revertir la baja']);
            }
            exit();
        break;

        case 'baja':
            header('Content-Type: application/json');
            try {
                //El motivo puede estar vacío aunque no sería lo ideal.
                if (!isset($_POST['idLoteAves']) || $_POST['idLoteAves'] === '' ||
                    !isset($_POST['fechaBaja']) || $_POST['fechaBaja'] === '' ||
                    !isset($_POST['precioVenta']) || $_POST['precioVenta'] === '')
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: hay campos vacíos.']);
                    exit();
                }
                $oLotes = new LoteAves();
                if ($oLotes->addBaja(
                        (int)$_POST['idLoteAves'],
                        $_POST['fechaBaja'],
                        $_POST['precioVenta'],
                        $_POST['motivo']
                    )) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Lote dado de baja correctamente']);
                } 
            } catch (RuntimeException $e) {
                http_response_code(400);
                error_log($e);
                echo json_encode(['msg' => 'Error al ejecutar la baja.']);
            }
            exit();
        break;

        case 'getBajas':
            header('Content-Type: application/json');
            try {
                $oLotes = new LoteAves();
                $lotes = $oLotes->getBajas();
                if ($lotes){
                    http_response_code(200);
                    echo json_encode($lotes);
                }else {
                    http_response_code(200);
                    echo '[]';
                }
            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['msg' => $e->getMessage()]);
            }
            exit();
        break;

        // ------------------------------------
        // AJAX - LOTE DE AVES - VACUNAS
        // ------------------------------------
        case 'addVacuna':
            header('Content-Type: application/json');
            try {
                if (!isset($_POST['idLoteAves']) || $_POST['idLoteAves'] === '' ||
                    !isset($_POST['idLoteVacuna']) || $_POST['idLoteVacuna'] === '' ||
                    !isset($_POST['fecha']) || $_POST['fecha'] === '' ||
                    !isset($_POST['cantidad']) || $_POST['cantidad'] === '') 
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: hay campos vacíos.']);
                    exit();
                }

                $oLotes = new LoteAves();
                if ($oLotes->agregarVacuna(
                        (int)$_POST['idLoteAves'],
                        (int)$_POST['idLoteVacuna'],
                        $_POST['fecha'],
                        (int)$_POST['cantidad']
                    )) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Vacuna aplicada correctamente']);
                }

            } catch (RuntimeException $e) {
                http_response_code(400);
                error_log($e);
                echo json_encode(['msg' => 'Error al añadir vacuna.']);
            }
            exit();
        break;

        case 'editVacuna':
            header('Content-Type: application/json');
            try {
                if (!isset($_POST['idAplicacion']) || $_POST['idAplicacion'] === '' ||
                    !isset($_POST['idLoteVacuna']) || $_POST['idLoteVacuna'] === '' ||
                    !isset($_POST['fecha']) || $_POST['fecha'] === '' ||
                    !isset($_POST['cantidad']) || $_POST['cantidad'] === '') 
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: hay campos vacíos.']);
                    exit();
                }

                $oLotes = new LoteAves();
                if ($oLotes->editarVacuna(
                        (int)$_POST['idAplicacion'],
                        (int)$_POST['idLoteVacuna'],
                        $_POST['fecha'],
                        (int)$_POST['cantidad']
                    )) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Cambios guardados correctamente']);
                }

            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['msg' => 'Error al editar aplicación de vacuna.']);
            }
            exit();
        break;

        case 'delVacuna': 
            header('Content-Type: application/json');
            try {
                if (!isset($_GET['idAplicacion']) || $_GET['idAplicacion'] === '') {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: registro no seleccionado.']);
                    exit();
                }

                $oLotes = new LoteAves();
                if ($oLotes->deleteVacuna((int)$_GET['idAplicacion'])) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Aplicación eliminada correctamente.']);
                }

            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['msg' => 'Error al eliminar, tiene registros asociados']);
            }
            exit();
        break;

        case 'getVacunas':
            header('Content-Type: application/json');
            try {
                if (!isset($_GET['idLoteAves']) || $_GET['idLoteAves'] === '') {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: lote no seleccionado.']);
                    exit();
                }

                $oLotes = new LoteAves();
                $vacunas = $oLotes->getVacunas((int)$_GET['idLoteAves']);
                if ($vacunas){
                    http_response_code(200);
                    echo json_encode($vacunas);
                } else {
                    http_response_code(200);
                    echo '[]';
                }

            } catch (RuntimeException $e) {
                http_response_code(400);
                echo json_encode(['msg' => $e->getMessage()]);
            }
            exit();
        break;

        default:
            exit();
        break;
    }
}