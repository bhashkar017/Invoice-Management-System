<?php
// Start output buffering
ob_start();

include('header.php');
include('functions.php');

if (isset($_GET['download']) && !empty($_GET['download'])) {
    $invoice_number = $_GET['download'];
    $pdf_path = 'invoices/' . $invoice_number . '.pdf';
    
    try {
        // Clear any previous output
        ob_end_clean();
        
        if (!file_exists($pdf_path)) {
            // If PDF doesn't exist, generate it using TCPDF
            require_once('tcpdf_invoice.php');
            $pdf_path = generateInvoicePDF($invoice_number);
        }
        
        if (file_exists($pdf_path)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="invoice_' . $invoice_number . '.pdf"');
            header('Content-Length: ' . filesize($pdf_path));
            readfile($pdf_path);
            exit;
        } else {
            throw new Exception('PDF could not be generated');
        }
    } catch (Exception $e) {
        header('Content-Type: text/html');
        die('Error generating PDF: ' . $e->getMessage());
    }
}

?>

<h1>Invoice List</h1>
<hr>

<div class="row">

	<div class="col-xs-12">

		<div id="response" class="alert alert-success" style="display:none;">
			<a href="#" class="close" data-dismiss="alert">&times;</a>
			<div class="message"></div>
		</div>
	
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4>Manage Invoices</h4>
			</div>
			<div class="panel-body form-group form-group-sm">
				<?php getInvoices(); ?>
			</div>
		</div>
	</div>
<div>

<div id="delete_invoice" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Delete Invoice</h4>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this invoice?</p>
      </div>
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-primary" id="delete">Delete</button>
		<button type="button" data-dismiss="modal" class="btn">Cancel</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
	include('footer.php');
?>