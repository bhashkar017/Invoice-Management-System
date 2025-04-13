<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include our fixed FPDF class
require_once('includes/fpdf_fixed.php');

try {
    // Create PDF object using our fixed class
    $pdf = new FPDF_Fixed();
    echo "PDF object created<br>";
    
    // Add a page
    $pdf->AddPage();
    echo "Page added<br>";
    
    // Set font - use Courier which is a basic font
    $pdf->SetFont('Courier', '', 12);
    echo "Font set<br>";
    
    // Add some text
    $pdf->Cell(40, 10, 'Hello World!');
    echo "Text added<br>";
    
    // Output to browser
    $pdf->Output();
    echo "PDF generated successfully!<br>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString() . "<br>";
}
?> 