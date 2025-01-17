<?php
require_once 'model/testModel.php';
if (!empty($_POST)) {
    switch ($_POST['btTest']) {
    //Casos de producción que se utilizan en la vista database.php
        case 'backupDB':
            $oTest = new Test();
            if ($oTest->backupDB()) {
                $oTest->descargarBackupBD();
                $oTest->guardarFechaBackup();
                $oTest->destruirBackup();
                echo json_encode(['msg' => 'Backup completado.']);
                exit;
            } else {
                echo json_encode(['msg' => 'Error al realizar backup.']);
            }
            break;

        case 'restoreDB':
            $oTest = new Test();
            if ($oTest->restaurarBackupBD($_FILES['archivoBackup']['tmp_name'])) {
                echo json_encode(['msg' => 'Base de datos restaurada correctamente.']);
            } else {
                http_response_code(400);
                echo json_encode(['msg' => 'Error al restaurar la base de datos.']);
            }
            exit;

    //Casos que no van a estar en producción
        case 'testConnect':
            //echo "Testeando conexión MariaDB";
            $oTest = new Test();
            $oTest->testConnect();
            break;
        case 'crearBD':
            //echo "Creando base de datos...";
            $oTest = new Test();
            $oTest->crearBD();
            break;
        case 'borrarDB':
            //echo "Borrando base de datos...";
            $oTest = new Test();
            $oTest->borrarBD();
            break;
        case 'cargarDatos':
            //echo "Cargando datos de prueba... Los password del test estan hasheados usar: 12345678";
            $oTest = new Test();
            $oTest->cargarDatos();
            break;
        case 'crearTablas':
            //echo "Creando tablas...";
            $oTest = new Test();
            $oTest->crearTablas();
            break;
        }
}
