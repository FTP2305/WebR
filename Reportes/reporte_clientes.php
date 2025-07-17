<?php
require('../fpdf/fpdf.php');
require('../Includes/conexion.php');

class PDF extends FPDF
{
    private $colors = [
        [79, 129, 189],   // Azul
        [155, 187, 89],    // Verde
        [247, 150, 70],    // Naranja
        [128, 100, 162],   // Morado
        [75, 172, 198],    // Celeste
        [247, 83, 79],     // Rojo
        [128, 128, 128]    // Gris
    ];

    function Header()
    {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, utf8_decode('Distribución de Clientes por Departamento'), 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }

    // Gráfico de barras verticales
    function VerticalBarChart($x, $y, $width, $height, $data, $title)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->SetXY($x, $y - 20); 
        $this->Cell($width, 10, utf8_decode($title), 0, 1, 'C'); 

        $maxValue = max(array_column($data, 'count'));
        $barWidth = $width / (count($data) * 1.5);
        $gap = $barWidth / 2;

        $this->Line($x, $y, $x, $y - $height);

        $this->Line($x, $y, $x + $width, $y);

        foreach ($data as $i => $item) {
            $barHeight = ($item['count'] / $maxValue) * $height;
            $barX = $x + ($i * ($barWidth + $gap)) + $gap;

            $colorIndex = $i % count($this->colors);
            $this->SetFillColor($this->colors[$colorIndex][0], $this->colors[$colorIndex][1], $this->colors[$colorIndex][2]);

            $this->Rect($barX, $y - $barHeight, $barWidth, $barHeight, 'F');

            $this->SetFont('Arial', '', 8);
            $departamento = strlen($item['departamento']) > 10 ? substr($item['departamento'], 0, 7).'...' : $item['departamento'];
            $this->RotatedText($barX + ($barWidth / 2), $y + 8, utf8_decode($departamento), 45); 

            $this->SetXY($barX, $y - $barHeight - 8);
            $this->Cell($barWidth, 5, $item['count'], 0, 0, 'C');
        }

        $this->SetFont('Arial', '', 8);
        for ($i = 0; $i <= 5; $i++) {
            $value = round($maxValue * ($i / 5));
            $yPos = $y - ($height * ($i / 5));
            $this->Line($x - 2, $yPos, $x, $yPos);
            $this->SetXY($x - 15, $yPos - 2);
            $this->Cell(10, 5, $value, 0, 0, 'R');
        }
    }

    function RotatedText($x, $y, $txt, $angle)
    {
        $this->_out('q');

        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F cm',
            1, 0, 0, 1, $x * $this->k, ($this->h - $y) * $this->k));
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F cm',
            cos(deg2rad($angle)), sin(deg2rad($angle)),
            -sin(deg2rad($angle)), cos(deg2rad($angle)), 0, 0));

        $this->_out(sprintf('BT %.2F %.2F Td (%s) Tj ET',
            0, 0, $this->_escape($txt)));

        $this->_out('Q');
    }
}

$pdf = new PDF('L'); 
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();

$conexion = new Conexion();
$conn = $conexion->getConectar();

$sql = "
    SELECT
        IFNULL(departamento, 'Sin especificar') AS departamento,
        COUNT(*) AS count
    FROM clientes
    GROUP BY departamento
    ORDER BY count DESC
";

$resultado = $conn->query($sql);

$datos_departamentos = [];
if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $datos_departamentos[] = [
            'departamento' => $fila['departamento'],
            'count' => $fila['count']
        ];
    }
}

$pageWidth = $pdf->GetPageWidth();
$chartWidth = 100;
$tableWidth = 200; 

$chartX = ($pageWidth - $chartWidth) / 2;
$chartY = 130; 
$pdf->VerticalBarChart($chartX, $chartY, $chartWidth, 80, $datos_departamentos, 'Clientes por Departamento (Gráfico de Barras)');

$tableX = ($pageWidth - $tableWidth) / 2;
$tableY = $chartY + 70; 

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetXY($tableX, $tableY);
$pdf->Cell($tableWidth, 10, 'Detalle de Clientes por Departamento', 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(220, 220, 220);
$pdf->SetX($tableX); 
$pdf->Cell(120, 8, 'Departamento', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Cantidad', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Porcentaje', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 9);
$total = array_sum(array_column($datos_departamentos, 'count'));
foreach ($datos_departamentos as $item) {
    $percentage = number_format(($item['count'] / $total) * 100, 2);
    $pdf->SetX($tableX); 
    $pdf->Cell(120, 8, utf8_decode($item['departamento']), 1);
    $pdf->Cell(40, 8, $item['count'], 1, 0, 'C');
    $pdf->Cell(40, 8, $percentage . '%', 1, 1, 'C');
}

$pdf->Output('reporte_clientes_departamentos.pdf', 'I');
?>