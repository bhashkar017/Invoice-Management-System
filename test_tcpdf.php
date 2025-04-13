<?php
// Ensure no output is sent before PDF generation
ob_start();

require_once('vendor/autoload.php');

// Create new PDF document
$pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Invoice Mg System');
$pdf->SetAuthor('Invoice Mg System');
$pdf->SetTitle('TCPDF Test');

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set default monospaced font
$pdf->SetDefaultMonospacedFont('courier');

// Set margins
$pdf->SetMargins(15, 15, 15);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 15);

// Set image scale factor
$pdf->setImageScale(1.25);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', 'B', 20);

// Add some text
$pdf->Cell(0, 10, 'TCPDF Test Page', 0, 1, 'C');

$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'If you can see this, TCPDF is working correctly!', 0, 1, 'C');

// Clear any previous output
ob_end_clean();

// Output the PDF
$pdf->Output('test.pdf', 'I');
?> 