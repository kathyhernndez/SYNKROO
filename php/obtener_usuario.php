<?php
include 'conexion_be.php';
session_start();

// Verificar permisos
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || ($_SESSION['id_roles'] != 1 && $_SESSION['id_roles'] != 2)) {
    die(json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']));
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    if (!$id) {
        die(json_encode(['success' => false, 'message' => 'ID de usuario no válido']));
    }

    try {
        $query = "SELECT id, nombre, apellido, correo, cedula FROM usuarios WHERE id = :id";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario) {
            echo json_encode(['success' => true, 'usuario' => $usuario]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error en la consulta: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Solicitud no válida']);
}

$conexion = null;
?>