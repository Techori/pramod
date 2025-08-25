<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../_conn.php';
$user_name = $_SESSION['user_name'];

// Authentication check
if (!(isset($_SESSION["uid"]) && isset($_SESSION["user_type"]) && isset($_SESSION["session_id"]))) {
    header("location:../../login.php");
    exit;
} else {
    if (in_array($_SESSION["user_type"], ['Factory', 'Admin', 'Vendor'])) {
        header("location:../index.php");
        exit;
    } else if ($_SESSION["user_type"] != 'Store') {
        header("location:../../login.php");
        exit;
    }
}

// Page routing
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$valid_pages = ['dashboard', 'billing', 'supply', 'inventory', 'customers', 'orders', 'payments', 'after_service', 'reports', 'settings'];
if (!in_array($page, $valid_pages)) {
    $page = 'dashboard';
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['whatAction'])) {

    function clean($input)
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    if ($_POST['whatAction'] === 'process_return') {
        // Collect data for transaction
        $invoice_id = clean($_POST['invoice_id']);
        $status = 'Refund';

        // Start database transaction
        $conn->begin_transaction();

        try {

            // Insert the transaction record
            $stmt = $conn->prepare("UPDATE invoice SET 
            status = '$status' WHERE invoice_id = ?");

            $stmt->bind_param("s", $invoice_id);
            $stmt->execute();

            $conn->commit();
            $stmt->close();

            header("Location: store_dashboard.php?page=dashboard");
            exit;

        } catch (Exception $e) {
            $conn->rollback();
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Return entry failed: " . $e->getMessage()
            ]);
            exit;
        }
    }
}


// Include database connection
require_once 'database.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retail Store Dashboard - Shree Unnati Wires & Traders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../public/css/styles.css">
</head>

