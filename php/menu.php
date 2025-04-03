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
    <title>Principal</title>


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
    <div class="filter-container">
        <button class="filter-btn"><i class="fas fa-filter"></i> Filtrar</button>
        <div class="filter-dropdown">
            <div class="filter-group">
                <label for="filter-type">Tipo de archivo:</label>
                <select id="filter-type" class="filter-select">
                    <option value="all">Todos</option>
                    <option value="image">Imágenes</option>
                    <option value="document">Documentos</option>
                    <option value="audio">Audio</option>
                    <option value="video">Videos</option>
                </select>
            </div>
            <button class="apply-filter-btn">Aplicar Filtro</button>
        </div>
    </div>
    <input type="text" class="search-bar" id="search-bar" placeholder="Buscar por nombre, tipo o fecha...">
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
                <!-- Las opciones de las carpetas se generarán dinámicamente aquí -->
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


  <!-- Modal para backup -->
  <?php include 'modal_backup.php'; ?>

  <!-- Modal para cierre_sesion -->
  <?php include 'confirmar_cierre.php'; ?>


  <!--- footer -->
<?php include 'footer.php'; ?>

    <script>
// Función para mostrar/ocultar el menú desplegable de filtrado
document.addEventListener('DOMContentLoaded', function () {
    const filterBtn = document.querySelector('.filter-btn');
    const filterDropdown = document.querySelector('.filter-dropdown');

    if (filterBtn && filterDropdown) {
        // Mostrar/ocultar el menú desplegable al hacer clic en el botón de filtro
        filterBtn.addEventListener('click', function (event) {
            event.stopPropagation(); // Evitar que el clic se propague
            filterDropdown.style.display = filterDropdown.style.display === 'block' ? 'none' : 'block';
        });

        // Evitar que el menú se cierre al hacer clic dentro de él
        filterDropdown.addEventListener('click', function (event) {
            event.stopPropagation();
        });

        // Ocultar el menú desplegable al hacer clic fuera
        document.addEventListener('click', function () {
            filterDropdown.style.display = 'none';
        });
    }
});

// Función para aplicar el filtro
function aplicarFiltro() {
    const filterType = document.getElementById('filter-type').value;
    const filesGrid = document.getElementById('files-grid');
    const fileItems = filesGrid.querySelectorAll('.file-item');

    fileItems.forEach(fileItem => {
        const tipoArchivo = fileItem.dataset.tipo; // Obtener el tipo de archivo del dataset
        if (filterType === 'all' || tipoArchivo === filterType) {
            fileItem.style.display = 'block'; // Mostrar el archivo
        } else {
            fileItem.style.display = 'none'; // Ocultar el archivo
        }
    });
}

// Evento para aplicar el filtro
const applyFilterBtn = document.querySelector('.apply-filter-btn');
if (applyFilterBtn) {
    applyFilterBtn.addEventListener('click', function () {
        aplicarFiltro();
    });
}

// Función para limpiar el filtro
function limpiarFiltro() {
    const filesGrid = document.getElementById('files-grid');
    const fileItems = filesGrid.querySelectorAll('.file-item');

    fileItems.forEach(fileItem => {
        fileItem.style.display = 'block'; // Mostrar todos los archivos
    });

    // Restablecer el valor del filtro
    document.getElementById('filter-type').value = 'all';
}

// Evento para limpiar el filtro
const clearFilterBtn = document.querySelector('.clear-filter-btn');
if (clearFilterBtn) {
    clearFilterBtn.addEventListener('click', function () {
        limpiarFiltro();
    });
}

// Función para buscar archivos
function buscarArchivos() {
    const searchTerm = document.getElementById('search-bar').value.toLowerCase();
    const filesGrid = document.getElementById('files-grid');
    const fileItems = filesGrid.querySelectorAll('.file-item');

    fileItems.forEach(fileItem => {
        const nombreArchivo = fileItem.querySelector('.file-name').textContent.toLowerCase();
        const tipoArchivo = fileItem.dataset.tipo.toLowerCase();
        const fechaArchivo = fileItem.querySelector('.file-date').textContent.toLowerCase();

        if (
            nombreArchivo.includes(searchTerm) ||
            tipoArchivo.includes(searchTerm) ||
            fechaArchivo.includes(searchTerm)
        ) {
            fileItem.style.display = 'block'; // Mostrar el archivo
        } else {
            fileItem.style.display = 'none'; // Ocultar el archivo
        }
    });
}

// Evento para la barra de búsqueda
const searchBar = document.getElementById('search-bar');
if (searchBar) {
    searchBar.addEventListener('input', function () {
        buscarArchivos();
    });
}

function abrirCarpeta(idCarpeta) {
    // Hacer una solicitud AJAX para obtener los archivos de la carpeta
    fetch('obtener_archivos.php?id_carpeta=' + idCarpeta)
        .then(response => response.json())
        .then(data => {
            // Limpiar el contenedor de archivos
            const filesGrid = document.getElementById('files-grid');
            filesGrid.innerHTML = '';

            // Mostrar los archivos en el contenedor
            data.forEach(archivo => {
                // Determinar el tipo de archivo
                const tipoArchivo = archivo.tipo_archivo;
                let icono = 'fa-file-alt'; // Ícono por defecto (documento)
                let tipo = 'document'; // Tipo por defecto (documento)

                // Asignar ícono y tipo según el tipo de archivo
                if (tipoArchivo.includes('image')) {
                    icono = 'fa-file-image';
                    tipo = 'image';
                } else if (tipoArchivo.includes('audio')) {
                    icono = 'fa-file-audio';
                    tipo = 'audio';
                } else if (tipoArchivo.includes('video')) {
                    icono = 'fa-file-video';
                    tipo = 'video';
                } else if (tipoArchivo.includes('pdf')) {
                    icono = 'fa-file-pdf';
                    tipo = 'document';
                } else if (tipoArchivo.includes('word') || tipoArchivo.includes('msword')) {
                    icono = 'fa-file-word';
                    tipo = 'document';
                } else if (tipoArchivo.includes('excel') || tipoArchivo.includes('spreadsheet')) {
                    icono = 'fa-file-excel';
                    tipo = 'document';
                } else if (tipoArchivo.includes('powerpoint') || tipoArchivo.includes('presentation')) {
                    icono = 'fa-file-powerpoint';
                    tipo = 'document';
                }

                // Generar el HTML para cada archivo
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';
                fileItem.setAttribute('data-tipo', tipo);
                fileItem.innerHTML = `
                    <div class="file-header">
                        <i class="fas ${icono} file-icon"></i>
                        <h4 class="file-name">${archivo.nombre_archivo}</h4>
                    </div>
                    <p class="file-date">${archivo.fecha_subida}</p>
                    <div class="file-actions">
                        <button class="download-btn" onclick="descargarArchivo('${archivo.nombre_archivo}')">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="edit-btn" onclick="editarArchivo(${archivo.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="delete-btn" onclick="eliminarArchivo(${archivo.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                filesGrid.appendChild(fileItem);
            });
        })
        .catch(error => console.error('Error:', error));
}

</script>
    <script src="../assets/js/main.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>