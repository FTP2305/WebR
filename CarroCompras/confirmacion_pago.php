<?php
session_start();
include '../Includes/conexion.php';

$conexion = new Conexion();
$conn = $conexion->getConectar();

// 1. Procesar actualización de estado de pago
$id_venta_recibido = $_GET['id_venta'] ?? null; 
$estado_pago_pasarela = $_GET['status'] ?? null; 

if ($id_venta_recibido && $estado_pago_pasarela === 'completed') { 
    try {
        // Actualizar estado en la base de datos
        $sql_update = "UPDATE ventas SET estado = 'completado' WHERE id_venta = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("i", $id_venta_recibido);
        $stmt->execute();
        $stmt->close();
        
        // Guardar en sesión para mostrar en la vista
        $_SESSION['compra_finalizada'] = true;
        $_SESSION['id_ultima_venta'] = $id_venta_recibido;
        
    } catch (Exception $e) {
        error_log("Error al actualizar el estado de la venta: " . $e->getMessage());
        $_SESSION['error_pago'] = "Ocurrió un error al procesar tu pago";
    }
}

$conn->close();

// 2. Mostrar confirmación HTML
if (isset($_SESSION['compra_finalizada']) && $_SESSION['compra_finalizada'] === true) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Compra Exitosa - TITI SHOP</title>
        <style>
            body {
                font-family: 'Arial', sans-serif;
                background-color: #f5f5f5;
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
            }
            .confirmation-container {
                background-color: white;
                border-radius: 10px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                padding: 40px;
                text-align: center;
                max-width: 600px;
                width: 90%;
            }
            h1 {
                color: #28a745;
                margin-bottom: 20px;
            }
            .message {
                margin-bottom: 30px;
                font-size: 18px;
                color: #333;
            }
            .btn {
                display: inline-block;
                padding: 12px 25px;
                margin: 10px;
                border-radius: 5px;
                text-decoration: none;
                font-weight: bold;
                transition: all 0.3s ease;
            }
            .btn-download {
                background-color: #007bff;
                color: white;
            }
            .btn-download:hover {
                background-color: #0056b3;
                transform: translateY(-2px);
            }
            .btn-home {
                background-color: #6c757d;
                color: white;
            }
            .btn-home:hover {
                background-color: #5a6268;
                transform: translateY(-2px);
            }
            .success-icon {
                font-size: 60px;
                color: #28a745;
                margin-bottom: 20px;
            }
        </style>
    </head>
    <body>
        <div class="confirmation-container">
            <div class="success-icon">✓</div>
            <h1>¡Compra realizada con éxito!</h1>
            
            <div class="message">
                <?php if ($_SESSION['correo_enviado']): ?>
                    <p>Hemos enviado la factura a tu correo electrónico registrado.</p>
                    <p>También puedes descargarla ahora:</p>
                <?php else: ?>
                    <p>No pudimos enviar el correo automáticamente, pero puedes descargar tu factura:</p>
                <?php endif; ?>
            </div>
            
            <div class="buttons-container">
                <a href="../facturas/factura_<?= $_SESSION['id_ultima_venta'] ?>.pdf" download class="btn btn-download">
                    Descargar Factura
                </a>
                <a href="/WebR/PaginaPrincipal/Home.php" class="btn btn-home">
                    Volver al Inicio
                </a>
            </div>
            
            <p style="margin-top: 30px; color: #6c757d;">
                Número de pedido: #<?= $_SESSION['id_ultima_venta'] ?>
            </p>
        </div>
    </body>
    </html>
    <?php
    unset($_SESSION['compra_finalizada']);
    unset($_SESSION['id_ultima_venta']);
    unset($_SESSION['correo_enviado']);
} else {
    header('Location: carrito.php');
    exit();
}
?>