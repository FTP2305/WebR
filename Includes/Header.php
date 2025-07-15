<header>
  <div class="navbar">
    <img src="/WebR/img/LOGOTITI.jpeg" alt="Logo TITI SHOP" class="logo">
    <h3><a href="/WebR/PaginaPrincipal/Home.php" class="nav-link" data-url="/WebR/Ajax/HomeAjax.php">Inicio</a></h3>
    <h3><a href="/WebR/PaginaPrincipal/Productos.php" class="nav-link" data-url="/WebR/Ajax/ProductosAjax.php">Productos</a></h3>
    <h3><a href="/WebR/PaginaPrincipal/Contactanos.php" class="nav-link" data-url="/WebR/Ajax/ContactanosAjax.php">Contáctanos</a></h3>
    <h3><a href="/WebR/PaginaPrincipal/Nosotros.php" class="nav-link" data-url="/WebR/Ajax/NosotrosAjax.php">Nosotros</a></h3>
    <h3><a href="/WebR/PaginaPrincipal/Preguntas.php" class="nav-link" data-url="/WebR/Ajax/PreguntasAjax.php">Preguntas Frecuentes</a></h3>
    <h3><a href="/WebR/PaginaPrincipal/Intranet.php" class="nav-link" data-url="/WebR/Ajax/IntranetAjax.php">Intranet</a></h3>

    <div class="user-menu">
      <?php if (isset($_SESSION['id_cliente'])): ?>
        <span style="color:black; font-weight: bold; font-size: 20px; margin-right:10px;">
          Hola! <?php echo htmlspecialchars($_SESSION['nombre']); ?>
        </span>
        <a href="/WebR/Clientes/Cliente.php" style="margin-left: 20px;">
          <img src="/WebR/img/loginsinfondo.png" alt="Ver Perfil" class="icono">
        </a>
        <a href="/WebR/Clientes/logout.php" style="margin-left: 20px;">
          <img src="/WebR/img/cerrarsesion1-removebg-preview.png" alt="Cerrar sesión" class="icono">
        </a>
      <?php else: ?>
        <a href="/WebR/Clientes/Login.php">
          <img src="/WebR/img/loginsinfondo.png" alt="Login" class="icono">
        </a>
      <?php endif; ?>
      <a href="/WebR/CarroCompras/historial_compras.php">
        <img src="/WebR/img/historial de compras.png" alt="Historial" class="icono">
      </a>
      <a href="/WebR/CarroCompras/carrito.php">
        <img src="/WebR/img/carrocomprassinfondo.png" alt="Carro de Compras" class="icono">
      </a>
    </div>
  </div>
</header>
