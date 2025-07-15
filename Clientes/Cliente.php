<?php
session_start();
include '../Includes/conexion.php';

if (!isset($_SESSION['id_cliente'])) {
    header("Location: Login.php");  // Redirigir si no está logueado
    exit();
}

// Conexión a la base de datos
$conexion = new Conexion();
$conn = $conexion->getConectar();

$id_cliente = $_SESSION['id_cliente'];

// Obtener datos del cliente
$sql_cliente = "SELECT * FROM clientes WHERE id_cliente = ?";
$stmt = $conn->prepare($sql_cliente);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$result = $stmt->get_result();
$cliente = $result->fetch_assoc();

// Si el formulario es enviado para actualizar datos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar'])) {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $direccion = $_POST['direccion'];

    // Actualizar cliente
    $sql_update = "UPDATE clientes SET nombre = ?, correo = ?, direccion = ? WHERE id_cliente = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssi", $nombre, $correo, $direccion, $id_cliente);
    $stmt_update->execute();

    // Mensaje de éxito
    $mensaje = "Datos actualizados correctamente!";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Cliente - TiTiShop</title>
    <link rel="stylesheet" href="../Home.css">
    <link rel="stylesheet" href="../base.css">
    <link rel="stylesheet" href="../components.css">
    <link rel="stylesheet" href="perfil.css"> <link rel="stylesheet" href="responsive.css">
</head>
<body>

  <!-- ENCABEZADO -->
  <header>
    <div class="navbar">
      <img src="..//img/LOGOTITI.jpeg" alt="Logo TITI SHOP" class="logo">
      <h3><a href="..//Home.php" style="color: black;">Inicio</a></h3>
      <h3><a href="..//Productos.php" style="color: black;">Productos</a></h3>
      <h3><a href="..//Contactanos.php" style="color: black;">Contáctanos</a></h3>
      <h3><a href="..//Nosotros.php" style="color: black;">Nosotros</a></h3>
      <h3><a href="..//Preguntas.php" style="color: black;">Preguntas Frecuentes</a></h3>
      <h3><a href="..//intranet.php" style="color: black;">Intranet</a></h3>

      <div class="user-menu">
        <span style="color:black; font-weight: bold; font-size: 20px;">
          Hola, <?php echo htmlspecialchars($cliente['nombre']); ?>
        </span>
        <a href="logout.php" style="margin-left: 20px;">
          <img src="..//img/cerrarsesion1-removebg-preview.png" alt="Cerrar sesión" class="icono">
        </a>
      </div>
    </div>
  </header>

  <!-- SECCIÓN DE PERFIL -->
  <main>
    <section class="perfil">
      <div class="container">
        <h1>Mi Perfil</h1>
        
        <!-- Mensaje de éxito al actualizar -->
        <?php if (isset($mensaje)) { ?>
          <p class="mensaje-exito"><?php echo $mensaje; ?></p>
        <?php } ?>

        <form action="Cliente.php" method="POST">
          <label for="nombre">Nombre:</label>
          <input type="text" name="nombre" value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required>

          <label for="correo">Correo:</label>
          <input type="email" name="correo" value="<?php echo htmlspecialchars($cliente['correo']); ?>" required>

          <label for="direccion">Dirección:</label>
          <textarea name="direccion" rows="4" required><?php echo htmlspecialchars($cliente['direccion']); ?></textarea>

          <button type="submit"  name="actualizar">Actualizar Datos</button>
        </form>
        
      </div>
    </section>
  </main>

</body>
</html>