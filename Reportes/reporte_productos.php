<?php
require('../fpdf/fpdf.php');
require('../Includes/conexion.php');

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, utf8_decode('Productos Más Vendidos'), 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }

    function BarraProductos($datos, $anchoMaximo = 100, $altoBarra = 8)
    {
        if (empty($datos)) {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, 'No hay datos para mostrar', 1, 1, 'C');
            return;
        }

        $this->SetFont('Arial', '', 10);
        $totalMayor = max(array_column($datos, 'ventas')); // Para proporción

        // Encabezados
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(70, $altoBarra, 'Producto', 1);
        $this->Cell(20, $altoBarra, 'Unidades', 1);
        $this->Cell(40, $altoBarra, 'Total Ventas', 1);
        $this->Ln();

        $this->SetFont('Arial', '', 10);
        
        foreach ($datos as $producto) {
            // Cálculo de proporción
            $proporcion = ($totalMayor > 0) ? ($producto['ventas'] / $totalMayor) : 0;
            $barraAncho = $proporcion * $anchoMaximo;

            // Nombre del producto (recortado si es muy largo)
            $nombre = strlen($producto['nombre']) > 40 ? substr($producto['nombre'], 0, 37).'...' : $producto['nombre'];
            $this->Cell(70, $altoBarra, utf8_decode($nombre), 1);

            // Barra proporcional para unidades vendidas
            $this->SetFillColor(100, 180, 240); // Azul
            $this->Cell($barraAncho, $altoBarra, '', 1, 0, 'L', true);
            
            // Cantidad de unidades vendidas
            $this->Cell(20, $altoBarra, $producto['ventas'], 1, 0, 'R');
            
            // Total de ventas
            $this->Cell(40, $altoBarra, 'S/ ' . number_format($producto['total_ventas'], 2), 1, 1, 'R');

            $this->Ln(1);
        }
    }
}

// Crear PDF y establecer márgenes
$pdf = new PDF();
$pdf->SetMargins(15, 20, 15);
$pdf->AddPage();

$conexion = new Conexion();
$conn = $conexion->getConectar();

// Consulta para productos más vendidos (top 10)
$sql = "
    SELECT 
        p.nombre AS nombre,
        SUM(dv.cantidad) AS ventas,
        SUM(dv.subtotal) AS total_ventas
    FROM detalle_venta dv
    JOIN productos p ON dv.id_producto = p.id_producto
    JOIN ventas v ON dv.id_venta = v.id_venta
    WHERE v.estado = 'completado'
    GROUP BY p.id_producto
    ORDER BY ventas DESC
    LIMIT 10
";

$resultado = $conn->query($sql);

$datos_productos = [];
if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $datos_productos[] = [
            'nombre' => $fila['nombre'],
            'ventas' => $fila['ventas'],
            'total_ventas' => $fila['total_ventas']
        ];
    }
}

$pdf->BarraProductos($datos_productos);
$pdf->Output();
?>