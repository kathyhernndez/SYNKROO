<?php
include 'conexion_be.php';

// Configuración de paginación
$registrosPorPagina = 10;
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaActual - 1) * $registrosPorPagina;

// Consulta para obtener el total de archivos
$queryTotal = "SELECT COUNT(*) as total FROM archivos";
$stmtTotal = $conexion->query($queryTotal);
$totalRegistros = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

// Consulta para obtener los archivos con paginación
$query = "SELECT * FROM archivos ORDER BY fecha_subida DESC LIMIT :limit OFFSET :offset";
$stmt = $conexion->prepare($query);
$stmt->bindParam(':limit', $registrosPorPagina, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

while ($archivo = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Determinar el tipo de archivo y el icono correspondiente
    $tipoArchivo = $archivo['tipo_archivo'];
    $icono = 'fa-file-alt'; // Ícono por defecto
    
    if (strpos($tipoArchivo, 'image') !== false) {
        $icono = 'fa-file-image';
    } elseif (strpos($tipoArchivo, 'audio') !== false) {
        $icono = 'fa-file-audio';
    } elseif (strpos($tipoArchivo, 'video') !== false) {
        $icono = 'fa-file-video';
    } elseif (strpos($tipoArchivo, 'pdf') !== false) {
        $icono = 'fa-file-pdf';
    } elseif (strpos($tipoArchivo, 'word') !== false) {
        $icono = 'fa-file-word';
    } elseif (strpos($tipoArchivo, 'excel') !== false) {
        $icono = 'fa-file-excel';
    } elseif (strpos($tipoArchivo, 'powerpoint') !== false) {
        $icono = 'fa-file-powerpoint';
    }
    
    // Limitar el nombre del archivo a 10 caracteres
    $nombreMostrado = strlen($archivo['nombre_archivo']) > 10 
        ? substr($archivo['nombre_archivo'], 0, 10) . '...' 
        : $archivo['nombre_archivo'];
    
    echo '
    <div class="file-item" data-tipo="' . htmlspecialchars($tipoArchivo) . '">
        <div class="file-header">
            <i class="fas ' . $icono . ' file-icon"></i>
            <h4 class="file-name" title="' . htmlspecialchars($archivo['nombre_archivo']) . '">' . 
                htmlspecialchars($nombreMostrado) . '</h4>
        </div>
        <p class="file-date">' . htmlspecialchars($archivo['fecha_subida']) . '</p>
        <div class="file-actions">
            <button class="download-btn" onclick="descargarArchivo(' . $archivo['id'] . ')">
                <i class="fas fa-download"></i>
            </button>
            <button class="edit-btn" onclick="editarArchivo(' . $archivo['id'] . ', \'' . htmlspecialchars(addslashes($archivo['nombre_archivo'])) . '\')">
                <i class="fas fa-edit"></i>
            </button>
            <button class="delete-btn" onclick="eliminarArchivo(' . $archivo['id'] . ')">
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
    $rango = 2; // Número de páginas a mostrar alrededor de la actual
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