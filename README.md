# sistema_gestor_archivos
Sistema gestor de archivos para el area de presan de la UPTAG Falcon Venezuela.


// Verificar si el usuario tiene una sesión activa en la base de datos
//$validar_sesion = mysqli_query($conexion, "SELECT * FROM usuarios WHERE correo='$correo' AND session_active=1");

//if (mysqli_num_rows($validar_sesion) > 0) {
  // echo '
   // <script>
   //    alert("El usuario ya tiene una sesión iniciada.");
   //     window.location = "../index.php";
    //</script>
   // ';
    //exit();
//}


if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo '
        <script>
            alert("Error de seguridad. Por favor, recarga la página e intenta nuevamente.");
            window.location = "../public/index.php";
        </script>
        ';
        exit();
    }

    // Verificar la caducidad del CSRF token (ejemplo: 10 minutos)
    $token_expiration_time = 600; // 600 segundos = 10 minutos
    if (!isset($_SESSION['csrf_token_time']) || (time() - $_SESSION['csrf_token_time']) > $token_expiration_time) {
        echo '
        <script>
            alert("El token de seguridad ha expirado. Por favor, recarga la página e intenta nuevamente.");
            window.location = "../public/index.php";
        </script>
        ';
        exit();
    }

    // Regenerar el token después de su uso
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_time'] = time();

<?php
// Archivo: logout.php

session_start();
include 'conexion_be.php'; // Incluye la conexión a la base de datos (debe ser PDO)
include 'registrar_accion.php'; // Incluye la función para registrar acciones

// Registrar la acción de cierre de sesión
if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    //$nombre_completo = $_SESSION['nombre_completo'] ?? 'Usuario desconocido'; // Usar un valor predeterminado si no existe

    // Llamar a la función para registrar la acción
    registrarAccion($conexion, $usuario_id, 'cierre de sesión', 'El usuario ha cerrado sesión en el sistema.');
}

// Verificar si el usuario está logueado y actualizar el estado de la sesión
if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    
    // Establecer la sesión como inactiva en la base de datos (usando PDO)
    try {
        $query = "UPDATE usuarios SET session_active = 0 WHERE id = :usuario_id";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        die("Error al actualizar el estado de la sesión: " . $e->getMessage());
    }
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