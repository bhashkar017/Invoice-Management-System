<?php
// Ensure no output is sent before PDF generation
ob_start();

// Include necessary files
require_once('vendor/autoload.php');
require_once('includes/config.php');

// Create new PDF document
class TCPDFInvoice extends \TCPDF {
    protected $font = 'helvetica';
    protected $columnOpacity = 0.06;
    protected $columnSpacing = 0.3;
    protected $referenceformat = array('.',',');
    protected $margins = array('l'=>20,'t'=>20,'r'=>20);
    
    protected $document;
    protected $type;
    protected $reference;
    protected $logo;
    protected $color;
    protected $date;
    protected $due;
    protected $from;
    protected $to;
    protected $ship;
    protected $items = array();
    protected $totals = array();
    protected $badge;
    protected $addText = array();
    protected $footernote;
    protected $dimensions;
    protected $currency;
    protected $language;
    
    public function __construct($size='A4', $currency='$', $language='en') {
        parent::__construct('P', 'mm', $size, true, 'UTF-8', false, false);
        
        $this->currency = $currency;
        $this->language = $language;
        
        // Set document information
        $this->SetCreator('Invoice Mg System');
        $this->SetAuthor(COMPANY_NAME);
        $this->SetTitle('Invoice');
        
        // Set default header data
        $this->SetHeaderData('', 0, COMPANY_NAME, COMPANY_ADDRESS_1 . "\n" . COMPANY_ADDRESS_2);
        
        // Set header and footer fonts
        $this->setHeaderFont(Array('helvetica', '', 10));
        $this->setFooterFont(Array('helvetica', '', 8));
        
        // Set default monospaced font
        $this->SetDefaultMonospacedFont('courier');
        
        // Set margins
        $this->SetMargins($this->margins['l'], $this->margins['t'], $this->margins['r']);
        $this->SetHeaderMargin(5);
        $this->SetFooterMargin(10);
        
        // Set auto page breaks
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        // Set image scale factor
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        // Set font
        $this->SetFont($this->font, '', 10);
    }
    
    public function setType($title) {
        $this->type = $title;
    }
    
    public function setReference($reference) {
        $this->reference = $reference;
    }
    
    public function setDate($date) {
        $this->date = $date;
    }
    
    public function setDue($date) {
        $this->due = $date;
    }
    
    public function setLogo($logo, $width=0, $height=0) {
        $this->logo = $logo;
        if ($width && $height) {
            $this->dimensions = array($width, $height);
        }
    }
    
    public function setFrom($data) {
        $this->from = array_filter($data);
    }
    
    public function setTo($data) {
        $this->to = $data;
    }
    
    public function shipTo($data) {
        $this->ship = $data;
    }
    
    public function addItem($item, $description, $quantity, $vat, $price, $discount, $total) {
        $this->items[] = array(
            'item' => $item,
            'description' => $description,
            'quantity' => $quantity,
            'vat' => $vat,
            'price' => $price,
            'discount' => $discount,
            'total' => $total
        );
    }
    
    public function addTotal($name, $value, $colored=false) {
        $this->totals[] = array(
            'name' => $name,
            'value' => $value,
            'colored' => $colored
        );
    }
    
    public function addBadge($badge) {
        $this->badge = $badge;
    }
    
    public function addTitle($title) {
        $this->addText[] = array('title', $title);
    }
    
    public function addParagraph($paragraph) {
        $this->addText[] = array('paragraph', $paragraph);
    }
    
    public function setFooternote($note) {
        $this->footernote = $note;
    }
    
