<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../_conn.php';

$user_name = $_SESSION['user_name'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    $whatAction = $data['whatAction'] ?? '';
    // echo "<script>console.log('What action: ". $whatAction ."') </script>";

    if ($whatAction === 'createInvoice') {
        $table = $data['table'];

        $docType = $data['document_type'];
        if ($table === 'invoice') {
            $prefix = ($docType === 'with GST') ? 'INV' : 'INVWO';
        } else if ($table === 'quotation') {
            $prefix = ($docType === 'with GST') ? 'QT' : 'QTWO';
        }

        $currentYear = date("Y");


        if ($table === 'invoice') {

            // Fetch latest invoice ID for the current document type and current or previous year
            $query = "SELECT invoice_id FROM $table WHERE invoice_id LIKE '$prefix-%' AND created_for = '$user_name' ORDER BY invoice_id DESC LIMIT 1 FOR UPDATE";
            $result = $conn->query($query);

            if ($row = $result->fetch_assoc()) {
                $parts = explode('-', $row['invoice_id']);
                $yearInId = $parts[1];
                if ($yearInId === $currentYear) {
                    $lastNumber = intval($parts[2]) + 1;
                } else {
                    $lastNumber = 1; // New year, start from 1
                }
            } else {
                $lastNumber = 1;
            }

            $newInvoiceId = $prefix . '-' . $currentYear . '-' . str_pad($lastNumber, 3, '0', STR_PAD_LEFT);

            // Get negative stock setting (default = 0)
            $allowNegativeStock = 0;
            $settingsResult = $conn->query("SELECT allow_negative_stock FROM store_inventory_settings WHERE created_by = '$user_name' LIMIT 1");

            if ($settingsResult && $row = $settingsResult->fetch_assoc()) {
                $allowNegativeStock = (int) $row['allow_negative_stock'];
            }

            // Prepare item arrays
            $itemNames = explode(',', $data['item_names']);
            $quantities = explode(',', $data['quantities']);

            // Validate stock before inserting invoice
            for ($i = 0; $i < count($itemNames); $i++) {
                $item = trim($itemNames[$i]);
                $qty = (int) trim($quantities[$i]);

                $stockResult = $conn->query("SELECT stock FROM retail_invetory WHERE item_name = '$item' AND inventory_of = '$user_name' LIMIT 1");

                if ($stockResult && $stockRow = $stockResult->fetch_assoc()) {
                    $currentStock = (int) $stockRow['stock'];
                    if (!$allowNegativeStock && ($currentStock - $qty < 0)) {
                        echo "<script>alert('Error: Not enough stock for item \"$item\". Available: $currentStock, Requested: $qty');</script>";
                        exit;
                    }
                } else {
                    echo "<script>alert('Error: Item \"$item\" not found in inventory.');</script>";
                    exit;
                }
            }


            // Fetch latest sales ID current or previous year
            $result = $conn->query("SELECT Sales_Id FROM invoice ORDER BY CAST(SUBSTRING(Sales_Id, 5) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");

            if ($result && $row = $result->fetch_assoc()) {
                $lastId = $row['Sales_Id']; // e.g. SL-005
                $num = (int) substr($lastId, 4);   // get "005" → 5
                $newNum = $num + 1;
            } else {
                $newNum = 1;
            }

            $newSalesId = 'SL-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);

            // Fetch latest payment ID current or previous year
            $result = $conn->query("SELECT payment_id FROM invoice WHERE created_for = '$user_name' ORDER BY CAST(SUBSTRING(payment_id, 5) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");

            if ($result && $row = $result->fetch_assoc()) {
                $lastId = $row['payment_id']; // e.g. SL-005
                $num = (int) substr($lastId, 4);   // get "005" → 5
                $newNum = $num + 1;
            } else {
                $newNum = 1;
            }

            $newPaymentId = 'PAY-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);


            // Prepare and insert
            $stmt = $conn->prepare("INSERT INTO $table (
                invoice_id, customer_name, document_type, date, due_date, tax_rate, notes, subtotal, GST_amount, grand_total,
                item_name, description, quantity, price, total, Sales_Id, payment_id, payment_method, created_by, created_for, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param(
                "sssssssdddsssssssssss",
                $newInvoiceId,
                $data['customer_name'],
                $data['document_type'],
                $data['date'],
                $data['due_date'],
                $data['tax_rate'],
                $data['notes'],
                $data['subtotal'],
                $data['GST_amount'],
                $data['grand_total'],
                $data['item_names'],
                $data['descriptions'],
                $data['quantities'],
                $data['prices'],
                $data['totals'],
                $newSalesId,
                $newPaymentId,
                $data['payment_method'],
                $user_name,
                $user_name,
                $data['status']

            );

            // Subtract sold quantity from inventory
            for ($i = 0; $i < count($itemNames); $i++) {
                $item = trim($itemNames[$i]);
                $qty = (int) trim($quantities[$i]);

                $updateInventory = $conn->query("UPDATE retail_invetory SET stock = stock - $qty WHERE item_name = '$item' AND inventory_of = '$user_name'");
            }

            if ($stmt->execute()) {
                echo "Invoice inserted successfully!";
            } else {
                echo "Error: " . $stmt->error;
            }


        } else if ($table === 'quotation') {

            // Fetch latest invoice ID for the current document type and current or previous year
            $query = "SELECT invoice_id FROM $table WHERE invoice_id LIKE '$prefix-%' ORDER BY invoice_id DESC LIMIT 1 FOR UPDATE";
            $result = $conn->query($query);

            if ($row = $result->fetch_assoc()) {
                $parts = explode('-', $row['invoice_id']);
                $yearInId = $parts[1];
                if ($yearInId === $currentYear) {
                    $lastNumber = intval($parts[2]) + 1;
                } else {
                    $lastNumber = 1; // New year, start from 1
                }
            } else {
                $lastNumber = 1;
            }

            $newInvoiceId = $prefix . '-' . $currentYear . '-' . str_pad($lastNumber, 3, '0', STR_PAD_LEFT);

            $stmt = $conn->prepare("INSERT INTO $table (
                invoice_id, customer_name, document_type, date, due_date, tax_rate, notes, subtotal, GST_amount, grand_total,
                item_name, description, quantity, price, total, payment_method, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param(
                "sssssssdddsssssss",
                $newInvoiceId,
                $data['customer_name'],
                $data['document_type'],
                $data['date'],
                $data['due_date'],
                $data['tax_rate'],
                $data['notes'],
                $data['subtotal'],
                $data['GST_amount'],
                $data['grand_total'],
                $data['item_names'],
                $data['descriptions'],
                $data['quantities'],
                $data['prices'],
                $data['totals'],
                $data['payment_method'],
                $data['status']

            );

            if ($stmt->execute()) {
                echo "Invoice inserted successfully!";
            } else {
                echo "Error: " . $stmt->error;
            }

        }


    } else if ($whatAction === 'createSales') {
        $table = $data['table'];

        if ($table === 'credit_note') {
            $prefix = 'CN';
        } else if ($table === 'sales_return') {
            $prefix = 'SR';
        }

        $currentYear = date("Y");

        // Fetch latest invoice ID for the current document type and current or previous year
        $query = "SELECT sales_return_id FROM $table WHERE sales_return_id LIKE '$prefix-%' ORDER BY sales_return_id DESC LIMIT 1";
        $result = $conn->query($query);

        if ($row = $result->fetch_assoc()) {
            $parts = explode('-', $row['sales_return_id']);
            $yearInId = $parts[1];
            if ($yearInId === $currentYear) {
                $lastNumber = intval($parts[2]) + 1;
            } else {
                $lastNumber = 1; // New year, start from 1
            }
        } else {
            $lastNumber = 1;
        }

        $newSalesId = $prefix . '-' . $currentYear . '-' . str_pad($lastNumber, 3, '0', STR_PAD_LEFT);


        // Prepare and insert
        $stmt = $conn->prepare("INSERT INTO $table (
            sales_return_id, customer_name, date, tax_rate, notes, subtotal, GST_amount, Grand_total,
            item, description, quantity, price, total, payment_method
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "sssssdddssssss",
            $newSalesId,
            $data['customer_name'],
            $data['date'],
            $data['tax_rate'],
            $data['notes'],
            $data['subtotal'],
            $data['GST_amount'],
            $data['grand_total'],
            $data['item_names'],
            $data['descriptions'],
            $data['quantities'],
            $data['prices'],
            $data['totals'],
            $data['payment_method']
        );

        if ($stmt->execute()) {
            echo "Invoice inserted successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else if ($whatAction === 'exportRecords') {
        $exportFormat = $data['export_format'] ?? '';
        $startDate = $data['start_date'] ?? '';
        $endDate = $data['end_date'] ?? '';

        // Validate inputs
        if (!in_array($exportFormat, ['csv', 'pdf']) || empty($startDate) || empty($endDate)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid input parameters']);
            exit;
        }

        // Sanitize dates
        $startDate = $conn->real_escape_string($startDate);
        $endDate = $conn->real_escape_string($endDate);

        // Fetch records
        $query = "SELECT invoice_id, customer_name, document_type, date, due_date, tax_rate, notes, subtotal, GST_amount, grand_total, item_name, description, quantity, price, total 
                  FROM invoice 
                  WHERE date BETWEEN '$startDate' AND '$endDate' 
                  ORDER BY invoice_id";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $rows = [];
            $headers = ['invoice_id', 'customer_name', 'document_type', 'date', 'due_date', 'tax_rate', 'notes', 'subtotal', 'GST_amount', 'grand_total', 'item_name', 'description', 'quantity', 'price', 'total'];
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }

            // Return JSON response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'headers' => $headers,
                'rows' => $rows,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No records found for the selected date range']);
        }
        exit;
    }
}
?>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['whatAction'])) {
    if ($_POST['whatAction'] === 'bankDepositEntry') {

        function clean($input)
        {
            return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }

        // Collect data for transaction
        $date = clean($_POST['date']);
        $transferTo = clean($_POST['transferTO']);
        $amount = clean($_POST['amount']);
        $user_id = $_SESSION['uid'];

        // Start database transaction
        $conn->begin_transaction();

        try {

            // Insert the transaction record
            $stmt = $conn->prepare("INSERT INTO retail_store_cash 
                (date, cash_deposit, cash_deposit_amount, user_id) 
                VALUES (?, ?, ?, ?)");

            $stmt->bind_param("ssds", $date, $transferTo, $amount, $user_id);
            $stmt->execute();

            $conn->commit();
            $stmt->close();
            @header("Location: store_dashboard.php?page=billing");

        } catch (Exception $e) {
            $conn->rollback();
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Customer entry failed: " . $e->getMessage()
            ]);
            exit;
        }
    } else if ($_POST['whatAction'] === 'generateReceiving') {

        $deliveryId = clean($_POST['deliveryId']);
        $trackingId = clean($_POST['trackingId']);
        $requestId = clean($_POST['requestId']);
        $receivedDate = clean($_POST['receivedDate']);
        $receivedBy = clean($_POST['Received_by']);
        $received = "Received";

        // Verify Delivery ID and Tracking ID match with the given Request ID
        $stmt = $conn->prepare("SELECT * FROM retail_store_stock_request WHERE request_id = ? AND delivery_id = ? AND tracking_id = ?");
        $stmt->bind_param("sss", $requestId, $deliveryId, $trackingId);
        $stmt->execute();
        $result = $stmt->get_result();

        //If not matching, alert and exit
        if ($result->num_rows === 0) {
            echo "<script>alert('Delivery ID or Tracking ID does not match the given Request ID.'); window.location.href='store_dashboard.php?page=billing';</script>";
            $stmt->close();
            exit;
        }

        while ($row = $result->fetch_assoc()) {
            $item_name = $row['item_name'];
            $quantity = (int) $row['quantity'];

            // Update stock in retail_inventory
            $updateStockStmt = $conn->prepare("
                UPDATE retail_invetory 
                SET stock = stock + ? 
                WHERE item_name = ?
            ");
            $updateStockStmt->bind_param("is", $quantity, $item_name);
            $updateStockStmt->execute();
            $updateStockStmt->close();
        }

        //If matched, update received data
        $updateStmt = $conn->prepare("UPDATE retail_store_stock_request SET received_date = ?, received_by = ?, status = ? WHERE tracking_id  = ?");
        $updateStmt->bind_param("ssss", $receivedDate, $receivedBy, $received, $trackingId);

        if ($updateStmt->execute()) {
            echo "<script>alert('Receiving info successfully updated.'); window.location.href='store_dashboard.php?page=billing';</script>";
        } else {
            echo "<script>alert('Error updating receiving info.'); window.location.href='store_dashboard.php?page=billing';</script>";
        }

        // Close connections
        $stmt->close();
        $updateStmt->close();
    }
}

