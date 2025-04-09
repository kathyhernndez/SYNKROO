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

// Actualizar el tiempo de última actividad
$_SESSION['LAST_ACTIVITY'] = time();
echo json_encode(['success' => true]);
?>