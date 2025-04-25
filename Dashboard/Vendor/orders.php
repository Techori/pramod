<?php
require_once 'database.php';
$all_orders = get_orders();
$statuses = ["All Statuses", "New", "Processing", "Shipped", "Delivered", "Cancelled"];
$paymentStatuses = ["All Payments", "Paid", "Pending", "Partial", "Refunded"];

$search = isset($_GET['search']) ? $_GET['search'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : 'All Statuses';
$payment = isset($_GET['payment']) ? $_GET['payment'] : 'All Payments';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date';
$dir = isset($_GET['dir']) ? $_GET['dir'] : 'desc';

// Filter orders
$filtered_orders = array_filter($all_orders, function($order) use ($search, $status, $payment) {
    $search_match = stripos($order['id'], $search) !== false || stripos($order['customer'], $search) !== false;
    $status_match = $status === 'All Statuses' || $order['status'] === $status;
    $payment_match = $payment === 'All Payments' || $order['payment'] === $payment;
    return $search_match && $status_match && $payment_match;
});

// Sort orders
usort($filtered_orders, function($a, $b) use ($sort, $dir) {
    if ($sort === 'date' || $sort === 'deliveryDate') {
        $a_val = strtotime($a[$sort]);
        $b_val = strtotime($b[$sort]);
    } elseif ($sort === 'amount') {
        $a_val = $a[$sort];
        $b_val = $b[$sort];
    } else {
        $a_val = $a[$sort];
        $b_val = $b[$sort];
    }
    if ($a_val < $b_val) return $dir === 'asc' ? -1 : 1;
    if ($a_val > $b_val) return $dir === 'asc' ? 1 : -1;
    return 0;
});

// Functions for styling and icons
function get_status_class($status) {
    switch ($status) {
        case 'New': return 'bg-primary';
        case 'Processing': return 'bg-warning';
        case 'Shipped': return 'bg-info';
        case 'Delivered': return 'bg-success';
        case 'Cancelled': return 'bg-danger';
        default: return 'bg-secondary';
    }
}

function get_status_icon($status) {
    switch ($status) {
        case 'New': return 'fa-exclamation-circle';
        case 'Processing': return 'fa-sync';
        case 'Shipped': return 'fa-truck';
        case 'Delivered': return 'fa-check-circle';
        case 'Cancelled': return 'fa-times-circle';
        default: return 'fa-question';
    }
}

function get_payment_class($payment) {
    switch ($payment) {
        case 'Paid': return 'bg-success';
        case 'Pending': return 'bg-warning';
        case 'Partial': return 'bg-info';
        case 'Refunded': return 'bg-danger';
        default: return 'bg-secondary';
    }
}

function build_sort_url($field) {
    global $search, $status, $payment, $sort, $dir;
    $new_dir = ($sort === $field && $dir === 'asc') ? 'desc' : 'asc';
    $params = [
        'page' => 'orders',
        'search' => $search,
        'status' => $status,
        'payment' => $payment,
        'sort' => $field,
        'dir' => $new_dir
    ];
    return '?' . http_build_query($params);
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><i class="fas fa-shopping-cart text-primary"></i> Order Management</h1>
            <p>Track and manage vendor orders efficiently.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="alert('Generating orders report')"><i class="fas fa-file-alt"></i> Export</button>
            <button class="btn btn-primary" onclick="location.reload()"><i class="fas fa-sync"></i> Refresh</button>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="">
                <input type="hidden" name="page" value="orders">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search Orders</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by order ID or customer...">
                            <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Order Status</label>
                        <select class="form-select" id="status" name="status">
                            <?php foreach ($statuses as $s): ?>
                                <option value="<?php echo $s; ?>" <?php echo $status === $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="payment" class="form-label">Payment Status</label>
                        <select class="form-select" id="payment" name="payment">
                            <?php foreach ($paymentStatuses as $p): ?>
                                <option value="<?php echo $p; ?>" <?php echo $payment === $p ? 'selected' : ''; ?>><?php echo $p; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </div>
                </div>
                <div class="text-end">
                    <a href="?page=orders" class="btn btn-secondary">Clear Filters</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Orders (<?php echo count($filtered_orders); ?>)</h5>
            <?php if (empty($filtered_orders)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x text-muted"></i>
                    <p class="mt-2 text-muted">No orders found matching your criteria.</p>
                    <a href="?page=orders" class="btn btn-primary">Clear Filters</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th><a href="<?php echo build_sort_url('id'); ?>">Order ID <?php if ($sort === 'id') echo $dir === 'asc' ? '↑' : '↓'; ?></a></th>
                                <th><a href="<?php echo build_sort_url('customer'); ?>">Customer <?php if ($sort === 'customer') echo $dir === 'asc' ? '↑' : '↓'; ?></a></th>
                                <th><a href="<?php echo build_sort_url('date'); ?>">Order Date <?php if ($sort === 'date') echo $dir === 'asc' ? '↑' : '↓'; ?></a></th>
                                <th><a href="<?php echo build_sort_url('amount'); ?>">Amount <?php if ($sort === 'amount') echo $dir === 'asc' ? '↑' : '↓'; ?></a></th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($filtered_orders as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                                    <td><?php echo htmlspecialchars($order['customer']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($order['date'])); ?></td>
                                    <td>₹<?php echo number_format($order['amount'], 2); ?></td>
                                    <td>
                                        <span class="badge <?php echo get_status_class($order['status']); ?>">
                                            <i class="fas <?php echo get_status_icon($order['status']); ?>"></i> <?php echo $order['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo get_payment_class($order['payment']); ?>">
                                            <?php echo $order['payment']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="alert('Viewing order <?php echo $order['id']; ?>')"><i class="fas fa-eye"></i></button>
                                            <?php if ($order['status'] === 'New'): ?>
                                                <button class="btn btn-sm btn-outline-warning" onclick="alert('Processing order <?php echo $order['id']; ?>')"><i class="fas fa-sync"></i></button>
                                            <?php endif; ?>
                                            <?php if ($order['status'] === 'New' || $order['status'] === 'Processing'): ?>
                                                <button class="btn btn-sm btn-outline-info" onclick="alert('Shipping order <?php echo $order['id']; ?>')"><i class="fas fa-truck"></i></button>
                                            <?php endif; ?>
                                            <?php if ($order['status'] === 'Shipped'): ?>
                                                <button class="btn btn-sm btn-outline-success" onclick="alert('Marking order <?php echo $order['id']; ?> as delivered')"><i class="fas fa-check"></i></button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>