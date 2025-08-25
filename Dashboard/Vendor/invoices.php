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

            $stockResult = $conn->query("SELECT stock FROM vendor_product WHERE product_name = '$item' AND product_of = '$user_name' LIMIT 1");

            if ($stockResult && $stockRow = $stockResult->fetch_assoc()) {
                $currentStock = (int) $stockRow['stock'];
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

            $updateInventory = $conn->query("UPDATE vendor_product SET stock = stock - $qty WHERE product_name = '$item' AND product_of = '$user_name'");
        }

        if ($stmt->execute()) {
            echo "Invoice inserted successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}


?>

<h4><i class="fas fa-file-invoice text-primary"></i> Invoices</h4>
<p>Generate and manage invoices.</p>



<div class="d-flex gap-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <button type="button" class="btn btn-primary btn-sm" onclick="openInvoiceModal(event)" id="invoice">
            <i class="fas fa-plus me-1"></i> Generate Invoice
        </button>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <button type="button" class="btn btn-primary btn-sm" onclick="exportTableToCSV()">
            Export
        </button>
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
                    <div id="HiddenForm" class="card p-3 mb-4" style="display: none;">
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
                    <!-- JS toggle form  -->
                    <script>
                        document.getElementById("ADD").addEventListener('click', function () {
                            const form = document.getElementById("HiddenForm")
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
        <h5 class="card-title">Recent Invoices</h5>
        <p class="text-muted">View and manage your invoices</p>
        <!-- Search and Filters -->
        <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center mb-4">
            <div class="flex-grow-1">
                <input type="hidden" name="page" value="billing">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" id="searchInput"><i
                            class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0 table-search" data-table="invoicesTable"
                        placeholder="Search..." />
                </div>
            </div>
            <div class="d-flex gap-2">
                <div>
                    <button class="btn btn-outline-primary gst-filter me-2" data-type="with GST"
                        data-table="invoicesTable">With
                        GST</button>
                </div>
                <div>
                    <button class="btn btn-outline-primary gst-filter me-2" data-type="without GST"
                        data-table="invoicesTable">Without
                        GST</button>
                </div>
                <div>
                    <button class="btn btn-outline-danger reset-filters me-2" data-table="invoicesTable">Remove
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
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="invoicesTable">
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

                                </div>
                            </td>';
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='17' class='text-center'>No transactions found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <script>
                // Export table data to CSV function
                function exportTableToCSV(filename = 'table-data.csv') {
                    const rows = document.querySelectorAll("#invoicesTable tr");
                    let csv = [];
                    rows.forEach(row => {
                        let cols = Array.from(row.querySelectorAll("th, td"))
                            .map(col => `"${col.innerText.trim().replace(/"/g, '""')}"`); // Escape quotes
                        csv.push(cols.join(","));
                    });

                    // Create a Blob from the CSV string
                    let csvFile = new Blob([csv.join("\n")], {
                        type: "text/csv"
                    });

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
                    // Get latest product_id for this item
                    $latestStockSql = "SELECT product_id FROM vendor_product 
                                       WHERE product_name = ? AND product_of = ? 
                                       ORDER BY created_at DESC, product_id DESC LIMIT 1";
                    $latestStockStmt = $conn->prepare($latestStockSql);
                    $latestStockStmt->bind_param("ss", $item, $user_name);
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

            echo "<script>alert('Invoice cancelled successfully!'); window.location.href=window.location.href;</script>";
        } else {
            echo "<script>alert('Error cancelling invoice: " . $conn->error . "');</script>";
        }

        $stmt->close();
    }
}
?>

<?php

// Fetch total GST invoices
$gst_sql = "SELECT COUNT(*) AS total_gst FROM invoice WHERE document_type = 'with GST' AND created_for = '$user_name'";
$gst_result = $conn->query($gst_sql);
$gst_count = $gst_result->fetch_assoc()['total_gst'];

// Fetch total Non-GST invoices
$non_gst_sql = "SELECT COUNT(*) AS total_non_gst FROM invoice WHERE document_type = 'without GST' AND created_for = '$user_name'";
$non_gst_result = $conn->query($non_gst_sql);
$non_gst_count = $non_gst_result->fetch_assoc()['total_non_gst'];

// Fetch Pending Payments
$outstanding_sql = "SELECT SUM(grand_total) AS total_outstanding FROM invoice WHERE status = 'Pending' AND created_for = '$user_name'";
$outstanding_result = $conn->query($outstanding_sql);
$outstanding_amount = $outstanding_result->fetch_assoc()['total_outstanding'] ?? 0;

?>


<!-- Invoice Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">GST Invoices</h5>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="h3 font-weight-bold"><?= $gst_count ?></p>
                        <p class="text-muted">Total GST Invoices</p>
                    </div>
                    <div class="p-3 bg-primary bg-opacity-10 rounded-circle">
                        <i class="fas fa-file-invoice text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Non-GST Invoices</h5>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="h3 font-weight-bold"><?= $non_gst_count ?></p>
                        <p class="text-muted">Total Non-GST Invoices</p>
                    </div>
                    <div class="p-3 bg-purple bg-opacity-10 rounded-circle">
                        <i class="fas fa-file-invoice text-purple"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Outstanding Amount</h5>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="h3 font-weight-bold">₹<?= number_format($outstanding_amount, 2) ?></p>
                        <p class="text-muted">Total Pending Amount</p>
                    </div>
                    <div class="p-3 bg-warning bg-opacity-10 rounded-circle">
                        <i class="fas fa-file-invoice text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title">Quick Actions</h5>
        <p class="text-muted">Generate or request documents</p>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
            <div class="col">
                <form method="POST" action="?page=invoices">
                    <input type="hidden" name="action" value="download_all">
                    <button type="button" id="downloadAllBtn"
                        class="btn btn-outline-success btn-sm w-100 h-100 py-3 d-flex flex-column align-items-center gap-2">
                        <i class="fas fa-download text-success"></i>
                        <span>Download All</span>
                    </button>
                    <script>
                        document.getElementById("downloadAllBtn").addEventListener("click", function () {
                            const rows = document.querySelectorAll("#invoicesTable tr");
                            let csv = [];

                            rows.forEach(row => {
                                let cols = Array.from(row.querySelectorAll("th, td"))
                                    .map(col => `"${col.innerText.trim()}"`);
                                csv.push(cols.join(","));
                            });

                            const csvContent = csv.join("\n");
                            const blob = new Blob([csvContent], {
                                type: "text/csv"
                            });
                            const url = URL.createObjectURL(blob);

                            const a = document.createElement("a");
                            a.href = url;
                            a.download = "all-invoices.csv";
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                        });
                    </script>


                </form>
            </div>
            <div class="col">
                <form method="POST" action="?page=invoices">
                    <input type="hidden" name="action" value="gst_reports">
                    <button id="gstReportBtn" type="button"
                        class="btn btn-outline-purple btn-sm w-100 h-100 py-3 d-flex flex-column align-items-center gap-2">
                        <i class="fas fa-file-alt text-purple"></i>
                        <span>GST Reports</span>
                    </button>

                    <script>
                        document.getElementById('gstReportBtn').addEventListener('click', () => {
                            const rows = document.querySelectorAll('#invoicesTable tbody tr');
                            let csv = 'Invoice ID,Sales ID,Payment ID,Customer,Date,Due Date,Document Type,Tax Rate,Items,Description,Quantity,Notes,GST Amount,Grand Total,Created By,Status\n';

                            rows.forEach(row => {
                                // Skip rows that have a colspan (like "No invoices found" message)
                                if (row.querySelector('td[colspan]')) return;

                                // Get the "Type" cell text, ignoring the GST number div inside it
                                let typeCell = row.cells[6];
                                let typeText = '';
                                if (typeCell) {
                                    // Get only the text node before the div, or fallback to whole text
                                    typeText = typeCell.childNodes[0].textContent.trim();
                                }

                                if (typeText === 'with GST') {
                                    // Extract columns 0 to 6
                                    const data = [];
                                    for (let i = 0; i <= 15; i++) {
                                        // Remove commas so CSV format is not broken
                                        let cellText = row.cells[i].innerText.replace(/,/g, '').trim();
                                        data.push(`"${cellText}"`);
                                    }
                                    csv += data.join(',') + '\n';
                                }
                            });

                            if (csv === 'Invoice ID,Sales ID,Payment ID,Customer,Date,Due Date,Document Type,Tax Rate,Items,Description,Quantity,Notes,GST Amount,Grand Total,Created By,Status\n') {
                                alert('No GST invoices found!');
                                return;
                            }

                            // Create and download the CSV file
                            const blob = new Blob([csv], {
                                type: 'text/csv'
                            });
                            const url = URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = 'gst-invoices-report.csv';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                            URL.revokeObjectURL(url);
                        });
                    </script>



                </form>
            </div>
            <div class="col">
                <form method="POST" action="?page=invoices">
                    <input type="hidden" name="action" value="non_gst_reports">
                    <button id="nongstReportBtn" type="button"
                        class="btn btn-outline-danger btn-sm w-100 h-100 py-3 d-flex flex-column align-items-center gap-2">
                        <i class="fas fa-file-alt text-danger"></i>
                        <span>Non GST Reports</span>
                    </button>

                    <script>
                        document.getElementById('nongstReportBtn').addEventListener('click', () => {
                            const rows = document.querySelectorAll('#invoicesTable tbody tr');
                            let csv = 'Invoice ID,Sales ID,Payment ID,Customer,Date,Due Date,Document Type,Tax Rate,Items,Description,Quantity,Notes,GST Amount,Grand Total,Created By,Status\n';

                            rows.forEach(row => {
                                // Skip rows that have a colspan (like "No invoices found" message)
                                if (row.querySelector('td[colspan]')) return;

                                // Get the "Type" cell text, ignoring the GST number div inside it
                                let typeCell = row.cells[6];
                                let typeText = '';
                                if (typeCell) {
                                    // Get only the text node before the div, or fallback to whole text
                                    typeText = typeCell.childNodes[0].textContent.trim();
                                }

                                if (typeText === 'without GST') {
                                    // Extract columns 0 to 6
                                    const data = [];
                                    for (let i = 0; i <= 15; i++) {
                                        // Remove commas so CSV format is not broken
                                        let cellText = row.cells[i].innerText.replace(/,/g, '').trim();
                                        data.push(`"${cellText}"`);
                                    }
                                    csv += data.join(',') + '\n';
                                }
                            });

                            if (csv === 'Invoice ID,Sales ID,Payment ID,Customer,Date,Due Date,Document Type,Tax Rate,Items,Description,Quantity,Notes,GST Amount,Grand Total,Created By,Status\n') {
                                alert('No non GST invoices found!');
                                return;
                            }

                            // Create and download the CSV file
                            const blob = new Blob([csv], {
                                type: 'text/csv'
                            });
                            const url = URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = 'non-gst-invoices-report.csv';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                            URL.revokeObjectURL(url);
                        });
                    </script>



                </form>
            </div>
        </div>
    </div>
</div>


<style>
    .bg-purple {
        background-color: #6f42c1;
    }

    .text-purple {
        color: #6f42c1;
    }

    .btn-outline-purple {
        border-color: #6f42c1;
        color: #6f42c1;
    }

    .btn-outline-purple:hover {
        background-color: #6f42c1;
        color: #fff;
    }

    .badge {
        font-size: 0.85rem;
        padding: 4px 8px;
    }
</style>

<script>
    let activeInvoiceButtonId = null;

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
                $result = $conn->query("SELECT product_name FROM vendor_product  WHERE product_of = '$user_name'");

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option>" . $row['product_name'] . "</option>";
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

        fetch("invoices.php", {
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
        window.location.href = "vendor_dashboard.php?page=products"
    }
</script>