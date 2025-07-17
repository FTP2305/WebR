<?php
session_start();
include('../Includes/conexion.php');

$conexion = new Conexion();
$conn = $conexion->getConectar();

$mensaje_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST["correo"]);
    $contrasena_ingresada = trim($_POST["contrasena"]);

    if (empty($correo) || empty($contrasena_ingresada)) {
        $mensaje_error = "Por favor, ingresa tu correo y contraseña.";
    } else {
        $sql = "SELECT u.id_usuario, u.nombre_usuario, u.contrasena, r.nombre_rol AS rol 
                FROM usuarios u
                JOIN roles r ON u.id_rol = r.id_rol
                WHERE u.correo = ?";

        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $correo);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows == 1) {
                $usuario = $resultado->fetch_assoc();

                if (password_verify($contrasena_ingresada, $usuario["contrasena"])) {
                    $_SESSION["usuario_id"] = $usuario["id_usuario"];
                    $_SESSION["usuario_nombre"] = $usuario["nombre_usuario"];
                    $_SESSION["rol"] = $usuario["rol"];

                    if ($usuario["rol"] == "admin") {
                        echo "<script>location.href='/WebR/Usuarios/adminpag.php';</script>";
                        exit();
                    } elseif ($usuario["rol"] == "vendedor") {
                        echo "<script>location.href='/WebR/Usuarios/vendedorpag.php';</script>";
                        exit();
                    } else {
                        $mensaje_error = "Rol de usuario no reconocido.";
                    }
                } else {
                    $mensaje_error = "Correo o contraseña incorrectos.";
                }
            } else {
                $mensaje_error = "Correo o contraseña incorrectos.";
            }
            $stmt->close();
        } else {
            $mensaje_error = "Error al preparar la consulta: " . $conn->error;
        }
    }
}
?>

<!-- SOLO EL CONTENIDO AJAX SIN HEADER NI FOOTER -->
<link rel="stylesheet" href="/WebR/css/styleintra.css"/>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />

<!-- ESTE DIV ES EL CLAVE -->
<div id="contenido-intranet">
    <div class="container">
        <div class="illustration-section">
            <img src="/WebR/img/Banner_intranet.png" alt="Ilustración de Bienvenida"/>
        </div>
        <div class="auth-section">
            <div class="auth-header">
                <img src="/WebR/img/LOGOTITI.jpeg" alt="Logo Titishop" class="titishop-logo" />
                <h2>La intranet de Titishop</h2>
                <p>Tu experiencia de gestión de ventas y productos</p>
            </div>
            <form class="auth-form" method="POST" action="IntranetAjax.php">
                <p>Ingresa tus datos para <span class="highlight">iniciar sesión</span>.</p>

                <?php if (!empty($mensaje_error)): ?>
                    <p class="error-message"><?php echo htmlspecialchars($mensaje_error); ?></p>
                <?php endif; ?>

                <div class="input-group">
                    <label for="correo">Correo electrónico</label>
                    <div class="input-wrapper">
                        <input type="email" id="correo" name="correo" placeholder="Ingresa tu correo" required />
                    </div>
                </div>

                <div class="input-group">
                    <label for="contrasena">Contraseña</label>
                    <div class="input-wrapper">
                        <input type="password" id="contrasena" name="contrasena" placeholder="Ingresa tu contraseña" required />
                    </div>
                </div>

                <button type="submit" class="btn-auth">Iniciar Sesión</button>

                <div class="centered">
                    <h3><a href="Home.php" style="color: black;" onclick="location.href='Home.php';">Volver a Inicio</a></h3>
                </div>
            </form>
        </div>
    </div>
</div>
