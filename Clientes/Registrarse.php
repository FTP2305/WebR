<?php
include '../Includes/conexion.php';

$mensaje = "";

// Verificación de formulario enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    $conexion = new Conexion();
    $conn = $conexion->getConectar();

    // Verificar si el correo ya está registrado
    $verificar = "SELECT * FROM clientes WHERE correo = ?";
    $stmt_verificar = $conn->prepare($verificar);
    $stmt_verificar->bind_param("s", $correo);
    $stmt_verificar->execute();
    $verificado = $stmt_verificar->get_result();

    if ($verificado->num_rows > 0) {
        $mensaje = "El correo ya está registrado.";
    } else {
        // Encriptar la contraseña
        $contrasena_hash = password_hash($contrasena, PASSWORD_BCRYPT);

        // Insertar el nuevo cliente en la base de datos
        $sql = "INSERT INTO clientes (nombre, apellidos, correo, contrasena) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nombres, $apellidos, $correo, $contrasena_hash);

        if ($stmt->execute()) {
            $mensaje = "Registro exitoso. ¡Ya puedes iniciar sesión!";
        } else {
            $mensaje = "Error al registrar. Intenta de nuevo.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TITI SHOP | Registro</title>
  <link rel="stylesheet" href="/WebR/css/Home.css">
    <link rel="stylesheet" href="/WebR/css/base.css">
    <link rel="stylesheet" href="/WebR/css/components.css">
    <link rel="stylesheet" href="/WebR/css/login-register.css">
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
      <div class="user-menu a">
        <a href="Login.php"><img src="..//img/loginsinfondo.png" class="icono"></a>
        <a href="#"><img src="..//img/historial de compras.png" class="icono"></a>
        <a href="#"><img src="..//img/carrocomprassinfondo.png" class="icono"></a>
      </div>
    </div>
  </header>

  <!-- FORMULARIO -->
  <main>
    <div class="form-container">
      <h2>REGISTRO</h2>
      <form method="POST" action="">
        <label for="nombres">Nombres</label>
        <input type="text" id="nombres" name="nombres" required>

        <label for="apellidos">Apellidos</label>
        <input type="text" id="apellidos" name="apellidos" required>

        <label for="correo">Correo electrónico</label>
        <input type="email" id="correo" name="correo" required>

        <label for="contrasena">Contraseña</label>
        <input type="password" id="contrasena" name="contrasena" required>

        <button type="submit" class="submit-btn">CREAR CUENTA</button>

        <?php if ($mensaje): ?>
          <p style="margin-top:10px; font-size:14px; color:<?= str_starts_with($mensaje, 'El correo') ? 'red' : 'green'; ?>;">
            <?= $mensaje ?>
          </p>
        <?php endif; ?>
      </form>
    </div>
  </main>

  <!-- PIE DE PÁGINA -->
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
            <img src="..//img/fb_sinfondo.png" alt="Facebook">
          </a>
          <a href="TikTok.com">
            <img src="..//img/tiktok_sinfondo.png" alt="TikTok">
          </a>
          <a href="Instagram.com">
            <img src="..//img/logo_insta_sinfondo.png" alt="Instagram">
          </a>
        </div>
    </div>
    <div class="copyright">
        <p>&copy; 2025 Tu Empresa. Todos los derechos reservados.</p>
    </div>
</footer>
</body>
</html>