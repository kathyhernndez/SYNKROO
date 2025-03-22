<?php
include 'conexion_be.php'; // Asegúrate de que este archivo contiene la conexión PDO
include 'registrar_accion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_archivo = $_POST['nombre_archivo'];
    $archivo = $_FILES['archivo'];

    try {
        // Verificar si el nombre del archivo ya existe en la base de datos
        $queryVerificar = "SELECT COUNT(*) as count FROM archivos WHERE nombre_archivo = :nombre_archivo";
        $stmtVerificar = $conexion->prepare($queryVerificar);
        $stmtVerificar->bindParam(':nombre_archivo', $nombre_archivo, PDO::PARAM_STR);
        $stmtVerificar->execute();
        $resultado = $stmtVerificar->fetch(PDO::FETCH_ASSOC);

        if ($resultado['count'] > 0) {
            // Si el nombre ya existe, mostrar un mensaje de error
            echo "Error: El nombre del archivo ya existe. Por favor, elige otro nombre.";
        } else {
            // Verificar si el archivo fue subido sin errores
            if ($archivo['error'] === UPLOAD_ERR_OK) {
                $tipo_archivo = $archivo['type'];
                $nombre_original = basename($archivo['name']);
                $extension = pathinfo($nombre_original, PATHINFO_EXTENSION); // Obtener la extensión del archivo

                // Crear un nuevo nombre para el archivo (solo el nombre ingresado por el usuario)
                $nuevo_nombre_archivo = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $nombre_archivo) . '.' . $extension;
                $ruta_archivo = 'uploads/' . $nuevo_nombre_archivo;

                // Crear el directorio 'uploads' si no existe
                if (!is_dir('uploads')) {
                    mkdir('uploads', 0755, true);
                }

                // Mover el archivo subido a la carpeta de destino
                if (move_uploaded_file($archivo['tmp_name'], $ruta_archivo)) {
                    // Guardar la información del archivo en la base de datos
                    $query = "INSERT INTO archivos (nombre_archivo, tipo_archivo, ruta_archivo) VALUES (:nombre_archivo, :tipo_archivo, :ruta_archivo)";
                    $stmt = $conexion->prepare($query);

                    // Vincular parámetros
                    $stmt->bindParam(':nombre_archivo', $nombre_archivo, PDO::PARAM_STR);
                    $stmt->bindParam(':tipo_archivo', $tipo_archivo, PDO::PARAM_STR);
                    $stmt->bindParam(':ruta_archivo', $ruta_archivo, PDO::PARAM_STR);

                    // Ejecutar la consulta
                    if ($stmt->execute()) {
                        // Registrar la acción en el sistema
                        registrarAccion($_SESSION['nombre_completo'], 'carga de archivo', 'Un nuevo archivo se ha cargado al sistema.');
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
        }
    } catch (PDOException $e) {
        // Manejo de errores
        echo "Error: " . $e->getMessage();
    }
}
?>