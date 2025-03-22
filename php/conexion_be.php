<?php
// Datos de conexión
$host = "localhost";
$dbname = "synkroo";
$username = "root";
$password = "";

try {
    // Crear una instancia de PDO
    $conexion = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Configurar el modo de error de PDO a excepciones
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Mensaje de éxito (opcional)
    //echo "Conexión exitosa";
} catch (PDOException $e) {
    // Manejo de errores
    die("Error de conexión: " . $e->getMessage());
}
?>