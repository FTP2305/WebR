<?php
session_start();
?>

<section class="contactanos-section">
  <div class="contenedor-central1">

    <div class="contenedor-izquierda">
      <h3 class="texto-titu">Nos contactamos contigo</h3>
      <img src="/WebR/img/comput.jpg" class="imgpequeña" alt="Imagen de Computadora">
    </div>

    <div class="contenedor-izquierda2">
      <h2>Envíanos un Mensaje</h2>
      <form action="procesar_contacto.php" method="POST">
        <label for="asunto">Asunto:</label>
        <div class="enfila1">
          <input type="text" id="asunto" name="asunto" required>
          <img src="/WebR/img/operador.jpg" alt="Icono Operador">
        </div>

        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" class="campo-largo1" required>

        <label for="apellidos">Apellidos:</label>
        <input type="text" id="apellidos" name="apellidos" class="campo-largo1" required>

        <div class="fila-campos-doble">
          <div class="campo-grupo">
            <label for="dni">DNI:</label>
            <input type="text" id="dni" name="dni" required>
          </div>
          <div class="campo-grupo">
            <label for="telefono">Teléfono:</label>
            <input type="tel" id="telefono" name="telefono" required>
          </div>
          <div class="campo-grupo">
            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo" required>
          </div>
        </div>

        <label for="mensaje">Mensaje:</label>
        <textarea id="mensaje" name="mensaje" class="campo-largo3" required></textarea>

        <div class="centro-boton">
          <button type="submit" class="miboton">Enviar Mensaje</button>
        </div>
      </form>
    </div>

    <img src="/WebR/img/contactanosofsinfondo.png" class="imagen-superpuesta1" alt="Contacto Superpuesto">

  </div>
</section>
