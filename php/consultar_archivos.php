<?php
include 'conexion_be.php';

try {
    // Preparar la consulta SQL
    $query = "SELECT * FROM archivos ORDER BY fecha_subida DESC";
    $stmt = $conexion->query($query);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Determinar el tipo de archivo
        $tipoArchivo = $row['tipo_archivo'];
        $icono = 'fa-file-alt'; // Ícono por defecto (documento)
        $tipo = 'document'; // Tipo por defecto (documento)

        // Asignar ícono y tipo según el tipo de archivo
        if (strpos($tipoArchivo, 'image') !== false) {
            $icono = 'fa-file-image';
            $tipo = 'image';
        } elseif (strpos($tipoArchivo, 'audio') !== false) {
            $icono = 'fa-file-audio';
            $tipo = 'audio';
        } elseif (strpos($tipoArchivo, 'video') !== false) {
            $icono = 'fa-file-video';
            $tipo = 'video';
        } elseif (strpos($tipoArchivo, 'pdf') !== false) {
            $icono = 'fa-file-pdf';
            $tipo = 'document';
        } elseif (strpos($tipoArchivo, 'word') !== false || strpos($tipoArchivo, 'msword') !== false) {
            $icono = 'fa-file-word';
            $tipo = 'document';
        } elseif (strpos($tipoArchivo, 'excel') !== false || strpos($tipoArchivo, 'spreadsheet') !== false) {
            $icono = 'fa-file-excel';
            $tipo = 'document';
        } elseif (strpos($tipoArchivo, 'powerpoint') !== false || strpos($tipoArchivo, 'presentation') !== false) {
            $icono = 'fa-file-powerpoint';
            $tipo = 'document';
        }

        // Generar el HTML para cada archivo
        echo '
        <div class="file-item" data-tipo="' . htmlspecialchars($tipo) . '">
            <div class="file-header">
                <i class="fas ' . $icono . ' file-icon"></i>
                <h4 class="file-name">' . htmlspecialchars($row['nombre_archivo']) . '</h4>
            </div>
            <p class="file-date">' . htmlspecialchars($row['fecha_subida']) . '</p>
            <div class="file-actions">
                <button class="download-btn" onclick="descargarArchivo(\'' . htmlspecialchars($row['nombre_archivo']) . '\')">
                    <i class="fas fa-download"></i>
                </button>
                <button class="edit-btn" onclick="editarArchivo(' . htmlspecialchars($row['id']) . ')">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="delete-btn" onclick="eliminarArchivo(' . htmlspecialchars($row['id']) . ')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        ';
    }
} catch (PDOException $e) {
    echo "Error al cargar los archivos: " . $e->getMessage();
}

// Cerrar la conexión (no es necesario en PDO, pero puedes asignar null para liberar recursos)
$conexion = null;
?>