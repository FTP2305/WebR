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
  <?php include("../Includes/header.php"); ?>

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
  <?php include("../Includes/footer.php"); ?>
</body>
</html>
