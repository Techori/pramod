<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../_conn.php';
$user_name = $_SESSION['user_name'];

// Count stats
$pending = $conn->query("SELECT COUNT(*) as count FROM retail_store_stock_request WHERE status='Ordered' AND request_to = '$user_name'")->fetch_assoc()['count'];
$in_transit = $conn->query("SELECT COUNT(*) as count FROM retail_store_stock_request WHERE status='In Transit' AND request_to = '$user_name'")->fetch_assoc()['count'];
$delivered = $conn->query("SELECT COUNT(*) as count FROM retail_store_stock_request WHERE status='Received' AND request_to = '$user_name' AND received_date <= CURDATE() AND received_date >= CURDATE() - INTERVAL 30 DAY")->fetch_assoc()['count'];
?>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['whatAction'])) {
    if ($_POST['whatAction'] === 'updateDeliveryDate') {
        $trackingId = $_POST['tracking_id'];
        $deliveryDate = $_POST['delivery_date'];
        $status = 'In Transit';

        // Generate a new delivery ID
        $result = $conn->query("SELECT delivery_id FROM retail_store_stock_request ORDER BY CAST(SUBSTRING(delivery_id, 6) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");

        if ($result && $row = $result->fetch_assoc()) {
            $lastId = $row['delivery_id']; // e.g. SL-005
            $num = (int) substr($lastId, 5);   // get "005" → 5
            $newNum = $num + 1;
        } else {
            $newNum = 1;
        }

        $newDeliveryId = 'DELS-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);

        $stmt = $conn->prepare("UPDATE retail_store_stock_request SET delivery_id = ?, delivery_date = ?, status = ? WHERE tracking_id  = ?");
        $stmt->bind_param("ssss", $newDeliveryId, $deliveryDate, $status, $trackingId);
        $stmt->execute();
        $stmt->close();

        $quantityFetch = $conn->query("SELECT * FROM retail_store_stock_request WHERE tracking_id = '$trackingId' LIMIT 1");

        while ($row = $quantityFetch->fetch_assoc()) {
            $quantity = (int) $row["quantity"];
            $itemName = $row["item_name"];

            // Get latest stock_id for this item and user
            $latestStockSql = "SELECT stock_id FROM factory_stock WHERE item_name = ? AND created_for = ? ORDER BY record_date DESC, stock_id DESC LIMIT 1";
            $latestStockStmt = $conn->prepare($latestStockSql);
            $latestStockStmt->bind_param("ss", $itemName, $user_name);
            $latestStockStmt->execute();
            $latestStockResult = $latestStockStmt->get_result();

            if ($latestStockResult && $latestStockRow = $latestStockResult->fetch_assoc()) {
                $latestStockId = $latestStockRow['stock_id'];
                // Update only latest entry
                $updateStockStmt = $conn->prepare("
                    UPDATE factory_stock 
                    SET quantity = quantity - ? 
                    WHERE stock_id = ?
                ");
                $updateStockStmt->bind_param("ds", $quantity, $latestStockId);
                $updateStockStmt->execute();
                $updateStockStmt->close();
            }
            $latestStockStmt->close();
        }

        @header("Location: factory_dashboard.php?page=supply_management");

    }
}

?>

<style>
    .tab-nav {
        background-color: #f8f9fa;
        padding: 10px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
    }

    .tab-nav a {
        text-decoration: none;
        padding: 10px 15px;
        color: #000;
        font-weight: 500;
    }

    .tab-nav a.active {
        border-bottom: 3px solid #0d6efd;
        color: #0d6efd;
    }

    .table-heading {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .table-actions i {
        margin: 0 6px;
        cursor: pointer;
    }

    .badge {
        font-size: 0.8rem;
    }
</style>

<!-- Top Section -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Factory Supply Management</h2>
        <p class="text-muted mb-0">Track and manage raw materials and supplies for production</p>
    </div>
</div>

<div class="row mb-4">

    <!-- Search and Filters -->
    <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center mb-4">
        <div class="flex-grow-1">
            <input type="hidden" name="page" value="billing">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0" id="searchInput"><i
                        class="fas fa-search"></i></span>
                <input type="text" class="form-control border-start-0 table-search" data-table="ordersTable"
                    placeholder="Search..." />
            </div>
        </div>
        <div class="d-flex gap-2">
            <div>
                <button class="btn btn-outline-primary gst-filter me-2" data-type="Ordered"
                    data-table="ordersTable">Ordered</button>
            </div>
            <div>
                <button class="btn btn-outline-primary gst-filter me-2" data-type="In Transit"
                    data-table="ordersTable">In Transit</button>
            </div>
            <div>
                <button class="btn btn-outline-primary gst-filter me-2" data-type="Received"
                    data-table="ordersTable">Received</button>
            </div>
            <div>
                <button class="btn btn-outline-danger reset-filters me-2" data-table="ordersTable">Remove
                    Filters</button>
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

            // 🧾 GST Filter Buttons
            document.querySelectorAll(".gst-filter").forEach(button => {
                button.addEventListener("click", () => {
                    const type = button.dataset.type.toLowerCase();
                    const tableId = button.dataset.table;
                    const rows = document.querySelectorAll(`#${tableId} tbody tr`);
                    rows.forEach(row => {
                        const docType = row.children[13]?.innerText.trim().toLowerCase();
                        row.style.display = docType === type ? "" : "none";
                    });
                });
            });

            // ❌ Remove Filters Button
            document.querySelectorAll(".reset-filters").forEach(button => {
                button.addEventListener("click", () => {
                    const tableId = button.dataset.table;
                    const rows = document.querySelectorAll(`#${tableId} tbody tr`);
                    rows.forEach(row => {
                        row.style.display = "";
                    });

                    // Also clear search inputs for that table
                    document.querySelectorAll(`.table-search[data-table='${tableId}']`).forEach(input => {
                        input.value = "";
                    });
                });
            });

            // ✅ Filter Helper Function
            function filterTable(tableId, conditionFn) {
                const rows = document.querySelectorAll(`#${tableId} tbody tr`);
                rows.forEach(row => {
                    row.style.display = conditionFn(row) ? "" : "none";
                });
            }
        });
    </script>
