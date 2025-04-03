<?php


// Verificar si el usuario ha iniciado sesión
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Obtener los datos del usuario desde la sesión
    $nombreCompleto = $_SESSION['nombre_completo'] ?? 'Usuario';
    $emailUsuario = $_SESSION['correo'] ?? 'correo@example.com';
    $idRol = $_SESSION['id_roles'] ?? '';

    // Mapear el ID del rol a un nombre de rol
    $roles = [
        1 => 'Admin',
        2 => 'SuperAdmin',
        3 => 'Personal',
    ];

    $rolUsuario = $roles[$idRol] ?? 'Rol no disponible';
} else {
    // Si no hay sesión, redirigir al login
    header('Location: ../index.php');
    exit();
}
?>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<body>
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
                <button id="logout-btn" class="logout_btn">Cerrar Sesión</button>
            </div>
        </div>
    </div>

    <!-- Nombre del programa y logo centrados -->
    <div class="program-info">
        <img src="../assets/image/logo.png" alt="Logo del programa" class="logo">
        <h1>SGDA</h1>
    </div>
</div>

<script>
    // Pasar los datos del usuario a JavaScript
    const usuario = {
        nombre: "<?php echo $nombreCompleto; ?>",
        email: "<?php echo $emailUsuario; ?>",
        rol: "<?php echo $rolUsuario; ?>"
    };

    // Función para actualizar los datos del usuario
    function actualizarDatosUsuario() {
        console.log('actualizarDatosUsuario ejecutado');
        const userNameElement = document.getElementById('user-name');
        const userEmailElement = document.getElementById('user-email');
        const userRolElement = document.getElementById('user-rol');

        if (usuario && usuario.nombre && usuario.email && usuario.rol) {
            console.log('Datos del usuario encontrados:', usuario);
            userNameElement.textContent = `Usuario: ${usuario.nombre}`;
            userEmailElement.textContent = `Correo: ${usuario.email}`;
            userRolElement.textContent = `Rol: ${usuario.rol}`;
        } else {
            console.log('Datos del usuario no disponibles');
            userNameElement.textContent = 'Usuario: No disponible';
            userEmailElement.textContent = 'Correo: No disponible';
            userRolElement.textContent = 'Rol: No disponible';
        }
    }

   // Función para confirmar el cierre de sesión con modal
function confirmLogout() {
    const modal = document.getElementById('logoutModal');
    if (!modal) {
        console.error('Modal de logout no encontrado');
        return;
    }
    
    // Mostrar modal
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    // Configurar eventos de cierre
    const closeModal = function() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    };
    
    // Cerrar al hacer clic en la X
    document.querySelector('.close-modal-logout').onclick = closeModal;
    
    // Cerrar al hacer clic en Cancelar
    document.getElementById('cancelLogoutBtn').onclick = closeModal;
    
    // Confirmar cierre de sesión
    document.getElementById('confirmLogoutBtn').onclick = function() {
        closeModal();
        // Redirigir al script de cierre de sesión
        window.location.href = 'logout.php';
    };
    
    // Cerrar al hacer clic fuera del modal
    window.onclick = function(event) {
        if (event.target === modal) {
            closeModal();
        }
    };
}

// Llamar a la función cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    actualizarDatosUsuario(); // Actualizar datos del usuario

    // Configurar el botón de cierre de sesión
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            confirmLogout();
        });
    }
    
    // Configurar otros eventos (tema, menú, etc.)
    // ... (mantén el resto de tu código existente)
});
</script>
</body>
</html>