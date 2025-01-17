
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("loginForm");
        const username = document.getElementById("username");
        const password = document.getElementById("password");
        const errorAlert = document.getElementById("errorAlert");

        // Function to show error message
        function showError(message) {
            errorAlert.textContent = message;
            errorAlert.classList.remove("d-none");
        }

        // Function to hide error message
        function hideError() {
            errorAlert.classList.add("d-none");
        }

        // Function to validate email format
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // Real-time validation for username
        username.addEventListener("input", function() {
            if (username.value.length < 3) {
                username.classList.add("is-invalid");
            } else {
                username.classList.remove("is-invalid");
                username.classList.add("is-valid");
            }
        });

        // Real-time validation for password
        password.addEventListener("input", function() {
            if (password.value.length < 6) {
                password.classList.add("is-invalid");
            } else {
                password.classList.remove("is-invalid");
                password.classList.add("is-valid");
            }
        });

        // Form submission handler
        form.addEventListener("submit", function(event) {
            event.preventDefault();
            hideError();

            // Reset validation states
            form.classList.remove("was-validated");
            
            let isValid = true;
            let errorMessage = "";

            // Validate username
            if (username.value.trim() === "") {
                errorMessage = "Por favor ingrese su usuario";
                isValid = false;
            } else if (username.value.length < 3) {
                errorMessage = "El usuario debe tener al menos 3 caracteres";
                isValid = false;
            }

            // If username looks like an email, validate email format
            if (username.value.includes("@") && !isValidEmail(username.value)) {
                errorMessage = "Por favor ingrese un correo electrónico válido";
                isValid = false;
            }

            // Validate password
            if (password.value === "") {
                errorMessage = "Por favor ingrese su contraseña";
                isValid = false;
            } else if (password.value.length < 6) {
                errorMessage = "La contraseña debe tener al menos 6 caracteres";
                isValid = false;
            }

            if (!isValid) {
                showError(errorMessage);
                return;
            }

            // If validation passes, show loading state and submit
            const submitButton = form.querySelector("button[type=submit]");
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Iniciando sesión...
            `;

        });
    });