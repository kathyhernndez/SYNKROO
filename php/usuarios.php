<?php
session_start();
include 'conexion_be.php';
include 'registrar_accion.php';
include 'verificar_almacenamiento.php';

/// plantillas front
include 'barra_izquierda.php';
include 'barra_superior.php';



// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo '
    <script>
    alert("Por Favor debes Iniciar Sesion");
    window.location = "../index.php";
    </script>
    ';
    session_destroy();
    die();
}

// Verificar si el usuario tiene el rol adecuado (id_roles igual a 1 o 2)
if (!isset($_SESSION['id_roles']) || ($_SESSION['id_roles'] != 1 && $_SESSION['id_roles'] != 2)) {
    echo '
    <script>
    alert("No tienes permisos para acceder a esta vista");
    window.location = "menu.php";
    </script>
    ';
    die();
}



// Verificar si hay un mensaje de sesión y mostrarlo
if (isset($_SESSION['message'])) {
    echo '
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        ' . $_SESSION['message'] . '
    </div>';
    unset($_SESSION['message']);
}

if (isset($message)) { 
    echo ' 
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
     ' . $message . ' 
    </div> 
    '; 
}
?>


    <title>Gestión Usuarios</title>
    
<body>
    <style>
        /* Estilos para el botón de filtrado */
.filter-btn {
    background-color: #ff8c42; /* Fondo naranja */
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-btn i {
    font-size: 18px;
}

/* Estilos para el menú desplegable de filtrado */
.filter-dropdown {
    display: none; /* Oculto por defecto */
    position: absolute;
    background-color: #f5f5f5; /* Fondo blanco hueso */
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    margin-top: 10px;
}

/* Estilos para los grupos de filtros */
.filter-group {
    margin-bottom: 10px;
}

.filter-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333; /* Letras negras en modo claro */
}

/* Estilos para los selects */
.filter-select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 5px; /* Borde redondeado */
    font-size: 14px;
    background-color: white; /* Fondo blanco */
    color: #333; /* Letras negras */
    cursor: pointer;
}

/* Estilos para el botón de aplicar filtros */
.apply-filter-btn {
    background-color: #ff8c42; /* Fondo naranja */
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    width: 100%;
    margin-top: 10px;
}

.apply-filter-btn:hover {
    background-color: #e67e22; /* Naranja más oscuro al pasar el mouse */
}
/* Estilos para el modo oscuro */
body.dark-mode .filter-dropdown {
    background-color: #333; /* Fondo negro en modo oscuro */
    border-color: #555;
}

body.dark-mode .filter-group label {
    color: #fff; /* Letras blancas en modo oscuro */
}

body.dark-mode .filter-select {
    background-color: #444; /* Fondo oscuro para selects */
    color: #fff; /* Letras blancas */
    border-color: #555;
}

body.dark-mode .apply-filter-btn {
    background-color: #e67e22; /* Naranja más oscuro en modo oscuro */
}

/* Estilos para el modal de confirmación */
#confirm-modal {
    display: none;
    position: fixed;
    z-index: 1001;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
}

#confirm-modal .modal-content {
    background-color: #f5f5f5;
    padding: 20px;
    border-radius: 10px;
    width: 300px;
    text-align: center;
}

#confirm-modal .close-modal {
    float: right;
    cursor: pointer;
    font-size: 20px;
}

#confirm-modal .password-container {
    position: relative;
    margin-bottom: 15px;
}

#confirm-modal .password-container input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

#confirm-modal .toggle-password-btn {
    position: absolute;
    right: 10px;
    top: 70%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: #ff8c42;
}

#confirm-modal .confirm-btn {
    background-color: #ff8c42;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
}

#confirm-modal .confirm-btn:hover {
    background-color: #e67e22;
}

/* Estilos para modo oscuro */
body.dark-mode #confirm-modal .modal-content {
    background-color: #333;
    color: white;
}

body.dark-mode #confirm-modal .password-container input {
    background-color: #444;
    color: white;
    border-color: #555;
}

body.dark-mode #confirm-modal .toggle-password-btn {
    color: #e67e22;
}


/** estilos registro usuarios **/
/* Estilos para el modal de usuario */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: #f5f5f5;
    padding: 20px;
    border-radius: 10px;
    width: 500px;
    max-width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.close-modal {
    float: right;
    cursor: pointer;
    font-size: 20px;
}

/* Estilos para el formulario */
#user-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

#user-form label {
    font-weight: bold;
    margin-bottom: 5px;
}

#user-form input,
#user-form select {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
    width: 100%;
}

#user-form button[type="submit"] {
    background-color: #ff8c42;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 10px;
}

#user-form button[type="submit"]:hover {
    background-color: #e67e22;
}

.error-message {
    padding: 10px;
    border-radius: 5px;
    margin-top: 10px;
    display: none;
}

/* Estilos para modo oscuro */
body.dark-mode .modal-content {
    background-color: #333;
    color: white;
}

body.dark-mode #user-form input,
body.dark-mode #user-form select {
    background-color: #444;
    color: white;
    border-color: #555;
}

body.dark-mode #user-form button[type="submit"] {
    background-color: #e67e22;
}

body.dark-mode #user-form button[type="submit"]:hover {
    background-color: #d35400;
}

/* Estilos para el indicador de fortaleza de contraseña */
.password-strength-container {
    margin-top: 5px;
}

.password-strength-bar {
    width: 100%;
    height: 5px;
    background-color: #e0e0e0;
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 3px;
}

.password-strength-progress {
    height: 100%;
    width: 0%;
    transition: width 0.3s ease, background-color 0.3s ease;
}

/* Colores para diferentes niveles de fortaleza */
.password-very-weak {
    background-color: #ff3333;
    width: 25%;
}

