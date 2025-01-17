<?php
    $error = '';
    $errMsg = '';
    if ( RECAPTCHA_ENABLED === false ){
        $_SESSION ['captcha'] = true;
    }else{
        $_SESSION ['captcha'] = false;
        if (isset($_POST['btLogin'])) {
            if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
                // Verificar el CAPTCHA
                $secret_key = "6LfMnkkqAAAAANcmx-vG5TPjL-p8WNY_nrgsPWft";
                $response = $_POST['g-recaptcha-response'];
                $remoteip = $_SERVER['REMOTE_ADDR'];
                $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secret_key&response=$response&remoteip=$remoteip";
                $result = file_get_contents( $url);
                $resultJson = json_decode($result);
                if ($resultJson->success) {
                    $_SESSION ['captcha'] = true;
                } else {
                    $error = "Invalid Captcha.";
                    $_SESSION ['captcha'] = false;
                }
            } 
        }else{
            $_SESSION ['captcha'] = false;
        }
        $_SESSION["captcha_error"] = $error;
    }

?>
