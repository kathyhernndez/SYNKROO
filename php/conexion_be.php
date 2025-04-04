<?php
// Verificar si la función ya existe
if (!function_exists('obtenerConexion')) {
    function obtenerConexion() {
        $host = "localhost";
        $dbname = "synkroo";
        $username = "root";
        $password = "";
        
        try {
            $conexion = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conexion;
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
}

// Crear conexión global solo si no existe
if (!isset($conexion)) {
    $conexion = obtenerConexion();
}
?>