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

if ($message) { echo ' 
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
   ' . $message . ' </div> '; }

?>




    <title>Bitácora</title>
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
                    <div class="filters">
                    <input type="text" id="search-bar" class="search-bar" placeholder="Buscar..." oninput="buscarBitacora()">
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
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const pagination = document.querySelector('.pagination');
    const tableBody = document.querySelector('.log-table tbody');
    const searchBar = document.getElementById('search-bar');
    let currentPage = 1;
    let totalPages = 1;

    // Función para cargar los datos de la página
    function loadPage(page, searchTerm = '') {
        fetch(`bitacora.php?page=${page}&search=${encodeURIComponent(searchTerm)}`)
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
        <td>${row.nombre} ${row.apellido}</td> <!-- Mostrar nombre y apellido -->
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

    // Evento para la barra de búsqueda
    searchBar.addEventListener('input', function () {
        loadPage(1, searchBar.value);
    });

    // Cargar la primera página al inicio
    loadPage(1);
});
    </script>
    <script src="../assets/js/main.js"></script>
</body>
</html>