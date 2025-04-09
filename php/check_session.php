<?php
session_set_cookie_params([
    'lifetime' => 1200, // 20 minutos en segundos
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();

include 'conexion_be.php';

// Verificar si la sesión está activa y el tiempo de inactividad
if (isset($_SESSION['LAST_ACTIVITY'])) {
    $inactive_time = time() - $_SESSION['LAST_ACTIVITY'];
    $warning_threshold = 900; // 15 minutos en segundos
    $expire_threshold = 1200; // 20 minutos en segundos
    
    if ($inactive_time > $expire_threshold) {
        // Marcar sesión como expirada
        $_SESSION['session_expired'] = true;
        session_write_close();
        echo json_encode(['status' => 'expired']);
        exit();
    } elseif ($inactive_time > $warning_threshold) {
        echo json_encode([
            'status' => 'warning',
            'remaining_time' => $expire_threshold - $inactive_time
        ]);
        exit();
    }
}

// Actualizar tiempo de última actividad
$_SESSION['LAST_ACTIVITY'] = time();
echo json_encode(['status' => 'active']);
?>