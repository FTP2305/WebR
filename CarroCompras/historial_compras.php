<?php
session_start();
include '../Includes/conexion.php'; // Asegúrate de que esta ruta sea correcta

$conexion = new Conexion();
$conn = $conexion->getConectar();

// 1. Verificar si el cliente está logueado
if (!isset($_SESSION['id_cliente'])) {
    // Redirigir al login si no está logueado
    $_SESSION['mensaje_error'] = "Debes iniciar sesión para ver tu historial de compras.";
    header('Location: Login.php'); // Asegura que esta ruta sea correcta
    exit();
}

$id_cliente = $_SESSION['id_cliente'];

// Variable para almacenar los pedidos del cliente
$pedidos = [];
$mensaje_historial = ""; // Para mensajes de éxito o no hay pedidos

try {
    // 2. Obtener las ventas (pedidos) del cliente desde la base de datos
    // Ordenamos por fecha descendente para mostrar los más recientes primero
    $sql_ventas = "SELECT id_venta, fecha, total, estado 
                   FROM ventas 
                   WHERE id_cliente = ? 
                   ORDER BY fecha DESC";
    
    $stmt_ventas = $conn->prepare($sql_ventas);
    if ($stmt_ventas === false) {
        throw new Exception("Error al preparar la consulta de ventas: " . $conn->error);
    }
    $stmt_ventas->bind_param("i", $id_cliente);
    $stmt_ventas->execute();
    $result_ventas = $stmt_ventas->get_result();

    if ($result_ventas->num_rows > 0) {
        while ($venta = $result_ventas->fetch_assoc()) {
            $id_venta = $venta['id_venta'];
            // Para cada venta, obtener sus productos detallados
            $sql_detalle = "SELECT p.nombre, p.precio, p.imagen_url, dv.cantidad, dv.subtotal
                            FROM detalle_venta dv
                            JOIN productos p ON dv.id_producto = p.id_producto
                            WHERE dv.id_venta = ?";
            
            $stmt_detalle = $conn->prepare($sql_detalle);
            if ($stmt_detalle === false) {
                throw new Exception("Error al preparar la consulta de detalle de venta: " . $conn->error);
            }
            $stmt_detalle->bind_param("i", $id_venta);
            $stmt_detalle->execute();
            $result_detalle = $stmt_detalle->get_result();
            
            $venta['productos'] = [];
            while ($producto_detalle = $result_detalle->fetch_assoc()) {
                $venta['productos'][] = $producto_detalle;
            }
            $stmt_detalle->close(); // Cierra el statement de detalle

            $pedidos[] = $venta; // Añade la venta con sus productos al array de pedidos
        }
    } else {
        $mensaje_historial = "Aún no tienes compras en tu historial. ¡Anímate a explorar nuestros productos!";
    }
    $stmt_ventas->close(); // Cierra el statement de ventas

} catch (Exception $e) {
    // Manejo de errores de la base de datos
    $mensaje_historial = "Error al cargar tu historial de compras: " . $e->getMessage();
} finally {
    // Asegurarse de que la conexión se cierre
    if (isset($conn) && $conn->ping()) {
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TITI SHOP - Mi Historial de Compras</title>
    <link rel="stylesheet" href="/WebR/css/Home.css">
    <link rel="stylesheet" href="/WebR/css/base.css">
    <link rel="stylesheet" href="/WebR/css/components.css">
    <link rel="stylesheet" href="/WebR/css/historial.css"> </head>
<body>
<header>
    <div class="navbar">
        <img src="/WebR/img/LOGOTITI.jpeg" alt="Logo TITI SHOP" class="logo">
        <h3><a href="/WebR/PaginaPrincipal/Home.php" style="color: black;">Inicio</a></h3>
        <h3><a href="/WebR/PaginaPrincipal/Productos.php" style="color: black;">Productos</a></h3>
        <h3><a href="/WebR/PaginaPrincipal/Contactanos.php" style="color: black;">Contáctanos</a></h3>
        <h3><a href="/WebR/PaginaPrincipal/Nosotros.php" style="color: black;">Nosotros</a></h3>
        <h3><a href="/WebR/PaginaPrincipal/Preguntas.php" style="color: black;">Preguntas Frecuentes</a></h3>
        <h3><a href="/WebR/PaginaPrincipal/intranet.php" style="color: black;">Intranet</a></h3>
        
        <div class="user-menu">
            <?php if (isset($_SESSION['nombre'])): ?>
            <span style="color:black; font-weight: bold; font-size: 20px; margin-right:10px;">
                Hola! <?php echo htmlspecialchars($_SESSION['nombre']); ?>
            </span>
            <a href="http://localhost/WebR/Clientes/Cliente.php" style="margin-left: 20px;">
                <img src="img/loginsinfondo.png" alt="Ver Perfil" class="icono">
            </a>
            <a href="/WebR/Clientes/logout.php" style="margin-left: 20px;">
                <img src="/WebR/img/cerrarsesion1-removebg-preview.png" alt="Cerrar sesión" class="icono">
            </a>
            <?php else: ?>
            <a href="http://localhost/WebR/Clientes/Login.php">
                <img src="/WebR/img/loginsinfondo.png" alt="Login" class="icono">
            </a>
            <?php endif; ?>
            <a href="/WebR/CarroCompras/historial_compras.php"> <img src="img/historial de compras.png" alt="Historial" class="icono">
            </a>
            <a href="/WebR/CarritoCompras/carrito.php">
                <img src="/WebR/img/carrocomprassinfondo.png" alt="Carro de Compras" class="icono">
            </a>
        </div>
    </div>
</header>

<main>
    <section class="historial-page">
        <h1>Mi Historial de Compras</h1>

        <?php
        // Mostrar mensajes de error/información
        if (isset($_SESSION['mensaje_error'])) {
            echo '<div class="mensaje-alerta error">' . htmlspecialchars($_SESSION['mensaje_error']) . '</div>';
            unset($_SESSION['mensaje_error']); // Limpiar el mensaje
        }
        if (!empty($mensaje_historial)) {
            echo '<div class="mensaje-alerta info">' . htmlspecialchars($mensaje_historial) . '</div>';
        }
        ?>

        <?php if (!empty($pedidos)): ?>
            <div class="pedidos-container">
                <?php foreach ($pedidos as $pedido): ?>
                    <div class="pedido-card">
                        <div class="pedido-header">
                            <h2>Pedido #<?php echo htmlspecialchars($pedido['id_venta']); ?></h2>
                            <span class="pedido-fecha">Fecha: <?php echo date('d/m/Y H:i', strtotime($pedido['fecha'])); ?></span>
                        </div>
                        <div class="pedido-summary">
                            <span class="pedido-estado">Estado: <strong><?php echo htmlspecialchars(ucfirst($pedido['estado'])); ?></strong></span>
                            <span class="pedido-total">Total: <strong>S/. <?php echo number_format($pedido['total'], 2); ?></strong></span>
                        </div>
                        
                        <div class="productos-pedido-list">
                            <h3>Productos:</h3>
                            <?php if (!empty($pedido['productos'])): ?>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Cantidad</th>
                                            <th>Precio Unitario</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pedido['productos'] as $producto): ?>
                                            <tr>
                                            <td class="product-info-cell" data-label="Producto:">  <img src="<?php echo htmlspecialchars($producto['imagen_url']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                            <span><?php echo htmlspecialchars($producto['nombre']); ?></span>
                                            </td>
                                            <td data-label="Cantidad:"><?php echo htmlspecialchars($producto['cantidad']); ?></td>
                                            <td data-label="Precio Unitario:">S/. <?php echo number_format($producto['precio'], 2); ?></td>
                                            <td data-label="Subtotal:">S/. <?php echo number_format($producto['subtotal'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p>No se encontraron productos para este pedido.</p>
                            <?php endif; ?>
                        </div>
                        </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-historial">
                <p>¡Explora nuestros productos y empieza a llenar tu historial de compras!</p>
                <a href="/WebR/PaginaPrincipal/Productos.php" class="btn-primary">Ver Productos</a>
            </div>
        <?php endif; ?>

    </section>
</main>

<footer>
    <div class="footer-section">
        <h4>Contáctanos</h4>
        <p>WhatsApp: +51 123 456 789</p>
        <p>Atención: Lun-Sáb 9am-6pm</p>
    </div>
    <div class="footer-section">
        <h4>Sobre Nosotros</h4>
        <ul>
            <li><a href="#">¿Quiénes somos?</a></li>
            <li><a href="#">Misión</a></li>
            <li><a href="#">Visión</a></li>
        </ul>
    </div>
    <div class="footer-section">
        <h4>Políticas de empresa</h4>
        <ul>
            <li><a href="#">Política de garantía</a></li>
            <li><a href="#">Devolución y cambio</a></li>
            <li><a href="#">Privacidad</a></li>
            <li><a href="#">Envíos</a></li>
        </ul>
    </div>
    <div class="footer-section">
        <h4>Síguenos</h4>
        <p>Facebook / TikTok / Instagram</p>
        <div class="redes-sociales">
            <a href="Facebook.com">
                <img src="/WebR/img/fb_sinfondo.png" alt="Facebook">
            </a>
            <a href="TikTok.com">
                <img src="/WebR/img/tiktok_sinfondo.png" alt="TikTok">
            </a>
            <a href="Instagram.com">
                <img src="/WebR/img/logo_insta_sinfondo.png" alt="Instagram">
            </a>
        </div>
    </div>
    <div class="copyright">
        <p>&copy; 2025 Tu Empresa. Todos los derechos reservados.</p>
    </div>
</footer>

</body>
</html>