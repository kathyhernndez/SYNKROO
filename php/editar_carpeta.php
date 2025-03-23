<?php
include 'conexion_be.php'; // Incluye el archivo de conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener el ID y el nuevo nombre de la carpeta desde la solicitud POST
    $id = $_POST['id'];
    $nombreCarpeta = $_POST['nombre_carpeta'];

    try {
        // Preparar la consulta SQL para actualizar el nombre de la carpeta
        $query = "UPDATE carpeta SET nombre_carpeta = :nombre_carpeta WHERE id = :id";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':nombre_carpeta', $nombreCarpeta, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo "Nombre de la carpeta actualizado correctamente.";
        } else {
            echo "Error al actualizar el nombre de la carpeta.";
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