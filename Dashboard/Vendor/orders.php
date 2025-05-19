
<?php

include '../../_conn.php';
$user_name = $_SESSION['user_name'];
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
        <button class="btn btn-outline-primary btn-sm" onclick="exportTableToCSV()">
            <i class="fas fa-file-export"></i> Export
        </button>
        <button class="btn btn-outline-primary btn-sm" id="refreshBtn">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
        <script>
            // Refresh Button (Reload page)
            document.getElementById('refreshBtn').addEventListener('click', function () {
                location.reload();
            });
        </script>
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
                        <input type="text" name="search" class="form-control border-start-0" id="ordersSearch"
                            placeholder="Search by order ID or customer..."
                            value="<?php echo htmlspecialchars($search_query); ?>">
                </form>
            </div>
            <!-- Filters -->
            <div class="d-flex flex-column gap-3">
                <!-- Order Status -->
                <div>
                    <label class="form-label text-muted">Order Status</label>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($statuses as $status): ?>
                            <a
                                href="?page=orders&status=<?php echo urlencode($status); ?>&payment=<?php echo urlencode($selected_payment); ?>&search=<?php echo urlencode($search_query); ?>">
                                <span
                                    class="badge <?php echo $selected_status === $status ? 'bg-primary text-white' : 'bg-light text-dark'; ?> px-3 py-1 rounded-pill">
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
                            <a
                                href="?page=orders&status=<?php echo urlencode($selected_status); ?>&payment=<?php echo urlencode($payment); ?>&search=<?php echo urlencode($search_query); ?>">
                                <span
                                    class="badge <?php echo $selected_payment === $payment ? 'bg-primary text-white' : 'bg-light text-dark'; ?> px-3 py-1 rounded-pill">
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
            <table class="table table-bordered table-hover" id="orderTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Delivery Date</th>
                        <th>Received Date</th>
                        <th>Request ID</th>
                        <th>Tracking ID</th>
                        <th>Delivery ID</th>
                        <th>Request To</th>
                        <th>Shop Name</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Location</th>
                        <th>Requested By</th>
                        <th>Status</th>
                        <th>Received By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch all rows from retail_store_stock_request
                    // Fetch rows related to the logged-in user
                    $result = $conn->query("SELECT * FROM retail_store_stock_request WHERE request_to = '$user_name' ORDER BY request_id DESC");

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $status = htmlspecialchars($row['status']);
                            $id = htmlspecialchars($row['request_id']);

                            echo "<tr>";
                            echo "<td>" . date('d-M-Y', strtotime($row['date'])) . "</td>";
                            echo "<td>" . (!empty($row['delivery_date']) ? date('d-M-Y', strtotime($row['delivery_date'])) : '-') . "</td>";
                            echo "<td>" . (!empty($row['received_date']) ? date('d-M-Y', strtotime($row['received_date'])) : '-') . "</td>";
                            echo "<td>" . $id . "</td>";
                            echo "<td>" . htmlspecialchars($row['tracking_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['delivery_id'] ?? '-') . "</td>";
                            echo "<td>" . htmlspecialchars($row['request_to']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['shop_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['requested_by']) . "</td>";
                            echo "<td>" . $status . "</td>";
                            echo "<td>" . htmlspecialchars($row['received_by'] ?? '-') . "</td>";

                            echo "<td>";
                            if ($status === 'Ordered') {
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

                            // Modal for updating status
                            if ($status === 'Ordered') {
                                ?>
                                <div class="modal fade" id="statusModal<?= $id ?>" tabindex="-1"
                                    aria-labelledby="statusModalLabel<?= $id ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form method="POST" action="update_stock_status.php">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="statusModalLabel<?= $id ?>">Update Status</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="request_id" value="<?= $id ?>">
                                                    <select name="status" class="form-select" required>
                                                        <option value="">Select Status</option>
                                                        <option value="Dispatched">Dispatched</option>
                                                        <option value="Delivered">Delivered</option>
                                                        <option value="Cancelled">Cancelled</option>
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
                        echo "<tr><td colspan='16' class='text-center'>No stock requests found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>


            <script>
                // Search Functionality
                document.getElementById('ordersSearch').addEventListener('input', function () {
                    const searchText = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#ordersTable tbody tr');

                    rows.forEach(row => {
                        const cells = row.getElementsByTagName('td');
                        let match = false;
                        for (let i = 0; i < cells.length; i++) {
                            if (cells[i].textContent.toLowerCase().includes(searchText)) {
                                match = true;
                                break;
                            }
                        }
                        row.style.display = match ? '' : 'none';
                    });
                });
                // Export table data to CSV
                function exportTableToCSV(filename = 'table-data.csv') {
                    const rows = document.querySelectorAll("#ordersTable tr");
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
                            <input type="number" class="form-control" id="amount" name="amount" min="0" step="0.01"
                                required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="itemsCount" class="form-label">Number of Items</label>
                            <input type="number" class="form-control" id="itemsCount" name="items_count" min="1"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label for="items" class="form-label">Item Details</label>
                            <textarea class="form-control" id="items" name="items" rows="3"
                                placeholder="Enter item details..." required></textarea>
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