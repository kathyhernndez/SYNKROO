
    <link rel="stylesheet" href="../assets/css/main.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<!-- Barra lateral izquierda (menú) -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h2>Menú</h2>
        <button class="close-btn" id="close-btn" aria-label="Cerrar menú"><i class="fas fa-times"></i></button>
    </div>
    <ul class="sidebar-links">
        <?php
        // echo(implode(" ", $_SESSION));
        print_r($_SESSION['id_roles']);
        if ($_SESSION['id_roles'] == 1) {
            echo '
                <li><a href="menu.php"><i class="fas fa-home"></i>Principal</a></li>
                <li><a href="usuarios.php"><i class="fas fa-users"></i>Gestionar Usuarios</a></li>
                <li><a href="verBitacora.php"><i class="fas fa-file"></i>Ver Bitácora</a></li>
                <li><a><i class="fas fa-cog"></i><button onclick="confirmBackup()" class="logout_btn">Hacer respaldo</button> </a></li>
            ';
        } else if ($_SESSION['id_roles'] == 2) {
            echo '
                <li><a href="menu.php"><i class="fas fa-home"></i>Principal</a></li>
                <li><a href="usuarios.php"><i class="fas fa-users"></i>Gestionar Usuarios</a></li>
                <li><a href="verBitacora.php"><i class="fas fa-file"></i>Ver Bitácora</a></li>
                <li><a><i class="fas fa-cog"></i><button onclick="confirmBackup()" class="backup_btn">Hacer respaldo</button> </a></li>
            ';
        } else if ($_SESSION['id_roles'] == 3) {
            echo '
                <li><a href="menu.php"><i class="fas fa-home"></i>Principal</a></li>
            ';
        }
        ?>
    </ul>
</div>