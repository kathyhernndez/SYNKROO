<?php
include 'conexion_be.php';
session_start();

// Verificar que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['status' => 'error', 'message' => 'Método no permitido']));
}

// Validar datos de entrada
if (empty($_POST['recoverUser']) || empty($_POST['recoverCedula']) || empty($_POST['recoverCaptcha'])) {
    die(json_encode(['status' => 'error', 'message' => 'Todos los campos son requeridos']));
}

// Verificar CAPTCHA
if ($_POST['recoverCaptcha'] !== $_SESSION['captcha']) {
    die(json_encode(['status' => 'error', 'message' => 'CAPTCHA incorrecto']));
}

// Sanitizar entradas
$correo = filter_var($_POST['recoverUser'], FILTER_SANITIZE_EMAIL);
$cedula = filter_var($_POST['recoverCedula'], FILTER_SANITIZE_NUMBER_INT);

try {
    // Verificar usuario en la base de datos
    $query = "SELECT id FROM usuarios WHERE correo = :correo AND cedula = :cedula";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
    $stmt->bindParam(':cedula', $cedula, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        die(json_encode(['status' => 'error', 'message' => 'No se encontró un usuario con esos datos']));
    }

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Obtener preguntas de seguridad
    $query_preguntas = "SELECT pregunta_1, pregunta_2, pregunta_3 FROM recuperar_contrasena WHERE id_usuario = :id_usuario";
    $stmt_preguntas = $conexion->prepare($query_preguntas);
    $stmt_preguntas->bindParam(':id_usuario', $usuario['id'], PDO::PARAM_INT);
    $stmt_preguntas->execute();
    
    $preguntas = $stmt_preguntas->fetch(PDO::FETCH_ASSOC);

    if (!$preguntas) {
        die(json_encode(['status' => 'error', 'message' => 'No se configuraron preguntas de seguridad para este usuario']));
    }

    // Guardar ID de usuario en sesión
    $_SESSION['recovery_user_id'] = $usuario['id'];


    // Devolver preguntas de seguridad
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'pregunta_1' => $preguntas['pregunta_1'],
        'pregunta_2' => $preguntas['pregunta_2'],
        'pregunta_3' => $preguntas['pregunta_3']
    ]);

} catch (PDOException $e) {
    error_log('Error en recuperar_contrasena_be.php: ' . $e->getMessage());
    die(json_encode(['status' => 'error', 'message' => 'Error al procesar la solicitud']));
}
?>