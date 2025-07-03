<?php 
include '../Includes/conexion.php';

$conexion = new Conexion();
$conn = $conexion->getConectar();

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
    <title>Listado de Productos</title>
    <link rel="stylesheet" href="prolistar.css">
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
                <a href="../Roles/listar.php">Clientes</a>
                <a href="../Usu/listar.php">Usuarios</a>
                <a href="../Usuarios/registroventas.php">Ventas</a>
                <a href="../Usuarios/historial.php">Reportes</a>
            </div>
        </div>
    </div>

    <div class="contenido">
        <h2 class="centrar">🛒 Listado de Productos</h2>
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
                                    <td><?= $fila['nombre_categoria'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5">No hay productos registrados aún.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>