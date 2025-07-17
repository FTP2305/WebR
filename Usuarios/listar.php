<?php
include '../Includes/conexion.php';
$conexion = new Conexion();
$conn = $conexion->getConectar();

// Consulta para obtener los tipos de rol (asumiendo que existe una tabla 'roles')
$sql_roles = "SELECT id_rol, nombre_rol FROM roles";
$resultado_roles = $conn->query($sql_roles);

// Consulta base con JOIN para obtener el nombre del rol
$sql = "SELECT u.id_usuario, u.nombre_usuario, u.correo, u.contrasena, r.nombre_rol 
        FROM usuarios u
        LEFT JOIN roles r ON u.id_rol = r.id_rol";

// Variables para filtros
$filtro_nombre = "";
$filtro_rol = "";

// Procesar filtros
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $busqueda = trim($_POST['nombre_usuario'] ?? '');
    $id_rol = $_POST['rol'] ?? '';
    
    // Construir consulta con parámetros
    $conditions = [];
    $params = [];
    $types = '';
    
    if (!empty($busqueda)) {
        $conditions[] = "u.nombre_usuario LIKE ?";
        $params[] = "%" . $busqueda . "%";
        $types .= 's';
    }
    
    if (!empty($id_rol) && $id_rol !== 'todos') {
        $conditions[] = "u.id_rol = ?";
        $params[] = $id_rol;
        $types .= 'i';
    }
    
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }
    
    // Preparar consulta
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $resultado = $stmt->get_result();
    } else {
        echo "Error al preparar la consulta: " . $conn->error;
        $resultado = $conn->query($sql); // Fallback a consulta sin filtros
    }
} else {
    $resultado = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Usuarios</title>
    <link rel="stylesheet" href="/WebR/css/usulist.css">
    <style>
        .filtros-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .filtros-form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .filtro-group {
            flex: 1;
            min-width: 200px;
        }
        .filtro-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .filtro-group input, 
        .filtro-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .btn-filtrar {
            padding: 10px 20px;
            background-color: #1e2f3a;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-filtrar:hover {
            background-color: #2c3e50;
        }
        .btn-limpiar {
            background-color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <div class="menu">
            <div class="menuIzquierda">
                <span class="title">Tienda Admin</span>
            </div>
            <div class="menuDerecha">
                <div class="menus">
                    <a href="/WebR/Productos/listar.php">Productos</a>
                    <a href="/WebR/Cleintes/listar.php">Clientes</a>
                    <a href="/WebR/Usuarios/listar.php">Usuarios</a>
                    <a href="/WebR/Usuarios/registroventas.php">Ventas</a>
                    <a href="/WebR/Usuarios/historial.php">Reportes</a>
                </div>
            </div>
        </div>

        <div class="contenido">
            <h2 class="centrar">📋 Listado de Usuarios</h2>
            
            <div class="filtros-container">
                <form method="post" class="filtros-form">
                    <div class="filtro-group">
                        <label for="nombre_usuario">Buscar por Nombre:</label>
                        <input type="text" id="nombre_usuario" name="nombre_usuario" 
                               placeholder="Nombre de usuario"
                               value="<?= htmlspecialchars($_POST['nombre_usuario'] ?? '') ?>">
                    </div>
                    
                    <div class="filtro-group">
                        <label for="rol">Filtrar por Rol:</label>
                        <select id="rol" name="rol">
                            <option value="todos">Todos los roles</option>
                            <?php if ($resultado_roles && $resultado_roles->num_rows > 0): ?>
                                <?php while($rol = $resultado_roles->fetch_assoc()): ?>
                                    <option value="<?= $rol['id_rol'] ?>"
                                        <?= (isset($_POST['rol']) && $_POST['rol'] == $rol['id_rol']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($rol['nombre_rol']) ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="filtro-group" style="display: flex; align-items: flex-end; gap: 10px;">
                        <button type="submit" class="btn-filtrar">Filtrar</button>
                        <a href="listar.php" class="btn-filtrar btn-limpiar">Limpiar</a>
                    </div>
                </form>
            </div>

            <div class="tabla">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Contraseña (Hash)</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado && $resultado->num_rows > 0): ?>
                            <?php while($row = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id_usuario']) ?></td>
                                    <td><?= htmlspecialchars($row['nombre_usuario']) ?></td>
                                    <td><?= htmlspecialchars($row['correo']) ?></td>
                                    <td><?= htmlspecialchars($row['nombre_rol'] ?? 'Sin rol') ?></td>
                                    <td><?= htmlspecialchars(substr($row['contrasena'], 0, 15)) ?>...</td>
                                    <td>
                                        <a href="eliminar_usuario.php?id=<?= $row['id_usuario'] ?>" 
                                           onclick="return confirm('¿Estás seguro de eliminar este usuario?')"
                                           style="color: #dc3545; text-decoration: none;">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6">No se encontraron usuarios con los filtros seleccionados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>