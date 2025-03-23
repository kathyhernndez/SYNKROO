<?php
include 'conexion_be.php'; // Incluye el archivo de conexión a la base de datos
include 'registrar_accion.php'; // Incluye el archivo para registrar acciones
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener el ID de la carpeta desde la solicitud POST
    $id = $_POST['id'];

    try {
        // Obtener la ruta de la carpeta desde la base de datos
        $query = "SELECT ruta_carpeta FROM carpeta WHERE id = :id";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $carpeta = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($carpeta) {
            $rutaCarpeta = $carpeta['ruta_carpeta'];

            // Eliminar la carpeta físicamente del servidor
            if (is_dir($rutaCarpeta)) {
                // Eliminar todos los archivos y subcarpetas dentro de la carpeta
                $files = glob($rutaCarpeta . '/*'); // Obtener todos los archivos y subcarpetas
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file); // Eliminar archivos
                    } elseif (is_dir($file)) {
                        // Eliminar subcarpetas recursivamente
                        array_map('unlink', glob("$file/*"));
                        rmdir($file);
                    }
                }
                rmdir($rutaCarpeta); // Eliminar la carpeta principal
            }

            // Eliminar la carpeta de la base de datos
            $query = "DELETE FROM carpeta WHERE id = :id";
            $stmt = $conexion->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                // Registrar la acción en el sistema
                if (isset($_SESSION['usuario_id'])) {
                    $usuario_id = $_SESSION['usuario_id'];
                    registrarAccion($conexion, $usuario_id, 'eliminar carpeta', "Se eliminó la carpeta con ID $id y su contenido físico.");
                    echo json_encode([
                        'success' => true,
                        'message' => 'Carpeta eliminada correctamente.',
                        'type' => 'success' // Tipo de mensaje: éxito
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error: Sesión no válida.',
                        'type' => 'error' // Tipo de mensaje: error
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al eliminar la carpeta de la base de datos.',
                    'type' => 'error' // Tipo de mensaje: error
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Carpeta no encontrada.',
                'type' => 'error' // Tipo de mensaje: error
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage(),
            'type' => 'error' // Tipo de mensaje: error
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido.',
        'type' => 'error' // Tipo de mensaje: error
    ]);
}

// Cerrar la conexión
$conexion = null;
?>