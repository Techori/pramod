<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../_conn.php';

$user_name = $_SESSION['user_name'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    // Optional fallback if $data is still null (e.g., for form submissions)
    if (!$data) {
        $data = $_POST; // fallback to regular POST form
    }

    $whatAction = isset($data['whatAction']) ? $data['whatAction'] : null;

    if ($whatAction === 'createInvoice') {
        $table = $data['table'];
        $created_for = $data['created_for'];

        $docType = $data['document_type'];
        if ($table === 'invoice') {
            $prefix = ($docType === 'with GST') ? 'INV' : 'INVWO';
        } else if ($table === 'quotation') {
            $prefix = ($docType === 'with GST') ? 'QT' : 'QTWO';
        } else if ($table === 'proforma') {
            $prefix = ($docType === 'with GST') ? 'PI' : 'PIWO';
        } else if ($table === 'purchase_order') {
            $prefix = ($docType === 'with GST') ? 'PO' : 'POWO';
        }

        $currentYear = date("Y");

        if ($table === 'invoice') {

            // Fetch latest invoice ID for the current document type and current or previous year
            $query = "SELECT invoice_id FROM $table WHERE invoice_id LIKE '$prefix-%' AND created_for = '$created_for' ORDER BY invoice_id DESC LIMIT 1";
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

            // // Get negative stock setting (default = 0)
            // $allowNegativeStock = 0;
            // $settingsResult = $conn->query("SELECT value FROM settings WHERE name = 'negative_stock' LIMIT 1");

            // if ($settingsResult && $row = $settingsResult->fetch_assoc()) {
            //     $allowNegativeStock = (int) $row['value'];
            // }

            // Prepare item arrays
            $itemNames = explode(',', $data['item_names']);
            $quantities = explode(',', $data['quantities']);

            // Validate stock before inserting invoice
            for ($i = 0; $i < count($itemNames); $i++) {
                $item = trim($itemNames[$i]);
                $qty = (int) trim($quantities[$i]);

                $stockResult = $conn->query("SELECT Stock FROM inventory WHERE Product_Name = '$item' LIMIT 1");

                if ($stockResult && $stockRow = $stockResult->fetch_assoc()) {
                    $currentStock = (int) $stockRow['Stock'];
                    if ($currentStock - $qty < 0) {
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
            $result = $conn->query("SELECT payment_id FROM invoice WHERE created_for = '$created_for' ORDER BY CAST(SUBSTRING(payment_id, 5) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");

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
                $data['created_for'],
                $data['status']

            );

            // Subtract sold quantity from inventory
            for ($i = 0; $i < count($itemNames); $i++) {
                $item = trim($itemNames[$i]);
                $qty = (int) trim($quantities[$i]);

                $updateInventory = $conn->query("UPDATE inventory SET Stock = Stock - $qty WHERE Product_Name = '$item'");
                $updateProduct = $conn->query("UPDATE products SET stock_quantity = stock_quantity - $qty WHERE name = '$item'");
            }
        } else {

            // Fetch latest invoice ID for the current document type and current or previous year
            $query = "SELECT invoice_id FROM $table WHERE invoice_id LIKE '$prefix-%' ORDER BY invoice_id DESC LIMIT 1";
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

            // Prepare and insert
            $stmt = $conn->prepare("INSERT INTO $table (
            invoice_id, customer_name, document_type, date, due_date, tax_rate, notes, subtotal, GST_amount, grand_total,
            item_name, description, quantity, price, total
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param(
                "sssssssdddsssss",
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
                $data['totals']
            );
        }

        if ($stmt->execute()) {
            echo "Invoice inserted successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else if ($whatAction === 'createSales') {
        $table = $data['table'];

        if ($table === 'credit_note') {
            $prefix = 'CN';
        } else if ($table === 'sales_return') {
            $prefix = 'SR';
        } else if ($table === 'delivery_challan') {
            $prefix = 'DC';
        } else if ($table === 'auto_bill') {
            $prefix = 'AB';
        } else if ($table === 'counter_purchase') {
            $prefix = 'CP';
        } else if ($table === 'payment_out') {
            $prefix = 'PO';
        } else if ($table === 'purchase_return') {
            $prefix = 'PR';
        } else if ($table === 'debit_note') {
            $prefix = 'DN';
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
            item, description, quantity, price, total
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "sssssdddsssss",
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
            $data['totals']
        );

        if ($stmt->execute()) {
            echo "Invoice inserted successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}



?>


<style>
    .cards {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
        height: 100%;
    }

    .cards:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.1);
    }

    .card-border {
        border-radius: 0.5rem;
        border-top: none;
        border-right: none;
        border-bottom: none;
    }

    .chart-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
    }

    .chart-box {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        width: 100%;
        max-width: 600px;
        flex: 1 1 300px;
    }

    h3 {
        margin-bottom: 15px;
    }

    canvas {
        width: 100% !important;
        height: auto !important;
    }

    .tabs {
        display: flex;
        border-bottom: 2px solid #ddd;
    }

    .billingTab {
        padding: 10px 20px;
        cursor: pointer;
        border: none;
        background: none;
        font-size: 16px;
    }

    .billingTab.active {
        border-bottom: 3px solid #007bff;
        font-weight: bold;
        color: #007bff;
    }

    .billing-tab-content {
        display: none;
        padding: 20px 0;
    }

    .billing-tab-content.active {
        display: block;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th,
    td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
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

<h1>Billing Dashboard</h1>
<p>Complete billing desk for invoices, bills, and payments</p>


<?php

// Fetch Pending Invoices
$pending = $conn->query("SELECT COUNT(*) AS count, IFNULL(SUM(grand_total), 0) AS total FROM invoice WHERE status = 'Pending'")->fetch_assoc();

// Monthly Revenue
$currentMonth = date('m');
$currentYear = date('Y');
$revenue = $conn->query("SELECT IFNULL(SUM(grand_total), 0) AS total FROM invoice WHERE MONTH(date) = $currentMonth AND YEAR(date) = $currentYear")->fetch_assoc();

// Last Month Revenue for % comparison
$lastMonth = date('m', strtotime('-1 month'));
$lastRevenue = $conn->query("SELECT IFNULL(SUM(grand_total), 0) AS total FROM invoice WHERE MONTH(date) = $lastMonth AND YEAR(date) = $currentYear")->fetch_assoc();
$revenueChange = ($lastRevenue['total'] > 0) ? (($revenue['total'] - $lastRevenue['total']) / $lastRevenue['total']) * 100 : 0;

// Purchase Orders
$po = $conn->query("SELECT COUNT(*) AS count, IFNULL(SUM(grand_total), 0) AS total FROM purchase_order_bill WHERE MONTH(date) = $currentMonth AND YEAR(date) = $currentYear")->fetch_assoc();

// Returns
$returns = $conn->query("SELECT COUNT(*) AS count, IFNULL(SUM(Grand_total), 0) AS total FROM sales_return WHERE MONTH(date) = $currentMonth AND YEAR(date) = $currentYear")->fetch_assoc();
?>

<!-- Cards -->
<div class="row">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
            <div class="card-body">
                <h6 class="text-muted">Pending Invoices</h6>
                <h3 class="fw-bold">₹<?= number_format($pending['total']) ?></h3>
                <p><?= $pending['count'] ?> invoices pending</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
            <div class="card-body">
                <h6 class="text-muted">Month's Revenue</h6>
                <h3 class="fw-bold">₹<?= number_format($revenue['total']) ?></h3>
                <p class="<?= $revenueChange < 0 ? 'text-danger' : 'text-success' ?>">
                    <?= round($revenueChange, 2) ?>% vs last month
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
            <div class="card-body">
                <h6 class="text-muted">Purchase Orders</h6>
                <h3 class="fw-bold">₹<?= number_format($po['total']) ?></h3>
                <p><?= $po['count'] ?> orders this month</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
            <div class="card-body">
                <h6 class="text-muted">Sales Return</h6>
                <h3 class="fw-bold">₹<?= number_format($returns['total']) ?></h3>
                <p><?= $returns['count'] ?> returns</p>
            </div>
        </div>
    </div>
</div>

<!-- Buttons -->
<div class="row justify-content-center">
    <div class="col-md-4 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100" onclick="openInvoiceModal(event)"
            id="invoice"><i class="fa-solid fa-file"></i> Create
            New Invoice</button>
    </div>
    <div class="col-md-4 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100" onclick="openSalesModal(event)"
            id="credit_note"><i class="fa-solid fa-file-export"></i> Issue
            Credit Note</button>
    </div>
    <div class="col-md-4 col-sm-6 mb-4">
        <a href="?page=reports" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-clipboard-list"></i>
            Generate Report</a>
    </div>
</div>

<!-- Charts -->
<div class="chart-container">
    <div class="chart-box">
        <h3>Monthly Billing Count</h3>
        <canvas id="barChart"></canvas>
    </div>
    <div class="chart-box">
        <h3>Payment Methods</h3>
        <div style="position: relative; width: 100%; max-width: 300px; margin: 0 auto;">
            <canvas id="pieChart"></canvas>
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
                    <div class="col-md-6" id="customer_section">
                        <label class="form-label">Customer:</label>
                        <select class="form-select" id="customer" name="customer" required>
                            <option>Select customer</option>
                            <?php

                            // Fetch transactions from the database
                            $result = $conn->query("SELECT name FROM customer ORDER BY customer_Id DESC");

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option>" . $row['name'] . "</option>";
                                }
                            }
                            ?>
                        </select>

                        <!-- Add Customer Button -->
                        <button id="showFormBtn" class="btn btn-primary my-2">+ Add Customer</button>
                    </div>


                    <!-- Hidden Customer Form -->
                    <div id="customerForm" class="card p-3 mb-4" style="display: none;">
                        <form method="POST" action="save_customer.php">
                            <div class="mb-3">
                                <label class="form-label">Create for:</label>
                                <select class="form-select" id="created_for" name="created_for" required>
                                    <option>Select status</option>
                                    <?php

                                    // Fetch transactions from the database
                                    $result = $conn->query("SELECT user_name FROM users");

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option>" . $row['user_name'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

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
                                <input type="text" name="customer_phone" class="form-control" required maxlength="10">
                            </div>
                            <input type="submit" value="Save Customer" class="btn btn-success text-white"
                                name="whatAction">
                        </form>
                    </div>

                    <!-- JS to Toggle Form -->
                    <script>
                        document.getElementById("showFormBtn").addEventListener("click", function () {
                            const form = document.getElementById("customerForm");
                            form.style.display = (form.style.display === "none") ? "block" : "none";
                        });
                    </script>



                    <div class="col-md-6" id="vendor_section">
                        <label class="form-label">Vendor:</label>
                        <select class="form-select" id="vendor" name="vendor" required>
                            <option>Select vendor</option>
                            <?php

                            // Fetch transactions from the database
                            $result = $conn->query("SELECT user_name FROM users WHERE user_type = 'Vendor'");

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option>" . $row['user_name'] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Payment Method:</label>
                        <select class="form-select" id="invoicePaymentMethod" name="invoicePaymentMethod" required>
                            <option>Select payment method</option>
                            <option>Digital payment</option>
                            <option>Cash</option>
                            <option id="bnpl">BNPL</option>
                            <option>Payment gateway</option>
                        </select>
                    </div>
                    <!-- BNPL -->
                    <div id="bnplFields" style="display: none;">
                        <div class="mb-3">
                            <label for="interest_rate" class="form-label">Interest Rate %</label>
                            <input type="number" id="interest_rate" class="form-control" name="interest_rate">
                        </div>
                    </div>
                    <!-- JS TO SHOW -->
                    <script>
                        const paymentMethod = document.getElementById("invoicePaymentMethod")
                        const bnplFields = document.getElementById("bnplFields")

                        paymentMethod.addEventListener("change", function () {
                            if (this.value === "BNPL") {
                                bnplFields.style.display = "block"
                            }
                            else {
                                bnplFields.style.display = "none"
                            }

                        }
                        )

                    </script>


                    <div class="col-md-6" id="status_section">
                        <label class="form-label">Status:</label>
                        <select class="form-select" id="invoiceStatus" name="invoiceStatus" required>
                            <option>Select status</option>
                            <option>Completed</option>
                            <option>Pending</option>
                            <option>Refund</option>
                        </select>
                    </div>
                    <div class="col-md-6" id="createdFor_section">
                        <label class="form-label">Create for:</label>
                        <select class="form-select" id="created_for" name="created_for" required>
                            <option>Select status</option>
                            <?php

                            // Fetch transactions from the database
                            $result = $conn->query("SELECT user_name FROM users");

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option>" . $row['user_name'] . "</option>";
                                }
                            }
                            ?>
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
                        <input type="date" id="invoiceDate" name="invoiceDate" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Due Date:</label>
                        <input type="date" id="dueDate" name="dueDate" class="form-control">
                    </div>
                    <div class="col-md-4 gst-section">
                        <label class="form-label">Tax Rate:</label>
                        <select id="taxRate" class="form-select" name="taxRate" onchange="updateTotals()">
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
                    <button class="btn btn-sm btn-outline-primary mb-2" onclick="addItem()">+ Add Item</button>
                    <button class="btn btn-sm btn-outline-primary mb-2" onclick="redirect()">+ Add prouct</button>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes:</label>
                    <textarea class="form-control" id="textarea" name="textarea"
                        placeholder="Additional notes, payment terms..." rows="3"></textarea>
                </div>

                <div class="text-end">
                    <p>Subtotal: ₹<span id="subtotal">0.00</span></p>
                    <p class="gst-section">GST (<span id="gstPercent">18</span>%): ₹<span id="gstAmount">0.00</span></p>
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
                <div class="mb-3">
                    <div id="sales_customer_section">
                        <label class="form-label">Customer:</label>
                        <select class="form-select" id="salesCustomer" name="salesCustomer" required>
                            <option>Select customer</option>
                            <?php

                            // Fetch transactions from the database
                            $result = $conn->query("SELECT name FROM customer ORDER BY customer_Id DESC");

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option>" . $row['name'] . "</option>";
                                }
                            }
                            ?>
                        </select>
                        <button class="btn bg-primary text-white mt-2" type="submit" id="salesFromBtn">+ Add
                            Customer</button>
                    </div>

                    <!-- Hidden Form -->
                    <div id="salesform" class="card p-3 mb-4" style="display: none;">
                        <form method="POST" action="save_customer.php">
                            <div class="mb-3">
                                <label class="form-label">Create for:</label>
                                <select class="form-select" id="created_for" name="created_for" required>
                                    <option>Select status</option>
                                    <?php

                                    // Fetch transactions from the database
                                    $result = $conn->query("SELECT user_name FROM users");

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option>" . $row['user_name'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

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
                                <input type="text" name="customer_phone" class="form-control" required maxlength="10">
                            </div>
                            <input type="submit" value="Save Customer" class="btn btn-success text-white"
                                name="whatAction">
                        </form>
                    </div>

                    <!-- JS to Toggle Form -->
                    <script>
                        document.getElementById("salesFromBtn").addEventListener("click", function () {
                            const showform = document.getElementById("salesform");
                            showform.style.display = (showform.style.display === "none") ? "block" : "none";
                        })
                    </script>


                    <div id="sales_vendor_section">
                        <label class="form-label">Vendor:</label>
                        <select class="form-select" id="salesVendor" name="salesVendor" required>
                            <option>Select vendor</option>
                            <?php

                            // Fetch transactions from the database
                            $result = $conn->query("SELECT user_name FROM users WHERE user_type = 'Vendor'");

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option>" . $row['user_name'] . "</option>";
                                }
                            }
                            ?>
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
                        <select id="gsttaxRate" class="form-select" name="gsttaxRate" onchange="calculateSalesTotals()">
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
                    <button class="btn btn-sm btn-outline-primary" onclick="redirect()">+ Add Product</button>
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


