<?php
include '../Includes/conexion.php'; 

$conexion = new Conexion();
$conn = $conexion->getConectar();


$id_venta_recibido = $_GET['id_venta'] ?? null; 
$estado_pago_pasarela = $_GET['status'] ?? null; 

if ($id_venta_recibido && $estado_pago_pasarela === 'completed') { 
    try {
        $sql_update = "UPDATE ventas SET estado = 'completado' WHERE id_venta = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("i", $id_venta_recibido);
        $stmt->execute();
        $stmt->close();
        
    } catch (Exception $e) {
        error_log("Error al actualizar el estado de la venta: " . $e->getMessage());
        
    }
}
$conn->close();
?>