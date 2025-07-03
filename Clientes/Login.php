<?php
session_start(); // Iniciar la sesión


$mensaje = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['email'];
    $clave = $_POST['password'];

   
    include '../Includes/conexion.php';
    $conexion = new Conexion();
    $conn = $conexion->getConectar();

    
    $sql = "SELECT * FROM clientes WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo); 
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $cliente = $resultado->fetch_assoc();
        if (password_verify($clave, $cliente['contrasena'])) {
            
            $_SESSION['id_cliente'] = $cliente['id_cliente'];
            $_SESSION['nombre'] = $cliente['nombre'];

            header("Location: /WebR/PaginaPrincipal/Home.php");
            exit();
        } else {
            $mensaje = "Contraseña incorrecta.";
        }
    } else {
        $mensaje = "Correo electrónico no registrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TITI SHOP | Iniciar Sesión</title>
    <link rel="stylesheet" href="/WebR/css/Home.css">
    <link rel="stylesheet" href="/WebR/css/base.css">
    <link rel="stylesheet" href="/WebR/css/components.css">
    <link rel="stylesheet" href="/WebR/css/login-register.css">
</head>
<body>
  <header>
    <div class="navbar">
      <img src="..//img/LOGOTITI.jpeg" alt="Logo TITI SHOP" class="logo">
      <h3><a href="..//Home.php" style="color: black;">Inicio</a></h3>
      <h3><a href="..//Productos.php" style="color: black;">Productos</a></h3>
      <h3><a href="..//Contactanos.php" style="color: black;">Contáctanos</a></h3>
      <h3><a href="..//Nosotros.php" style="color: black;">Nosotros</a></h3>
      <h3><a href="..//Preguntas.php" style="color: black;">Preguntas Frecuentes</a></h3>
      <div class="user-menu">
        <a href="Login.php"><img src="..//img/loginsinfondo.png" alt="Usuario" class="icono"></a>
        <a href="#"><img src="..//img/historial de compras.png" alt="Historial" class="icono"></a>
        <a href="#"><img src="..//img/carrocomprassinfondo.png" alt="Carro de Compras" class="icono"></a>
      </div>
    </div>
  </header>

  <main>
    <div class="container_login">
      <div class="logo-central">
        <img src="/WebR/img/LOGOTITI.jpeg" alt="Logo TITI SHOP">
      </div>
      <div class="login-box">
        <h2>INICIO DE SESIÓN</h2>
        <form method="POST" action="Login.php">
          <label for="email">Correo electrónico</label>
          <input type="email" id="email" name="email" required>

          <label for="password">Contraseña</label>
          <input type="password" id="password" name="password" required>

          <a href="#" class="forgot-link">¿Olvidaste tu contraseña?</a>

          <button type="submit" class="btn ingresar">INGRESAR</button>
          <a href="Registrarse.php" class="btn registrarse">REGISTRARSE</a>

          <?php if ($mensaje): ?>
            <p style="color: red; font-size: 14px;"><?php echo $mensaje; ?></p>
          <?php endif; ?>
        </form>
      </div>
    </div>
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