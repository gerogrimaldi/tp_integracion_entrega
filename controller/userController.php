<?php
require_once 'model/UsuarioModel.php';

//Manejo del login, se utiliza el formulario tal cual sin JavaScript.
if ( !empty($_POST) && isset($_POST['btLogin']) ) {
    switch ($_POST['btLogin']) {
        case 'login':
            $oUsuario = new Usuario();
            $oUsuario->setPassword($_POST['password']);
            $oUsuario->setEmail($_POST['email']);
            unset($_POST['password']);
            unset($_POST['email']);
            if ($oUsuario->validar())
            {
                $oUsuario->iniciarSesion();
                header('Location: index.php?opt=home'); exit;
            }else{
                //Si falla en el inicio de sesión, redirige al login
                require_once 'controller/PageController.php';
            }
            break;
    }
}

//Para todo lo demás, se utiliza AJAX
if (isset($_GET['ajax']))
{
    switch ($_GET['ajax']) {
    // SOLICITUDES AJAX DE USUARIOS
        case 'logout':
            header('Content-Type: application/json');
            try {
                $oUser = new Usuario();
                $oUser->setidUsuario($_SESSION['user_id']);
                $oUser->cerrarSesion();
                http_response_code(200);
                echo json_encode(['msg' => 'Se ha cerrado la sesión.']);
            } catch (RuntimeException $e) {
                    http_response_code(400);
                    echo json_encode(['msg' => $e->getMessage()]);
            }
            exit();

        case 'getUsuarios':
            header('Content-Type: application/json');
            try {
                $oUser = new Usuario();
                $Usuarios = $oUser->getall();
                   
                if ($Usuarios) {
                    http_response_code(200);
                    echo json_encode($Usuarios);
                }else{
                    http_response_code(200);
                    echo '[]';
                }
            } catch (RuntimeException $e) {
                    http_response_code(400);
                    echo json_encode(['msg' => $e->getMessage()]);
            }
            exit();

        case 'delUsuario':
            header('Content-Type: application/json');
            try {
                if( !isset($_GET['idUsuario']) || $_GET['idUsuario'] === '' )
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: no se ha seleccionado un usuario.']);
                    exit();
                }
                $oUser = new Usuario();
                if ($Usuarios = $oUser->deleteUsuarioPorId($_GET['idUsuario'])) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Usuario eliminado correctamente.']);
                }else{
                    http_response_code(200);
                    echo json_encode(['msg' => 'Error al eliminar usuario.']);
                }
            } catch (RuntimeException $e) {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error al eliminar usuario: ' . $e->getMessage()]);
            }
            exit();


        case 'getUsuario':
            header('Content-Type: application/json');
            try {
                if( !isset($_GET['idUsuario']) || $_GET['idUsuario'] === '' )
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: no se ha seleccionado un usuario.']);
                    exit();
                }
                $oUser = new Usuario();
                if ($Usuarios = $oUser->getUsuarioPorId($_GET['idUsuario'])) {
                    http_response_code(200);
                    echo json_encode($Usuarios);
                }else{
                    http_response_code(200);
                    echo '[]';
                }
            } catch (RuntimeException $e) {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error al actualizar el campo: ' . $e->getMessage()]);
            }
            exit();

        //index.php?opt=usuarios&ajax=updateCampo
        case 'updateCampo':
            header('Content-Type: application/json');
            try {
                if( !isset($_POST['idUsuario']) || $_POST['idUsuario'] === '' ||
                    !isset($_POST['campo']) || $_POST['campo'] === '' ||
                    !isset($_POST['valor']) || $_POST['valor'] === '' )
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: datos incompletos para actualizar.']);
                    exit();
                }
                if ($_POST['campo'] === "domicilio"){ $_POST['campo'] = "direccion";}
                $oUser = new Usuario();
                $oUser->setidUsuario($_POST['idUsuario']);
                if ($oUser->updateCampo($_POST['campo'], $_POST['valor'])) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Campo actualizado correctamente.']);
                }else {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error en el procedimiento para actualizar el campo.']);
                }
            } catch (RuntimeException $e) {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error al actualizar el campo: ' . $e->getMessage()]);
            }
            exit();

        case 'updatePassword':
            header('Content-Type: application/json');
            try {
                if( !isset($_POST['idUsuario']) || $_POST['idUsuario'] === '' ||
                    !isset($_POST['campo']) || $_POST['campo'] === '' ||
                    !isset($_POST['valor']) || $_POST['valor'] === '' )
                {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error: datos incompletos para actualizar.']);
                    exit();
                }
                $oUser = new Usuario();
                $oUser->setPassword($_POST['campo']);
                unset($_POST['password']);
                $oUser->setidUsuario($_POST['idUsuario']);
                if ($oUser->validarPorID()){
                    if ($oUser->updateCampo('password', $_POST['valor'])) {
                        http_response_code(200);
                        echo json_encode(['msg' => 'Contraseña actualizada.']);
                    }
                }else {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error al actualizar la contraseña. No coincide la contraseña actual.']);
                }
                unset($_POST['valor']);
            } catch (RuntimeException $e) {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error al actualizar el campo: ' . $e->getMessage()]);
            }
            exit();

        case 'registrar':
            try {
                $oUsuario = new Usuario();
                $oUsuario->setMaxIDUsuario();
                $oUsuario->setPassword($_POST['password']);
                $oUsuario->setNombre($_POST['nombre']);
                $oUsuario->setDireccion($_POST['direccion']);
                $oUsuario->setTelefono($_POST['telefono']);
                $oUsuario->setEmail($_POST['email']);
                $oUsuario->setDate($_POST['fechaNac']);
                $oUsuario->setTipoUsuario($_POST['tipoUsuario']);
                if ($oUsuario->save()) {
                    http_response_code(200);
                    echo json_encode(['msg' => 'Usuario registrado correctamente.']);
                } else {
                    http_response_code(400);
                    echo json_encode(['msg' => 'Error al registrar el usuario.']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['msg' => 'Error interno del servidor: ' . $e->getMessage()]);
            }
            exit();
    }
}