.password-weak {
    background-color: #ff9933;
    width: 50%;
}

.password-medium {
    background-color: #ffcc33;
    width: 75%;
}

.password-strong {
    background-color: #33cc33;
    width: 100%;
}

/* Estilos para modo oscuro */
body.dark-mode .password-strength-bar {
    background-color: #444;
}

/* Estilos para mensajes de error en campos */
.field-error {
    color: #ff3333;
    font-size: 12px;
    margin-top: 5px;
    display: none;
}

/* Resaltar campos con error */
input.error, select.error {
    border-color: #ff3333 !important;
}

/* Estilos para modo oscuro */
body.dark-mode .field-error {
    color: #ff6666;
}

/* Nuevos estilos para el formulario reorganizado */
.form-row {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
}

.form-group {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.form-group.full-width {
    flex: 0 0 100%;
}

.form-group label {
    font-weight: bold;
    margin-bottom: 5px;
    color: #333;
}

.form-group input,
.form-group select {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
    width: 100%;
}

/* Ajustes para modo oscuro */
body.dark-mode .form-group label {
    color: #fff;
}

body.dark-mode .form-group input,
body.dark-mode .form-group select {
    background-color: #444;
    color: white;
    border-color: #555;
}

/* Ajustes para pantallas pequeñas */
@media (max-width: 600px) {
    .form-row {
        flex-direction: column;
        gap: 10px;
    }
    
    .modal-content {
        width: 90%;
        padding: 15px;
    }
}

/* Estilo para el botón de guardar */
#user-form button[type="submit"] {
    background-color: #ff8c42;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 15px;
    width: 100%;
    transition: background-color 0.3s;
}

#user-form button[type="submit"]:hover {
    background-color: #e67e22;
}

/* Mensajes de error */
.field-error {
    color: #ff3333;
    font-size: 12px;
    margin-top: 5px;
    display: none;
}

input.error, select.error {
    border-color: #ff3333 !important;
}

body.dark-mode .field-error {
    color: #ff6666;
}

/* Estilos para el botón de guardar en el formulario de edición */
#edit-user-form button[type="submit"] {
    background-color: #ff8c42;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 15px;
    width: 100%;
    transition: background-color 0.3s;
}

#edit-user-form button[type="submit"]:hover {
    background-color: #e67e22;
}

/* Estilos para modo oscuro */
body.dark-mode #edit-user-form button[type="submit"] {
    background-color: #e67e22;
}

body.dark-mode #edit-user-form button[type="submit"]:hover {
    background-color: #d35400;
}

/* Estilos para mensajes de éxito/error */
.error-message {
    padding: 12px;
    border-radius: 5px;
    margin-top: 15px;
    display: none;
    font-size: 14px;
    text-align: center;
}

/* Mensajes de error */
/* Para hacer los mensajes más modernos */
.error-message {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

/* Efecto pulsante para acciones exitosas */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}

.error-message.success {
    animation: pulse 1.5s infinite;
}

/* Mensajes de éxito */
.error-message.success {
    background-color: #eeffee;
    color: #33cc33;
    border: 1px solid #ccffcc;
}

/* Estilos para modo oscuro */
body.dark-mode .error-message {
    background-color: #442222;
    color: #ff6666;
    border-color: #663333;
}

body.dark-mode .error-message.success {
    background-color: #224422;
    color: #77dd77;
    border-color: #336633;
}


    </style>
    <div class="dashboard">
         <!-- Barra lateral izquierda (menú) -->
        

        <!-- Contenido principal -->
         
         <!-- Barra superior -->
        <div class="main-content">

            <!-- Contenido del dashboard -->
            <div class="content">
               
                <!-- Contenedor de usuarios -->
                <div class="files-container">
                    <!-- Filtros y barra de búsqueda -->
                    <div class="filters">

                    <div class="filters">
    <button class="filter-btn" id="filter-btn">
        <i class="fas fa-filter"></i> Filtrar
    </button>
    <!-- Menú desplegable de filtrado -->
    <div class="filter-dropdown" id="filter-dropdown">
        <!-- Filtro por Rol -->
        <div class="filter-group">
            <label>Rol:</label>
            <select id="filter-rol" class="filter-select">
                <option value="">Todos</option>
                <option value="2">SuperAdmin</option>
                <option value="1">Admin</option>
                <option value="3">Personal</option>
            </select>
        </div>
        <!-- Filtro por Estado -->
        <div class="filter-group">
            <label>Estado:</label>
            <select id="filter-estado" class="filter-select">
                <option value="">Todos</option>
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
            </select>
        </div>
        <!-- Botón para aplicar filtros -->
        <button class="apply-filter-btn" id="apply-filter-btn">Aplicar Filtros</button>
    </div>
</div>

        <!-- Barra de búsqueda -->
        <input type="text" id="search-bar" class="search-bar" placeholder="Buscar por nombre, apellido o correo...">

        
    <!-- Botón para crear usuario -->
    <button class="create-user-btn" id="create-user-btn"><i class="fas fa-user-plus"></i> Crear Usuario</button>
</div>

                    
<table class="user-table">
    <thead>
        <tr>
            <th>Id</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Cédula</th>
            <th>Correo</th>
            <th>Estado</th>
            <th>Nivel Acceso</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
       <?php 
                include 'get_usuarios.php';
                ?>
    </tbody>
