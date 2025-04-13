<?php


include('header.php');
include('functions.php');

// Get invoice details
$invoice = getInvoice($_GET['id']);
$customers = getCustomers();
$products = getProducts();

// Get customer details
$customer = getCustomer($invoice['invoice']);

// Initialize invoice variables
$invoice_notes = $invoice['invoice_notes'] ?? '';
$invoice_type = $invoice['invoice_type'] ?? 'invoice';
$invoice_status = $invoice['status'] ?? 'Unpaid';
$invoice_subtotal = $invoice['invoice_subtotal'] ?? 0;
$invoice_discount = $invoice['invoice_discount'] ?? 0;
$invoice_shipping = $invoice['invoice_shipping'] ?? 0;
$invoice_vat = $invoice['invoice_vat'] ?? 0;
$invoice_total = $invoice['invoice_total'] ?? 0;

// Initialize customer variables with default values
$customer_name = $customer['name'] ?? '';
$customer_address_1 = $customer['address_1'] ?? '';
$customer_address_2 = $customer['address_2'] ?? '';
$customer_town = $customer['town'] ?? '';
$customer_county = $customer['county'] ?? '';
$customer_postcode = $customer['postcode'] ?? '';
$customer_phone = $customer['phone'] ?? '';
$customer_email = $customer['email'] ?? '';
$custom_email = $customer['custom_email'] ?? '';

// Initialize shipping variables with default values
$customer_name_ship = $customer['name_ship'] ?? '';
$customer_address_1_ship = $customer['address_1_ship'] ?? '';
$customer_address_2_ship = $customer['address_2_ship'] ?? '';
$customer_town_ship = $customer['town_ship'] ?? '';
$customer_county_ship = $customer['county_ship'] ?? '';
$customer_postcode_ship = $customer['postcode_ship'] ?? '';

// Get invoice items
$items = getInvoiceItems($invoice['invoice']);

// Connect to the database
$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

// output any connection error
if ($mysqli->connect_error) {
	die('Error : ('.$mysqli->connect_errno .') '. $mysqli->connect_error);
}

// the query
$query = "SELECT p.*, i.*, c.*
			FROM invoice_items p 
			JOIN invoices i ON i.invoice = p.invoice
			JOIN customers c ON c.invoice = i.invoice
			WHERE p.invoice = '" . $mysqli->real_escape_string($invoice['invoice']) . "'";

$result = mysqli_query($mysqli, $query);

// mysqli select query
if($result) {
	while ($row = mysqli_fetch_assoc($result)) {
		$customer_name = $row['name']; // customer name
		$customer_email = $row['email']; // customer email
		$customer_address_1 = $row['address_1']; // customer address
		$customer_address_2 = $row['address_2']; // customer address
		$customer_town = $row['town']; // customer town
		$customer_county = $row['county']; // customer county
		$customer_postcode = $row['postcode']; // customer postcode
		$customer_phone = $row['phone']; // customer phone number
		
		//shipping
		$customer_name_ship = $row['name_ship']; // customer name (shipping)
		$customer_address_1_ship = $row['address_1_ship']; // customer address (shipping)
		$customer_address_2_ship = $row['address_2_ship']; // customer address (shipping)
		$customer_town_ship = $row['town_ship']; // customer town (shipping)
		$customer_county_ship = $row['county_ship']; // customer county (shipping)
		$customer_postcode_ship = $row['postcode_ship']; // customer postcode (shipping)

		// invoice details
		$invoice_number = $row['invoice']; // invoice number
		$custom_email = $row['custom_email']; // invoice custom email body
		$invoice_date = $row['invoice_date']; // invoice date
		$invoice_due_date = $row['invoice_due_date']; // invoice due date
		$invoice_subtotal = $row['subtotal']; // invoice sub-total
		$invoice_shipping = $row['shipping']; // invoice shipping amount
		$invoice_discount = $row['discount']; // invoice discount
		$invoice_vat = $row['vat']; // invoice vat
		$invoice_total = $row['total']; // invoice total
		$invoice_notes = $row['notes']; // Invoice notes
		$invoice_type = $row['invoice_type']; // Invoice type
		$invoice_status = $row['status']; // Invoice status
	}
}

