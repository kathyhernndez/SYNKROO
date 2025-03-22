<?php
// eliminar_archivo.php

// Incluir la conexión a la base de datos
require 'conexion_be.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener el ID del archivo desde la solicitud POST
    $id = $_POST['id'];

    try {
        // 1. Obtener la ruta del archivo desde la base de datos
        $query = "SELECT ruta_archivo FROM archivos WHERE id = :id";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $archivo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($archivo) {
            $rutaArchivo = $archivo['ruta_archivo'];

            // 2. Eliminar el archivo del servidor
            if (file_exists($rutaArchivo)) {
                if (unlink($rutaArchivo)) { // Eliminar el archivo
                    // 3. Eliminar el registro de la base de datos
                    $queryDelete = "DELETE FROM archivos WHERE id = :id";
                    $stmtDelete = $conexion->prepare($queryDelete);
                    $stmtDelete->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmtDelete->execute();

                    echo "Archivo eliminado correctamente.";
                } else {
                    echo "Error al eliminar el archivo del servidor.";
                }
            } else {
                echo "El archivo no existe en el servidor.";
            }
        } else {
            echo "No se encontró el archivo en la base de datos.";
        }
    } catch (PDOException $e) {
        // Manejo de errores de la base de datos
        echo "Error en la base de datos: " . $e->getMessage();
    }
} else {
    echo "Método no permitido.";
}
?>