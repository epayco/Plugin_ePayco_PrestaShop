<?php

if (ob_get_length()) {
    ob_end_clean();
}

if (ini_get('zlib.output_compression')) {
    ini_set('zlib.output_compression', 'Off');
}

require_once EP_ROOT_URL . '/vendor/autoload.php';

use TCPDF;

if (isset($_GET['refPayco'])) {
    $refPayco = htmlspecialchars($_GET['refPayco']);
    $data = [
        'Estado' => htmlspecialchars($_GET['estado'] ?? ''),
        'Referencia' => $refPayco,
        'Fecha' => htmlspecialchars($_GET['fecha'] ?? ''),
        'Franquicia' => htmlspecialchars($_GET['franquicia'] ?? ''),
        'Autorización' => htmlspecialchars($_GET['autorizacion'] ?? ''),
        'Valor' => '$' . htmlspecialchars($_GET['valor'] ?? ''),
        'Descuento' => '$' . htmlspecialchars($_GET['descuento'] ?? ''),
        'Descripción' => htmlspecialchars($_GET['descripcion'] ?? ''),
        'IP' => htmlspecialchars($_GET['ip'] ?? ''),
        'Respuesta' => htmlspecialchars($_GET['respuesta'] ?? ''),
    ];

    $colores = [
        'aceptada' => [103, 201, 64],
        'rechazada' => [225, 37, 27],
        'pendiente' => [255, 209, 0],
    ];
    $color = $colores[strtolower($data['Estado'])] ?? [0, 0, 0];
    $titulo = 'Transacción ' . ucfirst(strtolower($data['Estado']));

    try {
       
        $pdf = new TCPDF();
        $pdf->setPrintHeader(false); 
        $pdf->setPrintFooter(false); 
        $pdf->AddPage();

    
       
        $pdf->Image('https://multimedia.epayco.co/plugins-sdks/logo-negro-epayco.png', 80, 15, 50, '', '', '', 'T');
        $pdf->Ln(20);
    
       
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetTextColor($color[0], $color[1], $color[2]);
        $pdf->Cell(0, 10, $titulo, 0, 1, 'C');
    
       
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Referencia ePayco: ' . $refPayco, 0, 1, 'C');
        $pdf->Cell(0, 10, 'Fecha: ' . $data['Fecha'], 0, 1, 'C');
        $pdf->Ln(10);
    
        $pdf->SetFillColor(249, 249, 249);
        $pdf->SetDrawColor(229, 229, 229);
    
        foreach ($data as $key => $value) {
            $pdf->Cell(50, 10, $key, 1, 0, 'L', true);
            $pdf->Cell(0, 10, $value, 1, 1, 'L', false);
        }
    
        
        if (ob_get_length()) {
            ob_end_clean();
        }
    
       
        if (headers_sent()) {
            throw new Exception('Error: Los encabezados ya fueron enviados.');
        }
    
         
        $pdf->Output('Factura-' . $refPayco . '.pdf', 'D');
        exit;
    
    } catch (Exception $e) {
        error_log($e->getMessage());
        exit('Error al generar el PDF. Consulte el log del servidor.');
    }
    
} else {
    echo 'Referencia de pago no proporcionada.';
}