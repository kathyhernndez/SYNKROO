<?php
include 'conexion_be.php';
include 'registrar_accion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y sanitizar las entradas
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
    $apellido = filter_input(INPUT_POST, 'apellido', FILTER_SANITIZE_STRING);
    $correo = filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_EMAIL);
    $cedula = filter_input(INPUT_POST, 'cedula', FILTER_SANITIZE_STRING);
    $clave = $_POST['clave']; // No sanitizar la contraseña para no alterarla

    if (!$id || !$nombre || !$apellido || !$correo || !$cedula) {
        die(json_encode(['success' => false, 'message' => 'Datos de entrada no válidos.']));
    }

    try {
        // Si se proporciona una nueva contraseña, encriptarla
        $clave_encriptada = !empty($clave) ? password_hash($clave, PASSWORD_DEFAULT) : null;

        // Actualizar el registro en la base de datos (sin modificar el rol)
        $query = "UPDATE usuarios SET nombre = :nombre, apellido = :apellido, correo = :correo, cedula = :cedula" . 
                 ($clave_encriptada ? ", clave = :clave" : "") . " WHERE id = :id";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':apellido', $apellido, PDO::PARAM_STR);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->bindParam(':cedula', $cedula, PDO::PARAM_STR);
        if ($clave_encriptada) {
            $stmt->bindParam(':clave', $clave_encriptada, PDO::PARAM_STR);
        }
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            if (isset($_SESSION['usuario_id'])) {
                $usuario_id = $_SESSION['usuario_id'];
                registrarAccion($conexion, $usuario_id, 'editar usuario', 'Un usuario ha sido actualizado en el sistema.');
                echo json_encode(['success' => true, 'message' => 'Usuario actualizado con éxito.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar el usuario: Sesión no válida.']);
            }
        }
    } catch (PDOException $e) {
        // Manejar errores de PDO
        echo json_encode(['success' => false, 'message' => 'Error en la consulta: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Solicitud no válida.']);
}

// Cerrar la conexión
$conexion = null;
?>