<?php
session_start();

include('../Includes/conexion.php');;

$conexion = new Conexion();
$conn = $conexion->getConectar();

$categoria_id = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0; 
$precio_min = isset($_GET['precio_min']) && $_GET['precio_min'] !== '' ? floatval($_GET['precio_min']) : 0;
$precio_max = isset($_GET['precio_max']) && $_GET['precio_max'] !== '' ? floatval($_GET['precio_max']) : 0;

$sql = "SELECT * FROM productos WHERE 1";
$conditions = [];
$params = [];
$param_types = '';

if ($categoria_id > 0) {
    $conditions[] = "id_categoria = ?";
    $params[] = $categoria_id;
    $param_types .= 'i';
}
if ($precio_min > 0) {
    $conditions[] = "precio >= ?";
    $params[] = $precio_min;
    $param_types .= 'd';
}
if ($precio_max > 0) {
    $conditions[] = "precio <= ?";
    $params[] = $precio_max;
    $param_types .= 'd';
}

if (!empty($conditions)) {
    $sql .= " AND " . implode(" AND ", $conditions);
}

if ($mas_vendidos) {
    $sql .= " ORDER BY ventas DESC";
} else {
    $sql .= " ORDER BY id_producto ASC";
}

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error al preparar la consulta de productos: " . $conn->error);
}

if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$categorias = [];
$sql_categorias = "SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria ASC";
$result_categorias = $conn->query($sql_categorias);
if ($result_categorias) {
    while ($row_cat = $result_categorias->fetch_assoc()) {
        $categorias[] = $row_cat;
    }
}
if (isset($_POST['agregar_al_carrito'])) {
    if (!isset($_SESSION['id_cliente'])) {
        $_SESSION['mensaje_error'] = "Debes iniciar sesión para agregar productos al carrito.";
        header('Location: http://localhost/WebR/Clientes/Login.php');
        exit();
    }

    $id_cliente = $_SESSION['id_cliente']; 
    $id_producto = (int)$_POST['id_producto'];
    $nombre_producto = htmlspecialchars($_POST['nombre']);
    $precio_producto = floatval($_POST['precio']);
    $cantidad_a_agregar = 1;

    $sql_check_db = "SELECT cantidad FROM carrito_compras WHERE id_cliente = ? AND id_producto = ? AND estado = 'activo'";
    $stmt_check_db = $conn->prepare($sql_check_db);
    $stmt_check_db->bind_param("ii", $id_cliente, $id_producto);
    $stmt_check_db->execute();
    $result_check_db = $stmt_check_db->get_result();

    if ($result_check_db->num_rows > 0) {
        $sql_update_db = "UPDATE carrito_compras SET cantidad = cantidad + ? WHERE id_cliente = ? AND id_producto = ? AND estado = 'activo'";
        $stmt_update_db = $conn->prepare($sql_update_db);
        $stmt_update_db->bind_param("iii", $cantidad_a_agregar, $id_cliente, $id_producto);
        $stmt_update_db->execute();
    } else {
        $sql_insert_db = "INSERT INTO carrito_compras (id_cliente, id_producto, cantidad, estado) VALUES (?, ?, ?, 'activo')";
        $stmt_insert_db = $conn->prepare($sql_insert_db);
        $stmt_insert_db->bind_param("iii", $id_cliente, $id_producto, $cantidad_a_agregar);
        $stmt_insert_db->execute();
    }
    
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = array();
    }
    $encontrado_en_session = false;
    foreach ($_SESSION['carrito'] as $key => $item) {
        if ($item['id_producto'] == $id_producto) {
            $_SESSION['carrito'][$key]['cantidad'] += $cantidad_a_agregar;
            $encontrado_en_session = true;
            break;
        }
    }
    if (!$encontrado_en_session) {
        $_SESSION['carrito'][] = array(
            'id_producto' => $id_producto,
            'nombre' => $nombre_producto,
            'precio' => $precio_producto,
            'cantidad' => $cantidad_a_agregar
        );
    }

    header("Location: Productos.php"); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TITI SHOP | Productos</title>
  <link rel="stylesheet" href="/WebR/css/estilo.css">
  <link rel="stylesheet" href="/WebR/css/productos.css"> <link rel="stylesheet" href="/WebR/css/Home.css">
  <link rel="stylesheet" href="/WebR/css/base.css">
  <link rel="stylesheet" href="/WebR/css/components.css">
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
          <a href="/WebR/PaginaPrincipal/logout.php" style="margin-left: 20px;">
            <img src="/WebR/img/cerrarsesion1-removebg-preview.png" alt="Cerrar sesión" class="icono" >
          </a>
        <?php else: ?>
          <a href="http://localhost/WebR/Clientes/Login.php">
            <img src="/WebR/img/loginsinfondo.png" alt="Usuario" class="icono">
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
  <section class="productos-page">
    <aside class="filtros">
      <h3>Filtrar</h3>
      <form method="GET" action="Productos.php">
        <h4>Categoría</h4>
        <select name="categoria">
            <option value="0">Todas las categorías</option>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?php echo htmlspecialchars($cat['id_categoria']); ?>" <?php echo ($categoria_id == $cat['id_categoria']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['nombre_categoria']); ?>
                </option>
            <?php endforeach; ?>
        </select>
       

        <h4>Precio</h4>
        <input type="number" name="precio_min" placeholder="Desde S/." class="precio-input" value="<?php echo ($precio_min > 0) ? $precio_min : ''; ?>">
        <input type="number" name="precio_max" placeholder="Hasta S/." class="precio-input" value="<?php echo ($precio_max > 0) ? $precio_max : ''; ?>">
        <button class="btn-filtrar" type="submit">Aplicar</button>
      </form>
    </aside>

    <div class="contenido-productos">
      <div class="barra-superior">
        <h2>Todos los productos</h2>
        </div>

      <div class="grid-productos">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <div class="producto">
                <div class="producto-centrado">
                  <img src="/WebR/<?php echo htmlspecialchars($row['imagen_url']); ?>" alt="<?php echo htmlspecialchars($row['nombre']); ?>">
                  <div class="producto-info">
                    <p class="producto-descripcion"><?php echo htmlspecialchars($row['nombre']); ?></p>
                    <p class="producto-especific"><?php echo htmlspecialchars($row['descripcion']); ?></p>
                    <p class="producto-precio">S/. <?php echo number_format($row['precio'], 2); ?></p>
                    <form method="POST" action="Productos.php">
                        <input type="hidden" name="id_producto" value="<?php echo htmlspecialchars($row['id_producto']); ?>">
                        <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($row['nombre']); ?>">
                        <input type="hidden" name="precio" value="<?php echo htmlspecialchars($row['precio']); ?>">
                        <button class="btn-filtrar" type="submit" name="agregar_al_carrito">Agregar al carrito</button>
                    </form>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-products-message">No se encontraron productos que coincidan con los filtros aplicados.</p>
        <?php endif; ?>
      </div>
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