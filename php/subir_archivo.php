<?php
session_start();
include 'conexion_be.php'; // Asegúrate de que este archivo contiene la conexión PDO
include 'registrar_accion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_archivo = $_POST['nombre_archivo'];
    $archivo = $_FILES['archivo'];
    $folderSelect = $_POST['folder-select'];
    $folderName = $_POST['folder-name'] ?? null;

    try {
        // Verificar si se seleccionó una carpeta o se creará una nueva
        if ($folderSelect === 'new' && !empty($folderName)) {
            // Crear una nueva carpeta
            $queryCarpeta = "INSERT INTO carpetas (nombre_carpeta, fecha_creacion) VALUES (:nombre_carpeta, NOW())";
            $stmtCarpeta = $conexion->prepare($queryCarpeta);
            $stmtCarpeta->bindParam(':nombre_carpeta', $folderName, PDO::PARAM_STR);
            $stmtCarpeta->execute();

            // Obtener el ID de la carpeta recién creada
            $carpeta_id = $conexion->lastInsertId();
        } elseif ($folderSelect !== 'new' && !empty($folderSelect)) {
            // Usar la carpeta existente
            $carpeta_id = $folderSelect;
        } else {
            throw new Exception("Debes seleccionar una carpeta o crear una nueva.");
        }

        // Verificar si el archivo fue subido sin errores
        if ($archivo['error'] === UPLOAD_ERR_OK) {
            $tipo_archivo = $archivo['type'];
            $nombre_original = basename($archivo['name']);
            $extension = pathinfo($nombre_original, PATHINFO_EXTENSION);

            // Crear un nuevo nombre para el archivo
            $nuevo_nombre_archivo = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $nombre_archivo) . '.' . $extension;
            $ruta_archivo = 'uploads/' . $nuevo_nombre_archivo;

            // Crear el directorio 'uploads' si no existe
            if (!is_dir('uploads')) {
                mkdir('uploads', 0755, true);
            }

            // Mover el archivo subido a la carpeta de destino
            if (move_uploaded_file($archivo['tmp_name'], $ruta_archivo)) {
                // Guardar la información del archivo en la base de datos
                $query = "INSERT INTO archivos (nombre_archivo, tipo_archivo, ruta_archivo, carpeta_id, fecha_subida) 
                          VALUES (:nombre_archivo, :tipo_archivo, :ruta_archivo, :carpeta_id, NOW())";
                $stmt = $conexion->prepare($query);

                // Vincular parámetros
                $stmt->bindParam(':nombre_archivo', $nombre_archivo, PDO::PARAM_STR);
                $stmt->bindParam(':tipo_archivo', $tipo_archivo, PDO::PARAM_STR);
                $stmt->bindParam(':ruta_archivo', $ruta_archivo, PDO::PARAM_STR);
                $stmt->bindParam(':carpeta_id', $carpeta_id, PDO::PARAM_INT);

                // Ejecutar la consulta
                if ($stmt->execute()) {
                    // Registrar la acción en el sistema
                    if (isset($_SESSION['usuario_id'])) {
                        $usuario_id = $_SESSION['usuario_id'];
                        registrarAccion($conexion, $usuario_id, 'carga de archivo', 'Un nuevo archivo se ha cargado al sistema.');
                    }
                    echo "Archivo subido y registrado con éxito.";
                } else {
                    echo "Error al ejecutar la consulta.";
                }
            } else {
                echo "Error al mover el archivo subido.";
            }
        } else {
            echo "Error al subir el archivo. Código de error: " . $archivo['error'];
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>