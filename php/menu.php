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



// Verificar si hay un mensaje de sesión y mostrarlo
if (isset($_SESSION['message'])) {
  echo '
  <div class="alert alert-success alert-dismissible fade show" role="alert">
      ' . $_SESSION['message'] . '
  </div>';
  unset($_SESSION['message']);
}

if ($message) { echo ' 
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
   ' . $message . ' </div> '; }

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/image/favicon.png" />
    <title>Principal</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard">
        <!-- Barra lateral izquierda (menú) -->

        <!-- Contenido principal -->
         <!-- Barra superior -->
        <div class="main-content">
        
            <!-- Contenido del dashboard -->
            <div class="content">
                <!-- Barra de estado -->
                
            <?php 
            include 'barra_estado.php';
            ?>
            
                <!-- Contenedor de archivos -->
                <div class="files-container">
                    <!-- Filtros y barra de búsqueda dentro del div de archivos -->
                    <div class="filters">
                        <button class="filter-btn"><i class="fas fa-filter"></i> Filtrar</button>
                        <input type="text" class="search-bar" placeholder="Buscar...">
                        <button class="upload-btn" id="upload-btn"><i class="fas fa-upload"></i> Cargar Archivos</button>
                        <button class="view-toggle-btn" id="view-toggle-btn"><i class="fas fa-folder"></i> Ver por Carpetas</button>
                    </div>
                    <div class="files-grid" id="files-grid">
                        <!-- Aquí se mostrarían los archivos -->
                         <?php 
            include 'consultar_archivos.php';
            ?>
                        <!-- Más archivos aquí -->
                    </div>
                </div>
            </div>
        </div>
    </div>

  <div class="modal" id="upload-modal">
    <div class="modal-content">
        <span class="close-modal" id="close-modal">&times;</span>
        <h2>Cargar Archivos</h2>
        <form id="upload-form">
            <label for="folder-select">Seleccionar carpeta:</label>
            <select id="folder-select">
                <option value="#">Seleccionar carpeta</option>
                <option value="new">Crear nueva carpeta</option>
                <option value="folder1">Carpeta 1</option>
                <option value="folder2">Carpeta 2</option>
            </select>
            <!-- Input para el nombre de la carpeta (oculto por defecto) -->
            <div id="new-folder-input" style="display: none;">
                <label for="folder-name">Nombre de la carpeta:</label>
                <input type="text" id="folder-name" placeholder="Ingrese el nombre de la carpeta">
            </div>
            <input type="file" id="file-input" multiple>
            <!-- Contenedor para mensajes de error -->
            <div id="error-message" class="error-message" style="display: none;"></div>
            <button type="submit">Cargar</button>
        </form>
    </div>
</div>
    <script>
      
    </script>
    <script src="../assets/js/main.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>