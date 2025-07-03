<?php 
include '../Includes/conexion.php';

$conexion = new Conexion();
$conn = $conexion->getConectar();

// Crear Usuario
if (isset($_POST['crear'])) {
    $nombre_usuario = $_POST['nombre_usuario'];
    $correo = $_POST['correo'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $id_rol = $_POST['id_rol'];

    $sql = "INSERT INTO usuarios (nombre_usuario, correo, contrasena, id_rol) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nombre_usuario, $correo, $contrasena, $id_rol);
    $stmt->execute();
}

// Eliminar Usuario
if (isset($_GET['eliminar'])) {
    $id_usuario = $_GET['eliminar'];
    $sql = "DELETE FROM usuarios WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
}

// Editar Usuario
if (isset($_GET['editar'])) {
    $id_usuario = $_GET['editar'];
    $sql = "SELECT * FROM usuarios WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();
}

if (isset($_POST['actualizar'])) {
    $id_usuario = $_POST['id_usuario'];
    $nombre_usuario = $_POST['nombre_usuario'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'] ? password_hash($_POST['contrasena'], PASSWORD_DEFAULT) : $_POST['contrasena_old'];
    $id_rol = $_POST['id_rol'];

    $sql = "UPDATE usuarios SET nombre_usuario = ?, correo = ?, contrasena = ?, id_rol = ? WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $nombre_usuario, $correo, $contrasena, $id_rol, $id_usuario);
    $stmt->execute();
}

// Listar Usuarios
$sql = "SELECT * FROM usuarios";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Usuarios</title>
    <link rel="stylesheet" href="/WebR/css/crudUsuarios.css">
</head>
<body>
    <div class="contenedor">
        <!-- Menú -->
        <div class="menu">
            <div class="menuIzquierda">
                <span class="title">Tienda TITISHOP</span> 
            </div>
            <div class="menuDerecha">
                <div class="menus">
                    <a href="/WebR/Productos/listar.php">Productos</a>
                    <a href="/WebR/Clientes/listar.php">Clientes</a>
                    <a href="../Usuarios/listar.php">Usuarios</a>
                    <a href="../Usuarios/registroventas.php">Ventas</a>
                    <a href="../Usuarios/historial.php">Reportes</a>
                    <a href="../Usuarios/adminpag.php">Volver</a>
                </div>
            </div>
        </div>

        <!-- Crear Usuario -->
        <div class="contenido">
            <h2>Agregar Usuario</h2>
            <div class="form-container">
                <form method="POST" action="">
                    <input type="text" name="nombre_usuario" placeholder="Nombre de Usuario" required>
                    <input type="email" name="correo" placeholder="Correo Electrónico" required>
                    <input type="password" name="contrasena" placeholder="Contraseña" required>
                    <select name="id_rol">
                        <option value="1">Administrador</option>
                        <option value="2">Vendedor</option>
                        <option value="3">Cliente</option>
                    </select>
                    <button type="submit" name="crear">Agregar Usuario</button>
                </form>
            </div>

            <!-- Editar Usuario -->
            <?php if (isset($usuario)): ?>
                <div class="form-container">
                    <h2>Editar Usuario</h2>
                    <form method="POST" action="">
                        <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">
                        <input type="text" name="nombre_usuario" value="<?= $usuario['nombre_usuario'] ?>" required>
                        <input type="email" name="correo" value="<?= $usuario['correo'] ?>" required>
                        <input type="password" name="contrasena" placeholder="Contraseña">
                        <input type="hidden" name="contrasena_old" value="<?= $usuario['contrasena'] ?>">
                        <select name="id_rol">
                            <option value="1" <?= $usuario['id_rol'] == 1 ? 'selected' : ''; ?>>Administrador</option>
                            <option value="2" <?= $usuario['id_rol'] == 2 ? 'selected' : ''; ?>>Vendedor</option>
                            <option value="3" <?= $usuario['id_rol'] == 3 ? 'selected' : ''; ?>>Cliente</option>
                        </select>
                        <button type="submit" name="actualizar">Actualizar Usuario</button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Listado de Usuarios -->
            <h2>Listado de Usuarios</h2>
            <div class="tabla">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado && $resultado->num_rows > 0): ?>
                            <?php while ($fila = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $fila['id_usuario'] ?></td>
                                    <td><?= htmlspecialchars($fila['nombre_usuario']) ?></td>
                                    <td><?= $fila['correo'] ?></td>
                                    <td><?= $fila['id_rol'] == 1 ? 'Administrador' : ($fila['id_rol'] == 2 ? 'Vendedor' : 'Cliente') ?></td>
                                    <td>
                                        <a href="?editar=<?= $fila['id_usuario'] ?>" class="btn-edit">Editar</a>
                                        <a href="?eliminar=<?= $fila['id_usuario'] ?>" class="btn-delete" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5">No hay usuarios registrados aún.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
