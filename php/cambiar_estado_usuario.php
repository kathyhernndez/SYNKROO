<?php
session_start();
include 'conexion_be.php';
include 'registrar_accion.php'; // Incluir la función para registrar acciones

header('Content-Type: application/json'); // Indicar que la respuesta es JSON

// Verificar si el usuario tiene el rol adecuado (id_roles igual a 1 o 2)
if (!isset($_SESSION['id_roles']) || ($_SESSION['id_roles'] != 1 && $_SESSION['id_roles'] != 2)) {
    echo '
    <script>
    alert("No tienes permisos para acceder a esta vista");
    window.location = "menu.php";
    </script>
    ';
    die();
}


try {
    // Obtener los datos del cuerpo de la solicitud
    $data = json_decode(file_get_contents('php://input'), true);
    $idUsuario = $data['idUsuario'];
    $estadoActual = $data['estadoActual'];
    $contrasena = $data['contrasena'];

    // Verificar que el usuario no esté intentando cambiarse a sí mismo
    if ($idUsuario == $_SESSION['usuario_id']) {
        echo json_encode(['success' => false, 'message' => 'No puedes cambiar tu propio estado.']);
        exit();
    }

    // Verificar si el usuario que se intenta modificar tiene rol 1 (SuperAdmin)
    $query = "SELECT id_roles FROM usuarios WHERE id = :idUsuario";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario['id_roles'] == 2) {
        echo json_encode(['success' => false, 'message' => 'No puedes cambiar el estado de un SuperAdmin.']);
        exit();
    }

    // Verificar la contraseña del usuario que realiza la acción
    $query = "SELECT clave FROM usuarios WHERE id = :idUsuario";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':idUsuario', $_SESSION['usuario_id'], PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!password_verify($contrasena, $row['clave'])) {
        echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta.']);
        exit();
    }

    // Cambiar el estado del usuario
    $nuevoEstado = ($estadoActual == 1) ? 0 : 1;
    $query = "UPDATE usuarios SET estado = :nuevoEstado WHERE id = :idUsuario";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':nuevoEstado', $nuevoEstado, PDO::PARAM_INT);
    $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Registrar la acción en la bitácora
        if (isset($_SESSION['usuario_id'])) {
            $usuario_id = $_SESSION['usuario_id'];
            $accion = ($nuevoEstado == 1) ? 'desbloqueo de usuario' : 'bloqueo de usuario';
            $descripcion = ($nuevoEstado == 1) 
                ? "El admin Desbloqueo un usuario." 
                : "El admin Bloqueo un usuario.";
            registrarAccion($conexion, $usuario_id, $accion, $descripcion);
        }

        echo json_encode(['success' => true, 'message' => 'Estado del usuario actualizado correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado del usuario.']);
    }
} catch (PDOException $e) {
    // Manejo de errores
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

// Cerrar la conexión
$conexion = null;
?>