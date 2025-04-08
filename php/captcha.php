<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Generar nuevo texto solo si no existe o se fuerza recarga
if (!isset($_SESSION['captcha_text']) || isset($_GET['reload'])) {
    // Crear un texto más legible para el audio
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $captcha_text = '';
    for ($i = 0; $i < 6; $i++) {
        $captcha_text .= $chars[rand(0, strlen($chars) - 1)]; 
    }
    $_SESSION['captcha_text'] = $captcha_text;
    $_SESSION['captcha'] = $captcha_text; // Mantener compatibilidad
}

// Crear imagen
$image = imagecreate(150, 50);
$bg = imagecolorallocate($image, 220, 220, 220);
$textcolor = imagecolorallocate($image, 0, 0, 0);

imagestring($image, 5, 50, 20, $_SESSION['captcha_text'], $textcolor);

// Ruido de fondo
for ($i = 0; $i < 100; $i++) {
    $noise_color = imagecolorallocate($image, rand(100, 255), rand(100, 255), rand(100, 255));
    imagesetpixel($image, rand(0, 150), rand(0, 80), $noise_color);
}

header('Content-type: image/png');
imagepng($image);
imagedestroy($image);
?>