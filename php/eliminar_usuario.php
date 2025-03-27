<?php
session_start();
include 'conexion_be.php';
include 'registrar_accion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si el usuario tiene permisos (admin o superadmin)
    if (!isset($_SESSION['id_roles']) || ($_SESSION['id_roles'] != 1 && $_SESSION['id_roles'] != 2)) {
        echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción.']);
        exit();
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $id = isset($data['id']) ? filter_var($data['id'], FILTER_VALIDATE_INT) : null;
    $contrasena = isset($data['contrasena']) ? trim($data['contrasena']) : '';

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID de usuario no válido.']);
        exit();
    }

    // Verificar la contraseña del administrador
    if (empty($contrasena)) {
        echo json_encode(['success' => false, 'message' => 'Debe ingresar su contraseña.']);
        exit();
    }

    try {
        // Verificar contraseña del administrador
        $queryAdmin = "SELECT id, clave FROM usuarios WHERE id = :admin_id";
        $stmtAdmin = $conexion->prepare($queryAdmin);
        $stmtAdmin->bindParam(':admin_id', $_SESSION['usuario_id'], PDO::PARAM_INT);
        $stmtAdmin->execute();
        $admin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);

        if (!password_verify($contrasena, $admin['clave'])) {
            echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta.']);
            exit();
        }

        // Obtener información del usuario a eliminar
        $queryUsuario = "SELECT id, id_roles FROM usuarios WHERE id = :id";
        $stmtUsuario = $conexion->prepare($queryUsuario);
        $stmtUsuario->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtUsuario->execute();
        $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            echo json_encode(['success' => false, 'message' => 'El usuario no existe.']);
            exit();
        }

        // Validar que un admin no pueda eliminar a un superadmin
        if ($_SESSION['id_roles'] == 1 && $usuario['id_roles'] == 2) {
            echo json_encode(['success' => false, 'message' => 'No tienes permisos para eliminar a un SuperAdmin.']);
            exit();
        }

        // No permitir auto-eliminación
        if ($usuario['id'] == $_SESSION['usuario_id']) {
            echo json_encode(['success' => false, 'message' => 'No puedes eliminarte a ti mismo.']);
            exit();
        }

        // Eliminar el usuario
        $queryDelete = "DELETE FROM usuarios WHERE id = :id";
        $stmtDelete = $conexion->prepare($queryDelete);
        $stmtDelete->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmtDelete->execute()) {
            // Registrar la acción
            if (isset($_SESSION['usuario_id'])) {
                $accion = 'Eliminó al usuario con ID: ' . $id;
                registrarAccion($conexion, $_SESSION['usuario_id'], 'eliminar usuario', $accion);
            }

            echo json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar el usuario.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}

$conexion = null;
?>