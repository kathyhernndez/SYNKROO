<?php
include 'conexion_be.php';

$idCarpeta = $_GET['id_carpeta'];

try {
    // Preparar la consulta SQL para obtener los archivos de la carpeta
    $query = "SELECT * FROM archivos WHERE carpeta_id = :id_carpeta ORDER BY fecha_subida DESC";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':id_carpeta', $idCarpeta, PDO::PARAM_INT);
    $stmt->execute();

    $archivos = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $archivos[] = $row;
    }

    // Devolver los archivos en formato JSON
    echo json_encode($archivos);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al cargar los archivos: ' . $e->getMessage()]);
}

$conexion = null;
?>