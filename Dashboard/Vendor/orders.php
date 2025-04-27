<?php

// Include mock database
require_once 'database.php';

// Get orders from database
$orders = get_orders();

// Handle form submission
$success_message = '';
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_order'])) {
    $factory_id = $_POST['factory_id'] ?? '';
    $factories = get_factories();
    $factory = array_filter($factories, function ($f) use ($factory_id) {
        return $f['id'] === $factory_id;
    });
    $factory = reset($factory);

    if (!$factory) {
        $error_message = 'Invalid factory selected';
    } else {
        $order_data = [
            'customer' => $factory['name'],
            'date' => $_POST['order_date'] ?? '',
            'deliveryDate' => $_POST['delivery_date'] ?? '',
            'amount' => $_POST['amount'] ?? 0,
            'items' => $_POST['items_count'] ?? 0,
            'status' => $_POST['status'] ?? '',
            'payment' => $_POST['payment'] ?? ''
        ];

        $result = save_order($order_data);
        if ($result['success']) {
            $success_message = $result['message'];
        } else {
            $error_message = $result['message'];
        }
    }
}

// Filter and sort parameters
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$selected_status = isset($_GET['status']) && in_array($_GET['status'], ['New', 'Processing', 'Shipped', 'Delivered', 'Cancelled']) ? $_GET['status'] : 'All Statuses';
$selected_payment = isset($_GET['payment']) && in_array($_GET['payment'], ['Paid', 'Pending', 'Partial', 'Refunded']) ? $_GET['payment'] : 'All Payments';
$sort_field = isset($_GET['sort']) && in_array($_GET['sort'], ['id', 'customer', 'date', 'amount']) ? $_GET['sort'] : 'date';
$sort_direction = isset($_GET['dir']) && in_array($_GET['dir'], ['asc', 'desc']) ? $_GET['dir'] : 'desc';

// Filter orders
$filtered_orders = array_filter($orders, function ($order) use ($search_query, $selected_status, $selected_payment) {
    $matches_search = empty($search_query) ||
        stripos($order['id'], $search_query) !== false ||
        stripos($order['customer'], $search_query) !== false;
    $matches_status = $selected_status === 'All Statuses' || $order['status'] === $selected_status;
    $matches_payment = $selected_payment === 'All Payments' || $order['payment'] === $selected_payment;
    return $matches_search && $matches_status && $matches_payment;
});

// Sort orders
usort($filtered_orders, function ($a, $b) use ($sort_field, $sort_direction) {
    if ($sort_field === 'date') {
        $date_a = strtotime($a['date']);
        $date_b = strtotime($b['date']);
        return $sort_direction === 'asc' ? $date_a - $date_b : $date_b - $date_a;
    } elseif ($sort_field === 'amount') {
        return $sort_direction === 'asc' ? $a['amount'] - $b['amount'] : $b['amount'] - $a['amount'];
    } else {
        $field_a = strtolower($a[$sort_field]);
        $field_b = strtolower($b[$sort_field]);
        return $sort_direction === 'asc' ? strcmp($field_a, $field_b) : strcmp($field_b, $field_a);
    }
});

