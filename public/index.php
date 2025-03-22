<?php
session_start();

// Generar CSRF TOKEN solo si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_time'] = time();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/image/favicon.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>UPTAG | Gestión Comunicacional</title>
</head>
<body>
    <!-- Barra superior -->
    <header class="navbar">
    <div class="navbar-content">
        <!-- Botón para cambiar de modo oscuro/claro -->
        <button id="theme-toggle" class="theme-toggle">
            <i id="theme-icon" class="fas fa-moon"></i>
        </button>
        <!-- Contenedor para el logo y el nombre del sistema -->
        <div class="navbar-brand">
            <img src="../assets/image/logo.png" alt="Logo UPTAG" class="logo">
            <h1 class="app-name">UPTAG</h1>
        </div>
    </div>
</header>

    <main>
        <!-- Contenedor de la Web Application -->
        <div class="container__all">
            <!-- Cajas Traseras -->
            <div class="caja__trasera">
                <div class="caja__trasera__login">
                    <h3>Eres usuario</h3>
                    <p>¡Inicia sesión y accede al sistema!</p>
                    <button id="btn__iniciar-sesion" class="btn">Iniciar Sesión</button>
                </div>
                <div class="caja__trasera__register">
                    <h3>¿Olvidaste tu contraseña?</h3>
                    <p>¡Sigue los pasos para recuperarla!</p>
                    <button id="btn__registrarse" class="btn">Recuperar Contraseña</button>
                </div>
            </div>
            <!-- Formulario de Login y Registro -->
            <div class="container__login__register">
                <!-- Login -->
                <form action="../php/login_user_be.php" method="POST" class="formulario__login" onsubmit="return validarFormularioLogin()">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <h2>Iniciar Sesión</h2>
                    <input type="email" placeholder="Correo Electrónico" name="correo" required minlength="5" maxlength="64" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                    <span class="help-text">Ingresa tu correo electrónico registrado.</span>
                    <input type="password" placeholder="Contraseña" name="clave" required minlength="8" maxlength="64">
                    <span class="help-text">La contraseña debe tener al menos 8 caracteres.</span>
                    <!-- CAPTCHA -->
                    <label for="captcha">CAPTCHA:</label>
                    <div>
                        <img src="../php/captcha.php" style="margin: 10px 0;" alt="CAPTCHA" class="captcha-image" id="captchaImage">
                        <button style="padding: 5px 10px;" title="Este botón es para recargar el captcha" type="button" class="reload-button" onclick="reloadCaptcha()">↻</button>
                        <button style="padding: 5px 10px;" title="Este botón es para reproducir el captcha" type="button" class="audio-button" onclick="playCaptcha()">▶</button>
                    </div>
                    <input type="text" id="captcha" name="captcha" placeholder="Ingresa lo que ves en la imagen" required>
                    <span class="help-text">Escribe el captcha de la imagen para verificar que no eres un robot.</span>
                    <button type="submit">Entrar</button>
                </form>
                <!-- Recuperación -->
                <form action="#" method="POST" class="formulario__register" id="registro-form" onsubmit="return validarFormularioRecuperacion()">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <h2>Recuperar Contraseña</h2>
                    <input type="email" id="recoverUser" name="recoverUser" placeholder="Ingresa un correo electrónico válido" required minlength="5" maxlength="64" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                    <span class="help-text">Ingresa tu correo electrónico para verificar tu usuario.</span>
                    <input type="number" id="recoverCedula" name="recoverCedula" placeholder="Ingresa tu cédula de identidad" required minlength="5" maxlength="12">
                    <span class="help-text">Ingresa tu cédula de identidad para verificar tu usuario.</span>
                    <!-- CAPTCHA -->
                    <label for="recoverCaptcha">CAPTCHA:</label>
                    <div>
                        <img src="../php/captcha.php" style="margin: 10px 0;" alt="CAPTCHA" class="captcha-image" id="captchaImageRecover">
                        <button style="padding: 5px 10px;" type="button" title="Este botón es para recargar el captcha" class="reload-button" onclick="reloadCaptchaRecover()">↻</button>
                        <button style="padding: 5px 10px;" type="button" title="Este botón es para reproducir el captcha" class="audio-button" onclick="playCaptchaRecover()">▶</button>
                    </div>
                    <input type="text" id="recoverCaptcha" name="recoverCaptcha" placeholder="Ingresa lo que ves en la imagen" required>
                    <span class="help-text">Escribe el captcha de la imagen para verificar que no eres un robot.</span>
                    <button type="submit">Enviar Código</button>
                </form>
            </div>
        </div>
    </main>

<!-- Modal para errores de CSRF -->
<div id="csrfErrorModal" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h2>Error de verificación</h2>
        <p>Debes recargar la página. El token de verificación ha expirado o no es válido.</p>
        <button id="reloadPageButton" class="modal-button">Recargar página</button>
    </div>
