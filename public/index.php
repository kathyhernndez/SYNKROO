<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/image/favicon.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Synkroo | UPTAG</title>
    <style>
        /* Estilos adicionales para los nuevos elementos */
        .password-container {
            position: relative;
        }
        
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 25%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }
        
    </style>
</head>
<body>
    <!-- Barra superior -->
    <header class="navbar">
        <div class="navbar-content">
            <button id="theme-toggle" class="theme-toggle">
                <i id="theme-icon" class="fas fa-moon"></i>
            </button>
            <div class="navbar-brand">
                <img src="../assets/image/logo.png" alt="Logo UPTAG" class="logo">
                <h1 class="app-name">UPTAG</h1>
            </div>
        </div>
    </header>

    <main>
        <div class="container__all">
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
            
            <div class="container__login__register">
                <!-- Formulario de Login -->
<form action="../php/login_user_be.php" method="POST" class="formulario__login" id="loginForm">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <h2>Iniciar Sesión</h2>
                    
                    <div class="form-group">
                        <input type="email" placeholder="Correo Electrónico" name="correo" required 
                               minlength="5" maxlength="64" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                        <span class="help-text">Ingresa tu correo electrónico registrado (ejemplo: usuario@dominio.com)</span>
                    </div>
                    
                    <div class="form-group password-container">
                        <input type="password" placeholder="Contraseña" name="clave" id="loginPassword" 
                               required minlength="" maxlength="64">
                        <i class="toggle-password fas fa-eye" onclick="togglePassword('loginPassword', this)"></i>
                        <span class="help-text">La contraseña debe tener al menos 16 caracteres, incluyendo mayúsculas, minúsculas y números</span>
                    </div>
                    
                    <label for="captcha">CAPTCHA:</label>
<div class="captcha-container">
    <img src="../php/captcha.php" alt="CAPTCHA" class="captcha-image" id="captchaImage">
    <button type="button" title="Recargar CAPTCHA" class="reload-button" onclick="reloadCaptcha()">
        <i class="fas fa-sync-alt"></i>
    </button>
    <button type="button" title="Reproducir CAPTCHA" class="audio-button" onclick="playCaptcha()">
        <i class="fas fa-volume-up"></i>
    </button>
</div>
<input type="text" id="captcha" name="captcha" placeholder="Ingresa el código CAPTCHA" required>
                    
                    <span class="help-text">Escribe el texto que aparece en la imagen para verificar que no eres un robot</span>
                    
                    <button type="submit" id="loginSubmit" class="btn">
    <span id="loginText">Entrar</span>
    <div id="loginSpinner" class="loading-spinner"></div>
