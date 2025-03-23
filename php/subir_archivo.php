<?php
session_start();
include 'conexion_be.php'; // Incluye la conexión a la base de datos
include 'registrar_accion.php'; // Incluye la función para registrar acciones

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folderSelect = $_POST['folder-select'];
    $folderName = $_POST['folder-name'] ?? null;
    $archivos = $_FILES['archivo'];

    try {
        // Sanitizar y validar entradas
        $folderSelect = filter_var($folderSelect, FILTER_SANITIZE_STRING);
        $folderName = filter_var($folderName, FILTER_SANITIZE_STRING);

        // Verificar si se seleccionó una carpeta o se creará una nueva
        if ($folderSelect === 'new' && !empty($folderName)) {
            // Validar el nombre de la carpeta
            if (!preg_match('/^[a-zA-Z0-9_\- ]+$/', $folderName)) {
                throw new Exception("El nombre de la carpeta contiene caracteres no permitidos.");
            }

            // Crear una nueva carpeta en la base de datos
            $queryCarpeta = "INSERT INTO carpeta (nombre_carpeta, fecha_creacion, ruta_carpeta) 
                             VALUES (:nombre_carpeta, NOW(), :ruta_carpeta)";
            $stmtCarpeta = $conexion->prepare($queryCarpeta);

            // Crear la ruta de la carpeta en el servidor
            $rutaCarpeta = 'uploads/' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $folderName);
            if (!is_dir($rutaCarpeta)) {
                mkdir($rutaCarpeta, 0755, true);
            }

            // Vincular parámetros y ejecutar la consulta
            $stmtCarpeta->bindParam(':nombre_carpeta', $folderName, PDO::PARAM_STR);
            $stmtCarpeta->bindParam(':ruta_carpeta', $rutaCarpeta, PDO::PARAM_STR);
            $stmtCarpeta->execute();

            // Obtener el ID de la carpeta recién creada
            $carpeta_id = $conexion->lastInsertId();
        } elseif ($folderSelect !== 'new' && !empty($folderSelect)) {
            // Usar la carpeta existente
            $carpeta_id = intval($folderSelect); // Sanitizar como entero

            // Obtener la ruta de la carpeta existente
            $queryRuta = "SELECT ruta_carpeta FROM carpeta WHERE id = :id";
            $stmtRuta = $conexion->prepare($queryRuta);
            $stmtRuta->bindParam(':id', $carpeta_id, PDO::PARAM_INT);
            $stmtRuta->execute();
            $rutaCarpeta = $stmtRuta->fetchColumn();
        } else {
            throw new Exception("Debes seleccionar una carpeta o crear una nueva.");
        }

        // Procesar cada archivo subido
        foreach ($archivos['tmp_name'] as $key => $tmp_name) {
            $nombre_original = basename($archivos['name'][$key]);
            $tipo_archivo = $archivos['type'][$key];
            $tamaño_archivo = $archivos['size'][$key];
            $extension = pathinfo($nombre_original, PATHINFO_EXTENSION);

            // Validar el tamaño del archivo (ejemplo: máximo 10 MB)
            $maxFileSize = 10 * 1024 * 1024; // 10 MB
            if ($tamaño_archivo > $maxFileSize) {
                throw new Exception("El archivo $nombre_original excede el tamaño máximo permitido (10 MB).");
            }

            // Validar el tipo de archivo
            $tiposPermitidos = [
                // Imágenes
                'image/jpeg', // JPEG/JPG
                'image/png',  // PNG

                // Documentos
                'application/pdf', // PDF
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // Word DOCX
                'application/vnd.openxmlformats-officedocument.presentationml.presentation', // PowerPoint PPTX
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // Excel XLSX

                // Audios
                'audio/mpeg', // MP3

                // Videos
                'video/mp4', // MP4
            ];

            if (!in_array($tipo_archivo, $tiposPermitidos)) {
                throw new Exception("El archivo $nombre_original no es un tipo de archivo permitido.");
            }

            // Crear un nuevo nombre para el archivo
            $nuevo_nombre_archivo = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $nombre_original);
            $ruta_archivo = $rutaCarpeta . '/' . $nuevo_nombre_archivo;

            // Verificar si el archivo ya existe
            if (file_exists($ruta_archivo)) {
                throw new Exception("El archivo $nombre_original ya existe en la carpeta.");
            }

            // Mover el archivo subido a la carpeta de destino
            if (move_uploaded_file($tmp_name, $ruta_archivo)) {
                // Guardar la información del archivo en la base de datos
                $query = "INSERT INTO archivos (nombre_archivo, tipo_archivo, ruta_archivo, carpeta_id, fecha_subida) 
                          VALUES (:nombre_archivo, :tipo_archivo, :ruta_archivo, :carpeta_id, NOW())";
                $stmt = $conexion->prepare($query);

                // Vincular parámetros
                $stmt->bindParam(':nombre_archivo', $nombre_original, PDO::PARAM_STR);
                $stmt->bindParam(':tipo_archivo', $tipo_archivo, PDO::PARAM_STR);
                $stmt->bindParam(':ruta_archivo', $ruta_archivo, PDO::PARAM_STR);
                $stmt->bindParam(':carpeta_id', $carpeta_id, PDO::PARAM_INT);

                // Ejecutar la consulta
                $stmt->execute();
            } else {
                throw new Exception("Error al mover el archivo subido: $nombre_original");
            }
        }

        // Registrar la acción en el sistema
        if (isset($_SESSION['usuario_id'])) {
            $usuario_id = $_SESSION['usuario_id'];
            registrarAccion($conexion, $usuario_id, 'carga de archivo', 'Un nuevo archivo se ha cargado al sistema.');
        }

        echo "Archivo(s) subido(s) y registrado(s) con éxito.";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>