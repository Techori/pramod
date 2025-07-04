<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../_conn.php';

$user_name = $_SESSION['user_name'];

if (!(isset($_SESSION["uid"]) && isset($_SESSION["user_type"]) && isset($_SESSION["session_id"]))) {
    header("location:../../login.php");
    exit;
} else {
    if (in_array($_SESSION["user_type"], ['Factory', 'Store', 'Vendor'])) {
        header("location:../index.php");
        exit;
    } else if (!($_SESSION["user_type"] == 'Admin')) {
        header("location:../../login.php");
        exit;
    }
}

$page = isset($_GET['page']) ? $_GET['page'] : 'admin_dashboard';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['whatAction'])) {

    function clean($input)
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    if ($_POST['whatAction'] === 'update') {
        // Get data from the form
        $invoice_id = $_POST['invoice_id'] ?? '';
        $status = $_POST['status'] ?? '';

        // Basic validation
        if (!empty($invoice_id) && !empty($status)) {
            // Prepare and execute the update query
            $stmt = $conn->prepare("UPDATE invoice SET status = ? WHERE invoice_id = ?");
            $stmt->bind_param("ss", $status, $invoice_id);

            if ($stmt->execute()) {
                // Redirect or show success message
                header("Location: admin_dashboard.php?page=admin_dashboard"); // Replace with your actual page
                exit();
            } else {
                echo "Error updating status: " . $conn->error;
            }

            $stmt->close();
        } else {
            echo "Invalid input.";
        }
    } else if ($_POST['whatAction'] === 'AddProduct') {
        $name = clean($_POST['name'] ?? '');
        $category = clean($_POST['category'] ?? '');
        $mrp = clean($_POST['mrp'] ?? '');
        $gst_rate = clean($_POST['gst_rate'] ?? '');
        $selling_price = clean($_POST['selling_price'] ?? '');
        $stock = clean($_POST['stock'] ?? '');
        $add_inventory = isset($_POST['add_inventory']) && $_POST['add_inventory'] === '1';
        $status = clean($_POST['status'] ?? '');
        $supplier = clean($_POST['supplier'] ?? '');

        // Validate Product Data
        if (empty($name) || empty($category) || empty($mrp) || empty($gst_rate) || empty($selling_price) || empty($stock)) {
            $error_message = 'All product fields are required.';
        } elseif ($add_inventory && (empty($status) || empty($supplier))) {
            $error_message = 'Status and Supplier are required when adding an inventory record.';
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
    // Handle Edit Product
    if ($_POST['whatAction'] === 'EditProduct') {
        $id = clean($_POST['id'] ?? '');
        $name = clean($_POST['name'] ?? '');
        $category = clean($_POST['category'] ?? '');
        $mrp = clean($_POST['mrp'] ?? '');
        $gst_rate = clean($_POST['gst_rate'] ?? '');
        $selling_price = clean($_POST['selling_price'] ?? '');
        $stock = clean($_POST['stock'] ?? '');

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
        $id = clean($_POST['id'] ?? '');

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

    // Transaction action
    if ($_POST['whatAction'] === 'addItem') {
        // Collect data for transaction
        $created_for = clean($_POST['created_for']);
        $itemName = clean($_POST['itemName']);
        $category = clean($_POST['category']);
        $price = clean($_POST['price']);
        $unit = clean($_POST['unit']);
        $stock = clean($_POST['stock']);
        $reorderPoint = clean($_POST['reorderPoint']);
        $status = clean($_POST['Status']);

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
            $result = $conn->query("SELECT Id FROM retail_invetory WHERE inventory_of = '$created_for' ORDER BY CAST(SUBSTRING(Id, 6) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");

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

            $stmt->bind_param("sssisdsssi", $newItemId, $itemName, $category, $stock, $unit, $price, $today, $status, $created_for, $reorderPoint);
            $stmt->execute();

            $conn->commit();
            $stmt->close();

            header("Location: admin_dashboard.php?page=admin_dashboard");
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
        $itemId = clean($_POST['itemId']);
        $newPrice = clean($_POST['newPrice']);
        $inventory_Of = clean($_POST['inventory_Of']);

        $stmt = $conn->prepare("UPDATE retail_invetory SET price = ?, last_updated = NOW() WHERE Id = ? AND inventory_of = ?");
        $stmt->bind_param("dss", $newPrice, $itemId, $inventory_Of);
        $stmt->execute();
        $stmt->close();

        @header("Location: admin_dashboard.php?page=admin_dashboard");
    } else if ($_POST['whatAction'] === 'deleteItem') {
        $itemId = clean($_POST['itemId']);
        $inventory_of = clean($_POST['inventory_of']);

        $stmt = $conn->prepare("DELETE FROM retail_invetory WHERE Id = ? AND inventory_of = ?");
        $stmt->bind_param("ss", $itemId, $inventory_of);
        $stmt->execute();
        $stmt->close();

        @header("Location: admin_dashboard.php?page=admin_dashboard");
    } else if ($_POST['whatAction'] === 'requestStock') {
        // Collect data for transaction
        $created_for = clean($_POST['created_for']);
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
            $result = $conn->query("SELECT request_id FROM retail_store_stock_request WHERE requested_by = '$created_for' ORDER BY CAST(SUBSTRING(request_id, 6) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");

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

            $stmt->bind_param("sssssssisss", $today, $newRequestId, $newTrackId, $requestTo, $shopName, $itemName, $category, $quantity, $location, $created_for, $status);
            $stmt->execute();

            $conn->commit();
            $stmt->close();

            header("Location: admin_dashboard.php?page=admin_dashboard");
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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="unnati">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../public/css/styles.css">
    
    <title>Shree Unnati Wires & Traders - Premium Wire Manufacturing</title>
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

        .tabs {
            display: flex;
            border-bottom: 2px solid #ddd;
        }

        .billingTab {
            padding: 10px 20px;
            cursor: pointer;
            border: none;
            background: none;
            font-size: 16px;
        }

        .billingTab.active {
            border-bottom: 3px solid #007bff;
            font-weight: bold;
            color: #007bff;
        }

        .billing-tab-content {
            display: none;
            padding: 20px 0;
        }

        .billing-tab-content.active {
            display: block;
        }

        .factoryTab {
            padding: 10px 20px;
            cursor: pointer;
            border: none;
            background: none;
            font-size: 16px;
        }

        .factoryTab.active {
            border-bottom: 3px solid #007bff;
            font-weight: bold;
            color: #007bff;
        }

        .factory-tab-content {
            display: none;
            padding: 20px 0;
        }

        .factory-tab-content.active {
            display: block;
        }

        .retailTab {
            padding: 10px 20px;
            cursor: pointer;
            border: none;
            background: none;
            font-size: 16px;
        }

        .retailTab.active {
            border-bottom: 3px solid #007bff;
            font-weight: bold;
            color: #007bff;
        }

        .retail-tab-content {
            display: none;
            padding: 20px 0;
        }

        .retail-tab-content.active {
            display: block;
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

        .modal-content {
            border-radius: 0.5rem;
        }

        .gst-section {
            display: block;
        }

        #itemTable input {
            width: 100px;
        }

        .text-end {
            text-align: right;
        }

        textarea {
            width: 100%;
            height: 60px;
            margin-top: 10px;
        }

        .bill-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: none;
            border-color: #0d6efd;
        }

        .btn-outline-danger:hover {
            color: white;
        }

        .total-section {
            background: #f1f1f1;
            padding: 15px;
            border-radius: 8px;
        }
    </style>
</head>

<body class="bg-secondary bg-opacity-10">
    <?php
    include './_admin_nav.php';
    ?>

    <main>
        <?php if ($page === 'admin_dashboard'): ?>

            <h1>Dashboard</h1>
            <p>Welcome back to your business management dashboard</p>


            <?php

            // Get current and last month info
            function getMonthYear($offset = 0)
            {
                $date = new DateTime();
                $date->modify("$offset month");
                return [$date->format('m'), $date->format('Y')];
            }
            list($currMonth, $currYear) = getMonthYear(0);
            list($lastMonth, $lastYear) = getMonthYear(-1);

            function percentageChange($current, $last)
            {
                if ($last == 0)
                    return 0;
                return round((($current - $last) / $last) * 100, 2);
            }

            // Get Total Sales
            $salesQuery = $conn->prepare("
                SELECT 
                    SUM(CASE WHEN MONTH(date) = ? AND YEAR(date) = ? THEN grand_total ELSE 0 END) as current_sales,
                    SUM(CASE WHEN MONTH(date) = ? AND YEAR(date) = ? THEN grand_total ELSE 0 END) as last_sales
                FROM invoice
            ");
            $salesQuery->bind_param("iiii", $currMonth, $currYear, $lastMonth, $lastYear);
            $salesQuery->execute();
            $sales = $salesQuery->get_result()->fetch_assoc();
            $salesAmount = $sales['current_sales'] ?: 0;
            $salesLast = $sales['last_sales'] ?: 0;
            $salesChange = percentageChange($salesAmount, $salesLast);
            $salesTrend = $salesChange >= 0 ? 'success' : 'danger';

            // Get Inventory Value
            $invQuery = $conn->query("
                SELECT 
                    SUM(mrp * stock_quantity) as total_value 
                FROM products
            ");
            $inv = $invQuery->fetch_assoc();
            $invAmount = $inv['total_value'] ?: 0;

            // If you want to compare with last month, you'll need to use created_at or updated_at
            $invLastQuery = $conn->prepare("
                SELECT SUM(mrp * stock_quantity) as last_value 
                FROM products 
                WHERE MONTH(updated_at) = ? AND YEAR(updated_at) = ?
            ");
            $invLastQuery->bind_param("ii", $lastMonth, $lastYear);
            $invLastQuery->execute();
            $invLast = $invLastQuery->get_result()->fetch_assoc()['last_value'] ?: 0;

            $invChange = percentageChange($invAmount, $invLast);
            $invTrend = $invChange >= 0 ? 'success' : 'danger';


            // Get BNPL Outstanding
            $bnplQuery = $conn->prepare("
                SELECT 
                    SUM(CASE WHEN MONTH(date) = ? AND YEAR(date) = ? THEN grand_total ELSE 0 END) as current_bnpl,
                    SUM(CASE WHEN MONTH(date) = ? AND YEAR(date) = ? THEN grand_total ELSE 0 END) as last_bnpl
                FROM invoice WHERE payment_method = 'BNPL'
            ");
            $bnplQuery->bind_param("iiii", $currMonth, $currYear, $lastMonth, $lastYear);
            $bnplQuery->execute();
            $bnpl = $bnplQuery->get_result()->fetch_assoc();
            $bnplAmount = $bnpl['current_bnpl'] ?: 0;
            $bnplLast = $bnpl['last_bnpl'] ?: 0;
            $bnplChange = percentageChange($bnplAmount, $bnplLast);
            $bnplTrend = $bnplChange >= 0 ? 'success' : 'danger';

            // Get Active Suppliers
            $suppliers = $conn->query("SELECT COUNT(*) as count FROM suppliers")->fetch_assoc();
            $supplierCount = $suppliers['count'] ?: 0;

            ?>

            <!-- HTML Dashboard Cards -->
            <div class="row">
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card shadow-sm" style="border-left: 5px solid #0d6efd;">
                        <div class="card-body">
                            <h6 class="text-muted">Total Sales</h6>
                            <h3 class="fw-bold">₹<?= number_format($salesAmount, 2) ?></h3>
                            <p class="text-<?= $salesTrend ?>">
                                <?= ($salesChange >= 0 ? '+' : '') . $salesChange ?>% vs last month
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card shadow-sm" style="border-left: 5px solid #198754;">
                        <div class="card-body">
                            <h6 class="text-muted">Inventory Value</h6>
                            <h3 class="fw-bold">₹<?= number_format($invAmount, 2) ?></h3>
                            <p class="text-<?= $invTrend ?>">
                                <?= ($invChange >= 0 ? '+' : '') . $invChange ?>% vs last month
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card shadow-sm" style="border-left: 5px solid #ffc107;">
                        <div class="card-body">
                            <h6 class="text-muted">BNPL Outstanding</h6>
                            <h3 class="fw-bold">₹<?= number_format($bnplAmount, 2) ?></h3>
                            <p class="text-<?= $bnplTrend ?>">
                                <?= ($bnplChange >= 0 ? '+' : '') . $bnplChange ?>% vs last month
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card shadow-sm h-100" style="border-left: 5px solid #6f42c1;">
                        <div class="card-body">
                            <h6 class="text-muted">Active Suppliers</h6>
                            <h3 class="fw-bold"><?= $supplierCount ?></h3>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Charts -->
            <div class="chart-container">
                <div class="chart-box">
                    <h3>Monthly Revenue Trend</h3>
                    <canvas id="lineChart"></canvas>
                </div>
                <div class="chart-box">
                    <h3>Sales by Payment Method</h3>
                    <div style="position: relative; width: 100%; max-width: 300px; margin: 0 auto;">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Create Invoice form -->
            <div id="invoiceModal" class="modal">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content p-3">
                        <div class="modal-header">
                            <button type="button" class="btn-close" onclick="closeInvoiceModal()"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Customer:</label>
                                    <select class="form-select" id="customer" name="customer" required>
                                        <option>Select customer</option>
                                        <?php
                                        // Fetch transactions from the database
                                        $result = $conn->query("SELECT name FROM customer ORDER BY customer_Id DESC");

                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<option>" . $row['name'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <button class="btn bg-primary text-white mt-2" id="showFormBtn">+ Add Customer</button>
                                </div>

                                <!-- Hidden Form -->
                                <div id="hiddenFrom" class="card p-3 mb-4" style="display: none;">
                                    <form method="POST" action="save_customer.php">
                                        <div class="mb-3">
                                            <label class="form-label">Create for:</label>
                                            <select class="form-select" id="created_for" name="created_for" required>
                                                <option>Select status</option>
                                                <?php

                                                // Fetch transactions from the database
                                                $result = $conn->query("SELECT user_name FROM users");

                                                if ($result->num_rows > 0) {
                                                    while ($row = $result->fetch_assoc()) {
                                                        echo "<option>" . $row['user_name'] . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Customer Name</label>
                                            <input type="text" name="customer_name" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="type" class="form-label">Type</label>
                                            <select class="form-select" id="type" name="type" required>
                                                <option value="Retail">Retail</option>
                                                <option value="Wholesale">Wholesale</option>
                                                <option value="Contractor">Contractor</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Phone</label>
                                            <input type="text" name="customer_phone" class="form-control" required maxlength="10">
                                        </div>
                                        <input type="submit" value="Save Customer" class="btn btn-success text-white" name="whatAction">
                                    </form>
                                </div>

                        <!-- JS toggle to show hideen form -->

                        <script>
                            document.getElementById("showFormBtn").addEventListener("click",function(){
                                const showForm = document.getElementById("hiddenFrom");
                                showForm.style.display = (showForm.style.display ==="none") ? "block" : "none";
                            })
                        </script>






                                <div class="col-md-6">
                                    <label class="form-label">Payment Method:</label>
                                    <select class="form-select" id="invoicePaymentMethod" name="invoicePaymentMethod"
                                        required>
                                        <option>Select payment method</option>
                                        <option>Digital payment</option>
                                        <option>Cash</option>
                                        <option>BNPL</option>
                                        <option>Payment gateway</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status:</label>
                                    <select class="form-select" id="invoiceStatus" name="invoiceStatus" required>
                                        <option>Select status</option>
                                        <option>Completed</option>
                                        <option>Pending</option>
                                        <option>Refund</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Create for:</label>
                                    <select class="form-select" id="created_for" name="created_for" required>
                                        <option>Select status</option>
                                        <?php

                                        // Fetch transactions from the database
                                        $result = $conn->query("SELECT user_name FROM users");

                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<option>" . $row['user_name'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label d-block">Document Type:</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="docType" value="withGST" checked
                                        onchange="toggleGST()">
                                    <label class="form-check-label">With GST</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="docType" value="withoutGST"
                                        onchange="toggleGST()">
                                    <label class="form-check-label">Without GST</label>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Date:</label>
                                    <input type="date" id="invoiceDate" name="invoiceDate" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Due Date:</label>
                                    <input type="date" id="dueDate" name="dueDate" class="form-control">
                                </div>
                                <div class="col-md-4 gst-section">
                                    <label class="form-label">Tax Rate:</label>
                                    <select id="taxRate" class="form-select" name="taxRate" onchange="updateTotals()">
                                        <option value="5">GST 5%</option>
                                        <option value="12">GST 12%</option>
                                        <option value="18">GST 18%</option>
                                        <option value="28">GST 28%</option>
                                    </select>
                                </div>
                            </div>

                            <div class="table-responsive mb-3">
                                <table class="table table-bordered" id="itemTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Item</th>
                                            <th>Description</th>
                                            <th>Qty</th>
                                            <th>Price (₹)</th>
                                            <th>Total (₹)</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                <button class="btn btn-sm btn-outline-primary" onclick="addItem()">+ Add Item</button>
                                <button class="btn btn-sm btn-outline-primary" onclick="redirect()">+ Add Product</button>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes:</label>
                                <textarea class="form-control" id="textarea" name="textarea"
                                    placeholder="Additional notes, payment terms..." rows="3"></textarea>
                            </div>

                            <div class="text-end">
                                <p>Subtotal: ₹<span id="subtotal">0.00</span></p>
                                <p class="gst-section">GST (<span id="gstPercent">18</span>%): ₹<span
                                        id="gstAmount">0.00</span></p>
                                <h5>Total: ₹<span id="totalAmount">0.00</span></h5>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-secondary" onclick="closeInvoiceModal()">Cancel</button>
                            <button class="btn btn-primary" onclick="collectInvoiceData()">Create Invoice</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Billing table -->
            <div class="col-md-12  card p-3 shadow-sm my-4 table-responsive">
                <h4><i class="bi bi-receipt-cutoff text-primary"></i> Billing & Invoice Management</h4>
                <p>Create, track, and manage invoices and payments</p>

                <div class="tabs">
                    <button class="billingTab active" onclick="showBillingTab('invoices')">Invoices</button>
                    <button class="billingTab" onclick="showBillingTab('payments')">Payments</button>
                    <button class="billingTab" onclick="showBillingTab('reports')">Reports</button>
                </div>

                <!-- Invoice table -->
                <div id="invoices" class="billing-tab-content active">
                    <div class="container-fluid d-flex justify-content-between align-items-center">

                        <div class="d-flex justify-content-start">
                            <div class="input-group w-100">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control border-start-0 table-search"
                                    data-table="invoice_table" placeholder="Search..." />
                            </div>
                        </div>

                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-outline-primary all" data-table="invoice_table">All</button>
                            <button class="btn btn-outline-primary status_filter" data-type="Completed"
                                data-table="invoice_table"><i class="fa-regular fa-circle-check text-success"></i>
                                Completed</button>
                            <button class="btn btn-outline-primary status_filter" data-type="Pending"
                                data-table="invoice_table"><i class="fa-regular fa-clock text-warning"></i>
                                Pending</button>
                            <button class="btn btn-outline-primary status_filter" data-type="Refund"
                                data-table="invoice_table"><i class="fa-solid fa-circle-exclamation text-danger"></i>
                                Refund</button>
                        </div>

                        <div class="justify-contnt-end">
                            <button class="btn btn-outline-primary" onclick="openInvoiceModal(event)" id="invoice"><i
                                    class="fa-solid fa-plus"></i> Create Invoice</button>
                        </div>
                    </div>
                    <table id="invoice_table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Invoice Of</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Due Date</th>
                                <th>Document Type</th>
                                <th>Tax Rate</th>
                                <th>Items</th>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>Notes</th>
                                <th>GST Amount</th>
                                <th>Grand Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            // Fetch transactions from the database
                            $result = $conn->query("SELECT * FROM invoice ORDER BY Sales_Id DESC");

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['invoice_id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['created_for']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                                    echo "<td>" . date('d-M-Y', strtotime($row['date'])) . "</td>";
                                    echo "<td>" . date('d-M-Y', strtotime($row['due_date'])) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['document_type']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['tax_rate']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['notes']) . "</td>";
                                    echo "<td>₹" . number_format($row['GST_amount'], 2) . "</td>";
                                    echo "<td>₹" . number_format($row['grand_total'], 2) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                    echo '<td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-eye"></i></button>
                                    <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-print"></i></button>
                                    <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i></button>
                                </div>
                            </td>';
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='14' class='text-center'>No transactions found</td></tr>";
                            }
                            ?>
                        </tbody>
                        <div id="pagination" class="mt-3 d-flex justify-content-center gap-2"></div>
                    </table>
                </div>

                <!-- Payments table -->
                <div id="payments" class="billing-tab-content">
                    <div class="container-fluid d-flex justify-content-between align-items-center">

                        <div class="d-flex justify-content-start">
                            <div class="input-group w-100">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control border-start-0 table-search"
                                    data-table="payment_table" placeholder="Search..." />
                            </div>
                        </div>

                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-outline-primary all" data-table="payment_table">All</button>
                            <button class="btn btn-outline-primary payment_filter" data-type="Digital payment"
                                data-table="payment_table">Digital payment</button>
                            <button class="btn btn-outline-primary payment_filter" data-type="Cash"
                                data-table="payment_table">Cash</button>
                            <button class="btn btn-outline-primary payment_filter" data-type="BNPL"
                                data-table="payment_table">BNPL</button>
                            <button class="btn btn-outline-primary payment_filter" data-type="Payment gateway"
                                data-table="payment_table">Payment gateway</button>
                        </div>

                    </div>
                    <table id="payment_table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Payment ID</th>
                                <th>Invoice ID</th>
                                <th>Invoice Of</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Actions</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            // Fetch transactions from the database
                            $result = $conn->query("SELECT * FROM invoice ORDER BY invoice_id DESC");

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $status = htmlspecialchars($row['status']);
                                    $id = $row['invoice_id'];

                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['payment_id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['invoice_id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['created_for']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                                    echo "<td>" . date('d-M-Y', strtotime($row['date'])) . "</td>";
                                    echo "<td>₹" . number_format($row['grand_total'], 2) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['payment_method']) . "</td>";
                                    echo "<td>" . $status . "</td>";

                                    echo "<td>";
                                    if ($status === 'Pending') {
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
                                    if ($status === 'Pending') {
                            ?>
                                        <div class="modal fade" id="statusModal<?= $id ?>" tabindex="-1"
                                            aria-labelledby="statusModalLabel<?= $id ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form method="POST" action="admin_dashboard.php">
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
                                                            <button type="submit" class="btn btn-primary" name="whatAction"
                                                                value="update">Update</button>
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
                                echo "<tr><td colspan='8' class='text-center'>No transactions found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Reports -->
                <div id="reports" class="billing-tab-content">
                    <h4>Outstanding Payments</h4>
                    <table id="Table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Invoice Of</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Due Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            // Fetch transactions from the database
                            $result = $conn->query("SELECT * FROM invoice ORDER BY invoice_id DESC");

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['invoice_id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['created_for']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                                    echo "<td>₹" . number_format($row['grand_total'], 2) . "</td>";
                                    echo "<td>" . date('d-M-Y', strtotime($row['date'])) . "</td>";
                                    echo "<td>" . date('d-M-Y', strtotime($row['due_date'])) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>No transactions found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php
            // Fetch products for Products Table
            $products_query = "SELECT id, name, category, stock_quantity, mrp, gst_rate, selling_price FROM products ORDER BY name LIMIT 10";
            $products_result = $conn->query($products_query);
            ?>

            <!-- Inventory management -->
            <div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">
                <h4><i class="fa-solid fa-box text-primary"></i> Inventory Management</h4>
                <p>Monitor your product inventory</p>

                <!-- Product  -->
                <div id="products" class="inventory-tab-content active">
                    <div class="container-fluid d-flex justify-content-between align-items-center">


                        <div class="d-flex justify-content-start">
                            <div class="input-group w-100 me-2">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control border-start-0 table-search"
                                    data-table="productsTable" placeholder="Search..." />
                            </div>
                        </div>


                        <div class="justify-contnt-end">
                            <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#addProductModal"><i class="fa-solid fa-plus"></i> Add Product</button>
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
                                                <button class="btn btn-outline-primary btn-sm edit-product-btn"
                                                    data-bs-toggle="modal" data-bs-target="#editProductModal"
                                                    data-id="<?php echo htmlspecialchars($product['id']); ?>"
                                                    data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                                    data-category="<?php echo htmlspecialchars($product['category']); ?>"
                                                    data-mrp="<?php echo htmlspecialchars($product['mrp']); ?>"
                                                    data-gst-rate="<?php echo htmlspecialchars($product['gst_rate']); ?>"
                                                    data-selling-price="<?php echo htmlspecialchars($product['selling_price']); ?>"
                                                    data-stock="<?php echo htmlspecialchars($product['stock_quantity']); ?>">
                                                    <i class="fa-solid fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-sm delete-product-btn"
                                                    data-bs-toggle="modal" data-bs-target="#deleteProductModal"
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

            <!-- Add Product Modal -->
            <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form method="post" action="admin_dashboard.php">
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
                                    <label for="category" class="form-label">Category</label>
                                    <input type="text" class="form-control" id="category" name="category" required>
                                </div>
                                <div class="mb-3">
                                    <label for="mrp" class="form-label">MRP (₹)</label>
                                    <input type="number" step="0.01" class="form-control" id="mrp" name="mrp" required>
                                </div>
                                <div class="mb-3">
                                    <label for="gst_rate" class="form-label">GST Rate (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="gst_rate" name="gst_rate"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="selling_price" class="form-label">Selling Price (₹)</label>
                                    <input type="number" step="0.01" class="form-control" id="selling_price"
                                        name="selling_price" required>
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
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add Product</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Product Modal -->
            <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form method="post" action="admin_dashboard.php">
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
                                    <input type="number" step="0.01" class="form-control" id="editGstRate" name="gst_rate"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="editSellingPrice" class="form-label">Selling Price (₹)</label>
                                    <input type="number" step="0.01" class="form-control" id="editSellingPrice"
                                        name="selling_price" required>
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
            <div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form method="post" action="admin_dashboard.php">
                            <input type="hidden" name="whatAction" value="DeleteProduct">
                            <input type="hidden" name="id" id="deleteProductId">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteProductLabel">Delete Product</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete this product? This action will not affect existing
                                    inventory transactions.</p>
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

            <!-- Factory management -->
            <div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">
                <h4><i class="fa-solid fa-industry text-primary"></i> Factory Production Management</h4>
                <p>Manage production orders, materials, and quality control</p>

                <div class="tabs">
                    <button class="factoryTab active" onclick="showFactoryTab('production')">Production</button>
                    <button class="factoryTab" onclick="showFactoryTab('material')">Raw Materials</button>
                    <button class="factoryTab" onclick="showFactoryTab('quality')">Quality Control</button>
                </div>

                <!-- Production -->
                <div id="production" class="factory-tab-content active">
                    <div class="container-fluid d-flex justify-content-between align-items-center">


                        <div class="d-flex gap-2 justify-content-start">
                            <button class="btn btn-outline-primary"><i class="fa-regular fa-clock text-warning"></i>
                                Pending</button>
                            <button class="btn btn-outline-primary"><i class="fa-regular fa-circle-play"></i> In
                                Progress</button>
                            <button class="btn btn-outline-primary"><i class="fa-regular fa-circle-check text-success"></i>
                                Complete</button>
                        </div>


                        <div class="justify-contnt-end">
                            <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#productionOrder"><i class="fa-solid fa-plus"></i> New Production
                                Order</button>
                        </div>
                    </div>
                    <table id="Table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th>Progress</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>PROD-0001</td>
                                <td>Copper Wire (2.5mm)</td>
                                <td>5,000 meters</td>
                                <td>In Progress</td>
                                <td>65%</td>
                                <td>08/04/2025</td>
                                <td>14/04/2025</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-pause"></i>
                                            Pause</button>
                                        <button class="btn btn-outline-primary btn-sm"><i
                                                class="fa-solid fa-pen-to-square"></i></button>
                                        <button class="btn btn-outline-primary btn-sm"><i
                                                class="fa-solid fa-print"></i></button>

                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Raw material -->
                <div id="material" class="factory-tab-content">
                    <div class="container-fluid d-flex justify-content-between align-items-center">

                        <div class="d-flex justify-content-start gap-2">
                            <button class="btn btn-outline-primary"><i
                                    class="fa-solid fa-triangle-exclamation text-warning"></i> Low Stock</button>
                            <button class="btn btn-outline-primary">All Materials</button>
                        </div>

                        <div class="d-flex gap-2 justify-contnt-end">
                            <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#orderMaterial"><i class="fa-solid fa-box"></i> Oreder Material</button>
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addMaterial"><i
                                    class="fa-solid fa-plus"></i> Add Material</button>
                        </div>
                    </div>
                    <table id="Table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Material</th>
                                <th>Current Stock</th>
                                <th>Unit</th>
                                <th>Reorder Level</th>
                                <th>Location</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>RM-0001</td>
                                <td>Copper (99.9%)</td>
                                <td>2,500</td>
                                <td>kg</td>
                                <td>500</td>
                                <td>Storage A</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-primary btn-sm">Update Stock</button>
                                        <button class="btn btn-outline-primary btn-sm"><i
                                                class="fa-solid fa-pen-to-square"></i></button>

                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Quality -->
                <div id="quality" class="factory-tab-content">
                    <div class="container-fluid d-flex justify-content-between align-items-center">

                        <div class="d-flex justify-content-start gap-2">
                            <button class="btn btn-outline-primary"><i class="fa-regular fa-circle-check text-success"></i>
                                Passed</button>
                            <button class="btn btn-outline-primary"><i
                                    class="fa-solid fa-triangle-exclamation text-danger"></i> Failed</button>
                            <button class="btn btn-outline-primary">All Tests</button>
                        </div>

                        <div class="justify-contnt-end">
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#qualityTest"><i
                                    class="fa-regular fa-clipboard"></i> New Quality
                                Test</button>
                        </div>
                    </div>
                    <table id="Table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Test ID</th>
                                <th>Product</th>
                                <th>Batch Number</th>
                                <th>Status</th>
                                <th>Tested By</th>
                                <th>Date</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>QC-0001</td>
                                <td>Copper Wire (2.5mm)</td>
                                <td>B20250408A</td>
                                <td>Passed</td>
                                <td>Rajiv Kumar</td>
                                <td>09/04/2025</td>
                                <td><a href="#" style="text-decoration: none;">View Details</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Production Order Form -->
            <div class="modal fade" id="productionOrder" tabindex="-1" aria-labelledby="productionOrderLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form>
                            <div class="modal-header">
                                <h5 class="modal-title" id="productionOrderLabel">Production Order</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="orderId" class="form-label">Order ID</label>
                                    <input type="text" class="form-control" id="orderId">
                                </div>

                                <div class="mb-3">
                                    <label for="product" class="form-label">Product</label>
                                    <input type="text" class="form-control" id="product">
                                </div>

                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <input type="text" class="form-control" id="quantity">
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <input type="text" class="form-control" id="status">
                                </div>

                                <div class="mb-3">
                                    <label for="progress" class="form-label">Progress</label>
                                    <input type="text" class="form-control" id="progress">
                                </div>

                                <div class="mb-3">
                                    <label for="startDate" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="startDate">
                                </div>

                                <div class="mb-3">
                                    <label for="endDate" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="endDate">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Production</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Order Material Form -->
            <div class="modal fade" id="orderMaterial" tabindex="-1" aria-labelledby="orderMaterialLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form>
                            <div class="modal-header">
                                <h5 class="modal-title" id="orderMaterialLabel">Order Material</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="material" class="form-label">Material</label>
                                    <input type="text" class="form-control" id="material">
                                </div>

                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <input type="number" class="form-control" id="quantity">
                                </div>

                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="location">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Order Material</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Add Material Form -->
            <div class="modal fade" id="addMaterial" tabindex="-1" aria-labelledby="addMaterialLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form>
                            <div class="modal-header">
                                <h5 class="modal-title" id="addMaterialLabel">Add Material</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="Id" class="form-label">ID</label>
                                    <input type="text" class="form-control" id="Id">
                                </div>

                                <div class="mb-3">
                                    <label for="material" class="form-label">Material</label>
                                    <input type="text" class="form-control" id="material">
                                </div>

                                <div class="mb-3">
                                    <label for="currentStock" class="form-label">Current Stock</label>
                                    <input type="number" class="form-control" id="currentStock">
                                </div>

                                <div class="mb-3">
                                    <label for="unit" class="form-label">Unit</label>
                                    <input type="text" class="form-control" id="unit">
                                </div>

                                <div class="mb-3">
                                    <label for="orderLevel" class="form-label">Reorder Level</label>
                                    <input type="number" class="form-control" id="orderLevel">
                                </div>

                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="location">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add Material</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Quality Test Form -->
            <div class="modal fade" id="qualityTest" tabindex="-1" aria-labelledby="qualityTestLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form>
                            <div class="modal-header">
                                <h5 class="modal-title" id="qualityTestLabel">Quality Test</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="testId" class="form-label">Test ID</label>
                                    <input type="text" class="form-control" id="testId">
                                </div>

                                <div class="mb-3">
                                    <label for="product" class="form-label">Product</label>
                                    <input type="text" class="form-control" id="product">
                                </div>

                                <div class="mb-3">
                                    <label for="batchNumber" class="form-label">Batch Number</label>
                                    <input type="text" class="form-control" id="batchNumber">
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <input type="text" class="form-control" id="status">
                                </div>

                                <div class="mb-3">
                                    <label for="testedBy" class="form-label">Tested By</label>
                                    <input type="text" class="form-control" id="testedBy">
                                </div>

                                <div class="mb-3">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="date">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Report</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Retail stor management -->
            <div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">
                <h4><i class="fa-solid fa-store text-primary"></i> Retail Store Management</h4>
                <p>Manage sales, inventory, and customers in your retail store</p>

                <div class="tabs">
                    <button class="retailTab active" onclick="showRetailTab('sales')">Sales</button>
                    <button class="retailTab" onclick="showRetailTab('inventory')">Inventory</button>
                    <button class="retailTab" onclick="showRetailTab('customers')">Customers</button>
                </div>

                <!-- Sales -->
                <div id="sales" class="retail-tab-content active">
                    <div class="container-fluid d-flex justify-content-between align-items-center">


                        <div class="d-flex justify-content-start">
                            <div class="input-group w-100 me-2">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control border-start-0 table-search" data-table="sales_table"
                                    placeholder="Search..." />
                            </div>
                        </div>


                        <div class="justify-contnt-end">
                            <button class="btn btn-outline-primary" onclick="openInvoiceModal(event)" id="invoice"><i
                                    class="fa-solid fa-cart-plus"></i> New Sales</button>
                        </div>
                    </div>
                    <table id="sales_table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Sales ID</th>
                                <th>Invoice Of</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            // Fetch transactions from the database
                            $result = $conn->query("SELECT * FROM invoice ORDER BY Sales_Id DESC");

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['Sales_Id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['created_for']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                                    echo "<td>" . date('d-M-Y', strtotime($row['date'])) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                                    echo "<td>₹" . number_format($row['grand_total'], 2) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['payment_method']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                    echo '<td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-eye"></i></button>
                                    <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-print"></i></button>
                                    <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i></button>
                                </div>
                            </td>';
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9' class='text-center'>No transactions found</td></tr>";
                            }
                            ?>
                        </tbody>
                        <div id="pagination" class="mt-3 d-flex justify-content-center gap-2"></div>
                    </table>
                </div>

                <!-- Inventory -->
                <div id="inventory" class="retail-tab-content">
                    <div class="container-fluid d-flex justify-content-between align-items-center">

                        <div class="d-flex justify-content-start">
                            <div class="input-group w-100 me-2">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control border-start-0 table-search"
                                    data-table="invetory_table" placeholder="Search..." />
                            </div>
                        </div>


                        <div class="d-flex gap-2 justify-contnt-end">
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#requestStock"><i
                                    class="fa-solid fa-box"></i> Request Stock</button>
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addItem"><i
                                    class="fa-solid fa-cart-plus"></i> Add
                                Product</button>
                        </div>
                    </div>
                    <table id="invetory_table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Invetory Of</th>
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
                            $result = $conn->query("SELECT * FROM retail_invetory ORDER BY Id DESC");

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['Id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['inventory_of']) . "</td>";
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
                                                        data-inventory_of="' . htmlspecialchars($row['inventory_of']) . '" 
                                                        data-price="' . $row['price'] . '">
                                                    <i class="fa-regular fa-pen-to-square"></i>
                                                </button>

                                                <form method="POST" action="admin_dashboard.php" style="display:inline;">
                                                    <input type="hidden" name="whatAction" value="deleteItem">
                                                    <input type="hidden" name="inventory_of" value="' . htmlspecialchars($row['inventory_of']) . '">
                                                    <input type="hidden" name="itemId" value="' . $row['Id'] . '">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this item?\')">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>';
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9' class='text-center'>No transactions found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Customer -->
                <div id="customers" class="retail-tab-content">
                    <div class="container-fluid d-flex justify-content-between align-items-center">

                        <div class="d-flex justify-content-start">
                            <div class="input-group w-100 me-2">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control border-start-0 table-search"
                                    data-table="customer_table" placeholder="Search..." />
                            </div>
                            <button class="btn btn-outline-primary customer-filter me-2" data-type="Retail"
                                data-table="customer_table">Retail</button>
                            <button class="btn btn-outline-primary customer-filter me-2" data-type="Wholesale"
                                data-table="customer_table">Wholesale</button>
                            <button class="btn btn-outline-primary customer-filter me-2" data-type="Contractor"
                                data-table="customer_table">Contractor</button>
                            <button class="btn btn-outline-danger all me-2" data-table="customer_table">Remove
                                Filters</button>
                        </div>


                        <div class="justify-contnt-end">
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addCustomer"><i
                                    class="fa-regular fa-user"></i> Add Customer</button>
                        </div>
                    </div>
                    <table id="customer_table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer Of</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Contact</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            // Fetch transactions from the database
                            $result = $conn->query("SELECT * FROM customer ORDER BY customer_Id DESC");

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['customer_Id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['created_for']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['contact']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center'>No transactions found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add New Item Form -->
            <div class="modal fade" id="addItem" tabindex="-1" aria-labelledby="addItemLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="admin_dashboard.php" method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addItemLabel">Add Item</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">

                                <div class="mb-3">
                                    <label class="form-label">Create for:</label>
                                    <select class="form-select" id="created_for" name="created_for" required>
                                        <option>Select status</option>
                                        <?php

                                        // Fetch transactions from the database
                                        $result = $conn->query("SELECT user_name FROM users");

                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<option>" . $row['user_name'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

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
                                    <input type="number" class="form-control" id="reorderPoint" name="reorderPoint"
                                        required>
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
                        <form action="admin_dashboard.php" method="POST">
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
                                    <label class="form-label">Requested by:</label>
                                    <select class="form-select" id="created_for" name="created_for" required>
                                        <option>Select status</option>
                                        <?php

                                        // Fetch transactions from the database
                                        $result = $conn->query("SELECT user_name FROM users");

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
                    <form method="POST" action="admin_dashboard.php" class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editPriceLabel">Edit Item Price</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="whatAction" value="editPrice">
                            <input type="hidden" name="itemId" id="editItemId">
                            <input type="hidden" name="inventory_Of" id="inventory_Of">
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

            <!-- Add Customer Form -->
            <div class="modal fade" id="addCustomer" tabindex="-1" aria-labelledby="addCustomerLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="admin_dashboard.php" method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addCustomerLabel">Add Customer</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">

                                <div class="mb-3">
                                    <label class="form-label">Create for:</label>
                                    <select class="form-select" id="created_for" name="created_for" required>
                                        <option>Select status</option>
                                        <?php

                                        // Fetch transactions from the database
                                        $result = $conn->query("SELECT user_name FROM users");

                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<option>" . $row['user_name'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>

                                <div class="mb-3">
                                    <label for="type" class="form-label">Type</label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="Retail">Retail</option>
                                        <option value="Wholesale">Wholesale</option>
                                        <option value="Contractor">Contractor</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="contact" class="form-label">Contact</label>
                                    <input type="tel" class="form-control" id="contact" name="contact" required>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary" name="whatAction"
                                        value="add_customer">Save
                                        Customer</button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>


        <?php elseif ($page === 'billing_desk'): ?>
            <?php include 'billing_desk.php'; ?>

        <?php elseif ($page === 'accounting'): ?>
            <?php include 'accounting.php'; ?>

        <?php elseif ($page === 'inventory'): ?>
            <?php include 'inventory.php'; ?>

        <?php elseif ($page === 'expenses_dashboard'): ?>
            <?php include 'expenses_dashboard.php'; ?>

        <?php elseif ($page === 'factory_stock'): ?>
            <?php include 'factory_stock.php'; ?>

        <?php elseif ($page === 'retail_store'): ?>
            <?php include 'retail_store.php'; ?>

        <?php elseif ($page === 'After_sales_service'): ?>
            <?php include 'After_sales_service.php'; ?>

        <?php elseif ($page === 'suppliers'): ?>
            <?php include 'suppliers.php'; ?>

        <?php elseif ($page === 'reports'): ?>
            <?php include 'reports.php'; ?>

        <?php elseif ($page === 'user_management'): ?>
            <?php include 'user_management.php'; ?>

        <?php elseif ($page === 'settings'): ?>
            <?php include 'settings.php'; ?>

        <?php endif; ?>

    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

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

            // status Filter Buttons
            document.querySelectorAll(".status_filter").forEach(button => {
                button.addEventListener("click", () => {
                    const type = button.dataset.type.toLowerCase();
                    const tableId = button.dataset.table;
                    const rows = document.querySelectorAll(`#${tableId} tbody tr`);
                    rows.forEach(row => {
                        const docType = row.children[12]?.innerText.trim().toLowerCase();
                        row.style.display = docType === type ? "" : "none";
                    });
                });
            });

            // payment Filter Buttons
            document.querySelectorAll(".payment_filter").forEach(button => {
                button.addEventListener("click", () => {
                    const type = button.dataset.type.toLowerCase();
                    const tableId = button.dataset.table;
                    const rows = document.querySelectorAll(`#${tableId} tbody tr`);
                    rows.forEach(row => {
                        const docType = row.children[6]?.innerText.trim().toLowerCase();
                        row.style.display = docType === type ? "" : "none";
                    });
                });
            });

            //  Customer Filter Buttons
            document.querySelectorAll(".customer-filter").forEach(button => {
                button.addEventListener("click", () => {
                    const type = button.dataset.type.toLowerCase();
                    const tableId = button.dataset.table;
                    const rows = document.querySelectorAll(`#${tableId} tbody tr`);
                    rows.forEach(row => {
                        const docType = row.children[2]?.innerText.trim().toLowerCase();
                        row.style.display = docType === type ? "" : "none";
                    });
                });
            });

            // ❌ Remove Filters Button
            document.querySelectorAll(".all").forEach(button => {
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

    <!-- Line chart -->
    <?php

    $monthLabels = [];
    $monthlyTotals = [];

    // Get today's date and loop back 6 months
    for ($i = 5; $i >= 0; $i--) {
        $date = new DateTime();
        $date->modify("-$i months");
        $month = $date->format('m');
        $year = $date->format('Y');
        $label = $date->format('M'); // e.g., Jan, Feb

        // Add to labels
        $monthLabels[] = "'$label'";

        // Fetch sum for this month
        $stmt = $conn->prepare("SELECT SUM(grand_total) as total FROM invoice WHERE MONTH(date) = ? AND YEAR(date) = ?");
        $stmt->bind_param("ii", $month, $year);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $total = $result['total'] ?: 0;
        $monthlyTotals[] = $total;
    }

    // Output comma-separated values
    $labelsStr = implode(", ", $monthLabels);
    $totalsStr = implode(", ", $monthlyTotals);
    ?>

    <!-- Pei chart -->
    <?php
    // Pie Chart: Get payment method counts
    $paymentLabels = [];
    $paymentCounts = [];

    $query = "SELECT payment_method, COUNT(*) AS total FROM invoice GROUP BY payment_method";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        $paymentLabels[] = $row['payment_method'];
        $paymentCounts[] = $row['total'];
    }
    ?>

    <script>
        // Line Chart
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: [<?= $labelsStr ?>],
                datasets: [{
                    label: 'Revenue',
                    data: [<?= $totalsStr ?>],
                    fill: false,
                    borderColor: '#0d6efd',
                    backgroundColor: '#0d6efd',
                    tension: 0.3,
                    pointRadius: 5,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 150000
                        }
                    }
                }
            }
        });

        // Pie Chart
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($paymentLabels); ?>,
                datasets: [{
                    data: <?php echo json_encode($paymentCounts); ?>,
                    backgroundColor: [
                        '#0d6efd',
                        '#20c997',
                        '#ffc107',
                        '#fd7e14'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#333',
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });

        // For billing section
        function showBillingTab(id) {
            const tabs = document.querySelectorAll('.billingTab');
            const contents = document.querySelectorAll('.billing-tab-content');

            tabs.forEach(tab => tab.classList.remove('active'));
            contents.forEach(content => content.classList.remove('active'));

            document.querySelector(`#${id}`).classList.add('active');
            document.querySelector(`[onclick="showBillingTab('${id}')"]`).classList.add('active');
        }

        // For inventory section
        function showInventoryTab(id) {
            const tabs = document.querySelectorAll('.inventoryTab');
            const contents = document.querySelectorAll('.inventory-tab-content');

            tabs.forEach(tab => tab.classList.remove('active'));
            contents.forEach(content => content.classList.remove('active'));

            document.querySelector(`#${id}`).classList.add('active');
            document.querySelector(`[onclick="showInventoryTab('${id}')"]`).classList.add('active');
        }

        // For factory section
        function showFactoryTab(id) {
            const tabs = document.querySelectorAll('.factoryTab');
            const contents = document.querySelectorAll('.factory-tab-content');

            tabs.forEach(tab => tab.classList.remove('active'));
            contents.forEach(content => content.classList.remove('active'));

            document.querySelector(`#${id}`).classList.add('active');
            document.querySelector(`[onclick="showFactoryTab('${id}')"]`).classList.add('active');
        }

        // For retail store section
        function showRetailTab(id) {
            const tabs = document.querySelectorAll('.retailTab');
            const contents = document.querySelectorAll('.retail-tab-content');

            tabs.forEach(tab => tab.classList.remove('active'));
            contents.forEach(content => content.classList.remove('active'));

            document.querySelector(`#${id}`).classList.add('active');
            document.querySelector(`[onclick="showRetailTab('${id}')"]`).classList.add('active');
        }

        let activeInvoiceButtonId = null;

        // To open form
        function openInvoiceModal(event) {
            activeInvoiceButtonId = event.target.id; // To store clicked button ID

            const modal = document.getElementById('invoiceModal');
            modal.style.display = 'block';
            modal.classList.add('show');

            if (document.querySelectorAll("#itemTable tbody tr").length === 0) {
                addItem();
            }
        }

        // To close form
        function closeInvoiceModal() {
            const modal = document.getElementById('invoiceModal');
            modal.style.display = 'none';
            modal.classList.remove('show');

            document.querySelector('#itemTable tbody').innerHTML = '';
            activeInvoiceButtonId = null;
            updateTotals();
        }

        // For add item row
        function addItem() {
            const tbody = document.querySelector("#itemTable tbody");
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>
                    <select onchange="updateTotals()">
                        <option value="">Select Product</option>
                        <?php

                        // Fetch transactions from the database
                        $result = $conn->query("SELECT Product_Name FROM inventory");

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option>" . $row['Product_Name'] . "</option>";
                            }
                        }
                        ?>
                    </select>
                </td>
                <td><input placeholder="Description"/></td>
                <td><input type="number" value="1" min="1" oninput="updateTotals()" /></td>
                <td><input type="number" value="0" step="0.01" oninput="updateTotals()" /></td>
                <td class="itemTotal">₹0.00</td>
                <td><button class="btn btn-sm btn-outline-danger" onclick="removeItem(this)">Delete</button></td>
            `;
            tbody.appendChild(tr);
            updateTotals();
        }

        // To remove item row
        function removeItem(btn) {
            btn.closest("tr").remove();
            updateTotals();
        }

        // For GST 
        function toggleGST() {
            const withGST = document.querySelector('input[name="docType"]:checked').value === 'withGST';
            document.querySelectorAll(".gst-section").forEach(el => {
                el.style.display = withGST ? 'block' : 'none';
            });
            updateTotals();
        }

        // For calculate total amount
        function updateTotals() {
            let subtotal = 0;
            document.querySelectorAll("#itemTable tbody tr").forEach(row => {
                const qty = parseFloat(row.children[2].querySelector('input').value || 0);
                const price = parseFloat(row.children[3].querySelector('input').value || 0);
                const total = qty * price;
                subtotal += total;
                row.children[4].innerText = "₹" + total.toFixed(2);
            });

            const taxRate = parseFloat(document.getElementById('taxRate')?.value || 0);
            const gstEnabled = document.querySelector('input[name="docType"]:checked').value === 'withGST';
            const gstAmount = gstEnabled ? (subtotal * taxRate / 100) : 0;

            document.getElementById('subtotal').innerText = subtotal.toFixed(2);
            document.getElementById('gstPercent').innerText = taxRate;
            document.getElementById('gstAmount').innerText = gstAmount.toFixed(2);
            document.getElementById('totalAmount').innerText = (subtotal + gstAmount).toFixed(2);
        }

        // Close form when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('invoiceModal');
            if (event.target === modal) {
                closeInvoiceModal();
            }
        };

        function collectInvoiceData() {
            let item_names = [],
                descriptions = [],
                quantities = [],
                prices = [],
                totals = [];

            document.querySelectorAll("#itemTable tbody tr").forEach(row => {
                item_names.push(row.children[0].querySelector("select").value);
                descriptions.push(row.children[1].querySelector("input").value);
                let qty = row.children[2].querySelector("input").value;
                let price = row.children[3].querySelector("input").value;
                quantities.push(qty);
                prices.push(price);
                totals.push((qty * price).toFixed(2));
            });

            const selectedRadio = document.querySelector('input[name="docType"]:checked');
            if (!selectedRadio) {
                alert("Please select document type (With GST / Without GST)");
                return;
            }
            const document_type = selectedRadio.value;
            const gstEnabled = document_type === 'withGST';

            const vedorActiveted = activeInvoiceButtonId === 'purchase_order_bill';

            const data = {
                table: activeInvoiceButtonId,
                customer_name: document.getElementById("customer").value,
                payment_method: document.getElementById("invoicePaymentMethod").value,
                status: document.getElementById("invoiceStatus").value,
                created_for: document.getElementById("created_for").value,
                document_type: gstEnabled ? "with GST" : "without GST",
                date: document.getElementById("invoiceDate").value,
                due_date: document.getElementById("dueDate").value,
                tax_rate: gstEnabled ? document.getElementById("taxRate").value : 0,
                notes: document.getElementById("textarea").value,
                subtotal: document.getElementById("subtotal").innerText,
                GST_amount: gstEnabled ? document.getElementById("gstAmount").innerText : 0,
                grand_total: document.getElementById("totalAmount").innerText,
                item_names: item_names.join(","),
                descriptions: descriptions.join(","),
                quantities: quantities.join(","),
                prices: prices.join(","),
                totals: totals.join(","),
                whatAction: "createInvoice",
            };

            fetch("billing_desk.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.text())
                .then(msg => {
                    // alert(msg);  
                    // console.log(msg);
                    activeInvoiceButtonId = null;
                    location.reload();
                })
                .catch(err => alert("Error submitting invoice."));
        }

        // Auto-Dismiss Alerts
        document.addEventListener('DOMContentLoaded', function() {
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
                button.addEventListener('click', function() {
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
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;

                    document.getElementById('deleteProductId').value = id;
                    document.getElementById('deleteProductIdDisplay').textContent = id;
                    document.getElementById('deleteProductName').textContent = name;
                });
            });

            // Show/Hide Status and Supplier Fields in Add Product Modal
            document.getElementById('add_inventory').addEventListener('change', function() {
                const statusField = document.getElementById('status_field');
                const supplierField = document.getElementById('supplier_field');
                statusField.style.display = this.checked ? 'block' : 'none';
                supplierField.style.display = this.checked ? 'block' : 'none';
            });

            // View All Buttons
            document.getElementById('viewAllProductsBtn').addEventListener('click', function() {
                document.getElementById('searchInput').value = '';
                let rows = document.querySelectorAll('#productsTable tbody tr');
                rows.forEach(row => row.style.display = '');
            });

            document.getElementById('viewAllInventoryBtn').addEventListener('click', function() {
                document.getElementById('searchInput').value = '';
                let rows = document.querySelectorAll('#inventoryTable tbody tr');
                rows.forEach(row => row.style.display = '');
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var editModal = document.getElementById('editPriceModal');
            editModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var itemId = button.getAttribute('data-id');
                var itemName = button.getAttribute('data-name');
                var inventory_Of = button.getAttribute('data-inventory_of');
                var itemPrice = button.getAttribute('data-price');

                document.getElementById('editItemId').value = itemId;
                document.getElementById('inventory_Of').value = inventory_Of;
                document.getElementById('editItemName').value = itemName;
                document.getElementById('newPrice').value = itemPrice;
            });
        });

        // to redirect

        function redirect() {
            window.location.href = "admin_dashboard.php?page=inventory"
        }
    </script>

</body>

</html>