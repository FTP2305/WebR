<?php
include '../Includes/conexion.php';

$conexion = new Conexion();
$conn = $conexion->getConectar();

// Configurar cabeceras para JSON
header('Content-Type: application/json');

// Operación: Crear Producto
if (isset($_POST['crear'])) {
    $response = ['success' => false, 'message' => ''];
    
    try {
        $stmt = $conn->prepare("INSERT INTO productos (nombre, precio, stock, id_categoria) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdii", $_POST['nombre'], $_POST['precio'], $_POST['stock'], $_POST['id_categoria']);
        
        if ($stmt->execute()) {
            $response = [
                'success' => true,
                'message' => 'Producto creado',
                'id' => $stmt->insert_id
            ];
        }
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
    
    echo json_encode($response);
    exit();
}

// Operación: Eliminar Producto
if (isset($_GET['eliminar'])) {
    $response = ['success' => false, 'message' => ''];
    
    try {
        $stmt = $conn->prepare("DELETE FROM productos WHERE id_producto = ?");
        $stmt->bind_param("i", $_GET['eliminar']);
        
        $response['success'] = $stmt->execute();
        $response['message'] = $response['success'] ? 'Producto eliminado' : 'Error al eliminar';
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
    
    echo json_encode($response);
    exit();
}

// Operación: Obtener Producto para Editar
if (isset($_GET['obtener'])) {
    try {
        $stmt = $conn->prepare("SELECT * FROM productos WHERE id_producto = ?");
        $stmt->bind_param("i", $_GET['obtener']);
        $stmt->execute();
        
        $result = $stmt->get_result();
        echo json_encode($result->fetch_assoc());
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit();
}

// Operación: Actualizar Producto
if (isset($_POST['actualizar'])) {
    $response = ['success' => false, 'message' => ''];
    
    try {
        $stmt = $conn->prepare("UPDATE productos SET nombre=?, precio=?, stock=?, id_categoria=? WHERE id_producto=?");
        $stmt->bind_param("sdiii", $_POST['nombre'], $_POST['precio'], $_POST['stock'], $_POST['id_categoria'], $_POST['id_producto']);
        
        $response['success'] = $stmt->execute();
        $response['message'] = $response['success'] ? 'Producto actualizado' : 'Error al actualizar';
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
    
    echo json_encode($response);
    exit();
}

// Si no es ninguna operación AJAX
echo json_encode(['error' => 'Acción no válida']);
?>