</table>

                    
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación para eliminar usuario -->
<div class="modal" id="delete-modal">
    <div class="modal-content">
        <span class="close-modal" id="close-delete-modal">&times;</span>
        <h2>Confirmar Eliminación</h2>
        <p id="delete-modal-message">¿Estás seguro de que deseas eliminar este usuario?</p>
        <div class="password-container">
            <label for="delete-password">Contraseña:</label>
            <input type="password" id="delete-password" placeholder="Ingrese su contraseña" required>
            <button id="toggle-delete-password" class="toggle-password-btn">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        <div id="delete-error-message" class="error-message" style="display: none;"></div>
        <button id="confirm-delete-btn" class='btn-eliminar'> <i class='fas fa-trash-alt'></i> Eliminar</button>
    </div>
</div>

    <!-- Modal de confirmación para bloquear/desbloquear usuario -->
<div class="modal" id="confirm-modal">
    <div class="modal-content">
        <span class="close-modal" id="close-confirm-modal">&times;</span>
        <h2 id="confirm-modal-title">Confirmar Acción</h2>
        <p id="confirm-modal-message">¿Estás seguro de que deseas realizar esta acción?</p>
        <div class="password-container">
            <label for="confirm-password">Contraseña:</label>
            <input type="password" id="confirm-password" placeholder="Ingrese su contraseña" required>
            <button id="toggle-password" class="toggle-password-btn">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        <button id="confirm-action-btn" class="confirm-btn">Confirmar</button>
    </div>
</div>

<!-- Modal para crear usuarios -->
<div class="modal" id="user-modal">
    <div class="modal-content">
        <span class="close-modal" id="close-user-modal">&times;</span>
        <h2 id="modal-title">Crear Usuario</h2>
        <form id="user-form">
            <input type="hidden" id="user-id" name="id">
            
            <!-- Fila 1: Nombre y Apellido -->
            <div class="form-row">
                <div class="form-group">
                    <label for="user-name">Nombre:</label>
                    <input type="text" id="user-name" name="nombre" placeholder="Ingrese el nombre" required>
                </div>
                <div class="form-group">
                    <label for="user-lastname">Apellido:</label>
                    <input type="text" id="user-lastname" name="apellido" placeholder="Ingrese el apellido" required>
                </div>
            </div>
            
            <!-- Fila 2: Correo y Cédula -->
            <div class="form-row">
                <div class="form-group">
                    <label for="user-email">Correo:</label>
                    <input type="email" id="user-email" name="correo" placeholder="Ingrese el correo" required>
                </div>
                <div class="form-group">
                    <label for="user-cedula">Cédula:</label>
                    <input type="text" id="user-cedula" name="cedula" placeholder="Ingrese la cédula" required>
                </div>
            </div>
            
            <!-- Fila 3: Contraseña y Confirmación -->
            <div class="form-row">
                <div class="form-group">
                    <label for="user-password">Contraseña:</label>
                    <input type="password" id="user-password" name="clave" placeholder="Ingrese la contraseña">
                    <div class="password-strength-container">
                        <div class="password-strength-bar">
                            <div class="password-strength-progress" id="password-strength-progress"></div>
                        </div>
                        <small id="password-strength-text">Seguridad: Muy débil</small>
                    </div>
                </div>
                <div class="form-group">
                    <label for="user-confirm-password">Confirmar Contraseña:</label>
                    <input type="password" id="user-confirm-password" name="confirmar_clave" placeholder="Confirme la contraseña">
                </div>
            </div>
            
            <!-- Fila 4: Rol -->
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="user-rol">Rol:</label>
                    <select id="user-rol" name="rol" required>
                        <option value="1">Admin</option>
                        <option value="3">Personal</option>
                    </select>
                </div>
            </div>
            
            <!-- Preguntas de seguridad - Fila 5: Pregunta 1 y Respuesta 1 -->
            <div class="form-row">
                <div class="form-group">
                    <label for="pregunta_1">Pregunta de seguridad 1:</label>
                    <select name="pregunta_1" id="pregunta_1" required>
                    <option value="">Selecciona una pregunta de seguridad</option>
                <option value="¿Cual es el nombre de tu primera mascota?">¿Cuál es el nombre de tu primera mascota?</option>
                <option value="¿En qué ciudad conociste a tu mejor amigo/a?">¿En qué ciudad conociste a tu mejor amigo/a?</option>
                <option value="¿Cual es el nombre de tu profesor/a favorito/a de la escuela?">¿Cuál es el nombre de tu profesor/a favorito/a de la escuela?</option>
                <option value="¿Cual es el segundo nombre de tu madre o padre?">¿Cuál es el segundo nombre de tu madre o padre?</option>
                <option value="¿Cual fue el primer automovil que condujiste o tuviste?">¿Cuál fue el primer automóvil que condujiste o tuviste?</option>
                <option value="¿Cual es el nombre de la calle donde creciste?">¿Cuál es el nombre de la calle donde creciste?</option>
                <option value="¿Cual es tu comida o restaurante favorito?">¿Cuál es tu comida o restaurante favorito?</option>
                <option value="¿Cual es el nombre de tu personaje historico o ficticio favorito?">¿Cuál es el nombre de tu personaje histórico o ficticio favorito?</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="respuesta_1">Respuesta 1:</label>
                    <input type="text" id="respuesta_1" name="respuesta_1" placeholder="Ingresa una respuesta" required>
                </div>
            </div>
            
            <!-- Fila 6: Pregunta 2 y Respuesta 2 -->
            <div class="form-row">
                <div class="form-group">
                    <label for="pregunta_2">Pregunta de seguridad 2:</label>
                    <select name="pregunta_2" id="pregunta_2" required>
                    <option value="">Selecciona una pregunta de seguridad</option>
                <option value="¿Cual es el nombre de tu primera mascota?">¿Cuál es el nombre de tu primera mascota?</option>
                <option value="¿En qué ciudad conociste a tu mejor amigo/a?">¿En qué ciudad conociste a tu mejor amigo/a?</option>
                <option value="¿Cual es el nombre de tu profesor/a favorito/a de la escuela?">¿Cuál es el nombre de tu profesor/a favorito/a de la escuela?</option>
                <option value="¿Cual es el segundo nombre de tu madre o padre?">¿Cuál es el segundo nombre de tu madre o padre?</option>
                <option value="¿Cual fue el primer automovil que condujiste o tuviste?">¿Cuál fue el primer automóvil que condujiste o tuviste?</option>
                <option value="¿Cual es el nombre de la calle donde creciste?">¿Cuál es el nombre de la calle donde creciste?</option>
                <option value="¿Cual es tu comida o restaurante favorito?">¿Cuál es tu comida o restaurante favorito?</option>
                <option value="¿Cual es el nombre de tu personaje historico o ficticio favorito?">¿Cuál es el nombre de tu personaje histórico o ficticio favorito?</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="respuesta_2">Respuesta 2:</label>
                    <input type="text" id="respuesta_2" name="respuesta_2" placeholder="Ingresa una respuesta" required>
                </div>
            </div>
            
            <!-- Fila 7: Pregunta 3 y Respuesta 3 -->
            <div class="form-row">
                <div class="form-group">
                    <label for="pregunta_3">Pregunta de seguridad 3:</label>
                    <select name="pregunta_3" id="pregunta_3" required>
                    <option value="">Selecciona una pregunta de seguridad</option>
                <option value="¿Cual es el nombre de tu primera mascota?">¿Cuál es el nombre de tu primera mascota?</option>
                <option value="¿En qué ciudad conociste a tu mejor amigo/a?">¿En qué ciudad conociste a tu mejor amigo/a?</option>
                <option value="¿Cual es el nombre de tu profesor/a favorito/a de la escuela?">¿Cuál es el nombre de tu profesor/a favorito/a de la escuela?</option>
                <option value="¿Cual es el segundo nombre de tu madre o padre?">¿Cuál es el segundo nombre de tu madre o padre?</option>
                <option value="¿Cual fue el primer automovil que condujiste o tuviste?">¿Cuál fue el primer automóvil que condujiste o tuviste?</option>
                <option value="¿Cual es el nombre de la calle donde creciste?">¿Cuál es el nombre de la calle donde creciste?</option>
                <option value="¿Cual es tu comida o restaurante favorito?">¿Cuál es tu comida o restaurante favorito?</option>
                <option value="¿Cual es el nombre de tu personaje historico o ficticio favorito?">¿Cuál es el nombre de tu personaje histórico o ficticio favorito?</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="respuesta_3">Respuesta 3:</label>
                    <input type="text" id="respuesta_3" name="respuesta_3" placeholder="Ingresa una respuesta" required>
                </div>
            </div>
            
            <div id="user-error-message" class="error-message" style="display: none;"></div>
            <button type="submit">Guardar</button>
        </form>
    </div>
