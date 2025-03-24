<?php
// Conexión a la base de datos
include 'conexion_be.php';

// Modifica la parte de la consulta para incluir el filtro por fecha
try {
    // Obtener parámetros
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
    $filterDate = isset($_GET['date']) ? $_GET['date'] : '';

    // Configuración de paginación
    $limit = 10;
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $offset = ($page - 1) * $limit;

    // Consulta base
    $baseQuery = "FROM bitacora b
                 INNER JOIN usuarios u ON b.usuario_id = u.id
                 WHERE 1=1";
    
    $params = [];

    // Añadir condiciones de búsqueda
    if ($searchTerm) {
        $baseQuery .= " AND (u.nombre LIKE :search 
                   OR u.apellido LIKE :search 
                   OR b.accion LIKE :search 
                   OR b.descripcion LIKE :search 
                   OR b.fecha_hora LIKE :search)";
        $params['search'] = "%$searchTerm%";
    }

    // Añadir condición de fecha
    if ($filterDate) {
        $baseQuery .= " AND DATE(b.fecha_hora) = :filterDate";
        $params['filterDate'] = $filterDate;
    }

    // Consulta para contar el total de registros
    $countQuery = "SELECT COUNT(*) as total " . $baseQuery;
    $countStmt = $conexion->prepare($countQuery);
    $countStmt->execute($params);
    $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalRecords / $limit);

    // Consulta para obtener los registros
    $query = "SELECT b.*, u.nombre, u.apellido " . $baseQuery . 
             " ORDER BY b.fecha_hora DESC 
              LIMIT $limit OFFSET $offset";
    $stmt = $conexion->prepare($query);
    $stmt->execute($params);

    // Obtener resultados
    $data = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    // Devolver los datos
    echo json_encode([
        'data' => $data,
        'totalPages' => $totalPages,
        'currentPage' => $page
    ]);

} catch (PDOException $e) {
    echo json_encode(['error' => "Error al realizar la consulta: " . $e->getMessage()]);
}

$conexion = null;
?>