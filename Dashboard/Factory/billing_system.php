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
        $prefix = ($docType === 'with GST') ? 'INV' : 'INVWO';

        $currentYear = date("Y");

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

        // Prepare item arrays
        $itemNames = explode(',', $data['item_names']);
        $quantities = explode(',', $data['quantities']);

        // Validate stock before inserting invoice
        for ($i = 0; $i < count($itemNames); $i++) {
            $item = trim($itemNames[$i]);
            $qty = (int) trim($quantities[$i]);

            $stockResult = $conn->query("SELECT quantity FROM factory_stock WHERE item_name = '$item' AND created_for = '$user_name' ORDER BY record_date DESC, stock_id DESC LIMIT 1");

            if ($stockResult && $stockRow = $stockResult->fetch_assoc()) {
                $currentStock = (int) $stockRow['quantity'];
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

            // Get latest stock_id for this item
            $latestStockSql = "SELECT stock_id FROM factory_stock WHERE item_name = '$item' AND created_for = '$user_name' ORDER BY record_date DESC, stock_id DESC LIMIT 1";
            $latestStockResult = $conn->query($latestStockSql);
            if ($latestStockResult && $latestStockRow = $latestStockResult->fetch_assoc()) {
                $latestStockId = $latestStockRow['stock_id'];
                // Update only latest entry
                $updateInventory = $conn->query("UPDATE factory_stock SET quantity = quantity - $qty WHERE stock_id = '$latestStockId'");
            }
        }

        if ($stmt->execute()) {
            echo "Invoice inserted successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

?>

<h2>Factory Billing System</h2>
<p>Manage all factory-related invoices, receipts and payments</p>

<div>

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

<!-- ✅ JavaScript to Export Table -->
<script>
    function exportTableToCSV() {
        const table = document.getElementById("invoice_table");
        let csv = [];
        for (let row of table.rows) {
            let cols = Array.from(row.cells)
                .slice(0, -1) // exclude last 'Actions' column
                .map(cell => `"${cell.innerText.replace(/"/g, '""')}"`);
            csv.push(cols.join(","));
        }
        let csvContent = csv.join("\n");
        let blob = new Blob([csvContent], {
            type: "text/csv;charset=utf-8;"
        });

        // Download link
        let link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = "Billing_detail.csv";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>

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

<!-- Factory Invoice table -->
<div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">

    <div id="workers">
        <div class="d-flex justify-content-between align-items-center">
            <div class="justify-content-start">
                <h5 class="mb-0">Factory Invoices</h5>
            </div>
            <div class="justify-content-end">
                <button class="btn btn-outline-primary btn-sm me-2" onclick="openInvoiceModal(event)" id="invoice"><i
                        class="fa-solid fa-file"></i> Create Factory Invoice</button>
                <button class="btn btn-outline-primary btn-sm" onclick="exportTableToCSV()"><i
                        class="fa-solid fa-download"></i> Export Records</button>
            </div>

        </div>
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
                    <?php if ($hasDeletePermission): ?>
                    <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php

                // Fetch transactions from the database
                $result = $conn->query("SELECT * FROM invoice WHERE created_for = '$user_name' ORDER BY invoice_id DESC");

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
                                <div class="d-flex gap-2">';
                        if ($hasDeletePermission && $row['status'] !== 'Refund'): ?>
                <form method="post" action=""
                    onsubmit="return confirm('Are you sure you want to cancel this invoice?');">
                    <input type="hidden" name="invoice_id" value="<?php echo htmlspecialchars($row['invoice_id']); ?>">
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
                    echo "<tr><td colspan='17' class='text-center'>No transactions found</td></tr>";
                }
                ?>
            </tbody>
        </table>
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
                    $latestStockSql = "SELECT stock_id FROM factory_stock 
                                       WHERE item_name = ? AND created_for = ? 
                                       ORDER BY record_date DESC, stock_id DESC LIMIT 1";
                    $latestStockStmt = $conn->prepare($latestStockSql);
                    $latestStockStmt->bind_param("ss", $item, $user_name);
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

            echo "<script>alert('Invoice cancelled successfully!'); window.location.href=window.location.href;</script>";
        } else {
            echo "<script>alert('Error cancelling invoice: " . $conn->error . "');</script>";
        }

        $stmt->close();
    }
}
?>


<!-- <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-eye"></i></button>
<button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-print"></i></button> -->

<?php

// Get current date
$today = date("Y-m-d");

// Query to count overdue invoices
$sql = "SELECT COUNT(*) AS overdue_count FROM invoice WHERE due_date < ? AND created_for = '$user_name'";
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

<?php

// Get current year and month
$currentYear = date('Y');
$currentMonth = date('m');

// SQL query to get total production billing of current month
$sql = "SELECT SUM(grand_total) AS total_billing 
        FROM invoice 
        WHERE created_for = ?  
        AND YEAR(date) = ? 
        AND MONTH(date) = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $user_name, $currentYear, $currentMonth);
$stmt->execute();
$result = $stmt->get_result();

$totalBilling = 0;
if ($row = $result->fetch_assoc()) {
    $totalBilling = $row['total_billing'];
}

// Format in INR
function formatINR($amount)
{
    return '₹' . number_format($amount, 2, '.', ',');
}

$formattedBilling = formatINR($totalBilling);

?>

<!-- Reminders -->
<div class="row">
    <div class="col-md-6 col-sm-12 my-4">
        <div class="card stat-card cards shadow-sm" style="background-color:rgb(255, 250, 232);">
            <div class="card-body">
                <h5 class="text-warning"><i class="fa-solid fa-triangle-exclamation"></i> Payment Reminders</h5>
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
    <div class="col-md-6 col-sm-12 my-4">
        <div class="card stat-card cards shadow-sm" style="background-color: #e7f3ff;">
            <div class="card-body">
                <h5 class="text-primary"><i class="fa-regular fa-circle-check"></i> This Month's Production Billing</h5>
                <p><?php echo $formattedBilling; ?> billed this month for production services</p>
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
                            $result = $conn->query("SELECT user_name FROM users WHERE user_type IN ('Vendor', 'Store')");

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option>" . $row['user_name'] . "</option>";
                                }
                            }
                            ?>
                        </select>
                        <!--Add customer btn -->
                        <button class="btn bg-primary text-white mt-2" id="ADD">+ Add Customer</button>
                    </div>


                    <!-- Hidden Customer Form -->
                    <div id="customerForm" class="card p-3 mb-4" style="display: none;">
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
                                <input type="text" name="customer_phone" class="form-control" required maxlength="10">
                            </div>
                            <input type="submit" value="Save Customer" class="btn btn-success text-white"
                                name="whatAction">
                        </form>
                    </div>

                    <!-- JS to Toggle Form -->
                    <script>
                        document.getElementById("ADD").addEventListener('click', function () {
                            const form = document.getElementById("customerForm")
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
                    <button class="btn btn-sm btn-outline-primary" onclick="Redirect()">+ Add Product</button>
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

<script>
    let activeInvoiceButtonId = null;

    // To open form
    function openInvoiceModal(event) {
        activeInvoiceButtonId = event.currentTarget.id; // To store clicked button ID

        const modal = document.getElementById('invoiceModal');
        modal.style.display = 'block';
        modal.classList.add('show');

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
                $result = $conn->query("SELECT DISTINCT item_name FROM factory_stock WHERE created_for = '$user_name'");

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

        fetch("billing_system.php", {
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

    function Redirect() {
        window.location.href = "factory_dashboard.php?page=inventory"
    }
</script>