<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../_conn.php';
$user_name = $_SESSION['user_name'];

// Get selected user (from URL ?user=email)
$selectedEmail = isset($_GET['user']) ? $_GET['user'] : 'all';
$filterByFactory = $selectedEmail !== 'all';

// Filter for most queries using created_for
$factoryFilter = $filterByFactory ? " AND created_for = '" . mysqli_real_escape_string($conn, $selectedEmail) . "'" : '';

// Filter for low stock using created_for
$lowStockFilter = $filterByFactory ? " AND fs.created_for = '" . mysqli_real_escape_string($conn, $selectedEmail) . "'" : '';
// Filter specifically for pending orders using request_to
$pendingOrderFilter = $filterByFactory ? " AND request_to = '" . mysqli_real_escape_string($conn, $selectedEmail) . "'" : '';

// Get pending orders (using request_to)
$pending_orders = $conn->query("SELECT COUNT(*) as count FROM retail_store_stock_request WHERE status='Ordered' $pendingOrderFilter")->fetch_assoc()['count'];

// Get current and previous month for percentage calculations
$currentMonth = date('Y-m');
$previousMonth = date('Y-m', strtotime('-1 month'));

// Get total stock value (current month)
$totalValueSql = "SELECT SUM(value) AS total_value FROM factory_stock WHERE 1=1 $factoryFilter";
$totalValueResult = $conn->query($totalValueSql);
$totalValue = 0;
if ($totalValueResult->num_rows > 0) {
    $row = $totalValueResult->fetch_assoc();
    $totalValue = $row['total_value'] ?? 0;
}

// Get total stock value (previous month) for percentage change
$prevTotalValueSql = "SELECT SUM(value) AS total_value 
    FROM factory_stock 
    WHERE DATE_FORMAT(record_date, '%Y-%m') = '$previousMonth' $factoryFilter";
$prevTotalValueResult = $conn->query($prevTotalValueSql);
$prevTotalValue = 0;
if ($prevTotalValueResult->num_rows > 0) {
    $row = $prevTotalValueResult->fetch_assoc();
    $prevTotalValue = $row['total_value'] ?? 0;
}
$totalValuePercent = ($prevTotalValue > 0) ? (($totalValue - $prevTotalValue) / $prevTotalValue * 100) : 0;

// Get stock value by category
$categoryValueSql = "SELECT category, SUM(value) AS category_value 
    FROM factory_stock 
    WHERE 1=1 $factoryFilter 
    GROUP BY category";
$categoryValueResult = $conn->query($categoryValueSql);
$categoryValues = [];
if ($categoryValueResult->num_rows > 0) {
    while ($row = $categoryValueResult->fetch_assoc()) {
        $categoryValues[$row['category']] = $row['category_value'];
    }
}

// Get low stock items (current month)
$lowStockSql = "SELECT COUNT(*) AS low_stock_count 
    FROM factory_stock 
    WHERE status = 'Low Stock' $factoryFilter";
$lowStockResult = $conn->query($lowStockSql);
$lowStockCount = 0;
if ($lowStockResult->num_rows > 0) {
    $row = $lowStockResult->fetch_assoc();
    $lowStockCount = $row['low_stock_count'];
}

// Get low stock items (previous month) for percentage change
$prevLowStockSql = "SELECT COUNT(*) AS low_stock_count 
    FROM factory_stock 
    WHERE status = 'Low Stock' AND DATE_FORMAT(record_date, '%Y-%m') = '$previousMonth' $factoryFilter";
$prevLowStockResult = $conn->query($prevLowStockSql);
$prevLowStockCount = 0;
if ($prevLowStockResult->num_rows > 0) {
    $row = $prevLowStockResult->fetch_assoc();
    $prevLowStockCount = $row['low_stock_count'];
}
$lowStockPercent = ($prevLowStockCount > 0) ? (($lowStockCount - $prevLowStockCount) / $prevLowStockCount * 100) : 0;

// Get monthly production (current month)
$monthlyProductionSql = "SELECT SUM(quantity) AS total_quantity 
    FROM factory_stock 
    WHERE DATE_FORMAT(record_date, '%Y-%m') = '$currentMonth' $factoryFilter";
$monthlyProductionResult = $conn->query($monthlyProductionSql);
$monthlyProduction = 0;
if ($monthlyProductionResult->num_rows > 0) {
    $row = $monthlyProductionResult->fetch_assoc();
    $monthlyProduction = $row['total_quantity'] ?? 0;
}

// Get monthly production (previous month) for percentage change
$prevMonthlyProductionSql = "SELECT SUM(quantity) AS total_quantity 
    FROM factory_stock 
    WHERE DATE_FORMAT(record_date, '%Y-%m') = '$previousMonth' $factoryFilter";
$prevMonthlyProductionResult = $conn->query($prevMonthlyProductionSql);
$prevMonthlyProduction = 0;
if ($prevMonthlyProductionResult->num_rows > 0) {
    $row = $prevMonthlyProductionResult->fetch_assoc();
    $prevMonthlyProduction = $row['total_quantity'] ?? 0;
}
$monthlyProductionPercent = ($prevMonthlyProduction > 0) ? (($monthlyProduction - $prevMonthlyProduction) / $prevMonthlyProduction * 100) : 0;

// Get stock value trend (last 6 months)
$trendSql = "SELECT DATE_FORMAT(record_date, '%Y-%m') AS month_year, SUM(value) AS total_value
    FROM factory_stock
    WHERE record_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) $factoryFilter
    GROUP BY DATE_FORMAT(record_date, '%Y-%m')
    ORDER BY DATE_FORMAT(record_date, '%Y-%m') ASC";
$trendResult = $conn->query($trendSql);
$trendLabels = [];
$trendData = [];
$months = [];
while ($row = $trendResult->fetch_assoc()) {
    $monthYear = $row['month_year'];
    $months[$monthYear] = $row['total_value'];
}

// Generate labels and data for the last 6 months
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $monthName = date('M', strtotime("-$i months"));
    $trendLabels[] = $monthName;
    $trendData[] = isset($months[$month]) ? $months[$month] : 0;
}

// Static list of transfer locations
$transferLocations = ['Warehouse A', 'Warehouse B', 'Factory', 'Distribution Center'];

// Fetch Factory Users
$sql = "SELECT email, user_name FROM users WHERE user_type = 'Factory'";
$result = mysqli_query($conn, $sql);
$factoryUsers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $factoryUsers[] = $row;
}

// Rebuild current query string without 'user'
parse_str($_SERVER['QUERY_STRING'], $query);
unset($query['user']);
$baseUrl = $_SERVER['PHP_SELF'] . '?' . http_build_query($query);

