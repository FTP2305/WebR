<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ../intranet.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes - TitiShop</title>
    <link rel="stylesheet" href="/WebR/css/estilo.css">
    <link rel="stylesheet" href="/WebR/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <header class="navbar">
        <div class="navbar-container">
            <a href="/WebR/Usuarios/adminpag.php" class="logo">Volver al Panel</a>
            <nav>
                <ul class="nav-links">
                    <li><a href="/WebR/PaginaPrincipal/Home.php">Inicio Tienda</a></li>
                    <li><a href="../intranet.php?logout=true">Cerrar Sesión <i class="fas fa-sign-out-alt"></i></a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="dashboard-content">
        <section class="dashboard-section">
            <h1><i class="fas fa-chart-bar"></i> Reportes Generales</h1>
            <p>Selecciona un tipo de reporte que deseas visualizar:</p>

            <div class="action-buttons">
                <a href="reporte.php" class="action-button">
                    <div class="icon-circle"><i class="fas fa-user-tie"></i></div>
                    Ventas por Trabajador
                </a>

                <a href="reporte_categoria.php" class="action-button">
                    <div class="icon-circle"><i class="fas fa-tags"></i></div>
                    Ventas por Categoría
                </a>

                <a href="reporte_marca.php" class="action-button">
                    <div class="icon-circle"><i class="fas fa-industry"></i></div>
                    Ventas por Marca
                </a>
            </div>
        </section>
    </main>

</body>
</html>
