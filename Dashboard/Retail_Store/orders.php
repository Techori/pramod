<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../_conn.php';
$user_name = $_SESSION['user_name'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['whatAction'])) {

    if ($_POST['whatAction'] === 'addOrder') {
        // Collect data for transaction
        $customer_name = clean($_POST['customer_name']);
        $date = clean($_POST['date']);
        $deliveryDate = clean($_POST['deliveryDate']);
        $item_Name = clean($_POST['item_Name']);
        $quantity = clean($_POST['quantity']);
        $amount = clean($_POST['amount']);
        $paymentMethod = clean($_POST['paymentMethod']);
        $paymentStatus = clean($_POST['paymentStatus']);
        $status = clean($_POST['status']);
        

        try {
            // Generate a new order ID
            $result = $conn->query("SELECT order_id FROM retail_store_orders WHERE created_for = '$user_name' ORDER BY CAST(SUBSTRING(order_id, 5) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");

            if ($result && $row = $result->fetch_assoc()) {
                $lastId = $row['order_id']; // e.g. SL-005
                $num = (int) substr($lastId, 4);   // get "005" → 5
                $newNum = $num + 1;
            } else {
                $newNum = 1;
            }

            $newOrderId = 'ORD-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);

            // Insert the transaction record
            $stmt = $conn->prepare("INSERT INTO retail_store_orders 
                (date, request_id, tracking_id, request_to, shop_name, item_name, category, quantity, location, requested_by, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param("sssssssisss", $today, $newRequestId, $newTrackId, $requestTo, $shopName, $itemName, $category, $quantity, $location, $user_name, $status);
            $stmt->execute();

            $conn->commit();
            $stmt->close();

            header("Location: store_dashboard.php?page=inventory");
            exit;

        } catch (Exception $e) {
            $conn->rollback();
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Sale entry failed: " . $e->getMessage()
            ]);
            exit;
        }
    }
}


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

        <!-- Add order Form -->
    <div class="modal fade" id="newOrder" tabindex="-1" aria-labelledby="newOrderLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="supply.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newOrderLabel">Add Order</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Customer Name</label>
                            <select class="form-select" id="customer_name" name="customer_name" required>
                                <option>Select Customer</option>
                                <?php

                                // Fetch transactions from the database
                                $result = $conn->query("SELECT name FROM customer WHERE created_for = '$user_name'");

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option>" . $row['name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>

                        <div class="mb-3">
                            <label for="deliveryDate" class="form-label">Delivery Date</label>
                            <input type="date" class="form-control" id="deliveryDate" name="deliveryDate" required>
                        </div>

                        <div class="mb-3">
                            <label for="item_Name" class="form-label">Item Name</label>
                            <select class="form-select" id="item_Name" name="item_Name" required>
                                <option>Select Item</option>
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
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" required>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" required>
                        </div>

                        <div class="mb-3">
                            <label for="paymentMethod" class="form-label">Payment Method</label>
                            <select class="form-select" id="paymentMethod" name="paymentMethod" required>
                                <option>Select payment method</option>
                                <option>Digital payment</option>
                                <option>Cash</option>
                                <option>BNPL</option>
                                <option>Payment gateway</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="paymentStatus" class="form-label">Payment Status</label>
                            <select class="form-select" id="paymentStatus" name="paymentStatus" required>
                                <option>Select payment status</option>
                                <option>Paid</option>
                                <option>Pending</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option>Select status</option>
                                <option>Processing</option>
                                <option>Ready for Pickup</option>
                                <option>Delivered</option>
                            </select>
                        </div>


                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" name="whatAction" value="addOrder">Add Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
                <button type="submit" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newOrder">
                    <i class="fas fa-plus me-1"></i> New Order
                </button>
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
                            <th>Delivery Date</th>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Amount</th>
                            <th>Payment Mthod</th>
                            <th>Payment Status</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        // Fetch transactions from the database
                        $result = $conn->query("SELECT * FROM retail_store_orders WHERE created_for = '$user_name' ORDER BY order_id DESC");

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $status = htmlspecialchars($row['status']);
                                $id = $row['invoice_id'];

                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['order_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                                echo "<td>" . date('d-M-Y', strtotime($row['date'])) . "</td>";
                                echo "<td>" . date('d-M-Y', strtotime($row['delivery_date'])) . "</td>";
                                echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                                echo '<td>' . htmlspecialchars($row['quantity']) . "</td>";
                                echo "<td>₹" . number_format($row['amount'], 2) . "</td>";
                                echo "<td>" . htmlspecialchars($row['payment_method']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['payment_status']) . "</td>";
                                echo "<td>" . $status . "</td>";

                                    echo "<td>";
                                    if ($status === 'Processing' || $status === 'Ready for Pickup') {
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
                                    if ($status === 'Processing' || $status === 'Ready for Pickup') {
                                        ?>
                                        <div class="modal fade" id="statusModal<?= $id ?>" tabindex="-1"
                                            aria-labelledby="statusModalLabel<?= $id ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form method="POST" action="update_status.php">
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
                                echo "<tr><td colspan='11' class='text-center'>No orders found</td></tr>";
                            }
                            ?>
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