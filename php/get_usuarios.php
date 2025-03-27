<?php
include 'conexion_be.php';

try {
    // Obtener los parámetros de búsqueda y filtrado
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
    $rol = isset($_GET['rol']) ? $_GET['rol'] : '';
    $estado = isset($_GET['estado']) ? $_GET['estado'] : '';

    // Construir la consulta SQL con JOIN a la tabla roles
    $query = "
        SELECT 
            u.id, 
            u.nombre, 
            u.apellido, 
            u.cedula, 
            u.correo, 
            u.estado, 
            u.id_roles,
            r.rol 
        FROM 
            usuarios u
        JOIN 
            roles r ON u.id_roles = r.id
        WHERE 
            1=1
    ";

    if (!empty($searchTerm)) {
        $query .= " AND (u.nombre LIKE :search OR u.apellido LIKE :search OR u.correo LIKE :search)";
    }

    if (!empty($rol)) {
        $query .= " AND u.id_roles = :rol";
    }

    if (!empty($estado)) {
        $query .= " AND u.estado = :estado";
    }

    $query .= " ORDER BY u.fecha_creacion DESC";

    // Preparar la consulta
    $stmt = $conexion->prepare($query);

    if (!empty($searchTerm)) {
        $searchTerm = "%$searchTerm%";
        $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
    }

    if (!empty($rol)) {
        $stmt->bindParam(':rol', $rol, PDO::PARAM_INT);
    }

    if (!empty($estado)) {
        $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
    }

    // Ejecutar la consulta
    $stmt->execute();

    // Verificar si se obtuvieron resultados
    if ($stmt) {
        // Recorrer los resultados
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($row['apellido']) . "</td>";
            echo "<td>" . htmlspecialchars($row['cedula']) . "</td>";
            echo "<td>" . htmlspecialchars($row['correo']) . "</td>";

            // Mostrar el estado como un botón con color dinámico
            $estado = $row['estado'];
            $color = ($estado == 1) ? 'green' : 'red';
            $texto = ($estado == 1) ? 'Activo' : 'Inactivo';
            echo "<td>";
            echo "<button style='background-color: $color; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer;' 
                  onclick='mostrarModalConfirmacion(" . $row['id'] . ", " . $estado . ")'>$texto</button>";
            echo "</td>";

            // Mostrar el nombre del rol en lugar del id_roles
            echo "<td>" . htmlspecialchars($row['rol']) . "</td>";

            echo "<td class='acciones'>";
            // Botón Eliminar (modificado para usar el modal de confirmación)
            echo "<button class='btn-eliminar' onclick='mostrarModalEliminar(" . $row['id'] . ")'>
                    <i class='fas fa-trash-alt'></i> Eliminar
                  </button>";
            
            // Botón Editar (nuevo estilo)
            echo "<button onclick='abrirModalEdicion(" . $row['id'] . ")' class='btn-editar'>
                    <i class='fas fa-edit'></i> Editar
                  </button>";
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No se encontraron usuarios.</td></tr>";
    }
} catch (PDOException $e) {
    // Manejo de errores
    echo "<tr><td colspan='8'>Error al realizar la consulta: " . $e->getMessage() . "</td></tr>";
}

// Cerrar la conexión
$conexion = null;
?>