<?php
session_start();
include 'conexion_be.php'; // Incluye la conexión a la base de datos

try {
    // Consulta para obtener las carpetas
    $query = "SELECT id, nombre_carpeta FROM carpeta ORDER BY nombre_carpeta ASC";
    $stmt = $conexion->prepare($query);
    $stmt->execute();

    // Obtener los resultados
    $carpetas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver los datos en formato JSON
    header('Content-Type: application/json');
    echo json_encode($carpetas);
} catch (PDOException $e) {
    // Manejar errores
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error al obtener las carpetas: ' . $e->getMessage()]);
}
?>