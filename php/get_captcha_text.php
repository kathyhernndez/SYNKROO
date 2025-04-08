<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Limpiar buffer de salida
if (ob_get_length()) ob_clean();

// Forzar tipo de contenido
header('Content-Type: text/plain; charset=UTF-8');

// Generar texto si no existe
if (!isset($_SESSION['captcha_text'])) {
    require 'captcha.php';
}

echo implode(' ', str_split($_SESSION['captcha_text']));
exit();
?>