$created_for = $_SESSION['user_name'];
$query = "SELECT * FROM invoice WHERE created_for = '$created_for' ORDER BY invoice_id DESC LIMIT 10";
$loadDataValue = "All";
$loadDataText = "Show All";
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['loadData'])) {
    $loadData = $_GET['loadData'];
    if ($loadData === 'All') {
        $query = "SELECT * FROM invoice WHERE created_for = '$created_for' ORDER BY invoice_id DESC";
        @header("Location: store_dashboard.php?page=billing&loadData=All");
        $loadDataValue = "Less";
        $loadDataText = "Show Less";
    } else {
        @header("Location: store_dashboard.php?page=billing");
    }
}
?>

<?php
// Include mock database
require_once 'database.php';

// Get data from database
$invoices = get_billing_invoices();
$quotations = get_quotations();
$credit_notes = get_credit_notes();
$sales_returns = get_sales_returns();
$transactions = get_transactions();
$customers = get_customers();
$payment_methods = get_payment_methods();

// Handle actions
$success_message = '';
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        if ($action === 'create_invoice') {
            // Form submission for new invoice
            $invoice_data = [
                'customer' => isset($_POST['customer']) ? trim($_POST['customer']) : '',
                'amount' => isset($_POST['amount']) ? trim($_POST['amount']) : '',
                'type' => isset($_POST['type']) ? trim($_POST['type']) : '',
                'payment' => isset($_POST['payment']) ? trim($_POST['payment']) : '',
                'date' => isset($_POST['date']) ? trim($_POST['date']) : '',
                'status' => isset($_POST['status']) ? trim($_POST['status']) : ''
            ];
            $result = save_billing_invoice($invoice_data);
            if ($result['success']) {
                $success_message = $result['message'];
                // Refresh invoices
                $invoices = get_billing_invoices();
            } else {
                $error_message = $result['message'];
            }
        } elseif ($action === 'print_invoice' && isset($_POST['invoice_id'])) {
            $success_message = "Printing store invoice {$_POST['invoice_id']}";
        } elseif ($action === 'view_invoice' && isset($_POST['invoice_id'])) {
            $success_message = "Viewing store invoice {$_POST['invoice_id']}";
        } elseif ($action === 'generate_receipt') {
            $success_message = 'Generating receipt';
        } elseif ($action === 'export_records') {
            $success_message = 'Exporting records';
        } elseif ($action === 'record_payment') {
            $success_message = 'Recording payment';
        }
    }
}

// Filter and search parameters
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) && in_array($_GET['status'], ['All', 'Paid', 'Pending', 'Overdue']) ? $_GET['status'] : 'All';
$type_filter = isset($_GET['type']) && in_array($_GET['type'], ['All', 'Retail', 'Wholesale']) ? $_GET['type'] : 'All';