<body>
    <?php include '_retail_nav.php'; ?>

    <!-- Main Content -->
    <main>
        <?php if ($page === 'dashboard'): ?>
            <div class="main-content">
                <h1>Retail Store Dashboard</h1>
                <p>Manage store operations, sales, and inventory</p>


                <?php

                $currentMonth = date('Y-m');
                $lastMonth = date('Y-m', strtotime('-1 month'));

                // 1. Store Visitors
                function getStoreVisitors($conn, $user_name, $month)
                {
                    $query = "SELECT COUNT(DISTINCT TRIM(LOWER(customer_name))) as count FROM invoice WHERE created_for = ? AND DATE_FORMAT(date, '%Y-%m') = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ss", $user_name, $month);
                    $stmt->execute();
                    return $stmt->get_result()->fetch_assoc()['count'] ?? 0;
                }
                $visitorsCurrent = getStoreVisitors($conn, $user_name, $currentMonth);
                $visitorsLast = getStoreVisitors($conn, $user_name, $lastMonth);
                $visitorsChange = ($visitorsLast > 0) ? round((($visitorsCurrent - $visitorsLast) / $visitorsLast) * 100, 1) : 0;

                // 2. Pending Orders (no need for last month comparison here)
                $query2 = "SELECT COUNT(*) as count FROM invoice WHERE created_for = ? AND status = 'Pending'";
                $stmt2 = $conn->prepare($query2);
                $stmt2->bind_param("s", $user_name);
                $stmt2->execute();
                $pending_orders = $stmt2->get_result()->fetch_assoc()['count'] ?? 0;

                // 3. Average Basket
                function getAvgBasket($conn, $user_name, $month)
                {
                    $query = "SELECT AVG(grand_total) as avg FROM invoice WHERE created_for = ? AND DATE_FORMAT(date, '%Y-%m') = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ss", $user_name, $month);
                    $stmt->execute();
                    return round($stmt->get_result()->fetch_assoc()['avg'] ?? 0, 2);
                }
                $avgCurrent = getAvgBasket($conn, $user_name, $currentMonth);
                $avgLast = getAvgBasket($conn, $user_name, $lastMonth);
                $avgChange = ($avgLast > 0) ? round((($avgCurrent - $avgLast) / $avgLast) * 100, 1) : 0;
                ?>

                <!-- Cards Row 1 -->
                <div class="row">
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
                            <div class="card-body">
                                <h6 class="text-muted">Store Visitors</h6>
                                <h3 class="fw-bold"><?php echo $visitorsCurrent; ?></h3>
                                <p class="<?php echo ($visitorsChange >= 0) ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo ($visitorsChange >= 0 ? '+' : '') . $visitorsChange; ?>% vs last month
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
                            <div class="card-body">
                                <h6 class="text-muted">Pending Orders</h6>
                                <h3 class="fw-bold"><?php echo $pending_orders; ?></h3>
                                <p>Live unpaid orders</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
                            <div class="card-body">
                                <h6 class="text-muted">Average Basket</h6>
                                <h3 class="fw-bold">₹<?php echo number_format($avgCurrent); ?></h3>
                                <p class="<?php echo ($avgChange >= 0) ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo ($avgChange >= 0 ? '+' : '') . $avgChange; ?>% vs last month
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                date_default_timezone_set('Asia/Kolkata');

                function getSalesAmount($conn, $user_name, $startDate, $endDate)
                {
                    $query = "SELECT SUM(grand_total) as total 
              FROM invoice 
              WHERE created_for = ? 
              AND DATE(date) BETWEEN ? AND ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("sss", $user_name, $startDate, $endDate);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    return $result->fetch_assoc()['total'] ?? 0;
                }

                function getPercentageChange($current, $previous)
                {
                    if ($previous == 0)
                        return $current > 0 ? 100 : 0;
                    return round((($current - $previous) / $previous) * 100, 1);
                }

                // === TODAY ===
                $today = date('Y-m-d');
                $yesterday = date('Y-m-d', strtotime('-1 day'));

                $todaySales = getSalesAmount($conn, $user_name, $today, $today);
                $yesterdaySales = getSalesAmount($conn, $user_name, $yesterday, $yesterday);
                $todayChange = getPercentageChange($todaySales, $yesterdaySales);

                // === WEEK ===
                $mondayThisWeek = date('Y-m-d', strtotime('monday this week'));
                $todayDate = date('Y-m-d');

                $mondayLastWeek = date('Y-m-d', strtotime('monday last week'));
                $sundayLastWeek = date('Y-m-d', strtotime('sunday last week'));

                $thisWeekSales = getSalesAmount($conn, $user_name, $mondayThisWeek, $todayDate);
                $lastWeekSales = getSalesAmount($conn, $user_name, $mondayLastWeek, $sundayLastWeek);
                $weekChange = getPercentageChange($thisWeekSales, $lastWeekSales);

                // === MONTH ===
                $firstDayThisMonth = date('Y-m-01');
                $firstDayLastMonth = date('Y-m-01', strtotime('first day of last month'));
                $lastDayLastMonth = date('Y-m-t', strtotime('last month'));

                $thisMonthSales = getSalesAmount($conn, $user_name, $firstDayThisMonth, $todayDate);
                $lastMonthSales = getSalesAmount($conn, $user_name, $firstDayLastMonth, $lastDayLastMonth);
                $monthChange = getPercentageChange($thisMonthSales, $lastMonthSales);
                ?>


                <!-- Cards Row 2 -->
                <div class="row">
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
                            <div class="card-body">
                                <h6 class="text-muted">Today's Sales</h6>
                                <h3 class="fw-bold">₹<?= number_format($todaySales) ?></h3>
                                <p class="<?= $todayChange >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= ($todayChange >= 0 ? '+' : '') . $todayChange ?>% vs last month
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
                            <div class="card-body">
                                <h6 class="text-muted">Week Sales</h6>
                                <h3 class="fw-bold">₹<?= number_format($thisWeekSales) ?></h3>
                                <p class="<?= $weekChange >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= ($weekChange >= 0 ? '+' : '') . $weekChange ?>% vs last month
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
                            <div class="card-body">
                                <h6 class="text-muted">Month Sales</h6>
                                <h3 class="fw-bold">₹<?= number_format($thisMonthSales) ?></h3>
                                <p class="<?= $monthChange >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= ($monthChange >= 0 ? '+' : '') . $monthChange ?>% vs last month
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bar chart & alerts -->
                <div class="row mb-4"
                    style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);">
                    <div class="col-md-8 col-sm-12 mb-4 text-center">
                        <h3>Sales Performance (Last 7 Days)</h3>
                        <p>Daily revenue breakdown</p>
                        <canvas id="barChart"></canvas>
                    </div>
                </div>

                <!-- Pie charts -->
                <div class="chart-container mb-4">
                    <div class="chart-box">
                        <h3>Inventory Stock</h3>
                        <div style="position: relative; width: 100%; max-width: 300px; margin: 0 auto;">
                            <canvas id="productChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-box">
                        <h3>Sales by Payment Method</h3>
                        <div style="position: relative; width: 100%; max-width: 300px; margin: 0 auto;">
                            <canvas id="salesByCategory"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Inventory Status -->
                <div class="card mb-4">
                    <div class="alert-card p-3">
                        <?php

                        // Fetch items for the user
                        $sql = "SELECT item_name, stock, reorder_point FROM retail_invetory WHERE inventory_of = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("s", $user_name);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Loop through items and calculate percentage
                        while ($row = $result->fetch_assoc()) {
                            $itemName = htmlspecialchars($row['item_name']);
                            $stock = (int) $row['stock'];
                            $reorderPoint = (int) $row['reorder_point'];

                            // Prevent division by zero
                            $maxStock = max($reorderPoint * 2, 1); // Optional logic: Max stock is double of reorder point
                            $percentage = min(100, ($stock / $maxStock) * 100);
                            ?>

                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span class="stock-label"><?= $itemName ?></span>
                                    <span class="stock-count"><?= $stock ?> unit left</span>
                                </div>
                                <div class="progress bg-light">
                                    <div class="progress-bar bg-primary" style="width: <?= $percentage ?>%"></div>
                                    <div class="progress-bar bg-warning" style="width: <?= 100 - $percentage ?>%"></div>
                                </div>
                            </div>

                            <?php
                        }
                        $stmt->close();
                        ?>
                    </div>
                </div>

                <!-- Quick Access -->
                <div class="row justify-content-center p-3 bg-body rounded-3 mb-4 m-2"
                    style="box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);">
                    <h5 class="mb-4">Quick Access</h5>
                    <!-- <div class="col-md-2 col-sm-6 mb-4">
                        <a href="?page=billing" class="btn btn-outline-primary btn-lg w-100"><i
                                class="fa-regular fa-file-lines"></i> Create Invoice</a>
                    </div> -->
                    <div class="col-md-3 col-sm-6 mb-4">
                        <a href="?page=inventory" class="btn btn-outline-primary btn-lg w-100"><i
                                class="fa-solid fa-box"></i> Check Inventory</a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <button type="submit" class="btn btn-outline-primary btn-lg w-100" data-bs-toggle="modal"
                            data-bs-target="#processReturn">
                            <i class="fas fa-user-plus me-1"></i> Process Return
                        </button>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <button type="submit" class="btn btn-outline-primary btn-lg w-100" data-bs-toggle="modal"
                            data-bs-target="#addCustomer">
                            <i class="fas fa-user-plus me-1"></i> Add Customer
                        </button>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <a href="?page=reports" class="btn btn-outline-primary btn-lg w-100"><i
                                class="fa-regular fa-file"></i> View Reports</a>
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

                <!-- Recent Sales Table -->
                <div class="card p-3 shadow-sm my-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Recent Transactions</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
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
                                                    <button class="btn btn-outline-primary btn-sm view-invoice"><i class="fa-regular fa-eye"></i></button>
                                                    <button class="btn btn-outline-primary btn-sm print-invoice"><i class="fa-solid fa-print"></i></button>';
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

                <!-- Process return Form -->
                <div class="modal fade" id="processReturn" tabindex="-1" aria-labelledby="processReturnLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form action="store_dashboard.php" method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="processReturnLabel">Process Return</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>

                                <div class="modal-body">

                                    <div class="mb-3">
                                        <label for="name" class="form-label">Invoice Id</label>
                                        <input type="text" class="form-control" id="invoice_id" name="invoice_id" required>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary" name="whatAction"
                                            value="process_return">Submit</button>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Invoice Modal Template -->
                <div class="modal fade" id="invoiceModal" tabindex="-1">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content p-4" id="invoiceContent">
                            <!-- Bill content gets dynamically inserted here using JS -->
                        </div>
                    </div>
                </div>

            </div>

            <!-- Add Customer Form -->
            <div class="modal fade" id="addCustomer" tabindex="-1" aria-labelledby="addCustomerLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="customers.php" method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addCustomerLabel">Add Customer</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">

                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
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
                                    <label for="contact" class="form-label">Contact</label>
                                    <input type="tel" class="form-control" id="contact" name="contact" required>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary" name="whatAction"
                                        value="add_customer">Save
                                        Customer</button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>


        <?php else: ?>
            <?php
            $page_file = $page . '.php';
            if (file_exists($page_file)) {
                include $page_file;
            } else {
                echo '<div class="container-fluid"><h1>Page Not Found</h1><p>The requested page is not available.</p></div>';
            }
            ?>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sales Performance -->
    <?php
    date_default_timezone_set('Asia/Kolkata');

    $labels = [];
    $salesData = [];

    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $dayName = date('D', strtotime($date)); // Mon, Tue, etc.
        $labels[] = $dayName;

        // Fetch total sales for this day
        $query = "SELECT SUM(grand_total) as total FROM invoice WHERE created_for = ? AND DATE(date) = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $user_name, $date);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $salesData[] = (int) ($result['total'] ?? 0);
    }
    ?>

    <!-- Inventory stock -->
    <?php

    // Step 1: Fetch distinct categories
    $category_query = $conn->prepare("SELECT DISTINCT category FROM retail_invetory WHERE inventory_of = ?");
    $category_query->bind_param("s", $user_name);
    $category_query->execute();
    $category_result = $category_query->get_result();

    $categoryLabels = [];
    $stockCounts = [];

    while ($row = $category_result->fetch_assoc()) {
        $category = $row['category'];
        $categoryLabels[] = $category;

        // Step 2: Fetch total stock for each category
        $stock_query = $conn->prepare("SELECT SUM(stock) as total FROM retail_invetory WHERE inventory_of = ? AND category = ?");
        $stock_query->bind_param("ss", $user_name, $category);
        $stock_query->execute();
        $stock_result = $stock_query->get_result()->fetch_assoc();

        $stockCounts[] = (int) ($stock_result['total'] ?? 0);
    }
    ?>



    <!-- Sales by payment method -->
    <?php
    $paymentLabels = [];
    $paymentCounts = [];

    $stmt = $conn->prepare("
    SELECT payment_method, SUM(grand_total) as total
    FROM invoice
    WHERE created_for = ?
    GROUP BY payment_method
");
    $stmt->bind_param("s", $user_name);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $paymentLabels[] = $row['payment_method'];
        $paymentCounts[] = (float) $row['total'];
    }
    $stmt->close();
    ?>

    <script>
        // Chart.js Scripts for Dashboard
        <?php if ($page === 'dashboard'): ?>
            // Bar Chart
            const barChartCtx = document.getElementById('barChart').getContext('2d');
            new Chart(barChartCtx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($labels) ?>,
                    datasets: [{
                        label: 'Daily Sales (₹)',
                        data: <?= json_encode($salesData) ?>,
                        backgroundColor: '#0d6efd',
                        borderColor: '#0d6efd',
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            // Product Chart (Pie)
            const productChartCtx = document.getElementById('productChart').getContext('2d');
            new Chart(productChartCtx, {
                type: 'pie',
                data: {
                    labels: <?= json_encode($categoryLabels) ?>,
                    datasets: [{
                        data: <?= json_encode($stockCounts) ?>,
                        backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#6f42c1']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });

            // Payment Chart (Pie)
            const pieCtx = document.getElementById('salesByCategory').getContext('2d');
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
                            '#fd7e14'
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
                                font: { size: 14 }
                            }
                        }
                    }
                }
            });
        <?php endif; ?>

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
        document.querySelectorAll('.view-invoice, .print-invoice, .download-invoice').forEach(button => {
            button.addEventListener('click', function () {
                const row = this.closest('tr');
                const invoice = JSON.parse(row.dataset.invoice);
                fetch('store_dashboard.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ invoice_id: invoice.invoice_id })
                })
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('invoiceContent').innerHTML = html;
                        const action = this.classList.contains('print-invoice') ? 'print' : this.classList.contains('download-invoice') ? 'download' : 'view';
                        if (action === 'view') {
                            new bootstrap.Modal(document.getElementById('invoiceModal')).show();
                        } else if (action === 'print') {
                            const printWindow = window.open('', '', 'width=900,height=650');
                            printWindow.document.write(html);
                            printWindow.document.close();
                            printWindow.focus();
                            printWindow.print();
                            printWindow.close();
                        } else if (action === 'download') {
                            const blob = new Blob([html], { type: 'application/pdf' });
                            const url = URL.createObjectURL(blob);
                            const link = document.createElement('a');
                            link.href = url;
                            link.download = `${invoice.invoice_id}.pdf`;
                            link.click();
                            URL.revokeObjectURL(url);
                        }
                    });
            });
        });
    </script>

    <?php
    // invoice_template.php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $invoice_id = $data['invoice_id'];

        $invoice = $conn->query("SELECT * FROM invoice WHERE invoice_id = '$invoice_id'")->fetch_assoc();
        $store = $conn->query("SELECT * FROM store_settings_general LIMIT 1")->fetch_assoc();
        $customer = $conn->query("SELECT * FROM customer WHERE customer_name = '" . $invoice['customer_name'] . "' LIMIT 1")->fetch_assoc();
        $settings = $conn->query("SELECT * FROM store_after_sales_settings LIMIT 1")->fetch_assoc();

        $isGST = strtolower($invoice['document_type']) === 'with GST';
        $isRefund = strtolower($invoice['status']) === 'Refund';

        ob_start();
        ?>
        <style>
            .watermark {
                position: absolute;
                top: 40%;
                left: 20%;
                transform: rotate(-45deg);
                font-size: 5em;
                color: red;
                opacity: 0.1;
                z-index: 0;
            }
        </style>
        <div class="position-relative">
            <?php if ($isRefund): ?>
                <div class="watermark">REFUND</div><?php endif; ?>
            <h2 class="text-center"><?php echo htmlspecialchars($store['store_name']); ?></h2>
            <p class="text-center">
                Store Code: <?php echo $store['store_code']; ?> | Phone: <?php echo $store['store_phone']; ?> <br>
                Email: <?php echo $store['store_email']; ?> <br>
                Address: <?php echo $store['store_address']; ?>
            </p>

            <h4>Invoice #: <?php echo $invoice['invoice_id']; ?></h4>
            <p>Date: <?php echo date('d-M-Y', strtotime($invoice['date'])); ?> | Due:
                <?php echo date('d-M-Y', strtotime($invoice['due_date'])); ?></p>

            <h5>Bill To:</h5>
            <p>
                <?php echo $customer['name']; ?><br>
                Phone: <?php echo $customer['contact']; ?>
            </p>

            <h5>Ship To:</h5>
            <p>
                <?php echo $customer['name']; ?><br>
                <?php echo $customer['contact']; ?><br>
            </p>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td><?php echo $invoice['description']; ?></td>
                        <td><?php echo $invoice['quantity']; ?></td>
                        <td>₹<?php echo number_format($invoice['price'], 2); ?></td>
                        <td>₹<?php echo number_format($invoice['total'], 2); ?></td>
                    </tr>
                </tbody>
            </table>

            <?php if ($isGST): ?>
                <p>GST (<?php echo $invoice['tax_rate']; ?>%): ₹<?php echo number_format($invoice['GST_amount'], 2); ?></p>
            <?php endif; ?>

            <h4>Grand Total: ₹<?php echo number_format($invoice['grand_total'], 2); ?></h4>
            <p><strong>Amount in Words:</strong> <?php echo ucwords(convert_number_to_words($invoice['grand_total'])); ?>
                Only</p>

            <h5>Notes</h5>
            <ul>
                <li>Return Period: <?php echo $settings['return_period']; ?></li>
                <li>Policy: <?php echo $settings['return_policy']; ?></li>
                <li>Condition: <?php echo $settings['returns_conditions']; ?></li>
            </ul>
        </div>
        <?php
        echo ob_get_clean();
        exit;
    }

    function convert_number_to_words($number)
    {
        // Dummy function: integrate number-to-word converter as needed
        return $number;
    }
    ?>
</body>

</html>