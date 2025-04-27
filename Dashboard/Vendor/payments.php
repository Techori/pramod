<?php

// Include mock database
require_once 'database.php';

// Get payments from database
$payments = get_payments();

// Handle actions
$success_message = '';
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        if ($action === 'new_payment') {
            $success_message = 'Initiating a new payment process';
        } elseif ($action === 'download_transaction' && isset($_POST['transaction_id'])) {
            $transaction_id = trim($_POST['transaction_id']);
            $success_message = "Downloading transaction $transaction_id";
        } elseif ($action === 'view_transaction' && isset($_POST['transaction_id'])) {
            $transaction_id = trim($_POST['transaction_id']);
            $success_message = "Viewing transaction $transaction_id";
        }
    }
}

// Filter and search parameters
$active_tab = isset($_GET['tab']) && in_array($_GET['tab'], ['overview', 'transactions', 'reports', 'settings']) ? $_GET['tab'] : 'overview';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) && in_array($_GET['status'], ['All', 'Completed', 'Pending', 'Failed']) ? $_GET['status'] : 'All';
$date_range_filter = isset($_GET['date_range']) && in_array($_GET['date_range'], ['All Time', 'Last 30 Days', 'Last 3 Months', 'This Year']) ? $_GET['date_range'] : 'All Time';

// Filter payments
$filtered_payments = array_filter($payments, function ($payment) use ($search_query, $status_filter, $date_range_filter) {
    $matches_search = empty($search_query) || stripos($payment['id'], $search_query) !== false;
    $matches_status = $status_filter === 'All' || $payment['status'] === $status_filter;

    if ($date_range_filter === 'Last 30 Days') {
        $thirty_days_ago = (new DateTime())->modify('-30 days');
        return $matches_search && $matches_status && new DateTime($payment['date']) >= $thirty_days_ago;
    } elseif ($date_range_filter === 'Last 3 Months') {
        $three_months_ago = (new DateTime())->modify('-3 months');
        return $matches_search && $matches_status && new DateTime($payment['date']) >= $three_months_ago;
    } elseif ($date_range_filter === 'This Year') {
        $start_of_year = new DateTime(date('Y-01-01'));
        return $matches_search && $matches_status && new DateTime($payment['date']) >= $start_of_year;
    }

    return $matches_search && $matches_status;
});

// Statuses and date ranges for filter
$statuses = ['All', 'Completed', 'Pending', 'Failed'];
$date_ranges = ['All Time', 'Last 30 Days', 'Last 3 Months', 'This Year'];
?>

<h4><i class="fas fa-credit-card text-primary"></i> Payments</h4>
<p>Track payments and manage financial transactions.</p>

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

<!-- Header with New Payment Button -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <form method="POST" action="?page=payments&tab=<?php echo urlencode($active_tab); ?>">
        <input type="hidden" name="action" value="new_payment">
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> New Payment
        </button>
    </form>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link <?php echo $active_tab === 'overview' ? 'active' : ''; ?>" href="?page=payments&tab=overview">Overview</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $active_tab === 'transactions' ? 'active' : ''; ?>" href="?page=payments&tab=transactions">Transactions</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $active_tab === 'reports' ? 'active' : ''; ?>" href="?page=payments&tab=reports">Reports</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $active_tab === 'settings' ? 'active' : ''; ?>" href="?page=payments&tab=settings">Settings</a>
    </li>
</ul>

