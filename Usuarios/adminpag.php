<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') { 
    header('Location: ../intranet.php'); 
    exit();
}

include '../Includes/conexion.php'; 

$conexion = new Conexion();
$conn = $conexion->getConectar();

$mensaje_registro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type']) && $_POST['form_type'] === 'register_user') {
    $nombre = trim($_POST["nombre"]);
    $correo = trim($_POST["correo"]);
    $contrasena_plana = trim($_POST["contrasena"]);
    $rol_nombre_seleccionado = trim($_POST["rol"]); 

    if (empty($nombre) || empty($correo) || empty($contrasena_plana) || empty($rol_nombre_seleccionado)) {
        $mensaje_registro = "Todos los campos son obligatorios para el registro.";
    } elseif ($rol_nombre_seleccionado !== "admin" && $rol_nombre_seleccionado !== "vendedor") {
        $mensaje_registro = "Rol no permitido. Solo 'admin' o 'vendedor' son válidos para el registro.";
    } else {
        $contrasena_hasheada = password_hash($contrasena_plana, PASSWORD_DEFAULT);
        $id_rol = null;
        $sql_get_id_rol = "SELECT id_rol FROM roles WHERE nombre_rol = ?";
        $stmt_get_id_rol = $conn->prepare($sql_get_id_rol);
        if ($stmt_get_id_rol) {
            $stmt_get_id_rol->bind_param("s", $rol_nombre_seleccionado);
            $stmt_get_id_rol->execute();
            $result_id_rol = $stmt_get_id_rol->get_result();
            if ($result_id_rol->num_rows > 0) {
                $fila_rol = $result_id_rol->fetch_assoc();
                $id_rol = $fila_rol['id_rol'];
            }
            $stmt_get_id_rol->close();
        } else {
            $mensaje_registro = "Error al preparar la consulta para obtener ID de rol: " . $conn->error;
        }

        if ($id_rol === null) {
            $mensaje_registro = "Error: El rol seleccionado no existe en la base de datos.";
        } else {
            $check_sql = "SELECT id_usuario FROM usuarios WHERE correo = ?";
            $stmt_check = $conn->prepare($check_sql);
            if ($stmt_check) {
                $stmt_check->bind_param("s", $correo);
                $stmt_check->execute();
                $existe = $stmt_check->get_result();

                if ($existe->num_rows > 0) {
                    $mensaje_registro = "El correo '" . htmlspecialchars($correo) . "' ya está registrado.";
                } else {
                    $insert_sql = "INSERT INTO usuarios (nombre_usuario, correo, contrasena, id_rol) VALUES (?, ?, ?, ?)";
                    $stmt_insert = $conn->prepare($insert_sql);
                    if ($stmt_insert) {
                        $stmt_insert->bind_param("sssi", $nombre, $correo, $contrasena_hasheada, $id_rol);
                        if ($stmt_insert->execute()) {
                            $mensaje_registro = "Usuario '" . htmlspecialchars($nombre) . "' registrado exitosamente.";
                            $_POST = array(); 
                        } else {
                            $mensaje_registro = "Error al registrar el usuario: " . $stmt_insert->error;
                        }
                        $stmt_insert->close();
                    } else {
                        $mensaje_registro = "Error al preparar la consulta de inserción: " . $conn->error;
                    }
                }
                $stmt_check->close();
            } else {
                $mensaje_registro = "Error al preparar la consulta de verificación de correo: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administrador - Titishop</title>
    <link rel="stylesheet" href="/WebR/css/estilo.css"> 
    <link rel="stylesheet" href="/WebR/css/admin.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <header class="navbar">
        <div class="navbar-container">
            <a href="#" class="logo" onclick="showSection('welcome-overview-section'); return false;">
                TiTiShop - Vista de Administrador
            </a>
            <nav>
                <ul class="nav-links">
                    <li><a href="/WebR/PaginaPrincipal/Home.php">Inicio (Tienda)</a></li>
                    <li><a href="../Productos/listar.php">Productos</a></li>
                    <li><a href="/WebR/Usuarios/historial.php">Ventas</a></li>
                    <li><a href="/WebR/Usuarios/intranet.php?logout=true">Cerrar Sesión <i class="fas fa-sign-out-alt"></i></a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="dashboard-content">
        <section id="welcome-overview-section" class="dashboard-section">
            <div class="welcome-section">
                <h1>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>!</h1>
                <p>Este es el panel de administración de Titishop. Aquí puedes gestionar todos los aspectos del negocio.</p>
            </div>

            <div class="action-buttons">
                <a href="/WebR/Productos/crudProductos.php" class="action-button">
                    <div class="icon-circle"><i class="fas fa-box"></i></div>
                    Gestión de Productos
                </a>
                <a href="../Clientes/crudClientes.php" class="action-button">
                    <div class="icon-circle"><i class="fas fa-users"></i></div>
                    Gestión de Clientes
                </a>
                <a href="#" class="action-button" onclick="showSection('register-user-section'); return false;">
                    <div class="icon-circle"><i class="fas fa-user-plus"></i></div>
                    Registrar Nuevo Usuario
                </a>
                <a href="/WebR/Usuarios/historial.php" class="action-button">
                    <div class="icon-circle"><i class="fas fa-dollar-sign"></i></div>
                    Gestión de Ventas
                </a>
                <a href="../Usuarios/crudUsuarios.php" class="action-button">
                    <div class="icon-circle"><i class="fas fa-user"></i></div>
                    Gestión de Usuarios
                </a>
                <!-- NUEVO BOTÓN: REPORTES -->
                <a href="/WebR/Reportes/reportes.php" class="action-button">
                    <div class="icon-circle"><i class="fas fa-chart-bar"></i></div>
                    Reportes
                </a>
            </div>
        </section>

        <section id="register-user-section" class="dashboard-section" style="display:none;">
            <h2><i class="fas fa-user-plus"></i> Registrar Nuevo Usuario</h2>
            <form class="auth-form" method="POST" action="adminpag.php"> 
                <input type="hidden" name="form_type" value="register_user">

                <?php if (!empty($mensaje_registro)): ?>
                    <p class="<?php echo (strpos($mensaje_registro, 'exitosamente') !== false) ? 'success-message' : 'error-message'; ?>">
                        <?php echo htmlspecialchars($mensaje_registro); ?>
                    </p>
                <?php endif; ?>

                <div class="input-group">
                    <label for="reg_nombre">Nombre completo</label>
                    <div class="input-wrapper">
                        <input type="text" id="reg_nombre" name="nombre" placeholder="Nombre del nuevo usuario" required 
                            value="<?php echo isset($_POST['nombre']) && strpos($mensaje_registro, 'exitosamente') === false ? htmlspecialchars($_POST['nombre']) : ''; ?>">
                        <span class="icon">&#128100;</span>
                    </div>
                </div>

                <div class="input-group">
                    <label for="reg_correo">Correo electrónico</label>
                    <div class="input-wrapper">
                        <input type="email" id="reg_correo" name="correo" placeholder="Correo del nuevo usuario" required
                            value="<?php echo isset($_POST['correo']) && strpos($mensaje_registro, 'exitosamente') === false ? htmlspecialchars($_POST['correo']) : ''; ?>">
                        <span class="icon">&#9993;</span>
                    </div>
                </div>

                <div class="input-group">
                    <label for="reg_contrasena">Contraseña</label>
                    <div class="input-wrapper">
                        <input type="password" id="reg_contrasena" name="contrasena" placeholder="Contraseña para el nuevo usuario" required>
                        <span class="icon">&#128065;</span>
                    </div>
                </div>

                <div class="input-group">
                    <label for="reg_rol">Rol</label>
                    <div class="input-wrapper">
                        <select id="reg_rol" name="rol" required>
                            <option value="">Selecciona un rol</option>
                            <option value="vendedor" <?php echo (isset($_POST['rol']) && $_POST['rol'] === 'vendedor' && strpos($mensaje_registro, 'exitosamente') === false) ? 'selected' : ''; ?>>Vendedor</option>
                            <option value="admin" <?php echo (isset($_POST['rol']) && $_POST['rol'] === 'admin' && strpos($mensaje_registro, 'exitosamente') === false) ? 'selected' : ''; ?>>Administrador</option>
                        </select>
                        <span class="icon">&#9881;</span>
                    </div>
                </div>

                <button type="submit" class="btn-auth register">Registrar Usuario</button>
            </form>
        </section>

        <section id="user-management-section" class="dashboard-section" style="display:none;">
            <h2><i class="fas fa-users-cog"></i> Gestión de Usuarios Existentes</h2>
            <p>Aquí podrás listar, editar roles o eliminar usuarios ya registrados.</p>
            <a href="../Usuarios/listar.php" class="btn-auth">Listar Usuarios</a> 
            <button class="btn-auth register" onclick="showSection('register-user-section'); return false;">Ir a Registrar Nuevo Usuario</button>
        </section>

        <section id="sales-management-section" class="dashboard-section" style="display:none;">
            <h2><i class="fas fa-dollar-sign"></i> Gestión de Ventas</h2>
            <p>Aquí podrás ver el historial de ventas, generar reportes, etc.</p>
            <a href="historial.php" class="btn-auth">Ver Historial de Ventas</a> 
            <a href="registroventas.php" class="btn-auth register">Registrar Nueva Venta</a>
        </section>
    </main>

    <script src="admin.js"></script> 
    <script>
        function showSection(sectionId) {
            document.querySelectorAll('.dashboard-section').forEach(section => {
                section.style.display = 'none';
            });
            document.getElementById(sectionId).style.display = 'block';
        }

        document.addEventListener('DOMContentLoaded', function() {
            <?php if (!empty($mensaje_registro) || (isset($_POST['form_type']) && $_POST['form_type'] === 'register_user')): ?>
                showSection('register-user-section');
            <?php else: ?>
                showSection('welcome-overview-section');
            <?php endif; ?>
        });
    </script>
</body>
</html>
