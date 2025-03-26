<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fox Hound</title>
    <link rel="stylesheet" href="<?php echo asset('styles.css'); ?>">
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

<body class="register-body">
    <!-- Alert -->
    <div id="liveAlertPlaceholder" class="alert-fixed"></div>

    <div class="my-wrapper">
        <form id="registerForm" action="{{ route('register.post') }}" method="POST">
            @csrf
            <h2>Registro 1</h2>
            <div class="input-field">
                <input type="text" name="name" required>
                <label>Ingresa tu nombre de usuario</label>
            </div>
            <div class="input-field">
                <input type="text" name="email" required>
                <label>Ingresa tu correo</label>
            </div>
            <div class="input-field">
                <input type="password" name="password" required>
                <label>Ingresa tu contraseña</label>
            </div>
            <small id="passwordHelp" class="form-text text-muted">La contraseña debe tener al menos:<br>
                - 8 caracteres<br>
                - Una letra mayúscula<br>
                - Una letra minúscula<br>
                - Un número<br>
                - Un carácter especial.
            </small><br>
            <div class="g-recaptcha" data-sitekey="{{ env('GOOGLE_RECAPTCHA_KEY') }}"
                data-callback="enableSubmitButton"></div>
            @if ($errors->has('g-recaptcha-response'))
                <div class="alert alert-danger">{{ $errors->first('g-recaptcha-response') }}</div>
            @endif
            <br>
            <button type="submit" id="registerBtn" disabled>Registrarme ahora</button><br>
            <p><a href="{{ route('login') }}">Volver</a></p>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ReCaptcha script -->
    <script>
        function onSubmit(token) {
            document.getElementById("registerForm").submit();
        }
    </script>

    <!-- Alert script -->
    <script>
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
            checkInputs();
        }

        function validateForm() {
            const response = grecaptcha.getResponse();
            if (response.length === 0) {
                appendAlert('Por favor, completa el reCAPTCHA.', 'danger');
                grecaptcha.reset();
                return false;
            }
            return true;
        }

        const registerForm = document.getElementById('registerForm');
        const registerBtn = document.getElementById('registerBtn');
        const inputs = registerForm.querySelectorAll('input[required]');

        const checkInputs = () => {
            let allFilled = true;
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    allFilled = false;
                }
            });
            const recaptchaResponse = grecaptcha.getResponse();
            registerBtn.disabled = !(allFilled && recaptchaResponse.length > 0);
        };

        inputs.forEach(input => {
            input.addEventListener('input', checkInputs);
        });

        registerForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(registerForm);
            const data = Object.fromEntries(formData.entries());

            const response = await fetch(registerForm.action, {
                method: registerForm.method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                appendAlert(result.message, 'success');
                setTimeout(() => {
                    window.location.href = result.redirect;
                }, 5000);
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
            }
        });
    </script>
</body>

</html>