// Statuses and payment statuses
$statuses = ['All Statuses', 'New', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
$payment_statuses = ['All Payments', 'Paid', 'Pending', 'Partial', 'Refunded'];

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
<link rel="stylesheet" href="../../public/css/styles.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
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

</head>
<body>
    <?php include '_vendor_nav.php'; ?>
    <main>
<h4><i class="fas fa-shopping-cart text-primary"></i> Order Management (<?php echo count($filtered_orders); ?>)</h4>
<p>Manage and process orders from factories.</p>

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

<!-- Header with New Order Button -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="text-muted">Order List</h5>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary btn-sm" onclick="alert('Generating orders report...')">
            <i class="fas fa-file-export"></i> Export
        </button>
        <button class="btn btn-outline-primary btn-sm" onclick="alert('Refreshing orders...')">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newOrderModal">
            <i class="fas fa-plus me-1"></i> New Order
        </button>
    </div>
</div>

<!-- Filters and Search -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex flex-column flex-md-row gap-3 align-items-md-end">
            <!-- Search -->
            <div class="flex-grow-1">
                <label class="form-label text-muted">Search Orders</label>
                <form method="GET" action="?page=orders" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="page" value="orders">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                        <input
                            type="text"
                            name="search"
                            class="form-control border-start-0"
                            placeholder="Search by order ID or customer..."
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
                <!-- Order Status -->
                <div>
                    <label class="form-label text-muted">Order Status</label>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($statuses as $status): ?>
                        <a href="?page=orders&status=<?php echo urlencode($status); ?>&payment=<?php echo urlencode($selected_payment); ?>&search=<?php echo urlencode($search_query); ?>">
                            <span class="badge <?php echo $selected_status === $status ? 'bg-primary text-white' : 'bg-light text-dark'; ?> px-3 py-1 rounded-pill">
                                <?php echo htmlspecialchars($status); ?>
                            </span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!-- Payment Status -->
                <div>
                    <label class="form-label text-muted">Payment Status</label>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($payment_statuses as $payment): ?>
                        <a href="?page=orders&status=<?php echo urlencode($selected_status); ?>&payment=<?php echo urlencode($payment); ?>&search=<?php echo urlencode($search_query); ?>">
                            <span class="badge <?php echo $selected_payment === $payment ? 'bg-primary text-white' : 'bg-light text-dark'; ?> px-3 py-1 rounded-pill">
                                <?php echo htmlspecialchars($payment); ?>
                            </span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <!-- Clear Filters -->
            <div>
                <a href="?page=orders" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-filter me-1"></i> Clear Filters
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Orders Table -->
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>
                            <a href="?page=orders&sort=id&dir=<?php echo $sort_field === 'id' && $sort_direction === 'asc' ? 'desc' : 'asc'; ?>&status=<?php echo urlencode($selected_status); ?>&payment=<?php echo urlencode($selected_payment); ?>&search=<?php echo urlencode($search_query); ?>" class="text-decoration-none">
                                Order ID
                                <?php if ($sort_field === 'id'): ?>
                                    <i class="fas fa-sort-<?php echo $sort_direction === 'asc' ? 'up' : 'down'; ?>"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="?page=orders&sort=customer&dir=<?php echo $sort_field === 'customer' && $sort_direction === 'asc' ? 'desc' : 'asc'; ?>&status=<?php echo urlencode($selected_status); ?>&payment=<?php echo urlencode($selected_payment); ?>&search=<?php echo urlencode($search_query); ?>" class="text-decoration-none">
                                Customer
                                <?php if ($sort_field === 'customer'): ?>
                                    <i class="fas fa-sort-<?php echo $sort_direction === 'asc' ? 'up' : 'down'; ?>"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="?page=orders&sort=date&dir=<?php echo $sort_field === 'date' && $sort_direction === 'asc' ? 'desc' : 'asc'; ?>&status=<?php echo urlencode($selected_status); ?>&payment=<?php echo urlencode($selected_payment); ?>&search=<?php echo urlencode($search_query); ?>" class="text-decoration-none">
                                Order Date
                                <?php if ($sort_field === 'date'): ?>
                                    <i class="fas fa-sort-<?php echo $sort_direction === 'asc' ? 'up' : 'down'; ?>"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="?page=orders&sort=amount&dir=<?php echo $sort_field === 'amount' && $sort_direction === 'asc' ? 'desc' : 'asc'; ?>&status=<?php echo urlencode($selected_status); ?>&payment=<?php echo urlencode($selected_payment); ?>&search=<?php echo urlencode($search_query); ?>" class="text-decoration-none">
                                Amount
                                <?php if ($sort_field === 'amount'): ?>
                                    <i class="fas fa-sort-<?php echo $sort_direction === 'asc' ? 'up' : 'down'; ?>"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($filtered_orders)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-shopping-cart fa-2x text-muted"></i>
                                <p class="mt-2 text-muted">No orders found matching your criteria.</p>
                                <a href="?page=orders" class="btn btn-outline-primary btn-sm">Clear Filters</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($filtered_orders as $order): ?>
                        <tr>
                            <td><a href="#" class="text-primary"><?php echo htmlspecialchars($order['id']); ?></a></td>
                            <td><?php echo htmlspecialchars($order['customer']); ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-calendar-alt text-muted"></i>
                                    <?php
                                        $date = new DateTime($order['date']);
                                        echo $date->format('M j, Y');
                                    ?>
                                </div>
                            </td>
                            <td>₹<?php echo number_format($order['amount']); ?></td>
                            <td>
                                <span class="badge <?php
                                    echo $order['status'] === 'New' ? 'bg-primary' :
                                        ($order['status'] === 'Processing' ? 'bg-warning' :
                                        ($order['status'] === 'Shipped' ? 'bg-purple' :
                                        ($order['status'] === 'Delivered' ? 'bg-success' : 'bg-danger')));
                                ?> text-white">
                                    <i class="fas <?php
                                        echo $order['status'] === 'New' ? 'fa-exclamation-circle' :
                                            ($order['status'] === 'Processing' ? 'fa-sync-alt' :
                                            ($order['status'] === 'Shipped' ? 'fa-truck' :
                                            ($order['status'] === 'Delivered' ? 'fa-check-circle' : 'fa-exclamation-circle')));
                                    ?> me-1"></i>
                                    <?php echo htmlspecialchars($order['status']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?php
                                    echo $order['payment'] === 'Paid' ? 'bg-success' :
                                        ($order['payment'] === 'Pending' ? 'bg-warning' :
                                        ($order['payment'] === 'Partial' ? 'bg-primary' : 'bg-danger'));
                                ?> text-white">
                                    <?php echo htmlspecialchars($order['payment']); ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-outline-primary btn-sm" title="View" onclick="alert('Viewing order <?php echo htmlspecialchars($order['id']); ?>')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if ($order['status'] === 'New'): ?>
                                        <button class="btn btn-outline-warning btn-sm" title="Process" onclick="alert('Processing order <?php echo htmlspecialchars($order['id']); ?>')">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($order['status'] === 'New' || $order['status'] === 'Processing'): ?>
                                        <button class="btn btn-outline-purple btn-sm" title="Ship" onclick="alert('Shipping order <?php echo htmlspecialchars($order['id']); ?>')">
                                            <i class="fas fa-truck"></i>
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($order['status'] === 'Shipped'): ?>
                                        <button class="btn btn-outline-success btn-sm" title="Mark Delivered" onclick="alert('Marking order <?php echo htmlspecialchars($order['id']); ?> as delivered')">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    <?php endif; ?>
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

<!-- New Order Modal -->
<div class="modal fade" id="newOrderModal" tabindex="-1" aria-labelledby="newOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newOrderModalLabel">Create New Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="?page=orders">
                    <input type="hidden" name="create_order" value="1">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="factorySelect" class="form-label">Select Factory</label>
                            <select class="form-select" id="factorySelect" name="factory_id" required>
                                <option value="">Choose a factory...</option>
                                <?php
                                $factories = get_factories();
                                foreach ($factories as $factory) {
                                    echo "<option value=\"" . htmlspecialchars($factory['id']) . "\">" . htmlspecialchars($factory['name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="orderDate" class="form-label">Order Date</label>
                            <input type="date" class="form-control" id="orderDate" name="order_date" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="deliveryDate" class="form-label">Delivery Date</label>
                            <input type="date" class="form-control" id="deliveryDate" name="delivery_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="amount" class="form-label">Amount (₹)</label>
                            <input type="number" class="form-control" id="amount" name="amount" min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="itemsCount" class="form-label">Number of Items</label>
                            <input type="number" class="form-control" id="itemsCount" name="items_count" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label for="items" class="form-label">Item Details</label>
                            <textarea class="form-control" id="items" name="items" rows="3" placeholder="Enter item details..." required></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="New">New</option>
                                <option value="Processing">Processing</option>
                                <option value="Shipped">Shipped</option>
                                <option value="Delivered">Delivered</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="payment" class="form-label">Payment Status</label>
                            <select class="form-select" id="payment" name="payment" required>
                                <option value="Pending">Pending</option>
                                <option value="Paid">Paid</option>
                                <option value="Partial">Partial</option>
                                <option value="Refunded">Refunded</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</main>
</body>
</html>