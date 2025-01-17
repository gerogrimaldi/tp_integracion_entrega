<?php
$recaptcha_key = RECAPTCHA_KEY;
if (empty($idUsuario)) {
    $idUsuario = '';
    $password = '';
}

$body = <<<HTML
<link rel="stylesheet" href="css/login.css">

<div class="login-wrapper d-flex flex-column align-items-center justify-content-center p-3">
    <div class="login-container p-4 p-md-5 rounded-4" style="max-width: 450px; width: 100%;">
        <!-- Header Section -->
        <div class="text-center">
            <div class="company-logo mx-auto">
                <i class="bi bi-building text-white" style="font-size: 3rem;"></i>
            </div>
            <h1 class="h3 fw-bold mb-2" style="color: var(--primary-color);">Bienvenido</h1>
            <p class="text-muted mb-4">Inicie sesión para acceder a la aplicación</p>
        </div>
        
        <!-- Login Form -->
        <form id="loginForm" action="index.php" method="post" class="needs-validation" novalidate>
            <!-- Email Input -->
            <div class="mb-4">
                <div class="input-group has-validation">
                    <span class="input-group-text border-end-0">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" 
                           class="form-control border-start-0" 
                           id="email" 
                           name="email" 
                           placeholder="Email"
                           minlength="3"
                           value="{$idUsuario}"
                           required>
                    <div class="invalid-feedback">
                        Por favor ingrese un usuario válido (mínimo 3 caracteres)
                    </div>
                </div>
            </div>
            
            <!-- Password Input -->
            <div class="mb-4">
                <div class="input-group has-validation">
                    <span class="input-group-text border-end-0">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" 
                           class="form-control border-start-0" 
                           id="password" 
                           name="password" 
                           placeholder="Contraseña"
                           minlength="6"
                           value="{$password}"
                           required>
                    <div class="invalid-feedback">
                        La contraseña debe tener al menos 6 caracteres
                    </div>
                </div>
            </div>
HTML;
            if (isset($_SESSION ['login_error'])){
                $body.= "<p style='color: red;'>" . $_SESSION['login_error'] . "</p>";
                unset($_SESSION['login_error']);
            };

$body.= <<<HTML
    <!-- reCAPTCHA -->
    <div class="g-recaptcha mb-4" data-sitekey="{$recaptcha_key}" required></div>
HTML;
        if (isset($_SESSION ['captcha_error'])){
            $body.= "<p style='color: red;'>" . $_SESSION['captcha_error'] . "</p>";
            unset($_SESSION['captcha_error']);
        };

$body.= <<<HTML
            <!-- Submit Button -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg fw-bold" name="btLogin" value="login">
                    Iniciar Sesión
                </button>
            </div>

            <!-- Password Recovery Link -->
            <div class="text-center mt-4">
                <a href="#" class="forgot-password">¿Olvidó su contraseña?</a> 
            </div>
        </form>
    </div>
</div>

<script src="js/clientValidation.js"></script>
HTML;