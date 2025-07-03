<?php
include '../Includes/conexion.php';
$conexion = new Conexion();
$conn = $conexion->getConectar();


$sql = "SELECT id_usuario, nombre_usuario, correo, contrasena FROM usuarios"; 


if (isset($_POST['buscar'])) {
    $busqueda = trim($_POST['nombre_usuario']); 

    $sql = "SELECT id_usuario, nombre_usuario, correo, contrasena FROM usuarios WHERE nombre_usuario LIKE ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $param = "%" . $busqueda . "%"; 
        $stmt->bind_param("s", $param);
        $stmt->execute();
        $resultado = $stmt->get_result(); 
    } else {
        
        echo "Error al preparar la consulta de búsqueda: " . $conn->error;
        $resultado = false; 
    }
} else {
   
    $resultado = $conn->query($sql);
}


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Usuarios</title> <link rel="stylesheet" href="/WebR/css/usulist.css">
    </head>
<body>
    <div class="contenedor">
        <div class="menu">
            <div class="menuIzquierda">
                <span class="title">Tienda Admin</span>
            </div>
            <div class="menuDerecha">
                <div class="menus">
                    <a href="/WebR/Productos/listar.php">Productos</a>
                    <a href="/WebR/Cleintes/listar.php">Clientes</a>
                    <a href="/WebR/Usuarios/listar.php">Usuarios</a>
                    <a href="/WebR/Usuarios/registroventas.php">Ventas</a>
                    <a href="/WebR/Usuarios/historial.php">Reportes</a>
                </div>
            </div>
        </div>

        <div class="contenido">
            <h2 class="centrar">📋 Listado de Usuarios</h2>
            <div class="tabla" style="gap: 30px; align-items: flex-start;">
                <div class="tablaLeft" style="width: 65%;">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th> <th>Correo</th>
                                <th>Contraseña (Hash)</th> 
                                <th>Eliminar</th></tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($resultado && $resultado->num_rows > 0) { 
                                while($row = $resultado->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id_usuario']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nombre_usuario']); ?></td> <td><?php echo htmlspecialchars($row['correo']); ?></td>
                                        <td><?php echo htmlspecialchars($row['contrasena']); ?></td>
                                        <td><a href="eliminar_usuario.php?id=<?php echo $row['id_usuario']; ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?')">Eliminar</a></td>
                                        </tr>
                                <?php }
                            } else {
                                echo "<tr><td colspan='4'>No se encontraron usuarios.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="tablaRight" style="width: 30%;">
                    <div style="background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                        <h3 style="text-align: center; margin-bottom: 15px;">🔍 Buscar Usuario</h3>
                        <form method="post" action="">
                            <label for="nombre_usuario" style="font-weight: bold;">Nombre:</label>
                            <input type="text" id="nombre_usuario" name="nombre_usuario" placeholder="Ingrese nombre" class="form-control" style="width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px;" required>

                            <button type="submit" name="buscar" class="registrar" style="width: 100%; padding: 10px; background-color: #1e2f3a; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
                                Buscar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>