</button>
</form>

                <!-- Formulario de Recuperación -->
                <form id="recoveryForm" class="formulario__register" style="display: none;">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <!-- Paso 1: Verificación inicial -->
                    <div id="step1">
                        <h2>Recuperar Contraseña</h2>
                        <div id="step1Messages"></div>
                        
                        <div class="form-group">
                            <input type="email" id="recoverUser" name="recoverUser" placeholder="Correo electrónico" 
                                   required minlength="5" maxlength="64" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                            <span class="help-text">Ingresa el correo electrónico asociado a tu cuenta</span>
                        </div>
                        
                        <div class="form-group">
                            <input type="number" id="recoverCedula" name="recoverCedula" placeholder="Cédula de identidad" 
                                   required minlength="5" maxlength="12">
                            <span class="help-text">Ingresa tu número de cédula sin puntos ni guiones</span>
                        </div>
                        
                        <label for="recoverCaptcha">CAPTCHA:</label>
                        <div class="captcha-container">
                            <img src="../php/captcha.php" alt="CAPTCHA" class="captcha-image" id="captchaImageRecover">
                            <button type="button" title="Recargar CAPTCHA" class="reload-button" onclick="reloadCaptchaRecover()">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <button type="button" title="Reproducir CAPTCHA" class="audio-button" onclick="playCaptchaRecover()">
                                <i class="fas fa-volume-up"></i>
                            </button>
                        </div>
                        <input type="text" id="recoverCaptcha" name="recoverCaptcha" placeholder="Ingresa el código CAPTCHA" required>
                        <span class="help-text">Escribe el texto que aparece en la imagen para continuar</span>
                        
                        <button type="button" onclick="verifyUser()" id="verifyUserBtn">
                            <span id="verifyUserText">Verificar Usuario</span>
                            <div id="verifyUserSpinner" class="loading-spinner"></div>
                        </button>
                    </div>
                    
                    <!-- Paso 2: Preguntas de seguridad -->
                    <div id="step2" style="display: none;">
                        <h2>Preguntas de Seguridad</h2>
                        <div id="step2Messages"></div>
                        <div id="preguntasContainer"></div>
                        
                        <button type="button" onclick="verifyAnswers()" id="verifyAnswersBtn">
                            <span id="verifyAnswersText">Verificar Respuestas</span>
                            <div id="verifyAnswersSpinner" class="loading-spinner"></div>
                        </button>
                    </div>
                    
                    <!-- Paso 3: Cambiar contraseña -->
                    <div id="step3" style="display: none;">
                        <h2>Cambiar Contraseña</h2>
                        <div id="step3Messages"></div>
                        
                        <div class="form-group password-container">
                            <input type="password" id="nueva_contrasena" name="nueva_contrasena" 
                                   placeholder="Nueva contraseña" required minlength="16" maxlength="64">
                            <i class="toggle-password fas fa-eye" onclick="togglePassword('nueva_contrasena', this)"></i>
                            <span class="help-text">Crea una contraseña segura, debe tener al menos 16 caracteres, incluyendo mayúsculas, minúsculas y números</span>
                        </div>
                        
                        <div class="form-group password-container">
                            <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" 
                                   placeholder="Confirmar nueva contraseña" required minlength="16" maxlength="64">
                            <i class="toggle-password fas fa-eye" onclick="togglePassword('confirmar_contrasena', this)"></i>
                            <span class="help-text">Vuelve a escribir tu nueva contraseña para confirmar</span>
                        </div>
                        
                        <button type="button" onclick="changePassword()" id="changePasswordBtn">
                            <span id="changePasswordText">Cambiar Contraseña</span>
                            <div id="changePasswordSpinner" class="loading-spinner"></div>
                        </button>
                    </div>
                    
                    <!-- Mensaje de éxito -->
                    <div id="successMessage" style="display: none; text-align: center;">
                        <h2 style="color: #4CAF50;">¡Contraseña cambiada con éxito!</h2>
                        <p>Tu contraseña ha sido actualizada correctamente.</p>
                        <button type="button" onclick="location.reload()" class="btn-success">Volver al inicio</button>
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

    <footer class="slim-footer">
    <div class="footer-content">
        <!-- Redes sociales a la izquierda -->
        <div class="footer-social">
            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
        </div>
        
        <!-- Logo y nombre a la derecha -->
        <div class="footer-brand">
            <img src="../assets/image/synkroo.png" alt="Synkroo Logo" class="footer-logo">
            <img src="../assets/image/UPTAG.png" alt="UPTAG Logo" class="footer-logo">
            <img src="../assets/image/logo.png" alt="Comunicacional Logo" class="footer-logo">
        </div>
        
        <!-- Copyright y desarrolladores en el centro en una línea -->
        <div class="footer-info">
            <span class="footer-copyright">&copy; <span id="current-year">2023</span> UPTAG</span>
            <span class="footer-separator">|</span>
            <span class="footer-developers">Desarrollado por: Hernandez, Pachano, Perez, Bracho</span>
        </div>
    </div>
</footer>

    

    <script src="../assets/js/script.js"></script>
    <script>
        // Función para mostrar/ocultar contraseña
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Función para mostrar mensajes de ayuda/error
        function showMessage(containerId, message, isError = false) {
            const container = document.getElementById(containerId);
            container.innerHTML = `<div class="${isError ? 'error-message' : 'success-message'}">${message}</div>`;
            setTimeout(() => {
                container.innerHTML = '';
            }, 5000);
        }

        // Función mejorada para mostrar carga
        function showLoading(buttonId, show) {
    const button = document.getElementById(buttonId);
    if (!button) return;

    const spinner = button.querySelector('.loading-spinner');
    const buttonText = button.querySelector('span:not(.loading-spinner)');

    if (show) {
        button.disabled = true;
        if (spinner) spinner.classList.add('visible');
        if (buttonText) buttonText.style.opacity = '0';
    } else {
        button.disabled = false;
        if (spinner) spinner.classList.remove('visible');
        if (buttonText) buttonText.style.opacity = '1';
    }
}

