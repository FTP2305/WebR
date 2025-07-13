<?php
session_start();
include('../Includes/conexion.php');

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

$sql .= " ORDER BY id_producto ASC";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error al preparar la consulta: " . $conn->error);
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
?>

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
          <p class="no-products-message">No se encontraron productos.</p>
      <?php endif; ?>
    </div>
  </div>
</section>
