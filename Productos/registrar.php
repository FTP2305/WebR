<?php 
include '../Includes/conexion.php';

$conexion = new Conexion();
$conn = $conexion->getConectar();

if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo "<script>alert('Producto registrado correctamente con imagen');</script>";
}

// Obtener categorías para el formulario
$sqlCat = "SELECT id_categoria, nombre_categoria FROM categorias";
$categorias = $conn->query($sqlCat);

// Registrar producto si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $precio = $_POST["precio"];
    $stock = $_POST["stock"];
    $id_categoria = $_POST["id_categoria"];

    // SUBIDA DE IMAGEN
    $nombreArchivo = basename($_FILES["imagen"]["name"]);
    $rutaDestino = "../img/" . $nombreArchivo;
    $subidaExitosa = move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino);

    if ($subidaExitosa) {
        $sqlInsert = "INSERT INTO productos (nombre, descripcion, precio, stock, imagen_url, id_categoria)
                      VALUES ('$nombre', '$descripcion', '$precio', '$stock', '$nombreArchivo', '$id_categoria')";

        if ($conn->query($sqlInsert) === TRUE) {
            header("Location: registrar.php?success=1");
            exit(); // Detiene el resto del script para evitar ejecución doble
        } else {
            echo "<script>alert('Error al guardar en BD: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Error al subir la imagen al servidor');</script>";
    }
}

// Consulta productos para mostrar tabla
$sqlProductos = "SELECT p.id_producto, p.nombre, p.precio, p.stock, c.nombre_categoria 
                 FROM productos p
                 INNER JOIN categorias c ON p.id_categoria = c.id_categoria";
$productos = $conn->query($sqlProductos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Producto</title>
    <link rel="stylesheet" href="/WebR/css/proregis.css">
</head>
<body>
<div class="contenedor">
    <div class="menu">
        <div class="menuIzquierda">
            <span class="title">Tienda TITISHOP</span>
        </div>
        <div class="menuDerecha">
            <div class="menus">
                <a href="listar.php">Productos</a>
                <a href="../Clientes/listar.php">Clientes</a>
                <a href="../Usuarios/listar.php">Usuarios</a>
                <a href="../Usuarios/registroventas.php">Ventas</a>
                <a href="../Usuarios/historial.php">Reportes</a>
            </div>
        </div>
    </div>

    <div class="contenido">
        <h2 class="centrar">🛒 Registro de Productos</h2>
        <div class="tabla">
            <!-- Tabla izquierda -->
            <div class="tablaLeft">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Categoría</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($fila = $productos->fetch_assoc()): ?>
                            <tr>
                                <td><?= $fila['id_producto'] ?></td>
                                <td><?= htmlspecialchars($fila['nombre']) ?></td>
                                <td>S/ <?= number_format($fila['precio'], 2) ?></td>
                                <td><?= $fila['stock'] ?></td>
                                <td><?= $fila['nombre_categoria'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Formulario a la derecha -->
            <div class="tablaRight">
                <div class="rounded1">
                    <form method="post" action="" enctype="multipart/form-data">
                        <label class="etiqueta">Nombre</label><br>
                        <input type="text" class="form-control" name="nombre" required><br>

                        <label class="etiqueta">Descripción</label><br>
                        <textarea class="form-control" name="descripcion" rows="3" required></textarea><br>

                        <label class="etiqueta">Precio</label><br>
                        <input type="number" step="0.01" class="form-control" name="precio" required><br>

                        <label class="etiqueta">Stock</label><br>
                        <input type="number" class="form-control" name="stock" required><br>

                        <label class="etiqueta">Imagen (archivo)</label><br>
                        <input type="file" class="form-control" name="imagen" accept="image/*" required>

                        <label class="etiqueta">Categoría</label><br>
                        <select name="id_categoria" class="form-control" required>
                            <option value="">Seleccione</option>
                            <?php while ($cat = $categorias->fetch_assoc()): ?>
                                <option value="<?= $cat['id_categoria'] ?>"><?= $cat['nombre_categoria'] ?></option>
                            <?php endwhile; ?>
                        </select><br>

                        <div class="d-grid">
                            <button type="submit" class="registrar">Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>    
    </div>
</div>
</body>
</html>