</div>


<!-- Modal para editar usuarios -->
<div class="modal" id="edit-modal">
    <div class="modal-content">
        <span class="close-modal" id="close-edit-modal">&times;</span>
        <h2>Editar Usuario</h2>
        <form id="edit-user-form">
            <input type="hidden" id="edit-user-id" name="id">
            
            <!-- Fila 1: Nombre y Apellido -->
            <div class="form-row">
                <div class="form-group">
                    <label for="edit-user-name">Nombre:</label>
                    <input type="text" id="edit-user-name" name="nombre" placeholder="Ingrese el nombre" required>
                </div>
                <div class="form-group">
                    <label for="edit-user-lastname">Apellido:</label>
                    <input type="text" id="edit-user-lastname" name="apellido" placeholder="Ingrese el apellido" required>
                </div>
            </div>
            
            <!-- Fila 2: Correo y Cédula -->
            <div class="form-row">
                <div class="form-group">
                    <label for="edit-user-email">Correo:</label>
                    <input type="email" id="edit-user-email" name="correo" placeholder="Ingrese el correo" required>
                </div>
                <div class="form-group">
                    <label for="edit-user-cedula">Cédula:</label>
                    <input type="text" id="edit-user-cedula" name="cedula" placeholder="Ingrese la cédula" required>
                </div>
            </div>
            
            <!-- Fila 3: Contraseña y Confirmación -->
            <div class="form-row">
                <div class="form-group">
                    <label for="edit-user-password">Nueva Contraseña:</label>
                    <input type="password" id="edit-user-password" name="clave" placeholder="Dejar vacío para no cambiar">
                    <div class="password-strength-container">
                        <div class="password-strength-bar">
                            <div class="password-strength-progress" id="edit-password-strength-progress"></div>
                        </div>
                        <small id="edit-password-strength-text">Seguridad: -</small>
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit-user-confirm-password">Confirmar Contraseña:</label>
                    <input type="password" id="edit-user-confirm-password" name="confirmar_clave" placeholder="Confirme la nueva contraseña">
                </div>
            </div>
            
            <div id="edit-user-error-message" class="error-message" style="display: none;"></div>
            <button type="submit">Guardar Cambios</button>
        </form>
    </div>
</div>

<!-- Modal para backup -->
<?php include 'modal_backup.php'; ?>

<!-- Modal para cierre_sesion -->
<?php include 'confirmar_cierre.php'; ?>

<!--- footer -->
<?php include 'footer.php'; ?>

<!---JavaScript -->
<script>
function cambiarEstadoUsuario(idUsuario, estadoActual) {
    // Solicitar la contraseña del administrador para confirmar la acción
    const contrasena = prompt("Por favor, ingrese su contraseña para confirmar el cambio de estado:");

    if (contrasena === null || contrasena.trim() === "") {
        alert("Debe ingresar una contraseña para continuar.");
        return;
    }

    // Enviar la solicitud al servidor
    fetch('cambiar_estado_usuario.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            idUsuario: idUsuario,
            estadoActual: estadoActual,
            contrasena: contrasena
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload(); // Recargar la página para reflejar el cambio
        } else {
            alert(data.message); // Mostrar mensaje de error
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Ocurrió un error al cambiar el estado del usuario.");
    });
}

