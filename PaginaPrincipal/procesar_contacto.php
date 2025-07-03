<?php
session_start();
include '../Includes/conexion.php';

$mensaje = "";

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $dni = $_POST['dni'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $asunto = $_POST['asunto'];
    $mensaje_contacto = $_POST['mensaje'];

    $conexion = new Conexion();
    $conn = $conexion->getConectar();

    // Consulta para insertar los datos del formulario en la tabla contactos
    $sql = "INSERT INTO contactos (nombre, apellidos, dni, telefono, correo, asunto, mensaje) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $nombre, $apellidos, $dni, $telefono, $correo, $asunto, $mensaje_contacto);

    if ($stmt->execute()) {
        $mensaje = "Tu mensaje ha sido enviado correctamente. ¡Gracias por contactarnos!";
    } else {
        $mensaje = "Hubo un error al enviar tu mensaje. Por favor, inténtalo nuevamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TITI SHOP | Contáctanos</title>
    <link rel="stylesheet" href="/WebR/css/base.css">
    <link rel="stylesheet" href="/WebR/css/components.css">
    <link rel="stylesheet" href="/WebR/css/contactanos.css">
    <link rel="stylesheet" href="/WebR/css/responsive.css">
    <link rel="stylesheet" href="/WebR/css/Home.css">
</head>
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
                    <span> Hola! <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                    <a href="http://localhost/WebR/Clientes/Cliente.php"> 
                        <img src="/WebR/img/loginsinfondo.png" alt="Ver Perfil" class="icono">
                    </a>
                    <a href="/WebR/PaginaPrincipal/logout.php"> 
                        <img src="/WebR/img/cerrarsesion1-removebg-preview.png" alt="Cerrar sesión" class="icono">
                    </a>
                <?php else: ?>
                    <a href="http://localhost/WebR/Clientes/Login.php">
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
        <section class="contactanos-section"> 
            <div class="contenedor-central1">
                <div class="contenedor-izquierda">
                    <h3 class="texto-titu">
                        Nos contactamos contigo
                    </h3>
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
                <li><a href="/WebR/PaginaPrincipal/Nosotros.php">¿Quiénes somos?</a></li> 
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