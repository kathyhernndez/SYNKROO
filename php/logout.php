<?php
session_start();
include 'conexion_be.php';
include 'registrar_accion.php';

// Registrar la acción de cierre de sesión
if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    registrarAccion($conexion, $usuario_id, 'cierre de sesión', 'El usuario ha cerrado sesión en el sistema.');
}

// Destruir la sesión y las cookies
session_unset();
session_destroy();
setcookie('PHPSESSID', '', time() - 3600, '/');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cierre de Sesión</title>
    <link rel="shortcut icon" href="../assets/image/favicon.png" />
    <link rel="stylesheet" href="../assets/css/main.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Estilos específicos para la página de logout */
        .logout-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(to right, #ff8c42, #ff6b35);
            color: white;
            text-align: center;
            flex-direction: column;
        }
        
        .logout-message {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 90%;
            color: #333;
            animation: fadeInUp 0.5s ease;
        }
        
        .logout-icon {
            font-size: 50px;
            color: #ff8c42;
            margin-bottom: 20px;
        }
        
        .logout-title {
            font-size: 24px;
            margin-bottom: 15px;
            color: #ff8c42;
        }
        
        .logout-text {
            margin-bottom: 25px;
            font-size: 16px;
        }
        
        .logout-button {
            background-color: #ff8c42;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .logout-button:hover {
            background-color: #e67e22;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Modo oscuro */
        .dark-mode .logout-container {
            background: linear-gradient(to right, #333, #222);
        }
        
        .dark-mode .logout-message {
            background-color: #444;
            color: white;
        }
        
        .dark-mode .logout-title {
            color: #ff8c42;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="logout-message">
            <div class="logout-icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <h2 class="logout-title">Sesión cerrada exitosamente</h2>
            <p class="logout-text">Has cerrado tu sesión de manera segura. Para volver a acceder al sistema, inicia sesión nuevamente.</p>
            <a href="../public/index.php" class="logout-button">
                <i class="fas fa-sign-in-alt"></i> Volver al inicio de sesión
            </a>
        </div>
    </div>

    <script>
        // Verificar el modo oscuro guardado
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-mode');
        }
        
        // Redirección automática después de 5 segundos
        setTimeout(() => {
            window.location.href = "../public/index.php";
        }, 5000);
    </script>
</body>
</html>