/* close connection */
$mysqli->close();

// Add this at the top of the file, right after the includes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_invoice') {
    header('Content-Type: application/json');
    
    $mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
    
    if ($mysqli->connect_error) {
        echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
        exit;
    }
    
    try {
        // Update invoice
        $query = "UPDATE invoices SET 
            invoice_date = ?,
            invoice_due_date = ?,
            invoice_type = ?,
            status = ?,
            invoice_notes = ?,
            invoice_subtotal = ?,
            invoice_discount = ?,
            invoice_shipping = ?,
            invoice_vat = ?,
            invoice_total = ?
            WHERE invoice = ?";
            
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('ssssssddddds', 
            $_POST['invoice_date'],
            $_POST['invoice_due_date'],
            $_POST['invoice_type'],
            $_POST['invoice_status'],
            $_POST['invoice_notes'],
            $_POST['invoice_subtotal'],
            $_POST['invoice_discount'],
            $_POST['invoice_shipping'],
            $_POST['invoice_vat'],
            $_POST['invoice_total'],
            $_POST['invoice_id']
        );
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to update invoice: ' . $stmt->error);
        }
        
        // Update customer
        $query = "UPDATE customers SET 
            name = ?,
            address_1 = ?,
            address_2 = ?,
            town = ?,
            county = ?,
            postcode = ?,
            phone = ?,
            email = ?,
            custom_email = ?,
            name_ship = ?,
            address_1_ship = ?,
            address_2_ship = ?,
            town_ship = ?,
            county_ship = ?,
            postcode_ship = ?
            WHERE invoice = ?";
            
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('ssssssssssssssss', 
            $_POST['customer_name'],
            $_POST['customer_address_1'],
            $_POST['customer_address_2'],
            $_POST['customer_town'],
            $_POST['customer_county'],
            $_POST['customer_postcode'],
            $_POST['customer_phone'],
            $_POST['customer_email'],
            $_POST['custom_email'],
            $_POST['customer_name_ship'],
            $_POST['customer_address_1_ship'],
            $_POST['customer_address_2_ship'],
            $_POST['customer_town_ship'],
            $_POST['customer_county_ship'],
            $_POST['customer_postcode_ship'],
            $_POST['invoice_id']
        );
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to update customer: ' . $stmt->error);
        }
        
        // Update invoice items
        if (isset($_POST['invoice_product']) && is_array($_POST['invoice_product'])) {
            // First, delete existing items
            $query = "DELETE FROM invoice_items WHERE invoice = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('s', $_POST['invoice_id']);
            $stmt->execute();
            
            // Then insert new items
            $query = "INSERT INTO invoice_items (invoice, product, qty, price, discount, subtotal) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($query);
            
            foreach ($_POST['invoice_product'] as $key => $product) {
                $qty = $_POST['invoice_product_qty'][$key];
                $price = $_POST['invoice_product_price'][$key];
                $discount = $_POST['invoice_product_discount'][$key];
                $subtotal = $_POST['invoice_product_sub'][$key];
                
                $stmt->bind_param('ssiddd', 
                    $_POST['invoice_id'],
                    $product,
                    $qty,
                    $price,
                    $discount,
                    $subtotal
                );
                
                if (!$stmt->execute()) {
                    throw new Exception('Failed to update items: ' . $stmt->error);
                }
            }
        }
        
        echo json_encode(['status' => 'success', 'message' => 'Invoice updated successfully']);
        
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    
    $mysqli->close();
    exit;
}

