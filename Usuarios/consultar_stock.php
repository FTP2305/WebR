<?php
session_start();

if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'vendedor' && $_SESSION['rol'] !== 'admin')) {
    header('Location: ../intranet.php');
    exit();
}

include '../Includes/conexion.php'; 

$conexion = new Conexion();
$conn = $conexion->getConectar();

$categoria_seleccionada = ''; 


if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['categoria_id']) && $_GET['categoria_id'] !== '') {
    $categoria_seleccionada = intval($_GET['categoria_id']);
   
    $sql_productos = "SELECT p.id_producto, p.nombre, p.precio, p.stock, p.imagen_url, c.nombre_categoria
                      FROM productos p
                      LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                      WHERE p.id_categoria = ?
                      ORDER BY p.nombre ASC";
    $stmt_productos = $conn->prepare($sql_productos);
    $stmt_productos->bind_param("i", $categoria_seleccionada);
    $stmt_productos->execute();
    $resultado_productos = $stmt_productos->get_result();
} else {
    
    $sql_productos = "SELECT p.id_producto, p.nombre, p.precio, p.stock, p.imagen_url, c.nombre_categoria
                      FROM productos p
                      LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                      ORDER BY p.nombre ASC"; 
    $resultado_productos = $conn->query($sql_productos);
}
$sql_categorias_filtro = "SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria ASC";
$resultado_categorias_filtro = $conn->query($sql_categorias_filtro);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Consultar Stock - Titishop</title>
    <link rel="stylesheet" href="/WebR/css/estilo.css" /> 
    <link rel="stylesheet" href="/WebR/css/vendedor.css" />
    <link rel="stylesheet" href="/WebR/css/stock.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>
<body>
    <header class="navbar">
        <div class="navbar-container">
            <a href="<?php echo ($_SESSION['rol'] === 'admin') ? 'adminpag.php' : 'vendedorpag.php'; ?>" class="logo">
                TiTiShop - Consultar Stock
            </a>
            <nav>
                <ul class="nav-links">
                    <li><a href="registroventas.php">Registrar Venta <i class="fas fa-cash-register"></i></a></li>
                    <li><a href="consultar_stock.php">Consultar Stock <i class="fas fa-boxes"></i></a></li>
                    <li><a href="historial.php">Historial de Ventas <i class="fas fa-history"></i></a></li>
                    <li><a href="../logout.php">Cerrar Sesión <i class="fas fa-sign-out-alt"></i></a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="dashboard-content">
        <section class="stock-section">
            <h1>Consultar Stock de Productos</h1>

            <div class="filter-section">
                <form method="GET" action="consultar_stock.php">
                    <label for="categoria_id">Filtrar por Categoría:</label>
                    <select name="categoria_id" id="categoria_id">
                        <option value="">Todas las Categorías</option>
                        <?php
                        if ($resultado_categorias_filtro && $resultado_categorias_filtro->num_rows > 0) {
                            while ($categoria = $resultado_categorias_filtro->fetch_assoc()) {
                                $selected = ($categoria['id_categoria'] == $categoria_seleccionada) ? 'selected' : '';
                                echo "<option value='{$categoria['id_categoria']}' $selected>" . htmlspecialchars($categoria['nombre_categoria']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                    <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Filtrar</button>
                </form>
            </div>
            
            <table class="stock-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Imagen</th> </tr>
                </thead>
                <tbody>
                    <?php
                    if ($resultado_productos && $resultado_productos->num_rows > 0) {
                        while ($producto = $resultado_productos->fetch_assoc()) {
                            $nombre_categoria_display = !empty($producto['nombre_categoria']) ? htmlspecialchars($producto['nombre_categoria']) : 'Sin Categoría';
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                <td><?php echo $nombre_categoria_display; ?></td>
                                <td>S/ <?php echo number_format($producto['precio'], 2); ?></td>
                                <td><?php echo htmlspecialchars($producto['stock']); ?></td>
                                <td>
                                    <?php if (!empty($producto['imagen_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($producto['imagen_url']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="product-thumbnail">
                                    <?php else: ?>
                                        No disponible
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='5'>No se encontraron productos para la categoría seleccionada o no hay productos.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>
    <?php
    if ($conn) {
        $conn->close();
    }
    ?>
</body>
</html>