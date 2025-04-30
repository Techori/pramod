<?php
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
function get_method_badge($method) {
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
function get_status_badge($status) {
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

    <!-- Payment Overview -->
    <div class="row row-cols-1 row-cols-md-4 g-4 mb-4">
        <div class="col">
            <div class="card stat-card card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Today's Transactions</h6>
                            <h3 class="fw-bold"><?php echo htmlspecialchars($payment_analytics['todaysTransactions']); ?></h3>
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
                            <h3 class="fw-bold">₹<?php echo number_format($payment_analytics['todaysRevenue'], 0); ?></h3>
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
                            <h3 class="fw-bold"><?php echo htmlspecialchars($payment_analytics['pendingPayments']); ?></h3>
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
                            <h6 class="text-muted">Failed Transactions</h6>
                            <h3 class="fw-bold"><?php echo htmlspecialchars($payment_analytics['failedTransactions']); ?></h3>
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
                    <a href="?page=payments&tab=transactions" class="nav-link <?php echo $tab === 'transactions' ? 'active' : ''; ?>">Transactions</a>
                </li>
                <li class="nav-item">
                    <a href="?page=payments&tab=analytics" class="nav-link <?php echo $tab === 'analytics' ? 'active' : ''; ?>">Analytics</a>
                </li>
                <li class="nav-item">
                    <a href="?page=payments&tab=methods" class="nav-link <?php echo $tab === 'methods' ? 'active' : ''; ?>">Payment Methods</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Transactions Tab -->
    <?php if ($tab === 'transactions'): ?>
    <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center justify-content-between mb-4">
        <div class="flex-grow-1">
            <form method="POST" action="?page=payments" class="d-flex align-items-center gap-2">
                <input type="hidden" name="action" value="search_payments">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input
                        type="text"
                        name="search"
                        class="form-control border-start-0"
                        placeholder="Search payments by ID, customer, or amount..."
                        value="<?php echo htmlspecialchars($search_query); ?>"
                    >
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search me-1"></i> Search
                </button>
            </form>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <form method="POST" action="?page=payments" class="d-inline">
                <input type="hidden" name="action" value="process_new_payment">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> New Payment
                </button>
            </form>
            <form method="GET" action="?page=payments" class="d-inline">
                <input type="hidden" name="page" value="payments">
                <input type="hidden" name="tab" value="transactions">
                <select
                    name="method"
                    class="form-select form-select-sm"
                    style="width: 180px;"
                    onchange="this.form.submit()"
                >
                    <option value="all" <?php echo $filter_method === 'all' ? 'selected' : ''; ?>>All Methods</option>
                    <?php foreach ($payment_methods as $method): ?>
                        <option value="<?php echo strtolower(htmlspecialchars($method['value'])); ?>" <?php echo strtolower($filter_method) === strtolower($method['value']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($method['label']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
            <form method="POST" action="?page=payments" class="d-inline">
                <input type="hidden" name="action" value="generate_payment_report">
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-file-alt me-1"></i> Export
                </button>
            </form>
        </div>
    </div>

    <div class="card card-border shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="mb-3">Recent Transactions</h5>
            <p class="text-muted mb-3">Process and manage payment transactions</p>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="w-120px">Payment ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Order Ref.</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($filtered_transactions)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-credit-card fa-2x text-muted"></i>
                                    <p class="mt-2 text-muted">No transactions found matching your criteria.</p>
                                    <a href="?page=payments&tab=transactions" class="btn btn-outline-primary btn-sm">Clear Filters</a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($filtered_transactions as $transaction): ?>
                            <tr>
                                <td class="font-medium"><?php echo htmlspecialchars($transaction['id']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['customer']); ?></td>
                                <td>
                                    <?php
                                        $date = new DateTime($transaction['date']);
                                        echo $date->format('M j, Y');
                                    ?>
                                </td>
                                <td>₹<?php echo number_format($transaction['amount'], 0); ?></td>
                                <td><?php echo get_method_badge($transaction['method']); ?></td>
                                <td><?php echo get_status_badge($transaction['status']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['orderId']); ?></td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <form method="POST" action="?page=payments">
                                                    <input type="hidden" name="action" value="view_details">
                                                    <input type="hidden" name="payment_id" value="<?php echo htmlspecialchars($transaction['id']); ?>">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-search me-2"></i> View Details
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" action="?page=payments">
                                                    <input type="hidden" name="action" value="print_receipt">
                                                    <input type="hidden" name="payment_id" value="<?php echo htmlspecialchars($transaction['id']); ?>">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-print me-2"></i> Print Receipt
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" action="?page=payments">
                                                    <input type="hidden" name="action" value="send_receipt">
                                                    <input type="hidden" name="payment_id" value="<?php echo htmlspecialchars($transaction['id']); ?>">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-envelope me-2"></i> Email Receipt
                                                    </button>
                                                </form>
                                            </li>
                                            <?php if ($transaction['status'] === 'Pending'): ?>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form method="POST" action="?page=payments">
                                                        <input type="hidden" name="action" value="mark_completed">
                                                        <input type="hidden" name="payment_id" value="<?php echo htmlspecialchars($transaction['id']); ?>">
                                                        <button type="submit" class="dropdown-item text-success">
                                                            <i class="fas fa-check me-2"></i> Mark Completed
                                                        </button>
                                                    </form>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
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
                        <i class="fas fa-chart-bar fa-2x text-muted me-2"></i>
                        <span class="text-muted">Daily Revenue Bar Chart Placeholder</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Payment Methods</h5>
                    <div class="bg-light rounded py-5 text-center">
                        <i class="fas fa-chart-pie fa-2x text-muted me-2"></i>
                        <span class="text-muted">Payment Methods Pie Chart Placeholder</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card card-border shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="mb-3">Payment Analytics</h5>
            <div class="space-y-4">
                <?php foreach ($payment_method_data as $method): ?>
                    <div>
                        <div class="d-flex justify-content-between mb-1 text-sm">
                            <span><?php echo htmlspecialchars($method['name']); ?> Transactions</span>
                            <span><?php echo htmlspecialchars($method['value']); ?>%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div
                                class="progress-bar bg-primary"
                                role="progressbar"
                                style="width: <?php echo htmlspecialchars($method['value']); ?>%;"
                                aria-valuenow="<?php echo htmlspecialchars($method['value']); ?>"
                                aria-valuemin="0"
                                aria-valuemax="100"
                            ></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php elseif ($tab === 'methods'): ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mb-4">
        <div class="col">
            <div class="card card-border shadow-sm border-2 border-primary-hover">
                <div class="card-body p-4 text-center">
                    <div class="d-flex align-items-center justify-content-center w-16 h-16 rounded-circle bg-blue-subtle mb-3">
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
                    <div class="d-flex align-items-center justify-content-center w-16 h-16 rounded-circle bg-green-subtle mb-3">
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
                    <div class="d-flex align-items-center justify-content-center w-16 h-16 rounded-circle bg-purple-subtle mb-3">
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
                    <div class="d-flex align-items-center justify-content-center w-16 h-16 rounded-circle bg-warning-subtle mb-3">
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
                        <p class="text-sm text-muted mt-1">Create a new transaction</p>
                    </div>
                    <form method="POST" action="?page=payments">
                        <input type="hidden" name="action" value="process_payment">
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100 border-0">Select</button>
                    </form>
                </div>
            </div>
            <div class="col">
                <div class="card card-border shadow-sm border-2 border-dashed border-primary-hover">
                    <div class="card-body p-4 text-center">
                        <i class="fas fa-search fa-3x text-primary mb-3"></i>
                        <h5 class="font-medium">Check Status</h5>
                        <p class="text-sm text-muted mt-1">Verify payment status</p>
                    </div>
                    <form method="POST" action="?page=payments">
                        <input type="hidden" name="action" value="check_payment_status">
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100 border-0">Select</button>
                    </form>
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
</div>

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
.space-y-4 > * + * {
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