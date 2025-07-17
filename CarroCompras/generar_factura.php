<?php
require_once('../fpdf/fpdf.php');

class GeneradorFacturas {
    public function generarYEnviarFactura($id_venta, $conn) {
        // Obtener datos del cliente y venta
        $datos = $this->obtenerDatosVenta($id_venta, $conn);

        // 1. Generar PDF
        $pdf_path = $this->generarPDF($id_venta, $datos, $conn);

        // 2. Enviar por correo
        $this->enviarPorCorreo($datos['correo'], $datos['nombre'], $pdf_path);

        return $pdf_path;
    }

    private function obtenerDatosVenta($id_venta, $conn) {
        $sql = "SELECT c.nombre, c.apellidos, c.correo, v.total 
                FROM ventas v 
                JOIN clientes c ON v.id_cliente = c.id_cliente 
                WHERE v.id_venta = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_venta);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    private function generarPDF($id_venta, $datos, $conn) {
        $pdf = new FPDF();
        $pdf->AddPage();

        // Cabecera
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'FACTURA #' . $id_venta, 0, 1, 'C');

        // Datos cliente
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Cliente: ' . $datos['nombre'] . ' ' . $datos['apellidos'], 0, 1);
        $pdf->Cell(0, 10, 'Total: S/ ' . number_format($datos['total'], 2), 0, 1);

        // Guardar PDF
        if (!file_exists('../facturas')) {
            mkdir('../facturas', 0777, true);
        }
        $pdf_path = "../facturas/factura_$id_venta.pdf";
        $pdf->Output('F', $pdf_path);

        return $pdf_path;
    }
    private function enviarPorCorreo($correo, $nombre, $pdf_path): bool {
    // Cargar PHPMailer correctamente
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Configuración SMTP para Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'felixtintaya2305@gmail.com';
        $mail->Password = 'rxbu zvaa rmku qxfk';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Configuración del correo
        $mail->setFrom('felixtintaya2305@gmail.com', 'TITI SHOP');
        $mail->addAddress($correo, $nombre);
        $mail->isHTML(true);
        $mail->Subject = 'Factura de tu compra en TITI SHOP';
        $mail->Body = 'Hola '.$nombre.',<br>Gracias por tu compra. Adjunto encontrarás tu factura.';

        // Adjunta el PDF
        $mail->addAttachment($pdf_path, 'factura.pdf');

        // Envía el correo y retorna true si fue exitoso
        return $mail->send();
         } catch (Exception $e) {
        error_log("Error al enviar correo: " . $e->getMessage());
        return false;
    }
}
}
?>