<?php
include 'conexion_be.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreArchivo = $_POST['nombre_archivo'];

    try {
        // Verificar en la base de datos si el archivo existe
        $stmt = $conexion->prepare("SELECT ruta_archivo FROM archivos WHERE nombre_archivo = :nombre_archivo");
        $stmt->bindParam(':nombre_archivo', $nombreArchivo, PDO::PARAM_STR);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            $rutaArchivo = $resultado['ruta_archivo'];
            $rutaCompleta = __DIR__ . "/" . $rutaArchivo; // Ruta absoluta al archivo

            // Verificar si el archivo existe en el servidor
            if (file_exists($rutaCompleta)) {
                // Devolver un JSON con la ruta del archivo
                echo json_encode([
                    'existe' => true,
                    'ruta' => $rutaArchivo, // Ruta relativa o completa según lo necesites
                ]);
            } else {
                echo json_encode([
                    'existe' => false,
                    'mensaje' => 'El archivo no existe en el servidor.',
                ]);
            }
        } else {
            echo json_encode([
                'existe' => false,
                'mensaje' => 'El archivo no existe en la base de datos.',
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'existe' => false,
            'mensaje' => 'Error en la base de datos: ' . $e->getMessage(),
        ]);
    }
} else {
    echo json_encode([
        'existe' => false,
        'mensaje' => 'Método no permitido.',
    ]);
}
?>