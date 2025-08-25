<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../_conn.php';
$user_name = $_SESSION['user_name'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['whatAction'])) {


    // Transaction action
    if ($_POST['whatAction'] === 'addItem') {
        // Collect data for transaction
        $itemName = $_POST['itemName'];
        $category = $_POST['category'];
        $price = $_POST['price'];
        $unit = $_POST['unit'];
        $stock = $_POST['stock'];
        $reorderPoint = $_POST['reorderPoint'];
        $status = $_POST['Status'];

        $today = date("Y-m-d");

        // Validate data for transaction
        $allowedStatus = ['In stock', 'Low stock', 'Out of stock'];
        if (!in_array($status, $allowedStatus)) {
            header("Location: store_dashboard.php?page=inventory");
            echo json_encode(["success" => false, "message" => "Invalid status"]);
            exit;
        }

        // Start database transaction
        $conn->begin_transaction();

        try {
            // Generate a new item ID
            $result = $conn->query("SELECT Id FROM retail_invetory WHERE inventory_of = '$user_name' ORDER BY CAST(SUBSTRING(Id, 6) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");

            if ($result && $row = $result->fetch_assoc()) {
                $lastId = $row['Id']; // e.g. SL-005
                $num = (int) substr($lastId, 5);   // get "005" → 5
                $newNum = $num + 1;
            } else {
                $newNum = 1;
            }

            $newItemId = 'ITEM-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);

            // Insert the item record
            $stmt = $conn->prepare("INSERT INTO retail_invetory 
                (Id, item_name, category, stock, unit, price, last_updated, status, inventory_of, reorder_point) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param("sssisdsssi", $newItemId, $itemName, $category, $stock, $unit, $price, $today, $status, $user_name, $reorderPoint);
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

    } else if ($_POST['whatAction'] === 'editPrice') {
        $itemId = $_POST['itemId'];
        $newPrice = $_POST['newPrice'];

        $stmt = $conn->prepare("UPDATE retail_invetory SET price = ?, last_updated = NOW() WHERE Id = ? AND inventory_of = ?");
        $stmt->bind_param("dss", $newPrice, $itemId, $user_name);
        $stmt->execute();
        $stmt->close();

        @header("Location: store_dashboard.php?page=inventory");

    } else if ($_POST['whatAction'] === 'deleteItem') {
        $itemId = $_POST['itemId'];

        $stmt = $conn->prepare("DELETE FROM retail_invetory WHERE Id = ? AND inventory_of = ?");
        $stmt->bind_param("ss", $itemId, $user_name);
        $stmt->execute();
        $stmt->close();

        @header("Location: store_dashboard.php?page=inventory");

    } else if ($_POST['whatAction'] === 'requestStock') {
        // Collect data for transaction
        $itemName = $_POST['item_Name'];
        $category = $_POST['Category'];
        $requestTo = $_POST['request_to'];
        $shopName = $_POST['shopName'];
        $quantity = $_POST['quantity'];
        $location = $_POST['location'];
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
$inventory_items = get_inventory_items();
$inventory_categories = get_inventory_categories();
$inventory_analytics = get_inventory_analytics();
$inventory_stats = get_inventory_stats();
$inventory_activities = get_inventory_activities();

// Handle actions
$success_message = '';
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $item_name = isset($_POST['item_name']) ? $_POST['item_name'] : '';
        switch ($action) {
            case 'add_item':
                $success_message = 'Add New Item operation initiated successfully.';
                break;
            case 'request_stock':
                $success_message = 'Request Stock operation initiated successfully.';
                break;
            case 'export_inventory':
                $success_message = 'Export Inventory operation initiated successfully.';
                break;
            case 'update_item':
                $success_message = 'Update Item operation initiated successfully.';
                break;
            case 'generate_report':
                $success_message = 'Generate Report operation initiated successfully.';
                break;
            case 'delete_item':
                $success_message = 'Delete Item operation initiated successfully.';
                break;
            case 'order_item':
                $success_message = "Order $item_name operation initiated successfully.";
                break;
            case 'view_low_stock':
                $success_message = 'View Low Stock operation initiated successfully.';
                break;
        }
    }
}

// Search and filter parameters
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) && in_array($_GET['category'], $inventory_categories) ? $_GET['category'] : 'All Categories';
$tab = isset($_GET['tab']) && in_array($_GET['tab'], ['all', 'low', 'out']) ? $_GET['tab'] : 'all';

// Filter inventory items
$filtered_items = array_filter($inventory_items, function ($item) use ($search_query, $category_filter, $tab) {
    $matches_search = empty($search_query) ||
        stripos($item['id'], $search_query) !== false ||
        stripos($item['name'], $search_query) !== false ||
        stripos($item['category'], $search_query) !== false;
    $matches_category = $category_filter === 'All Categories' || $item['category'] === $category_filter;
    $matches_tab = $tab === 'all' || ($tab === 'low' && $item['status'] === 'Low Stock') || ($tab === 'out' && $item['stock'] == 0);
    return $matches_search && $matches_category && $matches_tab;
});
?>

<div class="main-content">
    <h1><i class="fas fa-warehouse text-primary me-2"></i> Store Inventory Management</h1>
    <p class="text-muted">Manage and track your retail store inventory</p>

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

    <!-- Add New Item Form -->
    <div class="modal fade" id="addItem" tabindex="-1" aria-labelledby="addItemLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="inventory.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addItemLabel">Add Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="itemName" class="form-label">Item Name</label>
                            <input type="text" class="form-control" id="itemName" name="itemName" required>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <input type="text" class="form-control" id="category" name="category" required>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="price" name="price" required>
                        </div>

                        <div class="mb-3">
                            <label for="unit" class="form-label">Per Unit</label>
                            <input type="text" class="form-control" id="unit" name="unit" required>
                        </div>

                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="stock" name="stock" required>
                        </div>

                        <div class="mb-3">
                            <label for="reorderPoint" class="form-label">Reorder Point</label>
                            <input type="number" class="form-control" id="reorderPoint" name="reorderPoint" required>
                        </div>

                        <div class="mb-3">
                            <label for="Status" class="form-label">Status</label>
                            <select class="form-select" id="Status" name="Status" required>
                                <option value="In stock">In stock</option>
                                <option value="Low stock">Low stock</option>
                                <option value="Out of stock">Out of stock</option>
                            </select>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" name="whatAction" value="addItem">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Request Stock Form -->
    <div class="modal fade" id="requestStock" tabindex="-1" aria-labelledby="requestStockLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="inventory.php" method="POST">
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
                                $result = $conn->query("SELECT user_name FROM users  WHERE user_type IN ('Admin', 'Factory', 'Vendor')");

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

    <!-- Item price edite Modal -->
    <div class="modal fade" id="editPriceModal" tabindex="-1" aria-labelledby="editPriceLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="store_dashboard.php?page=inventory" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPriceLabel">Edit Item Price</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="whatAction" value="editPrice">
                    <input type="hidden" name="itemId" id="editItemId">
                    <div class="mb-3">
                        <label for="editItemName" class="form-label">Item</label>
                        <input type="text" class="form-control" id="editItemName" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="newPrice" class="form-label">New Price</label>
                        <input type="number" class="form-control" id="newPrice" name="newPrice" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update Price</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Search and Actions -->
    <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center justify-content-between mb-4">
        <div class="flex-grow-1">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0"
                    placeholder="Search inventory...">
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="submit" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addItem">
                <i class="fas fa-plus-circle me-1"></i> Add New Item
            </button>
            <button type="submit" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                data-bs-target="#requestStock">
                <i class="fas fa-truck me-1"></i> Request Stock
            </button>
            <button type="submit" class="btn btn-outline-primary btn-sm" onclick="exportTableToCSV()">
                <i class="fas fa-download me-1"></i> Export
            </button>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchInput = document.getElementById("searchInput");

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

    <!-- Tabs and Filters -->
    <div class="card card-border shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <ul class="nav nav-tabs mb-3 mb-md-0">
                    <li class="nav-item">
                        <a href="?page=inventory&tab=all"
                            class="nav-link <?php echo $tab === 'all' ? 'active' : ''; ?>">All Items</a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=inventory&tab=low"
                            class="nav-link <?php echo $tab === 'low' ? 'active' : ''; ?>">Low Stock</a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=inventory&tab=out"
                            class="nav-link <?php echo $tab === 'out' ? 'active' : ''; ?>">Out of Stock</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <?php
    // Check if user has Delete permission
    $hasDeletePermission = false;
    $permissionSql = "SELECT Permission FROM user_management WHERE User_Name = '$user_name'";
    $permissionResult = $conn->query($permissionSql);
    if ($permissionResult->num_rows > 0) {
        $permissionRow = $permissionResult->fetch_assoc();
        $permissions = json_decode($permissionRow['Permission'], true);
        $hasDeletePermission = in_array('Delete', $permissions);
    }
    ?>

    <!-- Inventory Tables -->
    <?php if ($tab === 'all'): ?>
        <div class="card card-border shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="mb-3">Inventory Items</h5>
                <p class="text-muted mb-3">Manage all your store inventory items</p>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="supplyTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Stock</th>
                                <th>Price</th>
                                <th>Last Updated</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            // Fetch transactions from the database
                            $result = $conn->query("SELECT * FROM retail_invetory WHERE inventory_of = '$user_name' ORDER BY Id DESC");

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['Id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['stock']) . "</td>";
                                    echo "<td>₹" . number_format($row['price']) . "/" . htmlspecialchars($row['unit']) . "</td>";
                                    echo "<td>" . date('d-M-Y', strtotime($row['last_updated'])) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                    echo '<td>
                                            <div>
                                    <button class="btn btn-outline-primary btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editPriceModal" 
                                                        data-id="' . $row['Id'] . '" 
                                                        data-name="' . htmlspecialchars($row['item_name']) . '" 
                                                        data-price="' . $row['price'] . '">
                                                    <i class="fa-regular fa-pen-to-square"></i>
                                                </button>';

                                    if ($hasDeletePermission) {
                                        echo '<form method="POST" action="store_dashboard.php?page=inventory" style="display:inline;">
                                                    <input type="hidden" name="whatAction" value="deleteItem">
                                                    <input type="hidden" name="itemId" value="' . $row['Id'] . '">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this item?\')">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>';
                                    }

                                    echo '</div>
                                        </td>';
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No transactions found</td></tr>";
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
                        // Export table data to CSV
                        function exportTableToCSV(filename = 'table-data.csv') {
                            const rows = document.querySelectorAll("#supplyTable tr");
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
    <?php elseif ($tab === 'low'): ?>
        <div class="card card-border shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="mb-3">Low Stock Items</h5>
                <p class="text-muted mb-3">Items that need to be restocked soon</p>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="inventoryTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Current Stock</th>
                                <th>Reorder Point</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php

                            // Fetch transactions from the database
                            $result = $conn->query("SELECT * FROM retail_invetory WHERE inventory_of = '$user_name' AND status = 'Low stock' ORDER BY Id DESC");

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['Id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                                    echo '<td class="text-danger">' . htmlspecialchars($row['stock']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['reorder_point']) . "</td>";
                                    echo "<td>₹" . number_format($row['price']) . "/" . htmlspecialchars($row['unit']) . "</td>";
                                    echo '<td><button type="submit" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#requestStock">
                                                    <i class="fas fa-truck me-1"></i> Order Now
                                                </button></td>';
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No transactions found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <script>
                        // Search Functionality
                        document.getElementById('searchInput').addEventListener('input', function () {
                            const searchText = this.value.toLowerCase();
                            const rows = document.querySelectorAll('#inventoryTable tbody tr');

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
                            const rows = document.querySelectorAll("#inventoryTable tr");
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
    <?php elseif ($tab === 'out'): ?>
        <div class="card card-border shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="mb-3">Out of Stock Items</h5>
                <p class="text-muted mb-3">Items that need immediate attention</p>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="outofStock">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Current Stock</th>
                                <th>Reorder Point</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php

                            // Fetch transactions from the database
                            $result = $conn->query("SELECT * FROM retail_invetory WHERE inventory_of = '$user_name' AND status = 'Low stock' ORDER BY Id DESC");

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['Id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                                    echo '<td class="text-danger">' . htmlspecialchars($row['stock']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['reorder_point']) . "</td>";
                                    echo "<td>₹" . number_format($row['price']) . "/" . htmlspecialchars($row['unit']) . "</td>";
                                    echo '<td><button type="submit" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#requestStock">
                                                    <i class="fas fa-truck me-1"></i> Order Now
                                                </button></td>';
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No transactions found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <script>
                        // Search Functionality
                        document.getElementById('searchInput').addEventListener('input', function () {
                            const searchText = this.value.toLowerCase();
                            const rows = document.querySelectorAll('#outofStock tbody tr');

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
                            const rows = document.querySelectorAll("#outofStock tr");
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
    <?php endif; ?>

    <!-- Stock Level Summary -->
    <div class="row row-cols-1 row-cols-lg-2 g-4 mb-4">
        <div class="col">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Stock Level Summary</h5>
                    <div class="space-y-4">
                        <?php

                        // Fetch items for the user
                        $sql = "SELECT item_name, stock, reorder_point FROM retail_invetory WHERE inventory_of = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("s", $user_name);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Loop through items and calculate percentage
                        while ($row = $result->fetch_assoc()) {
                            $itemName = htmlspecialchars($row['item_name']);
                            $stock = (int) $row['stock'];
                            $reorderPoint = (int) $row['reorder_point'];

                            // Prevent division by zero
                            $maxStock = max($reorderPoint * 2, 1); // Optional logic: Max stock is double of reorder point
                            $percentage = min(100, ($stock / $maxStock) * 100);
                            ?>

                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <span class="stock-label"><?= $itemName ?></span>
                                    <span class="stock-count"><?= $stock ?> unit</span>
                                </div>
                                <div class="progress bg-light">
                                    <div class="progress-bar bg-primary" style="width: <?= $percentage ?>%"></div>
                                    <div class="progress-bar bg-warning" style="width: <?= 100 - $percentage ?>%"></div>
                                </div>
                            </div>

                            <?php
                        }
                        $stmt->close();
                        ?>

                    </div>
                </div>
            </div>
        </div>

        <?php
        $currentUser = 'Store';

        // Total items
        $sqlTotalItems = "SELECT SUM(stock) AS total FROM retail_invetory WHERE inventory_of = ?";
        $stmt1 = $conn->prepare($sqlTotalItems);
        $stmt1->bind_param("s", $user_name);
        $stmt1->execute();
        $result1 = $stmt1->get_result()->fetch_assoc();
        $totalItems = $result1['total'];

        // Low stock items (where stock <= reorder_point)
        $sqlLowStock = "SELECT COUNT(*) AS lowStock FROM retail_invetory WHERE inventory_of = ? AND stock <= reorder_point";
        $stmt2 = $conn->prepare($sqlLowStock);
        $stmt2->bind_param("s", $user_name);
        $stmt2->execute();
        $result2 = $stmt2->get_result()->fetch_assoc();
        $lowStockItems = $result2['lowStock'];

        // Items in Transit (from 'request' table with status 'Ordered')
        $sqlTransit = "SELECT COUNT(*) AS transit FROM retail_store_stock_request WHERE requested_by = ? AND status = 'Ordered'";
        $stmt3 = $conn->prepare($sqlTransit);
        $stmt3->bind_param("s", $user_name);
        $stmt3->execute();
        $result3 = $stmt3->get_result()->fetch_assoc();
        $itemsInTransit = $result3['transit'];

        // Inventory Value (sum of stock * price)
        $sqlValue = "SELECT SUM(stock * price) AS totalValue FROM retail_invetory WHERE inventory_of = ?";
        $stmt4 = $conn->prepare($sqlValue);
        $stmt4->bind_param("s", $user_name);
        $stmt4->execute();
        $result4 = $stmt4->get_result()->fetch_assoc();
        $inventoryValue = number_format($result4['totalValue'], 2);

        // Store in array
        $inventory_stats = [
            'totalItems' => $totalItems,
            'lowStockItems' => $lowStockItems,
            'itemsInTransit' => $itemsInTransit,
            'inventoryValue' => '₹' . $inventoryValue,
        ];

        // Close statements and connection
        $stmt1->close();
        $stmt2->close();
        $stmt3->close();
        $stmt4->close();
        ?>


        <div class="col">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Inventory Statistics</h5>
                    <div class="row row-cols-1 row-cols-md-2 g-4">
                        <div class="col">
                            <div class="bg-primary-subtle p-3 rounded">
                                <h6 class="text-primary text-sm font-medium mb-1">Total Items</h6>
                                <p class="fs-4 font-bold">
                                    <?php echo htmlspecialchars($inventory_stats['totalItems']); ?>
                                </p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="bg-success-subtle p-3 rounded">
                                <h6 class="text-success text-sm font-medium mb-1">Requested ITems</h6>
                                <p class="fs-4 font-bold">
                                    <?php echo htmlspecialchars($inventory_stats['itemsInTransit']); ?>
                                </p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="bg-warning-subtle p-3 rounded">
                                <h6 class="text-warning text-sm font-medium mb-1">Low Stock Items</h6>
                                <p class="fs-4 font-bold">
                                    <?php echo htmlspecialchars($inventory_stats['lowStockItems']); ?>
                                </p>
                                <p class="text-xs text-muted mt-1">Need reordering soon</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="bg-purple-subtle p-3 rounded">
                                <h6 class="text-sm font-medium mb-1">Inventory Value</h6>
                                <p class="fs-4 font-bold">
                                    <?php echo htmlspecialchars($inventory_stats['inventoryValue']); ?>
                                </p>
                                <p class="text-xs mt-1">At retail prices</p>
                            </div>
                        </div>
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

    .text-sm {
        font-size: 0.875rem;
    }

    .fs-4 {
        font-size: 1.5rem;
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

    .bg-purple-subtle {
        background-color: #6f42c1 !important;
        color: #fff;
    }

    .text-purple {
        color: #6f42c1;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var editModal = document.getElementById('editPriceModal');
        editModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var itemId = button.getAttribute('data-id');
            var itemName = button.getAttribute('data-name');
            var itemPrice = button.getAttribute('data-price');

            document.getElementById('editItemId').value = itemId;
            document.getElementById('editItemName').value = itemName;
            document.getElementById('newPrice').value = itemPrice;
        });
    });
</script>