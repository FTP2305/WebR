<?php 
include '../Includes/conexion.php';

$conexion = new Conexion();
$conn = $conexion->getConectar();

// Crear Cliente
if (isset($_POST['crear'])) {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $direccion = $_POST['direccion'];

    $sql = "INSERT INTO clientes (nombre, correo, direccion) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nombre, $correo, $direccion);
    $stmt->execute();
}

// Eliminar Cliente
if (isset($_GET['eliminar'])) {
    $id_cliente = $_GET['eliminar'];
    $sql = "DELETE FROM clientes WHERE id_cliente = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_cliente);
    $stmt->execute();
}

// Editar Cliente
if (isset($_GET['editar'])) {
    $id_cliente = $_GET['editar'];
    $sql = "SELECT * FROM clientes WHERE id_cliente = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_cliente);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $cliente = $resultado->fetch_assoc();
}

if (isset($_POST['actualizar'])) {
    $id_cliente = $_POST['id_cliente'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $direccion = $_POST['direccion'];

    $sql = "UPDATE clientes SET nombre = ?, correo = ?, direccion = ? WHERE id_cliente = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nombre, $correo, $direccion, $id_cliente);
    $stmt->execute();
}

// Listar Clientes
$sql = "SELECT * FROM clientes";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Clientes</title>
    <link rel="stylesheet" href="/WebR/css/crudClientes.css">
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
                    <a href="listar.php">Clientes</a>
                    <a href="../Usuarios/listar.php">Usuarios</a>
                    <a href="../Usuarios/registroventas.php">Ventas</a>
                    <a href="../Usuarios/historial.php">Reportes</a>
                    <a href="../Usuarios/adminpag.php">Volver</a>
                </div>
            </div>
        </div>

        <!-- Crear Cliente -->
        <div class="contenido">
            <h2>Agregar Cliente</h2>
            <div class="form-container">
                <form method="POST" action="">
                    <input type="text" name="nombre" placeholder="Nombre del Cliente" required>
                    <input type="email" name="correo" placeholder="Correo Electrónico" required>
                    <input type="text" name="direccion" placeholder="Dirección" required>
                    <button type="submit" name="crear">Agregar Cliente</button>
                </form>
            </div>
        <!-- Editar Cliente -->
        <?php if (isset($cliente)): ?>
            <div class="form-container">
                <h2>Editar Cliente</h2>
                <form method="POST" action="">
                    <input type="hidden" name="id_cliente" value="<?= $cliente['id_cliente'] ?>">
                    <input type="text" name="nombre" value="<?= $cliente['nombre'] ?>" required>
                    <input type="email" name="correo" value="<?= $cliente['correo'] ?>" required>
                    <input type="text" name="direccion" value="<?= $cliente['direccion'] ?>" required>
                    <button type="submit" name="actualizar">Actualizar Cliente</button>
                </form>
            </div>
        <?php endif; ?>
            <!-- Listado de Clientes -->
            <h2>Listado de Clientes</h2>
            <div class="tabla">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Dirección</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado && $resultado->num_rows > 0): ?>
                            <?php while ($fila = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $fila['id_cliente'] ?></td>
                                    <td><?= htmlspecialchars($fila['nombre']) ?></td>
                                    <td><?= $fila['correo'] ?></td>
                                    <td><?= $fila['direccion'] ?></td>
                                    <td>
                                        <a href="?editar=<?= $fila['id_cliente'] ?>" class="btn-edit">Editar</a>
                                        <a href="?eliminar=<?= $fila['id_cliente'] ?>" class="btn-delete" onclick="return confirm('¿Estás seguro de eliminar este cliente?')">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5">No hay clientes registrados aún.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
    </div>
</body>
</html>