// Variable global para almacenar el ID del usuario a eliminar
let usuarioAEliminarId = null;

// Función para mostrar el modal de eliminación
function mostrarModalEliminar(idUsuario) {
    usuarioAEliminarId = idUsuario;
    const deleteModal = document.getElementById('delete-modal');
    const deleteMessage = document.getElementById('delete-modal-message');
    const deletePasswordInput = document.getElementById('delete-password');
    const toggleDeletePasswordBtn = document.getElementById('toggle-delete-password');
    const deleteErrorMessage = document.getElementById('delete-error-message');

    // Resetear el modal
    deletePasswordInput.value = '';
    deleteErrorMessage.style.display = 'none';
    
    // Mostrar el modal
    deleteModal.style.display = 'flex';

    // Configurar el botón de mostrar/ocultar contraseña
    toggleDeletePasswordBtn.onclick = function() {
        const type = deletePasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        deletePasswordInput.setAttribute('type', type);
        toggleDeletePasswordBtn.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    };

    // Configurar el botón de confirmación
    document.getElementById('confirm-delete-btn').onclick = function() {
        const contrasena = deletePasswordInput.value.trim();

        if (!contrasena) {
            showDeleteErrorMessage('Debe ingresar su contraseña para continuar');
            return;
        }

        eliminarUsuario(usuarioAEliminarId, contrasena);
    };

    // Cerrar el modal al hacer clic en la X
    document.getElementById('close-delete-modal').onclick = function() {
        deleteModal.style.display = 'none';
        usuarioAEliminarId = null;
    };

    // Cerrar el modal al hacer clic fuera
    window.onclick = function(event) {
        if (event.target === deleteModal) {
            deleteModal.style.display = 'none';
            usuarioAEliminarId = null;
        }
    };
}

// Función para mostrar mensajes de error en el modal de eliminación
function showDeleteErrorMessage(message) {
    const errorMessage = document.getElementById('delete-error-message');
    errorMessage.textContent = message;
    errorMessage.style.display = 'block';
    errorMessage.className = 'error-message'; // Estilo rojo
}

// Función para mostrar mensajes de éxito en el modal de eliminación
function showDeleteSuccessMessage(message) {
    const errorMessage = document.getElementById('delete-error-message');
    errorMessage.textContent = message;
    errorMessage.style.display = 'block';
    errorMessage.className = 'error-message success'; // Estilo verde
}

// Función para eliminar usuario con validación de permisos
function eliminarUsuario(idUsuario, contrasena) {
    // Mostrar spinner de carga
    const deleteModal = document.getElementById('delete-modal');
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'loading-overlay';
    loadingOverlay.innerHTML = `
        <div class="loading-spinner"></div>
        <div class="loading-text">Procesando...</div>
    `;
    deleteModal.querySelector('.modal-content').appendChild(loadingOverlay);

    // Enviar la solicitud al servidor
    fetch('eliminar_usuario.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id: idUsuario,
            contrasena: contrasena
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showDeleteSuccessMessage(data.message);
            setTimeout(() => {
                deleteModal.style.display = 'none';
                window.location.reload(); // Recargar la página para reflejar el cambio
            }, 1500);
        } else {
            showDeleteErrorMessage(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showDeleteErrorMessage('Ocurrió un error al eliminar el usuario.');
    })
    .finally(() => {
        loadingOverlay.remove();
    });
}



