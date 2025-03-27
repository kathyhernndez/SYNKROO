<?php
function registrarAccion($conexion, $usuario_id, $accion, $descripcion) {
    // Validación básica de parámetros
    if (!$conexion) {
        error_log("[Bitácora] Error: Conexión a BD no válida");
        return false;
    }

    if (empty($usuario_id) || !is_numeric($usuario_id)) {
        error_log("[Bitácora] Error: ID de usuario no válido: " . print_r($usuario_id, true));
        return false;
    }

    try {
        // Preparar la consulta SQL (incluyendo fecha_hora)
        $query = "INSERT INTO bitacora (usuario_id, accion, descripcion, fecha_hora) 
                 VALUES (:usuario_id, :accion, :descripcion, NOW())";
        
        $stmt = $conexion->prepare($query);
        
        if (!$stmt) {
            $error = $conexion->errorInfo();
            error_log("[Bitácora] Error al preparar consulta: " . print_r($error, true));
            return false;
        }

        // Vincular parámetros
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(':accion', $accion, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);

        // Ejecutar y verificar
        if ($stmt->execute()) {
            error_log("[Bitácora] Registro exitoso - Usuario: $usuario_id, Acción: $accion");
            return true;
        } else {
            $error = $stmt->errorInfo();
            error_log("[Bitácora] Error al ejecutar: " . print_r($error, true));
            return false;
        }
    } catch (PDOException $e) {
        error_log("[Bitácora] Excepción: " . $e->getMessage());
        return false;
    }
}
?>