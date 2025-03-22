<?php
session_start();
include 'conexion_be.php'; // Incluir la conexión a la base de datos
include 'registrar_accion.php'; // Incluir la función para registrar acciones

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener el cuerpo de la solicitud JSON
    $data = json_decode(file_get_contents('php://input'), true);

    // Validar y sanitizar el ID del usuario
    $id = isset($data['id']) ? filter_var($data['id'], FILTER_VALIDATE_INT) : null;

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID de usuario no válido.']);
        exit();
    }

    try {
        // Verificar si el usuario existe
        $query = "SELECT id FROM usuarios WHERE id = :id";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'El usuario no existe.']);
            exit();
        }

        // Eliminar el usuario de la base de datos
        $queryDelete = "DELETE FROM usuarios WHERE id = :id";
        $stmtDelete = $conexion->prepare($queryDelete);
        $stmtDelete->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmtDelete->execute()) {
            // Registrar la acción en la bitácora
            if (isset($_SESSION['usuario_id'])) {
                $usuario_id = $_SESSION['usuario_id'];
                registrarAccion($conexion, $usuario_id, 'eliminar usuario', 'Un usuario ha sido eliminado del sistema.');
            }

            echo json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar el usuario.']);
        }
    } catch (PDOException $e) {
        // Manejo de errores de la base de datos
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}

// Cerrar la conexión
$conexion = null;
?>