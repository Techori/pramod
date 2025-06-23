<?php
include '../../_conn.php';


// Handle Add Product
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['whatAction'])) {
    if ($_POST['whatAction'] === 'AddProduct') {
        $name = $_POST['name'] ?? '';
        $category = $_POST['category'] ?? '';
        $mrp = $_POST['mrp'] ?? '';
        $gst_rate = $_POST['gst_rate'] ?? '';
        $selling_price = $_POST['selling_price'] ?? '';
        $stock = $_POST['stock'] ?? '';
        $add_inventory = $_POST['add_inventory'] && $_POST['add_inventory'] === '1';
        $status = $_POST['status'] ?? '';
        $supplier = $_POST['supplier'] ?? '';
        $date = $_POST['date'] ?? '';
        $name = !empty($_POST['customCreatedBy']) ? mysqli_real_escape_string($conn, $_POST['customCreatedBy'] ?? '') : mysqli_real_escape_string($conn, $_POST['createdBy'] ?? '');

        // Validate Product Data
        if (empty($name) || empty($category) || empty($mrp) || empty($gst_rate) || empty($selling_price) || empty($stock)) {
            $error_message = 'All product fields are required.';
        } elseif ($add_inventory && (empty($status) || empty($supplier) || empty($name))) {
            $error_message = 'Status, Supplier and Created By are required when adding an inventory record.';
        } elseif ($stock < 0) {
            $error_message = 'Initial stock quantity cannot be negative.';
        } else {
            $conn->begin_transaction();
            try {
                // Generate Product ID (P-NNN)
                $id_query = "SELECT COUNT(*) as count FROM products";
                $id_result = $conn->query($id_query);
                $count = $id_result ? ($id_result->fetch_assoc()['count'] + 1) : 1;
                $product_id = "P-" . str_pad($count, 3, '0', STR_PAD_LEFT);
                $id_result->free();

                // Insert into products table with initial stock_quantity
                $sql = "INSERT INTO products (id, mrp, name, category, gst_rate, selling_price, stock_quantity) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $initial_stock = $add_inventory ? 0 : (int) $stock; // If adding inventory record, stock starts at 0
                $stmt->bind_param("sdssddi", $product_id, $mrp, $name, $category, $gst_rate, $selling_price, $initial_stock);
                $stmt->execute();
                $stmt->close();

                // Optionally add inventory record
                if ($add_inventory) {
                    $allowedStatus = ['In Stock', 'Low Stock'];
                    if (!in_array($status, $allowedStatus)) {
                        throw new Exception("Invalid status");
                    }

                    // Generate Inventory ID (TRX-NNN)
                    $inv_result = $conn->query("SELECT Id FROM inventory ORDER BY CAST(SUBSTRING(Id, 5) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");
                    $newNum = 1;
                    if ($inv_result && $row = $inv_result->fetch_assoc()) {
                        $lastId = $row['Id'];
                        $num = (int) substr($lastId, 4);
                        $newNum = $num + 1;
                    }
                    $newTransactionId = 'TRX-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);
                    $inv_result->free();

                    // Insert into inventory table (Transaction_Type is "Add Stock" for initial record)
                    $inv_sql = "INSERT INTO inventory (Id, Product_Name, Category, Stock, Transaction_Type, Status, Supplier, product_id) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $inv_stmt = $conn->prepare($inv_sql);
                    $transaction_type = "Add Stock";
                    $inv_stmt->bind_param("ssssssss", $newTransactionId, $name, $category, $stock, $transaction_type, $status, $supplier, $product_id);
                    $inv_stmt->execute();
                    $inv_stmt->close();

                    // Update product stock_quantity
                    $update_sql = "UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("is", $stock, $product_id);
                    $update_stmt->execute();
                    $update_stmt->close();

                    // Generate expense ID (EXP-YYYY-NNN)
                    $year = date('Y', strtotime($date));
                    $id_query = "SELECT COUNT(*) as count FROM expenses WHERE YEAR(date) = '$year'";
                    $id_result = $conn->query($id_query);
                    $count = $id_result ? ($id_result->fetch_assoc()['count'] + 1) : 1;
                    $id = "EXP-$year-" . str_pad($count, 3, '0', STR_PAD_LEFT);
                    $id_result->free();

                    $mrp_price = (int) $mrp;
                    $stock_quantity = (int) $stock;
                    $amount = $mrp_price * $stock_quantity;

                    // Insert into database
                    $exp_sql = "INSERT INTO expenses (id, date, category, addedBy, amount, vendor, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $exp_stmt = $conn->prepare($exp_sql);
                    $exp_stmt->bind_param("ssssdss", $id, $date, $category, $name, $amount, $supplier, $status);
                    $exp_stmt->execute();
                    $exp_stmt->close();
                }

                $conn->commit();
                $success_message = 'Product added successfully.';
                if ($add_inventory) {
                    $success_message .= ' Inventory record added.';
                }
            } catch (Exception $e) {
                $conn->rollback();
                $error_message = "Transaction failed: " . $e->getMessage();
            }
        }
    }

    // Handle Add Inventory Record
    if ($_POST['whatAction'] === 'AddInventory') {
        $product_id = $_POST['product_id'] ?? '';
        $stock = $_POST['stock'] ?? '';
        $transaction_type = $_POST['transaction_type'] ?? '';
        $status = $_POST['status'] ?? '';
        $amount = $_POST['amount'] ?? '';
        $supplier = $_POST['supplier'] ?? '';
        $date = $_POST['date'] ?? '';
        $name = !empty($_POST['customCreatedBy']) ? mysqli_real_escape_string($conn, $_POST['customCreatedBy'] ?? '') : mysqli_real_escape_string($conn, $_POST['createdBy'] ?? '');

        // Validate Inventory Data
        if (empty($product_id) || $stock === '' || empty($transaction_type) || empty($status) || empty($supplier) || empty($name)) {
            $error_message = 'All inventory fields are required.';
        } elseif ((int) $stock <= 0) {
            $error_message = 'Stock quantity must be a positive number.';
        } else {
            $allowedStatus = ['In Stock', 'Low Stock'];
            $allowedTransactionTypes = ['Add Stock', 'Deduct Stock'];
            if (!in_array($status, $allowedStatus)) {
                $error_message = 'Invalid status.';
            } elseif (!in_array($transaction_type, $allowedTransactionTypes)) {
                $error_message = 'Invalid transaction type.';
            } else {
                $conn->begin_transaction();
                try {
                    // Fetch product details
                    $product_query = "SELECT name, category, stock_quantity FROM products WHERE id = ?";
                    $product_stmt = $conn->prepare($product_query);
                    $product_stmt->bind_param("s", $product_id);
                    $product_stmt->execute();
                    $product_result = $product_stmt->get_result();
                    if ($product_result->num_rows === 0) {
                        throw new Exception("Product not found.");
                    }
                    $product = $product_result->fetch_assoc();
                    $product_name = $product['name'];
                    $category = $product['category'];
                    $current_stock = (int) $product['stock_quantity'];
                    $product_stmt->close();

                    // Determine stock change based on transaction type
                    $stock_quantity = (int) $stock;
                    $stock_change = ($transaction_type === 'Add Stock') ? $stock_quantity : -$stock_quantity;

                    // Validate stock availability for deductions
                    $new_stock = $current_stock + $stock_change;
                    if ($new_stock < 0) {
                        throw new Exception("Insufficient stock. Available: $current_stock, Requested deduction: " . abs($stock_change));
                    }

                    // Generate Inventory ID (TRX-NNN)
                    $inv_result = $conn->query("SELECT Id FROM inventory ORDER BY CAST(SUBSTRING(Id, 5) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");
                    $newNum = 1;
                    if ($inv_result && $row = $inv_result->fetch_assoc()) {
                        $lastId = $row['Id'];
                        $num = (int) substr($lastId, 4);
                        $newNum = $num + 1;
                    }
                    $newTransactionId = 'TRX-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);
                    $inv_result->free();

                    // Insert into inventory table
                    $inv_sql = "INSERT INTO inventory (Id, Product_Name, Category, Stock, Transaction_Type, Status, Supplier, product_id) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $inv_stmt = $conn->prepare($inv_sql);
                    $inv_stmt->bind_param("ssssssss", $newTransactionId, $product_name, $category, $stock_quantity, $transaction_type, $status, $supplier, $product_id);
                    $inv_stmt->execute();
                    $inv_stmt->close();

                    // Update product stock_quantity
                    $update_sql = "UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("is", $stock_change, $product_id);
                    $update_stmt->execute();
                    $update_stmt->close();

                    // Generate expense ID (EXP-YYYY-NNN)
                    $year = date('Y', strtotime($date));
                    $id_query = "SELECT COUNT(*) as count FROM expenses WHERE YEAR(date) = '$year'";
                    $id_result = $conn->query($id_query);
                    $count = $id_result ? ($id_result->fetch_assoc()['count'] + 1) : 1;
                    $id = "EXP-$year-" . str_pad($count, 3, '0', STR_PAD_LEFT);
                    $id_result->free();

                    // Insert into database
                    $exp_sql = "INSERT INTO expenses (id, date, category, addedBy, amount, vendor, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $exp_stmt = $conn->prepare($exp_sql);
                    $exp_stmt->bind_param("ssssdss", $id, $date, $category, $name, $amount, $supplier, $status);
                    $exp_stmt->execute();
                    $exp_stmt->close();

                    $conn->commit();
                    $success_message = 'Inventory record added successfully.';
                } catch (Exception $e) {
                    $conn->rollback();
                    $error_message = "Transaction failed: " . $e->getMessage();
                }
            }
        }
    }

    // Handle Edit Product
    if ($_POST['whatAction'] === 'EditProduct') {
        $id = $_POST['id'] ?? '';
        $name = $_POST['name'] ?? '';
        $category = $_POST['category'] ?? '';
        $mrp = $_POST['mrp'] ?? '';
        $gst_rate = $_POST['gst_rate'] ?? '';
        $selling_price = $_POST['selling_price'] ?? '';
        $stock = $_POST['stock'] ?? '';

        if (empty($id) || empty($name) || empty($category) || empty($mrp) || empty($gst_rate) || empty($selling_price) || empty($stock)) {
            $error_message = 'All fields are required.';
        } elseif ((int) $stock < 0) {
            $error_message = 'Stock quantity cannot be negative.';
        } else {
            $sql = "UPDATE products SET mrp = ?, name = ?, category = ?, gst_rate = ?, selling_price = ?, stock_quantity = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("dssddis", $mrp, $name, $category, $gst_rate, $selling_price, $stock, $id);
                if ($stmt->execute()) {
                    // Update inventory records with new name and category
                    $update_inv_sql = "UPDATE inventory SET Product_Name = ?, Category = ? WHERE product_id = ?";
                    $update_inv_stmt = $conn->prepare($update_inv_sql);
                    $update_inv_stmt->bind_param("sss", $name, $category, $id);
                    $update_inv_stmt->execute();
                    $update_inv_stmt->close();

                    $success_message = 'Product updated successfully.';
                } else {
                    $error_message = 'Error: ' . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_message = 'Error preparing statement: ' . $conn->error;
            }
        }
    }

    // Handle Delete Product
    if ($_POST['whatAction'] === 'DeleteProduct') {
        $id = $_POST['id'] ?? '';

        if (empty($id)) {
            $error_message = 'Product ID is required.';
        } else {
            $conn->begin_transaction();
            try {
                // Delete the product from products table (inventory records remain unaffected)
                $delete_sql = "DELETE FROM products WHERE id = ?";
                $delete_stmt = $conn->prepare($delete_sql);
                $delete_stmt->bind_param("s", $id);
                $delete_stmt->execute();
                $delete_stmt->close();

                $conn->commit();
                $success_message = 'Product deleted successfully.';
            } catch (Exception $e) {
                $conn->rollback();
                $error_message = "Delete failed: " . $e->getMessage();
            }
        }
    }
}

