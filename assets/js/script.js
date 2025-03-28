document.getElementById("btn__iniciar-sesion").addEventListener("click", login);
document.getElementById("btn__registrarse").addEventListener("click", register);
window.addEventListener("resize", anchoPagina);

// Declaración de variables
var container_login_register = document.querySelector(".container__login__register");
var formulario_login = document.querySelector(".formulario__login");
var formulario_register = document.querySelector(".formulario__register");
var caja_trasera_login = document.querySelector(".caja__trasera__login");
var caja_trasera_register = document.querySelector(".caja__trasera__register");

// Función para manejar el ancho de la página
function anchoPagina() {
    if (window.innerWidth > 850) {
        caja_trasera_login.style.display = "block";
        caja_trasera_register.style.display = "block";
    } else {
        caja_trasera_register.style.display = "block";
        caja_trasera_register.style.opacity = "1";
        caja_trasera_login.style.display = "none";
        formulario_login.style.display = "block";
        formulario_register.style.display = "none";
        container_login_register.style.left = "0";
    }
}

// Función para mostrar el formulario de inicio de sesión
function login() {
    if (window.innerWidth > 850) {
        formulario_register.style.display = "none";
        container_login_register.style.left = "10px";
        formulario_login.style.display = "block";
        caja_trasera_register.style.opacity = "1";
        caja_trasera_login.style.opacity = "1";
    } else {
        formulario_register.style.display = "none";
        container_login_register.style.left = "0px";
        formulario_login.style.display = "block";
        caja_trasera_register.style.display = "block";
        caja_trasera_login.style.display = "none";
    }
}

// Función para mostrar el formulario de recuperación de contraseña
function register() {
    if (window.innerWidth > 850) {
        formulario_register.style.display = "block";
        container_login_register.style.left = "410px";
        formulario_login.style.display = "none";
        caja_trasera_register.style.opacity = "1";
        caja_trasera_login.style.opacity = "1";
    } else {
        formulario_register.style.display = "block";
        container_login_register.style.left = "0px";
        formulario_login.style.display = "none";
        caja_trasera_register.style.display = "none";
        caja_trasera_login.style.display = "block";
    }
}

// Asegurarse de que el formulario de inicio de sesión sea el que se muestre al cargar la página
window.onload = function () {
    login(); // Mostrar el formulario de inicio de sesión por defecto
    anchoPagina(); // Ajustar el diseño según el ancho de la página
};

 