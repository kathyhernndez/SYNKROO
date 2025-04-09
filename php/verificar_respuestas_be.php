<?php
include 'conexion_be.php';
include 'registrar_accion.php';
session_start();

// Verificar que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['status' => 'error', 'message' => 'Método no permitido']));
}

// Verificar sesión de recuperación
if (!isset($_SESSION['recovery_user_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'Solicitud no válida']));
}

// Validar respuestas
if (empty($_POST['respuesta_1']) || empty($_POST['respuesta_2']) || empty($_POST['respuesta_3'])) {
    error_log("Datos recibidos: " . print_r($_POST, true));
    die(json_encode([
        'status' => 'error', 
        'message' => 'Debes responder todas las preguntas',
        'received_data' => $_POST // Para depuración
    ]));
}

try {
    // Obtener respuestas correctas (encriptadas)
    $query = "SELECT respuesta_1, respuesta_2, respuesta_3 FROM recuperar_contrasena 
              WHERE id_usuario = :id_usuario";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':id_usuario', $_SESSION['recovery_user_id'], PDO::PARAM_INT);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        die(json_encode(['status' => 'error', 'message' => 'No se encontraron preguntas de seguridad']));
    }

    // Normalizar respuestas (trim y lowercase para hacer la comparación más flexible)
    $respuesta1 = strtolower(trim($_POST['respuesta_1']));
    $respuesta2 = strtolower(trim($_POST['respuesta_2']));
    $respuesta3 = strtolower(trim($_POST['respuesta_3']));

    // Verificar respuestas usando password_verify()
    $respuestas_correctas = (
        password_verify($respuesta1, $result['respuesta_1']) &&
        password_verify($respuesta2, $result['respuesta_2']) &&
        password_verify($respuesta3, $result['respuesta_3'])
    );

    if (!$respuestas_correctas) {
        // Registrar intento fallido
        if (isset($_SESSION['usuario_id'])) {
            registrarAccion($conexion, $_SESSION['usuario_id'], 'recuperación fallida', 
                          'Respuestas incorrectas para usuario ID: ' . $_SESSION['recovery_user_id']);
        }
        die(json_encode(['status' => 'error', 'message' => 'Respuestas incorrectas']));
    }

    // Marcar respuestas correctas
    $_SESSION['respuestas_correctas'] = true;

    header('Content-Type: application/json');
    echo json_encode(['status' => 'success']);

} catch (PDOException $e) {
    error_log('Error en verificar_respuestas_be.php: ' . $e->getMessage());
    die(json_encode(['status' => 'error', 'message' => 'Error al procesar la solicitud']));
}
?>