    public function Header() {
        // Logo
        if ($this->logo) {
            $this->Image($this->logo, $this->margins['l'], 10, $this->dimensions[0], $this->dimensions[1]);
        }
        
        // Title
        $this->SetFont($this->font, 'B', 20);
        $this->Cell(0, 10, $this->type, 0, 1, 'R');
        
        // Invoice details
        $this->SetFont($this->font, '', 10);
        $this->Cell(0, 5, 'Invoice #: ' . $this->reference, 0, 1, 'R');
        $this->Cell(0, 5, 'Date: ' . $this->date, 0, 1, 'R');
        if ($this->due) {
            $this->Cell(0, 5, 'Due Date: ' . $this->due, 0, 1, 'R');
        }
        
        // From address
        $this->Ln(10);
        $this->SetFont($this->font, 'B', 10);
        $this->Cell(0, 5, 'From:', 0, 1);
        $this->SetFont($this->font, '', 10);
        foreach ($this->from as $line) {
            $this->Cell(0, 5, $line, 0, 1);
        }
        
        // To address
        $this->Ln(5);
        $this->SetFont($this->font, 'B', 10);
        $this->Cell(0, 5, 'To:', 0, 1);
        $this->SetFont($this->font, '', 10);
        foreach ($this->to as $line) {
            $this->Cell(0, 5, $line, 0, 1);
        }
        
        // Shipping address
        if ($this->ship) {
            $this->Ln(5);
            $this->SetFont($this->font, 'B', 10);
            $this->Cell(0, 5, 'Ship To:', 0, 1);
            $this->SetFont($this->font, '', 10);
            foreach ($this->ship as $line) {
                $this->Cell(0, 5, $line, 0, 1);
            }
        }
    }
    
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont($this->font, '', 8);
        $this->Cell(0, 10, $this->footernote, 0, 0, 'L');
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'R');
    }
    
    public function generate() {
        $this->AddPage();
        
        // Items table
        $this->SetFont($this->font, 'B', 10);
        $this->Cell(60, 7, 'Item', 1, 0, 'L');
        $this->Cell(20, 7, 'Qty', 1, 0, 'C');
        $this->Cell(30, 7, 'Price', 1, 0, 'R');
        $this->Cell(30, 7, 'VAT', 1, 0, 'R');
        $this->Cell(30, 7, 'Total', 1, 1, 'R');
        
        $this->SetFont($this->font, '', 10);
        foreach ($this->items as $item) {
            $this->Cell(60, 7, $item['item'], 1, 0, 'L');
            $this->Cell(20, 7, $item['quantity'], 1, 0, 'C');
            $this->Cell(30, 7, $this->currency . number_format($item['price'], 2), 1, 0, 'R');
            $this->Cell(30, 7, $this->currency . number_format($item['vat'], 2), 1, 0, 'R');
            $this->Cell(30, 7, $this->currency . number_format($item['total'], 2), 1, 1, 'R');
        }
        
        // Totals
        $this->Ln(10);
        foreach ($this->totals as $total) {
            $this->SetFont($this->font, 'B', 10);
            $this->Cell(140, 7, $total['name'], 0, 0, 'R');
            $this->Cell(30, 7, $this->currency . number_format($total['value'], 2), 0, 1, 'R');
        }
        
        // Additional text
        if (!empty($this->addText)) {
            $this->Ln(10);
            foreach ($this->addText as $text) {
                if ($text[0] == 'title') {
                    $this->SetFont($this->font, 'B', 12);
                    $this->Cell(0, 7, $text[1], 0, 1);
                } else {
                    $this->SetFont($this->font, '', 10);
                    $this->MultiCell(0, 5, $text[1]);
                }
                $this->Ln(5);
            }
        }
        
        // Badge
        if ($this->badge) {
            $this->SetFont($this->font, 'B', 20);
            $this->SetTextColor(255, 0, 0);
            $this->RotatedText(150, 50, $this->badge, 45);
            $this->SetTextColor(0, 0, 0);
        }
    }
    
    protected function RotatedText($x, $y, $txt, $angle) {
        $this->StartTransform();
        $this->Rotate($angle, $x, $y);
        $this->Text($x, $y, $txt);
        $this->StopTransform();
    }
}

// Function to generate invoice PDF using TCPDF
function generateInvoicePDF($invoice_number) {
    // Get invoice details from database
    $mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
    
    // Get invoice information
    $query = "SELECT * FROM invoices WHERE invoice = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $invoice_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $invoice_data = $result->fetch_assoc();
    
    if (!$invoice_data) {
        throw new Exception("Invoice not found");
    }
    
    // Get customer information
    $query = "SELECT * FROM customers WHERE invoice = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $invoice_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer_data = $result->fetch_assoc();
    
    if (!$customer_data) {
        throw new Exception("Customer not found");
    }
    
    // Get invoice items
    $query = "SELECT * FROM invoice_items WHERE invoice = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $invoice_number);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Create a new instance of the invoice class
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
    
    // Add items
    while($item = $result->fetch_assoc()) {
        $item_vat = 0;
        if(ENABLE_VAT == true) {
            $item_vat = (VAT_RATE / 100) * $item['subtotal'];
        }
        $invoice->addItem($item['product'], '', $item['qty'], $item_vat, $item['price'], $item['discount'], $item['subtotal']);
    }
    
    // Add totals
    $invoice->addTotal("Total", $invoice_data['invoice_subtotal'] ?? 0);
    if(!empty($invoice_data['invoice_discount'])) {
        $invoice->addTotal("Discount", $invoice_data['invoice_discount']);
    }
    if(!empty($invoice_data['invoice_shipping'])) {
        $invoice->addTotal("Delivery", $invoice_data['invoice_shipping']);
    }
    if(ENABLE_VAT == true) {
        $invoice->addTotal("TAX/VAT " . VAT_RATE . "%", $invoice_data['invoice_vat'] ?? 0);
    }
    $invoice->addTotal("Total Due", $invoice_data['invoice_total'] ?? 0, true);
    
    // Add badge
    $invoice->addBadge($invoice_data['status']);
    
    // Add notes if any
    if(!empty($invoice_data['invoice_notes'])) {
        $invoice->addTitle("Customer Notes");
        $invoice->addParagraph($invoice_data['invoice_notes']);
    }
    
    // Add payment information
    $invoice->addTitle("Payment information");
    $invoice->addParagraph(PAYMENT_DETAILS);
    
    // Set footer note
    $invoice->setFooternote(FOOTER_NOTE);
    
    // Create the invoices directory if it doesn't exist
    $invoices_dir = __DIR__ . '/invoices';
    if (!file_exists($invoices_dir)) {
        if (!mkdir($invoices_dir, 0777, true)) {
            throw new Exception("Failed to create invoices directory");
        }
    }
    
    // Generate the PDF using absolute path
    $pdf_path = $invoices_dir . '/' . $invoice_number . '.pdf';
    $invoice->generate();
    $invoice->Output($pdf_path, 'F');
    
    // Return the relative path for download
    return 'invoices/' . $invoice_number . '.pdf';
}
?> 