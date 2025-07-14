<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TITI SHOP | Preguntas</title>
  <link rel="stylesheet" href="/WebR/css/base.css">
    <link rel="stylesheet" href="/WebR/css/components.css">
    <link rel="stylesheet" href="/WebR/css/faq.css">
    <link rel="stylesheet" href="/WebR/css/responsive.css">
    <link rel="stylesheet" href="/WebR/css/Home.css">
</head>
<body>

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
      <?php if (isset($_SESSION['nombre'])): ?>
        <span style="color:black; font-weight: bold; font-size: 20px; margin-right:10px;">
          Hola! <?php echo htmlspecialchars($_SESSION['nombre']); ?>
        </span>
        <a href="logout.php" style="margin-left: 20px;">
          <img src="/WebR/img/cerrarsesion1-removebg-preview.png" alt="Cerrar sesión" class="icono" >
        </a>
      <?php else: ?>
        <a href="http://localhost/WebR/Clientes/Login.php">
          <img src="/WebR/img/loginsinfondo.png" alt="Usuario" class="icono">
        </a>
      <?php endif; ?>
      <a href="#">
        <img src="/WebR/img/historial de compras.png" alt="Historial" class="icono">
      </a>
      <a href="#">
        <img src="/WebR/img/carrocomprassinfondo.png" alt="Carro de Compras" class="icono">
      </a>
    </div>
  </div>
</header>

  <main class="main-faq">
    <div class="contenedor-central">
        <img src="/WebR/img/dron.jpg" class="img">
        <div class="contenedor-titular">
            <h1 class="texto-titu">
              ¿Tienes preguntas?
            </h1>
        </div>
        <p class="p">Aquí podrás encontrar las respuestas a las preguntas más comunes que <br>
           en Tittishop. Si aun no encuentras la respuesta que buscas, solo <a href="Contactanos.html">contáctanos</a>
          </p>
          <br>
        <hr>
        <div class="contenedor-horizontal">
 
           <table>
            <tr>
                <th class="color">PREGUNTAS SOBRE ENVIOS Y ENTREGAS</th>
            </tr>
            <tr>
                <th>1. ¿Hacen envíos a todo el Perú?<br>
                    Sí, realizamos envíos a nivel nacional.<br> 
                    Los tiempos de entrega varían según la zona.</th>
            </tr>
            <tr>
                <th>2. ¿Cuánto demora el envío?<br>
                    En Lima Metropolitana: de 1 a 3 días hábiles.<br> 
                    Provincias: entre 3 y 7 días hábiles.</th>
            </tr>
            <tr>
                <th>3. ¿Cómo puedo hacer seguimiento a mi pedido?<br>
                    Te enviaremos un correo con el número de seguimiento <br> 
                    una vez tu pedido sea despachado.</th>
            </tr>
           </table> 

           <table>
            <tr>
                <th class="color">CAMBIOS, DEVOLUCIONES Y GARANTIAS</th>
            </tr>
            <tr>
                <th>1. ¿Qué pasa si mi producto llega defectuoso o incorrecto?<br>
                    Puedes solicitar un cambio o devolución dentro de los 7 días <br> 
                    posteriores a la entrega. Contáctanos al WhatsApp <br>
                    o correo de atención al cliente.</th>
            </tr>
            <tr>
                <th>2. ¿Ofrecen garantía?<br>
                    Sí, todos nuestros productos tienen garantía de 3, 6 o 12 meses, <br> 
                    según el tipo de producto. Consulta los detalles en la <br>
                    descripción del artículo.</th>
            </tr>
           </table> 

           <table>
            <tr>
                <th class="color">CUENTA Y ACCESO </th>
            </tr>
            <tr>
                <th>1. ¿Cómo puedo crear una cuenta?<br>
                    Ve al botón "Iniciar sesión" luego en la parte superior derecha<br> 
                    te aparecerá un botón de "registrarme" seleccionas, llenas tu <br>
                    información personal, creas una contraseña y listo</th>
            </tr>
            <tr>
                <th>2. ¿Mi información personal está segura?<br>
                    Sí, usamos encriptación y políticas de<br> 
                    privacidad para proteger tus datos.</th>
            </tr>
           </table> 
        </div>
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
</body>
</html>
