// Variables globales
var container_login_register = document.querySelector(".container__login__register");
var formulario_login = document.querySelector(".formulario__login");
var formulario_register = document.querySelector(".formulario__register");
var caja_trasera_login = document.querySelector(".caja__trasera__login");
var caja_trasera_register = document.querySelector(".caja__trasera__register");
let currentForm = 'login'; // 'login' o 'recovery'

// Event listeners
document.getElementById("btn__iniciar-sesion").addEventListener("click", handleLoginClick);
document.getElementById("btn__registrarse").addEventListener("click", handleRegisterClick);
window.addEventListener("resize", handleResize);

// Función para manejar el ancho de la página
function handleResize() {
    showForm(currentForm);
}

// Función para mostrar el formulario de inicio de sesión
function handleLoginClick(e) {
    e.preventDefault();
    currentForm = 'login';
    showForm('login');
}

// Función para mostrar el formulario de recuperación
function handleRegisterClick(e) {
    e.preventDefault();
    currentForm = 'recovery';
    showForm('recovery');
}

// Función principal para mostrar el formulario adecuado
function showForm(formType) {
    // Asegurarse de que los elementos existen
    if (!formulario_login || !formulario_register || !caja_trasera_login || !caja_trasera_register) {
        return;
    }

    if (window.innerWidth > 850) {
        // Versión desktop
        if (formType === 'login') {
            formulario_login.style.display = "block";
            formulario_register.style.display = "none";
            container_login_register.style.left = "10px";
            caja_trasera_login.style.opacity = "1";
            caja_trasera_register.style.opacity = "1";
        } else {
            formulario_login.style.display = "none";
            formulario_register.style.display = "block";
            container_login_register.style.left = "410px";
            caja_trasera_login.style.opacity = "1";
            caja_trasera_register.style.opacity = "1";
        }
    } else {
        // Versión móvil
        if (formType === 'login') {
            formulario_login.style.display = "block";
            formulario_register.style.display = "none";
            caja_trasera_login.style.display = "none";
            caja_trasera_register.style.display = "block";
        } else {
            formulario_login.style.display = "none";
            formulario_register.style.display = "block";
            caja_trasera_login.style.display = "block";
            caja_trasera_register.style.display = "none";
        }
        container_login_register.style.left = "0px";
    }
}

// Prevenir cambios no deseados al interactuar con los inputs en móvil
function setupInputHandlers() {
    const inputs = document.querySelectorAll('.formulario__register input, .formulario__login input');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            if (window.innerWidth <= 850) {
                showForm(currentForm);
            }
        });
        
        input.addEventListener('touchstart', function(e) {
            if (window.innerWidth <= 850) {
                e.stopPropagation();
                showForm(currentForm);
            }
        }, {passive: false});
    });
}

// Inicialización al cargar la página
window.onload = function() {
    showForm('login'); // Mostrar formulario de login por defecto
    setupInputHandlers(); // Configurar manejadores para los inputs
    
    // Asegurar que el formulario correcto se muestre después de redimensionar
    window.addEventListener('orientationchange', function() {
        setTimeout(() => {
            showForm(currentForm);
        }, 300);
    });
};