// CSV downloadable report
if (isset($_GET['export']) && $_GET['export'] === '1') {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];

    if (empty($start_date) || empty($end_date)) {
        die('Please select both start and end dates.');
    }

    if (!preg_match("/\d{4}-\d{2}-\d{2}/", $start_date) || !preg_match("/\d{4}-\d{2}-\d{2}/", $end_date)) {
        die('Invalid date format. Please use YYYY-MM-DD.');
    }

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="stock_report.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Stock ID', 'Item Name', 'Category', 'Quantity', 'Value', 'Status', 'Factory']);

    $query = "SELECT * FROM factory_stock WHERE record_date BETWEEN ? AND ? $factoryFilter";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['stock_id'],
            $row['item_name'],
            $row['category'],
            $row['quantity'],
            $row['value'],
            $row['status'],
            $row['created_for']
        ]);
    }
    fclose($output);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    $whatAction = $data['whatAction'] ?? '';
    // echo "<script>console.log('What action: ". $whatAction ."') </script>";

    if ($whatAction === 'add_product') {

        $createdFor = $conn->real_escape_string($data['createdFor'] ?? $user_name); // Use provided createdFor or default to user_name

        // Fetch latest product ID current or previous year
        $result = $conn->query("SELECT id FROM factory_product WHERE created_for = '$createdFor' ORDER BY CAST(SUBSTRING(id, 5) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");

        if ($result && $row = $result->fetch_assoc()) {
            $lastId = $row['id'];
            $num = (int) substr($lastId, 4);
            $newNum = $num + 1;
        } else {
            $newNum = 1;
        }

        $newProductId = 'PR-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);

        // Get form data
        $productName = $conn->real_escape_string($data['productName'] ?? '');
        $category = $conn->real_escape_string($data['category'] ?? '');
        $rawMaterials = $data['rawMaterials'] ?? [];
        $totalRawCost = floatval($data['totalRawCost'] ?? 0);
        $transportCharge = floatval($data['transportCharge'] ?? 0);
        $otherCost = floatval($data['otherCost'] ?? 0);
        $totalProductCost = floatval($data['totalProductCost'] ?? 0);
        $mrpOfProduct = floatval($data['mrpOfProduct'] ?? 0);
        $salePrice = floatval($data['salePrice'] ?? 0);
        $profitLoss = floatval($data['profitLoss'] ?? 0);

        // Encode rawMaterials array as JSON string
        $rawMaterialsJson = json_encode($rawMaterials);

        // Insert into factory_product table
        $insertSql = "INSERT INTO factory_product 
        (id, productName, category, raw_materials, raw_material_total_cost, transport_charge, other_cost, product_total_cost, mrp, selling_price, profitLoss, created_by, created_for) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param(
            "ssssdddddddss",
            $newProductId,
            $productName,
            $category,
            $rawMaterialsJson,
            $totalRawCost,
            $transportCharge,
            $otherCost,
            $totalProductCost,
            $mrpOfProduct,
            $salePrice,
            $profitLoss,
            $user_name,
            $createdFor
        );

        if ($stmt->execute()) {
            echo "Product added successfully!";
        } else {
            echo "Error adding product: " . $conn->error;
        }
        $stmt->close();

    } else if ($whatAction === 'add_stock') {

        $created_for = $conn->real_escape_string($data['createdForSelect'] ?? $user_name); // Use provided created_for or default to user_name

        // Fetch latest product ID current or previous year
        $result = $conn->query("SELECT stock_id FROM factory_stock WHERE created_for = '$created_for' ORDER BY CAST(SUBSTRING(stock_id, 5) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");

        if ($result && $row = $result->fetch_assoc()) {
            $lastId = $row['stock_id'];
            $num = (int) substr($lastId, 4);
            $newNum = $num + 1;
        } else {
            $newNum = 1;
        }

        $newStockId = 'INV-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);

        // Get form data
        $productName = $conn->real_escape_string($data['productName'] ?? '');
        $category = $conn->real_escape_string($data['category'] ?? '');
        $currentQuantity = floatval($data['quantity'] ?? 0);
        $saleValueTotal = floatval($data['saleValueTotal'] ?? 0);
        $saleValuePiece = floatval($data['saleValuePiece'] ?? 0);
        $totalManufacturingCost = floatval($data['totalManufacturingCost'] ?? 0);
        $manufacturingCostPiece = floatval($data['manufacturingCostPiece'] ?? 0);
        $previousStock = floatval($data['previousStock'] ?? 0);
        $previousStockValue = floatval($data['previousStockValue'] ?? 0);
        $quantity = floatval($data['totalStock'] ?? 0);
        $value = floatval($data['totalStockValue'] ?? 0);
        $avgPreviousSale = floatval($data['avgPreviousSale'] ?? 0);
        $avgNewSale = floatval($data['avgNewSale'] ?? 0);
        $status = $conn->real_escape_string($data['status'] ?? 'In stock');
        $record_date = date('Y-m-d');

        // Insert into factory_stock table
        $insertSql = "INSERT INTO factory_stock 
            (stock_id, item_name, category, current_quantity, sale_value_total, sale_value_piece, total_manufacturing_cost, manufacturing_cost_piece, previous_stock, previous_stock_value, quantity, value, avg_previous_sale, avg_new_sale, status, record_date, created_for, created_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param(
            "sssiddddididddssss",
            $newStockId,
            $productName,
            $category,
            $currentQuantity,
            $saleValueTotal,
            $saleValuePiece,
            $totalManufacturingCost,
            $manufacturingCostPiece,
            $previousStock,
            $previousStockValue,
            $quantity,
            $value,
            $avgPreviousSale,
            $avgNewSale,
            $status,
            $record_date,
            $created_for,
            $user_name
        );

        if ($stmt->execute()) {

            // 1. Get product's raw materials from factory_product table
            $productSql = "SELECT raw_materials FROM factory_product WHERE productName = ? AND created_for = ?";
            $productStmt = $conn->prepare($productSql);
            $productStmt->bind_param("ss", $productName, $created_for);
            $productStmt->execute();
            $productResult = $productStmt->get_result();

            if ($productResult && $productRow = $productResult->fetch_assoc()) {
                $rawMaterialsArr = json_decode($productRow['raw_materials'], true);

                // 2. Subtract quantity for each raw material
                if (is_array($rawMaterialsArr)) {
                    foreach ($rawMaterialsArr as $rm) {
                        $rawMaterialId = $rm['id'];
                        $usedQty = floatval($rm['quantity']);

                        // Update factory_raw_material table: subtract usedQty from quantity
                        $updateSql = "UPDATE factory_raw_material SET quantity = quantity - ? WHERE id = ? AND created_for = ?";
                        $updateStmt = $conn->prepare($updateSql);
                        $updateStmt->bind_param("dis", $usedQty, $rawMaterialId, $created_for);
                        $updateStmt->execute();
                        $updateStmt->close();
                    }
                }
            }
            $productStmt->close();

            echo "Stock added successfully!";
        } else {
            echo "Error adding stock: " . $conn->error;
        }
        $stmt->close();

    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Factory Stock Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
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

        /* Custom Dropdown Styles */
        .custom-dropdown {
            position: relative;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .custom-dropdown-button {
            background-color: #0d6efd;
            color: white;
            padding: 0.375rem 0.75rem;
            border: none;
            border-radius: 0.25rem;
            cursor: pointer;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .custom-dropdown-button::after {
            content: '\f078';
            /* FontAwesome chevron-down */
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }

        .custom-dropdown-menu {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 160px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 0.25rem;
            z-index: 1000;
            top: 100%;
            left: 0;
            list-style: none;
            /* Remove bullet points */
            padding: 0;
            /* Remove default ul padding */
            margin: 0;
            /* Remove default ul margin */
        }

        .custom-dropdown-menu.show {
            display: block;
        }

        .custom-dropdown-menu li {
            list-style: none;
            /* Ensure no bullet points on li */
        }

        .custom-dropdown-item {
            display: block;
            padding: 0.5rem 1rem;
            color: #212529;
            text-decoration: none;
            font-size: 1rem;
            box-sizing: border-box;
            /* Prevent padding from affecting width */
            line-height: 1.5;
            /* Improve text readability */
            width: 100%;
            /* Ensure full-width clickable area */
        }

        .custom-dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .custom-dropdown-item.active {
            background-color: #0d6efd;
            color: white;
        }

        /* .table-container {
            max-height: 300px;
            overflow-y: auto;
        } */
        #rawMaterialSuggestions {
            z-index: 2;
        }

        .table-container {
            margin-top: 60px;
            /* Dropdown की height से ज्यादा */
        }

        @media (max-width: 768px) {
            .table-container {
                margin-top: 80px;
            }
        }
    </style>
</head>

<body>
    <h1>Factory Stock Dashboard</h1>
    <p>Monitor and manage production inventory</p>

    <!-- Custom Dropdown -->
    <div class="custom-dropdown mb-3">
        <button class="custom-dropdown-button" type="button" id="customDropdownButton">
            <?php
            if ($selectedEmail === 'all') {
                echo "All";
            } else {
                $found = false;
                foreach ($factoryUsers as $user) {
                    if ($user['user_name'] === $selectedEmail) {
                        echo htmlspecialchars($user['user_name']);
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    echo "All";
                }
            }
            ?>
        </button>
        <ul class="custom-dropdown-menu" id="customDropdownMenu">
            <li>
                <a class="custom-dropdown-item <?php echo $selectedEmail === 'all' ? 'active' : ''; ?>"
                    href="<?php echo $baseUrl . '&user=all'; ?>">All</a>
            </li>
            <?php foreach ($factoryUsers as $user): ?>
                <li>
                    <a class="custom-dropdown-item <?php echo $selectedEmail === $user['user_name'] ? 'active' : ''; ?>"
                        href="<?php echo $baseUrl . '&user=' . urlencode($user['user_name']); ?>">
                        <?php echo htmlspecialchars($user['user_name']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Cards -->
    <div class="row">
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
                <div class="card-body">
                    <h6 class="text-muted">Total Stock Value</h6>
                    <h3 class="fw-bold">₹<?php echo number_format($totalValue, 2); ?></h3>
                    <p class="<?php echo $totalValuePercent >= 0 ? 'text-success' : 'text-danger'; ?>">
                        <?php echo number_format($totalValuePercent, 1) . '% vs last month'; ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
                <div class="card-body">
                    <h6 class="text-muted">Low Stock Items</h6>
                    <h3 class="fw-bold"><?php echo $lowStockCount; ?></h3>
                    <p class="<?php echo $lowStockPercent >= 0 ? 'text-danger' : 'text-success'; ?>">
                        <?php echo ($lowStockCount - $prevLowStockCount) . ' vs last month'; ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
                <div class="card-body">
                    <h6 class="text-muted">Monthly Production</h6>
                    <h3 class="fw-bold"><?php echo number_format($monthlyProduction); ?> units</h3>
                    <p class="<?php echo $monthlyProductionPercent >= 0 ? 'text-success' : 'text-danger'; ?>">
                        <?php echo number_format($monthlyProductionPercent, 1) . '% vs last month'; ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
                <div class="card-body">
                    <h6 class="text-muted">Pending Orders</h6>
                    <h3 class="fw-bold"><?php echo $pending_orders; ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Buttons -->
    <div class="row justify-content-center">
        <div class="col-md-4 col-sm-6 mb-4">
            <button type="button" class="btn btn-outline-primary btn-lg w-100" data-bs-toggle="modal"
                data-bs-target="#addStock">
                <i class="fa-solid fa-plus"></i> Add Stock Entry
            </button>
        </div>
        <div class="col-md-4 col-sm-6 mb-4">
            <form method="get" action="factory_stock.php">
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" required>
                </div>
                <input type="hidden" name="export" value="1" />
                <input type="hidden" name="user" value="<?php echo htmlspecialchars($selectedEmail); ?>" />
                <button type="submit" class="btn btn-outline-primary btn-lg w-100 mt-2">
                    <i class="fa-solid fa-file-lines"></i> Download Stock Report
                </button>
            </form>
        </div>
        <?php
        $stock_count_result = $conn->query("SELECT SUM(quantity) as total_quantity FROM factory_stock WHERE 1=1 $factoryFilter");
        $stock_count = $stock_count_result->fetch_assoc()['total_quantity'];
        ?>
        <div class="col-md-4 col-sm-6 mb-4">
            <button type="button" class="btn btn-outline-primary btn-lg w-100 mb-4">
                <i class="fa-solid fa-clipboard"></i> Stock Count: <?php echo $stock_count; ?>
            </button>
            <button type="button" class="btn btn-outline-primary btn-lg w-100" data-bs-toggle="modal"
                data-bs-target="#addProduct">
                <i class="fa-solid fa-plus"></i> Add Product
            </button>
        </div>
    </div>

    <?php
    // Get raw materials for Add Stock form dropdown
    $rawMaterialSql = "SELECT id, material, cost FROM factory_raw_material WHERE 1=1 $factoryFilter";
    $rawMaterialResult = $conn->query($rawMaterialSql);
    $rawMaterials = [];
    if ($rawMaterialResult->num_rows > 0) {
        while ($row = $rawMaterialResult->fetch_assoc()) {
            $rawMaterials[] = [
                'id' => $row['id'],
                'material' => $row['material'],
                'cost' => $row['cost']
            ];
        }
    }
    // echo '<script>const RawMaterials = ' . json_encode($rawMaterials) . '; console.log("rawMaterials:", RawMaterials);</script>';
    // Convert to JSON for JavaScript
    $rawMaterialsJson = json_encode($rawMaterials);
    ?>

    <div class="modal fade" id="addProduct" tabindex="-1" aria-labelledby="addProductLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form class="bg-white shadow-md rounded-lg p-6 max-w-5xl w-full grid grid-cols-1 gap-6" id="productForm"
                    novalidate>
                    <h1 class="text-2xl font-bold mb-4 border-b pb-2">Add Product Form</h1>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                        <label for="productName" class="font-semibold">Product Name</label>
                        <input type="text" id="productName" name="productName" required
                            class="md:col-span-2 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                        <label for="category" class="font-semibold">Category</label>
                        <input type="text" id="category" name="category" required
                            class="md:col-span-2 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                        <label class="form-label">Create for:</label>
                            <select class="form-select" id="createdFor" name="createdFor" required>
                                <option>Select status</option>
                                <?php
                                $result = $conn->query("SELECT user_name FROM users WHERE user_type = 'Factory'");
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<option value="' . $row['user_name'] . '">' . $row['user_name'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                    </div>

                    <div class="mt-6 font-semibold">Choose Raw Material</div>

                    <div class="flex mt-2 mb-4 position-relative" style="position:relative;">
                        <input type="search" id="searchBar" placeholder="Search raw material..."
                            class="flex-grow border border-gray-300 rounded-l px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600"
                            aria-label="Search raw material" autocomplete="off" />
                        <div id="rawMaterialSuggestions"
                            class="border border-gray-300 rounded bg-white shadow position-absolute"
                            style="max-height:180px; overflow-y:auto; width:100%; left:0; top:100%; display:none;">
                        </div>
                        <button type="button" onclick="addRawMaterial()"
                            class="bg-blue-600 text-white px-4 rounded-r font-semibold hover:bg-blue-700 transition"
                            aria-label="Add selected raw material">
                            Add
                        </button>
                    </div>

                    <!-- Raw Materials Table -->
                    <div id="suggestionGap" style="margin-top: 60px; display: none;"></div>
                    <div class="table-container border border-gray-300 rounded overflow-x-auto mb-4" role="region"
                        aria-labelledby="tableTitle" tabindex="0">
                        <table class="min-w-full divide-y divide-gray-200" aria-describedby="tableTitle">
                            <caption id="tableTitle" class="sr-only">Selected raw materials table</caption>
                            <thead class="bg-gray-100 sticky top-0 z-10">
                                <tr>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-700">ID
                                    </th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-700">Raw
                                        Material</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-700">
                                        Quantity</th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-700">Cost
                                    </th>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-700">Total
                                    </th>
                                    <th scope="col" class="px-3 py-2 text-center text-xs font-medium text-gray-700">
                                        Remove</th>
                                </tr>
                            </thead>
                            <tbody id="rawMaterialRows" class="divide-y divide-gray-200 bg-white"></tbody>
                        </table>
                    </div>

                    <!-- Costs Section -->
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 text-gray-800">
                        <label class="md:col-span-4 font-semibold text-right py-2">Total Cost Of Raw Material</label>
                        <input type="number" id="totalRawCost" readonly value="0"
                            class="md:col-span-2 border border-gray-300 rounded px-3 py-2 bg-gray-100"
                            aria-readonly="true" />

                        <label class="md:col-span-4 font-semibold text-right py-2">Transport Charge</label>
                        <input type="number" id="transportCharge" value="0" min="0" step="0.01"
                            class="md:col-span-2 border border-gray-300 rounded px-3 py-2" />

                        <label class="md:col-span-4 font-semibold text-right py-2">Other Cost</label>
                        <input type="number" id="otherCost" value="0" min="0" step="0.01"
                            class="md:col-span-2 border border-gray-300 rounded px-3 py-2" />

                        <label class="md:col-span-4 font-semibold text-right py-2">Total Cost Of Product</label>
                        <input type="number" id="totalProductCost" readonly value="0"
                            class="md:col-span-2 border border-gray-300 rounded px-3 py-2 bg-gray-100"
                            aria-readonly="true" />
                    </div>

                    <!-- MRP AND Sale Price -->
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 text-gray-800 mt-6">
                        <label class="md:col-span-2 font-semibold py-2">MRP Of Product</label>
                        <input type="number" id="mrpOfProduct" value="0" min="0" step="0.01"
                            class="md:col-span-1 border border-gray-300 rounded px-1 py-2" />

                        <label class="md:col-span-1 font-semibold py-2">Sale Price</label>
                        <input type="number" id="salePrice" value="0" min="0" step="0.01"
                            class="md:col-span-1 border border-gray-300 rounded px-1 py-2" />
                    </div>

                    <!-- Profit/Loss -->
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 text-gray-800 mt-6">

                        <label class="md:col-span-1 font-semibold py-2">Profit &amp; Loss</label>
                        <input type="number" id="profitLoss" readonly value="0"
                            class="md:col-span-1 border border-gray-300 rounded px-1 py-2 bg-gray-100"
                            aria-readonly="true" />
                    </div>

                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded mt-6 transition"
                        aria-label="Submit product form" name="addProduct">
                        Submit Product
                    </button>
                </form>

                <script>
                    // Raw Material sample data (would usually come from backend)
                    // const rawMaterials = [
                    //     { cBox: "y", name: "Bottele", cost: 3 },
                    //     { cBox: "yy", name: "cap", cost: 1 },
                    //     { cBox: "y", name: "sticker", cost: 4.4 },
                    //     { cBox: "aa", name: "aa", cost: 0 },
                    //     { cBox: "ssdd", name: "ssdd", cost: 0 },
                    //     { cBox: "Wrr etc", name: "Wrr etc", cost: 0 },
                    // ];

                    const rawMaterials = <?php echo $rawMaterialsJson; ?>;

                    const rawMaterialRows = document.getElementById("rawMaterialRows");
                    const searchBar = document.getElementById("searchBar");
                    const totalRawCostInput = document.getElementById("totalRawCost");
                    const transportChargeInput = document.getElementById("transportCharge");
                    const otherCostInput = document.getElementById("otherCost");
                    const totalProductCostInput = document.getElementById("totalProductCost");
                    const mrpInput = document.getElementById("mrpOfProduct");
                    const salePriceInput = document.getElementById("salePrice");
                    const profitLossInput = document.getElementById("profitLoss");
                    const createdForInput = document.getElementById("createdFor");

                    let selectedRawMaterials = [];

                    function filterRawMaterials() {
                        const query = searchBar.value.trim().toLowerCase();
                        let filtered = rawMaterials.filter((rm) =>
                            rm.material.toLowerCase().includes(query)
                        );
                        if (filtered.length === 1) {
                            searchBar.value = filtered[0].material;
                        }
                    }

                    function addRawMaterial() {
                        const query = searchBar.value.trim();
                        if (!query) return;
                        // Check if material exists
                        const mat = rawMaterials.find(
                            (rm) => rm.material.toLowerCase() === query.toLowerCase()
                        );
                        if (!mat) {
                            alert("Raw Material not found in list.");
                            return;
                        }
                        // Avoid duplicates by cBox and name
                        if (
                            selectedRawMaterials.some(
                                (rm) => rm.id === mat.id
                            )
                        ) {
                            alert("Raw Material already added.");
                            return;
                        }

                        // Add default quantity 1 and calculate total
                        selectedRawMaterials.push({
                            id: mat.id,
                            material: mat.material,
                            quantity: 1,
                            cost: mat.cost,
                            total: mat.cost * 1,
                        });
                        searchBar.value = "";
                        renderRawMaterials();
                    }

                    // Suggestion list 
                    const rawMaterialSuggestions = document.getElementById("rawMaterialSuggestions");
                    const suggestionGap = document.getElementById("suggestionGap");

                    // Show all suggestions on focus/click
                    searchBar.addEventListener("focus", function () {
                        showSuggestions();
                    });

                    // Also show suggestions on click (for mobile)
                    searchBar.addEventListener("click", function () {
                        showSuggestions();
                    });

                    // On input, filter suggestions
                    searchBar.addEventListener("input", showSuggestions);

                    function showSuggestions() {
                        const query = searchBar.value.trim().toLowerCase();
                        let filtered;
                        if (!query) {
                            // If no query, show all raw materials
                            filtered = rawMaterials;
                        } else {
                            filtered = rawMaterials.filter(rm =>
                                rm.material.toLowerCase().includes(query)
                            );
                        }
                        if (filtered.length === 0) {
                            rawMaterialSuggestions.innerHTML = "<div class='px-3 py-2 text-muted'>No match found</div>";
                            rawMaterialSuggestions.style.display = "block";
                            suggestionGap.style.display = "block";
                            return;
                        }
                        // Sort by match position and alphabetically
                        if (query) {
                            filtered.sort((a, b) => {
                                const posA = a.material.toLowerCase().indexOf(query);
                                const posB = b.material.toLowerCase().indexOf(query);
                                if (posA === posB) {
                                    return a.material.localeCompare(b.material);
                                }
                                return posA - posB;
                            });
                        }
                        // Render suggestions as dropdown
                        rawMaterialSuggestions.innerHTML = filtered.map(rm =>
                            `<div class="px-3 py-2 suggestion-item" style="cursor:pointer;" data-material="${rm.material}">
                                ${rm.material}
                            </div>`
                        ).join("");
                        rawMaterialSuggestions.style.display = "block";
                        suggestionGap.style.display = "block";
                    }

                    // On click, fill searchBar and hide suggestions
                    rawMaterialSuggestions.addEventListener("mousedown", function (e) {
                        // Use mousedown instead of click for better UX
                        const item = e.target.closest(".suggestion-item");
                        if (item) {
                            searchBar.value = item.dataset.material;
                            rawMaterialSuggestions.innerHTML = "";
                            rawMaterialSuggestions.style.display = "none";
                            searchBar.focus();
                        }
                    });

                    // Hide suggestions on blur
                    searchBar.addEventListener("blur", function () {
                        setTimeout(() => {
                            rawMaterialSuggestions.style.display = "none";
                            suggestionGap.style.display = "none";
                        }, 200);
                    });

                    // Hide gap when suggestion list is hidden (also after selection)
                    rawMaterialSuggestions.addEventListener("mousedown", function (e) {
                        const item = e.target.closest(".suggestion-item");
                        if (item) {
                            searchBar.value = item.dataset.material;
                            rawMaterialSuggestions.innerHTML = "";
                            rawMaterialSuggestions.style.display = "none";
                            suggestionGap.style.display = "none";
                            searchBar.focus();
                            // Optionally, you can auto-add the material here:
                            addRawMaterial();
                        }
                    });

                    function renderRawMaterials() {
                        // console.log("Raw Materials:", rawMaterials);
                        rawMaterialRows.innerHTML = "";
                        selectedRawMaterials.forEach((rm, idx) => {
                            const tr = document.createElement("tr");
                            tr.classList.add("hover:bg-gray-50");

                            tr.innerHTML = `
                                <td class="px-3 py-2 align-top whitespace-nowrap">${rm.id}</td>
                                <td class="px-3 py-2 align-top whitespace-normal">${rm.material}</td>
                                <td class="px-3 py-2 align-top">
                                    <input type="number" min="0" step="1" value="${rm.quantity}" data-idx="${idx}" data-field="quantity"
                                        class="w-20 border border-gray-300 rounded px-2 py-1" aria-label="Quantity for ${rm.material}" />
                                </td>
                                <td class="px-3 py-2 align-top">
                                    <input type="number" min="0" step="0.01" value="${rm.cost}" data-idx="${idx}" data-field="cost"
                                        class="w-24 border border-gray-300 rounded px-2 py-1" aria-label="Cost per unit for ${rm.material}" />
                                </td>
                                <td class="px-3 py-2 align-top">${rm.total.toFixed(2)}</td>
                                <td class="px-3 py-2 align-top text-center">
                                    <button type="button" data-idx="${idx}" aria-label="Remove ${rm.material}"
                                        class="text-red-600 hover:text-red-900 font-bold">×</button>
                                </td>
                            `;

                            rawMaterialRows.appendChild(tr);
                        });

                        attachRowEventListeners();
                        calculateTotals();
                    }

                    function attachRowEventListeners() {
                        // Quantity and Cost input listeners
                        rawMaterialRows.querySelectorAll("input").forEach((input) => {
                            input.addEventListener("input", (e) => {
                                const idx = e.target.dataset.idx;
                                const field = e.target.dataset.field;
                                if (!idx || !field) return;
                                let val = parseFloat(e.target.value);
                                if (isNaN(val) || val < 0) val = 0;
                                selectedRawMaterials[idx][field] = val;

                                // Recalculate total for row
                                selectedRawMaterials[idx].total =
                                    selectedRawMaterials[idx].quantity * selectedRawMaterials[idx].cost;

                                renderRawMaterials();
                            });
                        });

                        // Remove button listeners
                        rawMaterialRows.querySelectorAll("button").forEach((btn) => {
                            btn.addEventListener("click", (e) => {
                                const idx = e.target.dataset.idx;
                                if (idx !== undefined) {
                                    selectedRawMaterials.splice(idx, 1);
                                    renderRawMaterials();
                                }
                            });
                        });
                    }

                    function calculateTotals() {
                        // Sum raw materials total
                        const rawCost = selectedRawMaterials.reduce(
                            (acc, cur) => acc + cur.total,
                            0
                        );

                        totalRawCostInput.value = rawCost.toFixed(2);

                        const transportCharge = parseFloat(transportChargeInput.value) || 0;
                        const otherCost = parseFloat(otherCostInput.value) || 0;
                        const totalProductCost = rawCost + transportCharge + otherCost;
                        totalProductCostInput.value = totalProductCost.toFixed(2);

                        const mrp = parseFloat(mrpInput.value) || 0;
                        const salePrice = parseFloat(salePriceInput.value) || 0;

                        // Profit & Loss = Sale Price - Total Product Cost
                        const profitLoss = salePrice - totalProductCost;
                        profitLossInput.value = profitLoss.toFixed(2);
                    }

                    transportChargeInput.addEventListener("input", calculateTotals);
                    otherCostInput.addEventListener("input", calculateTotals);
                    mrpInput.addEventListener("input", calculateTotals);
                    salePriceInput.addEventListener("input", calculateTotals);

                    // Prevent form submission and just alert the details for now
                    document.getElementById("productForm").addEventListener("submit", (e) => {
                        e.preventDefault();

                        const formData = {
                            productName: e.target.productName.value.trim(),
                            category: e.target.category.value.trim(),
                            rawMaterials: selectedRawMaterials,
                            totalRawCost: parseFloat(totalRawCostInput.value),
                            transportCharge: parseFloat(transportChargeInput.value),
                            otherCost: parseFloat(otherCostInput.value),
                            totalProductCost: parseFloat(totalProductCostInput.value),
                            mrpOfProduct: parseFloat(mrpInput.value),
                            salePrice: parseFloat(salePriceInput.value),
                            profitLoss: parseFloat(profitLossInput.value),
                            createdFor: e.target.createdFor.value.trim(),
                            whatAction: "add_product",
                        };

                        // Simple validation for product name and category
                        if (!formData.productName) {
                            alert("Please enter Product Name");
                            return;
                        }
                        if (!formData.category) {
                            alert("Please enter Category");
                            return;
                        }
                        if (!formData.createdFor) {
                            alert("Please select Created For");
                            return;
                        }

                        if (formData.rawMaterials.length === 0) {
                            alert("Please add at least one raw material");
                            return;
                        }

                        fetch("inventory.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify(formData)
                        })
                            .then(res => res.text())
                            .then(msg => {
                                // alert(msg);
                                // console.log(msg);
                                location.reload();
                                // console.log("Product Submission Data: ", formData);
                                // alert('Product data submitted successfully! Check console for details.');
                            })
                            .catch(err => alert("Error submitting invoice."));

                        // Reset form if desired:
                        // e.target.reset();
                        // selectedRawMaterials = [];
                        // renderRawMaterials();
                        // calculateTotals();
                    });

                    // Initial render empty
                    renderRawMaterials();
                </script>
            </div>
        </div>
    </div>

    <!-- Add Stock Form -->
    <div class="modal fade" id="addStock" tabindex="-1" aria-labelledby="addStockLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="stockForm" class="space-y-8 px-4 w-full py-2">
                    <h1 class="text-2xl font-bold border-b">Add Stock Form</h1>
                    <!-- Select Product and Quantity -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="productSelect" class="block font-medium mb-2 text-gray-700">Select
                                Product</label>
                            <select id="productSelect" required
                                class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <option value="" disabled selected>Select a product</option>
                                <?php
                                $result = $conn->query("SELECT * FROM factory_product WHERE 1=1 $factoryFilter");
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $productName = $row['productName'];
                                        $category = $row['category'];
                                        $manufacturing = $row['product_total_cost'];

                                        // Fetch latest stock and value from factory_stock table
                                        $stockSql = "SELECT quantity as prevstock, value as prevvalue, avg_new_sale as prevavg 
                                            FROM factory_stock 
                                            WHERE item_name = '$productName' AND 1=1 $factoryFilter 
                                            ORDER BY record_date DESC, stock_id DESC LIMIT 1";
                                        $stockResult = $conn->query($stockSql);
                                        $prevstock = 0;
                                        $prevvalue = 0;
                                        $prevavg = 0;
                                        if ($stockResult && $stockResult->num_rows > 0) {
                                            $stockRow = $stockResult->fetch_assoc();
                                            $prevstock = $stockRow['prevstock'] ?? 0;
                                            $prevvalue = $stockRow['prevvalue'] ?? 0;
                                            $prevavg = $stockRow['prevavg'] ?? 0;
                                        }

                                        echo '<option value="' . htmlspecialchars($productName) . '" 
                                        data-category="' . htmlspecialchars($category) . '" 
                                        data-manufacturing="' . htmlspecialchars($manufacturing) . '" 
                                        data-prevstock="' . htmlspecialchars($prevstock) . '" 
                                        data-prevavg="' . htmlspecialchars($prevavg) . '" 
                                        data-prevvalue="' . htmlspecialchars($prevvalue) . '">'
                                            . htmlspecialchars($productName) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label for="quantityInput" class="block font-medium mb-2 text-gray-700">Quantity</label>
                            <input type="number" id="quantityInput" min="0" value="0" required
                                class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="category" class="block font-medium mb-2 text-gray-700">
                                Category
                            </label>
                            <input type="text" id="Category" readonly
                                class="w-full bg-gray-100 border border-gray-300 rounded-md p-2" />
                        </div>
                        <div>
                            <label for="status" class="block font-medium mb-2 text-gray-700">
                                Status
                            </label>
                            <select class="form-select" id="Status" name="Status" required>
                                <option value="In stock">In stock</option>
                                <option value="Low stock">Low stock</option>
                                <option value="Out of stock">Out of stock</option>
                            </select>
                        </div>
                    </div>

                    <!-- Sale Value of new stock in Amount & Sale Value Amount per piece -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="saleValueTotal" class="block font-medium mb-2 text-gray-700">
                                Sale Value of New Stock (Amount)
                            </label>
                            <input type="number" step="any" min="0" id="saleValueTotal" value="0" required
                                class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
                        </div>
                        <div>
                            <label for="saleValuePiece" class="block font-medium mb-2 text-gray-700">
                                Sale Value Amount per Piece
                            </label>
                            <input type="number" step="any" min="0" id="saleValuePiece" value="0" readonly
                                class="w-full bg-gray-100 border border-gray-300 rounded-md p-2" />
                        </div>
                    </div>

                    <!-- Manufacturing Cost and Previous Stock Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                        <div>
                            <label for="totalManufacturingCost" class="block font-medium mb-2 text-gray-700">
                                Total Manufacturing Cost
                            </label>
                            <input type="number" step="any" min="0" id="totalManufacturingCost" value="0" required
                                class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
                        </div>
                        <div>
                            <label for="manufacturingCostPiece" class="block font-medium mb-2 text-gray-700">
                                Manufacturing Cost per Piece (Auto Fill)
                            </label>
                            <input type="number" step="any" min="0" id="manufacturingCostPiece" value="0" readonly
                                class="w-full bg-gray-100 border border-gray-300 rounded-md p-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                        <div>
                            <label for="previousStock" class="block font-medium mb-2 text-gray-700">
                                Previous Stock (Auto fill)
                            </label>
                            <input type="number" step="1" min="0" id="previousStock" value="0" readonly
                                class="w-full bg-gray-100 border border-gray-300 rounded-md p-2" />
                        </div>

                        <div>
                            <label for="previousStockValue" class="block font-medium mb-2 text-gray-700">
                                Previous Stock Value (Auto fill)
                            </label>
                            <input type="number" step="any" min="0" id="previousStockValue" value="0" readonly
                                class="w-full bg-gray-100 border border-gray-300 rounded-md p-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                        <div>
                            <label for="totalStock" class="block font-medium mb-2 text-gray-700">
                                Total Stock (Auto fill)
                            </label>
                            <input type="number" step="1" min="0" id="totalStock" value="0" readonly
                                class="w-full bg-gray-100 border border-gray-300 rounded-md p-2" />
                        </div>

                        <div>
                            <label for="totalStockValue" class="block font-medium mb-2 text-gray-700">
                                Total Stock Value (Auto fill)
                            </label>
                            <input type="number" step="any" min="0" id="totalStockValue" value="0" readonly
                                class="w-full bg-gray-100 border border-gray-300 rounded-md p-2" />
                        </div>
                    </div>

                    <!-- Average per piece sale values -->
                    <div class="flex flex-col md:flex-row justify-between gap-6 pt-6 border-t border-gray-300">
                        <div class="flex-1">
                            <label class="block font-medium mb-2 text-gray-700" for="avgPreviousSale">
                                Average Previous Per Piece Sale (Auto fill)
                            </label>
                            <input type="number" step="any" min="0" id="avgPreviousSale" value="0" readonly
                                class="w-full bg-gray-100 border border-gray-300 rounded-md p-2" />
                        </div>
                        <div class="flex-1">
                            <label class="block font-medium mb-2 text-gray-700" for="avgCurrentSale">
                                Current Average Per Piece Sale Value (Auto fill)
                            </label>
                            <input type="number" step="any" min="0" id="avgCurrentSale" value="0" readonly
                                class="w-full bg-gray-100 border border-gray-300 rounded-md p-2" />
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row justify-between gap-6 pt-6 border-t border-gray-300">
                        <div class="flex-1">
                            <label class="block font-medium mb-2 text-gray-700" for="avgNewSale">
                                New Average Per Piece Sale Value (Auto fill)
                            </label>
                            <input type="number" step="any" min="0" id="avgNewSale" value="0" readonly
                                class="w-full bg-gray-100 border border-gray-300 rounded-md p-2" />
                        </div>
                        <div class="flex-1">
                            <label class="form-label">Create for:</label>
                            <select class="form-select" id="created_for" name="created_for" required>
                                <option>Select status</option>
                                <?php
                                $result = $conn->query("SELECT user_name FROM users WHERE user_type = 'Factory'");
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<option value="' . $row['user_name'] . '">' . $row['user_name'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <button type="submit"
                            class="w-full max-w-xs bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded transition"
                            aria-label="Submit stock form" name="addStock">
                            Submit Stock
                        </button>
                    </div>
                </form>
                <script>
                    // Cache DOM elements
                    const productSelect = document.getElementById('productSelect');
                    const quantityInput = document.getElementById('quantityInput');
                    const categoryInput = document.getElementById('Category');
                    const statusInput = document.getElementById('Status');
                    const saleValueTotal = document.getElementById('saleValueTotal');
                    const saleValuePiece = document.getElementById('saleValuePiece');
                    const totalManufacturingCost = document.getElementById('totalManufacturingCost');
                    const manufacturingCostPiece = document.getElementById('manufacturingCostPiece');

                    const previousStock = document.getElementById('previousStock');
                    const previousStockValue = document.getElementById('previousStockValue');

                    const totalStock = document.getElementById('totalStock');
                    const totalStockValue = document.getElementById('totalStockValue');

                    const avgPreviousSale = document.getElementById('avgPreviousSale');
                    const avgNewSale = document.getElementById('avgNewSale');
                    const avgCurrentSale = document.getElementById('avgCurrentSale');
                    const createdForSelect = document.getElementById('created_for');

                    function resetAutoFillFields() {
                        categoryInput.value = '';
                        statusInput.value = 'In stock';
                        previousStock.value = 0;
                        previousStockValue.value = 0;
                        manufacturingCostPiece.value = 0;
                        totalStock.value = 0;
                        totalStockValue.value = 0;
                        avgPreviousSale.value = 0;
                        avgNewSale.value = 0;
                        avgCurrentSale.value = 0;
                        saleValuePiece.value = 0;
                        createdForSelect.value = '';
                    }

                    function fillProductAutoFillValues() {
                        if (!productSelect.value) {
                            resetAutoFillFields();
                            return;
                        }
                        const selectedOption = productSelect.options[productSelect.selectedIndex];
                        const prevStock = parseFloat(selectedOption.getAttribute('data-prevstock')) || 0;
                        const prevStockVal = parseFloat(selectedOption.getAttribute('data-prevvalue')) || 0;
                        const manufacturingCost = parseFloat(selectedOption.getAttribute('data-manufacturing')) || 0;
                        const prevAvg = parseFloat(selectedOption.getAttribute('data-prevavg')) || 0;
                        const category = selectedOption.getAttribute('data-category') || '';

                        previousStock.value = prevStock.toFixed(2);
                        previousStockValue.value = prevStockVal.toFixed(2);
                        manufacturingCostPiece.value = manufacturingCost.toFixed(2);
                        avgPreviousSale.value = prevAvg.toFixed(2);
                        categoryInput.value = category;

                        calculateAutoFields();
                    }

                    function calculateAutoFields() {
                        const qty = parseFloat(quantityInput.value) || 0;
                        const saleAmount = parseFloat(saleValueTotal.value) || 0;
                        const manufacturingCostUnit = parseFloat(manufacturingCostPiece.value) || 0;
                        const prevStockVal = parseFloat(previousStockValue.value) || 0;
                        const prevStockQty = parseFloat(previousStock.value) || 0;

                        // Sale value per piece = sale value total / quantity (if quantity > 0)
                        if (qty > 0) {
                            saleValuePiece.value = (saleAmount / qty).toFixed(2);
                        } else {
                            saleValuePiece.value = '0.00';
                        }

                        // Total manufacturing cost can be auto calculated or entered manually, 
                        // but let's calculate automatically here = manufacturing cost per piece * quantity
                        if (qty > 0) {
                            const totalManCostCalc = manufacturingCostUnit * qty;
                            totalManufacturingCost.value = totalManCostCalc.toFixed(2);
                        } else {
                            totalManufacturingCost.value = '0.00';
                        }

                        // Calculate total stock and total stock value
                        const totalStockQty = prevStockQty + qty;
                        const newStockValue = saleAmount; // sale value of the new stock

                        const totalStockVal = prevStockVal + newStockValue;

                        totalStock.value = totalStockQty.toFixed(2);
                        totalStockValue.value = totalStockVal.toFixed(2);

                        // Calculate new average per piece sale value
                        let newAvg = 0;
                        if (totalStockQty > 0) {
                            newAvg = totalStockVal / totalStockQty;
                        }

                        avgNewSale.value = newAvg.toFixed(2);

                        // Calculate current average per piece sale value
                        let currentAvg = 0;
                        if (qty > 0 && saleAmount > 0) {
                            currentAvg = saleAmount / qty;
                        }
                        avgCurrentSale.value = currentAvg.toFixed(2);
                    }

                    // Event Listeners
                    productSelect.addEventListener('change', () => {
                        fillProductAutoFillValues();
                    });

                    quantityInput.addEventListener('input', () => {
                        calculateAutoFields();
                    });

                    saleValueTotal.addEventListener('input', () => {
                        calculateAutoFields();
                    });

                    totalManufacturingCost.addEventListener('input', () => {
                        // If user manually updates total manufacturing cost, update manufacturing cost per piece accordingly
                        const qty = parseFloat(quantityInput.value) || 0;
                        const totalManCost = parseFloat(totalManufacturingCost.value) || 0;

                        if (qty > 0) {
                            manufacturingCostPiece.value = (totalManCost / qty).toFixed(2);
                        } else {
                            manufacturingCostPiece.value = '0.00';
                        }
                    });

                    document.getElementById("stockForm").addEventListener("submit", function (e) {
                        e.preventDefault();

                        const formData = {
                            productName: document.getElementById('productSelect').value,
                            category: document.getElementById('Category').value,
                            quantity: parseFloat(document.getElementById('quantityInput').value),
                            saleValueTotal: parseFloat(document.getElementById('saleValueTotal').value),
                            saleValuePiece: parseFloat(document.getElementById('saleValuePiece').value),
                            totalManufacturingCost: parseFloat(document.getElementById('totalManufacturingCost').value),
                            manufacturingCostPiece: parseFloat(document.getElementById('manufacturingCostPiece').value),
                            previousStock: parseFloat(document.getElementById('previousStock').value),
                            previousStockValue: parseFloat(document.getElementById('previousStockValue').value),
                            totalStock: parseFloat(document.getElementById('totalStock').value),
                            totalStockValue: parseFloat(document.getElementById('totalStockValue').value),
                            avgPreviousSale: parseFloat(document.getElementById('avgPreviousSale').value),
                            avgNewSale: parseFloat(document.getElementById('avgNewSale').value),
                            createdForSelect: document.getElementById('created_for').value,
                            status: document.getElementById('Status').value,
                            whatAction: "add_stock"
                        };

                        if (!formData.productName) {
                            alert("Please select Product Name");
                            return;
                        }
                        if (!formData.createdForSelect) {
                            alert("Please select Created For");
                            return;
                        }

                        fetch("inventory.php", {
                            method: "POST",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify(formData)
                        })
                            .then(res => res.text())
                            .then(msg => {
                                // alert(msg);
                                // console.log(msg);
                                location.reload();
                            })
                            .catch(err => alert("Error submitting stock entry."));
                    });

                    // Initialize form with defaults
                    resetAutoFillFields();
                </script>
            </div>
        </div>
    </div>

    <!-- Form Processing -->
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addStockSubmit'])) {
        $item_name = !empty($_POST['customItemName']) ? $conn->real_escape_string($_POST['customItemName']) : $conn->real_escape_string($_POST['itemName']);
        $category = !empty($_POST['customCategory']) ? $conn->real_escape_string($_POST['customCategory']) : $conn->real_escape_string($_POST['category']);
        $quantity = intval($_POST['quantity']);
        $value = floatval($_POST['value']);
        $record_date = date('Y-m-d');
        $status = $conn->real_escape_string($_POST['Status']);
        $created_for = $conn->real_escape_string($_POST['created_for']);

        if (empty($item_name) || empty($category)) {
            echo "<script>alert('Please select or enter an item name and category.');</script>";
        } else {
            $insertSql = "INSERT INTO factory_stock (item_name, category, quantity, value, status, record_date, createdby, created_for) 
                          VALUES ('$item_name', '$category', $quantity, $value, '$status', '$record_date', '$user_name', '$created_for')";
            if ($conn->query($insertSql)) {
                echo "<script>alert('Stock added successfully!'); window.location.href=window.location.href;</script>";
            } else {
                echo "<script>alert('Error adding stock: " . $conn->error . "');</script>";
            }
        }
    }
    ?>

    <!-- Charts -->
    <div class="chart-container">
        <div class="chart-box">
            <h3>Stock Value by Category</h3>
            <div style="position: relative; width: 100%; max-width: 300px; margin: 0 auto;">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
        <div class="chart-box">
            <h3>Stock Value Trend (Last 6 Months)</h3>
            <canvas id="lineChart"></canvas>
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

    <!-- Table for current stock -->
    <div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">
        <div id="factory">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <div class="justify-content-start">
                    <h1>Current Stock</h1>
                </div>
                <div class="justify-content-end">
                    <a href="admin_dashboard.php?page=factory_stock&view=<?php echo isset($_GET['view']) && $_GET['view'] === 'all' ? 'none' : 'all'; ?>&user=<?php echo urlencode($selectedEmail); ?>"
                        class="btn btn-outline-primary">
                        <?php echo isset($_GET['view']) && $_GET['view'] === 'all' ? 'Show Less' : 'View All'; ?>
                    </a>
                </div>
            </div>
            <table id="Table" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Record Date</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Sale Value Total</th>
                        <th>Sale Value Per Piece</th>
                        <th>Total Manufacturing Cost</th>
                        <th>Manufacturing Cost Per Piece</th>
                        <th>Previous Stock</th>
                        <th>Previous Stock Value</th>
                        <th>Total Stock</th>
                        <th>Total Stock Value</th>
                        <th>Avg Previous Sale</th>
                        <th>Avg New Sale</th>
                        <?php if ($selectedEmail === 'all'): ?>
                            <th>Factory</th>
                        <?php endif; ?>
                        <th>Status</th>
                        <?php if ($hasDeletePermission): ?>
                            <th>Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $limit = (isset($_GET['view']) && $_GET['view'] === 'all') ? '' : 'LIMIT 5';
                    $sql = "SELECT * FROM factory_stock WHERE 1=1 $factoryFilter ORDER BY stock_id DESC $limit";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $status = htmlspecialchars($row['status']);
                            echo "<tr>
                                <td>" . htmlspecialchars($row['stock_id']) . "</td>
                                <td>" . htmlspecialchars($row['record_date']) . "</td>
                                <td>" . htmlspecialchars($row['item_name']) . "</td>
                                <td>" . htmlspecialchars($row['category']) . "</td>
                                <td>" . htmlspecialchars($row['current_quantity']) . "</td>
                                <td>₹" . number_format($row['sale_value_total'], 2) . "</td>
                                <td>₹" . number_format($row['sale_value_piece'], 2) . "</td>
                                <td>₹" . number_format($row['total_manufacturing_cost'], 2) . "</td>
                                <td>₹" . number_format($row['manufacturing_cost_piece'], 2) . "</td>
                                <td>" . htmlspecialchars($row['previous_stock']) . "</td>
                                <td>₹" . number_format($row['previous_stock_value'], 2) . "</td>
                                <td>" . htmlspecialchars($row['quantity']) . "</td>
                                <td>₹" . number_format($row['value'], 2) . "</td>
                                <td>₹" . number_format($row['avg_previous_sale'], 2) . "</td>
                                <td>₹" . number_format($row['avg_new_sale'], 2) . "</td>";
                            if ($selectedEmail === 'all') {
                                echo "<td>" . htmlspecialchars($row['created_for']) . "</td>";
                            }
                            echo "<td>";
                            if ($status == 'In stock') {
                                echo '<span class="badge rounded-pill" style="background-color: #198754; color: white; padding: 8px 16px;">In Stock</span>';
                            } elseif ($status == 'Low stock') {
                                echo '<span class="badge rounded-pill" style="background-color: #ffc107; color: white; padding: 8px 16px;">Low Stock</span>';
                            } elseif ($status == 'Out of stock') {
                                echo '<span class="badge rounded-pill" style="background-color: #dc3545; color: white; padding: 8px 16px;">Out of Stock</span>';
                            }
                            echo "</td>";
                            if ($hasDeletePermission) {
                                echo "<td>
                                    <form method='post' action='' onsubmit='return confirm(\"Are you sure you want to delete this stock item?\");'>
                                        <input type='hidden' name='stock_id' value='" . htmlspecialchars($row['stock_id']) . "'>
                                        <button type='submit' name='deleteStock' class='btn btn-danger btn-sm'>
                                            <i class='fa-solid fa-trash'></i> Delete
                                        </button>
                                    </form>
                                  </td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        $colspan = ($hasDeletePermission && $selectedEmail === 'all')
                            ? 18
                            : (($hasDeletePermission || $selectedEmail === 'all')
                                ? 17
                                : 16);
                        echo "<tr><td colspan='{$colspan}'>No stock data found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteStock']) && $hasDeletePermission) {
        $stock_id = $conn->real_escape_string($_POST['stock_id']);

        // Prepare and execute delete query
        $deleteSql = "DELETE FROM factory_stock WHERE stock_id = ? AND created_for = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("ss", $stock_id, $user_name);

        if ($stmt->execute()) {
            echo "<script>alert('Stock item deleted successfully!'); window.location.href=window.location.href;</script>";
        } else {
            echo "<script>alert('Error deleting stock: " . $conn->error . "');</script>";
        }

        $stmt->close();
    }
    ?>

    <!-- Product Table -->

    <div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">
        <div id="factory">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <div class="justify-content-start">
                    <h1>Product Table</h1>
                </div>
            </div>

            <table id="Table" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Raw Material</th>
                        <th>Total Cost</th>
                        <th>Transport Charge</th>
                        <th>Other Charge</th>
                        <th>Total Cost of Product</th>
                        <th>MRP</th>
                        <th>Sales Price</th>
                        <th>Profit/Loss</th>
                        <?php if ($selectedEmail === 'all'): ?>
                            <th>Factory</th>
                        <?php endif; ?>
                        <?php if ($hasDeletePermission): ?>
                            <th>Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM factory_product WHERE 1=1 $factoryFilter ORDER BY id DESC";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['productName']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                            // Raw Materials (nested table)
                            $rawMaterialsArr = json_decode($row['raw_materials'], true);
                            echo "<td>";
                            if (is_array($rawMaterialsArr) && count($rawMaterialsArr) > 0) {
                                echo "<table class='table table-sm mb-0'>";
                                echo "<tr><th>ID</th><th>Material</th><th>Qty</th><th>Cost</th><th>Total</th></tr>";
                                foreach ($rawMaterialsArr as $rm) {
                                    echo "<tr>
                                    <td>" . htmlspecialchars($rm['id']) . "</td>
                                    <td>" . htmlspecialchars($rm['material']) . "</td>
                                    <td>" . htmlspecialchars($rm['quantity']) . "</td>
                                    <td>₹" . htmlspecialchars($rm['cost']) . "</td>
                                    <td>₹" . htmlspecialchars($rm['total']) . "</td>
                                </tr>";
                                }
                                echo "</table>";
                            } else {
                                echo "-";
                            }
                            echo "</td>";

                            echo "<td>₹" . number_format($row['raw_material_total_cost'], 2) . "</td>";
                            echo "<td>₹" . number_format($row['transport_charge'], 2) . "</td>";
                            echo "<td>₹" . number_format($row['other_cost'], 2) . "</td>";
                            echo "<td>₹" . number_format($row['product_total_cost'], 2) . "</td>";
                            echo "<td>₹" . number_format($row['mrp'], 2) . "</td>";
                            echo "<td>₹" . number_format($row['selling_price'], 2) . "</td>";
                            echo "<td>₹" . number_format($row['profitLoss'], 2) . "</td>";
                            if ($selectedEmail === 'all') {
                                echo "<td>" . htmlspecialchars($row['created_for']) . "</td>";
                            }

                            if ($hasDeletePermission) {
                                echo "<td>
                                <form method='post' action='' onsubmit='return confirm(\"Are you sure you want to delete this product?\");'>
                                    <input type='hidden' name='product_id' value='" . htmlspecialchars($row['id']) . "'>
                                    <button type='submit' name='deleteProduct' class='btn btn-danger btn-sm'>
                                        <i class='fa-solid fa-trash'></i> Delete
                                    </button>
                                </form>
                              </td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        $colspan1 = ($hasDeletePermission && $selectedEmail === 'all')
                            ? 13
                            : (($hasDeletePermission || $selectedEmail === 'all')
                                ? 12
                                : 11);
                        echo "<tr><td colspan='{$colspan1}'>No product data found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteProduct']) && $hasDeletePermission) {
        $product_id = $conn->real_escape_string($_POST['product_id']);

        // Prepare and execute delete query
        $deleteSql = "DELETE FROM factory_product WHERE id = ? AND createdFor = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("ss", $product_id, $user_name);

        if ($stmt->execute()) {
            echo "<script>alert('Product deleted successfully!'); window.location.href=window.location.href;</script>";
        } else {
            echo "<script>alert('Error deleting product: " . $conn->error . "');</script>";
        }

        $stmt->close();
    }
    ?>

    <!-- Check low stock -->
    <?php
    $low_stock_items = [];
    // $query = $conn->prepare("SELECT item_name, quantity, status, created_for FROM factory_stock WHERE status IN ('Low Stock', 'Out of Stock') $factoryFilter");
    $query = $conn->prepare("SELECT fs.item_name, fs.quantity, fs.status, fs.created_for
        FROM factory_stock fs
        JOIN (
            SELECT item_name, MAX(CONCAT(record_date, LPAD(stock_id, 10, '0'))) AS latest_key
            FROM factory_stock
            WHERE status IN ('Low Stock', 'Out of Stock')
            $factoryFilter
            GROUP BY item_name
        ) latest
            ON CONCAT(fs.record_date, LPAD(fs.stock_id, 10, '0')) = latest.latest_key
        WHERE fs.status IN ('Low Stock', 'Out of Stock')
        $lowStockFilter");
    $query->execute();
    $result = $query->get_result();
    while ($row = $result->fetch_assoc()) {
        $level = ($row['status'] === 'Out of Stock') ? 'Critical' : 'Low';
        $low_stock_items[] = [
            'item' => $row['item_name'],
            'stock' => $row['quantity'],
            'level' => $level,
            'factory' => $row['created_for']
        ];
    }
    $query->close();
    ?>

    <!-- Low Stock and Supply Trends -->
    <div class="row g-4 mt-4">
        <div class="col-md-6">
            <div class="card p-3">
                <h5 class="fw-bold text-warning"><i class="bi bi-exclamation-circle"></i> Low Stock Alert</h5>
                <div class="space-y-4">
                    <?php foreach ($low_stock_items as $item): ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="font-medium">
                                <?php echo htmlspecialchars($item['item']); ?>
                                <?php if ($selectedEmail === 'all'): ?>
                                    (<?php echo htmlspecialchars($item['factory']); ?>)
                                <?php endif; ?>
                            </span>
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
        $query = $conn->prepare("SELECT item_name, quantity FROM invoice WHERE date BETWEEN ? AND ? $factoryFilter");
        $query->bind_param("ss", $startOfMonth, $endOfMonth);
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
        arsort($item_sales);
        $top_items = array_slice($item_sales, 0, 5, true);
        foreach ($top_items as $item => $qty) {
            $percentage = min(100, round(($qty / 1000) * 100));
            $popular_products[] = [
                'item' => $item,
                'quantity' => $qty,
                'percentage' => $percentage
            ];
        }
        ?>
        <div class="col-md-6">
            <div class="card p-3">
                <h5 class="fw-bold text-primary"><i class="bi bi-graph-up"></i> Supply Trends</h5>
                <p class="text-muted mb-4">Monthly procurement of top 5 raw materials</p>
                <div class="space-y-3">
                    <?php foreach ($popular_products as $product): ?>
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-sm font-medium"><?php echo htmlspecialchars($product['item']); ?></span>
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

    <script>
        // Custom Dropdown Logic
        document.addEventListener('DOMContentLoaded', function () {
            const dropdownButton = document.getElementById('customDropdownButton');
            const dropdownMenu = document.getElementById('customDropdownMenu');

            if (!dropdownButton || !dropdownMenu) {
                console.error('Custom dropdown elements not found.');
                return;
            }

            // Toggle dropdown on button click
            dropdownButton.addEventListener('click', function () {
                dropdownMenu.classList.toggle('show');
                console.log('Dropdown toggled:', dropdownMenu.classList.contains('show') ? 'Open' : 'Closed');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function (event) {
                if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.classList.remove('show');
                    console.log('Dropdown closed (clicked outside)');
                }
            });

            // Close dropdown when an item is clicked
            dropdownMenu.querySelectorAll('.custom-dropdown-item').forEach(function (item) {
                item.addEventListener('click', function () {
                    dropdownMenu.classList.remove('show');
                    console.log('Dropdown item clicked:', item.textContent);
                });
            });
        });

        function toggleItemInput() {
            var select = document.getElementById('itemName');
            var input = document.getElementById('customItemName');
            input.style.display = select.value === 'Other' ? 'block' : 'none';
            if (select.value !== 'Other') input.value = '';
        }
        function toggleCategoryInput() {
            var select = document.getElementById('category');
            var input = document.getElementById('customCategory');
            input.style.display = select.value === 'Other' ? 'block' : 'none';
            if (select.value !== 'Other') input.value = '';
        }
        function toggleTransferInput() {
            var select = document.getElementById('transfer_to');
            var input = document.getElementById('customTransferTo');
            input.style.display = select.value === 'Other' ? 'block' : 'none';
            if (select.value !== 'Other') input.value = '';
        }
        function toggleCreatedByInput() {
            const select = document.getElementById("createdBy");
            const customInput = document.getElementById("customCreatedBy");
            if (select && customInput) {
                customInput.style.display = select.value === "Other" ? "block" : "none";
                customInput.required = select.value === "Other";
            }
        }
        var categoryLabels = <?php echo json_encode(array_keys($categoryValues)); ?>;
        var categoryData = <?php echo json_encode(array_values($categoryValues)); ?>;
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryData,
                    backgroundColor: ['#0d6efd', '#20c997', '#ffc107', '#fd7e14', '#6f42c1', '#6610f2', '#198754', '#d63384']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#333', font: { size: 14 } }
                    }
                }
            }
        });
        var trendLabels = <?php echo json_encode($trendLabels); ?>;
        var trendData = <?php echo json_encode($trendData); ?>;
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Stock Value',
                    data: trendData,
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
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 250000,
                            callback: function (value) { return '₹' + value.toLocaleString(); }
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>