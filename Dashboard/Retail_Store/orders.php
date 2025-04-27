<?php
// Include mock database
require_once 'database.php';

// Get data from database
$orders = get_orders();
$order_analytics = get_order_analytics();

// Handle actions
$success_message = '';
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : '';
        switch ($action) {
            case 'create_order':
                $success_message = 'Create New Order operation initiated.';
                break;
            case 'export_orders':
                $success_message = 'Export Orders operation initiated.';
                break;
            case 'view_details':
                $success_message = "View Details for order #$order_id";
                break;
            case 'print_invoice':
                $success_message = "Print Invoice for order #$order_id";
                break;
            case 'email_customer':
                $success_message = "Send to Customer for order #$order_id";
                break;
            case 'cancel_order':
                $success_message = "Cancel Order for order #$order_id";
                break;
            case 'process_pending':
                $success_message = 'Process Pending Orders operation initiated.';
                break;
            case 'update_status':
                $success_message = 'Update Order Status operation initiated.';
                break;
            case 'manage_shipping':
                $success_message = 'Manage Shipping operation initiated.';
                break;
            case 'view_calendar':
                $success_message = 'View Order Calendar operation initiated.';
                break;
        }
    }
}

// Search and filter parameters
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) && in_array($_GET['status'], ['all', 'new', 'processing', 'ready', 'delivered', 'cancelled']) ? $_GET['status'] : 'all';

// Filter orders
$filtered_orders = array_filter($orders, function ($order) use ($search_query, $status_filter) {
    $matches_search = empty($search_query) ||
        stripos($order['id'], $search_query) !== false ||
        stripos($order['customer'], $search_query) !== false ||
        stripos($order['amount'], $search_query) !== false;
    $matches_status = $status_filter === 'all' || $order['status'] === $status_filter;
    return $matches_search && $matches_status;
});

// Status badge function
function get_status_badge($status) {
    $status_config = [
        'new' => ['class' => 'bg-primary text-white', 'label' => 'New'],
        'processing' => ['class' => 'bg-warning text-dark', 'label' => 'Processing'],
        'ready' => ['class' => 'bg-purple text-white', 'label' => 'Ready for Pickup'],
        'delivered' => ['class' => 'bg-success text-white', 'label' => 'Delivered'],
        'cancelled' => ['class' => 'bg-danger text-white', 'label' => 'Cancelled']
    ];
    $config = isset($status_config[$status]) ? $status_config[$status] : $status_config['new'];
    return "<span class='badge {$config['class']}'>{$config['label']}</span>";
}

// Payment status badge function
function get_payment_badge($status) {
    $status_config = [
        'paid' => ['class' => 'bg-success text-white', 'label' => 'Paid'],
        'pending' => ['class' => 'bg-warning text-dark', 'label' => 'Pending'],
        'refunded' => ['class' => 'bg-primary text-white', 'label' => 'Refunded']
    ];
    $config = isset($status_config[$status]) ? $status_config[$status] : $status_config['pending'];
    return "<span class='badge {$config['class']}'>{$config['label']}</span>";
}
?>

