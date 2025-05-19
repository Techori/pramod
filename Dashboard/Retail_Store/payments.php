<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../_conn.php';

$user_name = $_SESSION['user_name'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

        // Get data from the form
        $invoice_id = $_POST['invoice_id'] ?? '';
        $status = $_POST['status'] ?? '';

        // Basic validation
        if (!empty($invoice_id) && !empty($status)) {
            // Prepare and execute the update query
            $stmt = $conn->prepare("UPDATE invoice SET status = ? WHERE invoice_id = ?");
            $stmt->bind_param("ss", $status, $invoice_id);

            if ($stmt->execute()) {
                // Redirect or show success message
                header("Location: store_dashboard.php?page=payments"); // Replace with your actual page
                exit();
            } else {
                echo "Error updating status: " . $conn->error;
            }

            $stmt->close();
        } else {
            echo "Invalid input.";
        }
}


// Include mock database
require_once 'database.php';

// Get data from database
$transactions = get_transactions();
$payment_analytics = get_payment_analytics();
$payment_method_data = get_payment_method_data();
$daily_revenue_data = get_daily_revenue_data();
$payment_methods = get_payment_methods();

// Handle actions
$success_message = '';
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $payment_id = isset($_POST['payment_id']) ? $_POST['payment_id'] : '';
        switch ($action) {
            case 'process_new_payment':
                $success_message = 'Process New Payment operation initiated successfully.';
                break;
            case 'generate_payment_report':
                $success_message = 'Generate Payment Report operation initiated successfully.';
                break;
            case 'view_details':
                $success_message = "View Details for payment #$payment_id operation initiated successfully.";
                break;
            case 'print_receipt':
                $success_message = "Print Receipt for payment #$payment_id operation initiated successfully.";
                break;
            case 'send_receipt':
                $success_message = "Send Receipt for payment #$payment_id operation initiated successfully.";
                break;
            case 'mark_completed':
                $success_message = "Mark as Completed for payment #$payment_id operation initiated successfully.";
                break;
            case 'configure_upi':
                $success_message = 'Configure UPI Payments operation initiated successfully.';
                break;
            case 'configure_cash':
                $success_message = 'Configure Cash Payments operation initiated successfully.';
                break;
            case 'configure_card':
                $success_message = 'Configure Card Payments operation initiated successfully.';
                break;
            case 'configure_qr':
                $success_message = 'Configure QR Code Payments operation initiated successfully.';
                break;
            case 'process_payment':
                $success_message = 'Process Payment operation initiated successfully.';
                break;
            case 'check_payment_status':
                $success_message = 'Check Payment Status operation initiated successfully.';
                break;
            case 'generate_payment_reports':
                $success_message = 'Generate Payment Reports operation initiated successfully.';
                break;
            case 'reconcile_payments':
                $success_message = 'Reconcile Payments operation initiated successfully.';
                break;
            case 'search_payments':
                $search_query = isset($_POST['search']) ? trim($_POST['search']) : '';
                $success_message = "Searching for \"$search_query\"";
                break;
        }
    }
}

// Search and filter parameters
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_method = isset($_GET['method']) && in_array(strtolower($_GET['method']), ['all', 'upi', 'cash', 'card', 'credit']) ? strtolower($_GET['method']) : 'all';
$tab = isset($_GET['tab']) && in_array($_GET['tab'], ['transactions', 'analytics', 'methods']) ? $_GET['tab'] : 'transactions';

// Filter transactions
$filtered_transactions = array_filter($transactions, function ($transaction) use ($search_query, $filter_method) {
    $matches_search = empty($search_query) ||
        stripos($transaction['id'], $search_query) !== false ||
        stripos($transaction['customer'], $search_query) !== false ||
        stripos($transaction['amount'], $search_query) !== false ||
        stripos($transaction['orderId'], $search_query) !== false;
    $matches_method = $filter_method === 'all' || strtolower($transaction['method']) === $filter_method;
    return $matches_search && $matches_method;
});

