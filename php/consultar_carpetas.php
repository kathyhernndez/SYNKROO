<?php
include 'conexion_be.php';

function mostrarCarpetas($conexion) {
    try {
        $query = "SELECT * FROM carpeta ORDER BY fecha_creacion DESC";
        $stmt = $conexion->query($query);
        $output = '';

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $output .= '
            <div class="file-item folder-item" data-id="' . htmlspecialchars($row['id']) . '">
                <div class="file-header">
                    <i class="fas fa-folder file-icon"></i>
                    <h4 class="file-name"><span class="folder-name">'. htmlspecialchars($row['nombre_carpeta']) . '</span></h4>
                </div>
                <p class="file-date">' . htmlspecialchars($row['fecha_creacion']) . '</p>
                <div class="file-actions">
                    <button class="edit-btn" onclick="event.stopPropagation(); editarCarpeta(' . htmlspecialchars($row['id']) . ', \'' . htmlspecialchars($row['nombre_carpeta'], ENT_QUOTES) . '\')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="delete-btn" onclick="event.stopPropagation(); eliminarCarpeta(' . htmlspecialchars($row['id']) . ')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            ';
        }
        return $output;
    } catch (PDOException $e) {
        return '<div class="error-message">Error al cargar las carpetas: ' . $e->getMessage() . '</div>';
    }
}

// Mostrar carpetas directamente si no es una llamada AJAX
if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    echo mostrarCarpetas($conexion);
}

$conexion = null;
?>