document.addEventListener('DOMContentLoaded', function () {
    const filterBtn = document.getElementById('filter-btn');
    const filterDropdown = document.getElementById('filter-dropdown');
    const applyFilterBtn = document.getElementById('apply-filter-btn');
    const filterRol = document.getElementById('filter-rol');
    const filterEstado = document.getElementById('filter-estado');
    const searchBar = document.getElementById('search-bar');

    filterBtn.addEventListener('click', function (event) {
        event.stopPropagation();
        filterDropdown.style.display = filterDropdown.style.display === 'block' ? 'none' : 'block';
    });

    document.addEventListener('click', function (event) {
        if (!filterBtn.contains(event.target) && !filterDropdown.contains(event.target)) {
            filterDropdown.style.display = 'none';
        }
    });

    applyFilterBtn.addEventListener('click', function () {
        cargarUsuarios();
        filterDropdown.style.display = 'none';
    });

    function cargarUsuarios() {
        const searchTerm = searchBar.value;
        const rol = filterRol.value;
        const estado = filterEstado.value;

        fetch(`get_usuarios.php?search=${encodeURIComponent(searchTerm)}&rol=${rol}&estado=${estado}`)
            .then(response => response.text())
            .then(data => {
                document.querySelector('.user-table tbody').innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    cargarUsuarios();
    searchBar.addEventListener('input', cargarUsuarios);
});

// Función para mostrar el modal de confirmación
function mostrarModalConfirmacion(idUsuario, estadoActual) {
    const confirmModal = document.getElementById('confirm-modal');
    const confirmMessage = document.getElementById('confirm-modal-message');
    const confirmActionBtn = document.getElementById('confirm-action-btn');
    const confirmPasswordInput = document.getElementById('confirm-password');
    const togglePasswordBtn = document.getElementById('toggle-password');

    // Configurar el mensaje del modal
    confirmMessage.textContent = estadoActual === 1 
        ? "¿Estás seguro de que deseas bloquear este usuario?" 
        : "¿Estás seguro de que deseas desbloquear este usuario?";

    // Mostrar el modal
    confirmModal.style.display = 'flex';

    // Configurar el botón de confirmación
    confirmActionBtn.onclick = function () {
        const contrasena = confirmPasswordInput.value;

        if (!contrasena) {
            alert("Debe ingresar una contraseña para continuar.");
            return;
        }

        // Enviar la solicitud al servidor
        fetch('cambiar_estado_usuario.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                idUsuario: idUsuario,
                estadoActual: estadoActual,
                contrasena: contrasena
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload(); // Recargar la página para reflejar el cambio
            } else {
                alert(data.message); // Mostrar mensaje de error
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Ocurrió un error al cambiar el estado del usuario.");
        });

        // Cerrar el modal
        confirmModal.style.display = 'none';
    };

    // Botón para mostrar/ocultar contraseña
    togglePasswordBtn.onclick = function () {
        const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPasswordInput.setAttribute('type', type);
        togglePasswordBtn.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    };

    // Cerrar el modal al hacer clic en la X
    document.getElementById('close-confirm-modal').onclick = function () {
        confirmModal.style.display = 'none';
    };

    // Cerrar el modal al hacer clic fuera del modal
    window.onclick = function (event) {
        if (event.target === confirmModal) {
            confirmModal.style.display = 'none';
        }
    };
}

// Modal de creación de usuarios
const createUserBtn = document.getElementById('create-user-btn');
const userModal = document.getElementById('user-modal');
const closeUserModal = document.getElementById('close-user-modal');
const userForm = document.getElementById('user-form');
const errorMessage = document.getElementById('user-error-message');

// Mostrar modal de creación de usuarios
createUserBtn.addEventListener('click', () => {
    userModal.style.display = 'flex';
    userForm.reset();
    errorMessage.style.display = 'none';
});

// Cerrar modal de creación de usuarios
closeUserModal.addEventListener('click', () => {
    userModal.style.display = 'none';
});

// Reemplaza todo el código JavaScript del formulario con este:

// Manejar envío del formulario de usuario
userForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Mostrar spinner de carga
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'loading-overlay';
    loadingOverlay.innerHTML = `
        <div class="loading-spinner"></div>
        <div class="loading-text">Procesando...</div>
    `;
    userModal.querySelector('.modal-content').appendChild(loadingOverlay);
    
    // Obtener valores del formulario
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    // Verificar que las preguntas están presentes
    if (!data.pregunta_1 || !data.pregunta_2 || !data.pregunta_3) {
        showErrorMessage('Debe seleccionar las tres preguntas de seguridad');
        return;
    }

    // Validar campos requeridos
    const requiredFields = ['nombre', 'apellido', 'correo', 'cedula', 'rol', 
                          'pregunta_1', 'pregunta_2', 'pregunta_3',
                          'respuesta_1', 'respuesta_2', 'respuesta_3'];
    
    const missingFields = requiredFields.filter(field => !data[field]);
    if (missingFields.length > 0) {
        loadingOverlay.remove();
        showErrorMessage(`Los siguientes campos son requeridos: ${missingFields.join(', ')}`);
        return;
    }
    
    // Validar preguntas de seguridad
    const preguntas = [data.pregunta_1, data.pregunta_2, data.pregunta_3];
    if (new Set(preguntas).size !== 3) {
        loadingOverlay.remove();
        showErrorMessage('Las preguntas de seguridad deben ser diferentes entre sí.');
        return;
    }
    
    // Validar respuestas de seguridad
    const respuestas = [data.respuesta_1, data.respuesta_2, data.respuesta_3];
    if (new Set(respuestas).size !== 3) {
        loadingOverlay.remove();
        showErrorMessage('Las respuestas de seguridad deben ser diferentes entre sí.');
        return;
    }
    
    // Validar contraseña si es nuevo usuario
    if (!data.id && (!data.clave || !data.confirmar_clave)) {
        loadingOverlay.remove();
        showErrorMessage('La contraseña es requerida para nuevos usuarios.');
        return;
    }
    
    // Validar coincidencia de contraseñas
    if (data.clave && data.clave !== data.confirmar_clave) {
        loadingOverlay.remove();
        showErrorMessage('Las contraseñas no coinciden.');
        return;
    }
    
    // Validar longitud de contraseña
    if (data.clave && data.clave.length < 16) {
        loadingOverlay.remove();
        showErrorMessage('La contraseña debe tener al menos 16 caracteres.');
        return;
    }
    
    // Validar cédula
    if (!/^\d{6,12}$/.test(data.cedula)) {
        loadingOverlay.remove();
        showErrorMessage('La cédula debe tener entre 6 y 12 dígitos y solo puede contener números.');
        return;
    }
    
    // Enviar datos al servidor
    fetch('registro_usuario_be.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        // Verificar si la respuesta es JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            return response.text().then(text => {
                throw new Error(`Respuesta no JSON: ${text.substring(0, 100)}...`);
            });
        }
        return response.json();
    })
    .then(data => {
        loadingOverlay.remove();
        if (data.success) {
            showSuccessMessage(data.message);
            setTimeout(() => {
                userModal.style.display = 'none';
                window.location.reload();
            }, 2000);
        } else {
            showErrorMessage(data.message || 'Error desconocido al registrar usuario');
        }
    })
    .catch(error => {
        loadingOverlay.remove();
        console.error('Error detallado:', error);
        let errorMsg = 'Ocurrió un error al procesar la solicitud.';
        
        if (error instanceof TypeError) {
            errorMsg = 'Error de conexión. Verifica tu conexión a internet.';
        } else if (error.message) {
            errorMsg = error.message;
        }
        
        showErrorMessage(errorMsg);
        
        console.group('Detalles del error');
        console.error('URL:', 'registro_usuario_be.php');
        console.error('Método:', 'POST');
        console.error('Datos enviados:', data);
        console.error('Error completo:', error);
        console.groupEnd();
    });
});

