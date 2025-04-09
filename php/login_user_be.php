<?php
// Configuración segura de cookies
session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,     // Forzado aunque no haya HTTPS
    'httponly' => true,   // Siempre activo
    'samesite' => 'Strict' // Opcional: Protección CSRF
]);

ob_start();
session_start();

include 'conexion_be.php';
include 'registrar_accion.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_EMAIL);
    $clave = $_POST['clave'];
    $user_captcha = $_POST['captcha'];

    // Verificar el CAPTCHA primero
    if (!isset($_SESSION['captcha'])) {  // Aquí estaba el error - faltaba cerrar el paréntesis
        die(json_encode(['success' => false, 'message' => 'Sesión expirada, recarga la página']));
    }
    
    if (strtolower($user_captcha) !== strtolower($_SESSION['captcha'])) {
        die(json_encode(['success' => false, 'message' => 'CAPTCHA incorrecto. Inténtalo de nuevo.']));
    }

    try {
        $query = "SELECT * FROM usuarios WHERE correo = :correo";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row['estado'] == 0) {
                die(json_encode(['success' => false, 'message' => 'Tu cuenta está bloqueada. Contacta al administrador.']));
            }

            if (password_verify($clave, $row['clave'])) {
                $_SESSION['usuario_id'] = $row['id'];
                $_SESSION['nombre_completo'] = $row['nombre'] . " " . $row['apellido'];
                $_SESSION['id_roles'] = $row['id_roles'];
                $_SESSION['correo'] = $correo;
                $_SESSION['loggedin'] = true;

                $queryReset = "UPDATE usuarios SET intentos_fallidos = 0, ultimo_intento = NULL WHERE id = :id";
                $stmtReset = $conexion->prepare($queryReset);
                $stmtReset->bindParam(':id', $row['id'], PDO::PARAM_INT);
                $stmtReset->execute();

                registrarAccion($conexion, $_SESSION['usuario_id'], 'Inicio de sesión', 'El usuario ha iniciado sesión en el sistema.');

                unset($_SESSION['captcha']);
                ob_end_clean();
                die(json_encode(['success' => true, 'redirect' => '../php/menu.php']));
            } else {
                $intentos_fallidos = $row['intentos_fallidos'] + 1;
                $ultimo_intento = date('Y-m-d H:i:s');

                $queryUpdate = "UPDATE usuarios SET intentos_fallidos = :intentos_fallidos, ultimo_intento = :ultimo_intento WHERE id = :id";
                $stmtUpdate = $conexion->prepare($queryUpdate);
                $stmtUpdate->bindParam(':intentos_fallidos', $intentos_fallidos, PDO::PARAM_INT);
                $stmtUpdate->bindParam(':ultimo_intento', $ultimo_intento, PDO::PARAM_STR);
                $stmtUpdate->bindParam(':id', $row['id'], PDO::PARAM_INT);
                $stmtUpdate->execute();

                if ($intentos_fallidos >= 3) {
                    $queryBlock = "UPDATE usuarios SET estado = 0 WHERE id = :id";
                    $stmtBlock = $conexion->prepare($queryBlock);
                    $stmtBlock->bindParam(':id', $row['id'], PDO::PARAM_INT);
                    $stmtBlock->execute();

                    registrarAccion($conexion, $row['id'], 'Bloqueo de usuario', 'El usuario ha sido bloqueado por superar los intentos fallidos.');

                    die(json_encode(['success' => false, 'message' => 'Tu cuenta ha sido bloqueada por superar los intentos fallidos. Contacta al administrador.']));
                } else {
                    die(json_encode(['success' => false, 'message' => 'Contraseña incorrecta. Te quedan ' . (3 - $intentos_fallidos) . ' intentos.']));
                }
            }
        } else {
            die(json_encode(['success' => false, 'message' => 'Usuario no registrado o las credenciales no coinciden. Por Favor Verifica e intenta Nuevamente']));
        }
    } catch (PDOException $e) {
        die(json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]));
    }
}
?>