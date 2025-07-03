<?php
session_start();
include '../Includes/conexion.php'; 

$conexion = new Conexion();
$conn = $conexion->getConectar();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar_compra_btn'])) {
    
    
    if (!isset($_SESSION['id_cliente'])) {
        header('Location: Login.php');
        exit();
    }

    $id_cliente = $_SESSION['id_cliente'];

   
    $conn->begin_transaction();

    try {
        
        $sql_carrito = "SELECT p.id_producto, p.nombre, p.precio, p.stock, c.cantidad 
                        FROM carrito_compras c
                        JOIN productos p ON c.id_producto = p.id_producto
                        WHERE c.id_cliente = ? AND c.estado = 'activo' FOR UPDATE"; 
        
        $stmt_carrito = $conn->prepare($sql_carrito);
        if ($stmt_carrito === false) {
            throw new Exception("Error al preparar la consulta del carrito: " . $conn->error);
        }
        $stmt_carrito->bind_param("i", $id_cliente);
        $stmt_carrito->execute();
        $result_carrito = $stmt_carrito->get_result();
        $productos_en_carrito = [];
        $total_compra = 0;

        
        if ($result_carrito->num_rows === 0) {
            throw new Exception("Tu carrito de compras está vacío. No se puede finalizar la compra.");
        }

        while ($row = $result_carrito->fetch_assoc()) {
            if ($row['cantidad'] > $row['stock']) {
                throw new Exception("El producto '" . htmlspecialchars($row['nombre']) . "' no tiene suficiente stock disponible. Solo quedan " . $row['stock'] . " unidades.");
            }
            $productos_en_carrito[] = $row;
            $total_compra += $row['precio'] * $row['cantidad'];
        }
        $stmt_carrito->close(); 

        $fecha_venta = date('Y-m-d H:i:s');
        $estado_venta = 'pendiente'; 

        $sql_insert_venta = "INSERT INTO ventas (id_cliente, fecha, total, estado) 
                             VALUES (?, ?, ?, ?)";
        $stmt_insert_venta = $conn->prepare($sql_insert_venta);
        if ($stmt_insert_venta === false) {
            throw new Exception("Error al preparar la inserción de la venta: " . $conn->error);
        }
        $stmt_insert_venta->bind_param("isds", $id_cliente, $fecha_venta, $total_compra, $estado_venta);
        $stmt_insert_venta->execute();

        if ($stmt_insert_venta->affected_rows === 0) {
            throw new Exception("No se pudo crear la venta. Por favor, inténtalo de nuevo.");
        }
        $id_venta = $conn->insert_id; 
        $stmt_insert_venta->close(); 

        $sql_detalle = "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, subtotal, precio_venta) 
                        VALUES (?, ?, ?, ?, ?)";
        $stmt_detalle = $conn->prepare($sql_detalle);
        if ($stmt_detalle === false) {
            throw new Exception("Error al preparar la inserción del detalle de venta: " . $conn->error);
        }

        $sql_update_stock = "UPDATE productos SET stock = stock - ? WHERE id_producto = ?";
        $stmt_update_stock = $conn->prepare($sql_update_stock);
        if ($stmt_update_stock === false) {
            throw new Exception("Error al preparar la actualización de stock: " . $conn->error);
        }

        foreach ($productos_en_carrito as $producto) {
            $id_producto = $producto['id_producto'];
            $cantidad = $producto['cantidad'];
            $precio_venta = $producto['precio'];
            $subtotal = $precio_venta * $cantidad;

            $stmt_detalle->bind_param("iiidd", $id_venta, $id_producto, $cantidad, $subtotal, $precio_venta);
            $stmt_detalle->execute();
            if ($stmt_detalle->affected_rows === 0) {
                throw new Exception("No se pudo insertar el detalle para el producto " . htmlspecialchars($producto['nombre']) . ".");
            }

            $stmt_update_stock->bind_param("ii", $cantidad, $id_producto);
            $stmt_update_stock->execute();
            if ($stmt_update_stock->affected_rows === 0) {
                throw new Exception("No se pudo actualizar el stock para el producto " . htmlspecialchars($producto['nombre']) . ". Puede que no haya suficiente stock.");
            }
        }
        $stmt_detalle->close();
        $stmt_update_stock->close();

        $sql_historial = "INSERT INTO historial_compras (id_cliente, id_venta, total) 
                          VALUES (?, ?, ?)";
        $stmt_historial = $conn->prepare($sql_historial);
        if ($stmt_historial === false) {
            throw new Exception("Error al preparar la inserción del historial: " . $conn->error);
        }
        $stmt_historial->bind_param("iid", $id_cliente, $id_venta, $total_compra);
        $stmt_historial->execute();
        $stmt_historial->close();


        $sql_clear_carrito = "DELETE FROM carrito_compras WHERE id_cliente = ? AND estado = 'activo'";
        $stmt_clear_carrito = $conn->prepare($sql_clear_carrito);
        if ($stmt_clear_carrito === false) {
            throw new Exception("Error al preparar la limpieza del carrito: " . $conn->error);
        }
        $stmt_clear_carrito->bind_param("i", $id_cliente);
        $stmt_clear_carrito->execute();
        $stmt_clear_carrito->close();

        $sql_update_venta_estado = "UPDATE ventas SET estado = 'completado' WHERE id_venta = ?";
        $stmt_update_venta_estado = $conn->prepare($sql_update_venta_estado);
        if ($stmt_update_venta_estado === false) {
            throw new Exception("Error al preparar la actualización del estado de venta: " . $conn->error);
        }
        $stmt_update_venta_estado->bind_param("i", $id_venta);
        $stmt_update_venta_estado->execute();
        if ($stmt_update_venta_estado->affected_rows === 0) {
            error_log("Advertencia: No se pudo actualizar el estado de la venta " . $id_venta);
        }
        $stmt_update_venta_estado->close();

        $conn->commit();
        require_once 'generar_factura.php';
        $generador = new GeneradorFacturas();

        try {
            $pdf_path = $generador->generarYEnviarFactura($id_venta, $conn);
            $_SESSION['correo_enviado'] = true;
        } catch (Exception $e) {
            error_log("Error al enviar factura: " . $e->getMessage());
            $_SESSION['correo_enviado'] = false;
        }

        $_SESSION['compra_finalizada'] = true;
        $_SESSION['id_ultima_venta'] = $id_venta;
        header('Location: confirmacion_pago.php');
        exit();

        

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['mensaje_error'] = "Error al procesar tu compra: " . $e->getMessage();
        
        header('Location: carrito.php'); 
        exit();

    } finally {
        if (isset($conn) && $conn->ping()) {
            $conn->close();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TITI SHOP - Finalizar Compra</title>
    <link rel="stylesheet" href="/WebR/css/Home.css">
    <link rel="stylesheet" href="/WebR/css/base.css">
    <link rel="stylesheet" href="/WebR/css/components.css">
    <link rel="stylesheet" href="/WebR/css/carrito.css"> <style>
        .confirmacion-container {
            max-width: 800px;
            margin: 60px auto;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .confirmacion-container h1 {
            color: #28a745; 
            font-size: 3rem;
            margin-bottom: 20px;
        }
        .confirmacion-container p {
            font-size: 1.2rem;
            color: #343a40;
            margin-bottom: 15px;
        }
        .confirmacion-container .order-details {
            margin-top: 30px;
            border-top: 1px solid #e9ecef;
            padding-top: 20px;
        }
        .confirmacion-container .order-details span {
            font-weight: bold;
            color: #007bff;
        }
        .btn-accion {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 15px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 30px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .btn-accion:hover {
            background-color: #0056b3;
            transform: translateY(-3px);
        }
        .mensaje-error {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 1.1rem;
        }
    </style>
</head>
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
                <img src="/WebR/img/loginsinfondo.png" alt="Ver Perfil" class="icono">
            </a>
            <a href="/WebR/Clientes/logout.php" style="margin-left: 20px;">
                <img src="/WebR/img/cerrarsesion1-removebg-preview.png" alt="Cerrar sesión" class="icono">
            </a>
            <?php else: ?>
            <a href="http://localhost/WebR/Clientes/Login.php">
                <img src="/WebR/img/loginsinfondo.png" alt="Login" class="icono">
            </a>
            <?php endif; ?>
            <a href="#">
                <img src="/WebR/img/historial de compras.png" alt="Historial" class="icono">
            </a>
            <a href="/WebR/CarroCompras/carrito.php">
                <img src="/WebR/img/carrocomprassinfondo.png" alt="Carro de Compras" class="icono">
            </a>
        </div>
    </div>
</header>

<main>
    <section class="confirmacion-page">
        <div class="confirmacion-container">
            <?php
            if (isset($_SESSION['mensaje_error'])) {
                echo '<div class="mensaje-error">' . htmlspecialchars($_SESSION['mensaje_error']) . '</div>';
                unset($_SESSION['mensaje_error']); 
            }

            if (isset($_SESSION['compra_finalizada']) && $_SESSION['compra_finalizada'] === true) {
                echo '<h1>¡Gracias por tu compra!</h1>';
                echo '<p>Tu pedido ha sido procesado con éxito.</p>';
                echo '<div class="order-details">';
                echo '<p>Número de Pedido: <span>' . htmlspecialchars($_SESSION['id_ultima_venta']) . '</span></p>';
                echo '<p>Total de la Compra: <span>S/. ' . number_format($_SESSION['total_ultima_compra'], 2) . '</span></p>';
                echo '</div>';
                echo '<a href="Home.php" class="btn-accion">Volver a la tienda</a>';
                unset($_SESSION['compra_finalizada']);
                unset($_SESSION['id_ultima_venta']);
                unset($_SESSION['total_ultima_compra']);
            } else {
                echo '<h1>Acceso Denegado</h1>';
                echo '<p>Parece que has llegado aquí sin finalizar una compra. Por favor, visita tu <a href="carrito.php">carrito</a> o <a href="Productos.php">explora nuestros productos</a>.</p>';
                echo '<a href="Home.php" class="btn-accion">Ir a Inicio</a>';
            }
            ?>
        </div>
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