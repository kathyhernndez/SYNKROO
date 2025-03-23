<?php
include 'conexion_be.php'; // Incluye el archivo de conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener el ID de la carpeta desde la solicitud POST
    $id = $_POST['id'];

    try {
        // Preparar la consulta SQL para eliminar la carpeta
        $query = "DELETE FROM carpeta WHERE id = :id";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo "Carpeta eliminada correctamente.";
        } else {
            echo "Error al eliminar la carpeta.";
        }
    } catch (PDOException $e) {
        echo "Error en la base de datos: " . $e->getMessage();
    }
} else {
    echo "Método no permitido.";
}

// Cerrar la conexión
$conexion = null;
?>