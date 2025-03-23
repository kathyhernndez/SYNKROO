<?php
include 'conexion_be.php'; // Incluye el archivo de conexión a la base de datos
include 'registrar_accion.php'; // Incluye el archivo para registrar acciones
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener el ID y el nuevo nombre de la carpeta desde la solicitud POST
    $id = $_POST['id'];
    $nombreCarpeta = $_POST['nombre_carpeta'];

    try {
        // Obtener el nombre actual de la carpeta desde la base de datos
        $query = "SELECT nombre_carpeta, ruta_carpeta FROM carpeta WHERE id = :id";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $carpeta = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($carpeta) {
            $nombreActual = $carpeta['nombre_carpeta'];
            $rutaActual = $carpeta['ruta_carpeta']; // Ruta completa de la carpeta en el servidor

            // Renombrar la carpeta física en el servidor
            $nuevaRuta = dirname($rutaActual) . DIRECTORY_SEPARATOR . $nombreCarpeta;
            if (rename($rutaActual, $nuevaRuta)) {
                // Actualizar el nombre de la carpeta en la base de datos
                $query = "UPDATE carpeta SET nombre_carpeta = :nombre_carpeta, ruta_carpeta = :nueva_ruta WHERE id = :id";
                $stmt = $conexion->prepare($query);
                $stmt->bindParam(':nombre_carpeta', $nombreCarpeta, PDO::PARAM_STR);
                $stmt->bindParam(':nueva_ruta', $nuevaRuta, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    // Registrar la acción en el sistema
                    if (isset($_SESSION['usuario_id'])) {
                        $usuario_id = $_SESSION['usuario_id'];
                        registrarAccion($conexion, $usuario_id, 'editar carpeta', "Se renombró la carpeta '$nombreActual' a '$nombreCarpeta'.");
                        echo json_encode([
                            'success' => true,
                            'message' => 'Nombre de la carpeta actualizado correctamente.',
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
                        'message' => 'Error al actualizar el nombre de la carpeta en la base de datos.',
                        'type' => 'error' // Tipo de mensaje: error
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al renombrar la carpeta en el servidor.',
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