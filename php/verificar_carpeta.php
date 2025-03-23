<?php
include 'conexion_be.php'; // Incluye el archivo de conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener el nombre de la carpeta desde la solicitud POST
    $nombreCarpeta = $_POST['nombre_carpeta'];

    try {
        // Preparar la consulta SQL para verificar si la carpeta existe
        $query = "SELECT ruta FROM carpeta WHERE nombre_carpeta = :nombre_carpeta";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':nombre_carpeta', $nombreCarpeta, PDO::PARAM_STR);
        $stmt->execute();

        // Obtener el resultado
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            // Si la carpeta existe, devolver su ruta
            echo json_encode([
                'existe' => true,
                'ruta' => $resultado['ruta']
            ]);
        } else {
            // Si la carpeta no existe
            echo json_encode([
                'existe' => false
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'error' => "Error en la base de datos: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'error' => "Método no permitido."
    ]);
}

// Cerrar la conexión
$conexion = null;
?>