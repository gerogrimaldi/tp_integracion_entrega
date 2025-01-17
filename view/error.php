<?php
// view/error.php
// Vista centralizada para mostrar errores personalizados
$error = $error ?? '';
switch ($error) 
{
    case 'db':
        $error_title = 'Error de Base de Datos';
        $error_message = 'No hemos podido conectar con la base de datos en este momento. Por favor, intenta de nuevo más tarde.';
        break;
    
    case '404':
        $error_title = 'Error 404: Página no encontrada';
        $error_message = 'No hemos podido encontrar la página que buscas.';
        break;
    
    case '403':
        $error_title = 'Error 403: Acceso restringido';
        $error_message = 'El usuario no tiene permisos de acceso a esta funcionalidad.';
        break;
    
    default:
        echo '<!DOCTYPE html>
        <html lang="es">
        <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>ERROR</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        </head>
        <p>Ha ocurrido un error. La URL no es correcta o no tiene permisos para acceder.</p>
        </footer>
        </div>
        </body>
        </html>';
        exit;
        break;
}

$body = <<<HTML
<link rel='stylesheet' href='css/error.css'>
<div class='container d-flex justify-content-center align-items-center min-vh-100'>
    <div class='error-container text-center'>
        <h1 class='error-title'>{$error_title}</h1>
        <p>{$error_message}</p>
        <a href='index.php' class='home-link btn btn-primary'>Volver a la página principal</a>
    </div>
</div>
HTML;
?>
