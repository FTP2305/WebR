<?php
session_start();
include '../Includes/conexion.php';

$conexion = new Conexion();
$conn = $conexion->getConectar();

// --- VERIFICACIÓN DE SESIÓN (CRUCIAL) ---
// Si el cliente no está logueado, redirigirlo a la página de login.
if (!isset($_SESSION['id_cliente'])) {
    header('Location: http://localhost/WebR/Clientes/Login.php'); // Asegura esta ruta
    exit();
}

$id_cliente = $_SESSION['id_cliente'];

// --- LÓGICA PARA ACTUALIZAR CANTIDAD ---
if (isset($_POST['actualizar_cantidad'])) {
    $id_producto_update = (int)$_POST['id_producto'];
    $nueva_cantidad = (int)$_POST['cantidad'];

    if ($nueva_cantidad <= 0) {
        // Si la cantidad es 0 o menos, eliminar el producto del carrito
        $sql_delete = "DELETE FROM carrito_compras WHERE id_cliente = ? AND id_producto = ? AND estado = 'activo'";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("ii", $id_cliente, $id_producto_update);
        $stmt_delete->execute();
    } else {
        // Si la cantidad es válida, actualizarla
        $sql_update = "UPDATE carrito_compras SET cantidad = ? WHERE id_cliente = ? AND id_producto = ? AND estado = 'activo'";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("iii", $nueva_cantidad, $id_cliente, $id_producto_update);
        $stmt_update->execute();
    }
    // Redirigir para evitar reenvío del formulario
    header("Location: /WebR/CarroCompras/carrito.php");
    exit();
}

// --- LÓGICA PARA ELIMINAR PRODUCTO ---
if (isset($_POST['eliminar_producto'])) {
    $id_producto_delete = (int)$_POST['id_producto'];

    $sql_delete = "DELETE FROM carrito_compras WHERE id_cliente = ? AND id_producto = ? AND estado = 'activo'";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("ii", $id_cliente, $id_producto_delete);
    $stmt_delete->execute();

    // Redirigir para evitar reenvío del formulario
    header("Location: /WebR/CarroCompras/carrito.php");
    exit();
}

// --- OBTENER PRODUCTOS DEL CARRITO (Después de posibles actualizaciones/eliminaciones) ---
// Agregamos p.imagen_url para mostrar la imagen del producto
$sql = "SELECT p.id_producto, p.nombre, p.precio, p.imagen_url, c.cantidad, (p.precio * c.cantidad) AS subtotal
        FROM carrito_compras c
        JOIN productos p ON c.id_producto = p.id_producto
        WHERE c.id_cliente = ? AND c.estado = 'activo'";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error al preparar la consulta del carrito: " . $conn->error);
}
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$result = $stmt->get_result();

// Calcular el total del carrito
$total_carrito = 0;
$productos_en_carrito = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productos_en_carrito[] = $row; // Almacenar los productos en un array
        $total_carrito += $row['subtotal'];
    }
}

// Ahora cerramos la conexión para liberar recursos
$stmt->close();
$conn->close();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TITI SHOP - Tu Carrito</title>
    <link rel="stylesheet" href="/WebR/css/Home.css">
    <link rel="stylesheet" href="/WebR/css/base.css">
    <link rel="stylesheet" href="/WebR/css/components.css">
    <link rel="stylesheet" href="/WebR/css/carrito.css">
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
                <img src="/WebR/mg/loginsinfondo.png" alt="Login" class="icono">
            </a>
            <?php endif; ?>
            <a href="/WebR/CarroCompras/historial_compras.php">
                <img src="/WebR/img/historial de compras.png" alt="Historial" class="icono">
            </a>
            <a href="/WebR/CarroCompras/carrito.php">
                <img src="/WebR/img/carrocomprassinfondo.png" alt="Carro de Compras" class="icono">
            </a>
        </div>
    </div>
</header>

<main>
    <section class="carrito-page">
        <h1>Tu Carrito de Compras</h1>

        <?php if (!empty($productos_en_carrito)): ?>
            <div class="carrito-container">
                <div class="lista-productos">
                    <div class="lista-productos-header">
                        <div class="header-item">Producto</div>
                        <div class="header-item">Precio</div>
                        <div class="header-item">Cantidad</div>
                        <div class="header-item">Subtotal</div>
                        <div class="header-item">Acciones</div>
                    </div>

                    <?php foreach ($productos_en_carrito as $row): ?>
                        <div class="producto-item">
                            <div class="producto-info-cell">
                                <img src="/WebR/<?php echo htmlspecialchars($row['imagen_url']); ?>" alt="<?php echo htmlspecialchars($row['nombre']); ?>">
                                <span><?php echo htmlspecialchars($row['nombre']); ?></span>
                            </div>
                            <div class="producto-precio">S/. <?php echo number_format($row['precio'], 2); ?></div>
                            <div class="producto-cantidad">
                                <form method="POST" action="carrito.php" class="cantidad-form">
                                    <input type="hidden" name="id_producto" value="<?php echo htmlspecialchars($row['id_producto']); ?>">
                                    <input type="number" name="cantidad" value="<?php echo htmlspecialchars($row['cantidad']); ?>" min="0" class="input-cantidad">
                                    <button type="submit" name="actualizar_cantidad" class="btn-actualizar-cantidad">Actualizar</button>
                                </form>
                            </div>
                            <div class="producto-subtotal">S/. <?php echo number_format($row['subtotal'], 2); ?></div>
                            <div class="producto-acciones">
                                <form method="POST" action="carrito.php">
                                    <input type="hidden" name="id_producto" value="<?php echo htmlspecialchars($row['id_producto']); ?>">
                                    <button type="submit" name="eliminar_producto" class="btn-eliminar">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>

                <div class="resumen-pedido">
                    <h2>Resumen del Pedido</h2>
                    <div class="resumen-item">
                        <span>Subtotal de productos:</span>
                        <span>S/. <?php echo number_format($total_carrito, 2); ?></span>
                    </div>
                    <div class="resumen-item">
                        <span>Envío:</span>
                        <span>S/. 0.00</span>
                    </div>
                    <div class="resumen-total">
                        <span>Total:</span>
                        <span>S/. <?php echo number_format($total_carrito, 2); ?></span>
                    </div>
                    <form method="POST" action="/WebR/CarroCompras/finalizar_compra.php">
                        <button type="submit" name="finalizar_compra_btn" class="btn-finalizar">Finalizar Compra</button>
                    </form>
                    <a href="/WebR/Productos/Productos.php" class="btn-continuar">Continuar Comprando</a>
                </div>
            </div>

        <?php else: ?>
            <div class="carrito-vacio">
                <p>Tu carrito de compras está vacío.</p>
                <a href="/WebR/Productos/Productos.php" class="btn-continuar-comprando">Explorar Productos</a>
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