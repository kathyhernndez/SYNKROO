<?php
session_start();
if (isset($_SESSION['captcha'])) {
    echo $_SESSION['captcha'];
} else {
    echo ''; // Si no hay CAPTCHA, devuelve una cadena vacía
}
?>