// Method badge function
function get_method_badge($method)
{
    $method_config = [
        'UPI' => ['class' => 'bg-blue-subtle text-blue', 'label' => 'UPI', 'icon' => 'fa-mobile-alt'],
        'Cash' => ['class' => 'bg-green-subtle text-green', 'label' => 'Cash', 'icon' => 'fa-money-bill'],
        'Card' => ['class' => 'bg-purple-subtle text-purple', 'label' => 'Card', 'icon' => 'fa-credit-card'],
        'Credit' => ['class' => 'bg-warning-subtle text-warning', 'label' => 'Credit', 'icon' => 'fa-dollar-sign']
    ];
    $config = isset($method_config[$method]) ? $method_config[$method] : $method_config['Cash'];
    return "<span class='badge {$config['class']} d-flex align-items-center gap-1'><i class='fas {$config['icon']}'></i> {$config['label']}</span>";
}

// Status badge function
function get_status_badge($status)
{
    $status_config = [
        'Completed' => ['class' => 'bg-green-subtle text-green', 'label' => 'Completed', 'icon' => 'fa-check'],
        'Pending' => ['class' => 'bg-warning-subtle text-warning', 'label' => 'Pending', 'icon' => 'fa-sync'],
        'Failed' => ['class' => 'bg-danger-subtle text-danger', 'label' => 'Failed', 'icon' => 'fa-exclamation-circle']
    ];
    $config = isset($status_config[$status]) ? $status_config[$status] : $status_config['Pending'];
    return "<span class='badge {$config['class']} d-flex align-items-center gap-1'><i class='fas {$config['icon']}'></i> {$config['label']}</span>";
}
?>

