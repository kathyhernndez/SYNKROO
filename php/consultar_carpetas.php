<?php
include 'conexion_be.php';

try {
    // Preparar la consulta SQL
    $query = "SELECT * FROM carpeta ORDER BY fecha_creacion DESC";
    $stmt = $conexion->query($query);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '
        <div class="file-item">
            <div class="file-header">
                <i class="fas fa-folder file-icon"></i>
                <h4 class="file-name">'. htmlspecialchars($row['nombre_carpeta']) . '</h4>
            </div>
            <p class="file-date">' . htmlspecialchars($row['fecha_creacion']) . '</p>
            <div class="file-actions">
                <button class="download-btn" onclick="descargarCarpeta(\'' . htmlspecialchars($row['ruta_carpeta']) . '\')">
                    <i class="fas fa-download"></i>
                </button>
                <button class="edit-btn" onclick="editarCarpeta(' . htmlspecialchars($row['id']) . ')">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="delete-btn" onclick="eliminarCarpeta(' . htmlspecialchars($row['id']) . ')">
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