// Función para mostrar mensajes de error
function showErrorMessage(message) {
    errorMessage.textContent = message;
    errorMessage.style.display = 'block';
    errorMessage.style.color = '#ff3333'; // Rojo para errores
}

// Función para mostrar mensajes de éxito
function showSuccessMessage(message) {
    errorMessage.textContent = message;
    errorMessage.style.display = 'block';
    errorMessage.style.color = '#33cc33'; // Verde para éxito
}

// Función para evaluar la fortaleza de la contraseña
function checkPasswordStrength(password) {
    let strength = 0;
    
    // Longitud mínima
    if (password.length >= 16) strength += 1;
    
    // Contiene letras mayúsculas y minúsculas
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 1;
    
    // Contiene números
    if (/\d/.test(password)) strength += 1;
    
    // Contiene caracteres especiales
    if (/[^a-zA-Z0-9]/.test(password)) strength += 1;
    
    return strength;
}

// Event listener para el campo de contraseña
document.getElementById('user-password').addEventListener('input', function(e) {
    const password = e.target.value;
    const strength = checkPasswordStrength(password);
    const progressBar = document.getElementById('password-strength-progress');
    const strengthText = document.getElementById('password-strength-text');
    
    // Resetear clases
    progressBar.className = 'password-strength-progress';
    
    if (password.length === 0) {
        progressBar.style.width = '0%';
        strengthText.textContent = 'Seguridad: -';
        return;
    }
    
    // Actualizar barra y texto según fortaleza
    if (strength === 0) {
        progressBar.classList.add('password-very-weak');
        strengthText.textContent = 'Seguridad: Muy débil';
    } else if (strength === 1) {
        progressBar.classList.add('password-weak');
        strengthText.textContent = 'Seguridad: Débil';
    } else if (strength === 2 || strength === 3) {
        progressBar.classList.add('password-medium');
        strengthText.textContent = 'Seguridad: Media';
    } else {
        progressBar.classList.add('password-strong');
        strengthText.textContent = 'Seguridad: Fuerte';
    }
});

// Función para validar cédula en tiempo real
document.getElementById('user-cedula').addEventListener('input', function(e) {
    const cedula = e.target.value;
    const errorElement = document.getElementById('cedula-error') || createErrorElement(e.target, 'cedula-error');
    
    if (!/^\d{0,12}$/.test(cedula)) {
        showFieldError(errorElement, 'La cédula solo puede contener números (máx. 12)');
    } else if (cedula.length > 0 && cedula.length < 6) {
        showFieldError(errorElement, 'La cédula debe tener al menos 6 dígitos');
    } else {
        clearFieldError(errorElement);
    }
});

// Función para validar email en tiempo real
document.getElementById('user-email').addEventListener('input', function(e) {
    const email = e.target.value;
    const errorElement = document.getElementById('email-error') || createErrorElement(e.target, 'email-error');
    
    if (email.length > 0 && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showFieldError(errorElement, 'Ingrese un correo electrónico válido');
    } else {
        clearFieldError(errorElement);
    }
});

// Función para validar coincidencia de contraseñas en tiempo real
document.getElementById('user-confirm-password').addEventListener('input', function(e) {
    const password = document.getElementById('user-password').value;
    const confirmPassword = e.target.value;
    const errorElement = document.getElementById('confirm-password-error') || createErrorElement(e.target, 'confirm-password-error');
    
    if (confirmPassword.length > 0 && password !== confirmPassword) {
        showFieldError(errorElement, 'Las contraseñas no coinciden');
    } else {
        clearFieldError(errorElement);
    }
});

// Funciones auxiliares para manejo de errores
function createErrorElement(inputElement, id) {
    const errorElement = document.createElement('div');
    errorElement.id = id;
    errorElement.className = 'field-error';
    inputElement.parentNode.insertBefore(errorElement, inputElement.nextSibling);
    return errorElement;
}

function showFieldError(element, message) {
    element.textContent = message;
    element.style.display = 'block';
}

function clearFieldError(element) {
    element.textContent = '';
    element.style.display = 'none';
}

// Función para validar preguntas de seguridad
function validateSecurityQuestions() {
    const pregunta1 = document.getElementById('pregunta_1').value;
    const pregunta2 = document.getElementById('pregunta_2').value;
    const pregunta3 = document.getElementById('pregunta_3').value;
    
    const errorElement = document.getElementById('questions-error') || 
        createErrorElement(document.getElementById('pregunta_3'), 'questions-error');
    
    if (pregunta1 && pregunta2 && pregunta3) {
        if (pregunta1 === pregunta2 || pregunta1 === pregunta3 || pregunta2 === pregunta3) {
            showFieldError(errorElement, 'Las preguntas de seguridad deben ser diferentes');
            return false;
        } else {
            clearFieldError(errorElement);
            return true;
        }
    }
    return true;
}

// Añadir event listeners a las preguntas
document.getElementById('pregunta_1').addEventListener('change', validateSecurityQuestions);
document.getElementById('pregunta_2').addEventListener('change', validateSecurityQuestions);
document.getElementById('pregunta_3').addEventListener('change', validateSecurityQuestions);