</div>


    <footer class="footer">
        <div class="footer-content">
            <p>&copy; Hernandez, Pachano, Perez, Bracho. 2025 - UPTAG</p>
        </div>
    </footer>

    <script src="../assets/js/script.js"></script>
    <script>
        // Validación del formulario de login
        function validarFormularioLogin() {
            const correo = document.querySelector('input[name="correo"]').value;
            const clave = document.querySelector('input[name="clave"]').value;

            if (!correo || !clave) {
                alert("Por favor, completa todos los campos.");
                return false;
            }

            if (!/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/.test(correo)) {
                alert("Por favor, ingresa un correo electrónico válido.");
                return false;
            }

            return true;
        }

        // Validación del formulario de recuperación
        function validarFormularioRecuperacion() {
            const email = document.getElementById('email').value;

            if (!email) {
                alert("Por favor, ingresa tu correo electrónico.");
                return false;
            }

            if (!/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/.test(email)) {
                alert("Por favor, ingresa un correo electrónico válido.");
                return false;
            }

            return true;
        }

        // Función para recargar el CAPTCHA
        function reloadCaptcha() {
            const captchaImage = document.getElementById('captchaImage');
            captchaImage.src = '../php/captcha.php?' + new Date().getTime(); // Evitar caché
        }

        // Función para recargar el CAPTCHA en el modal de recuperación
        function reloadCaptchaRecover() {
            const captchaImage = document.getElementById('captchaImageRecover');
            captchaImage.src = '../php/captcha.php?' + new Date().getTime(); // Evitar caché
        }

        // Función para reproducir el CAPTCHA en voz (deletreado)
        function playCaptcha() {
            // Obtener el texto del CAPTCHA desde el servidor
            fetch('../php/get_captcha_text.php')
                .then(response => response.text())
                .then(text => {
                    // Dividir el texto en caracteres individuales
                    const characters = text.split('');
                    let index = 0;

                    // Función para reproducir un carácter
                    function speakNextCharacter() {
                        if (index < characters.length) {
                            const utterance = new SpeechSynthesisUtterance(characters[index]);
                            utterance.lang = 'es-ES'; // Configurar el idioma
                            utterance.onend = speakNextCharacter; // Reproducir el siguiente carácter al terminar
                            speechSynthesis.speak(utterance);
                            index++;
                        }
                    }

                    // Comenzar a reproducir
                    speakNextCharacter();
                })
                .catch(error => console.error('Error al obtener el CAPTCHA:', error));
        }

        // Función para reproducir el CAPTCHA en el modal de recuperación
        function playCaptchaRecover() {
            // Obtener el texto del CAPTCHA desde el servidor
            fetch('get_captcha_text.php')
                .then(response => response.text())
                .then(text => {
                    // Dividir el texto en caracteres individuales
                    const characters = text.split('');
                    let index = 0;

                    // Función para reproducir un carácter
                    function speakNextCharacter() {
                        if (index < characters.length) {
                            const utterance = new SpeechSynthesisUtterance(characters[index]);
                            utterance.lang = 'es-ES'; // Configurar el idioma
                            utterance.onend = speakNextCharacter; // Reproducir el siguiente carácter al terminar
                            speechSynthesis.speak(utterance);
                            index++;
                        }
                    }

                    // Comenzar a reproducir
                    speakNextCharacter();
                })
                .catch(error => console.error('Error al obtener el CAPTCHA:', error));
        }

        // Cambiar entre modo oscuro y claro
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const body = document.body;

        themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            if (body.classList.contains('dark-mode')) {
                themeIcon.classList.remove('fa-moon'); // Quitar ícono de luna
                themeIcon.classList.add('fa-sun'); // Agregar ícono de sol
            } else {
                themeIcon.classList.remove('fa-sun'); // Quitar ícono de sol
                themeIcon.classList.add('fa-moon'); // Agregar ícono de luna
            }
        });


        // Mostrar el modal de error CSRF
function showCSRFErrorModal() {
    const modal = document.getElementById('csrfErrorModal');
    modal.style.display = 'flex'; // Mostrar el modal
}

// Cerrar el modal y recargar la página
function closeCSRFErrorModal() {
    const modal = document.getElementById('csrfErrorModal');
    modal.style.display = 'none'; // Ocultar el modal
    window.location.reload(); // Recargar la página
}

// Recargar la página
function reloadPage() {
    window.location.reload();
}

// Eventos para el modal
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('csrfErrorModal');
    const closeButton = document.querySelector('.close-modal');


    // Cerrar el modal y recargar la página al hacer clic en la "X"
    closeButton.addEventListener('click', closeCSRFErrorModal);


    // Cerrar el modal y recargar la página al hacer clic fuera del contenido
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeCSRFErrorModal();
        }
    });
});
    </script>
</body>
</html>