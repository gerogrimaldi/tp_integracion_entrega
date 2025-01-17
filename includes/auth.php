<?php
// Esta función valida:
// Si el usuario está logueado correctamente y verifica su token

function checkAuth() {
    try {
        require_once __DIR__ . '/../model/UsuarioModel.php';
        if ( !(isset($_SESSION['user_id']) ) || empty($_SESSION['token'])) {
            return false;
        }
        $usuario = new Usuario();
        $usuario->setidUsuario($_SESSION['user_id']);
        if (!$usuario->validarToken($_SESSION['token'])) {
            destruirSession();
            return false;
        } else {
            return true;
        }
    } catch (mysqli_sql_exception $e) {
        return 'error_db';
    }
}

function destruirSession(){
    $_SESSION = [];
    // Destruir cookie de sesión
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}