</div>

<div class="row mb-5 g-3">
    <div class="col-md-4">
        <div class="card text-center p-3">
            <div class="fs-1 mb-2"><i class="fa-regular fa-hourglass-half"></i></div>
            <h5 class="mb-0">Pending Orders</h5>
            <h3 class="fw-bold"><?= $pending ?></h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center p-3">
            <div class="fs-1 mb-2"><i class="fa-solid fa-truck-arrow-right"></i></div>
            <h5 class="mb-0">In Transit</h5>
            <h3 class="fw-bold"><?= $in_transit ?></h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center p-3">
            <div class="fs-1 mb-2"><i class="fa-solid fa-check"></i></div>
            <h5 class="mb-0">Delivered This Month</h5>
            <h3 class="fw-bold"><?= $delivered ?></h3>
        </div>
    </div>
</div>


<!-- Recent Supply Orders -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">Recent Supply Orders</h4>
    <div>
        <button class="btn btn-outline-primary btn-sm" id="refreshBtn">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
    </div>
    <script>
        // Refresh Button (Reload page)
        document.getElementById('refreshBtn').addEventListener('click', function () {
            location.reload();
        });
    </script>
</div>

<div class="table-responsive mb-5">
    <table class="table table-bordered table-hover" id="ordersTable">
        <thead>
            <tr>
                <th>Request ID</th>
                <th>Tracking ID</th>
                <th>Delivery ID</th>
                <th>Date</th>
                <th>Shop Name</th>
                <th>Item Name</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Location</th>
                <th>Requested By</th>
                <th>Received By</th>
                <th>Delivery Date</th>
                <th>Received Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php

            $result = $conn->query("SELECT * FROM retail_store_stock_request WHERE request_to = '$user_name' ORDER BY request_id DESC");

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $status = htmlspecialchars($row['status']);
                    $id = htmlspecialchars($row['tracking_id']);

                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['request_id']) . "</td>";
                    echo "<td>" . $id . "</td>";
                    echo "<td>" . htmlspecialchars($row['delivery_id'] ?? '-') . "</td>";
                    echo "<td>" . date('d-M-Y', strtotime($row['date'])) . "</td>";
                    echo "<td>" . htmlspecialchars($row['shop_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['requested_by']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['received_by'] ?? '-') . "</td>";
                    echo "<td>" . (!empty($row['delivery_date']) ? date('d-M-Y', strtotime($row['delivery_date'])) : '-') . "</td>";
                    echo "<td>" . (!empty($row['received_date']) ? date('d-M-Y', strtotime($row['received_date'])) : '-') . "</td>";
                    echo "<td>" . $status . "</td>";

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
                                <form method="POST" action="supply_management.php">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="statusModalLabel<?= $id ?>">Update Status</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="tracking_id" value="<?= $id ?>">
                                            <label class="form-label">Delivery Date</label>
                                            <input type="date" name="delivery_date" class="form-control" placeholder="Delivery Date"
                                                required>
                                            <!-- <select name="status" class="form-select" required>
                                                        <option value="">Select Status</option>
                                                        <option value="Dispatched">Dispatched</option>
                                                        <option value="Delivered">Delivered</option>
                                                        <option value="Cancelled">Cancelled</option>
                                                    </select> -->
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary" name="whatAction"
                                                value="updateDeliveryDate">Update</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php
                    }
                }
            } else {
                echo "<tr><td colspan='15' class='text-center'>No stock requests found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Check low stock -->
<?php
$low_stock_items = [];

$query = $conn->prepare("SELECT fs.item_name, fs.quantity, fs.status
FROM factory_stock fs
JOIN (
    SELECT item_name, MAX(CONCAT(record_date, LPAD(stock_id, 10, '0'))) AS latest_key
    FROM factory_stock
    WHERE status IN ('Low Stock', 'Out of Stock')
      AND created_by = '$user_name'
    GROUP BY item_name
) latest
    ON CONCAT(fs.record_date, LPAD(fs.stock_id, 10, '0')) = latest.latest_key
WHERE fs.status IN ('Low Stock', 'Out of Stock')
  AND fs.created_for = '$user_name'");
$query->execute();
$result = $query->get_result();

while ($row = $result->fetch_assoc()) {

    if ($row['status'] === 'Out of Stock') {
        $level = 'Critical';
    } else {
        $level = 'Low';
    }

    $low_stock_items[] = [
        'item' => $row['item_name'],
        'stock' => $row['quantity'], // e.g. '5 rolls'
        'level' => $level
    ];
}

$query->close();
?>

<!-- New Section: Low Stock and Supply Trends -->
<div class="row g-4">
    <!-- Low Stock Alert Section -->
    <div class="col-md-6">
        <div class="card p-3">
            <h5 class="fw-bold text-warning"><i class="bi bi-exclamation-circle"></i> Low Stock Alert</h5>
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

    <!-- Supply Trends Card -->
    <div class="col-md-6">
        <div class="card p-3">
            <h5 class="fw-bold text-primary"><i class="bi bi-graph-up"></i> Supply Trends</h5>
            <p class="text-muted mb-4">Monthly procurement of top 3 raw materials</p>

            <div class="space-y-3">
                <?php foreach ($popular_products as $product): ?>
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-sm font-medium"><?php echo htmlspecialchars($product['item']); ?></span>
                            <span class="text-sm text-muted"><?php echo htmlspecialchars($product['quantity']); ?></span>
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