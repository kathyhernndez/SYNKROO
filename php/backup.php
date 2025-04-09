<?php
session_set_cookie_params([
    'lifetime' => 1200, // 20 minutos
    'path' => '/',
    'domain' => '', // Dejar vacío para que funcione en todos los subdominios
    'secure' => false, // Cambiar a false si no usas HTTPS
    'httponly' => true,
    'samesite' => 'Lax' // Menos restrictivo que 'Strict'
]);

date_default_timezone_set('America/Caracas');
session_start();
include 'conexion_be.php';
include 'registrar_accion.php';


// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo '
    <script>
    alert("Por Favor debes Iniciar Sesion");
    window.location = "../public/index.php";
    </script>
    ';
    session_destroy();
    die();
}

// Verificar si el usuario tiene el rol adecuado (id_roles igual a 1 o 2)
if (!isset($_SESSION['id_roles']) || ($_SESSION['id_roles'] != 1 && $_SESSION['id_roles'] != 2)) {
    echo '
    <script>
    alert("No tienes permisos para acceder a esta vista");
    window.location = "menu.php";
    </script>
    ';
    die();
}



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Nombre del archivo de respaldo de la base de datos
    $backupFile = 'respaldo_db_' . date('Y-m-d_H-i') . '.sql';

    // Comando para crear el respaldo de la base de datos
    $command = "mysqldump --host=localhost --user=root --password='' synkroo > {$backupFile}";
    system($command);

    // Directorio donde se encuentran los archivos subidos
    $uploadsDir = 'uploads/';

    // Nombre del archivo ZIP
    $zipFile = 'respaldo_completo_' . date('Y-m-d_H-i') . '.zip';

    // Crear una nueva instancia de ZipArchive
    $zip = new ZipArchive();
    if ($zip->open($zipFile, ZipArchive::CREATE) !== TRUE) {
        $_SESSION['message'] = "No se puede abrir el archivo ZIP.";
        echo '<script>
            alert("No se puede abrir el archivo ZIP.");
            window.location.href = "menu.php";
        </script>';
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

    // Enviar el archivo ZIP al navegador para descarga
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . basename($zipFile) . '"');
    header('Content-Length: ' . filesize($zipFile));

    // Establecer una cookie para indicar que la descarga ha finalizado
    setcookie('download_finished', 'true', time() + 10, '/'); // La cookie expira en 10 segundos

    // Enviar el archivo al navegador
    readfile($zipFile);

    // Eliminar el archivo ZIP del servidor después de enviarlo al cliente
    unlink($zipFile);

    $_SESSION['message'] = "Respaldo completo creado con éxito.";

    // Registrar la acción de respaldo
    if (isset($_SESSION['usuario_id'])) {
        $usuario_id = $_SESSION['usuario_id'];
        registrarAccion($conexion, $usuario_id, 'respaldo de archivos', 'El usuario ha hecho un Backup de los archivos del sistema.');
    }

    exit();
} else {
    // Mostrar el formulario de confirmación
    echo '
    <title>Respaldo de Archivos</title>
    <link rel="shortcut icon" href="../assets/image/favicon.png" />
    <link rel="stylesheet" href="../assets/css/main.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <div class="container mt-5">
        <form method="post" action="" class="backup-form">
            <div class="form-group">
                <p class="form-text">¿Deseas vaciar la carpeta de archivos subidos antes de hacer el backup?</p>
                <div class="form-check">
                    <input class="form-check-input" type="radio" id="si" name="vaciar_uploads" value="1" required>
                    <label class="form-check-label" for="si">Sí</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" id="no" name="vaciar_uploads" value="0" required>
                    <label class="form-check-label" for="no">No</label>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-dark backup-btn">Hacer Backup</button>
                <a href="menu.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
    <script src="../assets/js/main.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Verificar si la descarga ha finalizado
        function checkDownload() {
            if (document.cookie.indexOf("download_finished=true") !== -1) {
                // Redirigir al usuario después de la descarga
                window.location.href = "menu.php";
            } else {
                // Reintentar después de 1 segundo
                setTimeout(checkDownload, 1000);
            }
        }

        // Iniciar la verificación después de enviar el formulario
        $(document).ready(function() {
            $("form").on("submit", function() {
                setTimeout(checkDownload, 1000); // Comenzar a verificar después de 1 segundo
            });
        });
    </script>';
}
   
?>