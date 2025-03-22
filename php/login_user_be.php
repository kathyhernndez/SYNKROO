<?php
// Iniciar el buffer de salida para evitar problemas con header()
ob_start();

// Habilitar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar la sesión
session_start();
include 'conexion_be.php';
include 'registrar_accion.php';

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar y sanitizar entradas
    $correo = filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_EMAIL);
    $clave = $_POST['clave']; // No se sanitiza para no afectar el hash
    $user_captcha = $_POST['captcha'];

    if (empty($correo)) {
        die("El correo es obligatorio.");
    }

    if (empty($clave)) {
        die("La contraseña es obligatoria.");
    }

    // Verificar el CAPTCHA
    if (!isset($_SESSION['captcha']) || $user_captcha !== $_SESSION['captcha']) {
        echo '
        <script>
            alert("CAPTCHA incorrecto. Inténtalo de nuevo.");
            window.location = "../public/index.php";
        </script>
        ';
        exit();
    }

    try {
        // Buscar el usuario por correo
        $query = "SELECT * FROM usuarios WHERE correo = :correo";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar si el usuario está bloqueado
            if ($row['estado'] == 0) {
                echo '
                <script>
                    alert("Tu cuenta está bloqueada. Contacta al administrador.");
                    window.location = "../public/index.php";
                </script>
                ';
                exit();
            }

            // Verificar la contraseña
            if (password_verify($clave, $row['clave'])) {
                // Contraseña válida
                $_SESSION['usuario_id'] = $row['id'];
                $_SESSION['nombre_completo'] = $row['nombre'] . " " . $row['apellido'];
                $_SESSION['id_roles'] = $row['id_roles'];
                $_SESSION['correo'] = $correo;
                $_SESSION['loggedin'] = true;

                // Reiniciar los intentos fallidos y el último intento
                $queryReset = "UPDATE usuarios SET intentos_fallidos = 0, ultimo_intento = NULL WHERE id = :id";
                $stmtReset = $conexion->prepare($queryReset);
                $stmtReset->bindParam(':id', $row['id'], PDO::PARAM_INT);
                $stmtReset->execute();

                // Registrar la acción de inicio de sesión
                registrarAccion($conexion, $_SESSION['usuario_id'], 'Inicio de sesión', 'El usuario ha iniciado sesión en el sistema.');

                // Redirigir al usuario a la página principal
                ob_end_flush(); // Limpiar el buffer antes de redirigir
                header("location: menu.php");
                exit();
            } else {
                // Contraseña incorrecta
                $intentos_fallidos = $row['intentos_fallidos'] + 1;
                $ultimo_intento = date('Y-m-d H:i:s');

                // Actualizar los intentos fallidos y el último intento
                $queryUpdate = "UPDATE usuarios SET intentos_fallidos = :intentos_fallidos, ultimo_intento = :ultimo_intento WHERE id = :id";
                $stmtUpdate = $conexion->prepare($queryUpdate);
                $stmtUpdate->bindParam(':intentos_fallidos', $intentos_fallidos, PDO::PARAM_INT);
                $stmtUpdate->bindParam(':ultimo_intento', $ultimo_intento, PDO::PARAM_STR);
                $stmtUpdate->bindParam(':id', $row['id'], PDO::PARAM_INT);
                $stmtUpdate->execute();

                // Bloquear al usuario si supera los 3 intentos fallidos
                if ($intentos_fallidos >= 3) {
                    $queryBlock = "UPDATE usuarios SET estado = 0 WHERE id = :id";
                    $stmtBlock = $conexion->prepare($queryBlock);
                    $stmtBlock->bindParam(':id', $row['id'], PDO::PARAM_INT);
                    $stmtBlock->execute();

                    // Registrar la acción de bloqueo
                    registrarAccion($conexion, $row['id'], 'Bloqueo de usuario', 'El usuario ha sido bloqueado por superar los intentos fallidos.');

                    echo '
                    <script>
                        alert("Tu cuenta ha sido bloqueada por superar los intentos fallidos. Contacta al administrador.");
                        window.location = "../public/index.php";
                    </script>
                    ';
                    exit();
                } else {
                    echo '
                    <script>
                        alert("Contraseña incorrecta. Te quedan ' . (3 - $intentos_fallidos) . ' intentos.");
                        window.location = "../public/index.php";
                    </script>
                    ';
                    exit();
                }
            }
        } else {
            // Usuario no encontrado
            echo '
            <script>
                alert("Usuario no registrado o las credenciales no coinciden. Por Favor Verifica e intenta Nuevamente");
                window.location = "../public/index.php";
            </script>
            ';
            exit();
        }
    } catch (PDOException $e) {
        // Manejar errores de PDO
        die("Error en la consulta: " . $e->getMessage());
    }

    // Limpiar el CAPTCHA de la sesión después de usarlo
    unset($_SESSION['captcha']);
}
?>