?>

		<h1>Edit Invoice (<?php echo $invoice['invoice']; ?>)</h1>
		<hr>

		<div id="response" class="alert alert-success" style="display:none;">
			<a href="#" class="close" data-dismiss="alert">&times;</a>
			<div class="message"></div>
		</div>

		<form method="post" id="update_invoice">
			<input type="hidden" name="action" value="update_invoice">
			<input type="hidden" name="invoice_id" value="<?php echo $invoice['invoice']; ?>">

			<div class="row">
				<div class="col-xs-12">
					<textarea name="custom_email" id="custom_email" class="custom_email_textarea" placeholder="Enter a custom email message here if you wish to override the default invoice type email message."><?php echo $custom_email; ?></textarea>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-5">
					<h1>
						<img src="<?php echo COMPANY_LOGO ?>" class="img-responsive">
					</h1>
				</div>
				<div class="col-xs-7 text-right">
					<div class="row">
						<div class="col-xs-6">
							<h1>INVOICE</h1>
						</div>
						<div class="col-xs-3">
							<select name="invoice_type" id="invoice_type" class="form-control">
								<option value="invoice" <?php if($invoice_type === 'invoice'){?>selected<?php } ?>>Invoice</option>
								<option value="quote" <?php if($invoice_type === 'quote'){?>selected<?php } ?>>Quote</option>
								<option value="receipt" <?php if($invoice_type === 'receipt'){?>selected<?php } ?>>Receipt</option>
							</select>
						</div>
						<div class="col-xs-3">
							<select name="invoice_status" id="invoice_status" class="form-control">
								<option value="open" <?php if($invoice_status === 'open'){?>selected<?php } ?>>Open</option>
								<option value="paid" <?php if($invoice_status === 'paid'){?>selected<?php } ?>>Paid</option>
							</select>
						</div>
					</div>
					<div class="col-xs-4 no-padding-right">
				        <div class="form-group">
				            <div class="input-group date" id="invoice_date">
				                <input type="text" class="form-control required" name="invoice_date" placeholder="Select invoice date" data-date-format="<?php echo DATE_FORMAT ?>" value="<?php echo $invoice['invoice_date']; ?>" />
				                <span class="input-group-addon">
				                    <span class="glyphicon glyphicon-calendar"></span>
				                </span>
				            </div>
				        </div>
				    </div>
				    <div class="col-xs-4">
				        <div class="form-group">
				            <div class="input-group date" id="invoice_due_date">
				                <input type="text" class="form-control required" name="invoice_due_date" placeholder="Select due date" data-date-format="<?php echo DATE_FORMAT ?>" value="<?php echo $invoice['invoice_due_date']; ?>" />
				                <span class="input-group-addon">
				                    <span class="glyphicon glyphicon-calendar"></span>
				                </span>
				            </div>
				        </div>
				    </div>
					<div class="input-group col-xs-4 float-right">
						<span class="input-group-addon">#<?php echo INVOICE_PREFIX ?></span>
						<input type="text" name="invoice_id" id="invoice_id" class="form-control required" placeholder="Invoice Number" aria-describedby="sizing-addon1" value="<?php echo $invoice['invoice']; ?>">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4>Customer Information</h4>
							<div class="clear"></div>
						</div>
						<div class="panel-body form-group form-group-sm">
							<div class="row">
								<div class="col-xs-6">
									<div class="form-group">
										<label class="col-sm-4 control-label">Customer</label>
										<div class="col-sm-8">
											<select name="customer_id" id="customer_id" class="form-control">
												<option value="">Select Customer</option>
												<?php foreach ($customers as $c) { ?>
													<option value="<?php echo $c['id']; ?>" <?php echo ($c['id'] == $customer['id']) ? 'selected' : ''; ?>>
														<?php echo $c['name']; ?>
													</option>
												<?php } ?>
											</select>
										</div>
									</div>
								</div>
								<div class="col-xs-6">
									<div class="form-group">
										<label class="col-sm-4 control-label">Name</label>
										<div class="col-sm-8">
											<input type="text" class="form-control" name="customer_name" value="<?php echo $customer_name; ?>" required>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6">
									<div class="form-group">
										<label class="col-sm-4 control-label">Address 1</label>
										<div class="col-sm-8">
											<input type="text" class="form-control" name="customer_address_1" value="<?php echo $customer_address_1; ?>" required>
										</div>
									</div>
								</div>
								<div class="col-xs-6">
									<div class="form-group">
										<label class="col-sm-4 control-label">Town</label>
										<div class="col-sm-8">
											<input type="text" class="form-control" name="customer_town" value="<?php echo $customer_town; ?>" required>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6">
									<div class="form-group">
										<label class="col-sm-4 control-label">Postcode</label>
										<div class="col-sm-8">
											<input type="text" class="form-control" name="customer_postcode" value="<?php echo $customer_postcode; ?>" required>
										</div>
									</div>
								</div>
								<div class="col-xs-6">
									<div class="form-group">
										<label class="col-sm-4 control-label">Email</label>
										<div class="col-sm-8">
											<input type="email" class="form-control" name="customer_email" value="<?php echo $customer_email; ?>" required>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6">
									<div class="form-group">
										<label class="col-sm-4 control-label">Address 2</label>
										<div class="col-sm-8">
											<input type="text" class="form-control" name="customer_address_2" value="<?php echo $customer_address_2; ?>">
										</div>
									</div>
								</div>
								<div class="col-xs-6">
									<div class="form-group">
										<label class="col-sm-4 control-label">County</label>
										<div class="col-sm-8">
											<input type="text" class="form-control" name="customer_county" value="<?php echo $customer_county; ?>">
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6">
									<div class="form-group">
										<label class="col-sm-4 control-label">Phone</label>
										<div class="col-sm-8">
											<input type="text" class="form-control" name="customer_phone" value="<?php echo $customer_phone; ?>">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-6 text-right">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4>Shipping Information</h4>
						</div>
						<div class="panel-body form-group form-group-sm">
							<div class="row">
								<div class="col-xs-6">
									<div class="form-group">
										<label class="col-sm-4 control-label">Name</label>
										<div class="col-sm-8">
											<input type="text" class="form-control" name="customer_name_ship" value="<?php echo $customer_name_ship; ?>">
										</div>
									</div>
								</div>
								<div class="col-xs-6">
									<div class="form-group">
										<label class="col-sm-4 control-label">Address 2</label>
										<div class="col-sm-8">
											<input type="text" class="form-control" name="customer_address_2_ship" value="<?php echo $customer_address_2_ship; ?>">
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6">
									<div class="form-group">
										<label class="col-sm-4 control-label">County</label>
										<div class="col-sm-8">
											<input type="text" class="form-control" name="customer_county_ship" value="<?php echo $customer_county_ship; ?>">
										</div>
									</div>
								</div>
								<div class="col-xs-6">
									<div class="form-group">
										<label class="col-sm-4 control-label">Address 1</label>
										<div class="col-sm-8">
											<input type="text" class="form-control" name="customer_address_1_ship" value="<?php echo $customer_address_1_ship; ?>">
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-6">
									<div class="form-group">
										<label class="col-sm-4 control-label">Town</label>
										<div class="col-sm-8">
											<input type="text" class="form-control" name="customer_town_ship" value="<?php echo $customer_town_ship; ?>">
										</div>
									</div>
								</div>
								<div class="col-xs-6">
									<div class="form-group">
										<label class="col-sm-4 control-label">Postcode</label>
										<div class="col-sm-8">
											<input type="text" class="form-control" name="customer_postcode_ship" value="<?php echo $customer_postcode_ship; ?>">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- / end client details section -->
			<table class="table table-bordered" id="invoice_table">
				<thead>
					<tr>
						<th width="500">
							<h4><a href="#" class="btn btn-success btn-xs add-row"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a> Item</h4>
						</th>
						<th>
							<h4>Qty</h4>
						</th>
						<th>
							<h4>Price</h4>
						</th>
						<th width="300">
							<h4>Discount</h4>
						</th>
						<th>
							<h4>Sub Total</h4>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php 

						// Connect to the database
						$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

						// output any connection error
						if ($mysqli->connect_error) {
							die('Error : ('.$mysqli->connect_errno .') '. $mysqli->connect_error);
						}

						// the query
						$query2 = "SELECT * FROM invoice_items WHERE invoice = '" . $mysqli->real_escape_string($invoice['invoice']) . "'";

						$result2 = mysqli_query($mysqli, $query2);

						//var_dump($result2);

						// mysqli select query
						if($result2) {
							while ($rows = mysqli_fetch_assoc($result2)) {

								//var_dump($rows);

							    $item_product = $rows['product'];
							    $item_qty = $rows['qty'];
							    $item_price = $rows['price'];
							    $item_discount = $rows['discount'];
							    $item_subtotal = $rows['subtotal'];
					?>
					<tr>
						<td>
							<div class="form-group form-group-sm  no-margin-bottom">
								<a href="#" class="btn btn-danger btn-xs delete-row"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
								<input type="text" class="form-control form-group-sm item-input invoice_product" name="invoice_product[]" placeholder="Enter item title and / or description" value="<?php echo $item_product; ?>">
								<p class="item-select">or <a href="#">select an item</a></p>
							</div>
						</td>
						<td class="text-right">
							<div class="form-group form-group-sm no-margin-bottom">
								<input type="text" class="form-control invoice_product_qty calculate" name="invoice_product_qty[]" value="<?php echo $item_qty; ?>">
							</div>
						</td>
						<td class="text-right">
							<div class="input-group input-group-sm  no-margin-bottom">
								<span class="input-group-addon"><?php echo CURRENCY ?></span>
								<input type="text" class="form-control calculate invoice_product_price required" name="invoice_product_price[]" aria-describedby="sizing-addon1" placeholder="0.00" value="<?php echo $item_price; ?>">
							</div>
						</td>
						<td class="text-right">
							<div class="form-group form-group-sm  no-margin-bottom">
								<input type="text" class="form-control calculate" name="invoice_product_discount[]" placeholder="Enter % or value (ex: 10% or 10.50)" value="<?php echo $item_discount; ?>">
							</div>
						</td>
						<td class="text-right">
							<div class="input-group input-group-sm">
								<span class="input-group-addon"><?php echo CURRENCY ?></span>
								<input type="text" class="form-control calculate-sub" name="invoice_product_sub[]" id="invoice_product_sub" aria-describedby="sizing-addon1" value="<?php echo $item_subtotal; ?>" disabled>
							</div>
						</td>
					</tr>
					<?php } } ?>
				</tbody>
			</table>
			<div id="invoice_totals" class="padding-right row text-right">
				<div class="col-xs-6">
					<div class="input-group form-group-sm textarea no-margin-bottom">
						<textarea class="form-control" name="invoice_notes" placeholder="Please enter any order notes here."><?php echo $invoice_notes; ?></textarea>
					</div>
				</div>
				<div class="col-xs-6 no-padding-right">
					<div class="row">
						<div class="col-xs-3 col-xs-offset-6">
							<strong>Sub Total:</strong>
						</div>
						<div class="col-xs-3">
							<?php echo CURRENCY ?><span class="invoice-sub-total"> <?php echo $invoice_subtotal; ?></span>
							<input type="hidden" name="invoice_subtotal" id="invoice_subtotal" value="<?php echo $invoice_subtotal; ?>">
						</div>
					</div>
					<div class="row">
						<div class="col-xs-3 col-xs-offset-6">
							<strong>Discount:</strong>
						</div>
						<div class="col-xs-3">
							<?php echo CURRENCY ?><span class="invoice-discount"> <?php echo $invoice_discount; ?></span>
							<input type="hidden" name="invoice_discount" id="invoice_discount" value="<?php echo $invoice_discount; ?>">
						</div>
					</div>
					<div class="row">
						<div class="col-xs-3 col-xs-offset-6">
							<strong class="shipping">Shipping:</strong>
						</div>
						<div class="col-xs-3">
							<div class="input-group input-group-sm">
								<span class="input-group-addon"><?php echo CURRENCY ?></span>
								<input type="text" class="form-control calculate shipping" name="invoice_shipping" aria-describedby="sizing-addon1" placeholder="0.00" value="<?php echo $invoice_shipping; ?>">
							</div>
						</div>
					</div>
					<?php if (ENABLE_VAT == true) { ?>
					<div class="row">
						<div class="col-xs-3 col-xs-offset-6">
							<strong>TAX/VAT:</strong>
						</div>
						<div class="col-xs-3">
							<?php echo CURRENCY ?><span class="invoice-vat" data-enable-vat="<?php echo ENABLE_VAT ?>" data-vat-rate="<?php echo VAT_RATE ?>" data-vat-method="<?php echo VAT_INCLUDED ?>"><?php echo $invoice_vat; ?></span>
							<input type="hidden" name="invoice_vat" id="invoice_vat" value="<?php echo $invoice_vat; ?>">
						</div>
					</div>
					<?php } ?>
					<div class="row">
						<div class="col-xs-3 col-xs-offset-6">
							<strong>Total:</strong>
						</div>
						<div class="col-xs-3">
							<?php echo CURRENCY ?><span class="invoice-total"> <?php echo $invoice_total; ?></span>
							<input type="hidden" name="invoice_total" id="invoice_total" value="<?php echo $invoice_total; ?>">
						</div>
					</div>
				</div>

			</div>
			<div class="row">
				<div class="col-xs-12 margin-top btn-group">
					<input type="submit" id="action_edit_invoice" class="btn btn-success float-right" value="Update Invoice" data-loading-text="Updating...">
				</div>
			</div>
		</form>

		<div id="insert" class="modal fade">
		  <div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title">Select an item</h4>
		      </div>
		      <div class="modal-body">
				<?php popProductsList(); ?>
		      </div>
		      <div class="modal-footer">
		        <button type="button" data-dismiss="modal" class="btn btn-primary" id="selected">Add</button>
				<button type="button" data-dismiss="modal" class="btn">Cancel</button>
		      </div>
		    </div><!-- /.modal-content -->
		  </div><!-- /.modal-dialog -->
		</div><!-- /.modal -->

		<a href="#" data-invoice-id="<?php echo $invoice['invoice']; ?>" data-email="<?php echo $customer_email; ?>" data-invoice-type="<?php echo $invoice['invoice_type']; ?>" data-custom-email="<?php echo $custom_email; ?>" class="btn btn-success btn-xs email-invoice">
			<span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
		</a>

