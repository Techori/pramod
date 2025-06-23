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

            header("Location: vendor_dashboard.php?page=deliveries");
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
    } else if ($_POST['whatAction'] === 'confirmDelivery') {

        $deliveryId = clean($_POST['delivery_id']);
        $trackingId = clean($_POST['tracking_id']);
        $requestId = clean($_POST['requestId']);
        $receivedDate = clean($_POST['received_date']);
        $receivedBy = clean($_POST['received_by']);
        $received = "Received";

        // Verify Delivery ID and Tracking ID match with the given Request ID
        $stmt = $conn->prepare("SELECT * FROM retail_store_stock_request WHERE request_id = ? AND delivery_id = ? AND tracking_id = ?");
        $stmt->bind_param("sss", $requestId, $deliveryId, $trackingId);
        $stmt->execute();
        $result = $stmt->get_result();

        //If not matching, alert and exit
        if ($result->num_rows === 0) {
            echo "<script>alert('Delivery ID or Tracking ID does not match the given Request ID.'); window.location.href='store_dashboard.php?page=billing';</script>";
            $stmt->close();
            exit;
        }

        while ($row = $result->fetch_assoc()) {
            $item_name = $row['item_name'];
            $quantity = (int) $row['quantity'];

            // Update stock in retail_inventory
            $updateStockStmt = $conn->prepare("
                UPDATE vendor_product 
                SET stock = stock + ? 
                WHERE product_name = ?
            ");
            $updateStockStmt->bind_param("is", $quantity, $item_name);
            $updateStockStmt->execute();
            $updateStockStmt->close();
        }

        //If matched, update received data
        $updateStmt = $conn->prepare("UPDATE retail_store_stock_request SET received_date = ?, received_by = ?, status = ? WHERE tracking_id  = ?");
        $updateStmt->bind_param("ssss", $receivedDate, $receivedBy, $received, $trackingId);

        if ($updateStmt->execute()) {
            echo "<script>alert('Receiving info successfully updated.'); window.location.href='vendor_dashboard.php?page=deliveries';</script>";
        } else {
            echo "<script>alert('Error updating receiving info.'); window.location.href='vendor_dashboard.php?page=deliveries';</script>";
        }

        // Close connections
        $stmt->close();
        $updateStmt->close();
    }
}

?>

<!-- Header with New Order Button -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4><i class="fas fa-truck text-primary"></i> Deliveries</h4>
        <p>Track and manage your deliveries from factories.</p>
    </div>
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
            data-bs-target="#requestStock">
            <i class="fas fa-plus-circle me-1"></i> Request Stock
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

<!-- Request Stock Form -->
<div class="modal fade" id="requestStock" tabindex="-1" aria-labelledby="requestStockLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="deliveries.php" method="POST">
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
                            $result = $conn->query("SELECT user_name FROM users WHERE user_type IN ('Admin', 'Factory')");

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
                            $result = $conn->query("SELECT product_name FROM vendor_product WHERE product_of = '$user_name'");

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option>" . $row['product_name'] . "</option>";
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


<!-- Deliveries Table -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Recent Deliveries</h5>
        <p class="text-muted">Track your recent and ongoing deliveries from factories</p>
        <!-- Search and Filters -->
        <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center mb-4">
            <div class="flex-grow-1">
                <input type="hidden" name="page" value="billing">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" id="searchInput"><i
                            class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0 table-search" data-table="deliveriesTable"
                        placeholder="Search..." />
                </div>
            </div>
            <div class="d-flex gap-2">
                <div>
                    <button class="btn btn-outline-primary gst-filter me-2" data-type="Ordered"
                        data-table="deliveriesTable">Ordered</button>
                </div>
                <div>
                    <button class="btn btn-outline-primary gst-filter me-2" data-type="In Transit"
                        data-table="deliveriesTable">In Transit</button>
                </div>
                <div>
                    <button class="btn btn-outline-primary gst-filter me-2" data-type="Received"
                        data-table="deliveriesTable">Received</button>
                </div>
                <div>
                    <button class="btn btn-outline-danger reset-filters me-2" data-table="deliveriesTable">Remove
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
                            const docType = row.children[10]?.innerText.trim().toLowerCase();
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
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="deliveriesTable">
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

