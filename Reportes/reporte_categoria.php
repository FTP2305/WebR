<?php
require('../fpdf/fpdf.php');
require('../Includes/conexion.php');

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, utf8_decode('Reporte de Ventas por Categoría'), 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }

    function BarraCategoria($datos, $anchoMaximo = 100, $altoBarra = 8)
    {
        if (empty($datos)) {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, 'No hay datos para mostrar', 1, 1, 'C');
            return;
        }

        $this->SetFont('Arial', '', 11);
        $totalMayor = max($datos);  // Para proporción

        foreach ($datos as $categoria => $monto) {
            // Cálculo de proporción
            $proporcion = ($totalMayor > 0) ? ($monto / $totalMayor) : 0;
            $barraAncho = $proporcion * $anchoMaximo;

            // Etiqueta de categoría (máximo 40mm de ancho)
            $this->Cell(50, $altoBarra, utf8_decode($categoria), 1);

            // Barra proporcional
            $this->SetFillColor(255, 204, 0);
            $this->Cell($barraAncho, $altoBarra, '', 1, 0, 'L', true);

            // Monto mostrado
            $this->Cell(30, $altoBarra, 'S/ ' . number_format($monto, 2), 1, 1);

            $this->Ln(1.5);
        }
    }
}

// Crear PDF y establecer márgenes
$pdf = new PDF();
$pdf->SetMargins(15, 20, 15); // márgenes izquierdo, arriba, derecho
$pdf->AddPage();

$conexion = new Conexion();
$conn = $conexion->getConectar();

// Consulta agrupada por categoría
$sql = "
    SELECT c.nombre_categoria AS categoria, SUM(vd.subtotal) AS total_ventas
    FROM ventas v
    JOIN detalle_venta vd ON v.id_venta = vd.id_venta
    JOIN productos p ON vd.id_producto = p.id_producto
    JOIN categorias c ON p.id_categoria = c.id_categoria
    GROUP BY c.nombre_categoria
";

$resultado = $conn->query($sql);

$datos_categoria = [];
if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $datos_categoria[$fila['categoria']] = $fila['total_ventas'];
    }
}

$pdf->BarraCategoria($datos_categoria);
$pdf->Output();
?>
