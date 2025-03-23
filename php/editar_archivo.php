<?php
include 'conexion_be.php'; // Asegúrate de que este archivo contiene la conexión PDO
include 'registrar_accion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nuevoNombre = trim($_POST['nombre_archivo']); // Eliminar espacios innecesarios al inicio y final

    try {
        // Validar el nombre del archivo
        if (!preg_match('/^[a-zA-Z0-9_\-\.\s]{1,100}$/', $nuevoNombre)) {
            echo "Error: El nombre del archivo contiene caracteres no permitidos o es demasiado largo. Solo se permiten letras, números, guiones bajos (_), guiones (-), puntos (.) y espacios. El nombre debe tener entre 1 y 100 caracteres.";
            exit; // Detener la ejecución del script
        }

        // Verificar si el nombre del archivo ya existe en la base de datos
        $queryVerificar = "SELECT COUNT(*) as count FROM archivos WHERE nombre_archivo = :nombre_archivo AND id != :id";
        $stmtVerificar = $conexion->prepare($queryVerificar);
        $stmtVerificar->bindParam(':nombre_archivo', $nuevoNombre, PDO::PARAM_STR);
        $stmtVerificar->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtVerificar->execute();
        $resultado = $stmtVerificar->fetch(PDO::FETCH_ASSOC);

        if ($resultado['count'] > 0) {
            // Si el nombre ya existe, mostrar un mensaje de error
            echo "Error: El nombre del archivo ya existe. Por favor, elige otro nombre.";
        } else {
            // Obtener la ruta actual del archivo desde la base de datos
            $query = "SELECT ruta_archivo FROM archivos WHERE id = :id";
            $stmt = $conexion->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $archivo = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($archivo) {
                $rutaActual = $archivo['ruta_archivo'];

                // Obtener la extensión del archivo
                $extension = pathinfo($rutaActual, PATHINFO_EXTENSION);

                // Crear la nueva ruta con el nuevo nombre y la misma extensión
                $nuevaRuta = dirname($rutaActual) . '/' . $nuevoNombre . '.' . $extension;

                // Renombrar el archivo físico en el servidor
                if (rename($rutaActual, $nuevaRuta)) {
                    // Actualizar el nombre del archivo y la ruta en la base de datos
                    $query = "UPDATE archivos SET nombre_archivo = :nombre_archivo, ruta_archivo = :ruta_archivo WHERE id = :id";
                    $stmt = $conexion->prepare($query);
                    $stmt->bindParam(':nombre_archivo', $nuevoNombre, PDO::PARAM_STR);
                    $stmt->bindParam(':ruta_archivo', $nuevaRuta, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

                    
                    if ($stmt->execute()) {
                        // Registrar la acción en el sistema
                        echo "Nombre del archivo y ruta actualizados con éxito.";
                    } else {
                        echo "Error al actualizar el nombre y la ruta del archivo en la base de datos.";
                    }
                } else {
                    echo "Error al renombrar el archivo en el servidor.";
                }
            } else {
                echo "No se encontró el archivo en la base de datos.";
            }
        }
    } catch (PDOException $e) {
        // Manejo de errores
        echo "Error: " . $e->getMessage();
    }
}
?>