// Card Data (based on products table)
$total_products_query = "SELECT COUNT(*) as total, COUNT(DISTINCT category) as categories FROM products";
$total_products_result = $conn->query($total_products_query);
$total_products_data = $total_products_result ? $total_products_result->fetch_assoc() : ['total' => 0, 'categories' => 0];
$total_products = $total_products_data['total'];
$total_categories = $total_products_data['categories'];
$total_products_result->free();

$low_stock_query = "SELECT COUNT(*) as count FROM products WHERE stock_quantity < 50";
$low_stock_result = $conn->query($low_stock_query);
$low_stock = $low_stock_result ? $low_stock_result->fetch_assoc()['count'] : 0;
$low_stock_result->free();

$total_stock_value_query = "SELECT SUM(stock_quantity * selling_price) as total FROM products";
$total_stock_value_result = $conn->query($total_stock_value_query);
$total_stock_value = $total_stock_value_result ? $total_stock_value_result->fetch_assoc()['total'] ?? 0 : 0;
$total_stock_value_result->free();

$avg_gst_query = "SELECT AVG(gst_rate) as avg FROM products";
$avg_gst_result = $conn->query($avg_gst_query);
$avg_gst = $avg_gst_result ? $avg_gst_result->fetch_assoc()['avg'] ?? 0 : 0;
$avg_gst_result->free();

