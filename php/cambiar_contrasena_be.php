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
        'session_data' => $_SESSION
    ]));
}

// Validar contraseñas
if (empty($_POST['nueva_contrasena']) || empty($_POST['confirmar_contrasena'])) {
    die(json_encode(['status' => 'error', 'message' => 'Ambos campos son requeridos']));
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
    // Iniciar transacción para asegurar integridad
    $conexion->beginTransaction();
    
    // Actualizar contraseña
    $query = "UPDATE usuarios SET clave = :clave, modifcado_clave = NOW() WHERE id = :id";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':clave', $contrasena_hash, PDO::PARAM_STR);
    $stmt->bindParam(':id', $_SESSION['recovery_user_id'], PDO::PARAM_INT);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar actualización de contraseña");
    }

    // Verificar actualización
    $queryVerificacion = "SELECT 1 FROM usuarios WHERE id = :id AND clave = :clave LIMIT 1";
    $stmtVerificacion = $conexion->prepare($queryVerificacion);
    $stmtVerificacion->bindParam(':id', $_SESSION['recovery_user_id'], PDO::PARAM_INT);
    $stmtVerificacion->bindParam(':clave', $contrasena_hash, PDO::PARAM_STR);
    $stmtVerificacion->execute();
    
    if ($stmtVerificacion->fetch()) {
        // Registrar en bitácora - ¡AHORA DESPUÉS DE VERIFICAR!
        $usuarioBitacora = $_SESSION['usuario_id'] ?? $_SESSION['recovery_user_id'];
        $registroExitoso = registrarAccion(
            $conexion,
            $usuarioBitacora,
            'cambio de contraseña',
            'Usuario ID: ' . $_SESSION['recovery_user_id'] . ' cambió su contraseña'
        );
        
        if (!$registroExitoso) {
            error_log("Advertencia: No se pudo registrar en bitácora pero la contraseña se cambió");
        }
        
        // Limpiar sesión
        unset($_SESSION['recovery_user_id']);
        unset($_SESSION['respuestas_correctas']);
        
        // Confirmar transacción
        $conexion->commit();
        
        // Responder éxito
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Contraseña actualizada correctamente']);
    } else {
        throw new Exception("La contraseña no se actualizó correctamente");
    }
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    if (isset($conexion) && $conexion->inTransaction()) {
        $conexion->rollBack();
    }
    
    error_log('Error en cambiar_contrasena_be.php: ' . $e->getMessage());
    die(json_encode([
        'status' => 'error', 
        'message' => 'Error al actualizar la contraseña',
        'error_details' => $e->getMessage()
    ]));
}
?>