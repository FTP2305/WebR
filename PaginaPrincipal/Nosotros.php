

<?php
session_start(); // Verificar sesión del usuario
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TITI SHOP | Nosotros</title>
    <link rel="stylesheet" href="/WebR/css/base.css">
    <link rel="stylesheet" href="/WebR/css/components.css">
    <link rel="stylesheet" href="/WebR/css/nosotros.css"> <link rel="stylesheet" href="responsive.css"> </head>
<body>

    <header>
        <div class="navbar">
            <img src="/WebR/img/LOGOTITI.jpeg" alt="Logo TITI SHOP" class="logo">
            <h3><a href="/WebR/PaginaPrincipal/Home.php">Inicio</a></h3>
            <h3><a href="/WebR/PaginaPrincipal/Productos.php">Productos</a></h3>
            <h3><a href="/WebR/PaginaPrincipal/Contactanos.php">Contáctanos</a></h3>
            <h3><a href="/WebR/PaginaPrincipal/Nosotros.php">Nosotros</a></h3>
            <h3><a href="/WebR/PaginaPrincipal/Preguntas.php">Preguntas Frecuentes</a></h3>
            <h3><a href="/WebR/PaginaPrincipal/intranet.php">Intranet</a></h3>
            
            <div class="user-menu">
                <?php if (isset($_SESSION['id_cliente'])): ?>
                    <span>
                        Hola! <?php echo htmlspecialchars($_SESSION['nombre']); ?>
                    </span>
                    <a href="http://localhost/AlterWeb/Roles/Cliente.php">
                        <img src="/WebR/img/loginsinfondo.png" alt="Ver Perfil" class="icono">
                    </a>
                    <a href="logout.php">
                        <img src="/WebR/img/cerrarsesion1-removebg-preview.png" alt="Cerrar sesión" class="icono">
                    </a>
                <?php else: ?>
                    <a href="http://localhost/AlterWeb/Roles/Login.php">
                        <img src="/WebR/img/loginsinfondo.png" alt="Login" class="icono">
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

    <main>
        <section class="nosotros">
            <div class="container">

                <h1>Conoce más sobre TiTi Shop</h1>

                <div class="nosotros-section">
                    <div class="nosotros-content">
                        <div class="texto">
                            <h2>¿Quiénes somos?</h2>
                            <p>Somos TITI SHOP, una tienda online comprometida con brindar productos tecnológicos y de tendencia a precios accesibles. Nos apasiona ofrecer una experiencia de compra rápida, confiable y amigable.</p>
                        </div>
                        <div class="imagen">
                            <img src="/WebR/img/quienes-removebg-preview.png" alt="Quiénes somos">
                        </div>
                    </div>
                </div>

                <div class="nosotros-section">
                    <div class="nosotros-content">
                        <div class="texto">
                            <h2>Nuestra Misión</h2>
                            <p>Brindar productos innovadores y de calidad que mejoren el estilo de vida de nuestros clientes, con un servicio que supere sus expectativas.</p>
                        </div>
                        <div class="imagen">
                            <img src="/WebR/img/png-transparent-mision-logo-removebg-preview.png" alt="Misión">
                        </div>
                    </div>
                </div>

                <div class="nosotros-section">
                    <div class="nosotros-content">
                        <div class="texto">
                            <h2>Nuestra Visión</h2>
                            <p>Ser reconocidos como una de las tiendas online líderes en Perú, destacando por nuestro compromiso con la satisfacción del cliente y la constante evolución en el mercado digital.</p>
                        </div>
                        <div class="imagen">
                            <img src="/WebR/img/image-removebg-preview.png" alt="Visión">
                        </div>
                    </div>
                </div>

            </div>
        </section>
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
                <li><a href="Nosotros.php">¿Quiénes somos?</a></li>
                <li><a href="Nosotros.php#mision">Misión</a></li>
                <li><a href="Nosotros.php#vision">Visión</a></li>
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
                <a href="https://www.facebook.com/TuEmpresa">
                    <img src="/WebR/img/fb_sinfondo.png" alt="Facebook">
                </a>
                <a href="https://www.tiktok.com/@TuEmpresa">
                    <img src="/WebR/img/tiktok_sinfondo.png" alt="TikTok">
                </a>
                <a href="https://www.instagram.com/TuEmpresa">
                    <img src="/WebR/img/logo_insta_sinfondo.png" alt="Instagram">
                </a>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2025 TITI SHOP. RUC: 12345678901 | Razón Social: TITI SHOP IMPORTACIONES E.I.R.L.</p>
        </div>
    </footer>

</body>
</html>
