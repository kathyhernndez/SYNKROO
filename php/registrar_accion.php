<?php
// Archivo: registrar_accion.php

function registrarAccion($conexion, $usuario_id, $accion, $descripcion) {
    try {
        // Preparar la consulta SQL
        $stmt = $conexion->prepare("INSERT INTO bitacora (usuario_id, accion, descripcion) VALUES (:usuario_id, :accion, :descripcion)");
        
        // Vincular los parámetros
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(':accion', $accion, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        
        // Ejecutar la consulta
        $stmt->execute();
        
        // Cerrar el statement
        $stmt = null;
    } catch (PDOException $e) {
        // Manejo de errores
        die("Error al registrar la acción: " . $e->getMessage());
    }
}
?>