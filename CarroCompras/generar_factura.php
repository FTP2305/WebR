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

    private function enviarPorCorreo($correo, $nombre, $pdf_path) {
        $boundary = md5(time());
        $headers = "From: felixtintaya2305@titi-shop.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
        
        // Cuerpo del mensaje
        $message = "--$boundary\r\n";
        $message .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= "Hola $nombre,\r\n\r\n";
        $message .= "Gracias por tu compra en TITI SHOP. Adjuntamos tu factura.\r\n\r\n";
        $message .= "¡Esperamos verte pronto de nuevo!\r\n";
        
        // Adjuntar PDF
        $file_content = file_get_contents($pdf_path);
        $file_encoded = chunk_split(base64_encode($file_content));
        
        $message .= "--$boundary\r\n";
        $message .= "Content-Type: application/pdf; name=\"factura.pdf\"\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n";
        $message .= "Content-Disposition: attachment\r\n\r\n";
        $message .= "$file_encoded\r\n";
        $message .= "--$boundary--";
        
        // Enviar correo
        mail(
            $correo,
            "Factura de tu compra en TITI SHOP",
            $message,
            $headers
        );
    }
}
?>