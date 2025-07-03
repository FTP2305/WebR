<?php 
include '../Includes/conexion.php';

$conexion = new Conexion();
$conn = $conexion->getConectar();

// Crear Producto
if (isset($_POST['crear'])) {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $id_categoria = $_POST['id_categoria'];

    $sql = "INSERT INTO productos (nombre, precio, stock, id_categoria) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdii", $nombre, $precio, $stock, $id_categoria);
    $stmt->execute();
}

// Eliminar Producto
if (isset($_GET['eliminar'])) {
    $id_producto = $_GET['eliminar'];
    $sql = "DELETE FROM productos WHERE id_producto = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
}

// Editar Producto
if (isset($_GET['editar'])) {
    $id_producto = $_GET['editar'];
    $sql = "SELECT * FROM productos WHERE id_producto = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $producto = $resultado->fetch_assoc();
}

if (isset($_POST['actualizar'])) {
    $id_producto = $_POST['id_producto'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $id_categoria = $_POST['id_categoria'];

    $sql = "UPDATE productos SET nombre = ?, precio = ?, stock = ?, id_categoria = ? WHERE id_producto = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdiii", $nombre, $precio, $stock, $id_categoria, $id_producto);
    $stmt->execute();
}

// Listar Productos
$sql = "SELECT p.id_producto, p.nombre, p.precio, p.stock, c.nombre_categoria 
        FROM productos p
        INNER JOIN categorias c ON p.id_categoria = c.id_categoria";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Productos</title>
    <link rel="stylesheet" href="/WebR/css/crudProductos.css">
</head>
<body>
    <div class="contenedor">
        <div class="menu">
            <div class="menuIzquierda">
                <span class="title">Tienda TITISHOP</span> 
            </div>
            <div class="menuDerecha">
                <div class="menus">
                    <a href="listar.php">Productos</a>
                    <a href="../Clientes/listar.php">Clientes</a>
                    <a href="../Usuarios/listar.php">Usuarios</a>
                    <a href="../Usuarios/registroventas.php">Ventas</a>
                    <a href="../Usuarios/historial.php">Reportes</a>
                    <a href="../Usuarios/adminpag.php">Volver</a>
                </div>
            </div>
        </div>

        <!-- Crear Producto -->
        <div class="contenido">
            <h2>Agregar Producto</h2>
            <div class="form-container">
                <form method="POST" action="">
                    <input type="text" name="nombre" placeholder="Nombre del Producto" required>
                    <input type="number" step="0.01" name="precio" placeholder="Precio (S/.)" required>
                    <input type="number" name="stock" placeholder="Stock" required>
                    <select name="id_categoria" required>
                        <option value="">Seleccione Categoría</option>
                        <?php 
                            $categorias = $conn->query("SELECT * FROM categorias");
                            while ($categoria = $categorias->fetch_assoc()) {
                                echo "<option value='" . $categoria['id_categoria'] . "'>" . $categoria['nombre_categoria'] . "</option>";
                            }
                        ?>
                    </select>
                    <button type="submit" name="crear">Agregar Producto</button>
                </form>
            </div>
             <!-- Editar Producto -->
        <?php if (isset($producto)): ?>
            <div class="form-container">
                <h2>Editar Producto</h2>
                <form method="POST" action="">
                    <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
                    <input type="text" name="nombre" value="<?= $producto['nombre'] ?>" required>
                    <input type="number" step="0.01" name="precio" value="<?= $producto['precio'] ?>" required>
                    <input type="number" name="stock" value="<?= $producto['stock'] ?>" required>
                    <select name="id_categoria" required>
                        <option value="">Seleccione Categoría</option>
                        <?php 
                            $categorias = $conn->query("SELECT * FROM categorias");
                            while ($categoria = $categorias->fetch_assoc()) {
                                echo "<option value='" . $categoria['id_categoria'] . "' " . ($categoria['id_categoria'] == $producto['id_categoria'] ? 'selected' : '') . ">" . $categoria['nombre_categoria'] . "</option>";
                            }
                        ?>
                    </select>
                    <button type="submit" name="actualizar">Actualizar Producto</button>
                </form>
            </div>
        <?php endif; ?>               
            <!-- Listado de Productos -->
            <h2>Listado de Productos</h2>
            <div class="tabla">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Precio (S/.)</th>
                            <th>Stock</th>
                            <th>Categoría</th>
                            <th>Acciones</th>
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
                                    <td><?= $fila['nombre_categoria'] ?></td>
                                    <td>
                                        <a href="?editar=<?= $fila['id_producto'] ?>" class="btn-edit">Editar</a>
                                        <a href="?eliminar=<?= $fila['id_producto'] ?>" class="btn-delete" onclick="return confirm('¿Estás seguro de eliminar este producto?')">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6">No hay productos registrados aún.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
    </div>
</body>
</html>

