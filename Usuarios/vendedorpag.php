<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'vendedor') {
    header('Location: ../intranet.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Panel de Vendedor - Titishop</title>
    <link rel="stylesheet" href="/WebR/css/estilo.css" />
    <link rel="stylesheet" href="/WebR/css/vendedor.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>
<body>
    <header class="navbar">
        <div class="navbar-container">  <a href="#" class="logo">
            TiTiShop - Vista de Vendedor
            </a>
            <nav>
            <ul class="nav-links">
            <li><a href="../logout.php">Cerrar Sesión <i class="fas fa-sign-out-alt"></i></a></li>
            </ul>
            </nav>
        </div>
    </header>

    <main class="dashboard-content">
        <section id="welcome-overview-section" class="dashboard-section">
            <div class="welcome-section">
                <h1>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>!</h1>
                <p>Este es el panel de ventas de Titishop. Aquí puedes gestionar todos las ventas del negocio.</p>
            </div>

            <div class="action-buttons">
                <a href="registroventas.php" class="action-button">
                    <div class="icon-circle"><i class="fas fa-box"></i></div>
                    Registro de ventas
                </a>
                <a href="consultar_stock.php" class="action-button" >
                    <div class="icon-circle"><i class="fas fa-users"></i></div>
                    Consultar Stock
                </a>
                <a href="historial.php" class="action-button" onclick="showSection('register-user-section'); return false;">
                    <div class="icon-circle"><i class="fas fa-user-plus"></i></div>
                    Historial de ventas
                </a>
            </div>
        </section>
    </main>
</body>
</html>