<!-- Overview Tab -->
<?php if ($active_tab === 'overview'): ?>
    <div class="mb-4">
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Total Revenue</h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="h3 font-weight-bold">₹5,40,000</p>
                                <p class="text-muted">Total amount received</p>
                            </div>
                            <div class="p-3 bg-success bg-opacity-10 rounded-circle">
                                <i class="fas fa-rupee-sign text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Pending Payments</h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="h3 font-weight-bold">₹18,750</p>
                                <p class="text-muted">Total amount pending</p>
                            </div>
                            <div class="p-3 bg-warning bg-opacity-10 rounded-circle">
                                <i class="fas fa-clock text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Payment Methods</h5>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="h3 font-weight-bold">4</p>
                                <p class="text-muted">Payment methods used</p>
                            </div>
                            <div class="p-3 bg-primary bg-opacity-10 rounded-circle">
                                <i class="fas fa-credit-card text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Recent Transactions</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $recent_payments = array_slice($payments, 0, 5);
                                foreach ($recent_payments as $payment):
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($payment['id']); ?></td>
                                <td>
                                    <?php
                                        $date = new DateTime($payment['date']);
                                        echo $date->format('M j, Y');
                                    ?>
                                </td>
                                <td>₹<?php echo number_format($payment['amount'], 0); ?></td>
                                <td>
                                    <span class="badge <?php
                                        echo $payment['status'] === 'Completed' ? 'bg-success' :
                                            ($payment['status'] === 'Pending' ? 'bg-warning' : 'bg-danger');
                                    ?> text-white">
                                        <i class="fas <?php
                                            echo $payment['status'] === 'Completed' ? 'fa-check-circle' :
                                                ($payment['status'] === 'Pending' ? 'fa-clock' : 'fa-times-circle');
                                        ?> me-1"></i>
                                        <?php echo htmlspecialchars($payment['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-end">
                    <a href="?page=payments&tab=transactions" class="btn btn-outline-primary btn-sm">
                        View All Transactions <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Transactions Tab -->
<?php if ($active_tab === 'transactions'): ?>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Payment Transactions</h5>
            <p class="text-muted">Manage and view all payment transactions.</p>

            <!-- Filters and Search -->
            <div class="d-flex flex-column flex-md-row gap-3 align-items-md-end mb-4">
                <!-- Search -->
                <div class="flex-grow-1">
                    <label class="form-label text-muted">Search Transactions</label>
                    <form method="GET" action="?page=payments" class="d-flex align-items-center gap-2">
                        <input type="hidden" name="page" value="payments">
                        <input type="hidden" name="tab" value="transactions">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                            <input
                                type="text"
                                name="search"
                                class="form-control border-start-0"
                                placeholder="Search transactions by ID..."
                                value="<?php echo htmlspecialchars($search_query); ?>"
                            >
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                    </form>
                </div>
                <!-- Filters -->
                <div class="d-flex flex-column gap-3">
                    <!-- Status Filter -->
                    <div>
                        <label class="form-label text-muted">Status</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($statuses as $status): ?>
                            <a href="?page=payments&tab=transactions&status=<?php echo urlencode($status); ?>&date_range=<?php echo urlencode($date_range_filter); ?>&search=<?php echo urlencode($search_query); ?>">
                                <span class="badge <?php echo $status_filter === $status ? 'bg-primary text-white' : 'bg-light text-dark'; ?> px-3 py-1 rounded-pill">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- Date Range Filter -->
                    <div>
                        <label class="form-label text-muted">Date Range</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($date_ranges as $range): ?>
                            <a href="?page=payments&tab=transactions&status=<?php echo urlencode($status_filter); ?>&date_range=<?php echo urlencode($range); ?>&search=<?php echo urlencode($search_query); ?>">
                                <span class="badge <?php echo $date_range_filter === $range ? 'bg-primary text-white' : 'bg-light text-dark'; ?> px-3 py-1 rounded-pill">
                                    <?php echo htmlspecialchars($range); ?>
                                </span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <!-- Clear Filters -->
                <div>
                    <a href="?page=payments&tab=transactions" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-filter me-1"></i> Clear Filters
                    </a>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($filtered_payments)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-file-alt fa-2x text-muted"></i>
                                    <p class="mt-2 text-muted">No transactions found matching your criteria.</p>
                                    <a href="?page=payments&tab=transactions" class="btn btn-outline-primary btn-sm">Clear Filters</a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($filtered_payments as $payment): ?>
                            <tr>
                                <td><a href="#" class="text-primary"><?php echo htmlspecialchars($payment['id']); ?></a></td>
                                <td>
                                    <?php
                                        $date = new DateTime($payment['date']);
                                        echo $date->format('M j, Y');
                                    ?>
                                </td>
                                <td>₹<?php echo number_format($payment['amount'], 0); ?></td>
                                <td><?php echo htmlspecialchars($payment['method']); ?></td>
                                <td>
                                    <span class="badge <?php
                                        echo $payment['status'] === 'Completed' ? 'bg-success' :
                                            ($payment['status'] === 'Pending' ? 'bg-warning' : 'bg-danger');
                                    ?> text-white">
                                        <i class="fas <?php
                                            echo $payment['status'] === 'Completed' ? 'fa-check-circle' :
                                                ($payment['status'] === 'Pending' ? 'fa-clock' : 'fa-times-circle');
                                        ?> me-1"></i>
                                        <?php echo htmlspecialchars($payment['status']); ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <form method="POST" action="?page=payments&tab=transactions" class="d-inline">
                                            <input type="hidden" name="action" value="view_transaction">
                                            <input type="hidden" name="transaction_id" value="<?php echo htmlspecialchars($payment['id']); ?>">
                                            <button type="submit" class="btn btn-outline-primary btn-sm" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="?page=payments&tab=transactions" class="d-inline">
                                            <input type="hidden" name="action" value="download_transaction">
                                            <input type="hidden" name="transaction_id" value="<?php echo htmlspecialchars($payment['id']); ?>">
                                            <button type="submit" class="btn btn-outline-success btn-sm" title="Download">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </form>
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
<?php endif; ?>

<!-- Reports Tab -->
<?php if ($active_tab === 'reports'): ?>
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Payment Reports</h5>
            <p class="text-muted">Generate and view payment reports.</p>
            <p>Coming Soon...</p>
        </div>
    </div>
<?php endif; ?>

<!-- Settings Tab -->
<?php if ($active_tab === 'settings'): ?>
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Payment Settings</h5>
            <p class="text-muted">Configure payment settings.</p>
            <p>Coming Soon...</p>
        </div>
    </div>
<?php endif; ?>

<!-- Payment Process -->
<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title">Payment Process</h5>
        <div class="mt-3">
            <div class="d-flex align-items-center mb-3">
                <div class="p-2 bg-primary bg-opacity-10 rounded-circle">
                    <i class="fas fa-check text-primary"></i>
                </div>
                <div class="ms-3">
                    <p class="font-weight-bold mb-0">Select payment method</p>
                    <p class="text-muted small">Choose your preferred payment option</p>
                </div>
            </div>
            <div class="d-flex align-items-center mb-3">
                <div class="p-2 bg-success bg-opacity-10 rounded-circle">
                    <i class="fas fa-check text-success"></i>
                </div>
                <div class="ms-3">
                    <p class="font-weight-bold mb-0">Enter payment details</p>
                    <p class="text-muted small">Provide necessary payment information</p>
                </div>
            </div>
            <div class="d-flex align-items-center mb-3">
                <div class="p-2 bg-warning bg-opacity-10 rounded-circle">
                    <i class="fas fa-check text-warning"></i>
                </div>
                <div class="ms-3">
                    <p class="font-weight-bold mb-0">Review and confirm</p>
                    <p class="text-muted small">Verify the payment details before submitting</p>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <div class="p-2 bg-secondary bg-opacity-10 rounded-circle">
                    <i class="fas fa-check text-secondary"></i>
                </div>
                <div class="ms-3">
                    <p class="font-weight-bold mb-0">Payment successful</p>
                    <p class="text-muted small">Confirmation of successful payment</p>
                </div>
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