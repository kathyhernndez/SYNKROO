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


<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<title>Bitácora</title>
<link rel="shortcut icon" href="../assets/image/favicon.png" />
</head>


    <style>
     .filters {
    display: flex;
    gap: 15px;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
}



.date-filter-container {
    display: flex;
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow: hidden;
    height: 36px; /* Misma altura que otros inputs */
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transition: box-shadow 0.3s ease;
}


.date-filter-container:focus-within {
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    border-color: #4CAF50;
}

.date-filter {
    border: none;
    padding: 8px 12px;
    outline: none;
    min-width: 150px;
}

.filter-button, .clear-filter {
    border: none;
    background-color: #f8f9fa;
    color: #495057;
    cursor: pointer;
    padding: 0 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.filter-button:hover {
    background-color: #4CAF50;
    color: white;
}

.clear-filter:hover {
    background-color: #f44336;
    color: white;
}

/* Estilos para los iconos (opcional) */
.filter-button i, .clear-filter i {
    font-size: 14px;
}

/* Para pantallas pequeñas */
@media (max-width: 768px) {
    .filters {
        flex-direction: column;
        align-items: stretch;
    }
    
    .date-filter-container {
        width: 50%;
    }
}

/* Estilos para modo oscuro SOLO para el date-filter-container */
.dark-mode .date-filter-container {
    background-color: #1a1a1a;
    border-color: #444444;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
}

.dark-mode .date-filter {
    background-color: #1a1a1a;
    color: #ffffff;
}

.dark-mode .date-filter::-webkit-calendar-picker-indicator {
    filter: invert(1);
}

.dark-mode .filter-button,
.dark-mode .clear-filter {
    background-color: #2d2d2d;
    color: #ffffff;
}

.dark-mode .filter-button:hover {
    background-color: #388E3C;
}

.dark-mode .clear-filter:hover {
    background-color: #D32F2F;
}

/* Estilos para los iconos en modo oscuro */
.dark-mode .filter-button i,
.dark-mode .clear-filter i {
    color: #ffffff;
}
    </style>
<body>
    <div class="dashboard">
          <!-- Barra lateral izquierda (menú) -->
         

        <!-- Contenido principal -->
         
         <!-- Barra superior -->
        <div class="main-content">
         

            <!-- Contenido del dashboard -->
            <div class="content">
               

                <!-- Contenedor de la bitácora -->
                <div class="files-container">
                    <!-- Filtros y barra de búsqueda -->
                <!-- Reemplaza el div .filters con este código -->
<div class="filters">
    <!-- Barra de búsqueda independiente -->
    <input type="text" id="search-bar" class="search-bar" placeholder="Buscar..." oninput="buscarBitacora()">
    
    <!-- Contenedor filtro por fecha -->
    <div class="date-filter-container">
        <input type="date" id="date-filter" class="date-filter">
        <button id="filter-button" class="filter-button">
            <i class="fas fa-filter"></i> 
        </button>
        <button id="clear-filter" class="clear-filter">
            <i class="fas fa-times"></i> 
        </button>
    </div>
    
    <div class="pagination"><!-- La paginación se generará aquí dinámicamente --></div>
</div>
           
                    <!-- Tabla de la bitácora -->
<div class="table-container">
    <table class="log-table">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Acción</th>
                <th>Descripción</th>
                <th>Fecha y Hora</th>
            </tr>
        </thead>
        <tbody>
             
        </tbody>
    </table>
   
</div>

                </div>
            </div>
        </div>
    </div>

<!-- Modal para backup -->
<?php include 'modal_backup.php'; ?>

<!-- Modal para cierre_sesion -->
<?php include 'confirmar_cierre.php'; ?>

    <script>
// Modifica el script para incluir el filtrado por fecha
document.addEventListener('DOMContentLoaded', function () {
    const pagination = document.querySelector('.pagination');
    const tableBody = document.querySelector('.log-table tbody');
    const searchBar = document.getElementById('search-bar');
    const dateFilter = document.getElementById('date-filter');
    const filterButton = document.getElementById('filter-button');
    const clearFilter = document.getElementById('clear-filter');
    let currentPage = 1;
    let totalPages = 1;

    // Función para cargar los datos de la página
    function loadPage(page, searchTerm = '', filterDate = '') {
        let url = `bitacora.php?page=${page}`;
        if (searchTerm) url += `&search=${encodeURIComponent(searchTerm)}`;
        if (filterDate) url += `&date=${filterDate}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    return;
                }

                // Actualizar la tabla
                tableBody.innerHTML = '';
                data.data.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${row.nombre} ${row.apellido}</td>
                        <td>${row.accion}</td>
                        <td>${row.descripcion}</td>
                        <td>${row.fecha_hora}</td>
                    `;
                    tableBody.appendChild(tr);
                });

                // Actualizar la paginación
                totalPages = data.totalPages;
                currentPage = data.currentPage;
                updatePagination();
            })
            .catch(error => console.error('Error:', error));
    }

      // Función para actualizar la paginación
      function updatePagination() {
        pagination.innerHTML = '';

        // Flecha "anterior"
        const prevArrow = document.createElement('a');
        prevArrow.href = '#';
        prevArrow.className = 'arrow prev';
        prevArrow.innerHTML = '&laquo;';
        prevArrow.addEventListener('click', function (e) {
            e.preventDefault();
            if (currentPage > 1) {
                loadPage(currentPage - 1, searchBar.value);
            }
        });
        pagination.appendChild(prevArrow);

        // Números de página
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);

        for (let i = startPage; i <= endPage; i++) {
            const pageLink = document.createElement('a');
            pageLink.href = '#';
            pageLink.className = 'page';
            pageLink.textContent = i;
            if (i === currentPage) {
                pageLink.classList.add('active');
            }
            pageLink.addEventListener('click', function (e) {
                e.preventDefault();
                loadPage(i, searchBar.value);
            });
            pagination.appendChild(pageLink);
        }

        // Flecha "siguiente"
        const nextArrow = document.createElement('a');
        nextArrow.href = '#';
        nextArrow.className = 'arrow next';
        nextArrow.innerHTML = '&raquo;';
        nextArrow.addEventListener('click', function (e) {
            e.preventDefault();
            if (currentPage < totalPages) {
                loadPage(currentPage + 1, searchBar.value);
            }
        });
        pagination.appendChild(nextArrow);
    }


    // Evento para el botón de filtro
    filterButton.addEventListener('click', function () {
        const selectedDate = dateFilter.value;
        if (selectedDate) {
            loadPage(1, searchBar.value, selectedDate);
        }
    });

    // Evento para limpiar filtros
    clearFilter.addEventListener('click', function() {
        dateFilter.value = '';
        searchBar.value = '';
        loadPage(1);
    });

    // Evento para la barra de búsqueda
    searchBar.addEventListener('input', function () {
        loadPage(1, searchBar.value, dateFilter.value);
    });

    // Cargar la primera página al inicio
    loadPage(1);
});
    </script>
    <script src="../assets/js/main.js"></script>
</body>
</html>