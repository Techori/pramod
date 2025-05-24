<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../_conn.php';
$user_name = $_SESSION['user_name'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['whatAction'])) {
    function clean($input)
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    if ($_POST['whatAction'] === 'requestStock') {
        // Collect data for transaction
        $itemName = clean($_POST['item_Name']);
        $category = clean($_POST['Category']);
        $requestTo = clean($_POST['request_to']);
        $shopName = clean($_POST['shopName']);
        $quantity = clean($_POST['quantity']);
        $location = clean($_POST['location']);
        $status = "Ordered";

        $today = date("Y-m-d");

        try {
            // Generate a new request ID
            $result = $conn->query("SELECT request_id FROM retail_store_stock_request WHERE requested_by = '$user_name' ORDER BY CAST(SUBSTRING(request_id, 6) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");

            if ($result && $row = $result->fetch_assoc()) {
                $lastId = $row['request_id']; // e.g. SL-005
                $num = (int) substr($lastId, 5);   // get "005" → 5
                $newNum = $num + 1;
            } else {
                $newNum = 1;
            }

            $newRequestId = 'RQST-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);

            // Generate a new tracking ID
            $result = $conn->query("SELECT tracking_id FROM retail_store_stock_request ORDER BY CAST(SUBSTRING(tracking_id, 6) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");

            if ($result && $row = $result->fetch_assoc()) {
                $lastId = $row['tracking_id']; // e.g. SL-005
                $num = (int) substr($lastId, 5);   // get "005" → 5
                $newNum = $num + 1;
            } else {
                $newNum = 1;
            }

            $newTrackId = 'TRCK-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);

            // Insert the transaction record
            $stmt = $conn->prepare("INSERT INTO retail_store_stock_request 
                (date, request_id, tracking_id, request_to, shop_name, item_name, category, quantity, location, requested_by, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param("sssssssisss", $today, $newRequestId, $newTrackId, $requestTo, $shopName, $itemName, $category, $quantity, $location, $user_name, $status);
            $stmt->execute();

            $conn->commit();
            $stmt->close();

            header("Location: store_dashboard.php?page=supply");
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
$supply_requests = get_supply_requests();
$low_stock_items = get_low_stock_items();
$popular_products = get_popular_products();
$supply_analytics = get_supply_analytics();

// Handle actions
$success_message = '';
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        if ($action === 'create_order') {
            $success_message = 'Creating new store supply request...';
        } elseif ($action === 'track_shipment' && isset($_POST['request_id'])) {
            $success_message = "Tracking supply request {$_POST['request_id']}";
        } elseif ($action === 'refresh') {
            $success_message = 'Refreshing supply requests...';
        } elseif ($action === 'view_all') {
            $success_message = 'Viewing all supply requests...';
        } elseif ($action === 'request_low_stock') {
            $success_message = 'Requesting low stock items...';
        } elseif ($action === 'view_full_report') {
            $success_message = 'Viewing full popular products report...';
        }
    }
}

// Search and filter parameters
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) && in_array($_GET['status'], ['All', 'Delivered', 'In Transit', 'Ordered']) ? $_GET['status'] : 'All';

// Filter supply requests
$filtered_requests = array_filter($supply_requests, function ($request) use ($search_query, $status_filter) {
    $matches_search = empty($search_query) ||
        stripos($request['id'], $search_query) !== false ||
        stripos($request['item'], $search_query) !== false ||
        stripos($request['source'], $search_query) !== false;
    $matches_status = $status_filter === 'All' || $request['status'] === $status_filter;
    return $matches_search && $matches_status;
});
?>

<div class="main-content">
    <h1><i class="fas fa-boxes text-primary me-2"></i> Store Supply Management</h1>
    <p class="text-muted">Track and manage inventory supplies for the retail store</p>

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
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0"
                    placeholder="Search products, orders, suppliers...">
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="submit" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#requestStock">
                <i class="fas fa-plus-circle me-1"></i> Request Stock
            </button>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchInput = document.getElementById("globalSearch");

            searchInput.addEventListener("input", function () {
                // Remove previous highlights
                document.querySelectorAll("mark.search-highlight").forEach(el => {
                    const parent = el.parentNode;
                    parent.replaceChild(document.createTextNode(el.textContent), el);
                    parent.normalize(); // Combine adjacent text nodes
                });

                const query = searchInput.value.trim().toLowerCase();
                if (!query) return;

                const allElements = document.body.querySelectorAll("*:not(script):not(style)");

                let firstMatch = null;

                allElements.forEach(el => {
                    if (el.children.length === 0 && el.textContent.toLowerCase().includes(query)) {
                        const regex = new RegExp(`(${query})`, "i");
                        const newHTML = el.textContent.replace(regex, '<mark class="search-highlight">$1</mark>');
                        el.innerHTML = newHTML;

                        if (!firstMatch) firstMatch = el;
                    }
                });

                if (firstMatch) {
                    setTimeout(() => {
                        firstMatch.scrollIntoView({ behavior: "smooth", block: "center" });
                    }, 100);
                }
            });
        });
    </script>

    <!-- Request Stock Form -->
    <div class="modal fade" id="requestStock" tabindex="-1" aria-labelledby="requestStockLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="supply.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="requestStockLabel">Request Stock</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="request_to" class="form-label">Request to</label>
                            <select class="form-select" id="request_to" name="request_to" required>
                                <option>Select</option>
                                <?php

                                // Fetch transactions from the database
                                $result = $conn->query("SELECT user_name FROM users WHERE user_type IN ('Admin', 'Factory', 'Vendor')");

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option>" . $row['user_name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="shopName" class="form-label">Shop Name</label>
                            <input type="text" class="form-control" id="shopName" name="shopName" required>
                        </div>

                        <div class="mb-3">
                            <label for="item_Name" class="form-label">Item Name</label>
                            <select class="form-select" id="item_Name" name="item_Name" required>
                                <option>Select Item</option>
                                <?php

                                // Fetch transactions from the database
                                $result = $conn->query("SELECT item_name FROM retail_invetory WHERE inventory_of = '$user_name'");

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option>" . $row['item_name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="Category" class="form-label">Category</label>
                            <input type="text" class="form-control" id="Category" name="Category" required>
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" required>
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" name="whatAction" value="requestStock">Request
                            Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php

    // 1. Pending Requests (status = 'ordered')
    $pendingQuery = $conn->prepare("SELECT COUNT(*) AS total FROM retail_store_stock_request WHERE status = 'Ordered' AND requested_by = ?");
    $pendingQuery->bind_param("s", $user_name);
    $pendingQuery->execute();
    $pendingResult = $pendingQuery->get_result()->fetch_assoc();
    $pendingRequests = $pendingResult['total'];

    // 2. In Transit (status = 'in transit')
    $transitQuery = $conn->prepare("SELECT COUNT(*) AS total FROM retail_store_stock_request WHERE status = 'In Transit' AND requested_by = ?");
    $transitQuery->bind_param("s", $user_name);
    $transitQuery->execute();
    $transitResult = $transitQuery->get_result()->fetch_assoc();
    $inTransit = $transitResult['total'];

    // 3. Received This Week (delivery_date this week and not null)
    $receivedQuery = $conn->prepare("
    SELECT COUNT(*) AS total FROM retail_store_stock_request 
    WHERE requested_by = ? AND delivery_date IS NOT NULL 
    AND WEEK(delivery_date) = WEEK(CURRENT_DATE()) AND YEAR(delivery_date) = YEAR(CURRENT_DATE())
");
    $receivedQuery->bind_param("s", $user_name);
    $receivedQuery->execute();
    $receivedResult = $receivedQuery->get_result()->fetch_assoc();
    $receivedThisWeek = $receivedResult['total'];

    // 4. Low Stock Items (stock < reorder_point)
    $lowStockQuery = $conn->prepare("
    SELECT COUNT(*) AS total FROM retail_invetory 
    WHERE inventory_of = ? AND stock < reorder_point
");
    $lowStockQuery->bind_param("s", $user_name);
    $lowStockQuery->execute();
    $lowStockResult = $lowStockQuery->get_result()->fetch_assoc();
    $lowStockItems = $lowStockResult['total'];

    // Close connections
    $pendingQuery->close();
    $transitQuery->close();
    $receivedQuery->close();
    $lowStockQuery->close();
    ?>


    <!-- Quick Stats -->
    <div class="row row-cols-1 row-cols-md-4 g-4 mb-4">
        <div class="col">
            <div class="card stat-card card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Pending Requests</h6>
                            <h3 class="fw-bold"><?php echo $pendingRequests; ?></h3>
                        </div>
                        <i class="fas fa-box fa-2x text-primary opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card card-border shadow-sm" style="border-left: 5px solid #ffc107;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">In Transit</h6>
                            <h3 class="fw-bold"><?php echo $inTransit; ?></h3>
                        </div>
                        <i class="fas fa-truck fa-2x text-warning opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card card-border shadow-sm" style="border-left: 5px solid #198754;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Received This Week</h6>
                            <h3 class="fw-bold"><?php echo $receivedThisWeek; ?></h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x text-success opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card card-border shadow-sm" style="border-left: 5px solid #dc3545;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Low Stock Items</h6>
                            <h3 class="fw-bold"><?php echo $lowStockItems; ?></h3>
                        </div>
                        <i class="fas fa-exclamation-circle fa-2x text-danger opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Supply Requests -->
    <div class="card card-border shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Recent Supply Requests</h5>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary btn-sm" id="refreshBtn">
                        <i class="fas fa-sync-alt me-1"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="supplyTable">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Tracking ID</th>
                            <th>Delivery ID</th>
                            <th>Requested To</th>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Source</th>
                            <th>Delivery Date</th>
                            <th>Received Date</th>
                            <th>Received By</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        // Fetch transactions from the database
                        $result = $conn->query("SELECT * FROM retail_store_stock_request WHERE requested_by = '$user_name' ORDER BY requested_by DESC");

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['request_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['tracking_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['delivery_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['request_to']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                                echo '<td>' . htmlspecialchars($row['quantity']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['request_to']) . "</td>";
                                if (!empty($row['delivery_date']) && $row['delivery_date'] !== '0000-00-00') {
                                    echo "<td>" . date('d-M-Y', strtotime($row['delivery_date'])) . "</td>";
                                } else {
                                    echo "<td></td>"; // Leave blank if null or invalid
                                }
                                if (!empty($row['received_date']) && $row['received_date'] !== '0000-00-00') {
                                    echo "<td>" . date('d-M-Y', strtotime($row['received_date'])) . "</td>";
                                } else {
                                    echo "<td></td>"; // Leave blank if null or invalid
                                }
                                echo "<td>" . htmlspecialchars($row['received_by']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='11' class='text-center'>No transactions found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <script>
                    // Search Functionality
                    document.getElementById('searchInput').addEventListener('input', function () {
                        const searchText = this.value.toLowerCase();
                        const rows = document.querySelectorAll('#supplyTable tbody tr');

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
                </script>
            </div>
        </div>
    </div>
    <script>
        // Refresh Button
        document.getElementById('refreshBtn').addEventListener('click', function () {
            window.location.reload();
        });
    </script>

    <!-- Check low stock -->
    <?php
    $low_stock_items = [];

    $query = $conn->prepare("SELECT item_name, stock, reorder_point, unit FROM retail_invetory WHERE inventory_of = ? AND stock < reorder_point");
    $query->bind_param("s", $user_name);
    $query->execute();
    $result = $query->get_result();

    while ($row = $result->fetch_assoc()) {
        $threshold = $row['reorder_point'] * 0.25;

        if ($row['stock'] <= $threshold) {
            $level = 'Critical';
        } else {
            $level = 'Low';
        }

        $low_stock_items[] = [
            'item' => $row['item'],
            'stock' => $row['stock'] . ' ' . $row['unit'], // e.g. '5 rolls'
            'level' => $level
        ];
    }

    $query->close();
    ?>

    <!-- Check popular products -->
    <?php
    $popular_products = [];
    $item_sales = [];

    $startOfMonth = date('Y-m-01');
    $endOfMonth = date('Y-m-t');

    $query = $conn->prepare("
    SELECT item_name, quantity 
    FROM invoice
    WHERE created_for = ? 
      AND date BETWEEN ? AND ?
");
    $query->bind_param("sss", $user_name, $startOfMonth, $endOfMonth);
    $query->execute();
    $result = $query->get_result();

    while ($row = $result->fetch_assoc()) {
        $items = explode(",", $row['item_name']);
        $quantities = explode(",", $row['quantity']);

        foreach ($items as $index => $item) {
            $item = trim($item);
            $qty = isset($quantities[$index]) ? (int) trim($quantities[$index]) : 0;

            if (!isset($item_sales[$item])) {
                $item_sales[$item] = 0;
            }
            $item_sales[$item] += $qty;
        }
    }
    $query->close();

    // Sort by sold quantity in descending order
    arsort($item_sales);

    // Take top 5 items
    $top_items = array_slice($item_sales, 0, 5, true);

    foreach ($top_items as $item => $qty) {
        // Calculate percentage based on max 1000 units
        $percentage = min(100, round(($qty / 1000) * 100));
        $popular_products[] = [
            'item' => $item,
            'quantity' => $qty,
            'percentage' => $percentage
        ];
    }

    ?>

    <!-- Stock Insights -->
    <div class="row row-cols-1 row-cols-md-2 g-4">
        <!-- Low Stock Alert -->
        <div class="col">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3"><i class="fas fa-exclamation-circle text-warning me-2"></i> Low Stock Alert</h5>
                    <div class="space-y-4">
                        <?php foreach ($low_stock_items as $item): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="font-medium"><?php echo htmlspecialchars($item['item']); ?></span>
                                <span
                                    class="<?php echo $item['level'] === 'Critical' ? 'text-danger' : 'text-warning'; ?> font-medium">
                                    <?php echo htmlspecialchars($item['level']); ?>
                                    (<?php echo htmlspecialchars($item['stock']); ?> left)
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>


        <!-- Popular Products -->
        <div class="col">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3"><i class="fas fa-shopping-cart text-primary me-2"></i> Popular Products</h5>
                    <p class="text-muted mb-3">Most requested items this month</p>
                    <div class="space-y-3">
                        <?php foreach ($popular_products as $product): ?>
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span
                                        class="text-sm font-medium"><?php echo htmlspecialchars($product['item']); ?></span>
                                    <span
                                        class="text-sm text-muted"><?php echo htmlspecialchars($product['quantity']); ?></span>
                                </div>
                                <div class="progress bg-light h-2">
                                    <div class="progress-bar bg-primary"
                                        style="width: <?php echo htmlspecialchars($product['percentage']); ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    mark.search-highlight {
        background-color: yellow;
        color: black;
        padding: 0;
        border-radius: 2px;
    }

    .space-y-4>*+* {
        margin-top: 1rem;
    }

    .space-y-3>*+* {
        margin-top: 0.75rem;
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

    .h-2 {
        height: 0.5rem;
    }
</style>