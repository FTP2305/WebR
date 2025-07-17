<?php 
include '../Includes/conexion.php';

$conexion = new Conexion();
$conn = $conexion->getConectar();

// Consulta para obtener las direcciones (distritos) únicos
$sql_direcciones = "SELECT DISTINCT direccion FROM clientes ORDER BY direccion";
$resultado_direcciones = $conn->query($sql_direcciones);

$sql = "SELECT * FROM clientes";
if (isset($_POST['buscar'])) {
    if (!empty($_POST['nombre_cliente'])) {
        $busqueda = $_POST['nombre_cliente'];
        $sql = "SELECT * FROM clientes WHERE nombre LIKE '%$busqueda%'";
    }
}
if (isset($_POST['filtrar_direccion'])) {
    $direccion = $_POST['direccion'];
    if (!empty($direccion)) {
        $sql = "SELECT * FROM clientes WHERE direccion = '$direccion'";
    }
}
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes</title>
    <link rel="stylesheet" href="/WebR/css/clientelist.css"> 
</head>
<body>
    <div class="contenedor">
        <div class="menu">
            <div class="menuIzquierda">
                <span class="title">Tienda TITISHOP</span> 
            </div>
            <div class="menuDerecha">
                <div class="menus">
                <a href="/WebR/Productos/listar.php">Productos</a>
                <a href="/WebR/Clientes/listar.php">Clientes</a>
                <a href="/WebR/Usuarios/listar.php">Usuarios</a>
                <a href="/WebR/Usuarios/registroventas.php">Ventas</a>
                <a href="/WebR/Usuarios/historial.php">Reportes</a>
            </div>
            </div>
        </div>

        <div class="contenido">
            <h2 class="centrar">📋 Listado de Clientes</h2>
            <div class="tabla" style="gap: 30px; align-items: flex-start;">
                <div class="tablaLeft" style="width: 65%;">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellidos</th>
                                <th>Correo</th>
                                <th>Dirección</th>
                                <th>Tipo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($resultado->num_rows > 0) {
                                while($row = $resultado->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?php echo $row['id_cliente']; ?></td>
                                        <td><?php echo $row['nombre']; ?></td>
                                        <td><?php echo $row['apellidos']; ?></td>
                                        <td><?php echo $row['correo']; ?></td>
                                        <td><?php echo $row['direccion']; ?></td>
                                        <td><?php echo $row['es_registrado'] ? 'Registrado' : 'No registrado'; ?></td>
                                    </tr>
                            <?php } 
                            } else {
                                echo "<tr><td colspan='6'>No se encontraron clientes.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="tablaRight" style="width: 30%;">
                    <div style="background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                        <h3 style="text-align: center; margin-bottom: 15px;">🔍 Filtros de Búsqueda</h3>
                        <form method="post" action="">
                            <div style="margin-bottom: 20px;">
                                <label for="nombre_cliente" style="font-weight: bold;">Buscar por Nombre:</label>
                                <input type="text" name="nombre_cliente" placeholder="Ingrese nombre" class="form-control" style="width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px;">

                                <button type="submit" name="buscar" class="registrar" style="width: 100%; padding: 10px; background-color: #1e2f3a; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
                                    Buscar por Nombre
                                </button>
                            </div>
                            
                            <div>
                                <label for="direccion" style="font-weight: bold;">Filtrar por Dirección/Distrito:</label>
                                <select name="direccion" class="form-control" style="width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px;">
                                    <option value="">-- Todas las direcciones --</option>
                                    <?php 
                                    if ($resultado_direcciones->num_rows > 0) {
                                        while($row = $resultado_direcciones->fetch_assoc()) {
                                            echo "<option value='".htmlspecialchars($row['direccion'])."'>".htmlspecialchars($row['direccion'])."</option>";
                                        }
                                    }
                                    ?>
                                </select>
                    
                                <button type="submit" name="filtrar_direccion" class="registrar" style="width: 100%; padding: 10px; background-color: #1e2f3a; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
                                    Filtrar por Dirección
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>    
        </div>
    </div>
</body>
</html>