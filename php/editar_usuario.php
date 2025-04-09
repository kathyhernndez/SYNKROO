<?php
include 'conexion_be.php';
include 'registrar_accion.php';
session_start();

// Verificar que el usuario tenga permisos para editar
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || ($_SESSION['id_roles'] != 1 && $_SESSION['id_roles'] != 2)) {
    die(json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $apellido = isset($_POST['apellido']) ? trim($_POST['apellido']) : '';
    $correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
    $cedula = isset($_POST['cedula']) ? trim($_POST['cedula']) : '';
    $clave = isset($_POST['clave']) ? $_POST['clave'] : null;

    // Validaciones básicas
    if ($id <= 0 || empty($nombre) || empty($apellido) || empty($correo) || empty($cedula)) {
        die(json_encode([
            'success' => false, 
            'message' => 'Todos los campos son requeridos excepto la contraseña.'
        ]));
    }

    // Validar formato de cédula (solo números, entre 6 y 12 dígitos)
    if (!preg_match('/^\d{6,12}$/', $cedula)) {
        die(json_encode(['success' => false, 'message' => 'La cédula debe contener entre 6 y 12 dígitos numéricos.']));
    }

    // Validar formato de correo con los nuevos requisitos
    if (!preg_match('/^[a-zA-Z0-9._%+-]{2,}@[a-zA-Z0-9.-]{5,}\.[a-zA-Z]{2,}$/', $correo)) {
        die(json_encode(['success' => false, 'message' => 'El correo electrónico no tiene un formato válido. Debe tener al menos 2 caracteres antes de la @, al menos 5 caracteres en el dominio y una extensión de dominio válida.']));
    }

    // Validar contraseña si se proporcionó
    if ($clave) {
        if (strlen($clave) < 16) {
            die(json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 16 caracteres.']));
        }
        if (!preg_match('/[A-Z]/', $clave)) {
            die(json_encode(['success' => false, 'message' => 'La contraseña debe contener al menos una letra mayúscula.']));
        }
        if (!preg_match('/[a-z]/', $clave)) {
            die(json_encode(['success' => false, 'message' => 'La contraseña debe contener al menos una letra minúscula.']));
        }
        if (!preg_match('/[0-9]/', $clave)) {
            die(json_encode(['success' => false, 'message' => 'La contraseña debe contener al menos un número.']));
        }
    }

    try {
        // Verificar que el correo no esté en uso por otro usuario
        $queryCheck = "SELECT id FROM usuarios WHERE (correo = :correo OR cedula = :cedula) AND id != :id";
        $stmtCheck = $conexion->prepare($queryCheck);
        $stmtCheck->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmtCheck->bindParam(':cedula', $cedula, PDO::PARAM_STR);
        $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtCheck->execute();

        if ($stmtCheck->rowCount() > 0) {
            die(json_encode(['success' => false, 'message' => 'El correo o cédula ya están en uso por otro usuario.']));
        }

        // Si se proporciona una nueva contraseña, encriptarla
        $clave_encriptada = !empty($clave) ? password_hash($clave, PASSWORD_DEFAULT) : null;

        // Actualizar el registro en la base de datos
        $query = "UPDATE usuarios SET 
                    nombre = :nombre, 
                    apellido = :apellido, 
                    correo = :correo, 
                    cedula = :cedula" . 
                 ($clave_encriptada ? ", clave = :clave" : "") . " 
                  WHERE id = :id";
        
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':apellido', $apellido, PDO::PARAM_STR);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->bindParam(':cedula', $cedula, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($clave_encriptada) {
            $stmt->bindParam(':clave', $clave_encriptada, PDO::PARAM_STR);
        }

        if ($stmt->execute()) {
            // Registrar la acción
            registrarAccion($conexion, $_SESSION['usuario_id'], 'editar usuario', 'Se actualizó el usuario ID: ' . $id);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Usuario actualizado con éxito.',
                'updated_fields' => $clave_encriptada ? 'Datos básicos y contraseña' : 'Datos básicos'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se realizaron cambios en el usuario.']);
        }
    } catch (PDOException $e) {
        error_log('Error al editar usuario: ' . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Error en la base de datos al actualizar el usuario.'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}

$conexion = null;
?>