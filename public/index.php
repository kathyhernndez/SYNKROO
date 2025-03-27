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
                    <span class="help-text">La contraseña debe tener al menos 16 caracteres.</span>
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
<form id="recoveryForm" class="formulario__register" style="display: none;">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    
    <!-- Paso 1: Verificación inicial -->
    <div id="step1">
        <h2>Recuperar Contraseña</h2>
        <input type="email" id="recoverUser" name="recoverUser" placeholder="Ingresa un correo electrónico válido" required minlength="5" maxlength="64" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
        <span class="help-text">Ingresa tu correo electrónico para verificar tu usuario.</span>
        <input type="number" id="recoverCedula" name="recoverCedula" placeholder="Ingresa tu cédula de identidad" required minlength="5" maxlength="12">
        <span class="help-text">Ingresa tu cédula de identidad para verificar tu usuario.</span>
        <label for="recoverCaptcha">CAPTCHA:</label>
        <div>
            <img src="../php/captcha.php" style="margin: 10px 0;" alt="CAPTCHA" class="captcha-image" id="captchaImageRecover">
            <button style="padding: 5px 10px;" type="button" title="Este botón es para recargar el captcha" class="reload-button" onclick="reloadCaptchaRecover()">↻</button>
            <button style="padding: 5px 10px;" type="button" title="Este botón es para reproducir el captcha" class="audio-button" onclick="playCaptchaRecover()">▶</button>
        </div>
        <input type="text" id="recoverCaptcha" name="recoverCaptcha" placeholder="Ingresa lo que ves en la imagen" required>
        <span class="help-text">Escribe el captcha de la imagen para verificar que no eres un robot.</span>
        <button type="button" onclick="verifyUser()">Verificar Usuario</button>
    </div>
    
    <!-- Paso 2: Preguntas de seguridad -->
    <div id="step2" style="display: none;">
        <h2>Preguntas de Seguridad</h2>
        <div id="preguntasContainer">
            <!-- Las preguntas se cargarán dinámicamente aquí -->
        </div>
        <button type="button" onclick="verifyAnswers()">Verificar Respuestas</button>
    </div>
    
    <!-- Paso 3: Cambiar contraseña -->
    <div id="step3" style="display: none;">
        <h2>Cambiar Contraseña</h2>
        <input type="password" id="nueva_contrasena" name="nueva_contrasena" placeholder="Nueva contraseña" required minlength="8" maxlength="64">
        <span class="help-text">La contraseña debe tener al menos 16 caracteres.</span>
        <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" placeholder="Confirmar nueva contraseña" required minlength="8" maxlength="64">
        <span class="help-text">Vuelve a escribir la nueva contraseña.</span>
        <button type="button" onclick="changePassword()">Cambiar Contraseña</button>
    </div>
    
    <!-- Mensaje de éxito -->
    <div id="successMessage" style="display: none; text-align: center;">
        <h2 style="color: #4CAF50;">¡Contraseña cambiada con éxito!</h2>
        <p>Tu contraseña ha sido actualizada correctamente.</p>
        <button type="button" onclick="location.reload()">Volver al inicio</button>
    </div>
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



// Función para verificar el usuario (Paso 1)
function verifyUser() {
    const email = document.getElementById('recoverUser').value;
    const cedula = document.getElementById('recoverCedula').value;
    const captcha = document.getElementById('recoverCaptcha').value;

    if (!email || !cedula || !captcha) {
        alert("Por favor, completa todos los campos.");
        return;
    }

    // Validar formato de email
    if (!/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/.test(email)) {
        alert("Por favor, ingresa un correo electrónico válido.");
        return;
    }

    // Mostrar carga
    const btn = document.querySelector('#step1 button');
    btn.disabled = true;
    btn.innerHTML = 'Verificando...';

    // Enviar datos al servidor
    const formData = new FormData();
    formData.append('recoverUser', email);
    formData.append('recoverCedula', cedula);
    formData.append('recoverCaptcha', captcha);
    formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

    fetch('../php/recuperar_contrasena_be.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Mostrar preguntas de seguridad
            document.getElementById('step1').style.display = 'none';
            document.getElementById('step2').style.display = 'block';
            
            // Cargar preguntas
            const container = document.getElementById('preguntasContainer');
            container.innerHTML = `
                <div class="pregunta">
                    <label>${data.pregunta_1}</label>
                    <input type="text" name="respuesta_1" required>
                </div>
                <div class="pregunta">
                    <label>${data.pregunta_2}</label>
                    <input type="text" name="respuesta_2" required>
                </div>
                <div class="pregunta">
                    <label>${data.pregunta_3}</label>
                    <input type="text" name="respuesta_3" required>
                </div>
            `;
        } else {
            alert(data.message || 'Error al verificar el usuario');
            reloadCaptchaRecover();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al procesar la solicitud');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = 'Verificar Usuario';
    });
}

// Función para verificar respuestas (Paso 2)
function verifyAnswers() {
    // Obtener valores de los inputs
    const respuesta1 = document.querySelector('input[name="respuesta_1"]').value.trim();
    const respuesta2 = document.querySelector('input[name="respuesta_2"]').value.trim();
    const respuesta3 = document.querySelector('input[name="respuesta_3"]').value.trim();
    const csrfToken = document.querySelector('input[name="csrf_token"]').value;

    // Validar que no estén vacíos
    if (!respuesta1 || !respuesta2 || !respuesta3) {
        alert("Por favor, responde todas las preguntas.");
        return;
    }

    // Mostrar carga
    const btn = document.querySelector('#step2 button');
    btn.disabled = true;
    btn.innerHTML = 'Verificando...';

    // Crear objeto con los datos
    const formData = new FormData();
    formData.append('respuesta_1', respuesta1);
    formData.append('respuesta_2', respuesta2);
    formData.append('respuesta_3', respuesta3);
    formData.append('csrf_token', csrfToken);

    // Enviar respuestas al servidor como FormData
    fetch('../php/verificar_respuestas_be.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('step2').style.display = 'none';
            document.getElementById('step3').style.display = 'block';
        } else {
            alert(data.message || 'Respuestas incorrectas');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al procesar las respuestas');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = 'Verificar Respuestas';
    });
}

// Función para cambiar contraseña (Paso 3)
function changePassword() {
    const nuevaContrasena = document.getElementById('nueva_contrasena').value;
    const confirmarContrasena = document.getElementById('confirmar_contrasena').value;
    const csrfToken = document.querySelector('input[name="csrf_token"]').value;

    if (!nuevaContrasena || !confirmarContrasena) {
        alert("Por favor, completa ambos campos.");
        return;
    }

    if (nuevaContrasena !== confirmarContrasena) {
        alert("Las contraseñas no coinciden.");
        return;
    }

    if (nuevaContrasena.length < 8) {
        alert("La contraseña debe tener al menos 8 caracteres.");
        return;
    }

    // Mostrar carga
    const btn = document.querySelector('#step3 button');
    btn.disabled = true;
    btn.innerHTML = 'Procesando...';

    // Usar FormData en lugar de JSON
    const formData = new FormData();
    formData.append('nueva_contrasena', nuevaContrasena);
    formData.append('confirmar_contrasena', confirmarContrasena);
    formData.append('csrf_token', csrfToken);

    fetch('../php/cambiar_contrasena_be.php', {
        method: 'POST',
        body: formData // Enviar como FormData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('step3').style.display = 'none';
            document.getElementById('successMessage').style.display = 'block';
        } else {
            alert(data.message || 'Error al cambiar la contraseña');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al cambiar la contraseña');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = 'Cambiar Contraseña';
    });
}

    </script>
</body>
</html>