<?php
// Función para obtener el tamaño de una carpeta
function getFolderSize($dir) {
    if (!is_dir($dir)) {
        return 0; // Si la carpeta no existe, retornar 0
    }

    $size = 0;
    foreach (glob(rtrim($dir, '/') . '/*', GLOB_NOSORT) as $each) {
        $size += is_file($each) ? filesize($each) : getFolderSize($each);
    }
    return $size;
}

$uploadsDir = 'uploads/';
$maxSize = 5 * 1024 * 1024 * 1024; // 5GB en bytes
$alertSize = 4 * 1024 * 1024 * 1024; // 4GB en bytes

// Obtener el tamaño de la carpeta
$folderSize = getFolderSize($uploadsDir);

// Calcular el porcentaje de almacenamiento utilizado
$percentage = 0; // Valor por defecto
if ($maxSize > 0) { // Evitar división por cero
    $percentage = ($folderSize / $maxSize) * 100;
}

$buttonDisabled = false;
$message = '';

if ($folderSize >= $maxSize) {
    $message = 'El almacenamiento está lleno. No puedes subir más archivos.';
    $buttonDisabled = true;
} elseif ($folderSize >= $alertSize) {
    $message = 'El almacenamiento está casi lleno. Debes hacer un respaldo antes de que se llene.';
}
?>