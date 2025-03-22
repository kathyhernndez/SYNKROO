<?php
include 'conexion_be.php';
include 'registrar_accion.php';
session_start();

header('Content-Type: application/json'); // Indicar que la respuesta es JSON

try {
    // Sanitizar entradas del usuario para evitar inyecciones SQL
    $nombres = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $cedula = $_POST['cedula'];
    $clave = $_POST['clave'];
    $confirmar_clave = $_POST['confirmar_clave'];
    $rol = $_POST['rol'];

    // Validar que la cédula tenga entre 6 y 12 dígitos y solo números
    if (!preg_match('/^\d{6,12}$/', $cedula)) {
        echo json_encode(['success' => false, 'message' => 'La cédula debe tener entre 6 y 12 dígitos y solo puede contener números.']);
        exit();
    }

    // Validar que la contraseña tenga al menos 16 caracteres
    if (strlen($clave) < 16) {
        echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 16 caracteres.']);
        exit();
    }

    // Verificar que las contraseñas coincidan
    if ($clave !== $confirmar_clave) {
        echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden. Por favor, inténtelo de nuevo.']);
        exit();
    }

    // Verificar si el correo ya está en uso
    $query_verificar_correo = "SELECT id FROM usuarios WHERE correo = :correo";
    $stmt_verificar_correo = $conexion->prepare($query_verificar_correo);
    $stmt_verificar_correo->bindParam(':correo', $correo, PDO::PARAM_STR);
    $stmt_verificar_correo->execute();

    if ($stmt_verificar_correo->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'El correo ya está registrado. Por favor, use otro correo.']);
        exit();
    }

    // Verificar si la cédula ya está en uso
    $query_verificar_cedula = "SELECT id FROM usuarios WHERE cedula = :cedula";
    $stmt_verificar_cedula = $conexion->prepare($query_verificar_cedula);
    $stmt_verificar_cedula->bindParam(':cedula', $cedula, PDO::PARAM_STR);
    $stmt_verificar_cedula->execute();

    if ($stmt_verificar_cedula->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'La cédula ya está registrada. Por favor, use otra cédula.']);
        exit();
    }

    // Encriptar contraseña usando password_hash
    $clave_encriptada = password_hash($clave, PASSWORD_DEFAULT);

    // Insertar el nuevo usuario en la tabla usuarios
    $query_usuario = "INSERT INTO usuarios (nombre, apellido, correo, cedula, clave, estado, id_roles) 
                      VALUES (:nombres, :apellido, :correo, :cedula, :clave_encriptada, '1', :rol)";
    $stmt_usuario = $conexion->prepare($query_usuario);
    $stmt_usuario->bindParam(':nombres', $nombres, PDO::PARAM_STR);
    $stmt_usuario->bindParam(':apellido', $apellido, PDO::PARAM_STR);
    $stmt_usuario->bindParam(':correo', $correo, PDO::PARAM_STR);
    $stmt_usuario->bindParam(':cedula', $cedula, PDO::PARAM_STR);
    $stmt_usuario->bindParam(':clave_encriptada', $clave_encriptada, PDO::PARAM_STR);
    $stmt_usuario->bindParam(':rol', $rol, PDO::PARAM_INT);

    if ($stmt_usuario->execute()) {
        if (isset($_SESSION['usuario_id'])) {
            $usuario_id = $_SESSION['usuario_id'];
            registrarAccion($conexion, $usuario_id, 'registro de usuario', 'Un nuevo usuario ha sido registrado en el sistema.');
            echo json_encode(['success' => true, 'message' => 'Usuario registrado exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al registrar el usuario: Sesión no válida.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al registrar el usuario en la base de datos.']);
    }
} catch (PDOException $e) {
    // Manejo de errores
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

// Cerrar la conexión (no es necesario en PDO, pero puedes asignar null para liberar recursos)
$conexion = null;
?>