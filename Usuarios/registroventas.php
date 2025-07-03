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


if (!isset($_SESSION['productos']) || !is_array($_SESSION['productos'])) {
    $_SESSION['productos'] = [];
}


$sql_productos = "SELECT id_producto, nombre, precio, stock FROM productos"; 
$resultado_productos = $conn->query($sql_productos);


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $producto_id = isset($_POST['producto_id']) ? intval($_POST['producto_id']) : 0;
    $cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;

    if ($producto_id <= 0 || $cantidad <= 0) {
        $mensaje = "Debe seleccionar un producto y una cantidad válida (mínimo 1).";
    } else {
        if (isset($_POST['cliente_id'])) {
            $_SESSION['cliente_id'] = intval($_POST['cliente_id']);
        }

        $sql_producto = "SELECT nombre, precio, stock FROM productos WHERE id_producto = ?";
        $stmt_producto = $conn->prepare($sql_producto);

        if ($stmt_producto === false) {
            $mensaje = "Error al preparar la consulta de producto: " . htmlspecialchars($conn->error);
        } else {
            $stmt_producto->bind_param("i", $producto_id);
            $stmt_producto->execute();
            $stmt_producto->store_result();
            $stmt_producto->bind_result($nombre_producto, $precio, $stock);
            $stmt_producto->fetch();

            if ($stmt_producto->num_rows === 0) {
                $mensaje = "Producto no encontrado.";
            } elseif ($cantidad > $stock) {
                $mensaje = "No hay suficiente stock del producto: " . htmlspecialchars($nombre_producto) . ". Quedan $stock unidades disponibles.";
            } else {
                $producto_en_sesion = false;
                
                
                foreach ($_SESSION['productos'] as &$producto) {
                    if ($producto['producto_id'] == $producto_id) {
                        
                        if (($producto['cantidad'] + $cantidad) > $stock) {
                            $mensaje = "No se puede añadir más. La cantidad total para '" . htmlspecialchars($nombre_producto) . "' excedería el stock disponible ($stock).";
                        } else {
                            $producto['cantidad'] += $cantidad;
                            $producto['subtotal'] = $producto['precio'] * $producto['cantidad'];
                            $mensaje = "Cantidad de '" . htmlspecialchars($nombre_producto) . "' actualizada.";
                        }
                        $producto_en_sesion = true;
                        break; 
                    }
                }
                unset($producto); 

                
                if (!$producto_en_sesion) {
                    $_SESSION['productos'][] = [
                        'producto_id' => $producto_id,
                        'nombre' => $nombre_producto,
                        'cantidad' => $cantidad,
                        'precio' => $precio,
                        'subtotal' => $precio * $cantidad
                    ];
                    $mensaje = "Producto '" . htmlspecialchars($nombre_producto) . "' agregado correctamente.";
                }
            }
            $stmt_producto->close();
        }
    }
    header("Location: registroventas.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['finalizar_venta'])) {
    $cliente_id = isset($_POST['cliente_id']) ? intval($_POST['cliente_id']) : 0; 
    $total = 0;
    
    
    $productos = $_SESSION['productos']; 

    if (empty($productos)) {
        $mensaje = "No hay productos en el carrito para finalizar la venta.";
    } elseif ($cliente_id <= 0) {
        $mensaje = "Debe seleccionar un cliente válido para finalizar la venta.";
    } else {
        
        $_SESSION['cliente_id'] = $cliente_id;

        $conn->begin_transaction(); 
        try {
            
            $sql_venta = "INSERT INTO ventas (id_cliente, total) VALUES (?, ?)";
            $stmt_venta = $conn->prepare($sql_venta);
            if ($stmt_venta === false) {
                
                throw new Exception("Error al preparar la consulta de venta: " . htmlspecialchars($conn->error));
            }
            $stmt_venta->bind_param("id", $cliente_id, $total); 
            $stmt_venta->execute();
            $venta_id = $stmt_venta->insert_id; 
            $stmt_venta->close();

            
            $sql_detalle = "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, subtotal) VALUES (?, ?, ?, ?)";
            $stmt_detalle = $conn->prepare($sql_detalle);
            if ($stmt_detalle === false) {
                throw new Exception("Error al preparar la consulta de detalle de venta: " . htmlspecialchars($conn->error));
            }

            $sql_update_stock = "UPDATE productos SET stock = stock - ? WHERE id_producto = ?";
            $stmt_update_stock = $conn->prepare($sql_update_stock);
            if ($stmt_update_stock === false) {
                throw new Exception("Error al preparar la consulta de actualización de stock: " . htmlspecialchars($conn->error));
            }

            foreach ($productos as $producto) {
                $stmt_detalle->bind_param("iiid", $venta_id, $producto['producto_id'], $producto['cantidad'], $producto['subtotal']);
                $stmt_detalle->execute();

                $stmt_update_stock->bind_param("ii", $producto['cantidad'], $producto['producto_id']);
                $stmt_update_stock->execute();

                $total += $producto['subtotal']; 
            }
            $stmt_detalle->close();
            $stmt_update_stock->close();

            $sql_update_total = "UPDATE ventas SET total = ? WHERE id_venta = ?";
            $stmt_update_total = $conn->prepare($sql_update_total);
            if ($stmt_update_total === false) {
                throw new Exception("Error al preparar la consulta de actualización de total de venta: " . htmlspecialchars($conn->error));
            }
            $stmt_update_total->bind_param("di", $total, $venta_id);
            $stmt_update_total->execute();
            $stmt_update_total->close();

            $conn->commit();

            unset($_SESSION['productos']); 
            unset($_SESSION['cliente_id']); 

            $mensaje = "Venta registrada exitosamente.";

        } catch (Exception $e) {
            $conn->rollback();
            $mensaje = "Error al registrar la venta: " . htmlspecialchars($e->getMessage());
        }
    }
    header("Location: registroventas.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Panel de Vendedor - Titishop</title>
    <link rel="stylesheet" href="/WebR/css/vendedor.css" />
    <link rel="stylesheet" href="/WebR/css/regisven.css" />
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
                <li><a href="/WebR/PaginaPrincipal/logout.php">Cerrar Sesión <i class="fas fa-sign-out-alt"></i></a></li>
            </ul>
        </nav>
    </header>

    <main class="dashboard-content">
        <section class="register-sale-section">
            <h1>Registrar Venta</h1>
            <form method="POST" action="registroventas.php">
                <div class="form-group">
                    <label for="cliente_id">Cliente</label>
                    <select name="cliente_id" required>
                        <option value="">Seleccionar cliente</option>
                        <?php
                        
                        $sql_clientes = "SELECT id_cliente, nombre, apellidos FROM clientes";
                        $resultado_clientes = $conn->query($sql_clientes);

                        
                        $cliente_seleccionado = isset($_SESSION['cliente_id']) ? $_SESSION['cliente_id'] : '';

                        if ($resultado_clientes) { 
                            while ($cliente = $resultado_clientes->fetch_assoc()) {
                                
                                $selected = ($cliente['id_cliente'] == $cliente_seleccionado) ? 'selected' : '';
                                echo "<option value='{$cliente['id_cliente']}' $selected>" . htmlspecialchars($cliente['nombre']) . " " . htmlspecialchars($cliente['apellidos']) . "</option>";
                            }
                        } else {
                            echo "<option value=''>Error al cargar clientes</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="productos">Productos</label>
                    <div id="productos">
                        <select name="producto_id" required>
                            <option value="">Seleccionar producto</option>
                            <?php
                            
                            if ($resultado_productos) { 
                                $resultado_productos->data_seek(0); 
                                while ($producto = $resultado_productos->fetch_assoc()) {
                                    
                                    echo "<option value='{$producto['id_producto']}'>" . htmlspecialchars($producto['nombre']) . " - S/ " . number_format($producto['precio'], 2) . "</option>";
                                }
                            } else {
                                echo "<option value=''>Error al cargar productos</option>";
                            }
                            ?>
                        </select>
                        <input type="number" name="cantidad" placeholder="Cantidad" min="1" value="1" required />
                        <button type="submit" name="add_product" class="submit-btn">Agregar Producto</button>
                    </div>
                </div>

                <div class="product-table">
                    <h3>Productos Agregados</h3>
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
                            
                            if (!empty($_SESSION['productos'])) { 
                                $total_venta_actual = 0; 
                                foreach ($_SESSION['productos'] as $producto) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($producto['cantidad']); ?></td>
                                        <td>S/ <?php echo number_format($producto['subtotal'], 2); ?></td>
                                    </tr>
                                <?php
                                    $total_venta_actual += $producto['subtotal'];
                                } ?>
                                <tr>
                                    <td colspan="2" style="text-align: right; font-weight: bold;">TOTAL:</td>
                                    <td>S/ <?php echo number_format($total_venta_actual, 2); ?></td>
                                </tr>
                            <?php
                            } else { ?>
                                <tr>
                                    <td colspan="3">No hay productos agregados.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <button type="submit" name="finalizar_venta" class="submit-btn">Finalizar Venta</button>
            </form>

            <div class="mensaje">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        </section>
    </main>
</body>
</html>