// Filter invoices
$filtered_invoices = array_filter($invoices, function ($invoice) use ($search_query, $status_filter, $type_filter) {
    $matches_search = empty($search_query) ||
        stripos($invoice['id'], $search_query) !== false ||
        stripos($invoice['customer'], $search_query) !== false;
    $matches_status = $status_filter === 'All' || $invoice['status'] === $status_filter;
    $matches_type = $type_filter === 'All' || $invoice['type'] === $type_filter;
    return $matches_search && $matches_status && $matches_type;
});

// Statuses and types for filter
$statuses = ['All', 'Paid', 'Pending', 'Overdue'];
$types = ['All', 'Retail', 'Wholesale'];
?>

<div class="main-content">
    <h1><i class="fas fa-file-invoice-dollar text-primary"></i> Store Billing System</h1>
    <p>Manage all store-related invoices, receipts, and payments</p>

    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="modal" id="exportRecords">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Export Records</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Click the button below to export all invoice records as a CSV file.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="exportTableToCSV()">Export Records</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Create Invoice form -->
    <div id="invoiceModal" class="modal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <button type="button" class="btn-close" onclick="closeInvoiceModal()"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Customer:</label>
                            <select class="form-select" id="customer" name="customer" required>
                                <option>Select customer</option>
                                <?php

                                // Fetch transactions from the database
                                $result = $conn->query("SELECT name FROM customer WHERE created_for = '$user_name' ORDER BY customer_Id DESC");

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option>" . $row['name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                            <button class="btn bg-primary text-white mt-2" id="ADD">+ Add Customer</button>
                        </div>

                        <!-- Hidden form -->
                        <div id="HiddenForm" class="card p-3 mb-4" style="display:none;">
                            <form method="POST" action="save_customer.php">

                                <div class="mb-3">
                                    <label class="form-label">Customer Name</label>
                                    <input type="text" name="customer_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="type" class="form-label">Type</label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="Retail">Retail</option>
                                        <option value="Wholesale">Wholesale</option>
                                        <option value="Contractor">Contractor</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="customer_phone" class="form-control" required
                                        maxlength="10">
                                </div>
                                <input type="submit" value="Save Customer" class="btn btn-success text-white"
                                    name="whatAction">
                            </form>
                        </div>

                        <!-- JS toggle  -->
                        <script>
                            document.getElementById("ADD").addEventListener("click", function () {
                                const form = document.getElementById("HiddenForm");
                                form.style.display = (form.style.display === "none") ? "block" : "none"
                            })
                        </script>




                        <div class="col-md-4">
                            <label class="form-label">Payment Method:</label>
                            <select class="form-select" id="invoicePaymentMethod" name="invoicePaymentMethod" required>
                                <option>Select payment method</option>
                                <option>Digital payment</option>
                                <option>Cash</option>
                                <option>BNPL</option>
                                <option>Payment gateway</option>
                            </select>
                        </div>
                        <div class="col-md-4" id="status_section">
                            <label class="form-label">Status:</label>
                            <select class="form-select" id="invoiceStatus" name="invoiceStatus" required>
                                <option>Select status</option>
                                <option>Completed</option>
                                <option>Pending</option>
                                <option>Refund</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block">Document Type:</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="docType" value="withGST" checked
                                onchange="toggleGST()">
                            <label class="form-check-label">With GST</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="docType" value="withoutGST"
                                onchange="toggleGST()">
                            <label class="form-check-label">Without GST</label>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Date:</label>
                            <input type="date" id="invoiceDate" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Due Date:</label>
                            <input type="date" id="dueDate" class="form-control">
                        </div>
                        <div class="col-md-4 gst-section">
                            <label class="form-label">Tax Rate:</label>
                            <select id="taxRate" class="form-select" onchange="updateTotals()">
                                <option value="5">GST 5%</option>
                                <option value="12">GST 12%</option>
                                <option value="18">GST 18%</option>
                                <option value="28">GST 28%</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered" id="itemTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Description</th>
                                    <th>Qty</th>
                                    <th>Price (₹)</th>
                                    <th>Total (₹)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <button class="btn btn-sm btn-outline-primary" onclick="addItem()">+ Add Item</button>
                        <button class="btn btn-sm btn-outline-primary" onclick="redirect()">+ Add Product</button>

                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes:</label>
                        <textarea class="form-control" id="textarea" name="textarea"
                            placeholder="Additional notes, payment terms..." rows="3"></textarea>
                    </div>

                    <div class="text-end">
                        <p>Subtotal: ₹<span id="subtotal">0.00</span></p>
                        <p class="gst-section">GST (<span id="gstPercent">18</span>%): ₹<span id="gstAmount">0.00</span>
                        </p>
                        <h5>Total: ₹<span id="totalAmount">0.00</span></h5>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="closeInvoiceModal()">Cancel</button>
                    <button class="btn btn-primary" onclick="collectInvoiceData()">Create Invoice</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Invoice Form -->
    <div class="modal" id="salesModal" style="display: none;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <button type="button" class="btn-close"
                        onclick="document.getElementById('salesModal').style.display='none'"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Customer:</label>
                            <select class="form-select" id="salesCustomer" name="salesCustomer" required>
                                <option>Select customer</option>
                                <?php

                                // Fetch transactions from the database
                                $result = $conn->query("SELECT name FROM customer WHERE created_for = '$user_name' ORDER BY customer_Id DESC");

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option>" . $row['name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Payment Method:</label>
                            <select class="form-select" id="salesPaymentMethod" name="salesPaymentMethod" required>
                                <option>Select payment method</option>
                                <option>Digital payment</option>
                                <option>Cash</option>
                                <option>BNPL</option>
                                <option>Payment gateway</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Date:</label>
                            <input type="date" id="salesDate" name="salesDate" class="form-control" required>
                        </div>
                        <div class="col-md-4 gst-section">
                            <label class="form-label">Tax Rate:</label>
                            <select id="gsttaxRate" class="form-select" name="gsttaxRate"
                                onchange="calculateSalesTotals()">
                                <option value="5">GST 5%</option>
                                <option value="12">GST 12%</option>
                                <option value="18">GST 18%</option>
                                <option value="28">GST 28%</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered" id="salesItemTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Description</th>
                                    <th>Qty</th>
                                    <th>Price (₹)</th>
                                    <th>Total (₹)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <button class="btn btn-sm btn-outline-primary" onclick="addSalesItem()">+ Add Item</button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes:</label>
                        <textarea class="form-control" id="salestextarea" name="salestextarea"
                            placeholder="Additional notes, payment terms..." rows="3"></textarea>
                    </div>

                    <div class="text-end">
                        <p>Subtotal: ₹<span id="subTotal">0.00</span></p>
                        <p class="gst-section">GST (<span id="taxLabel">18%</span>): ₹<span id="gstTax">0.00</span></p>
                        <h5>Total: ₹<span id="grandTotal">0.00</span></h5>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="closeSalesModal()">Cancel</button>
                    <button class="btn btn-primary" onclick="collectSaleseData()">Create Invoice</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Generate Receiving Form -->
    <div class="modal fade" id="generateReceiving" tabindex="-1" aria-labelledby="generateReceivingLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="store_dashboard.php?page=billing" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="generateReceivingLabel">Generate Receiving</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="deliveryId" class="form-label">Delivery ID</label>
                            <input type="text" class="form-control" id="deliveryId" name="deliveryId" required>
                        </div>

                        <div class="mb-3">
                            <label for="trackingId" class="form-label">Tracking ID</label>
                            <input type="text" class="form-control" id="trackingId" name="trackingId" required>
                        </div>

                        <div class="mb-3">
                            <label for="requestId" class="form-label">Request ID</label>
                            <input type="text" class="form-control" id="requestId" name="requestId" required>
                        </div>

                        <div class="mb-3">
                            <label for="receivedDate" class="form-label">Date Received</label>
                            <input type="date" class="form-control" id="receivedDate" name="receivedDate" required>
                        </div>

                        <div class="mb-3">
                            <label for="Received_by" class="form-label">Received By</label>
                            <input type="text" class="form-control" id="Received_by" name="Received_by" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" name="whatAction"
                            value="generateReceiving">Generate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bank Deposit Entry Form -->
    <div class="modal fade" id="bankDepositEntry" tabindex="-1" aria-labelledby="bankDepositEntryLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="store_dashboard.php?page=billing" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bankDepositEntryLabel">Generate Receiving</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>

                        <div class="mb-3">
                            <label for="transferTO" class="form-label">Transfer To</label>
                            <input type="text" class="form-control" id="transferTO" name="transferTO" required>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" name="whatAction"
                            value="bankDepositEntry">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    $user_id = $_SESSION['uid'];

    $today = date("Y-m-d");
    $yesterday = date("Y-m-d", strtotime("-1 day"));

    // Opening Balance (Yesterday's Closing Balance)
    $sql1 = "SELECT opening_balance AS opening_balance 
         FROM retail_store_cash 
         WHERE user_id = ? AND date = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("is", $user_id, $yesterday);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $opening = $result1->fetch_assoc()['opening_balance'] ?? 0;

    // Cash Sales Today
    $sql2 = "SELECT SUM(grand_total) AS cash_sales 
         FROM invoice 
         WHERE created_for = ? AND date = ? AND payment_method = 'Cash'";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("is", $created_for, $today);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $cash_sales = $result2->fetch_assoc()['cash_sales'] ?? 0;

    // Cash Refunds Today
    $sql3 = "SELECT SUM(grand_total) AS cash_refund 
         FROM invoice 
         WHERE created_for = ? AND date = ? AND status = 'Refund'";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("is", $created_for, $today);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
    $cash_refund = $result3->fetch_assoc()['cash_refund'] ?? 0;

    // Cash Deposits Today
    $sql4 = "SELECT SUM(cash_deposit_amount) AS cash_deposit 
         FROM retail_store_cash 
         WHERE user_id = ? AND date = ?";
    $stmt4 = $conn->prepare($sql4);
    $stmt4->bind_param("is", $user_id, $today);
    $stmt4->execute();
    $result4 = $stmt4->get_result();
    $cash_deposit = $result4->fetch_assoc()['cash_deposit'] ?? 0;

    // Final Cash in Hand
    $current_cash_in_hand = $opening + $cash_sales - $cash_refund - $cash_deposit;
    ?>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="billingTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#invoices"
                type="button" role="tab" aria-controls="invoices" aria-selected="true">Invoices</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="quotations-tab" data-bs-toggle="tab" data-bs-target="#quotations" type="button"
                role="tab" aria-controls="quotations" aria-selected="false">Quotations</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="credit-notes-tab" data-bs-toggle="tab" data-bs-target="#credit-notes"
                type="button" role="tab" aria-controls="credit-notes" aria-selected="false">Credit Notes</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sales-returns-tab" data-bs-toggle="tab" data-bs-target="#sales-returns"
                type="button" role="tab" aria-controls="sales-returns" aria-selected="false">Sales Returns</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button"
                role="tab" aria-controls="payments" aria-selected="false">Payments</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="reports-tab" data-bs-toggle="tab" data-bs-target="#reports" type="button"
                role="tab" aria-controls="reports" aria-selected="false">Reports</button>
        </li>
    </ul>

    <div class="tab-content" id="billingTabContent">
        <!-- Invoices Tab -->
        <div class="tab-pane fade show active" id="invoices" role="tabpanel" aria-labelledby="invoices-tab">
            <!-- Search and Filters -->
            <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center mb-4">
                <div class="flex-grow-1">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0" id="searchInput"><i
                                class="fas fa-search"></i></span>
                        <input type="text" class="form-control border-start-0 table-search" data-table="invoice_table"
                            placeholder="Search..." />
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <div>
                        <button class="btn btn-outline-primary gst-filter me-2" data-type="with GST"
                            data-table="invoice_table">With
                            GST</button>
                    </div>
                    <div>
                        <button class="btn btn-outline-primary gst-filter me-2" data-type="without GST"
                            data-table="invoice_table">Without
                            GST</button>
                    </div>
                    <div>
                        <button class="btn btn-outline-danger reset-filters me-2" data-table="invoice_table">Remove
                            Filters</button>
                    </div>
                </div>
                <button type="button" class="btn btn-primary btn-sm" onclick="openInvoiceModal(event)" id="invoice">
                    <i class="fas fa-file-invoice me-1"></i> Create Invoice
                </button>
            </div>

            <script>


                document.addEventListener("DOMContentLoaded", () => {

                    // 🔍 Live Search Function
                    document.querySelectorAll(".table-search").forEach(input => {
                        input.addEventListener("input", () => {
                            const tableId = input.dataset.table;
                            const value = input.value.toLowerCase();
                            const rows = document.querySelectorAll(`#${tableId} tbody tr`);
                            rows.forEach(row => {
                                const text = row.textContent.toLowerCase();
                                row.style.display = text.includes(value) ? "" : "none";
                            });
                        });
                    });

                    // 🧾 GST Filter Buttons
                    document.querySelectorAll(".gst-filter").forEach(button => {
                        button.addEventListener("click", () => {
                            const type = button.dataset.type.toLowerCase();
                            const tableId = button.dataset.table;
                            const rows = document.querySelectorAll(`#${tableId} tbody tr`);
                            rows.forEach(row => {
                                const docType = row.children[6]?.innerText.trim().toLowerCase();
                                row.style.display = docType === type ? "" : "none";
                            });
                        });
                    });

                    // ❌ Remove Filters Button
                    document.querySelectorAll(".reset-filters").forEach(button => {
                        button.addEventListener("click", () => {
                            const tableId = button.dataset.table;
                            const rows = document.querySelectorAll(`#${tableId} tbody tr`);
                            rows.forEach(row => {
                                row.style.display = "";
                            });

                            // Also clear search inputs for that table
                            document.querySelectorAll(`.table-search[data-table='${tableId}']`).forEach(input => {
                                input.value = "";
                            });
                        });
                    });

                    // ✅ Filter Helper Function
                    function filterTable(tableId, conditionFn) {
                        const rows = document.querySelectorAll(`#${tableId} tbody tr`);
                        rows.forEach(row => {
                            row.style.display = conditionFn(row) ? "" : "none";
                        });
                    }
                });
            </script>

            <!-- Quick Actions -->
            <div class="row row-cols-1 row-cols-md-3 g-3 mb-4">
                <div class="col">
                    <button type="button"
                        class="btn btn-outline-primary w-100 h-100 py-4 d-flex flex-column align-items-center gap-2"
                        onclick="openInvoiceModal(event)" id="invoice">
                        <i class="fas fa-file-invoice fa-2x"></i>
                        <span>Create Store Invoice</span>
                    </button>
                </div>
                <div class="col">
                    <button type="submit"
                        class="btn btn-outline-primary w-100 h-100 py-4 d-flex flex-column align-items-center gap-2"
                        data-bs-toggle="modal" data-bs-target="#generateReceiving">
                        <i class="fas fa-receipt fa-2x"></i>
                        <span>Generate Receiving</span>
                    </button>
                </div>
                <div class="col">
                    <!-- <form method="POST" action="?page=billing"> -->
                    <!-- <input type="hidden" name="action" value="export_records"> -->
                    <button type="button" data-bs-toggle="modal" data-bs-target="#exportRecords"
                        class="btn btn-outline-primary w-100 h-100 py-4 d-flex flex-column align-items-center gap-2">
                        <i class="fas fa-download fa-2x"></i>
                        <span>Export Records</span>
                    </button>
                    <!-- </form> -->
                </div>
                <!-- <div class="col">
                    <form method="POST" action="?page=billing">
                        <input type="hidden" name="action" value="record_payment">
                        <button type="submit"
                            class="btn btn-outline-primary w-100 h-100 py-4 d-flex flex-column align-items-center gap-2">
                            <i class="fas fa-check-circle fa-2x"></i>
                            <span>Record Payment</span>
                        </button>
                    </form>
                </div> -->
            </div>

            <?php
            // Check if user has Delete permission
            $hasDeletePermission = false;
            $permissionSql = "SELECT Permission FROM user_management WHERE User_Name = '$user_name'";
            $permissionResult = $conn->query($permissionSql);
            if ($permissionResult->num_rows > 0) {
                $permissionRow = $permissionResult->fetch_assoc();
                $permissions = json_decode($permissionRow['Permission'], true);
                $hasDeletePermission = in_array('Delete', $permissions);
            }
            ?>

            <!-- Invoices Table -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Store Invoices</h5>
                        <div class="d-flex gap-2">
                            <form action="billing.php" method="get">
                                <button type="submit" name="loadData" value="<?php echo $loadDataValue ?>"
                                    class="btn btn-outline-primary btn-sm"><?php echo $loadDataText ?></button>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="invoice_table">
                            <thead>
                                <tr>
                                    <th>Invoice ID</th>
                                    <th>Sales ID</th>
                                    <th>Payment ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Due Date</th>
                                    <th>Document Type</th>
                                    <th>Tax Rate</th>
                                    <th>Items</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Notes</th>
                                    <th>GST Amount</th>
                                    <th>Grand Total</th>
                                    <th>Created By</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                // Fetch transactions from the database
                                $result = $conn->query($query);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['invoice_id']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['Sales_Id']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['payment_id']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                                        echo "<td>" . date('d-M-Y', strtotime($row['date'])) . "</td>";
                                        echo "<td>" . date('d-M-Y', strtotime($row['due_date'])) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['document_type']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['tax_rate']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['notes']) . "</td>";
                                        echo "<td>₹" . number_format($row['GST_amount'], 2) . "</td>";
                                        echo "<td>₹" . number_format($row['grand_total'], 2) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['created_by']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                        echo '<td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-eye"></i></button>
                                    <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-print"></i></button>';
                                        if ($hasDeletePermission && $row['status'] !== 'Refund'): ?>
                                            <form method="post" action=""
                                                onsubmit="return confirm('Are you sure you want to cancel this invoice?');">
                                                <input type="hidden" name="invoice_id"
                                                    value="<?php echo htmlspecialchars($row['invoice_id']); ?>">
                                                <button type="submit" name="cancelInvoice" class="btn btn-danger btn-sm">
                                                    <i class="fa-solid fa-xmark"></i> Cancel
                                                </button>
                                            </form>
                                        <?php endif;

                                        echo '</div>
                            </td>';
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='14' class='text-center'>No transactions found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancelInvoice']) && $hasDeletePermission) {
            $invoice_id = $conn->real_escape_string($_POST['invoice_id']);

            // 1. Get items & quantities from invoice
            $fetchSql = "SELECT item_name, quantity FROM invoice WHERE invoice_id = ? AND created_for = ?";
            $fetchStmt = $conn->prepare($fetchSql);
            $fetchStmt->bind_param("ss", $invoice_id, $user_name);
            $fetchStmt->execute();
            $fetchResult = $fetchStmt->get_result();
            $invoiceRow = $fetchResult->fetch_assoc();
            $fetchStmt->close();

            if ($invoiceRow) {

                // fallback if stored as comma separated
                $itemNames = explode(",", $invoiceRow['item_name']);
                $quantities = explode(",", $invoiceRow['quantity']);


                // 2. Update invoice table (grand_total negative & status refund)
                $sql = "UPDATE invoice 
                SET grand_total = -grand_total, status = 'Refund' 
                WHERE invoice_id = ? AND created_for = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $invoice_id, $user_name);

                if ($stmt->execute()) {
                    // 3. Add cancelled items back to stock
                    for ($i = 0; $i < count($itemNames); $i++) {
                        $item = trim($itemNames[$i]);
                        $qty = intval($quantities[$i]);

                        if ($item && $qty > 0) {
                            // Get latest stock_id for this item
                            $latestStockSql = "SELECT Id FROM retail_invetory 
                                       WHERE item_name = ? AND inventory_of = ? 
                                       ORDER BY last_updated DESC, Id DESC LIMIT 1";
                            $latestStockStmt = $conn->prepare($latestStockSql);
                            $latestStockStmt->bind_param("ss", $item, $user_name);
                            $latestStockStmt->execute();
                            $latestStockResult = $latestStockStmt->get_result();

                            if ($latestStockResult && $latestStockRow = $latestStockResult->fetch_assoc()) {
                                $latestStockId = $latestStockRow['Id'];
                                // Update only latest entry
                                $updateSql = "UPDATE retail_invetory SET stock = stock + ? WHERE Id = ?";
                                $updateStmt = $conn->prepare($updateSql);
                                $updateStmt->bind_param("is", $qty, $latestStockId);
                                $updateStmt->execute();
                                $updateStmt->close();
                            }
                            $latestStockStmt->close();
                        }
                    }

                    echo "<script>alert('Invoice cancelled successfully!'); window.location.href=window.location.href;</script>";
                } else {
                    echo "<script>alert('Error cancelling invoice: " . $conn->error . "');</script>";
                }

                $stmt->close();
            }
        }
        ?>

        <!-- Quotations Tab -->
        <div class="tab-pane fade" id="quotations" role="tabpanel" aria-labelledby="quotations-tab">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5>Manage Quotations</h5>
                <button type="button" class="btn btn-primary btn-sm" onclick="openInvoiceModal(event)" id="quotation">
                    <i class="fas fa-plus me-1"></i> Create Quotation
                </button>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Due Date</th>
                                    <th>Document Type</th>
                                    <th>Tax Rate</th>
                                    <th>Items</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Notes</th>
                                    <th>GST Amount</th>
                                    <th>Grand Total</th>
                                    <th>Payment Method</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                // Fetch transactions from the database
                                $result = $conn->query("SELECT * FROM quotation ORDER BY invoice_id DESC");

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['invoice_id']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                                        echo "<td>" . date('d-M-Y', strtotime($row['date'])) . "</td>";
                                        echo "<td>" . date('d-M-Y', strtotime($row['due_date'])) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['document_type']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['tax_rate']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['notes']) . "</td>";
                                        echo "<td>₹" . number_format($row['GST_amount'], 2) . "</td>";
                                        echo "<td>₹" . number_format($row['grand_total'], 2) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['payment_method']) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='13' class='text-center'>No transactions found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <script>
                            // Export table data to CSV
                            function exportTableToCSV(filename = 'table-data.csv') {
                                const rows = document.querySelectorAll("#supplyTable tr");
                                let csv = [];

                                rows.forEach(row => {
                                    let cols = Array.from(row.querySelectorAll("th, td"))
                                        .map(col => `"${col.innerText.trim()}"`);
                                    csv.push(cols.join(","));
                                });

                                // Create a Blob from the CSV string
                                let csvFile = new Blob([csv.join("\n")], { type: "text/csv" });

                                // Create a temporary link to trigger download
                                let downloadLink = document.createElement("a");
                                downloadLink.download = filename;
                                downloadLink.href = window.URL.createObjectURL(csvFile);
                                downloadLink.style.display = "none";
                                document.body.appendChild(downloadLink);

                                downloadLink.click();
                                document.body.removeChild(downloadLink);
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>

        <!-- Credit Notes Tab -->
        <div class="tab-pane fade" id="credit-notes" role="tabpanel" aria-labelledby="credit-notes-tab">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5>Manage Credit Notes</h5>
                <button type="button" class="btn btn-primary btn-sm" onclick="openSalesModal(event)" id="credit_note">
                    <i class="fas fa-plus me-1"></i> Issue Credit Note
                </button>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Tax Rate</th>
                                    <th>Items</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Notes</th>
                                    <th>GST Amount</th>
                                    <th>Grand Total</th>
                                    <th>Payment Method</th>
                                    <th>Created By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                // Fetch transactions from the database
                                $result = $conn->query("SELECT * FROM credit_note WHERE created_for = '$created_for' ORDER BY sales_return_id DESC");

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['sales_return_id']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                                        echo "<td>" . date('d-M-Y', strtotime($row['date'])) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['tax_rate']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['item']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['notes']) . "</td>";
                                        echo "<td>₹" . number_format($row['GST_amount'], 2) . "</td>";
                                        echo "<td>₹" . number_format($row['Grand_total'], 2) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['payment_method']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['created_by']) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='10' class='text-center'>No transactions found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Returns Tab -->
        <div class="tab-pane fade" id="sales-returns" role="tabpanel" aria-labelledby="sales-returns-tab">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5>Manage Sales Returns</h5>
                <button type="button" class="btn btn-primary btn-sm" onclick="openSalesModal(event)" id="sales_return">
                    <i class="fas fa-plus me-1"></i> Sales Return
                </button>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="supplyTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Tax Rate</th>
                                    <th>Items</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Notes</th>
                                    <th>GST Amount</th>
                                    <th>Grand Total</th>
                                    <th>Payment Method</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                // Fetch transactions from the database
                                $result = $conn->query("SELECT * FROM sales_return ORDER BY sales_return_id DESC");

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['sales_return_id']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                                        echo "<td>" . date('d-M-Y', strtotime($row['date'])) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['tax_rate']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['item']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['notes']) . "</td>";
                                        echo "<td>₹" . number_format($row['GST_amount'], 2) . "</td>";
                                        echo "<td>₹" . number_format($row['Grand_total'], 2) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['payment_method']) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='11' class='text-center'>No transactions found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <script>
                            // Export table data to CSV
                            function exportTableToCSV(filename = 'table-data.csv') {
                                const rows = document.querySelectorAll("#supplyTable tr");
                                let csv = [];

                                rows.forEach(row => {
                                    let cols = Array.from(row.querySelectorAll("th, td"))
                                        .map(col => `"${col.innerText.trim()}"`);
                                    csv.push(cols.join(","));
                                });

                                // Create a Blob from the CSV string
                                let csvFile = new Blob([csv.join("\n")], { type: "text/csv" });

                                // Create a temporary link to trigger download
                                let downloadLink = document.createElement("a");
                                downloadLink.download = filename;
                                downloadLink.href = window.URL.createObjectURL(csvFile);
                                downloadLink.style.display = "none";
                                document.body.appendChild(downloadLink);

                                downloadLink.click();
                                document.body.removeChild(downloadLink);
                            }

                            // Refresh Button (Reload page)
                            document.getElementById('refreshBtn').addEventListener('click', function () {
                                location.reload();
                            });

                        </script>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments Tab -->
        <div class="tab-pane fade" id="payments" role="tabpanel" aria-labelledby="payments-tab">
            <div class="mb-4">
                <h5>Payment Methods</h5>
                <div class="row row-cols-1 row-cols-md-4 g-3">
                    <div class="col">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-credit-card text-primary me-2"></i> Payment
                                    Gateway</h6>
                                <p class="text-muted small">Accept credit/debit cards via payment gateway</p>
                                <button type="button" class="btn btn-outline-primary btn-sm">Configure</button>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-mobile text-primary me-2"></i> Digital Payment
                                </h6>
                                <p class="text-muted small">UPI, mobile wallets and other digital options</p>
                                <button type="button" class="btn btn-outline-primary btn-sm">Configure</button>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-money-bill text-primary me-2"></i> Cash in Hand
                                </h6>
                                <p class="text-muted small">Track cash payments and manage cash drawer</p>
                                <button type="button" class="btn btn-outline-primary btn-sm"
                                    onclick="exportTableToCSV()">Generate Report</button>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-receipt text-primary me-2"></i> Payment Reports
                                </h6>
                                <p class="text-muted small">Generate reports on all payment methods</p>
                                <button type="button" class="btn btn-outline-primary btn-sm">View Reports</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Recent Transactions</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="table">
                            <thead>
                                <tr>
                                    <th>Payment ID</th>
                                    <th>Invoice ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Fetch transactions from the database
                                $result = $conn->query("SELECT * FROM invoice WHERE created_for = '$created_for' ORDER BY invoice_id DESC");

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['payment_id']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['invoice_id']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                                        echo "<td>" . date('d-M-Y', strtotime($row['date'])) . "</td>";
                                        echo "<td>₹" . number_format($row['grand_total'], 2) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['payment_method']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center'>No transactions found</td></tr>";
                                }
                                ?>
                            </tbody>
                            <script>
                                function exportTableToCSV(filename = 'export.csv') {
                                    const table = document.getElementById("table");
                                    const rows = table.querySelectorAll("tbody tr");

                                    if (rows.length === 0) {
                                        alert("No data found in the table.");
                                        return;
                                    }

                                    let csv = [];
                                    const headers = table.querySelectorAll("thead th");
                                    let headerRow = [];
                                    headers.forEach(th => headerRow.push('"' + th.innerText.trim() + '"'));
                                    csv.push(headerRow.join(","));

                                    rows.forEach(row => {
                                        let rowData = [];
                                        row.querySelectorAll("td").forEach(td => {
                                            rowData.push('"' + td.innerText.trim().replace(/"/g, '""') + '"');
                                        });
                                        csv.push(rowData.join(","));
                                    });

                                    // Create and download CSV file
                                    let csvBlob = new Blob([csv.join("\n")], { type: "text/csv" });
                                    let url = URL.createObjectURL(csvBlob);
                                    let a = document.createElement("a");
                                    a.href = url;
                                    a.download = filename;
                                    a.click();
                                    URL.revokeObjectURL(url);
                                }
                            </script>

                        </table>

                    </div>
                </div>
            </div>
        </div>

        <!-- Reports Tab -->
        <div class="tab-pane fade" id="reports" role="tabpanel" aria-labelledby="reports-tab">
            <div class="mb-4">
                <h5>Billing Reports</h5>
                <div class="row row-cols-1 row-cols-md-3 g-3">
                    <div class="col">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title">Sales Summary</h6>
                                <p class="text-muted small">Overview of all sales transactions</p>
                                <button type="button" class="btn btn-outline-primary btn-sm"
                                    onclick="exportTableToCSV()">Generate Report</button>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title">Payment Analysis</h6>
                                <p class="text-muted small">Analysis of payment methods used</p>
                                <button type="button" class="btn btn-outline-primary btn-sm"
                                    onclick="exportTableToCSV('export.csv')">Generate Report</button>

                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title">Cash Flow Report</h6>
                                <p class="text-muted small">Track cash in hand and cash flow</p>
                                <button type="button" class="btn btn-outline-primary btn-sm"
                                    onclick="exportTableToCSV()">Generate Report</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5>Cash in Hand Report</h5>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#bankDepositEntry">Bank Deposit Entry
                        </button>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between p-3 bg-light rounded mb-2">
                            <span class="fw-bold">Opening Balance (Today)</span>
                            <span class="fw-bold">₹<?= number_format($opening) ?></span>
                        </div>
                        <div class="d-flex justify-content-between p-3 bg-light rounded mb-2">
                            <span class="fw-bold">Cash Sales</span>
                            <span class="text-success">+₹<?= number_format($cash_sales) ?></span>
                        </div>
                        <div class="d-flex justify-content-between p-3 bg-light rounded mb-2">
                            <span class="fw-bold">Cash Refunds</span>
                            <span class="text-danger">-₹<?= number_format($cash_refund) ?></span>
                        </div>
                        <div class="d-flex justify-content-between p-3 bg-light rounded mb-2">
                            <span class="fw-bold">Cash Deposits to Bank</span>
                            <span class="text-danger">-₹<?= number_format($cash_deposit) ?></span>
                        </div>
                        <div class="d-flex justify-content-between p-3 bg-light rounded border border-primary">
                            <span class="fw-bold">Current Cash in Hand</span>
                            <span class="fw-bold text-lg">₹<?= number_format($current_cash_in_hand) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php

    // Get current date
    $today = date("Y-m-d");

    // Query to count overdue invoices
    $sql = "SELECT COUNT(*) AS overdue_count FROM invoice WHERE due_date < ? AND created_for = '$created_for'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $overdueCount = $row['overdue_count'];

    $stmt->close();
    ?>

    <?php

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT COUNT(*) as overdue_count 
        FROM invoice 
        WHERE created_for = ? 
        AND due_date IS NOT NULL 
        AND STR_TO_DATE(due_date, '%Y-%m-%d') < CURDATE()");
    $stmt->bind_param("s", $user_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $overdueCount = $row['overdue_count'];
    ?>


    <!-- Payment Reminders & Collections -->
    <div class="row mt-4">
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm bg-amber-50 border-amber-200">
                <div class="card-body">
                    <h5 class="card-title text-amber-800"><i class="fas fa-exclamation-circle me-2"></i> Payment
                        Reminders</h5>
                    <p class="text-amber-700 mb-3">
                        <?php
                        if ($overdueCount > 0) {
                            echo "$overdueCount store invoices are overdue and require immediate attention.";
                        } else {
                            echo "No overdue invoices at the moment.";
                        }
                        ?>
                    </p>

                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .bg-amber-50 {
        background-color: #fefae8;
    }

    .border-amber-200 {
        border-color: #fde68a;
    }

    .text-amber-700 {
        color: #b45309;
    }

    .text-amber-800 {
        color: #92400e;
    }

    .bg-blue-50 {
        background-color: #eff6ff;
    }

    .border-blue-200 {
        border-color: #93c5fd;
    }

    .text-blue-700 {
        color: #1e40af;
    }

    .text-blue-800 {
        color: #1e3a8a;
    }

    .text-blue {
        color: #0d6efd;
    }

    .text-green {
        color: #198754;
    }

    .text-purple {
        color: #6f42c1;
    }

    .text-amber {
        color: #f59e0b;
    }

    .badge {
        font-size: 0.85rem;
        padding: 4px 8px;
    }

    .modal-content {
        border-radius: 0.5rem;
    }

    .gst-section {
        display: block;
    }

    #itemTable input {
        width: 100px;
    }

    .text-end {
        text-align: right;
    }

    textarea {
        width: 100%;
        height: 60px;
        margin-top: 10px;
    }

    .bill-modal {
        position: fixed;
        z-index: 1050;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .bill-modal-dialog {
        margin: 5% auto;
        max-width: 800px;
    }
</style>

<script>

    function submitExport() {
        const form = document.getElementById('exportForm');
        const formData = new FormData(form);
        const startDate = formData.get('start_date');
        const endDate = formData.get('end_date');

        // Validate date range
        if (!startDate || !endDate) {
            alert('Please select both start and end dates.');
            return;
        }
        if (new Date(startDate) > new Date(endDate)) {
            alert('Start date cannot be later than end date.');
            return;
        }

        const data = {
            whatAction: 'exportRecords',
            export_format: formData.get('export_format'),
            start_date: startDate,
            end_date: endDate
        };

        fetch('billing.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(data => {
                console.log(data)
                if (!data.success) {
                    alert(data.message);
                    window.location.href = 'store_dashboard.php?page=billing';
                    return;
                }

                const exportFormat = formData.get('export_format');
                const exportData = data;

                if (exportFormat === 'csv') {
                    // Generate CSV
                    let csvContent = exportData.headers.join(',') + '\n';
                    exportData.rows.forEach(row => {
                        let rowData = exportData.headers.map(header => {
                            let cell = row[header] || '';
                            cell = cell.toString().replace(/"/g, '""');
                            return `"${cell}"`;
                        });
                        csvContent += rowData.join(',') + '\n';
                    });

                    const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.setAttribute('download', 'invoice_report.csv');
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } else {
                    // Generate PDF with jsPDF and autoTable
                    const script = document.createElement('script');
                    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
                    const autoTableScript = document.createElement('script');
                    autoTableScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js';
                    autoTableScript.onload = function () {
                        const { jsPDF } = window.jspdf;
                        const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
                        doc.setFontSize(16);
                        doc.text('Invoice Report', 14, 15);
                        doc.setFontSize(12);
                        doc.text(`Period: ${exportData.start_date} to ${exportData.end_date}`, 14, 25);
                        doc.autoTable({
                            head: [exportData.headers.map(h => h.toUpperCase())],
                            body: exportData.rows.map(row => exportData.headers.map(h => row[h] || '')),
                            startY: 35,
                            theme: 'grid',
                            styles: { fontSize: 8, cellPadding: 2 },
                            headStyles: { fillColor: [0, 123, 255], textColor: [255, 255, 255] },
                            alternateRowStyles: { fillColor: [240, 240, 240] }
                        });
                        doc.save('invoice_report.pdf');
                    };
                    script.onload = function () { document.head.appendChild(autoTableScript); };
                    document.head.appendChild(script);
                }

                // Close modal and redirect
                const modal = bootstrap.Modal.getInstance(document.getElementById('exportRecords'));
                modal.hide();
                setTimeout(() => {
                    window.location.href = 'store_dashboard.php?page=billing';
                }, 1000);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Export failed. Please try again.');
            });
    }
    let activeInvoiceButtonId = null;

    let activeSalesButtonId = null;

    // To open form
    function openInvoiceModal(event) {
        activeInvoiceButtonId = event.target.id; // To store clicked button ID

        const modal = document.getElementById('invoiceModal');
        modal.style.display = 'block';
        modal.classList.add('show');

        const status = document.getElementById('status_section');
        if (activeInvoiceButtonId === 'invoice') {
            status.style.display = 'block';
            status.classList.add('show');
        } else {
            status.style.display = 'none';
            status.classList.remove('show');
        }
        if (document.querySelectorAll("#itemTable tbody tr").length === 0) {
            addItem();
        }

    }

    // To close form
    function closeInvoiceModal() {
        const modal = document.getElementById('invoiceModal');
        modal.style.display = 'none';
        modal.classList.remove('show');

        document.querySelector('#itemTable tbody').innerHTML = '';
        activeInvoiceButtonId = null;
        updateTotals();
    }

    // For add item row
    function addItem() {
        const tbody = document.querySelector("#itemTable tbody");
        const tr = document.createElement("tr");
        tr.innerHTML = `
        <td>
            <select onchange="updateTotals()">
                <option value="">Select Product</option>
                <?php

                // Fetch transactions from the database
                $result = $conn->query("SELECT item_name FROM retail_invetory  WHERE inventory_of = '$user_name'");

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option>" . $row['item_name'] . "</option>";
                    }
                }
                ?>
            </select>
        </td>
        <td><input placeholder="Description"></td>
        <td><input type="number" value="1" min="1" oninput="updateTotals()"></td>
        <td><input type="number" value="0" step="0.01" oninput="updateTotals()" class="price"></td>
        <td class="itemTotal">₹0.00</td>
        <td><button class="btn btn-sm btn-outline-danger" onclick="removeItem(this)">Delete</button></td>
    `;
        tbody.appendChild(tr);
        updateTotals();
    }

    // To remove item row
    function removeItem(btn) {
        btn.closest("tr").remove();
        updateTotals();
    }

    // For GST 
    function toggleGST() {
        const withGST = document.querySelector('input[name="docType"]:checked').value === 'withGST';
        document.querySelectorAll(".gst-section").forEach(el => {
            el.style.display = withGST ? 'block' : 'none';
        });
        updateTotals();
    }

    // For calculate total amount
    function updateTotals() {
        let subtotal = 0;
        document.querySelectorAll("#itemTable tbody tr").forEach(row => {
            const qty = parseFloat(row.children[2].querySelector('input').value || 0);
            const price = parseFloat(row.children[3].querySelector('input').value || 0);
            const total = qty * price;
            subtotal += total;
            row.children[4].innerText = "₹" + total.toFixed(2);
        });

        const taxRate = parseFloat(document.getElementById('taxRate')?.value || 0);
        const gstEnabled = document.querySelector('input[name="docType"]:checked').value === 'withGST';
        const gstAmount = gstEnabled ? (subtotal * taxRate / 100) : 0;

        document.getElementById('subtotal').innerText = subtotal.toFixed(2);
        document.getElementById('gstPercent').innerText = taxRate;
        document.getElementById('gstAmount').innerText = gstAmount.toFixed(2);
        document.getElementById('totalAmount').innerText = (subtotal + gstAmount).toFixed(2);
    }

    // Close form when clicking outside of it
    window.onclick = function (event) {
        const modal = document.getElementById('invoiceModal');
        if (event.target === modal) {
            closeInvoiceModal();
        }
    };

    function collectInvoiceData() {
        let item_names = [],
            descriptions = [],
            quantities = [],
            prices = [],
            totals = [];

        document.querySelectorAll("#itemTable tbody tr").forEach(row => {
            item_names.push(row.children[0].querySelector("select").value);
            descriptions.push(row.children[1].querySelector("input").value);
            let qty = row.children[2].querySelector("input").value;
            let price = row.children[3].querySelector("input").value;
            quantities.push(qty);
            prices.push(price);
            totals.push((qty * price).toFixed(2));
        });

        const selectedRadio = document.querySelector('input[name="docType"]:checked');
        if (!selectedRadio) {
            alert("Please select document type (With GST / Without GST)");
            return;
        }
        const document_type = selectedRadio.value;
        const gstEnabled = document_type === 'withGST';

        const data = {
            table: activeInvoiceButtonId,
            customer_name: document.getElementById("customer").value,
            payment_method: document.getElementById("invoicePaymentMethod").value,
            status: document.getElementById("invoiceStatus").value,
            document_type: gstEnabled ? "with GST" : "without GST",
            date: document.getElementById("invoiceDate").value,
            due_date: document.getElementById("dueDate").value,
            tax_rate: gstEnabled ? document.getElementById("taxRate").value : 0,
            notes: document.getElementById("textarea").value,
            subtotal: document.getElementById("subtotal").innerText,
            GST_amount: gstEnabled ? document.getElementById("gstAmount").innerText : 0,
            grand_total: document.getElementById("totalAmount").innerText,
            item_names: item_names.join(","),
            descriptions: descriptions.join(","),
            quantities: quantities.join(","),
            prices: prices.join(","),
            totals: totals.join(","),
            whatAction: "createInvoice",
        };

        fetch("billing.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        })
            .then(res => res.text())
            .then(msg => {
                // alert(msg);
                // console.log(msg);
                activeInvoiceButtonId = null;
                location.reload();
            })
            .catch(err => alert("Error submitting invoice."));
    }

    // To open sales form
    function openSalesModal(event) {
        activeSalesButtonId = event.target.id; // To store clicked button ID

        document.getElementById('salesModal').style.display = 'block';

        const tbody = document.querySelector('#salesItemTable tbody');
        if (tbody.children.length === 0) {
            addSalesItem();
        }
    }

    function addSalesItem() {
        const tbody = document.querySelector('#salesItemTable tbody');
        const row = document.createElement('tr');
        row.innerHTML = `
        <td>
            <select onchange="calculateSalesTotals()">
                <option value="">Select Product</option>
                <?php

                // Fetch transactions from the database
                $result = $conn->query("SELECT item_name FROM retail_invetory  WHERE inventory_of = '$user_name'");

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option>" . $row['item_name'] . "</option>";
                    }
                }
                ?>
            </select>
        </td>
        <td><input type="text" placeholder="Description"></td>
        <td><input type="number" class="qty" value="1" min="1" oninput="calculateSalesTotals()"></td>
        <td><input type="number" class="price" value="0" min="0" oninput="calculateSalesTotals()" class="price"></td>
        <td class="itemTotal">₹0.00</td>
        <td><button class="btn btn-sm btn-outline-danger" onclick="deleteSalesRow(this)">Delete</button></td>
        `;
        tbody.appendChild(row);
        calculateSalesTotals();
    }

    function closeSalesModal() {
        document.getElementById('salesModal').style.display = 'none';
        document.querySelector('#itemTable tbody').innerHTML = '';
        activeSalesButtonId = null;
        calculateSalesTotals();
    }

    function deleteSalesRow(btn) {
        btn.closest('tr').remove();
        calculateSalesTotals();
    }

    function calculateSalesTotals() {
        let subTotal = 0;
        const rows = document.querySelectorAll('#salesItemTable tbody tr');

        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.qty')?.value || 0);
            const price = parseFloat(row.querySelector('.price')?.value || 0);
            const total = qty * price;
            row.querySelector('.itemTotal').textContent = `₹${total.toFixed(2)}`;
            subTotal += total;
        });

        const gsttaxRate = parseFloat(document.getElementById('gsttaxRate').value || 0);
        const gst = subTotal * (gsttaxRate / 100);
        const totalWithTax = subTotal + gst;

        document.getElementById('subTotal').textContent = subTotal.toFixed(2);
        document.getElementById('gstTax').textContent = gst.toFixed(2);
        document.getElementById('grandTotal').textContent = totalWithTax.toFixed(2);
        document.getElementById('taxLabel').textContent = `${gsttaxRate}%`;
    }

    function collectSaleseData() {
        let item_names = [],
            descriptions = [],
            quantities = [],
            prices = [],
            totals = [];

        document.querySelectorAll("#salesItemTable tbody tr").forEach(row => {
            item_names.push(row.children[0].querySelector("select").value);
            descriptions.push(row.children[1].querySelector("input").value);
            let qty = row.children[2].querySelector("input").value;
            let price = row.children[3].querySelector("input").value;
            quantities.push(qty);
            prices.push(price);
            totals.push((qty * price).toFixed(2));
        });

        const data = {
            table: activeSalesButtonId,
            customer_name: document.getElementById("salesCustomer").value,
            payment_method: document.getElementById("salesPaymentMethod").value,
            date: document.getElementById("salesDate").value,
            tax_rate: document.getElementById("taxRate").value,
            notes: document.getElementById("salestextarea").value,
            subtotal: document.getElementById("subtotal").innerText,
            GST_amount: document.getElementById("gstTax").innerText,
            grand_total: document.getElementById("grandTotal").innerText,
            item_names: item_names.join(","),
            descriptions: descriptions.join(","),
            quantities: quantities.join(","),
            prices: prices.join(","),
            totals: totals.join(","),
            whatAction: "createSales",
        };

        fetch("billing.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        })
            .then(res => res.text())
            .then(msg => {
                // alert(msg);
                // console.log(msg);
                activeSalesButtonId = null;
                location.reload();
            })
            .catch(err => alert("Error submitting invoice."));
    }
    // redirect form
    function redirect() {
        window.location.href = "store_dashboard.php?page=inventory"
    }


</script>