// Fetch products for Products Table
$products_query = "SELECT id, name, category, stock_quantity, mrp, gst_rate, selling_price FROM products ORDER BY name LIMIT 10";
$products_result = $conn->query($products_query);

// Fetch inventory records for Inventory Table
$inventory_query = "SELECT Id, Product_Name, Category, Stock, Transaction_Type, Status, Supplier FROM inventory ORDER BY Id DESC LIMIT 10";
$inventory_result = $conn->query($inventory_query);

// Fetch all products for the Add Inventory modal dropdown
$all_products_query = "SELECT id, name, category FROM products ORDER BY name";
$all_products_result = $conn->query($all_products_query);
$all_products = [];
if ($all_products_result) {
    while ($row = $all_products_result->fetch_assoc()) {
        $all_products[] = $row;
    }
    $all_products_result->free();
}

// Pie Chart: Stock by Category (products table)
$pie_query = "SELECT category, SUM(stock_quantity) as total FROM products GROUP BY category";
$pie_result = $conn->query($pie_query);
$pie_labels = [];
$pie_data = [];
$pie_colors = ['#0d6efd', '#20c997', '#ffc107', '#fd7e14', '#6f42c1', '#C66EF9'];
$color_index = 0;
if ($pie_result) {
    while ($row = $pie_result->fetch_assoc()) {
        $pie_labels[] = $row['category'];
        $pie_data[] = $row['total'];
        $color_index = ($color_index + 1) % count($pie_colors);
    }
    $pie_result->free();
}