// Manejo del formulario de login
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    
    // Validación básica
    const email = form.querySelector('input[name="correo"]').value;
    const password = form.querySelector('input[name="clave"]').value;
    const captcha = form.querySelector('input[name="captcha"]').value;
    
    if (!email || !password || !captcha) {
        alert('Por favor complete todos los campos');
        return false;
    }
    
    // Mostrar spinner
    showLoading('loginSubmit', true);
    
    // Crear FormData
    const formData = new FormData(form);
    
    // Enviar con fetch
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.redirected) {
            // Si hay redirección, seguirla
            window.location.href = response.url;
            return;
        }
        return response.text();
    })
    .then(data => {
        showLoading('loginSubmit', false);
        if (data) {
            // Manejar respuestas que no son redirección
            try {
                const jsonData = JSON.parse(data);
                if (jsonData.success) {
                    window.location.href = jsonData.redirect || '../php/menu.php';
                } else {
                    alert(jsonData.message || 'Error al iniciar sesión');
                    reloadCaptcha();
                }
            } catch (e) {
                // Si no es JSON, mostrar el mensaje tal cual
                console.error(data);
                alert('Error en el servidor');
                reloadCaptcha();
            }
        }
    })
    .catch(error => {
        showLoading('loginSubmit', false);
        console.error('Error:', error);
        alert('Ocurrió un error al procesar la solicitud');
        reloadCaptcha();
    });
});



        // Función para verificar el usuario (Paso 1) con retraso
        function verifyUser() {
            const email = document.getElementById('recoverUser').value;
            const cedula = document.getElementById('recoverCedula').value;
            const captcha = document.getElementById('recoverCaptcha').value;

            if (!email || !cedula || !captcha) {
                showMessage('step1Messages', 'Por favor, completa todos los campos', true);
                return;
            }

            if (!/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/.test(email)) {
                showMessage('step1Messages', 'Por favor, ingresa un correo electrónico válido', true);
                return;
            }

            showLoading('verifyUserBtn', true);

            // Simular retraso de 2 segundos
            setTimeout(() => {
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
                        // Mostrar preguntas de seguridad después de otro breve retraso
                        setTimeout(() => {
                            document.getElementById('step1').style.display = 'none';
                            document.getElementById('step2').style.display = 'block';
                            
                            const container = document.getElementById('preguntasContainer');
                            container.innerHTML = `
                                <div class="pregunta">
                                    <label>${data.pregunta_1}</label>
                                    <input type="text" name="respuesta_1" required>
                                    <span class="help-text">Responde la pregunta de seguridad</span>
                                </div>
                                <div class="pregunta">
                                    <label>${data.pregunta_2}</label>
                                    <input type="text" name="respuesta_2" required>
                                    <span class="help-text">Responde la pregunta de seguridad</span>
                                </div>
                                <div class="pregunta">
                                    <label>${data.pregunta_3}</label>
                                    <input type="text" name="respuesta_3" required>
                                    <span class="help-text">Responde la pregunta de seguridad</span>
                                </div>
                            `;
                            showLoading('verifyUserBtn', false);
                        }, 500);
                    } else {
                        showMessage('step1Messages', data.message || 'Error al verificar el usuario', true);
                        reloadCaptchaRecover();
                        showLoading('verifyUserBtn', false);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('step1Messages', 'Ocurrió un error al procesar la solicitud', true);
                    showLoading('verifyUserBtn', false);
                });
            }, 2000); // Retraso de 2 segundos
        }

        // Función para verificar respuestas (Paso 2) con retraso
        function verifyAnswers() {
            const respuesta1 = document.querySelector('input[name="respuesta_1"]').value.trim();
            const respuesta2 = document.querySelector('input[name="respuesta_2"]').value.trim();
            const respuesta3 = document.querySelector('input[name="respuesta_3"]').value.trim();
            const csrfToken = document.querySelector('input[name="csrf_token"]').value;

            if (!respuesta1 || !respuesta2 || !respuesta3) {
                showMessage('step2Messages', 'Por favor, responde todas las preguntas', true);
                return;
            }

            showLoading('verifyAnswersBtn', true);

            // Simular retraso de 2 segundos
            setTimeout(() => {
                const formData = new FormData();
                formData.append('respuesta_1', respuesta1);
                formData.append('respuesta_2', respuesta2);
                formData.append('respuesta_3', respuesta3);
                formData.append('csrf_token', csrfToken);

                fetch('../php/verificar_respuestas_be.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Mostrar formulario de cambio de contraseña después de otro breve retraso
                        setTimeout(() => {
                            document.getElementById('step2').style.display = 'none';
                            document.getElementById('step3').style.display = 'block';
                            showLoading('verifyAnswersBtn', false);
                        }, 500);
                    } else {
                        showMessage('step2Messages', data.message || 'Respuestas incorrectas', true);
                        showLoading('verifyAnswersBtn', false);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('step2Messages', 'Ocurrió un error al procesar las respuestas', true);
                    showLoading('verifyAnswersBtn', false);
                });
            }, 2000); // Retraso de 2 segundos
        }

        // Función para cambiar contraseña (Paso 3) con retraso
        function changePassword() {
            const nuevaContrasena = document.getElementById('nueva_contrasena').value;
            const confirmarContrasena = document.getElementById('confirmar_contrasena').value;
            const csrfToken = document.querySelector('input[name="csrf_token"]').value;

            if (!nuevaContrasena || !confirmarContrasena) {
                showMessage('step3Messages', 'Por favor, completa ambos campos', true);
                return;
            }

            if (nuevaContrasena !== confirmarContrasena) {
                showMessage('step3Messages', 'Las contraseñas no coinciden', true);
                return;
            }

            if (nuevaContrasena.length < 8) {
                showMessage('step3Messages', 'La contraseña debe tener al menos 16 caracteres', true);
                return;
            }

            showLoading('changePasswordBtn', true);

            // Simular retraso de 2 segundos
            setTimeout(() => {
                const formData = new FormData();
                formData.append('nueva_contrasena', nuevaContrasena);
                formData.append('confirmar_contrasena', confirmarContrasena);
                formData.append('csrf_token', csrfToken);

                fetch('../php/cambiar_contrasena_be.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Mostrar mensaje de éxito después de otro breve retraso
                        setTimeout(() => {
                            document.getElementById('step3').style.display = 'none';
                            document.getElementById('successMessage').style.display = 'block';
                            showLoading('changePasswordBtn', false);
                        }, 500);
                    } else {
                        showMessage('step3Messages', data.message || 'Error al cambiar la contraseña', true);
                        showLoading('changePasswordBtn', false);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('step3Messages', 'Ocurrió un error al cambiar la contraseña', true);
                    showLoading('changePasswordBtn', false);
                });
            }, 2000); // Retraso de 2 segundos
        }

        // Funciones para el CAPTCHA
        function reloadCaptcha() {
            const captchaImage = document.getElementById('captchaImage');
            captchaImage.src = '../php/captcha.php?' + new Date().getTime();
        }

        function reloadCaptchaRecover() {
            const captchaImage = document.getElementById('captchaImageRecover');
            captchaImage.src = '../php/captcha.php?' + new Date().getTime();
        }

        function playCaptcha() {
            fetch('../php/get_captcha_text.php')
                .then(response => response.text())
                .then(text => {
                    const characters = text.split('');
                    let index = 0;
                    function speakNextCharacter() {
                        if (index < characters.length) {
                            const utterance = new SpeechSynthesisUtterance(characters[index]);
                            utterance.lang = 'es-ES';
                            utterance.onend = speakNextCharacter;
                            speechSynthesis.speak(utterance);
                            index++;
                        }
                    }
                    speakNextCharacter();
                })
                .catch(error => console.error('Error al obtener el CAPTCHA:', error));
        }

        function playCaptchaRecover() {
            fetch('get_captcha_text.php')
                .then(response => response.text())
                .then(text => {
                    const characters = text.split('');
                    let index = 0;
                    function speakNextCharacter() {
                        if (index < characters.length) {
                            const utterance = new SpeechSynthesisUtterance(characters[index]);
                            utterance.lang = 'es-ES';
                            utterance.onend = speakNextCharacter;
                            speechSynthesis.speak(utterance);
                            index++;
                        }
                    }
                    speakNextCharacter();
                })
                .catch(error => console.error('Error al obtener el CAPTCHA:', error));
        }

        // Configuración del tema oscuro/claro persistente
