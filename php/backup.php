<?php
date_default_timezone_set('America/Caracas');
session_start();
include 'conexion_be.php';
include 'registrar_accion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vaciarUploads = $_POST['vaciar_uploads'] ?? 0;

    // Nombre del archivo de respaldo de la base de datos
    $backupFile = 'respaldo_db_' . date('Y-m-d_H-i') . '.sql';

    // Comando para crear el respaldo de la base de datos
    $command = "mysqldump --host=localhost --user=root --password='' gestor_archivos > {$backupFile}";
    system($command);

    // Directorio donde se encuentran los archivos subidos
    $uploadsDir = 'uploads/';

    // Nombre del archivo ZIP
    $zipFile = 'respaldo_completo_' . date('Y-m-d_H-i') . '.zip';

    // Crear una nueva instancia de ZipArchive
    $zip = new ZipArchive();
    if ($zip->open($zipFile, ZipArchive::CREATE) !== TRUE) {
        echo json_encode(['success' => false, 'message' => 'No se puede abrir el archivo ZIP.']);
        exit();
    }

    // Añadir el archivo de respaldo de la base de datos al ZIP
    $zip->addFile($backupFile);

    // Función para añadir archivos a un archivo ZIP
    function addFilesToZip($dir, $zip, $pathInZip) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                if (is_dir($dir . $file)) {
                    addFilesToZip($dir . $file . '/', $zip, $pathInZip . $file . '/');
                } else {
                    $zip->addFile($dir . $file, $pathInZip . $file);
                }
            }
        }
    }

    // Añadir archivos subidos al ZIP
    addFilesToZip($uploadsDir, $zip, 'uploads/');

    // Cerrar el archivo ZIP
    $zip->close();

    // Eliminar el archivo de respaldo de la base de datos temporal
    unlink($backupFile);

    // Vaciar la carpeta de archivos subidos si se seleccionó la opción
    if ($vaciarUploads == 1) {
        array_map('unlink', glob("$uploadsDir/*"));
    }

    // Registrar la acción de respaldo
    if (isset($_SESSION['usuario_id'])) {
        $usuario_id = $_SESSION['usuario_id'];
        registrarAccion($conexion, $usuario_id, 'respaldo de archivos', 'El usuario ha hecho un Backup de los archivos del sistema.');
    }

    echo json_encode(['success' => true, 'message' => 'Respaldo completo creado con éxito.']);
    exit();
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit();
}
?>