<div class="main-content">
    <h1><i class="fas fa-shopping-cart text-primary me-2"></i> Order Management</h1>
    <p class="text-muted">Track, process, and manage customer orders</p>

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

    <!-- Search and Actions -->
    <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center justify-content-between mb-4">
        <div class="flex-grow-1">
            <form method="GET" action="?page=orders" class="d-flex align-items-center gap-2">
                <input type="hidden" name="page" value="orders">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input
                        type="text"
                        name="search"
                        class="form-control border-start-0"
                        placeholder="Search orders by ID, customer, or amount..."
                        value="<?php echo htmlspecialchars($search_query); ?>"
                    >
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search me-1"></i> Search
                </button>
            </form>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <form method="POST" action="?page=orders" class="d-inline">
                <input type="hidden" name="action" value="create_order">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> New Order
                </button>
            </form>
            <form method="GET" action="?page=orders" class="d-inline">
                <input type="hidden" name="page" value="orders">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                    <option value="new" <?php echo $status_filter === 'new' ? 'selected' : ''; ?>>New</option>
                    <option value="processing" <?php echo $status_filter === 'processing' ? 'selected' : ''; ?>>Processing</option>
                    <option value="ready" <?php echo $status_filter === 'ready' ? 'selected' : ''; ?>>Ready for Pickup</option>
                    <option value="delivered" <?php echo $status_filter === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                    <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </form>
            <form method="POST" action="?page=orders" class="d-inline">
                <input type="hidden" name="action" value="export_orders">
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-file-alt me-1"></i> Export
                </button>
            </form>
        </div>
    </div>

    <!-- Order Statistics -->
    <div class="row row-cols-1 row-cols-md-4 g-4 mb-4">
        <div class="col">
            <div class="card stat-card card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">New Orders</h6>
                            <h3 class="fw-bold"><?php echo htmlspecialchars($order_analytics['newOrders']); ?></h3>
                        </div>
                        <i class="fas fa-shopping-cart fa-2x text-primary opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card card-border shadow-sm" style="border-left: 5px solid #ffc107;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Processing</h6>
                            <h3 class="fw-bold"><?php echo htmlspecialchars($order_analytics['processing']); ?></h3>
                        </div>
                        <i class="fas fa-clock fa-2x text-warning opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Ready for Pickup</h6>
                            <h3 class="fw-bold"><?php echo htmlspecialchars($order_analytics['readyForPickup']); ?></h3>
                        </div>
                        <i class="fas fa-box fa-2x text-purple opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card card-border shadow-sm" style="border-left: 5px solid #198754;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Delivered Today</h6>
                            <h3 class="fw-bold"><?php echo htmlspecialchars($order_analytics['deliveredToday']); ?></h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x text-success opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card card-border shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="mb-3">Recent Orders</h5>
            <p class="text-muted mb-3">Manage and process customer orders</p>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($filtered_orders)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-shopping-cart fa-2x text-muted"></i>
                                    <p class="mt-2 text-muted">No orders found matching your criteria.</p>
                                    <a href="?page=orders" class="btn btn-outline-primary btn-sm">Clear Filters</a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($filtered_orders as $order): ?>
                            <tr>
                                <td class="font-medium"><?php echo htmlspecialchars($order['id']); ?></td>
                                <td><?php echo htmlspecialchars($order['customer']); ?></td>
                                <td>
                                    <?php
                                        $date = new DateTime($order['date']);
                                        echo $date->format('M j, Y');
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($order['items']); ?></td>
                                <td>₹<?php echo number_format($order['amount'], 0); ?></td>
                                <td><?php echo get_status_badge($order['status']); ?></td>
                                <td><?php echo get_payment_badge($order['paymentStatus']); ?></td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <form method="POST" action="?page=orders" class="d-inline">
                                                    <input type="hidden" name="action" value="view_details">
                                                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-search me-2"></i> View Details
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" action="?page=orders" class="d-inline">
                                                    <input type="hidden" name="action" value="print_invoice">
                                                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-print me-2"></i> Print Invoice
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" action="?page=orders" class="d-inline">
                                                    <input type="hidden" name="action" value="email_customer">
                                                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-envelope me-2"></i> Email Customer
                                                    </button>
                                                </form>
                                            </li>
                                            <?php if ($order['status'] !== 'cancelled'): ?>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form method="POST" action="?page=orders" class="d-inline">
                                                    <input type="hidden" name="action" value="cancel_order">
                                                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-times me-2"></i> Cancel Order
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

    <!-- Quick Actions -->
    <div class="mb-4">
        <h5 class="mb-3">Quick Actions</h5>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
            <div class="col">
                <div class="card card-border shadow-sm border-2 border-dashed h-100" style="cursor: pointer;" onclick="document.getElementById('process_pending_form').submit();">
                    <div class="card-body p-4 text-center">
                        <i class="fas fa-clock fa-3x text-primary mb-3"></i>
                        <h6 class="font-medium">Process Pending</h6>
                        <p class="text-sm text-muted mt-1">Process pending orders</p>
                        <form id="process_pending_form" method="POST" action="?page=orders">
                            <input type="hidden" name="action" value="process_pending">
                        </form>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card card-border shadow-sm border-2 border-dashed h-100" style="cursor: pointer;" onclick="document.getElementById('update_status_form').submit();">
                    <div class="card-body p-4 text-center">
                        <i class="fas fa-sort fa-3x text-primary mb-3"></i>
                        <h6 class="font-medium">Update Status</h6>
                        <p class="text-sm text-muted mt-1">Change order status</p>
                        <form id="update_status_form" method="POST" action="?page=orders">
                            <input type="hidden" name="action" value="update_status">
                        </form>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card card-border shadow-sm border-2 border-2 border-dashed h-100" style="cursor: pointer;" onclick="document.getElementById('manage_shipping_form').submit();">
                    <div class="card-body p-4 text-center">
                        <i class="fas fa-truck fa-3x text-primary mb-3"></i>
                        <h6 class="font-medium">Manage Shipping</h6>
                        <p class="text-sm text-muted mt-1">Handle delivery logistics</p>
                        <form id="manage_shipping_form" method="POST" action="?page=orders">
                            <input type="hidden" name="action" value="manage_shipping">
                        </form>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card card-border shadow-sm border-2 border-dashed h-100" style="cursor: pointer;" onclick="document.getElementById('view_calendar_form').submit();">
                    <div class="card-body p-4 text-center">
                        <i class="fas fa-calendar-alt fa-3x text-primary mb-3"></i>
                        <h6 class="font-medium">Order Calendar</h6>
                        <p class="text-sm text-muted mt-1">View delivery schedule</p>
                        <form id="view_calendar_form" method="POST" action="?page=orders">
                            <input type="hidden" name="action" value="view_calendar">