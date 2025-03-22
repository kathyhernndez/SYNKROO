<?php
// Conexión a la base de datos
include 'conexion_be.php';

try {
    // Obtener el término de búsqueda
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

    // Configuración de paginación
    $limit = 10; // Número de registros por página
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1; // Página actual
    $offset = ($page - 1) * $limit; // Cálculo del offset

    // Consulta para contar el total de registros (para la paginación)
    $countQuery = "SELECT COUNT(*) as total FROM bitacora b
                   INNER JOIN usuarios u ON b.usuario_id = u.id
                   WHERE u.nombre LIKE :search 
                   OR u.apellido LIKE :search 
                   OR b.accion LIKE :search 
                   OR b.descripcion LIKE :search 
                   OR b.fecha_hora LIKE :search";
    $countStmt = $conexion->prepare($countQuery);
    $countStmt->execute(['search' => "%$searchTerm%"]);
    $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalRecords / $limit); // Cálculo del total de páginas

    // Consulta para obtener los registros con paginación
    $query = "SELECT b.*, u.nombre, u.apellido 
              FROM bitacora b
              INNER JOIN usuarios u ON b.usuario_id = u.id
              WHERE u.nombre LIKE :search 
              OR u.apellido LIKE :search 
              OR b.accion LIKE :search 
              OR b.descripcion LIKE :search 
              OR b.fecha_hora LIKE :search 
              ORDER BY b.fecha_hora DESC 
              LIMIT $limit OFFSET $offset";
    $stmt = $conexion->prepare($query);
    $stmt->execute(['search' => "%$searchTerm%"]);

    // Verificar si se obtuvieron resultados
    $data = [];
    if ($stmt) {
        // Recorrer los resultados
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
    }

    // Devolver los datos en formato JSON
    echo json_encode([
        'data' => $data,
        'totalPages' => $totalPages,
        'currentPage' => $page
    ]);

} catch (PDOException $e) {
    // Manejo de errores
    echo json_encode(['error' => "Error al realizar la consulta: " . $e->getMessage()]);
}

// Cerrar la conexión
$conexion = null;
?>