// Bar Chart: Top 5 Products by Stock (products table)
$bar_query = "SELECT name, stock_quantity FROM products ORDER BY stock_quantity DESC LIMIT 5";
$bar_result = $conn->query($bar_query);
$bar_labels = [];
$bar_data = [];
if ($bar_result) {
    while ($row = $bar_result->fetch_assoc()) {
        $bar_labels[] = $row['name'];
        $bar_data[] = $row['stock_quantity'];
    }
    $bar_result->free();
}
?>

<style>
    .cards {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
        height: 100%;
    }

    .cards:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.1);
    }

    .card-border {
        border-radius: 0.5rem;
        border-top: none;
        border-right: none;
        border-bottom: none;
    }

    .chart-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
    }

    .chart-box {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        width: 100%;
        max-width: 600px;
        flex: 1 1 300px;
    }

    h3 {
        margin-bottom: 15px;
    }

    canvas {
        width: 100% !important;
        height: auto !important;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th,
    td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
    }

    .alert-dismissible {
        position: relative;
        padding-right: 4rem;
    }

    .alert-dismissible .btn-close {
        position: absolute;
        top: 0;
        right: 0;
        padding: 1rem;
    }
</style>

<h1>Inventory Dashboard</h1>
<p>Manage stock and products</p>

<!-- Error/Success Messages -->
<?php if (isset($error_message)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($error_message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php if (isset($success_message)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($success_message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Cards -->
<div class="row">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
            <div class="card-body">
                <h6 class="text-muted">Total Products</h6>
                <h3 class="fw-bold"><?php echo htmlspecialchars($total_products); ?></h3>
                <p>Across <?php echo htmlspecialchars($total_categories); ?> categories</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
            <div class="card-body">
                <h6 class="text-muted">Low Stock Items</h6>
                <h3 class="fw-bold"><?php echo htmlspecialchars($low_stock); ?></h3>
                <p class="text-danger">Requires attention</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
            <div class="card-body">
                <h6 class="text-muted">Total Stock Value</h6>
                <h3 class="fw-bold">₹<?php echo number_format($total_stock_value, 0); ?></h3>
                <p class="text-success">Based on selling price</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
            <div class="card-body">
                <h6 class="text-muted">Average GST Rate</h6>
                <h3 class="fw-bold"><?php echo number_format($avg_gst, 2); ?>%</h3>
                <p class="text-info-emphasis">Across all products</p>
            </div>
        </div>
    </div>
</div>

<!-- Search Bar & Buttons -->
<div class="container-fluid d-flex justify-content-between align-items-center">
    <div class="d-flex">
        <div class="input-group w-100 me-2">
            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Search..."
                onkeyup="filterTables()" />
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary d-flex" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="fa-solid fa-circle-plus"></i><span> Add</span><span> Product</span>
            </button>
            <button class="btn btn-outline-primary d-flex" data-bs-toggle="modal" data-bs-target="#addInventoryModal">
                <i class="fa-solid fa-box"></i><span> Add</span><span> Inventory</span>
            </button>
            <a href="?page=reports" class="btn btn-outline-primary d-flex">
                <i class="fa-solid fa-chart-column"></i> Report
            </a>
            <button class="btn btn-outline-primary d-flex" id="refreshBtn">
                <i class="fa-solid fa-arrows-rotate"></i> Refresh
            </button>
        </div>
    </div>
</div>


<?php
// Get names for Add expense form dropdown
$itemSql = "SELECT DISTINCT addedBy FROM expenses ORDER BY addedBy";
$itemResult = $conn->query($itemSql);
$items = [];
if ($itemResult->num_rows > 0) {
    while ($row = $itemResult->fetch_assoc()) {
        $items[] = $row['addedBy'];
    }
}
?>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="admin_dashboard.php?page=inventory">
                <input type="hidden" name="whatAction" value="AddProduct">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductLabel">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" class="form-control" id="category" name="category" required>
                    </div>
                    <div class="mb-3">
                        <label for="mrp" class="form-label">MRP (₹)</label>
                        <input type="number" step="0.01" class="form-control" id="mrp" name="mrp" required>
                    </div>
                    <div class="mb-3">
                        <label for="gst_rate" class="form-label">GST Rate (%)</label>
                        <input type="number" step="0.01" class="form-control" id="gst_rate" name="gst_rate" required>
                    </div>
                    <div class="mb-3">
                        <label for="selling_price" class="form-label">Selling Price (₹)</label>
                        <input type="number" step="0.01" class="form-control" id="selling_price" name="selling_price"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="stock" class="form-label">Initial Stock Quantity</label>
                        <input type="number" class="form-control" id="stock" name="stock" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="add_inventory" name="add_inventory"
                            value="1">
                        <label class="form-check-label" for="add_inventory">Add Initial Inventory Record</label>
                    </div>
                    <div class="mb-3" id="status_field" style="display: none;">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="In Stock">In Stock</option>
                            <option value="Low Stock">Low Stock</option>
                        </select>
                    </div>
                    <div class="mb-3" id="supplier_field" style="display: none;">
                        <label for="supplier" class="form-label">Supplier</label>
                        <input type="text" class="form-control" id="supplier" name="supplier">
                    </div>
                    <div class="mb-3" id="name_field" style="display: none;">
                        <label for="createdBy" class="form-label">Created by</label>
                        <select class="form-control" id="createdBy" name="createdBy" onchange="toggleItemInput()">
                            <option value="">Select Name</option>
                            <?php foreach ($items as $item): ?>
                                <option value="<?php echo htmlspecialchars($item); ?>">
                                    <?php echo htmlspecialchars($item); ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="Other">Other</option>
                        </select>
                        <input type="text" class="form-control mt-2" id="customCreatedBy" name="customCreatedBy"
                            style="display:none;" placeholder="Enter new name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Inventory Modal -->
<div class="modal fade" id="addInventoryModal" tabindex="-1" aria-labelledby="addInventoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="admin_dashboard.php?page=inventory">
                <input type="hidden" name="whatAction" value="AddInventory">
                <div class="modal-header">
                    <h5 class="modal-title" id="addInventoryLabel">Add Inventory Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="product_id" class="form-label">Product</label>
                        <select class="form-select" id="product_id" name="product_id" required>
                            <option value="">Select Product</option>
                            <?php foreach ($all_products as $product): ?>
                                <option value="<?php echo htmlspecialchars($product['id']); ?>">
                                    <?php echo htmlspecialchars($product['name']) . ' (' . htmlspecialchars($product['category']) . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="createdBy" class="form-label">Created by</label>
                        <select class="form-control" id="createdBy" name="createdBy" onchange="toggleItemInput()">
                            <option value="">Select Name</option>
                            <?php foreach ($items as $item): ?>
                                <option value="<?php echo htmlspecialchars($item); ?>">
                                    <?php echo htmlspecialchars($item); ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="Other">Other</option>
                        </select>
                        <input type="text" class="form-control mt-2" id="customCreatedBy" name="customCreatedBy"
                            style="display:none;" placeholder="Enter new name">
                    </div>
                    <div class="mb-3">
                        <label for="transaction_type" class="form-label">Transaction Type</label>
                        <select class="form-select" id="transaction_type" name="transaction_type" required>
                            <option value="Add Stock">Add Stock</option>
                            <option value="Deduct Stock">Deduct Stock</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="inv_stock" class="form-label">Stock Quantity</label>
                        <input type="number" min="1" class="form-control" id="inv_stock" name="stock" required>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="inv_status" class="form-label">Status</label>
                        <select class="form-select" id="inv_status" name="status" required>
                            <option value="In Stock">In Stock</option>
                            <option value="Low Stock">Low Stock</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="inv_supplier" class="form-label">Supplier</label>
                        <input type="text" class="form-control" id="inv_supplier" name="supplier" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Inventory Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleItemInput() {
        const createdBySelect = document.getElementById('createdBy');
        const customCreatedByInput = document.getElementById('customCreatedBy');
        if (createdBySelect.value === 'Other') {
            customCreatedByInput.style.display = 'block';
            customCreatedByInput.required = true;
        } else {
            customCreatedByInput.style.display = 'none';
            customCreatedByInput.required = false;
        }
    }

</script>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="admin_dashboard.php?page=inventory">
                <input type="hidden" name="whatAction" value="EditProduct">
                <input type="hidden" name="id" id="editProductId">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editName" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editCategory" class="form-label">Category</label>
                        <input type="text" class="form-control" id="editCategory" name="category" required>
                    </div>
                    <div class="mb-3">
                        <label for="editMrp" class="form-label">MRP (₹)</label>
                        <input type="number" step="0.01" class="form-control" id="editMrp" name="mrp" required>
                    </div>
                    <div class="mb-3">
                        <label for="editGstRate" class="form-label">GST Rate (%)</label>
                        <input type="number" step="0.01" class="form-control" id="editGstRate" name="gst_rate" required>
                    </div>
                    <div class="mb-3">
                        <label for="editSellingPrice" class="form-label">Selling Price (₹)</label>
                        <input type="number" step="0.01" class="form-control" id="editSellingPrice" name="selling_price"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="editStock" class="form-label">Stock Quantity</label>
                        <input type="number" class="form-control" id="editStock" name="stock" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Product Modal -->
<div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="admin_dashboard.php?page=inventory">
                <input type="hidden" name="whatAction" value="DeleteProduct">
                <input type="hidden" name="id" id="deleteProductId">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteProductLabel">Delete Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this product? This action will not affect existing inventory
                        transactions.</p>
                    <p><strong>Product ID:</strong> <span id="deleteProductIdDisplay"></span></p>
                    <p><strong>Name:</strong> <span id="deleteProductName"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="chart-container">
    <div class="chart-box">
        <h3>Stock by Category</h3>
        <div style="position: relative; width: 100%; max-width: 300px; margin: 0 auto;">
            <canvas id="pieChart"></canvas>
        </div>
    </div>
    <div class="chart-box">
        <h3>Top 5 Products by Stock</h3>
        <canvas id="barChart"></canvas>
    </div>
</div>

<!-- Products Table -->
<div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">
    <div id="products">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="justify-content-start">
                <h1>Products</h1>
            </div>
            <div class="justify-content-end">
                <button class="btn btn-outline-primary" id="viewAllProductsBtn">View All</button>
            </div>
        </div>
        <table id="productsTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>MRP (₹)</th>
                    <th>GST Rate (%)</th>
                    <th>Selling Price (₹)</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($products_result && $products_result->num_rows > 0): ?>
                    <?php while ($product = $products_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['id']); ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                            <td><?php echo number_format($product['mrp'], 2); ?></td>
                            <td><?php echo number_format($product['gst_rate'], 2); ?></td>
                            <td><?php echo number_format($product['selling_price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($product['stock_quantity']); ?></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary btn-sm edit-product-btn" data-bs-toggle="modal"
                                        data-bs-target="#editProductModal"
                                        data-id="<?php echo htmlspecialchars($product['id']); ?>"
                                        data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                        data-category="<?php echo htmlspecialchars($product['category']); ?>"
                                        data-mrp="<?php echo htmlspecialchars($product['mrp']); ?>"
                                        data-gst-rate="<?php echo htmlspecialchars($product['gst_rate']); ?>"
                                        data-selling-price="<?php echo htmlspecialchars($product['selling_price']); ?>"
                                        data-stock="<?php echo htmlspecialchars($product['stock_quantity']); ?>">
                                        <i class="fa-solid fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm delete-product-btn" data-bs-toggle="modal"
                                        data-bs-target="#deleteProductModal"
                                        data-id="<?php echo htmlspecialchars($product['id']); ?>"
                                        data-name="<?php echo htmlspecialchars($product['name']); ?>">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No products found.</td>
                    </tr>
                <?php endif; ?>
                <?php $products_result->free(); ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Inventory Table -->
<div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">
    <div id="inventory">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="justify-content-start">
                <h1>Inventory Transactions</h1>
            </div>
            <div class="justify-content-end">
                <button class="btn btn-outline-primary" id="viewAllInventoryBtn">View All</button>
            </div>
        </div>
        <table id="inventoryTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Stock Quantity</th>
                    <th>Transaction Type</th>
                    <th>Status</th>
                    <th>Supplier</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($inventory_result && $inventory_result->num_rows > 0): ?>
                    <?php while ($inventory = $inventory_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($inventory['Id']); ?></td>
                            <td><?php echo htmlspecialchars($inventory['Product_Name']); ?></td>
                            <td><?php echo htmlspecialchars($inventory['Category']); ?></td>
                            <td><?php echo htmlspecialchars($inventory['Stock']); ?></td>
                            <td><?php echo htmlspecialchars($inventory['Transaction_Type']); ?></td>
                            <td>
                                <?php
                                $status_class = ($inventory['Status'] === 'In Stock') ? 'text-success' : 'text-danger';
                                echo "<span class='$status_class'>" . htmlspecialchars($inventory['Status']) . "</span>";
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($inventory['Supplier']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No inventory transactions found.</td>
                    </tr>
                <?php endif; ?>
                <?php $inventory_result->free(); ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Search Functionality for Both Tables
    function filterTables() {
        const input = document.getElementById('searchInput').value.toLowerCase();

        // Filter Products Table
        const productsTable = document.getElementById('productsTable');
        const productRows = productsTable.getElementsByTagName('tr');
        for (let i = 1; i < productRows.length; i++) {
            const cells = productRows[i].getElementsByTagName('td');
            const id = cells[0].textContent.toLowerCase();
            const name = cells[1].textContent.toLowerCase();
            const category = cells[2].textContent.toLowerCase();

            if (id.includes(input) || name.includes(input) || category.includes(input)) {
                productRows[i].style.display = '';
            } else {
                productRows[i].style.display = 'none';
            }
        }

        // Filter Inventory Table
        const inventoryTable = document.getElementById('inventoryTable');
        const inventoryRows = inventoryTable.getElementsByTagName('tr');
        for (let i = 1; i < inventoryRows.length; i++) {
            const cells = inventoryRows[i].getElementsByTagName('td');
            const id = cells[0].textContent.toLowerCase();
            const name = cells[1].textContent.toLowerCase();
            const category = cells[2].textContent.toLowerCase();
            const transaction_type = cells[4].textContent.toLowerCase();
            const supplier = cells[6].textContent.toLowerCase();

            if (id.includes(input) || name.includes(input) || category.includes(input) || transaction_type.includes(input) || supplier.includes(input)) {
                inventoryRows[i].style.display = '';
            } else {
                inventoryRows[i].style.display = 'none';
            }
        }
    }

    // Auto-Dismiss Alerts
    document.addEventListener('DOMContentLoaded', function () {
        // Auto-dismiss alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });

        // Populate Edit Product Modal
        document.querySelectorAll('.edit-product-btn').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.dataset.id;
                const name = this.dataset.name;
                const category = this.dataset.category;
                const mrp = this.dataset.mrp;
                const gstRate = this.dataset.gstRate;
                const sellingPrice = this.dataset.sellingPrice;
                const stock = this.dataset.stock;

                document.getElementById('editProductId').value = id;
                document.getElementById('editName').value = name;
                document.getElementById('editCategory').value = category;
                document.getElementById('editMrp').value = mrp;
                document.getElementById('editGstRate').value = gstRate;
                document.getElementById('editSellingPrice').value = sellingPrice;
                document.getElementById('editStock').value = stock;
            });
        });

        // Populate Delete Product Modal
        document.querySelectorAll('.delete-product-btn').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.dataset.id;
                const name = this.dataset.name;

                document.getElementById('deleteProductId').value = id;
                document.getElementById('deleteProductIdDisplay').textContent = id;
                document.getElementById('deleteProductName').textContent = name;
            });
        });

        // Show/Hide Status and Supplier Fields in Add Product Modal
        document.getElementById('add_inventory').addEventListener('change', function () {
            const statusField = document.getElementById('status_field');
            const supplierField = document.getElementById('supplier_field');
            const nameField = document.getElementById('name_field');
            statusField.style.display = this.checked ? 'block' : 'none';
            supplierField.style.display = this.checked ? 'block' : 'none';
            nameField.style.display = this.checked ? 'block' : 'none';
        });

        // Refresh Button
        document.getElementById('refreshBtn').addEventListener('click', function () {
            window.location.reload();
        });

        // View All Buttons
        document.getElementById('viewAllProductsBtn').addEventListener('click', function () {
            document.getElementById('searchInput').value = '';
            let rows = document.querySelectorAll('#productsTable tbody tr');
            rows.forEach(row => row.style.display = '');
        });

        document.getElementById('viewAllInventoryBtn').addEventListener('click', function () {
            document.getElementById('searchInput').value = '';
            let rows = document.querySelectorAll('#inventoryTable tbody tr');
            rows.forEach(row => row.style.display = '');
        });
    });

    // Pie Chart: Stock by Category
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($pie_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($pie_data); ?>,
                backgroundColor: <?php echo json_encode($pie_colors); ?>
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#333',
                        font: { size: 14 }
                    }
                }
            }
        }
    });

    // Bar Chart: Top 5 Products by Stock
    const barCtx = document.getElementById('barChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($bar_labels); ?>,
            datasets: [{
                label: 'Stock Quantity',
                data: <?php echo json_encode($bar_data); ?>,
                backgroundColor: '#0d6efd',
                borderColor: '#0d6efd',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Stock Quantity'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Product'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
</body>

</html>