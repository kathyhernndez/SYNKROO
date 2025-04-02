<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="shortcut icon" href="../assets/image/favicon.png" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<!-- Barra lateral izquierda (menú) -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h2>Menu</h2>
        <button class="close-btn" id="close-btn" aria-label="Cerrar menú"><i class="fa-solid fa-arrow-left"></i></button>
    </div>
    <ul class="sidebar-links">
    <?php
    if ($_SESSION['id_roles'] == 1) {
        echo '
            <li><a href="menu.php"><i class="fas fa-home"></i><span>Principal</span></a></li>
            <li><a href="usuarios.php"><i class="fas fa-users"></i><span>Gestionar Usuarios</span></a></li>
            <li><a href="verBitacora.php"><i class="fas fa-file"></i><span>Ver Bitácora</span></a></li>
            <li><a href="#" onclick="confirmBackup(); return false;"><i class="fas fa-save"></i><span>Hacer respaldo</span></a></li>
        ';
    } else if ($_SESSION['id_roles'] == 2) {
        echo '
            <li><a href="menu.php"><i class="fas fa-home"></i><span>Principal</span></a></li>
            <li><a href="usuarios.php"><i class="fas fa-users"></i><span>Gestionar Usuarios</span></a></li>
            <li><a href="verBitacora.php"><i class="fas fa-file"></i><span>Ver Bitácora</span></a></li>
            <li><a href="#" onclick="confirmBackup(); return false;"><i class="fas fa-save"></i><span>Hacer respaldo</span></a></li>
        ';
    } else if ($_SESSION['id_roles'] == 3) {
        echo '
            <li><a href="menu.php"><i class="fas fa-home"></i><span>Principal</span></a></li>
        ';
    }
    ?>
    </ul>
</div>



        
