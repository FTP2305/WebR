

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
<!-- ENCABEZADO -->
  <?php include("../Includes/header.php"); ?>

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

  <!-- PIE DE PÁGINA -->
  <?php include("../Includes/footer.php"); ?>

</body>
</html>
