<?php
// Archivo: logout.php

session_start();
include 'conexion_be.php'; // Incluye la conexión a la base de datos (debe ser PDO)
include 'registrar_accion.php'; // Incluye la función para registrar acciones

// Registrar la acción de cierre de sesión
if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];

    // Llamar a la función para registrar la acción
    registrarAccion($conexion, $usuario_id, 'cierre de sesión', 'El usuario ha cerrado sesión en el sistema.');
}



// Destruir la sesión y las cookies
session_unset();
session_destroy();
setcookie('PHPSESSID', '', time() - 3600, '/');

// Redirigir al usuario con un mensaje de éxito
echo '
<script>
alert("Sesión cerrada exitosamente.");
window.location = "../public/index.php";
</script>
';
exit();
?>