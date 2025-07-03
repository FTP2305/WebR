<?php
session_start();


if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'vendedor' && $_SESSION['rol'] !== 'admin')) {
    header('Location: ../intranet.php');
    exit();
}

include '../Includes/conexion.php'; 

$conexion = new Conexion();
$conn = $conexion->getConectar();


$fecha_inicio = '';
$fecha_fin = '';
$cliente_nombre_filtro = '';


$where_clauses = [];
$bind_types = '';
$bind_params = [];

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['fecha_inicio']) && !empty($_GET['fecha_inicio'])) {
        $fecha_inicio = $_GET['fecha_inicio'];
        $where_clauses[] = "v.fecha >= ?";
        $bind_types .= "s";
        $bind_params[] = $fecha_inicio;
    }
    if (isset($_GET['fecha_fin']) && !empty($_GET['fecha_fin'])) {
        $fecha_fin = $_GET['fecha_fin'];
        $where_clauses[] = "v.fecha <= ?";
        $bind_types .= "s";
        $bind_params[] = $fecha_fin;
    }
    if (isset($_GET['cliente_nombre']) && !empty($_GET['cliente_nombre'])) {
        $cliente_nombre_filtro = $_GET['cliente_nombre'];
        $where_clauses[] = "c.nombre LIKE ?";
        $bind_types .= "s";
        $bind_params[] = '%' . $cliente_nombre_filtro . '%';
    }
}

$sql_ventas = "SELECT v.id_venta, v.fecha, v.total, c.nombre AS cliente_nombre
               FROM ventas v
               JOIN clientes c ON v.id_cliente = c.id_cliente";

if (!empty($where_clauses)) {
    $sql_ventas .= " WHERE " . implode(" AND ", $where_clauses);
}

$sql_ventas .= " ORDER BY v.fecha DESC";


$stmt_ventas = $conn->prepare($sql_ventas);

if (!empty($bind_params)) {
    
    if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
        $stmt_ventas->bind_param($bind_types, ...$bind_params);
    } else {
        call_user_func_array([$stmt_ventas, 'bind_param'], array_merge([$bind_types], $bind_params));
    }
}
$stmt_ventas->execute();
$resultado_ventas = $stmt_ventas->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Historial de Ventas - Titishop</title>
    <link rel="stylesheet" href="/WebR/css/estilo.css" /> 
    <link rel="stylesheet" href="/WebR/css/vendedor.css" />
    <link rel="stylesheet" href="/WebR/css/historial.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>
<body>
    <header class="navbar">
        <div class="navbar-container">
            <a href="<?php echo ($_SESSION['rol'] === 'admin') ? 'adminpag.php' : 'vendedorpag.php'; ?>" class="logo">
                TiTiShop - Historial de Ventas
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
        <section class="sales-history-section">
            <h1>Historial de Ventas</h1>

            <div class="filter-section">
                <form method="GET" action="historial.php">
                    <label for="fecha_inicio">Desde:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio); ?>">

                    <label for="fecha_fin">Hasta:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin); ?>">

                    <label for="cliente_nombre">Cliente:</label>
                    <input type="text" id="cliente_nombre" name="cliente_nombre" placeholder="Nombre del cliente" value="<?php echo htmlspecialchars($cliente_nombre_filtro); ?>">

                    <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Filtrar</button>
                    <a href="historial.php" class="btn-clear-filter"><i class="fas fa-redo"></i> Limpiar Filtros</a>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID Venta</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($resultado_ventas->num_rows > 0) {
                        while ($venta = $resultado_ventas->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($venta['id_venta']) . "</td>";
                            echo "<td>" . htmlspecialchars($venta['cliente_nombre']) . "</td>";
                            echo "<td>" . htmlspecialchars(date('d/m/Y H:i:s', strtotime($venta['fecha']))) . "</td>"; // Formatear fecha
                            echo "<td>S/ " . number_format($venta['total'], 2) . "</td>"; // Formatear total
                            echo "<td><a href='detalle_venta.php?id=" . htmlspecialchars($venta['id_venta']) . "'>Ver detalles</a></td>"; 
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No se encontraron ventas con los criterios seleccionados.</td></tr>";
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