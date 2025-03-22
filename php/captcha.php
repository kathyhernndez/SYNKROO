<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Crear una imagen de 150x50 píxeles
$image = imagecreate(150, 50);

// Color de fondo (gris claro)
$bg = imagecolorallocate($image, 220, 220, 220);

// Color del texto (negro)
$textcolor = imagecolorallocate($image, 0, 0, 0);

// Generar un texto aleatorio para el CAPTCHA
$captcha_text = substr(md5(uniqid()), 0, 6);
$_SESSION['captcha'] = $captcha_text;

// Escribir el texto en la imagen 
imagestring($image, 5, 50, 20, $captcha_text, $textcolor);

// Añadir ruido (puntos aleatorios)
for ($i = 0; $i < 100; $i++) {
    $noise_color = imagecolorallocate($image, rand(100, 255), rand(100, 255), rand(100, 255));
    imagesetpixel($image, rand(0, 150), rand(0, 50), $noise_color);
}

// Enviar la imagen como PNG
header('Content-type: image/png');
imagepng($image);

// Liberar memoria
imagedestroy($image);
?>