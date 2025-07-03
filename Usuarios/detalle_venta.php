<?php
session_start();

include '../Includes/conexion.php'; 

$conexion = new Conexion();
$conn = $conexion->getConectar();

$mensaje = ""; 
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'vendedor' && $_SESSION['rol'] !== 'admin')) {
    header('Location: ../intranet.php');
    exit();
}


$venta_id = isset($_GET['id']) ? intval($_GET['id']) : 0; 

if ($venta_id === 0) { 
    header('Location: historial.php'); 
    exit();
}
$sql_venta_info = "SELECT v.total, c.nombre, c.apellidos
                   FROM ventas v
                   JOIN clientes c ON v.id_cliente = c.id_cliente
                   WHERE v.id_venta = ?";
$stmt_venta_info = $conn->prepare($sql_venta_info);

$venta_total = 0;
$cliente_nombre_completo = "Cliente Desconocido";

if ($stmt_venta_info === false) {
    $mensaje = "Error al preparar la consulta de información de venta: " . htmlspecialchars($conn->error);
} else {
    $stmt_venta_info->bind_param("i", $venta_id);
    $stmt_venta_info->execute();
    $stmt_venta_info->store_result();
    
    if ($stmt_venta_info->num_rows > 0) {
        $stmt_venta_info->bind_result($venta_total, $nombre_cliente, $apellidos_cliente);
        $stmt_venta_info->fetch();
        $cliente_nombre_completo = htmlspecialchars($nombre_cliente . " " . $apellidos_cliente);
    } else {
        $mensaje = "Información de venta no encontrada.";
    }
    $stmt_venta_info->close();
}

$sql_detalles = "SELECT dv.id_producto, p.nombre, dv.cantidad, dv.subtotal
                 FROM detalle_venta dv
                 JOIN productos p ON dv.id_producto = p.id_producto
                 WHERE dv.id_venta = ?";
$stmt_detalles = $conn->prepare($sql_detalles);


if ($stmt_detalles === false) {

    die("Error al preparar la consulta de detalles: " . htmlspecialchars($conn->error));
}

$stmt_detalles->bind_param("i", $venta_id);
$stmt_detalles->execute();
$resultado_detalles = $stmt_detalles->get_result();

$stmt_detalles->close(); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Detalles de Venta - TiTiShop</title>
    <link rel="stylesheet" href="/WebR/css/vendedor.css" />
    <link rel="stylesheet" href="/WebR/css/historial.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>
<body>
    <header class="navbar">
        <a href="#" class="logo">
            TiTiShop - Vista de Vendedor
        </a>
        <nav>
            <ul class="nav-links">
                <li><a href="registroventas.php">Registrar Venta <i class="fas fa-cash-register"></i></a></li>
                <li><a href="consultar_stock.php">Consultar Stock <i class="fas fa-boxes"></i></a></li>
                <li><a href="historial.php">Historial de Ventas <i class="fas fa-history"></i></a></li>
                <li><a href="../logout.php">Cerrar Sesión <i class="fas fa-sign-out-alt"></i></a></li>
            </ul>
        </nav>
    </header>

    <main class="dashboard-content">
        <section class="sale-details-section">
            <h1>Detalles de Venta #<?php echo htmlspecialchars($venta_id); ?></h1>
            <p><strong>Cliente:</strong> <?php echo $cliente_nombre_completo; ?></p>
            <p><strong>Total de la Venta:</strong> S/ <?php echo number_format($venta_total, 2); ?></p>

            <?php if (!empty($mensaje)) { // Muestra el mensaje de error si existe ?>
                <div class="mensaje-error">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php } ?>

            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($resultado_detalles->num_rows > 0) {
                        while ($detalle = $resultado_detalles->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($detalle['nombre']) . "</td>";
                            echo "<td>" . htmlspecialchars($detalle['cantidad']) . "</td>";
                            echo "<td>S/ " . number_format($detalle['subtotal'], 2) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No hay detalles de productos para esta venta.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <a href="historial.php" class="back-link">Volver al Historial de Ventas</a>
        </section>
    </main>
    </body>
</html>