<div class="main-content">
    <h1><i class="fas fa-credit-card text-primary me-2"></i> Payment System</h1>
    <p class="text-muted">Process payments and manage transactions</p>

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

    <?php
    $today = date("Y-m-d");

    // Prepare analytics array
    $payment_analytics = [
        'todaysTransactions' => 0,
        'todaysRevenue' => 0,
        'pendingPayments' => 0,
        'refundTransactions' => 0
    ];

    // Total transactions today
    $sql = "SELECT COUNT(*) AS count FROM invoice WHERE date = ? AND created_for = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $today, $user_name);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $payment_analytics['todaysTransactions'] = $count;
    $stmt->close();

    // Total revenue today (only successful)
    $sql = "SELECT SUM(grand_total) AS revenue FROM invoice WHERE status = 'Completed' AND date = ? AND created_for = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $today, $user_name);
    $stmt->execute();
    $stmt->bind_result($revenue);
    $stmt->fetch();
    $payment_analytics['todaysRevenue'] = $revenue ?? 0;
    $stmt->close();

    // Pending payments count
    $sql = "SELECT COUNT(*) AS pending FROM invoice WHERE status = 'Pending' AND created_for = '$user_name'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $payment_analytics['pendingPayments'] = $row['pending'] ?? 0;

    // Refund transactions count
    $sql = "SELECT COUNT(*) AS refund FROM invoice WHERE status = 'Refund' AND created_for = '$user_name'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $payment_analytics['refundTransactions'] = $row['refund'] ?? 0;

    ?>


    <!-- Payment Overview -->
    <div class="row row-cols-1 row-cols-md-4 g-4 mb-4">
        <div class="col">
            <div class="card stat-card card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Today's Transactions</h6>
                            <h3 class="fw-bold">
                                <?php echo htmlspecialchars($payment_analytics['todaysTransactions']); ?>
                            </h3>
                        </div>
                        <i class="fas fa-exchange-alt fa-2x text-primary opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card card-border shadow-sm" style="border-left: 5px solid #198754;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Today's Revenue</h6>
                            <h3 class="fw-bold">₹<?php echo number_format($payment_analytics['todaysRevenue'], 0); ?>
                            </h3>
                        </div>
                        <i class="fas fa-dollar-sign fa-2x text-success opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card card-border shadow-sm" style="border-left: 5px solid #ffc107;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Pending Payments</h6>
                            <h3 class="fw-bold"><?php echo htmlspecialchars($payment_analytics['pendingPayments']); ?>
                            </h3>
                        </div>
                        <i class="fas fa-exclamation-circle fa-2x text-warning opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card card-border shadow-sm" style="border-left: 5px solid #dc3545;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Refund Transactions</h6>
                            <h3 class="fw-bold">
                                <?php echo htmlspecialchars($payment_analytics['refundTransactions']); ?>
                            </h3>
                        </div>
                        <i class="fas fa-exclamation-circle fa-2x text-danger opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="card card-border shadow-sm mb-4">
        <div class="card-body p-4">
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a href="?page=payments&tab=transactions"
                        class="nav-link <?php echo $tab === 'transactions' ? 'active' : ''; ?>">Transactions</a>
                </li>
                <li class="nav-item">
                    <a href="?page=payments&tab=analytics"
                        class="nav-link <?php echo $tab === 'analytics' ? 'active' : ''; ?>">Analytics</a>
                </li>
                <li class="nav-item">
                    <a href="?page=payments&tab=methods"
                        class="nav-link <?php echo $tab === 'methods' ? 'active' : ''; ?>">Payment Methods</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Transactions Tab -->
    <?php if ($tab === 'transactions'): ?>
        <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center justify-content-between mb-4">
            <div class="flex-grow-1">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 table-search"
                        data-table="transaction" placeholder="Search payments by ID, customer, or amount...">
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <input type="hidden" name="action" value="generate_payment_report">
                <button type="submit" class="btn btn-outline-primary btn-sm" onclick="exportTableToCSV()">
                    <i class="fas fa-file-alt me-1"></i> Export
                </button>
            </div>
        </div>

        <div class="card card-border shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="mb-3">Recent Transactions</h5>
                <p class="text-muted mb-3">Process and manage payment transactions</p>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="transaction">
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
                                                <form method="POST" action="payments.php">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="statusModalLabel<?= $id ?>">Update Status</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
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
                                                            <button type="submit" class="btn btn-primary">Update</button>
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
                    <script>
                        // Export table data to CSV
                        function exportTableToCSV(filename = 'table-data.csv') {
                            const rows = document.querySelectorAll("#transaction tr");
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
    <?php elseif ($tab === 'analytics'): ?>
        <div class="row row-cols-1 row-cols-lg-2 g-4 mb-4">
            <div class="col">
                <div class="card card-border shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="mb-3">Daily Revenue (Last 7 Days)</h5>
                        <div class="bg-light rounded py-5 text-center">
                            <canvas id="barChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card card-border shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="mb-3">Payment Methods</h5>
                        <div class="bg-light rounded py-5 text-center"
                            style="position: relative; width: 100%; max-width: 300px; margin: 0 auto;">
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // Payment methods to track
        $methods = ['Digital Payment', 'Cash', 'BNPL', 'Payment Gateway'];
        $sales = array_fill_keys($methods, 0);
        $total_sales = 0;

        // Query the database
        $query = "
    SELECT payment_method, SUM(grand_total) AS total_sales
    FROM invoice
    WHERE date >= CURDATE() - INTERVAL 6 DAY AND created_for = '$user_name'
    GROUP BY payment_method";

        $result = $conn->query($query);

        while ($row = $result->fetch_assoc()) {
            $method = $row['payment_method'];
            $amount = (float) $row['total_sales'];
            if (isset($sales[$method])) {
                $sales[$method] = $amount;
                $total_sales += $amount;
            }
        }

        // Calculate percentages
        $percentages = [];
        foreach ($sales as $method => $amount) {
            $percentages[$method] = $total_sales > 0 ? ($amount / $total_sales) * 100 : 0;
        }
        ?>


        <div class="card">
            <div class="alert-card p-2">
                <h5 class="mb-2">Payment Analytics</h5>

                <?php foreach ($sales as $method => $amount):
                    $percentage = $percentages[$method];
                    $class = strtolower(str_replace(' ', '-', $method)); // e.g., 'digital-payment'
                    ?>

                    <!-- Digital Payment -->
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span class="stock-label"><?= htmlspecialchars($method) ?></span>
                            <span class="stock-count"><?= number_format($percentage, 2) ?>%</span>
                        </div>
                        <div class="progress bg-light">
                            <div class="progress-bar bg-primary" style="width: <?= $percentage ?>%"></div>
                            <div class="progress-bar bg-warning" style="width: <?= 100 - $percentage ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php elseif ($tab === 'methods'): ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mb-4">
            <div class="col">
                <div class="card card-border shadow-sm border-2 border-primary-hover">
                    <div class="card-body p-4 text-center">
                        <div
                            class="d-flex align-items-center justify-content-center w-16 h-16 rounded-circle bg-blue-subtle mb-3">
                            <i class="fas fa-mobile-alt fa-2x text-blue"></i>
                        </div>
                        <h5 class="font-medium mb-2">UPI Payments</h5>
                        <p class="text-sm text-muted mb-3">Accept payments via UPI apps</p>
                        <span class="badge bg-green-subtle text-green">Active</span>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 pt-0 pb-4 px-4">
                        <form method="POST" action="?page=payments">
                            <input type="hidden" name="action" value="configure_upi">
                            <button type="submit" class="btn btn-outline-primary btn-sm w-100">Configure</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card card-border shadow-sm border-2 border-success-hover">
                    <div class="card-body p-4 text-center">
                        <div
                            class="d-flex align-items-center justify-content-center w-16 h-16 rounded-circle bg-green-subtle mb-3">
                            <i class="fas fa-money-bill fa-2x text-green"></i>
                        </div>
                        <h5 class="font-medium mb-2">Cash Payments</h5>
                        <p class="text-sm text-muted mb-3">Collect and manage cash payments</p>
                        <span class="badge bg-green-subtle text-green">Active</span>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 pt-0 pb-4 px-4">
                        <form method="POST" action="?page=payments">
                            <input type="hidden" name="action" value="configure_cash">
                            <button type="submit" class="btn btn-outline-primary btn-sm w-100">Configure</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card card-border shadow-sm border-2 border-purple-hover">
                    <div class="card-body p-4 text-center">
                        <div
                            class="d-flex align-items-center justify-content-center w-16 h-16 rounded-circle bg-purple-subtle mb-3">
                            <i class="fas fa-credit-card fa-2x text-purple"></i>
                        </div>
                        <h5 class="font-medium mb-2">Card Payments</h5>
                        <p class="text-sm text-muted mb-3">Accept debit and credit cards</p>
                        <span class="badge bg-green-subtle text-green">Active</span>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 pt-0 pb-4 px-4">
                        <form method="POST" action="?page=payments">
                            <input type="hidden" name="action" value="configure_card">
                            <button type="submit" class="btn btn-outline-primary btn-sm w-100">Configure</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card card-border shadow-sm border-2 border-warning-hover">
                    <div class="card-body p-4 text-center">
                        <div
                            class="d-flex align-items-center justify-content-center w-16 h-16 rounded-circle bg-warning-subtle mb-3">
                            <i class="fas fa-qrcode fa-2x text-warning"></i>
                        </div>
                        <h5 class="font-medium mb-2">QR Code Payments</h5>
                        <p class="text-sm text-muted mb-3">Generate QR codes for payments</p>
                        <span class="badge bg-green-subtle text-green">Active</span>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 pt-0 pb-4 px-4">
                        <form method="POST" action="?page=payments">
                            <input type="hidden" name="action" value="configure_qr">
                            <button type="submit" class="btn btn-outline-primary btn-sm w-100">Configure</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Quick Actions -->
    <div class="mb-4">
        <h2 class="h5 font-semibold mb-3">Quick Actions</h2>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
            <div class="col">
                <div class="card card-border shadow-sm border-2 border-dashed border-primary-hover">
                    <div class="card-body p-4 text-center">
                        <i class="fas fa-plus fa-3x text-primary mb-3"></i>
                        <h5 class="font-medium">Process Payment</h5>
                        <p class="text-sm text-muted mt-1">Create a new invoice</p>
                    </div>
                    <button type="submit" class="btn btn-outline-primary btn-sm w-100 border-0"
                        onclick="openInvoiceModal(event)" id="invoice">Select</button>
                </div>
            </div>

            <div class="col">
                <div class="card card-border shadow-sm border-2 border-dashed border-primary-hover">
                    <div class="card-body p-4 text-center">
                        <i class="fas fa-chart-bar fa-3x text-primary mb-3"></i>
                        <h5 class="font-medium">Payment Reports</h5>
                        <p class="text-sm text-muted mt-1">Generate payment reports</p>
                    </div>
                    <form method="POST" action="?page=payments">
                        <input type="hidden" name="action" value="generate_payment_reports">
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100 border-0">Select</button>
                    </form>
                </div>
            </div>
            <div class="col">
                <div class="card card-border shadow-sm border-2 border-dashed border-primary-hover">
                    <div class="card-body p-4 text-center">
                        <i class="fas fa-exchange-alt fa-3x text-primary mb-3"></i>
                        <h5 class="font-medium">Reconciliation</h5>
                        <p class="text-sm text-muted mt-1">Reconcile payment records</p>
                    </div>
                    <form method="POST" action="?page=payments">
                        <input type="hidden" name="action" value="reconcile_payments">
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100 border-0">Select</button>
                    </form>
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
                                $result = $conn->query("SELECT name FROM customer ORDER BY customer_Id DESC");

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option>" . $row['name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
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
                        <div class="col-md-4">
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
    });
