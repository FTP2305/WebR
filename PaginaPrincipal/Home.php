<?php
session_start();  // Verificar sesión del usuario
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiii Shop - Tienda de Electrónica</title>
    <link rel="stylesheet" href="/WebR/css/Home.css">
    <link rel="stylesheet" href="/WebR/css/components.css">
    <link rel="stylesheet" href="/WebR/css/base.css">
    <link rel="stylesheet" href="/WebR/css/chatbot.css">
    
</head>
<body>
  <!-- CHATBOT -->
  <div id="chatbot-container">
    <div id="chatbot-header">
        <h2>Chat de Ayuda</h2>
        <button id="close-chatbot" aria-label="Cerrar chat">X</button>
    </div>
    <div id="chatbot-messages">
        <div class="message bot-message">
            <p>¡Hola! Soy Titobot, ¿en qué puedo ayudarte hoy?</p>
        </div>
    </div>
    <div id="chatbot-input-container">
        <input type="text" id="chatbot-input" placeholder="Escribe tu pregunta...">
        <button id="chatbot-send">Enviar</button>
    </div>
  </div>
  <button id="open-chatbot-button" aria-label="Abrir chat de ayuda">
    <img src="/WebR/img/chat_icon.svg" alt="Abrir chat">
  </button>
  <!-- FIN CHATBOT -->

  <!-- ENCABEZADO -->
  <header>
    <div class="navbar">
      <img src="/WebR/img/LOGOTITI.jpeg" alt="Logo TITI SHOP" class="logo">
      <h3><a href="/WebR/PaginaPrincipal/Home.php" style="color: black;">Inicio</a></h3>
      <h3><a href="/WebR/PaginaPrincipal/Productos.php" style="color: black;">Productos</a></h3>
      <h3><a href="/WebR/PaginaPrincipal/Contactanos.php" style="color: black;">Contáctanos</a></h3>
      <h3><a href="/WebR/PaginaPrincipal/Nosotros.php" style="color: black;">Nosotros</a></h3>
      <h3><a href="/WebR/PaginaPrincipal/Preguntas.php" style="color: black;">Preguntas Frecuentes</a></h3>
      <h3><a href="/WebR/PaginaPrincipal/intranet.php" style="color: black;">Intranet</a></h3>
      
      <div class="user-menu">
        <?php if (isset($_SESSION['id_cliente'])): ?>
          <span style="color:black; font-weight: bold; font-size: 20px; margin-right:10px;">
            Hola! <?php echo htmlspecialchars($_SESSION['nombre']); ?>
          </span>
          <a href="http://localhost/AlterWeb/Roles/Cliente.php" style="margin-left: 20px;">
            <img src="/WebR/img/loginsinfondo.png" alt="Ver Perfil" class="icono">
          </a>
          <a href="logout.php" style="margin-left: 20px;">
            <img src="/WebR/img/cerrarsesion1-removebg-preview.png" alt="Cerrar sesión" class="icono">
          </a>
        <?php else: ?>
          <a href="http://localhost/AlterWeb/Roles/Login.php">
            <img src="/WebR/img/loginsinfondo.png" alt="Login" class="icono">
          </a>
        <?php endif; ?>
        <a href="historial_compras.php">
          <img src="/WebR/img/historial de compras.png" alt="Historial" class="icono">
        </a>
        <a href="carrito.php">
          <img src="/WebR/img/carrocomprassinfondo.png" alt="Carro de Compras" class="icono">
        </a>
      </div>
    </div>
  </header>

  <!-- BANNER PRINCIPAL -->
  <section class="banner">
    <div class="banner-img">
      <img src="/WebR/img/BANER_HOME.jpg" alt="Banner TiTiShop" class="tmñbanner">
    </div>
    <div class="banner-texto">
      <h1>TiTiShop: Lo mejor en tecnología, con delivery rápido a todo Lima y provincias. ¡Tu próximo gadget está a un clic!</h1>
      <p>TiTiShop: Tecnología que te conecta, precios que te sorprenden.</p>
      <a href="Productos.php">
        <button>Catálogo</button>
      </a>
    </div>
  </section>

  <!-- SECCIÓN DE MARCAS -->
  <section class="marcas">
    <h2>Marcas</h2>
    <div class="marcas-grid">
      <div class="marca">BRANDAME
        <img src="/WebR/img/brandnamemarca1.jpg" alt="Marca 1" class="tmñmarca">
      </div>
      <div class="marca">DRONEX
        <img src="/WebR/img/marca2.jpg" alt="Marca 2" class="tmñmarca">
      </div>
      <div class="marca">LIDIMI
        <img src="/WebR/img/marca3.jpg" alt="Marca 3" class="tmñmarca">
      </div>
      <div class="marca">TV BOX
        <img src="/WebR/img/marca4.webp" alt="Marca 4" class="tmñmarca">
      </div>
      <div class="marca">SONY
        <img src="/WebR/img/marca5.jpg" alt="Marca 5" class="tmñmarca">
      </div>
    </div>
  </section>

  <!-- SECCIÓN DE PRODUCTOS DESTACADOS -->
  <section class="productos">
    <div class="destacado-grande">
      <img src="/WebR/img/panel.png" alt="Panel de ofertas" class="tmñproductodestacado">
      <div class="texto-oferta">
        <h1>Las mejores ofertas en TitiShop</h1>
        <p>Encuentra descuentos increíbles en tus productos favoritos. ¡Sólo por tiempo limitado!</p>
      </div>
    </div>

    <div class="productos-destacados">
      <div>
        <img src="/WebR/img/DRONE S159.webp" alt="Drone S159" class="tmñproductoGrande">
        <div class="texto-productodestacado">
          <p>El S159 cuenta con cámara HD, control de altitud y función de retorno automático, ideal para capturar imágenes aéreas con estabilidad y facilidad de uso.</p>
        </div>
      </div>
      <div>
        <img src="/WebR/img/DRONE K13 MAX.jpg" alt="Drone K13 Max" class="tmñproductoGrande">
        <div class="texto-productodestacado">
          <p>El K13 MAX es un dron compacto y fácil de usar, ideal para divertirse. Ofrece vuelo estable, cámara HD y funciones como volteretas y control desde el celular. Perfecto para principiantes.</p>
        </div>
      </div>
    </div>

    <div class="productos-grid">
      <div>DRONE FP 2736
        <img src="/WebR/img/DRON FP 2736.jpg" alt="DRONE FP 2736" class="tmñproducto">
      </div>
      <div>DRONE M6
        <img src="/WebR/img/DRONE M6.jpg" alt="DRONE M6" class="tmñproducto">
      </div>
      <div>K11 PRO MAX
        <img src="/WebR/img/K11 PRO MAX.webp" alt="K11 PRO MAX" class="tmñproducto">
      </div>
      <div>DRONE E99 PRO
        <img src="/WebR/img/DRONE E99 PRO.jpg" alt="DRONE E99 PRO" class="tmñproducto">
      </div>
      <div>DRONE K12 MAX
        <img src="/WebR/img/DRONE K12 MAX.jpg" alt="DRONE K12 MAX" class="tmñproducto">
      </div>
    </div>
  </section>

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
            <img src="/WebR/img/fb_sinfondo.png" alt="Facebook">
          </a>
          <a href="TikTok.com">
            <img src="/WebR/img/tiktok_sinfondo.png" alt="TikTok">
          </a>
          <a href="Instagram.com">
            <img src="/WebR/img/logo_insta_sinfondo.png" alt="Instagram">
          </a>
        </div>
    </div>
    <div class="copyright">
        <p>&copy; 2025 Tu Empresa. Todos los derechos reservados.</p>
    </div>
</footer>
<script src="/WebR/Chatbot/chatbot.js"></script> 
</body>
</html>