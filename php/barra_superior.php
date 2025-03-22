<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    /// Obtener los datos del usuario desde la sesión
    $nombreCompleto = $_SESSION['nombre_completo'] ?? 'Usuario';
    $emailUsuario = $_SESSION['correo'] ?? 'correo@example.com';
    $idRol = $_SESSION['id_roles'] ?? '';

    // Mapear el ID del rol a un nombre de rol (puedes hacerlo con un array o una consulta a la base de datos)
    $roles = [
        1 => 'Admin',
        2 => 'Editor',
        3 => 'Usuario',
        // Agrega más roles según sea necesario
    ];

    $rolUsuario = $roles[$idRol] ?? 'Rol no disponible';
} else {
    // Si no hay sesión, redirigir al login
    header('Location: ../index.php');
    exit();
}
?>




<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
 
 
 <!-- Barra superior -->
 <div class="top-bar">
                <!-- Botones a la izquierda -->
                <div class="left-buttons">
    <button class="menu-btn" id="menu-btn" aria-label="Abrir menú"><i class="fas fa-bars"></i></button>
    <button class="theme-btn" id="theme-btn" aria-label="Cambiar tema"><i class="fas fa-moon"></i></button>
    <div class="user-info">
        <button class="user-btn" id="user-btn" aria-label="Abrir menú de usuario"><i class="fas fa-user"></i></button>
        <div class="user-dropdown" id="user-dropdown">
            <p id="user-name">Usuario: Cargando...</p>
            <p id="user-email">Correo: Cargando...</p>
            <p id="user-rol">Rol: Cargando...</p>
            <a><button onclick="confirmLogout()" class="logout_btn">Cerrar Sesión</button></a>
        </div>
    </div>
</div>

                <!-- Nombre del programa y logo centrados -->
                <div class="program-info">
                    <h1>SGDA</h1>
                    <img src="../assets/image/logo.png" alt="Logo del programa" class="logo">
                </div>
            </div>

            
            <script>
    // Pasar los datos del usuario a JavaScript
    const usuario = {
        nombre: "<?php echo $nombreCompleto; ?>",
        email: "<?php echo $emailUsuario; ?>",
        rol: "<?php echo $rolUsuario; ?>"
    };
</script>