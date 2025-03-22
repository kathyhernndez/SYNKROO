<?php
include 'conexion_be.php';

if (isset($_GET['ruta_archivo'])) {
    $rutaArchivo = $_GET['ruta_archivo'];
    $rutaCompleta = __DIR__ . "/" . $rutaArchivo; // Ruta absoluta al archivo

    try {
        // Verificar en la base de datos si el archivo existe
        $stmt = $conexion->prepare("SELECT ruta_archivo FROM archivos WHERE ruta_archivo = :ruta_archivo");
        $stmt->bindParam(':ruta_archivo', $rutaArchivo, PDO::PARAM_STR);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado && file_exists($rutaCompleta)) {
            // Configurar las cabeceras para forzar la descarga
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($rutaCompleta) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($rutaCompleta));
            flush(); // Limpiar el buffer de salida

            // Leer y enviar el archivo al cliente
            readfile($rutaCompleta);
            exit();
        } else {
            die('El archivo no existe o no es válido.');
        }
    } catch (PDOException $e) {
        die('Error al verificar el archivo en la base de datos: ' . $e->getMessage());
    }
} else {
    die('No se especificó ningún archivo.');
}
?>