<?php
	include('footer.php');
?>

<script>
$(document).ready(function() {
    $('#update_invoice').on('submit', function(e) {
        e.preventDefault();
        
        var $btn = $('#action_edit_invoice');
        $btn.button('loading');
        
        // Get all form data
        var formData = new FormData(this);
        
        $.ajax({
            url: 'invoice-edit.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#response').removeClass('alert-danger').addClass('alert-success');
                    $('#response .message').html(response.message);
                    $('#response').show();
                    
                    setTimeout(function() {
                        window.location.href = 'invoice-list.php';
                    }, 2000);
                } else {
                    $('#response').removeClass('alert-success').addClass('alert-danger');
                    $('#response .message').html(response.message || 'An error occurred');
                    $('#response').show();
                }
            },
            error: function(xhr, status, error) {
                $('#response').removeClass('alert-success').addClass('alert-danger');
                $('#response .message').html('Error updating invoice: ' + error);
                $('#response').show();
            },
            complete: function() {
                $btn.button('reset');
            }
        });
    });
    
    // Add row functionality
    $('.add-row').on('click', function() {
        var row = '<tr>' +
            '<td>' +
                '<div class="form-group form-group-sm no-margin-bottom">' +
                    '<a href="#" class="btn btn-danger btn-xs delete-row"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>' +
                    '<input type="text" class="form-control form-group-sm item-input invoice_product" name="invoice_product[]" placeholder="Enter item title and / or description">' +
                    '<p class="item-select">or <a href="#">select an item</a></p>' +
                '</div>' +
            '</td>' +
            '<td class="text-right">' +
                '<div class="form-group form-group-sm no-margin-bottom">' +
                    '<input type="text" class="form-control invoice_product_qty calculate" name="invoice_product_qty[]" value="1">' +
                '</div>' +
            '</td>' +
            '<td class="text-right">' +
                '<div class="input-group input-group-sm no-margin-bottom">' +
                    '<span class="input-group-addon"><?php echo CURRENCY ?></span>' +
                    '<input type="text" class="form-control calculate invoice_product_price required" name="invoice_product_price[]" aria-describedby="sizing-addon1" placeholder="0.00">' +
                '</div>' +
            '</td>' +
            '<td class="text-right">' +
                '<div class="form-group form-group-sm no-margin-bottom">' +
                    '<input type="text" class="form-control calculate" name="invoice_product_discount[]" placeholder="Enter % or value (ex: 10% or 10.50)">' +
                '</div>' +
            '</td>' +
            '<td class="text-right">' +
                '<div class="input-group input-group-sm">' +
                    '<span class="input-group-addon"><?php echo CURRENCY ?></span>' +
                    '<input type="text" class="form-control calculate-sub" name="invoice_product_sub[]" aria-describedby="sizing-addon1" disabled>' +
                '</div>' +
            '</td>' +
        '</tr>';
        
        $('#invoice_table tbody').append(row);
    });
    
    // Delete row functionality
    $(document).on('click', '.delete-row', function() {
        $(this).closest('tr').remove();
        calculateTotal();
    });
    
    // Calculate totals
    function calculateTotal() {
        var subtotal = 0;
        var discount = 0;
        var shipping = parseFloat($('input[name="invoice_shipping"]').val()) || 0;
        
        $('input[name="invoice_product_sub[]"]').each(function() {
            subtotal += parseFloat($(this).val()) || 0;
        });
        
        $('input[name="invoice_product_discount[]"]').each(function() {
            var discountValue = $(this).val();
            if (discountValue.indexOf('%') > -1) {
                var percent = parseFloat(discountValue) / 100;
                discount += subtotal * percent;
            } else {
                discount += parseFloat(discountValue) || 0;
            }
        });
        
        var vat = 0;
        if (<?php echo ENABLE_VAT ? 'true' : 'false'; ?>) {
            vat = (subtotal - discount) * (<?php echo VAT_RATE; ?> / 100);
        }
        
        var total = subtotal - discount + shipping + vat;
        
        $('.invoice-sub-total').text(subtotal.toFixed(2));
        $('.invoice-discount').text(discount.toFixed(2));
        $('.invoice-vat').text(vat.toFixed(2));
        $('.invoice-total').text(total.toFixed(2));
        
        $('input[name="invoice_subtotal"]').val(subtotal.toFixed(2));
        $('input[name="invoice_discount"]').val(discount.toFixed(2));
        $('input[name="invoice_vat"]').val(vat.toFixed(2));
        $('input[name="invoice_total"]').val(total.toFixed(2));
    }
    
    // Calculate on input change
    $(document).on('input', '.calculate', function() {
        var row = $(this).closest('tr');
        var qty = parseFloat(row.find('input[name="invoice_product_qty[]"]').val()) || 0;
        var price = parseFloat(row.find('input[name="invoice_product_price[]"]').val()) || 0;
        var discount = row.find('input[name="invoice_product_discount[]"]').val();
        var subtotal = qty * price;
        
        if (discount.indexOf('%') > -1) {
            var percent = parseFloat(discount) / 100;
            subtotal -= subtotal * percent;
        } else {
            subtotal -= parseFloat(discount) || 0;
        }
        
        row.find('input[name="invoice_product_sub[]"]').val(subtotal.toFixed(2));
        calculateTotal();
    });
});
</script>