</script>



<!-- To get data for bar chart -->
<?php

// Get sales for the last 7 days
$query = "
SELECT 
DATE(date) as sale_date,
SUM(grand_total) as revenue 
FROM invoice 
WHERE date >= CURDATE() - INTERVAL 6 DAY AND status = 'Completed' AND created_for = '$user_name'
GROUP BY sale_date 
ORDER BY sale_date ASC";

$result = $conn->query($query);

$labels = [];
$revenue = [];

while ($row = $result->fetch_assoc()) {
    $date = date("d-M (D)", strtotime($row['sale_date'])); // e.g., 01-May (Wed)
    $labels[] = $date;
    $revenue[] = $row['revenue'];
}
?>

<?php

// Define category list (payment methods in your case)
$categories = ['Digital Payment', 'Cash', 'BNPL', 'Payment Gateway'];

// Initialize sales array with zeros
$sales = array_fill(0, count($categories), 0);

// Query to fetch sales by payment method for the last 6 days
$query = "
    SELECT payment_method, SUM(grand_total) AS total_sales 
    FROM invoice 
    WHERE date >= CURDATE() - INTERVAL 6 DAY AND created_for = '$user_name'
    GROUP BY payment_method";

$result = $conn->query($query);

// Map results to predefined categories (payment methods)
while ($row = $result->fetch_assoc()) {
    // Get the index of the payment method in the categories array
    $index = array_search($row['payment_method'], $categories);

    // If the payment method is found in the categories array, update the sales
    if ($index !== false) {
        $sales[$index] = $row['total_sales'];
    }
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
                data: <?php echo json_encode($revenue); ?>,
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
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 6500
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
            labels: <?php echo json_encode($categories); ?>,
            datasets: [{
                data: <?php echo json_encode($sales); ?>,
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
</script>

<style>
    .w-120px {
        width: 120px;
    }

    .w-16 {
        width: 4rem;
    }

    .h-16 {
        height: 4rem;
    }

    .space-y-4>*+* {
        margin-top: 1rem;
    }

    .text-sm {
        font-size: 0.875rem;
    }

    .font-medium {
        font-weight: 500;
    }

    .font-bold {
        font-weight: 700;
    }

    .bg-blue-subtle {
        background-color: #e7f1ff !important;
    }

    .text-blue {
        color: #0d6efd !important;
    }

    .bg-green-subtle {
        background-color: #d4edda !important;
    }

    .text-green {
        color: #155724 !important;
    }

    .bg-purple-subtle {
        background-color: #e2d9f3 !important;
    }

    .text-purple {
        color: #6f42c1 !important;
    }

    .bg-warning-subtle {
        background-color: #fff3cd !important;
    }

    .text-warning {
        color: #664d03 !important;
    }

    .bg-danger-subtle {
        background-color: #f8d7da !important;
    }

    .text-danger {
        color: #842029 !important;
    }

    .border-primary-hover:hover {
        border-color: #0d6efd !important;
        background-color: rgba(13, 110, 253, 0.05) !important;
    }

    .border-success-hover:hover {
        border-color: #198754 !important;
        background-color: rgba(25, 135, 84, 0.05) !important;
    }

    .border-purple-hover:hover {
        border-color: #6f42c1 !important;
        background-color: rgba(111, 66, 193, 0.05) !important;
    }

    .border-warning-hover:hover {
        border-color: #ffc107 !important;
        background-color: rgba(255, 193, 7, 0.05) !important;
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

    let productsData = [];

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

    function setPriceAndUpdate(selectEl) {
        const selectedProduct = selectEl.value;
        const product = productsData.find(p => p.product_name === selectedProduct);
        if (product) {
            const priceInput = selectEl.closest('tr').querySelector('.price');
            priceInput.value = parseFloat(product.price).toFixed(2);
        }
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
</script>