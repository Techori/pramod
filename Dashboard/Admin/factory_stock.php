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

// Get item names for Add Stock form dropdown
$itemSql = "SELECT DISTINCT item_name FROM factory_stock WHERE 1=1 $factoryFilter ORDER BY item_name";
$itemResult = $conn->query($itemSql);
$items = [];
if ($itemResult->num_rows > 0) {
    while ($row = $itemResult->fetch_assoc()) {
        $items[] = $row['item_name'];
    }
}

// Get categories for Add Stock form dropdown
$categorySql = "SELECT DISTINCT category FROM factory_stock WHERE 1=1 $factoryFilter ORDER BY category";
$categoryResult = $conn->query($categorySql);
$categories = [];
if ($categoryResult->num_rows > 0) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

// Get stock items for Stock Transfer form dropdown
$stockSql = "SELECT stock_id, item_name FROM factory_stock WHERE 1=1 $factoryFilter ORDER BY item_name";
$stockResult = $conn->query($stockSql);
$stocks = [];
if ($stockResult->num_rows > 0) {
    while ($row = $stockResult->fetch_assoc()) {
        $stocks[] = $row;
    }
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Factory Stock Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw=="
        crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
        crossorigin="anonymous"></script>
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
                <button type="submit" class="btn btn-outline-primary btn-lg w-100">
                    <i class="fa-solid fa-file-lines"></i> Download Stock Report
                </button>
            </form>
        </div>
        <?php
        $stock_count_result = $conn->query("SELECT SUM(quantity) as total_quantity FROM factory_stock WHERE 1=1 $factoryFilter");
        $stock_count = $stock_count_result->fetch_assoc()['total_quantity'];
        ?>
        <div class="col-md-4 col-sm-6 mb-4">
            <button type="button" class="btn btn-outline-primary btn-lg w-100">
                <i class="fa-solid fa-clipboard"></i> Stock Count: <?php echo $stock_count; ?>
            </button>
        </div>
    </div>

    <!-- Add Stock Form -->
    <div class="modal fade" id="addStock" tabindex="-1" aria-labelledby="addStockLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="post" action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addStockLabel">Add Stock</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="itemName" class="form-label">Item Name</label>
                            <select class="form-control" id="itemName" name="itemName" onchange="toggleItemInput()">
                                <option value="">Select Item</option>
                                <?php foreach ($items as $item): ?>
                                    <option value="<?php echo htmlspecialchars($item); ?>">
                                        <?php echo htmlspecialchars($item); ?>
                                    </option>
                                <?php endforeach; ?>
                                <option value="Other">Other</option>
                            </select>
                            <input type="text" class="form-control mt-2" id="customItemName" name="customItemName"
                                style="display:none;" placeholder="Enter new item name">
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-control" id="category" name="category" onchange="toggleCategoryInput()">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>">
                                        <?php echo htmlspecialchars($cat); ?>
                                    </option>
                                <?php endforeach; ?>
                                <option value="Other">Other</option>
                            </select>
                            <input type="text" class="form-control mt-2" id="customCategory" name="customCategory"
                                style="display:none;" placeholder="Enter new category">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Create for:</label>
                            <select class="form-select" id="created_for" name="created_for" required>
                                <option>Select status</option>
                                <?php
                                $result = $conn->query("SELECT user_name FROM users WHERE user_type = 'Factory'");
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option>" . $row['user_name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" min="0" class="form-control" id="quantity" name="quantity" required>
                        </div>
                        <div class="mb-3">
                            <label for="value" class="form-label">Value (₹)</label>
                            <input type="number" min="0" step="0.01" class="form-control" id="value" name="value"
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
                        <button type="submit" class="btn btn-primary" name="addStockSubmit">Add Stock</button>
                    </div>
                </form>
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

    <!-- Table -->
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
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Value</th>
                        <?php if ($selectedEmail === 'all'): ?>
                            <th>Factory</th>
                        <?php endif; ?>
                        <th>Status</th>
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
                                    <td>" . htmlspecialchars($row['item_name']) . "</td>
                                    <td>" . htmlspecialchars($row['category']) . "</td>
                                    <td>" . htmlspecialchars($row['quantity']) . "</td>
                                    <td>₹" . number_format($row['value'], 2) . "</td>";
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
                            echo "</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='" . ($selectedEmail === 'all' ? '7' : '6') . "'>No stock data found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Check low stock -->
    <?php
    $low_stock_items = [];
    $query = $conn->prepare("SELECT item_name, quantity, status, created_for FROM factory_stock WHERE status IN ('Low Stock', 'Out of Stock') $factoryFilter");
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
                    <span class="<?php echo $item['level'] === 'Critical' ? 'text-danger' : 'text-warning'; ?> font-medium">
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