<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fox Hound</title>
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('token')) {
                window.location.href = '/home';
            }
        });
    </script>
</head>

<body class="login-body">
    <!-- Alert -->
    <div id="liveAlertPlaceholder" class="alert-fixed"></div>

    <div class="my-wrapper">
        <form id="loginForm" action="{{ route('login.post') }}" method="POST" onsubmit="return validateForm()">
            @csrf
            <h2>Inicio de sesión</h2>
            <div class="input-field">
                <input type="text" name="email" required>
                <label>Ingresa tu correo</label>
            </div>
            <div class="input-field">
                <input type="password" name="password" required>
                <label>Ingresa tu contraseña</label>
            </div>
            <div class="input-field">
                <input type="text" name="code" required>
                <label>Ingresa tu código de verificación</label>
            </div><br>
            <div class="g-recaptcha" data-sitekey="{{ env('GOOGLE_RECAPTCHA_KEY') }}"
                data-callback="onRecaptchaSuccess"></div>
            <input type="hidden" id="recaptchaToken" name="recaptcha_token" required>
            @if ($errors->has('g-recaptcha-response'))
                <div class="alert alert-danger">{{ $errors->first('g-recaptcha-response') }}</div>
            @endif
            <br>
            <button type="submit" id="loginBtn" disabled>Iniciar sesión</button>
            <p>¿No cuentas con una cuenta aún? <a href="{{ route('register') }}">Registrarse</a></p>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function onSubmit(token) {
            document.getElementById("loginForm").submit();
        }

        function onRecaptchaSuccess(token) {
            document.getElementById('recaptchaToken').value = token;
            checkInputs();
        }

        function validateForm() {
            const response = document.getElementById('recaptchaToken').value;
            if (response.length === 0) {
                appendAlert('Por favor, completa el reCAPTCHA.', 'danger');
                return false;
            }
            return true;
        }

        const alertPlaceholder = document.getElementById('liveAlertPlaceholder');
        let alertCount = 0;
        const maxAlerts = 5;

        const appendAlert = (message, type) => {
            if (alertCount >= maxAlerts) {
                return;
            }

            const alertClass = type === 'danger' ? 'alert-custom-danger' : 'alert-custom-success';
            const wrapper = document.createElement('div');
            wrapper.innerHTML = [
                `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">`,
                `   <div>${message}</div>`,
                '</div>'
            ].join('');

            alertPlaceholder.append(wrapper);
            alertCount++;

            // Auto close alert after 10 seconds
            setTimeout(() => {
                const alert = new bootstrap.Alert(wrapper);
                alert.close();
                alertCount--;
            }, 10000);
        };

        function enableSubmitButton(token) {
            document.getElementById('recaptchaToken').value = token;
            checkInputs();
        }

        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const inputs = loginForm.querySelectorAll('input[required]');

        const checkInputs = () => {
            let allFilled = true;
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    allFilled = false;
                }
            });
            const recaptchaResponse = document.getElementById('recaptchaToken').value;
            loginBtn.disabled = !(allFilled && recaptchaResponse.length > 0);
        };

        inputs.forEach(input => {
            input.addEventListener('input', checkInputs);
        });

        loginForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(loginForm);
            const data = Object.fromEntries(formData.entries());

            const response = await fetch(loginForm.action, {
                method: loginForm.method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            function resetForm() {
                loginForm.reset(); 
                loginBtn.disabled = true; 
                document.getElementById('recaptchaToken').value = '';  
                checkInputs();
            }

            if (response.ok) {
                localStorage.setItem('token', result.token);

                const homeResponse = await fetch(result.redirect, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'Accept': 'application/json'
                    }
                });

                if (homeResponse.ok) {
                    window.location.href = result.redirect; // Redirect to home only if the request is successful
                } else {
                    appendAlert('Error al acceder a home.', 'danger');
                    resetForm();
                }
            } else {
                const errors = result.message;
                if (typeof errors === 'string') {
                    appendAlert(errors, 'danger');
                } else {
                    for (const key in errors) {
                        if (errors.hasOwnProperty(key)) {
                            appendAlert(errors[key].join(' '), 'danger');
                        }
                    }
                }
                resetForm();
            }
        });

        // Call checkInputs initially to set the button state
        checkInputs();
    </script>
</body>

</html>