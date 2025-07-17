<?php 
include '../Includes/conexion.php';

$conexion = new Conexion();
$conn = $conexion->getConectar();

// Consulta para obtener las categorías
$sql_categorias = "SELECT * FROM categorias ORDER BY nombre_categoria";
$resultado_categorias = $conn->query($sql_categorias);

// Consulta base para productos
$sql = "SELECT p.id_producto, p.nombre, p.precio, p.stock, c.nombre_categoria 
        FROM productos p
        INNER JOIN categorias c ON p.id_categoria = c.id_categoria
        WHERE p.activo = TRUE";

// Filtros
$filtro_categoria = "";
$filtro_nombre = "";

if (isset($_GET['categoria']) && !empty($_GET['categoria'])) {
    $filtro_categoria = " AND c.id_categoria = " . intval($_GET['categoria']);
}

if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
    $busqueda = $conn->real_escape_string($_GET['busqueda']);
    $filtro_nombre = " AND p.nombre LIKE '%$busqueda%'";
}

$sql .= $filtro_categoria . $filtro_nombre;
$resultado = $conn->query($sql);

// Salida JSON
if (isset($_GET['json']) || 
    (isset($_SERVER['HTTP_ACCEPT']) && 
     str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))) {

    $productos = [];
    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $productos[] = [
                'id'        => (int)$fila['id_producto'],
                'nombre'    => $fila['nombre'],
                'precio'    => (float)$fila['precio'],
                'stock'     => (int)$fila['stock'],
                'categoria' => $fila['nombre_categoria'],
            ];
        }
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['productos' => $productos],
                    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Productos</title>
    <link rel="stylesheet" href="/WebR/css/prolistar.css">
    <style>
        .filtros {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .filtros form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: flex-end;
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
    </style>
</head>
<body>
<div class="contenedor">
    <div class="menu">
        <div class="menuIzquierda">
            <span class="title">TITI SHOP ADMIN</span>
        </div>
        <div class="menuDerecha">
            <div class="menus">
                <a href="../Productos/listar.php">Productos</a>
                <a href="../Clientes/listar.php">Clientes</a>
                <a href="../Usuarios/listar.php">Usuarios</a>
                <a href="../Usuarios/registroventas.php">Ventas</a>
                <a href="../Usuarios/historial.php">Reportes</a>
            </div>
        </div>
    </div>

    <div class="contenido">
        <h2 class="centrar">🛒 Listado de Productos</h2>
        
        <div class="filtros">
            <form method="GET" action="">
                <div class="filtro-group">
                    <label for="busqueda">Buscar por Nombre:</label>
                    <input type="text" id="busqueda" name="busqueda" 
                           placeholder="Nombre del producto" 
                           value="<?= isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : '' ?>">
                </div>
                
                <div class="filtro-group">
                    <label for="categoria">Filtrar por Categoría:</label>
                    <select id="categoria" name="categoria">
                        <option value="">Todas las categorías</option>
                        <?php if ($resultado_categorias && $resultado_categorias->num_rows > 0): ?>
                            <?php while($categoria = $resultado_categorias->fetch_assoc()): ?>
                                <option value="<?= $categoria['id_categoria'] ?>" 
                                    <?= (isset($_GET['categoria']) && $_GET['categoria'] == $categoria['id_categoria']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($categoria['nombre_categoria']) ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn-filtrar">Filtrar</button>
                <a href="listar.php" class="btn-filtrar" style="background-color: #6c757d; text-decoration: none;">Limpiar</a>
            </form>
        </div>

        <div class="tabla">
            <div class="tablaLeft" style="width: 100%;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Precio (S/.)</th>
                            <th>Stock</th>
                            <th>Categoría</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado && $resultado->num_rows > 0): ?>
                            <?php while ($fila = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $fila['id_producto'] ?></td>
                                    <td><?= htmlspecialchars($fila['nombre']) ?></td>
                                    <td><?= number_format($fila['precio'], 2) ?></td>
                                    <td><?= $fila['stock'] ?></td>
                                    <td><?= htmlspecialchars($fila['nombre_categoria']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5">No se encontraron productos con los filtros seleccionados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>