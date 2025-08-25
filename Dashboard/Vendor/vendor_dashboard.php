<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../_conn.php';

$user_name = $_SESSION['user_name'];

if (!(isset($_SESSION["uid"]) && isset($_SESSION["user_type"]) && isset($_SESSION["session_id"]))) {
    header("location:../../login.php");
    exit;
} else {
    if (in_array($_SESSION["user_type"], ['Factory', 'Store', 'Admin'])) {
        header("location:../index.php");
        exit;
    } else if (!($_SESSION["user_type"] == 'Vendor')) {
        header("location:../../login.php");
        exit;
    }
}
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['whatAction'])) {

    if ($_POST['whatAction'] === 'editStatus') {

        // Get data from the form
        $invoice_id = $_POST['invoice_id'] ?? '';
        $status = $_POST['status'] ?? '';

        // Basic validation
        if (!empty($invoice_id) && !empty($status)) {
            // Prepare and execute the update query
            $stmt = $conn->prepare("UPDATE invoice SET status = ? WHERE invoice_id = ?");
            $stmt->bind_param("ss", $status, $invoice_id);

            if ($stmt->execute()) {
                @header("Location: vendor_dashboard.php?page=dashboard");
            } else {
                echo "Error updating status: " . $conn->error;
            }

            $stmt->close();
        } else {
            echo "Invalid input.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Dashboard - Shree Unnati Wires & Traders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../public/css/styles.css">
    <script src="https://cdn.sheetjs.com/xlsx-latest/package/xlsx.full.min.js"></script>
    <style> </style>
</head>

<body>
    <?php include '_vendor_nav.php'; ?>
    <!-- Main Content -->
    <main>
        <?php if ($page === 'dashboard'): ?>
            <div class="container-fluid">
                <h1>Dashboard</h1>
                <p>Welcome back to your vendor management dashboard</p>

                <?php
                // Pending Orders
                $pendingOrdersQuery = $conn->query("SELECT COUNT(*) AS pending_order FROM retail_store_stock_request WHERE status IN ('Ordered', 'In Transit') AND request_to = '$user_name'");
                $pendingOrdersRow = $pendingOrdersQuery->fetch_assoc();
                $pendingOrders = $pendingOrdersRow['pending_order'] ?? 0;

                // Pending Deliveries
                $pendingDeliveriesQuery = $conn->query("SELECT COUNT(*) AS pending_count FROM retail_store_stock_request WHERE status = 'Ordered' AND requested_by = '$user_name'");
                $pendingDeliveriesRow = $pendingDeliveriesQuery->fetch_assoc();
                $pendingDeliveries = $pendingDeliveriesRow['pending_count'] ?? 0;

                // Pending Payments
                $pendingPaymentsQuery = $conn->query("SELECT SUM(grand_total) AS pending_total FROM invoice WHERE status = 'Pending' AND created_for = '$user_name'");
                $pendingPaymentsRow = $pendingPaymentsQuery->fetch_assoc();
                $pendingPayments = $pendingPaymentsRow['pending_total'] ?? 0;

                // Total Revenue (last 30 days or total?)
                $revenueQuery = $conn->query("SELECT SUM(grand_total) AS total_revenue FROM invoice WHERE date >= CURDATE() - INTERVAL 30 DAY AND created_for = '$user_name'");
                $revenueRow = $revenueQuery->fetch_assoc();
                $totalRevenue = $revenueRow['total_revenue'] ?? 0;

                //  Compare with previous month
                $prevMonthRevenueQuery = $conn->query("SELECT SUM(grand_total) AS prev_revenue FROM invoice WHERE date BETWEEN DATE_SUB(CURDATE(), INTERVAL 60 DAY) AND DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND created_for = '$user_name'");
                $prevRevenueRow = $prevMonthRevenueQuery->fetch_assoc();
                $prevRevenue = $prevRevenueRow['prev_revenue'] ?? 0;

                // Calculate revenue growth
                $revenueGrowth = 0;
                if ($prevRevenue > 0) {
                    $revenueGrowth = (($totalRevenue - $prevRevenue) / $prevRevenue) * 100;
                }

                ?>

                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm h-100"
                            style="border-left: 5px solid #0d6efd;">
                            <div class="card-body">
                                <h6 class="text-muted">Active Orders</h6>
                                <h3 class="fw-bold"><?php echo $pendingOrders; ?> orders</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm h-100"
                            style="border-left: 5px solid #198754;">
                            <div class="card-body">
                                <h6 class="text-muted">Pending Deliveries</h6>
                                <h3 class="fw-bold"><?php echo $pendingDeliveries; ?> deliveries</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm h-100"
                            style="border-left: 5px solid #ffc107;">
                            <div class="card-body">
                                <h6 class="text-muted">Pending Payments</h6>
                                <h3 class="fw-bold">₹<?php echo number_format($pendingPayments); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
                            <div class="card-body">
                                <h6 class="text-muted">This Month Revenue</h6>
                                <h3 class="fw-bold">₹<?php echo number_format($totalRevenue); ?></h3>
                                <p class="text-success">+<?php echo round($revenueGrowth, 1); ?>% vs last month</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-4"
                    style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);">
                    <div class="col-md-8 col-sm-12 mb-4 text-center">
                        <h3>Order Trends (Last 6 Months)</h3>
                        <canvas id="orderTrendsChart"></canvas>
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

                <div class="card shadow-sm cards card-border" style="border-left: 5px solid #0d6efd;">
                    <div class="card-body">
                        <h5 class="card-title d-flex align-items-center">
                            <i class="fas fa-file-text me-2 text-primary"></i> Billing & Invoice Management
                        </h5>
                        <p class="card-text text-muted">Create, track, and manage invoices and payments</p>
                        <ul class="nav nav-tabs mb-4">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#invoices">Invoices</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#payments">Payments</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="invoices">
                                <!-- Search and Filters -->
                                <div class="container-fluid d-flex justify-content-between align-items-center">
                                    <div class="d-flex justify-content-start">
                                        <input type="hidden" name="page" value="billing">
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0" id="searchInput"><i
                                                    class="fas fa-search"></i></span>
                                            <input type="text" class="form-control border-start-0 table-search"
                                                data-table="dashTable" placeholder="Search..." />
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <div>
                                            <button class="btn btn-outline-primary gst-filter me-2" data-type="with GST"
                                                data-table="dashTable">With
                                                GST</button>
                                        </div>
                                        <div>
                                            <button class="btn btn-outline-primary gst-filter me-2" data-type="without GST"
                                                data-table="dashTable">Without
                                                GST</button>
                                        </div>
                                        <div>
                                            <button class="btn btn-outline-danger reset-filters me-2"
                                                data-table="dashTable">Remove
                                                Filters</button>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2 justify-contnt-end">
                                        <button class="btn btn-outline-primary" onclick="openInvoiceModal(event)"
                                            id="invoice"><i class="fa-solid fa-plus"></i> Create Invoice</button>

                                        <button class="btn btn-outline-primary btn-sm" onclick="exportTableToCSV()">
                                            Export
                                        </button>
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
                                    <table class="table table-bordered table-hover" id="dashTable">
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
                                        // Export table data to CSV
                                        function exportTableToCSV(filename = 'table-data.csv') {
                                            const rows = document.querySelectorAll("#dashTable tr");
                                            let csv = [];

                                            rows.forEach(row => {
                                                let cols = Array.from(row.querySelectorAll("th, td"))
                                                    .map(col => `"${col.innerText.trim()}"`);
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
                            <div class="tab-pane fade" id="payments">
                                <!-- Search and Filters -->
                                <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center mb-4">
                                    <div class="flex-grow-1">
                                        <input type="hidden" name="page" value="billing">
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0" id="searchInput"><i
                                                    class="fas fa-search"></i></span>
                                            <input type="text" class="form-control border-start-0 table-search"
                                                data-table="paymentsTable" placeholder="Search..." />
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <div>
                                            <button class="btn btn-outline-primary gst-filter me-2" data-type="Completed"
                                                data-table="paymentsTable">Completed</button>
                                        </div>
                                        <div>
                                            <button class="btn btn-outline-primary gst-filter me-2" data-type="Pending"
                                                data-table="paymentsTable">Pending</button>
                                        </div>
                                        <div>
                                            <button class="btn btn-outline-primary gst-filter me-2" data-type="Refund"
                                                data-table="paymentsTable">Refund</button>
                                        </div>
                                        <div>
                                            <button class="btn btn-outline-danger reset-filters me-2"
                                                data-table="paymentsTable">Remove
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
                                    <table class="table table-bordered table-hover" id="paymentsTable">
                                        <thead>
                                            <tr>
                                                <th>Payment ID</th>
                                                <th>Invoice ID</th>
                                                <th>Customer</th>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Method</th>
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
                                                    $status = htmlspecialchars($row['status']);
                                                    $id = $row['invoice_id'];

                                                    echo "<tr>";
                                                    echo "<td>" . htmlspecialchars($row['payment_id']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['invoice_id']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                                                    echo "<td>" . date('d-M-Y', strtotime($row['date'])) . "</td>";
                                                    echo "<td>₹" . number_format($row['grand_total'], 2) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['payment_method']) . "</td>";
                                                    echo "<td>" . $status . "</td>";

                                                    echo "<td>";
                                                    if ($status === 'Pending') {
                                                        echo '<button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#statusModal' . $id . '">
                                                                <i class="fa-regular fa-pen-to-square"></i>
                                                            </button>';
                                                    } else {
                                                        echo '<button class="btn btn-outline-secondary btn-sm" disabled>
                                                                <i class="fa-regular fa-pen-to-square"></i>
                                                            </button>';
                                                    }
                                                    echo "</td>";
                                                    echo "</tr>";

                                                    // Modal only for pending rows
                                                    if ($status === 'Pending') {
                                            ?>
                                                        <div class="modal fade" id="statusModal<?= $id ?>" tabindex="-1"
                                                            aria-labelledby="statusModalLabel<?= $id ?>" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <form method="POST" action="vendor_dashboard.php">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="statusModalLabel<?= $id ?>">
                                                                                Update Status</h5>
                                                                            <button type="button" class="btn-close"
                                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <input type="hidden" name="invoice_id" value="<?= $id ?>">
                                                                            <select name="status" class="form-select" required>
                                                                                <option value="">Select Status</option>
                                                                                <option value="Completed">Completed</option>
                                                                                <option value="Refund">Refund</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="submit" class="btn btn-primary"
                                                                                name="whatAction" value="editStatus">Update</button>
                                                                            <button type="button" class="btn btn-secondary"
                                                                                data-bs-dismiss="modal">Cancel</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                            <?php
                                                    }
                                                }
                                            } else {
                                                echo "<tr><td colspan='8' class='text-center'>No transactions found</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
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
                                        <button class="btn bg-primary mt-2 text-white" id="ADD"> + Add Customer</button>
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
                                            <input type="submit" value="Save Customer" class="btn btn-success text-white" name="whatAction">
                                        </form>
                                    </div>
                                    <!-- JS toggle form  -->
                                    <script>
                                        document.getElementById("ADD").addEventListener('click', function() {
                                            const form = document.getElementById("HiddenForm")
                                            form.style.display = (form.style.display === "none") ? "block" : "none"
                                        })
                                    </script>

                                    <div class="col-md-4">
                                        <label class="form-label">Payment Method:</label>
                                        <select class="form-select" id="invoicePaymentMethod" name="invoicePaymentMethod"
                                            required>
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
                                    <p class="gst-section">GST (<span id="gstPercent">18</span>%): ₹<span
                                            id="gstAmount">0.00</span>
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

            </div>
        <?php elseif ($page === 'orders'): ?>
            <?php include 'orders.php'; ?>

        <?php elseif ($page === 'deliveries'): ?>
            <?php include 'deliveries.php'; ?>

        <?php elseif ($page === 'products'): ?>
            <?php include 'products.php'; ?>

        <?php elseif ($page === 'payments'): ?>
            <?php include 'payments.php'; ?>

        <?php elseif ($page === 'invoices'): ?>
            <?php include 'invoices.php'; ?>

        <?php elseif ($page === 'reports'): ?>
            <?php include 'reports.php'; ?>

        <?php elseif ($page === 'settings'): ?>
            <?php include 'settings.php'; ?>
        <?php else: ?>
            <div class="container-fluid">
                <h1>Dashboard</h1>
                <p>Welcome back to your vendor management dashboard</p>
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm h-100"
                            style="border-left: 5px solid #0d6efd;">
                            <div class="card-body">
                                <h6 class="text-muted">Active Orders</h6>
                                <h3 class="fw-bold"><?php echo $pendingOrders; ?> orders</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm h-100"
                            style="border-left: 5px solid #198754;">
                            <div class="card-body">
                                <h6 class="text-muted">Pending Deliveries</h6>
                                <h3 class="fw-bold"><?php echo $pendingDeliveries; ?> deliveries</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm h-100"
                            style="border-left: 5px solid #ffc107;">
                            <div class="card-body">
                                <h6 class="text-muted">Pending Payments</h6>
                                <h3 class="fw-bold">₹<?php echo number_format($pendingPayments); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
                            <div class="card-body">
                                <h6 class="text-muted">This Month Revenue</h6>
                                <h3 class="fw-bold">₹<?php echo number_format($totalRevenue); ?></h3>
                                <p class="text-success">+<?php echo round($revenueGrowth, 1); ?>% vs last month</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-4"
                    style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);">
                    <div class="col-md-8 col-sm-12 mb-4 text-center">
                        <h3>Order Trends (Last 6 Months)</h3>
                        <canvas id="orderTrendsChart"></canvas>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <!-- Order Trends -->
    <?php

    $monthLabels = [];
    $monthlyTotals = [];

    // Get today's date and loop back 6 months
    for ($i = 5; $i >= 0; $i--) {
        $date = new DateTime();
        $date->modify("-$i months");
        $month = $date->format('m');
        $year = $date->format('Y');
        $label = $date->format('M'); // e.g., Jan, Feb

        // Add to labels
        $monthLabels[] = "'$label'";

        // Fetch sum for this month
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM retail_store_stock_request WHERE MONTH(date) = ? AND YEAR(date) = ? AND request_to = '$user_name'");
        $stmt->bind_param("ii", $month, $year);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $total = $result['total'] ?: 0;
        $monthlyTotals[] = $total;
    }

    // Output comma-separated values
    $labelsStr = implode(", ", $monthLabels);
    $totalsStr = implode(", ", $monthlyTotals);
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const lineCtx = document.getElementById('orderTrendsChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: [<?= $labelsStr ?>],
                datasets: [{
                    label: 'Revenue',
                    data: [<?= $totalsStr ?>],
                    fill: false,
                    borderColor: '#0d6efd',
                    backgroundColor: '#0d6efd',
                    tension: 0.3,
                    pointRadius: 5,
                    pointHoverRadius: 6
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
                            stepSize: 150000
                        }
                    }
                }
            }
        });
    </script>
    <script>
        // Sidebar Toggle
        const hamburger = document.getElementById('hamburger');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        hamburger.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        });

        // Close sidebar when clicking a nav link on mobile
        document.querySelectorAll('.sidebar nav a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('show');
                }
            });
        });
    </script>
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
        window.onclick = function(event) {
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

        function Redirect(){
            window.location.href ="vendor_dashboard.php?page=products"
        }
    </script>
</body>
</html>