// Función para abrir el modal de edición con los datos del usuario
function abrirModalEdicion(idUsuario) {
    // Mostrar spinner de carga
    const editModal = document.getElementById('edit-modal');
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'loading-overlay';
    loadingOverlay.innerHTML = `
        <div class="loading-spinner"></div>
        <div class="loading-text">Cargando datos del usuario...</div>
    `;
    editModal.querySelector('.modal-content').appendChild(loadingOverlay);
    
    // Mostrar el modal
    editModal.style.display = 'flex';
    
    // Obtener datos del usuario
    fetch(`obtener_usuario.php?id=${idUsuario}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Llenar el formulario con los datos del usuario
                document.getElementById('edit-user-id').value = data.usuario.id;
                document.getElementById('edit-user-name').value = data.usuario.nombre;
                document.getElementById('edit-user-lastname').value = data.usuario.apellido;
                document.getElementById('edit-user-email').value = data.usuario.correo;
                document.getElementById('edit-user-cedula').value = data.usuario.cedula;
                
                // Limpiar campos de contraseña
                document.getElementById('edit-user-password').value = '';
                document.getElementById('edit-user-confirm-password').value = '';
                
                // Limpiar mensajes de error
                document.getElementById('edit-user-error-message').style.display = 'none';
            } else {
                alert('Error al cargar los datos del usuario: ' + data.message);
                editModal.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al cargar los datos del usuario');
            editModal.style.display = 'none';
        })
        .finally(() => {
            loadingOverlay.remove();
        });
}

// Manejar envío del formulario de edición
document.getElementById('edit-user-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Mostrar spinner de carga
    const editModal = document.getElementById('edit-modal');
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'loading-overlay';
    loadingOverlay.innerHTML = `
        <div class="loading-spinner"></div>
        <div class="loading-text">Actualizando usuario...</div>
    `;
    editModal.querySelector('.modal-content').appendChild(loadingOverlay);
    
    // Obtener valores del formulario como FormData
    const formData = new FormData(this);
    
    // Convertir FormData a objeto para validación
    const data = Object.fromEntries(formData.entries());
    const errorMessage = document.getElementById('edit-user-error-message');
    
    // Validaciones 
    // Validar campos requeridos
    const requiredFields = ['nombre', 'apellido', 'correo', 'cedula'];
    const missingFields = requiredFields.filter(field => !data[field]);
    
    if (missingFields.length > 0) {
        loadingOverlay.remove();
        showEditErrorMessage(`Los siguientes campos son requeridos: ${missingFields.join(', ')}`);
        return;
    }
    
    // Validar cédula
    if (!/^\d{6,12}$/.test(data.cedula)) {
        loadingOverlay.remove();
        showEditErrorMessage('La cédula debe tener entre 6 y 12 dígitos y solo puede contener números.');
        return;
    }
    
    // Validar email
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.correo)) {
        loadingOverlay.remove();
        showEditErrorMessage('Ingrese un correo electrónico válido.');
        return;
    }
    
    // Validar contraseña solo si se proporcionó
    if (data.clave) {
        // Validar longitud de contraseña
        if (data.clave.length < 16) {
            loadingOverlay.remove();
            showEditErrorMessage('La contraseña debe tener al menos 16 caracteres.');
            return;
        }
        
        // Validar coincidencia de contraseñas
        if (data.clave !== data.confirmar_clave) {
            loadingOverlay.remove();
            showEditErrorMessage('Las contraseñas no coinciden.');
            return;
        }
    }
    
    
    // Enviar datos al servidor como FormData
    fetch('editar_usuario.php', {
        method: 'POST',
        body: formData  // Envía como multipart/form-data
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showEditSuccessMessage(data.message);
            setTimeout(() => {
                editModal.style.display = 'none';
                window.location.reload();
            }, 1500);
        } else {
            showEditErrorMessage(data.message || 'Error desconocido al actualizar usuario');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showEditErrorMessage('Ocurrió un error al procesar la solicitud.');
    })
    .finally(() => {
        loadingOverlay.remove();
    });
});


// Función para mostrar mensajes de error en el modal de edición
function showEditErrorMessage(message) {
    const errorMessage = document.getElementById('edit-user-error-message');
    errorMessage.textContent = message;
    errorMessage.style.display = 'block';
    errorMessage.className = 'error-message'; // Remueve la clase success si existe
}

// Función para mostrar mensajes de éxito en el modal de edición
function showEditSuccessMessage(message) {
    const errorMessage = document.getElementById('edit-user-error-message');
    errorMessage.textContent = message;
    errorMessage.style.display = 'block';
    errorMessage.className = 'error-message success'; // Agrega clase success
}

// Cerrar modal de edición
document.getElementById('close-edit-modal').addEventListener('click', () => {
    document.getElementById('edit-modal').style.display = 'none';
});

// Validación de contraseña en tiempo real para el modal de edición
document.getElementById('edit-user-password').addEventListener('input', function(e) {
    const password = e.target.value;
    const strength = checkPasswordStrength(password);
    const progressBar = document.getElementById('edit-password-strength-progress');
    const strengthText = document.getElementById('edit-password-strength-text');
    
    // Resetear clases
    progressBar.className = 'password-strength-progress';
    
    if (password.length === 0) {
        progressBar.style.width = '0%';
        strengthText.textContent = 'Seguridad: -';
        return;
    }
    
    // Actualizar barra y texto según fortaleza
    if (strength === 0) {
        progressBar.classList.add('password-very-weak');
        strengthText.textContent = 'Seguridad: Muy débil';
    } else if (strength === 1) {
        progressBar.classList.add('password-weak');
        strengthText.textContent = 'Seguridad: Débil';
    } else if (strength === 2 || strength === 3) {
        progressBar.classList.add('password-medium');
        strengthText.textContent = 'Seguridad: Media';
    } else {
        progressBar.classList.add('password-strong');
        strengthText.textContent = 'Seguridad: Fuerte';
    }
});

</script>
    <script src="../assets/js/main.js"></script>
</body>
</html>