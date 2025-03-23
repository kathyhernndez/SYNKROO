<?php
include 'conexion_be.php'; // Incluye el archivo de conexión a la base de datos
include 'registrar_accion.php'; // Incluye el archivo para registrar acciones
session_start();

if (isset($_GET['ruta_carpeta'])) {
    $rutaCarpeta = $_GET['ruta_carpeta'];
    $rutaCompleta = __DIR__ . "/" . $rutaCarpeta; // Ruta absoluta a la carpeta

    try {
        // Verificar en la base de datos si la carpeta existe
        $stmt = $conexion->prepare("SELECT ruta_carpeta FROM carpeta WHERE ruta_carpeta = :ruta_carpeta");
        $stmt->bindParam(':ruta_carpeta', $rutaCarpeta, PDO::PARAM_STR);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado && is_dir($rutaCompleta)) {
            // Crear un archivo ZIP temporal
            $zipNombre = tempnam(sys_get_temp_dir(), 'carpeta_') . '.zip';

            // Comprimir la carpeta usando el comando del sistema
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Windows: Usar el comando `tar` (requiere que tar esté instalado)
                $comando = "tar -czf \"$zipNombre\" -C \"" . dirname($rutaCompleta) . "\" \"" . basename($rutaCompleta) . "\"";
            } else {
                // Unix/Linux: Usar el comando `zip`
                $comando = "zip -r \"$zipNombre\" \"" . $rutaCompleta . "\"";
            }

            // Ejecutar el comando
            exec($comando, $output, $return_var);

            if ($return_var === 0) {
                // Registrar la acción en el sistema
                if (isset($_SESSION['usuario_id'])) {
                    $usuario_id = $_SESSION['usuario_id'];
                    registrarAccion($conexion, $usuario_id, 'descargar carpeta', "Se descargó la carpeta '$rutaCarpeta' como archivo ZIP.");
                }

                // Configurar las cabeceras para forzar la descarga
                header('Content-Description: File Transfer');
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . basename($rutaCarpeta) . '.zip"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($zipNombre));
                flush(); // Limpiar el buffer de salida

                // Leer y enviar el archivo ZIP al cliente
                readfile($zipNombre);

                // Eliminar el archivo ZIP temporal
                unlink($zipNombre);
                exit();
            } else {
                die('Error al crear el archivo ZIP.');
            }
        } else {
            die('La carpeta no existe o no es válida.');
        }
    } catch (PDOException $e) {
        die('Error al verificar la carpeta en la base de datos: ' . $e->getMessage());
    }
} else {
    die('No se especificó ninguna carpeta.');
}
?>