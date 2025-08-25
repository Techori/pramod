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
        $mrp = $_POST['mrp'];
        $selling_price = $_POST['selling_price'];
        $unit = $_POST['unit'];
        $stock = $_POST['stock'];
        $reorderPoint = $_POST['reorderPoint'];
        $status = $_POST['Status'];
        $today = date('Y-m-d H:i:s');

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
            $result = $conn->query("SELECT product_id FROM vendor_product WHERE product_of = '$user_name' ORDER BY CAST(SUBSTRING(product_id, 6) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");

            if ($result && $row = $result->fetch_assoc()) {
                $lastId = $row['product_id']; // e.g. SL-005
                $num = (int) substr($lastId, 5);   // get "005" → 5
                $newNum = $num + 1;
            } else {
                $newNum = 1;
            }

            $newItemId = 'ITEM-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);

            // Insert the item record
            $stmt = $conn->prepare("INSERT INTO vendor_product 
                (product_id, product_name, category, stock, unit, mrp, selling_price, reorder_point, status, product_of, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param("sssisddissss", $newItemId, $itemName, $category, $stock, $unit, $mrp, $selling_price, $reorderPoint, $status, $user_name, $today, $today);
            $stmt->execute();

            $conn->commit();
            $stmt->close();

            header("Location: vendor_dashboard.php?page=products");
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

        $stmt = $conn->prepare("UPDATE vendor_product SET selling_price = ?, updated_at = NOW() WHERE product_id = ? AND product_of = ?");
        $stmt->bind_param("dss", $newPrice, $itemId, $user_name);
        $stmt->execute();
        $stmt->close();

        @header("Location: vendor_dashboard.php?page=products");

    } else if ($_POST['whatAction'] === 'deleteItem') {
        $itemId = $_POST['itemId'];

        $stmt = $conn->prepare("DELETE FROM vendor_product WHERE product_id = ? AND product_of = ?");
        $stmt->bind_param("ss", $itemId, $user_name);
        $stmt->execute();
        $stmt->close();

        @header("Location: vendor_dashboard.php?page=products");

    }
}

?>

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

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><i class="fas fa-box text-primary"></i> Product Management</h1>
            <p>Manage your product catalog, update inventory and pricing.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItem"><i class="fas fa-plus"></i> Add
            Product</button>
    </div>

    <!-- Products Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Product Inventory</h5>
            <!-- Search and Filters -->
            <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center mb-4">
                <div class="flex-grow-1">
                    <input type="hidden" name="page" value="billing">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0" id="searchInput"><i
                                class="fas fa-search"></i></span>
                        <input type="text" class="form-control border-start-0 table-search" data-table="productsTable"
                            placeholder="Search..." />
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <div>
                        <button class="btn btn-outline-primary gst-filter me-2" data-type="In stock"
                            data-table="productsTable">In stock</button>
                    </div>
                    <div>
                        <button class="btn btn-outline-primary gst-filter me-2" data-type="Low stock"
                            data-table="productsTable">Low stock</button>
                    </div>
                    <div>
                        <button class="btn btn-outline-primary gst-filter me-2" data-type="Out of stock"
                            data-table="productsTable">Out of stock</button>
                    </div>
                    <div>
                        <button class="btn btn-outline-danger reset-filters me-2" data-table="productsTable">Remove
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
                                const docType = row.children[7]?.innerText.trim().toLowerCase();
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
                <table class="table table-bordered table-hover" id="productsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Stock</th>
                            <th>MRP</th>
                            <th>Selling Price</th>
                            <th>Reorder Point</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        // Fetch transactions from the database
                        $result = $conn->query("SELECT * FROM vendor_product WHERE product_of = '$user_name' ORDER BY product_id DESC");

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['product_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['stock']) . "</td>";
                                echo "<td>₹" . number_format($row['mrp']) . "/" . htmlspecialchars($row['unit']) . "</td>";
                                echo "<td>₹" . number_format($row['selling_price']) . "/" . htmlspecialchars($row['unit']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['reorder_point']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                echo '<td>
                                            <div>
                                                <button class="btn btn-outline-primary btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editPriceModal" 
                                                        data-id="' . $row['product_id'] . '" 
                                                        data-name="' . htmlspecialchars($row['product_name']) . '" 
                                                        data-price="' . $row['selling_price'] . '">
                                                    <i class="fa-regular fa-pen-to-square"></i>
                                                </button>';

                                if ($hasDeletePermission) {
                                    echo '<form method="POST" action="vendor_dashboard.php?page=products" style="display:inline;">
                                                    <input type="hidden" name="whatAction" value="deleteItem">
                                                    <input type="hidden" name="itemId" value="' . $row['product_id'] . '">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this item?\')">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>';
                                }
                                echo '
                                            </div>
                                        </td>';
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center'>No transactions found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add New Item Form -->
<div class="modal fade" id="addItem" tabindex="-1" aria-labelledby="addItemLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="products.php" method="POST">
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
                        <label for="stock" class="form-label">Stock</label>
                        <input type="number" class="form-control" id="stock" name="stock" required>
                    </div>

                    <div class="mb-3">
                        <label for="unit" class="form-label">Unit</label>
                        <input type="text" class="form-control" id="unit" name="unit" required>
                    </div>

                    <div class="mb-3">
                        <label for="mrp" class="form-label">MRP</label>
                        <input type="number" class="form-control" id="mrp" name="mrp" required>
                    </div>

                    <div class="mb-3">
                        <label for="selling_price" class="form-label">Selling Price</label>
                        <input type="number" class="form-control" id="selling_price" name="selling_price" required>
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

<!-- Item price edite Modal -->
<div class="modal fade" id="editPriceModal" tabindex="-1" aria-labelledby="editPriceLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="products.php" class="modal-content">
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