<?php
include 'conexion_be.php';
include 'registrar_accion.php';
session_start();

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Verificar método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido', 405);
    }

    // Obtener datos del cuerpo de la solicitud
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar JSON: ' . json_last_error_msg(), 400);
    }

    // Verificar datos requeridos
    $requiredFields = ['nombre', 'apellido', 'correo', 'cedula', 'rol', 
                      'pregunta_1', 'pregunta_2', 'pregunta_3',
                      'respuesta_1', 'respuesta_2', 'respuesta_3'];
    
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            throw new Exception("El campo $field es requerido", 400);
        }
    }

    // Sanitizar entradas
    $nombres = filter_var($data['nombre'], FILTER_SANITIZE_STRING);
    $apellido = filter_var($data['apellido'], FILTER_SANITIZE_STRING);
    $correo = filter_var($data['correo'], FILTER_SANITIZE_EMAIL);
    $cedula = filter_var($data['cedula'], FILTER_SANITIZE_STRING);
    $clave = $data['clave'] ?? null;
    $confirmar_clave = $data['confirmar_clave'] ?? null;
    $rol = filter_var($data['rol'], FILTER_VALIDATE_INT);
    
    // Validar rol
    if (!in_array($rol, [1, 2, 3])) {
        throw new Exception('Rol de usuario no válido', 400);
    }

    // Validar que la cédula tenga entre 6 y 12 dígitos y solo números
    if (!preg_match('/^\d{6,12}$/', $cedula)) {
        throw new Exception('La cédula debe tener entre 6 y 12 dígitos y solo puede contener números.', 400);
    }

    // Validar formato de correo con los nuevos requisitos
    if (!preg_match('/^[a-zA-Z0-9._%+-]{2,}@[a-zA-Z0-9.-]{5,}\.[a-zA-Z]{2,}$/', $correo)) {
        throw new Exception('El correo electrónico no tiene un formato válido. Debe tener al menos 2 caracteres antes de la @, al menos 5 caracteres en el dominio y una extensión de dominio válida.', 400);
    }

     // Validar que la contraseña tenga al menos 16 caracteres
     if ($clave && strlen($clave) < 16) {
        throw new Exception('La contraseña debe tener al menos 16 caracteres.', 400);
    }

    // Validar complejidad de la contraseña
    if ($clave) {
        if (!preg_match('/[A-Z]/', $clave)) {
            throw new Exception('La contraseña debe contener al menos una letra mayúscula.', 400);
        }
        if (!preg_match('/[a-z]/', $clave)) {
            throw new Exception('La contraseña debe contener al menos una letra minúscula.', 400);
        }
        if (!preg_match('/[0-9]/', $clave)) {
            throw new Exception('La contraseña debe contener al menos un número.', 400);
        }
    }

    // Verificar que las contraseñas coincidan
    if ($clave && $clave !== $confirmar_clave) {
        throw new Exception('Las contraseñas no coinciden. Por favor, inténtelo de nuevo.', 400);
    }

    // Verificar si el correo ya está en uso
    $query_verificar_correo = "SELECT id FROM usuarios WHERE correo = ?";
    $stmt_verificar_correo = $conexion->prepare($query_verificar_correo);
    $stmt_verificar_correo->execute([$correo]);

    if ($stmt_verificar_correo->rowCount() > 0) {
        throw new Exception('El correo ya está registrado. Por favor, use otro correo.', 400);
    }

    // Verificar si la cédula ya está en uso
    $query_verificar_cedula = "SELECT id FROM usuarios WHERE cedula = ?";
    $stmt_verificar_cedula = $conexion->prepare($query_verificar_cedula);
    $stmt_verificar_cedula->execute([$cedula]);

    if ($stmt_verificar_cedula->rowCount() > 0) {
        throw new Exception('La cédula ya está registrada. Por favor, use otra cédula.', 400);
    }

    // Encriptar contraseña usando password_hash
    $clave_encriptada = $clave ? password_hash($clave, PASSWORD_DEFAULT) : null;

    // Encriptar respuestas de seguridad (convertir a minúsculas y trim)
    $respuesta_1 = strtolower(trim($data['respuesta_1']));
    $respuesta_2 = strtolower(trim($data['respuesta_2']));
    $respuesta_3 = strtolower(trim($data['respuesta_3']));

    $respuesta_1_encriptada = password_hash($respuesta_1, PASSWORD_DEFAULT);
    $respuesta_2_encriptada = password_hash($respuesta_2, PASSWORD_DEFAULT);
    $respuesta_3_encriptada = password_hash($respuesta_3, PASSWORD_DEFAULT);

    // Iniciar transacción
    $conexion->beginTransaction();

    try {
        // Insertar el nuevo usuario
        $query_usuario = "INSERT INTO usuarios (nombre, apellido, correo, cedula, clave, estado, id_roles) 
                          VALUES (?, ?, ?, ?, ?, 1, ?)";
        $stmt_usuario = $conexion->prepare($query_usuario);
        $stmt_usuario->execute([$nombres, $apellido, $correo, $cedula, $clave_encriptada, $rol]);

        // Obtener ID del nuevo usuario
        $usuario_id = $conexion->lastInsertId();

        // Insertar preguntas y respuestas de seguridad (con respuestas encriptadas)
        $query_preguntas = "INSERT INTO recuperar_contrasena 
                            (pregunta_1, pregunta_2, pregunta_3, 
                             respuesta_1, respuesta_2, respuesta_3, 
                             id_usuario) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_preguntas = $conexion->prepare($query_preguntas);
        $stmt_preguntas->execute([
            $data['pregunta_1'],  // Texto completo de la pregunta 1
            $data['pregunta_2'],  // Texto completo de la pregunta 2
            $data['pregunta_3'],  // Texto completo de la pregunta 3
            $respuesta_1_encriptada,
            $respuesta_2_encriptada,
            $respuesta_3_encriptada,
            $usuario_id
        ]);

        // Registrar en bitácora
        if (isset($_SESSION['usuario_id'])) {
            registrarAccion($conexion, $_SESSION['usuario_id'], 'registro de usuario', 'Nuevo usuario registrado');
        }

        $conexion->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Usuario registrado exitosamente',
            'userId' => $usuario_id
        ]);
        
    } catch (PDOException $e) {
        $conexion->rollBack();
        throw new Exception("Error en la base de datos: " . $e->getMessage(), 500);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage(),
        'code' => $e->getCode()
    ]);
    
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'code' => $e->getCode() ?: 400
    ]);
    
} finally {
    if (isset($conexion)) {
        $conexion = null;
    }
}
?>