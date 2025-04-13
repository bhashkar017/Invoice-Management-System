<?php
// Ensure no output is sent before PDF generation
ob_start();

require_once('vendor/autoload.php');
require_once('tcpdf_invoice.php');

// Test invoice data
$invoice_number = 'TEST001';
$invoice_data = array(
    'invoice_type' => 'Invoice',
    'invoice_date' => date('Y-m-d'),
    'invoice_due_date' => date('Y-m-d', strtotime('+30 days')),
    'invoice_subtotal' => 100.00,
    'invoice_discount' => 10.00,
    'invoice_shipping' => 5.00,
    'invoice_vat' => 19.00,
    'invoice_total' => 114.00,
    'status' => 'Paid',
    'invoice_notes' => 'Test invoice notes'
);

$customer_data = array(
    'name' => 'Test Customer',
    'address_1' => '123 Test Street',
    'address_2' => 'Test City',
    'town' => 'Test Town',
    'county' => 'Test County',
    'postcode' => '12345',
    'phone' => '123-456-7890',
    'name_ship' => 'Test Customer',
    'address_1_ship' => '123 Test Street',
    'address_2_ship' => 'Test City',
    'town_ship' => 'Test Town',
    'county_ship' => 'Test County',
    'postcode_ship' => '12345'
);

// Create test invoice
$invoice = new TCPDFInvoice("A4", CURRENCY, "en");

// Set logo only if GD or Imagick is available
if (extension_loaded('gd') || extension_loaded('imagick')) {
    try {
        $invoice->setLogo(COMPANY_LOGO, COMPANY_LOGO_WIDTH, COMPANY_LOGO_HEIGHT);
    } catch (Exception $e) {
        // If logo setting fails, continue without logo
        error_log("Logo setting failed: " . $e->getMessage());
    }
}

// Set invoice details
$invoice->setType($invoice_data['invoice_type']);
$invoice->setReference($invoice_number);
$invoice->setDate($invoice_data['invoice_date']);
$invoice->setDue($invoice_data['invoice_due_date']);

// Set company information
$invoice->setFrom(array(
    COMPANY_NAME,
    COMPANY_ADDRESS_1,
    COMPANY_ADDRESS_2,
    COMPANY_COUNTY,
    COMPANY_POSTCODE,
    COMPANY_NUMBER,
    COMPANY_VAT
));

// Set customer information
$invoice->setTo(array(
    $customer_data['name'],
    $customer_data['address_1'],
    $customer_data['address_2'],
    $customer_data['town'],
    $customer_data['county'],
    $customer_data['postcode'],
    "Phone: " . $customer_data['phone']
));

// Set shipping information
$invoice->shipTo(array(
    $customer_data['name_ship'],
    $customer_data['address_1_ship'],
    $customer_data['address_2_ship'],
    $customer_data['town_ship'],
    $customer_data['county_ship'],
    $customer_data['postcode_ship'],
    ''
));

// Add test items
$invoice->addItem('Test Product 1', '', 2, 3.80, 10.00, 0, 20.00);
$invoice->addItem('Test Product 2', '', 1, 1.90, 20.00, 0, 20.00);
$invoice->addItem('Test Product 3', '', 3, 5.70, 30.00, 0, 90.00);

// Add totals
$invoice->addTotal("Total", $invoice_data['invoice_subtotal']);
$invoice->addTotal("Discount", $invoice_data['invoice_discount']);
$invoice->addTotal("Delivery", $invoice_data['invoice_shipping']);
$invoice->addTotal("TAX/VAT " . VAT_RATE . "%", $invoice_data['invoice_vat']);
$invoice->addTotal("Total Due", $invoice_data['invoice_total'], true);

// Add badge
$invoice->addBadge($invoice_data['status']);

// Add notes
$invoice->addTitle("Customer Notes");
$invoice->addParagraph($invoice_data['invoice_notes']);

// Add payment information
$invoice->addTitle("Payment information");
$invoice->addParagraph(PAYMENT_DETAILS);

// Set footer note
$invoice->setFooternote(FOOTER_NOTE);

// Clear any previous output
ob_end_clean();

// Generate the PDF
$invoice->generate();
$invoice->Output('test_invoice.pdf', 'I');
?> 