<?php
session_start();
include 'conexion_be.php';
include 'registrar_accion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folderSelect = $_POST['folder-select'] ?? null;
    $folderName = $_POST['folder-name'] ?? null;
    $archivos = $_FILES['archivo'] ?? null;

    // Verificar que se hayan enviado archivos
    if (!$archivos || empty($archivos['name'][0])) {
        echo json_encode(['status' => 'error', 'message' => 'No se han seleccionado archivos para subir.']);
        exit;
    }

    try {
        // Sanitizar y validar entradas
        $folderSelect = $folderSelect ? filter_var($folderSelect, FILTER_SANITIZE_STRING) : null;
        $folderName = $folderName ? filter_var($folderName, FILTER_SANITIZE_STRING) : null;

        // Validar selección de carpeta
        if ($folderSelect === '#' || empty($folderSelect)) {
            throw new Exception("No has seleccionado o creado una carpeta. Por favor, elige una carpeta existente o crea una nueva.");
        }

        if ($folderSelect === 'new') {
            if (empty($folderName)) {
                throw new Exception("Debes ingresar un nombre para la nueva carpeta.");
            }

            if (!preg_match('/^[a-zA-Z0-9_\- ]+$/', $folderName)) {
                throw new Exception("El nombre de la carpeta contiene caracteres no permitidos.");
            }

            $rutaCarpeta = 'uploads/' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $folderName);
            if (!is_dir($rutaCarpeta)) {
                if (!mkdir($rutaCarpeta, 0755, true)) {
                    throw new Exception("No se pudo crear la carpeta en el servidor.");
                }
            }

            $queryCarpeta = "INSERT INTO carpeta (nombre_carpeta, fecha_creacion, ruta_carpeta) 
                             VALUES (:nombre_carpeta, NOW(), :ruta_carpeta)";
            $stmtCarpeta = $conexion->prepare($queryCarpeta);
            $stmtCarpeta->bindParam(':nombre_carpeta', $folderName, PDO::PARAM_STR);
            $stmtCarpeta->bindParam(':ruta_carpeta', $rutaCarpeta, PDO::PARAM_STR);
            $stmtCarpeta->execute();
            $carpeta_id = $conexion->lastInsertId();
        } else {
            $carpeta_id = intval($folderSelect);
            $queryRuta = "SELECT ruta_carpeta FROM carpeta WHERE id = :id";
            $stmtRuta = $conexion->prepare($queryRuta);
            $stmtRuta->bindParam(':id', $carpeta_id, PDO::PARAM_INT);
            $stmtRuta->execute();
            $rutaCarpeta = $stmtRuta->fetchColumn();

            if (!$rutaCarpeta) {
                throw new Exception("La carpeta seleccionada no existe.");
            }
        }

        $uploadedFiles = [];
        foreach ($archivos['tmp_name'] as $key => $tmp_name) {
            $nombre_original = basename($archivos['name'][$key]);
            $tipo_archivo = $archivos['type'][$key];
            $tamaño_archivo = $archivos['size'][$key];
            $extension = pathinfo($nombre_original, PATHINFO_EXTENSION);

            $maxFileSize = 10 * 1024 * 1024;
            if ($tamaño_archivo > $maxFileSize) {
                throw new Exception("El archivo $nombre_original excede el tamaño máximo permitido (10 MB).");
            }

            $tiposPermitidos = [
                'image/jpeg', 'image/png',
                'application/pdf',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'audio/mpeg', 'video/mp4'
            ];

            if (!in_array($tipo_archivo, $tiposPermitidos)) {
                throw new Exception("El archivo $nombre_original no es un tipo de archivo permitido.");
            }

            $nuevo_nombre_archivo = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $nombre_original);
            $ruta_archivo = $rutaCarpeta . '/' . $nuevo_nombre_archivo;

            if (file_exists($ruta_archivo)) {
                throw new Exception("El archivo $nombre_original ya existe en la carpeta.");
            }

            if (move_uploaded_file($tmp_name, $ruta_archivo)) {
                $query = "INSERT INTO archivos (nombre_archivo, tipo_archivo, ruta_archivo, carpeta_id, fecha_subida) 
                          VALUES (:nombre_archivo, :tipo_archivo, :ruta_archivo, :carpeta_id, NOW())";
                $stmt = $conexion->prepare($query);
                $stmt->bindParam(':nombre_archivo', $nombre_original, PDO::PARAM_STR);
                $stmt->bindParam(':tipo_archivo', $tipo_archivo, PDO::PARAM_STR);
                $stmt->bindParam(':ruta_archivo', $ruta_archivo, PDO::PARAM_STR);
                $stmt->bindParam(':carpeta_id', $carpeta_id, PDO::PARAM_INT);
                $stmt->execute();
                $uploadedFiles[] = $nombre_original;
            } else {
                throw new Exception("Error al mover el archivo subido: $nombre_original");
            }
        }

        if (isset($_SESSION['usuario_id'])) {
            $usuario_id = $_SESSION['usuario_id'];
            $archivosSubidos = implode(", ", $uploadedFiles);
            registrarAccion($conexion, $usuario_id, 'carga de archivo', "Se han subido los archivos: $archivosSubidos");
        }

        echo json_encode(['status' => 'success', 'message' => "Archivo(s) subido(s) y registrado(s) con éxito: " . implode(", ", $uploadedFiles)]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'warning', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
}
?>