// 3. On-Time Delivery Rate
// Step 1: Total Delivered Requests in Last 30 Days
$totalQuery = $conn->prepare("
    SELECT COUNT(*) AS total 
    FROM retail_store_stock_request 
    WHERE requested_by = ? 
    AND delivery_date IS NOT NULL 
    AND delivery_date >= CURDATE() - INTERVAL 30 DAY
");
$totalQuery->bind_param("s", $user_name);
$totalQuery->execute();
$totalResult = $totalQuery->get_result()->fetch_assoc();
$totalRequests = $totalResult['total'];

// Step 2: On-Time Deliveries (received on or before delivery_date)
$onTimeQuery = $conn->prepare("
    SELECT COUNT(*) AS on_time 
    FROM retail_store_stock_request 
    WHERE requested_by = ? 
    AND delivery_date IS NOT NULL 
    AND received_date IS NOT NULL
    AND received_date <= delivery_date
    AND delivery_date >= CURDATE() - INTERVAL 30 DAY
");
$onTimeQuery->bind_param("s", $user_name);
$onTimeQuery->execute();
$onTimeResult = $onTimeQuery->get_result()->fetch_assoc();
$onTimeRequests = $onTimeResult['on_time'];

// Step 3: Calculate Percentage
$onTimePercentage = 0;
if ($totalRequests > 0) {
    $onTimePercentage = ($onTimeRequests / $totalRequests) * 100;
}

// Close connections
$pendingQuery->close();
$transitQuery->close();
?>

<!-- Delivery Statistics -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Pending Deliveries</h5>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="h3 font-weight-bold"><?php echo $pendingRequests; ?></p>
                        <p class="text-muted">Awaiting delivery</p>
                    </div>
                    <div class="p-3 bg-warning bg-opacity-10 rounded-circle">
                        <i class="fas fa-truck text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Items In Transit</h5>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="h3 font-weight-bold"><?php echo $inTransit; ?></p>
                        <p class="text-muted">Currently in transit</p>
                    </div>
                    <div class="p-3 bg-primary bg-opacity-10 rounded-circle">
                        <i class="fas fa-box text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">On-Time Deliveries</h5>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="h3 font-weight-bold"><?php echo $onTimePercentage; ?>%</p>
                        <p class="text-muted">Last 30 days (<?php echo $onTimeRequests . " of " . $totalRequests; ?>)</p>
                    </div>
                    <div class="p-3 bg-success bg-opacity-10 rounded-circle">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delivery Confirmation Form -->
<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title">Confirm Delivery Receipt</h5>
        <p class="text-muted">Manually confirm a delivery that has been received</p>
        <div class="row">
            <div class="col-md-6">
                <form method="POST" action="deliveries.php">
                    <div class="mb-3">
                        <label for="delivery_id" class="form-label">Delivery ID</label>
                        <input type="text" class="form-control" id="delivery_id" name="delivery_id"
                            placeholder="Enter Delivery ID (e.g., DELV-2025-001)" required>
                    </div>
                    <div class="mb-3">
                        <label for="tracking_id" class="form-label">Tracking ID</label>
                        <input type="text" class="form-control" id="tracking_id" name="tracking_id"
                            placeholder="Enter Tracking ID">
                    </div>
                    <div class="mb-3">
                        <label for="requestId" class="form-label">Request ID</label>
                        <input type="text" class="form-control" id="requestId" name="requestId"
                            placeholder="Enter Request ID">
                    </div>
                    <div class="mb-3">
                        <label for="received_date" class="form-label">Date Received</label>
                        <input type="date" class="form-control" id="received_date" name="received_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="received_by" class="form-label">Received By</label>
                        <input type="text" class="form-control" id="received_by" name="received_by"
                            placeholder="Name of person who received the delivery" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm" name="whatAction" value="confirmDelivery">
                        <i class="fas fa-check-circle me-1"></i> Confirm Receipt
                    </button>
                </form>
            </div>
            <div class="col-md-6 border-start ps-4 d-none d-md-block">
                <h5 class="font-weight-bold">Delivery Confirmation Guidelines</h5>
                <ul class="list-unstyled mt-3">
                    <li class="d-flex align-items-start mb-2">
                        <i class="fas fa-clipboard-list text-primary me-2 mt-1"></i>
                        <span>Check all items against the delivery note before confirming.</span>
                    </li>
                    <li class="d-flex align-items-start mb-2">
                        <i class="fas fa-clipboard-list text-primary me-2 mt-1"></i>
                        <span>Report any damages or discrepancies immediately.</span>
                    </li>
                    <li class="d-flex align-items-start mb-2">
                        <i class="fas fa-clipboard-list text-primary me-2 mt-1"></i>
                        <span>Keep delivery notes and packaging until quality is verified.</span>
                    </li>
                    <li class="d-flex align-items-start mb-2">
                        <i class="fas fa-clipboard-list text-primary me-2 mt-1"></i>
                        <span>Confirmation must be done within 24 hours of receipt.</span>
                    </li>
                </ul>
                <div class="p-3 bg-warning bg-opacity-10 border border-warning rounded mt-4">
                    <p class="font-weight-bold text-warning">Important Notice</p>
                    <p class="text-muted small">
                        Once a delivery is confirmed as received, it cannot be disputed for missing items. Please ensure
                        a thorough check before confirmation.
                    </p>
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