<!-- Tabels -->
<div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">

    <div class="tabs">
        <button class="billingTab active" onclick="showbillingTab('invoice')">Invoice</button>
        <button class="billingTab" onclick="showbillingTab('sales')">Sales Return</button>
        <button class="billingTab" onclick="showbillingTab('credit')">Credit Note</button>
        <button class="billingTab" onclick="showbillingTab('quotation')">Quotation</button>
        <button class="billingTab" onclick="showbillingTab('delivery')">Delivery Challan</button>
        <button class="billingTab" onclick="showbillingTab('proforma')">Proforma</button>
        <button class="billingTab" onclick="showbillingTab('auto')">Auto Bill</button>
        <button class="billingTab" onclick="showbillingTab('counter')">Counter Purchase</button>
        <button class="billingTab" onclick="showbillingTab('payment')">Payment Out</button>
        <button class="billingTab" onclick="showbillingTab('purchase')">Purchase Return</button>
        <button class="billingTab" onclick="showbillingTab('debit')">Debit Note</button>
        <button class="billingTab" onclick="showbillingTab('purchase_order')">Purchase Order</button>
    </div>

    <!-- Invoice table -->
    <div id="invoice" class="billing-tab-content active">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Invoices</h1>
            </div>

            <div class="d-flex justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0 table-search" data-table="invoice_table"
                        placeholder="Search..." />
                </div>
                <button class="btn btn-outline-primary gst-filter me-2" data-type="with GST"
                    data-table="invoice_table">With
                    GST</button>
                <button class="btn btn-outline-primary gst-filter me-2" data-type="without GST"
                    data-table="invoice_table">Without
                    GST</button>
                <button class="btn btn-outline-danger reset-filters me-2" data-table="invoice_table">Remove
                    Filters</button>

            </div>

            <div class="justify-contnt-end">
                <button class="btn btn-outline-primary" onclick="openInvoiceModal(event)" id="invoice">Create New
                    Invoice</button>
            </div>

        </div>
        <table id="invoice_table" class="table table-bordered table-hover">
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
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php

                // Fetch transactions from the database
                $result = $conn->query("SELECT * FROM invoice ORDER BY Sales_Id DESC");

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
                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                        echo '<td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-eye"></i></button>
                                    <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-print"></i></button>
                                    <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i></button>';
                        if ($row['status'] !== 'Refund'): ?>
                            <form method="post" action=""
                                onsubmit="return confirm('Are you sure you want to cancel this invoice?');">
                                <input type="hidden" name="invoice_id" value="<?php echo htmlspecialchars($row['invoice_id']); ?>">
                                <input type="hidden" name="created_for"
                                    value="<?php echo htmlspecialchars($row['created_for']); ?>">
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

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancelInvoice']) && $hasDeletePermission) {
        $invoice_id = $conn->real_escape_string($_POST['invoice_id']);
        $created_for = $conn->real_escape_string($_POST['created_for']);

        // 1. Get items & quantities from invoice
        $fetchSql = "SELECT item_name, quantity FROM invoice WHERE invoice_id = ? AND created_for = ?";
        $fetchStmt = $conn->prepare($fetchSql);
        $fetchStmt->bind_param("ss", $invoice_id, $created_for);
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
            $stmt->bind_param("ss", $invoice_id, $created_for);

            if ($stmt->execute()) {

                // Fetch user type
                $fetchUser = "SELECT user_type FROM users WHERE user_name = ?";
                $fetchUserType = $conn->prepare($fetchUser);
                $fetchUserType->bind_param("s", $created_for);
                $fetchUserType->execute();
                $fetchUserResult = $fetchUserType->get_result();
                $userRow = $fetchUserResult->fetch_assoc();
                $fetchUserType->close();

                $user_type = $userRow['user_type'] ?? $user_name;

                // 3. Add cancelled items back to stock
                if ($user_type === 'Store') {
                    for ($i = 0; $i < count($itemNames); $i++) {
                        $item = trim($itemNames[$i]);
                        $qty = intval($quantities[$i]);

                        if ($item && $qty > 0) {
                            // Get latest stock_id for this item
                            $latestStockSql = "SELECT Id FROM retail_invetory 
                                       WHERE item_name = ? AND inventory_of = ? 
                                       ORDER BY last_updated DESC, Id DESC LIMIT 1";
                            $latestStockStmt = $conn->prepare($latestStockSql);
                            $latestStockStmt->bind_param("ss", $item, $created_for);
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
                } else if ($user_type === 'Vendor') {
                    for ($i = 0; $i < count($itemNames); $i++) {
                        $item = trim($itemNames[$i]);
                        $qty = intval($quantities[$i]);

                        if ($item && $qty > 0) {
                            // Get latest product_id for this item
                            $latestStockSql = "SELECT product_id FROM vendor_product 
                                       WHERE product_name = ? AND product_of = ? 
                                       ORDER BY created_at DESC, product_id DESC LIMIT 1";
                            $latestStockStmt = $conn->prepare($latestStockSql);
                            $latestStockStmt->bind_param("ss", $item, $created_for);
                            $latestStockStmt->execute();
                            $latestStockResult = $latestStockStmt->get_result();

                            if ($latestStockResult && $latestStockRow = $latestStockResult->fetch_assoc()) {
                                $latestStockId = $latestStockRow['product_id'];
                                // Update only latest entry
                                $updateSql = "UPDATE vendor_product SET stock = stock + ? WHERE product_id = ?";
                                $updateStmt = $conn->prepare($updateSql);
                                $updateStmt->bind_param("is", $qty, $latestStockId);
                                $updateStmt->execute();
                                $updateStmt->close();
                            }
                            $latestStockStmt->close();
                        }
                    }
                } else if ($user_type === 'Factory') {
                    for ($i = 0; $i < count($itemNames); $i++) {
                        $item = trim($itemNames[$i]);
                        $qty = intval($quantities[$i]);

                        if ($item && $qty > 0) {
                            // Get latest stock_id for this item
                            $latestStockSql = "SELECT stock_id FROM factory_stock 
                                       WHERE item_name = ? AND created_for = ? 
                                       ORDER BY record_date DESC, stock_id DESC LIMIT 1";
                            $latestStockStmt = $conn->prepare($latestStockSql);
                            $latestStockStmt->bind_param("ss", $item, $created_for);
                            $latestStockStmt->execute();
                            $latestStockResult = $latestStockStmt->get_result();

                            if ($latestStockResult && $latestStockRow = $latestStockResult->fetch_assoc()) {
                                $latestStockId = $latestStockRow['stock_id'];
                                // Update only latest entry
                                $updateSql = "UPDATE factory_stock SET quantity = quantity + ? WHERE stock_id = ?";
                                $updateStmt = $conn->prepare($updateSql);
                                $updateStmt->bind_param("is", $qty, $latestStockId);
                                $updateStmt->execute();
                                $updateStmt->close();
                            }
                            $latestStockStmt->close();
                        }
                    }
                } else if ($user_type === 'Admin') {
                    for ($i = 0; $i < count($itemNames); $i++) {
                        $item = trim($itemNames[$i]);
                        $qty = intval($quantities[$i]);

                        if ($item && $qty > 0) {
                            // Get latest stock_id for this item
                            $latestStockSql = "SELECT Id  FROM inventory 
                                       WHERE Product_Name = ?
                                       ORDER BY Date DESC, Id  DESC LIMIT 1";
                            $latestStockStmt = $conn->prepare($latestStockSql);
                            $latestStockStmt->bind_param("s", $item);
                            $latestStockStmt->execute();
                            $latestStockResult = $latestStockStmt->get_result();

                            if ($latestStockResult && $latestStockRow = $latestStockResult->fetch_assoc()) {
                                $latestStockId = $latestStockRow['Id'];
                                // Update only latest entry
                                $updateSql = "UPDATE inventory SET Stock = Stock + ? WHERE Id = ?";
                                $updateStmt = $conn->prepare($updateSql);
                                $updateStmt->bind_param("is", $qty, $latestStockId);
                                $updateStmt->execute();
                                $updateStmt->close();
                            }
                            $latestStockStmt->close();

                            // Get latest product id for this item
                            $latestProductSql = "SELECT id  FROM products 
                                       WHERE name = ?
                                       ORDER BY created_at DESC, id  DESC LIMIT 1";
                            $latestProductStmt = $conn->prepare($latestProductSql);
                            $latestProductStmt->bind_param("s", $item);
                            $latestProductStmt->execute();
                            $latestProductResult = $latestProductStmt->get_result();

                            if ($latestProductResult && $latestProductRow = $latestProductResult->fetch_assoc()) {
                                $latestProductId = $latestProductRow['id'];
                                // Update only latest entry
                                $updateProductSql = "UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?";
                                $updateProductStmt = $conn->prepare($updateProductSql);
                                $updateProductStmt->bind_param("is", $qty, $latestProductId);
                                $updateProductStmt->execute();
                                $updateProductStmt->close();
                            }
                            $latestProductStmt->close();
                        }
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

    <!-- Sales table -->
    <div id="sales" class="billing-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Sales Returns</h1>
            </div>

            <div class="d-flex justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0 table-search" data-table="sales_table"
                        placeholder="Search..." />
                </div>
            </div>

            <div class="justify-contnt-end">
                <button class="btn btn-outline-primary" onclick="openSalesModal(event)" id="sales_return">Create Sales
                    Return</button>
            </div>

        </div>
        <table id="sales_table" class="table table-bordered table-hover">
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
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='11' class='text-center'>No transactions found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Credit table -->
    <div id="credit" class="billing-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Credit Notes</h1>
            </div>

            <div class="d-flex justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0 table-search" data-table="credit_note"
                        placeholder="Search..." />
                </div>
            </div>

            <div class="justify-contnt-end">
                <button class="btn btn-outline-primary" onclick="openSalesModal(event)" id="credit_note">Create Credit
                    Note</button>
            </div>

        </div>
        <table id="credit_note" class="table table-bordered table-hover">
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
                </tr>
            </thead>
            <tbody>
                <?php

                // Fetch transactions from the database
                $result = $conn->query("SELECT * FROM credit_note ORDER BY sales_return_id DESC");

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
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='11' class='text-center'>No transactions found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Quotation table -->
    <div id="quotation" class="billing-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Quotations / Estimates</h1>
            </div>

            <div class="d-flex justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0 table-search" data-table="quotation_table"
                        placeholder="Search..." />
                </div>
                <button class="btn btn-outline-primary gst-filter me-2" data-type="with GST"
                    data-table="quotation_table">With
                    GST</button>
                <button class="btn btn-outline-primary gst-filter me-2" data-type="without GST"
                    data-table="quotation_table">Without
                    GST</button>
                <button class="btn btn-outline-danger reset-filters me-2" data-table="quotation_table">Remove
                    Filters</button>

            </div>

            <div class="justify-contnt-end">
                <button class="btn btn-outline-primary" onclick="openInvoiceModal(event)" id="quotation">Create
                    Quotation</button>
            </div>

        </div>
        <table id="quotation_table" class="table table-bordered table-hover">
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
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='14' class='text-center'>No transactions found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Delivery table -->
    <div id="delivery" class="billing-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Delivery Challans</h1>
            </div>

            <div class="d-flex justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0 table-search" data-table="delivery_table"
                        placeholder="Search..." />
                </div>
            </div>

            <div class="justify-contnt-end">
                <button class="btn btn-outline-primary" onclick="openSalesModal(event)" id="delivery_challan">Create
                    Delivery Challan</button>
            </div>

        </div>
        <table id="delivery_table" class="table table-bordered table-hover">
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
                </tr>
            </thead>
            <tbody>
                <?php

                // Fetch transactions from the database
                $result = $conn->query("SELECT * FROM delivery_challan ORDER BY sales_return_id DESC");

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
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='11' class='text-center'>No transactions found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Proforma table -->
    <div id="proforma" class="billing-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Proforma Invoices</h1>
            </div>

            <div class="d-flex justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0 table-search" data-table="proforma_table"
                        placeholder="Search..." />
                </div>
                <button class="btn btn-outline-primary gst-filter me-2" data-type="with GST"
                    data-table="proforma_table">With
                    GST</button>
                <button class="btn btn-outline-primary gst-filter me-2" data-type="without GST"
                    data-table="proforma_table">Without
                    GST</button>
                <button class="btn btn-outline-danger reset-filters me-2" data-table="proforma_table">Remove
                    Filters</button>

            </div>

            <div class="justify-contnt-end">
                <button class="btn btn-outline-primary" onclick="openInvoiceModal(event)" id="proforma">Create
                    Proforma Invoice</button>
            </div>

        </div>
        <table id="proforma_table" class="table table-bordered table-hover">
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
                </tr>
            </thead>
            <tbody>
                <?php

                // Fetch transactions from the database
                $result = $conn->query("SELECT * FROM proforma ORDER BY invoice_id DESC");

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
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='14' class='text-center'>No transactions found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Auto table -->
    <div id="auto" class="billing-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Automated Bills</h1>
            </div>

            <div class="d-flex justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0 table-search" data-table="auto_table"
                        placeholder="Search..." />
                </div>
            </div>

            <div class="justify-contnt-end">
                <button class="btn btn-outline-primary" onclick="openSalesModal(event)" id="auto_bill">Create
                    Automated Bills</button>
            </div>

        </div>
        <table id="auto_table" class="table table-bordered table-hover">
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
                </tr>
            </thead>
            <tbody>
                <?php

                // Fetch transactions from the database
                $result = $conn->query("SELECT * FROM auto_bill ORDER BY sales_return_id DESC");

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
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='11' class='text-center'>No transactions found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- counter -->
    <div id="counter" class="billing-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Counter Purchases</h1>
            </div>

            <div class="d-flex justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0 table-search" data-table="counter_table"
                        placeholder="Search..." />
                </div>
            </div>

            <div class="justify-contnt-end">
                <button class="btn btn-outline-primary" onclick="openSalesModal(event)" id="counter_purchase">Create
                    Counter Purchases</button>
            </div>

        </div>
        <table id="counter_table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vendor</th>
                    <th>Date</th>
                    <th>Tax Rate</th>
                    <th>Items</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Notes</th>
                    <th>GST Amount</th>
                    <th>Grand Total</th>
                </tr>
            </thead>
            <tbody>
                <?php

                // Fetch transactions from the database
                $result = $conn->query("SELECT * FROM counter_purchase ORDER BY sales_return_id DESC");

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
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='11' class='text-center'>No transactions found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Payment table -->
    <div id="payment" class="billing-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Payments Out</h1>
            </div>

            <div class="d-flex justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0 table-search" data-table="payment_table"
                        placeholder="Search..." />
                </div>
            </div>

            <div class="justify-contnt-end">
                <button class="btn btn-outline-primary" onclick="openSalesModal(event)" id="payment_out">Create Payments
                    Out</button>
            </div>

        </div>
        <table id="payment_table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vendor</th>
                    <th>Date</th>
                    <th>Tax Rate</th>
                    <th>Items</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Notes</th>
                    <th>GST Amount</th>
                    <th>Grand Total</th>
                </tr>
            </thead>
            <tbody>
                <?php

                // Fetch transactions from the database
                $result = $conn->query("SELECT * FROM payment_out ORDER BY sales_return_id DESC");

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
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='11' class='text-center'>No transactions found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Purchase table -->
    <div id="purchase" class="billing-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Purchase Returns</h1>
            </div>

            <div class="d-flex justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0 table-search" data-table="purchase_return"
                        placeholder="Search..." />
                </div>
            </div>

            <div class="justify-contnt-end">
                <button class="btn btn-outline-primary" onclick="openSalesModal(event)" id="purchase_return">Create
                    Purchase Returns</button>
            </div>

        </div>
        <table id="purchase_return" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vendor</th>
                    <th>Date</th>
                    <th>Tax Rate</th>
                    <th>Items</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Notes</th>
                    <th>GST Amount</th>
                    <th>Grand Total</th>
                </tr>
            </thead>
            <tbody>
                <?php

                // Fetch transactions from the database
                $result = $conn->query("SELECT * FROM purchase_return ORDER BY sales_return_id DESC");

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
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='11' class='text-center'>No transactions found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Debit table -->
    <div id="debit" class="billing-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Debit Notes</h1>
            </div>

            <div class="d-flex justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0 table-search" data-table="debit_table"
                        placeholder="Search..." />
                </div>
            </div>

            <div class="justify-contnt-end">
                <button class="btn btn-outline-primary" onclick="openSalesModal(event)" id="debit_note">Create Debit
                    Notes</button>
            </div>

        </div>
        <table id="debit_table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vendor</th>
                    <th>Date</th>
                    <th>Tax Rate</th>
                    <th>Items</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Notes</th>
                    <th>GST Amount</th>
                    <th>Grand Total</th>
                </tr>
            </thead>
            <tbody>
                <?php

                // Fetch transactions from the database
                $result = $conn->query("SELECT * FROM debit_note ORDER BY sales_return_id DESC");

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
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='11' class='text-center'>No transactions found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Purchase order table-->
    <div id="purchase_order" class="billing-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Purchase Orders</h1>
            </div>

            <div class="d-flex justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0 table-search" data-table="purchase_table"
                        placeholder="Search..." />
                </div>
                <button class="btn btn-outline-primary gst-filter me-2" data-type="with GST"
                    data-table="purchase_table">With
                    GST</button>
                <button class="btn btn-outline-primary gst-filter me-2" data-type="without GST"
                    data-table="purchase_table">Without
                    GST</button>
                <button class="btn btn-outline-danger reset-filters me-2" data-table="purchase_table">Remove
                    Filters</button>

            </div>

            <div class="justify-contnt-end">
                <button class="btn btn-outline-primary" onclick="openInvoiceModal(event)"
                    id="purchase_order_bill">Create
                    Purchase Orders</button>
            </div>

        </div>
        <table id="purchase_table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vendor</th>
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
                </tr>
            </thead>
            <tbody>
                <?php

                // Fetch transactions from the database
                $result = $conn->query("SELECT * FROM purchase_order_bill ORDER BY invoice_id DESC");

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
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='14' class='text-center'>No transactions found</td></tr>";
                }
                ?>
            </tbody>
        </table>
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
                        const docType = row.children[4]?.innerText.trim().toLowerCase();
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

    <?php

    // Bar Chart: Get number of bills per month for the last 6 months
    $labels = [];
    $billCounts = [];

    for ($i = 5; $i >= 0; $i--) {
        $monthStart = date('Y-m-01', strtotime("-$i months"));
        $monthEnd = date('Y-m-t', strtotime("-$i months"));
        $monthLabel = date('M Y', strtotime("-$i months"));

        $query = "SELECT COUNT(*) AS bill_count FROM invoice WHERE date BETWEEN '$monthStart' AND '$monthEnd'";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();

        $labels[] = $monthLabel;
        $billCounts[] = $row['bill_count'] ?? 0;
    }

    // Pie Chart: Get payment method counts
    $paymentLabels = [];
    $paymentCounts = [];

    $query = "SELECT payment_method, COUNT(*) AS total FROM invoice GROUP BY payment_method";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        $paymentLabels[] = $row['payment_method'];
        $paymentCounts[] = $row['total'];
    }
    ?>

    <script>
        // Bar Chart
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Revenue',
                    data: <?php echo json_encode($billCounts); ?>,
                    backgroundColor: '#0d6efd',
                    borderColor: '#0d6efd',
                    borderWidth: 1,
                    borderRadius: 6,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 8
                        }
                    }
                }
            }
        });

        // Pie Chart
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($paymentLabels); ?>,
                datasets: [{
                    data: <?php echo json_encode($paymentCounts); ?>,
                    backgroundColor: [
                        '#0d6efd',
                        '#20c997',
                        '#ffc107',
                        '#fd7e14',
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#333',
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });

        function showbillingTab(id) {
            const tabs = document.querySelectorAll('.billingTab');
            const contents = document.querySelectorAll('.billing-tab-content');

            tabs.forEach(tab => tab.classList.remove('active'));
            contents.forEach(content => content.classList.remove('active'));

            document.querySelector(`#${id}`).classList.add('active');
            document.querySelector(`[onclick="showbillingTab('${id}')"]`).classList.add('active');
        }

        // Create invoice form 

        // let itemIndex = 0;
        let activeInvoiceButtonId = null;
        let activeSalesButtonId = null;

        // To open form
        function openInvoiceModal(event) {
            activeInvoiceButtonId = event.target.id; // To store clicked button ID

            const modal = document.getElementById('invoiceModal');
            modal.style.display = 'block';
            modal.classList.add('show');

            const status = document.getElementById('status_section');
            const createdFor = document.getElementById('createdFor_section');
            if (activeInvoiceButtonId === 'invoice') {
                status.style.display = 'block';
                status.classList.add('show');

                createdFor.style.display = 'block';
                createdFor.classList.add('show');
            } else {
                status.style.display = 'none';
                status.classList.remove('show');

                createdFor.style.display = 'none';
                createdFor.classList.remove('show');
            }

            const customer = document.getElementById('customer_section');
            const vendor = document.getElementById('vendor_section');
            if (activeInvoiceButtonId === 'purchase_order_bill') {
                customer.style.display = 'none';
                customer.classList.remove('show');

                vendor.style.display = 'block';
                vendor.classList.add('show');
            } else {
                customer.style.display = 'block';
                customer.classList.add('show');

                vendor.style.display = 'none';
                vendor.classList.remove('show');
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
                        $result = $conn->query("SELECT Product_Name FROM inventory");

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option>" . $row['Product_Name'] . "</option>";
                            }
                        }
                        ?>
                    </select>
                </td>
                <td><input placeholder="Description"/></td>
                <td><input type="number" value="1" min="1" oninput="updateTotals()" /></td>
                <td><input type="number" value="0" step="0.01" oninput="updateTotals()" /></td>
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

            const vedorActiveted = activeInvoiceButtonId === 'purchase_order_bill';

            const data = {
                table: activeInvoiceButtonId,
                customer_name: vedorActiveted ? document.getElementById("vendor").value : document.getElementById("customer").value,
                payment_method: document.getElementById("invoicePaymentMethod").value,
                status: document.getElementById("invoiceStatus").value,
                created_for: document.getElementById("created_for").value,
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

            fetch("billing_desk.php", {
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

            const salesCustomer = document.getElementById('sales_customer_section');
            const salesVendor = document.getElementById('sales_vendor_section');
            if (activeSalesButtonId === 'counter_purchase' || activeSalesButtonId === 'payment_out' || activeSalesButtonId === 'purchase_return' || activeSalesButtonId === 'debit_note') {
                salesCustomer.style.display = 'none';
                salesCustomer.classList.remove('show');

                salesVendor.style.display = 'block';
                salesVendor.classList.add('show');
            } else {
                salesCustomer.style.display = 'block';
                salesCustomer.classList.add('show');

                salesVendor.style.display = 'none';
                salesVendor.classList.remove('show');
            }

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
                $result = $conn->query("SELECT Product_Name FROM inventory");

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option>" . $row['Product_Name'] . "</option>";
                    }
                }
                ?>
            </select>
        </td>
        <td><input type="text" placeholder="Description"></td>
        <td><input type="number" class="qty" value="1" min="1" oninput="calculateSalesTotals()"></td>
        <td><input type="number" class="price" value="0" min="0" oninput="calculateSalesTotals()"></td>
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

            const salesVedorActiveted = (activeSalesButtonId === 'counter_purchase' || activeSalesButtonId === 'payment_out' || activeSalesButtonId === 'purchase_return' || activeSalesButtonId === 'debit_note');

            const data = {
                table: activeSalesButtonId,
                customer_name: salesVedorActiveted ? document.getElementById("salesVendor").value : document.getElementById("salesCustomer").value,
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

            fetch("billing_desk.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(data)
            })
                .then(res => res.text())
                .then(msg => {
                    // alert(msg);
                    activeSalesButtonId = null;
                    location.reload();
                })
                .catch(err => alert("Error submitting invoice."));
        }

        function redirect() {
            window.location.href = "admin_dashboard.php?page=inventory"
        }
    </script>