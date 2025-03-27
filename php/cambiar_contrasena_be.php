<?php
include 'conexion_be.php';
include 'registrar_accion.php';
session_start();

// Verificar que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['status' => 'error', 'message' => 'Método no permitido']));
}

// Verificar sesión de recuperación
if (!isset($_SESSION['recovery_user_id']) || !isset($_SESSION['respuestas_correctas'])) {
    die(json_encode([
        'status' => 'error', 
        'message' => 'Solicitud no válida',
        'session_data' => $_SESSION // Para depuración
    ]));
}

// Depuración: Registrar los datos recibidos
error_log("Datos POST recibidos: " . print_r($_POST, true));

// Validar contraseñas
if (empty($_POST['nueva_contrasena']) || empty($_POST['confirmar_contrasena'])) {
    die(json_encode([
        'status' => 'error', 
        'message' => 'Ambos campos son requeridos',
        'received_data' => $_POST // Para depuración
    ]));
}

if ($_POST['nueva_contrasena'] !== $_POST['confirmar_contrasena']) {
    die(json_encode(['status' => 'error', 'message' => 'Las contraseñas no coinciden']));
}

if (strlen($_POST['nueva_contrasena']) < 8) {
    die(json_encode(['status' => 'error', 'message' => 'La contraseña debe tener al menos 8 caracteres']));
}

// Hashear la nueva contraseña
$contrasena_hash = password_hash($_POST['nueva_contrasena'], PASSWORD_DEFAULT);

try {
    // Actualizar contraseña
    $query = "UPDATE usuarios SET clave = :clave, modifcado_clave = NOW() WHERE id = :id";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':clave', $contrasena_hash, PDO::PARAM_STR);
    $stmt->bindParam(':id', $_SESSION['recovery_user_id'], PDO::PARAM_INT);
    
    if (!$stmt->execute()) {
        error_log("Error al ejecutar la consulta: " . print_r($stmt->errorInfo(), true));
        die(json_encode(['status' => 'error', 'message' => 'Error en la base de datos']));
    }

    // Verificar actualización (rowCount puede no ser confiable con UPDATE)
    $queryVerificacion = "SELECT COUNT(*) as count FROM usuarios WHERE id = :id AND clave = :clave";
    $stmtVerificacion = $conexion->prepare($queryVerificacion);
    $stmtVerificacion->bindParam(':id', $_SESSION['recovery_user_id'], PDO::PARAM_INT);
    $stmtVerificacion->bindParam(':clave', $contrasena_hash, PDO::PARAM_STR);
    $stmtVerificacion->execute();
    $resultado = $stmtVerificacion->fetch(PDO::FETCH_ASSOC);

    if ($resultado['count'] > 0) {
        // Registrar en bitácora
        if (isset($_SESSION['usuario_id'])) {
            registrarAccion($conexion, $_SESSION['usuario_id'], 'cambio de contraseña', 
                          'Usuario ID: ' . $_SESSION['recovery_user_id'] . ' cambió su contraseña');
        }
        
        // Limpiar sesión
        unset($_SESSION['recovery_user_id']);
        unset($_SESSION['respuestas_correctas']);
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Contraseña actualizada correctamente']);
    } else {
        die(json_encode(['status' => 'error', 'message' => 'No se pudo actualizar la contraseña']));
    }

} catch (PDOException $e) {
    error_log('Error en cambiar_contrasena_be.php: ' . $e->getMessage());
    die(json_encode([
        'status' => 'error', 
        'message' => 'Error al actualizar la contraseña',
        'error_details' => $e->getMessage()
    ]));
}
?>