const themeToggle = document.getElementById('theme-toggle');
const themeIcon = document.getElementById('theme-icon');
const body = document.body;

// Función para cargar el tema guardado
function loadTheme() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        body.classList.add('dark-mode');
        themeIcon.classList.replace('fa-moon', 'fa-sun');
    } else {
        body.classList.remove('dark-mode');
        themeIcon.classList.replace('fa-sun', 'fa-moon');
    }
}

// Función para cambiar el tema
function toggleTheme() {
    body.classList.toggle('dark-mode');
    if (body.classList.contains('dark-mode')) {
        themeIcon.classList.replace('fa-moon', 'fa-sun');
        localStorage.setItem('theme', 'dark');
    } else {
        themeIcon.classList.replace('fa-sun', 'fa-moon');
        localStorage.setItem('theme', 'light');
    }
}

// Cargar el tema al iniciar
document.addEventListener('DOMContentLoaded', loadTheme);

// Configurar el evento click
themeToggle.addEventListener('click', toggleTheme);

        
        // Configuración del modal de error CSRF 
        function showCSRFErrorModal() {
            document.getElementById('csrfErrorModal').style.display = 'flex';
        }

        function closeCSRFErrorModal() {
            document.getElementById('csrfErrorModal').style.display = 'none';
            window.location.reload();
        }

        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('csrfErrorModal');
            const closeButton = document.querySelector('.close-modal');

            closeButton.addEventListener('click', closeCSRFErrorModal);
            window.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeCSRFErrorModal();
                }
            });
        });

/* Actualizar año automáticamente */
        document.getElementById('current-year').textContent = new Date().getFullYear();
    </script>
</body>
</html>