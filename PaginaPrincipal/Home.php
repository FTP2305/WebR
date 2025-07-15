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
  <link rel="stylesheet" href="/WebR/css/estilo.css">    
  <link rel="stylesheet" href="/WebR/css/chatbot.css">
  <link rel="stylesheet" href="/WebR/css/productos.css">
  <link rel="stylesheet" href="/WebR/css/contactanos.css">
  <link rel="stylesheet" href="/WebR/css/nosotros.css"> 
  <link rel="stylesheet" href="/WebR/css/faq.css">
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
  <?php include("../Includes/header.php"); ?>

  <!-- CONTENIDO PRINCIPAL -->
  <main id="contenido-dinamico">

  <?php include("../Ajax/HomeAjax.php"); ?>


  </main>

  <!-- PIE DE PÁGINA -->
  <?php include("../Includes/footer.php"); ?>

  <script src="/WebR/Chatbot/chatbot.js"></script> 
  <script src="/WebR/js/ajax-navegacion.js"></script>
</body>
</html>

