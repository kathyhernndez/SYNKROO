<?php
include 'conexion_be.php';

// Configuración de paginación
$registrosPorPagina = 10;
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaActual - 1) * $registrosPorPagina;

// Consulta para obtener el total de carpetas
$queryTotal = "SELECT COUNT(*) as total FROM carpeta";
$stmtTotal = $conexion->query($queryTotal);
$totalRegistros = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

// Consulta para obtener las carpetas con paginación
$query = "SELECT * FROM carpeta ORDER BY nombre_carpeta ASC LIMIT :limit OFFSET :offset";
$stmt = $conexion->prepare($query);
$stmt->bindParam(':limit', $registrosPorPagina, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

while ($carpeta = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Limitar el nombre de la carpeta a 10 caracteres
    $nombreMostrado = strlen($carpeta['nombre_carpeta']) > 10 
        ? substr($carpeta['nombre_carpeta'], 0, 10) . '...' 
        : $carpeta['nombre_carpeta'];
    
    echo '
    <div class="folder-item" data-id="' . $carpeta['id'] . '">
        <div class="folder-header">
            <i class="fas fa-folder folder-icon"></i>
            <h4 class="folder-name" title="' . htmlspecialchars($carpeta['nombre_carpeta']) . '">' . 
                htmlspecialchars($nombreMostrado) . '</h4>
        </div>
        <div class="folder-actions">
            <button class="edit-btn" onclick="editarCarpeta(' . $carpeta['id'] . ', \'' . htmlspecialchars(addslashes($carpeta['nombre_carpeta'])) . '\')">
                <i class="fas fa-edit"></i>
            </button>
            <button class="delete-btn" onclick="eliminarCarpeta(' . $carpeta['id'] . ')">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>';
}

// Mostrar paginación si hay más de una página
if ($totalPaginas > 1) {
    echo '<div class="pagination">';
    
    // Botón Anterior
    if ($paginaActual > 1) {
        echo '<a href="?pagina=' . ($paginaActual - 1) . '">&laquo; Anterior</a>';
    }
    
    // Números de página
    $rango = 2;
    $inicio = max(1, $paginaActual - $rango);
    $fin = min($totalPaginas, $paginaActual + $rango);
    
    if ($inicio > 1) {
        echo '<a href="?pagina=1">1</a>';
        if ($inicio > 2) echo '<span class="pagination-dots">...</span>';
    }
    
    for ($i = $inicio; $i <= $fin; $i++) {
        if ($i == $paginaActual) {
            echo '<a href="?pagina=' . $i . '" class="active">' . $i . '</a>';
        } else {
            echo '<a href="?pagina=' . $i . '">' . $i . '</a>';
        }
    }
    
    if ($fin < $totalPaginas) {
        if ($fin < $totalPaginas - 1) echo '<span class="pagination-dots">...</span>';
        echo '<a href="?pagina=' . $totalPaginas . '">' . $totalPaginas . '</a>';
    }
    
    // Botón Siguiente
    if ($paginaActual < $totalPaginas) {
        echo '<a href="?pagina=' . ($paginaActual + 1) . '">Siguiente &raquo;</a>';
    }
    
    echo '</div>';
}
?>