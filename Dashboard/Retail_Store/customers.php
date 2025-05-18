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

    if ($_POST['whatAction'] === 'add_customer') {
        // Collect data for transaction
        $Name = clean($_POST['name']);
        $type = clean($_POST['type']);
        $contact = clean($_POST['contact']);
        $current_date = date('Y-m-d');

        // Validate data for transaction
        $allowedType = ['Contractor', 'Retail', 'Wholesale'];
        if (!in_array($type, $allowedType)) {
            header("Location: admin_dashboard.php?page=retail_store");
            echo json_encode(["success" => false, "message" => "Invalid type"]);
            exit;
        }

        // Start database transaction
        $conn->begin_transaction();

        try {
            // Generate a new transaction ID
            $result = $conn->query("SELECT customer_Id FROM customer ORDER BY CAST(SUBSTRING(customer_Id, 6) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");

            if ($result && $row = $result->fetch_assoc()) {
                $lastId = $row['customer_Id']; // e.g. SL-005
                $num = (int) substr($lastId, 5);   // get "005" → 5
                $newNum = $num + 1;
            } else {
                $newNum = 1;
            }

            $newCustomerId = 'CUST-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);

            // Insert the transaction record
            $stmt = $conn->prepare("INSERT INTO customer 
                (customer_Id, name, type, contact, date, created_by, created_for) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param("sssssss", $newCustomerId, $Name, $type, $contact, $current_date, $user_name, $user_name);
            $stmt->execute();

            $conn->commit();
            $stmt->close();

            header("Location: store_dashboard.php?page=customers&tab=all");
            exit;

        } catch (Exception $e) {
            $conn->rollback();
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Customer entry failed: " . $e->getMessage()
            ]);
            exit;
        }
    }
}

// Include mock database
require_once 'database.php';

// Get data from database
// $customers = get_customers();
// $customer_segmentation = get_customer_segmentation();
// $top_spending_categories = get_top_spending_categories();
// $recent_activity = get_recent_activity();

// Handle actions
$success_message = '';
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $customer_name = isset($_POST['customer_name']) ? $_POST['customer_name'] : '';
        switch ($action) {
            case 'add_customer':
                $success_message = 'Add New Customer operation initiated successfully.';
                break;
            case 'export_customers':
                $success_message = 'Export Customer Data operation initiated successfully.';
                break;
            case 'filter_type':
                $success_message = 'Filter by Type operation initiated successfully.';
                break;
            case 'filter_status':
                $success_message = 'Filter by Status operation initiated successfully.';
                break;
            case 'filter_purchase_date':
                $success_message = 'Filter by Purchase Date operation initiated successfully.';
                break;
            case 'filter_spend':
                $success_message = 'Filter by Spend Amount operation initiated successfully.';
                break;
            case 'view_customer':
                $success_message = "View $customer_name operation initiated successfully.";
                break;
            case 'edit_customer':
                $success_message = "Edit $customer_name operation initiated successfully.";
                break;
            case 'new_sale':
                $success_message = "New Sale for $customer_name operation initiated successfully.";
                break;
            case 'view_all_activity':
                $success_message = 'View All Activity operation initiated successfully.';
                break;
        }
    }
}

// Search and filter parameters
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$tab = isset($_GET['tab']) && in_array($_GET['tab'], ['all', 'retail', 'wholesale', 'contractor']) ? $_GET['tab'] : 'all';

// Filter customers
$filtered_customers = array_filter($customers, function ($customer) use ($search_query, $tab) {
    $matches_search = empty($search_query) ||
        stripos($customer['id'], $search_query) !== false ||
        stripos($customer['name'], $search_query) !== false ||
        stripos($customer['email'], $search_query) !== false ||
        stripos($customer['phone'], $search_query) !== false;
    $matches_tab = $tab === 'all' ||
        ($tab === 'retail' && $customer['type'] === 'Retail') ||
        ($tab === 'wholesale' && $customer['type'] === 'Wholesale') ||
        ($tab === 'contractor' && $customer['type'] === 'Contractor');
    return $matches_search && $matches_tab;
});

// Status badge function
function get_status_badge($status)
{
    $status_config = [
        'Active' => ['class' => 'bg-green-subtle text-green', 'label' => 'Active'],
        'Inactive' => ['class' => 'bg-gray-subtle text-gray', 'label' => 'Inactive']
    ];
    $config = isset($status_config[$status]) ? $status_config[$status] : $status_config['Active'];
    return "<span class='badge {$config['class']}'>{$config['label']}</span>";
}

// Type badge function
function get_type_badge($type)
{
    $type_config = [
        'Retail' => ['class' => 'bg-primary-subtle text-primary', 'label' => 'Retail'],
        'Wholesale' => ['class' => 'bg-secondary-subtle text-secondary', 'label' => 'Wholesale'],
        'Contractor' => ['class' => 'bg-info-subtle text-info', 'label' => 'Contractor']
    ];
    $config = isset($type_config[$type]) ? $type_config[$type] : $type_config['Retail'];
    return "<span class='badge {$config['class']}'>{$config['label']}</span>";
}
?>

<div class="main-content">
    <h1><i class="fas fa-users text-primary me-2"></i> Customer Management</h1>
    <p class="text-muted">Manage and track your store customers</p>

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
            <form method="GET" action="?page=customers" class="d-flex align-items-center gap-2">
                <input type="hidden" name="page" value="customers">
                <input type="hidden" name="tab" value="<?php echo htmlspecialchars($tab); ?>">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" id="searchInput"
                        placeholder="Search customers..." value="<?php echo htmlspecialchars($search_query); ?>">
                </div>
            </form>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="submit" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCustomer">
                <i class="fas fa-user-plus me-1"></i> Add Customer
            </button>

            <button type="submit" class="btn btn-outline-primary btn-sm" onclick="exportTableToCSV()">
                <i class="fas fa-download me-1"></i> Export
            </button>
        </div>
    </div>

    <?php

    date_default_timezone_set("Asia/Kolkata");
    $current_month = date('m');
    $current_year = date('Y');

    // Calculate last month and year correctly (handle January case)
    if ($current_month == 1) {
        $last_month = 12;
        $last_year = $current_year - 1;
    } else {
        $last_month = $current_month - 1;
        $last_year = $current_year;
    }

    // Helper function to calculate percentage change
    function percent_change($old, $new)
    {
        if ($old == 0) {
            return $new == 0 ? 0 : 100; // Avoid division by zero
        }
        return round((($new - $old) / $old) * 100, 1);
    }

    // ---------------- Total Customers ----------------
// Current month total customers (created_for filter)
    $totalCustomersQuery = "SELECT COUNT(*) AS total FROM customer WHERE created_for = ?";
    $stmt1 = $conn->prepare($totalCustomersQuery);
    $stmt1->bind_param("s", $user_name);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $currentTotalCustomers = $result1->fetch_assoc()['total'] ?? 0;
    $stmt1->close();

    // Last month total customers
    $lastMonthCustomersQuery = "SELECT COUNT(*) AS total FROM customer WHERE created_for = ? AND MONTH(date) = ? AND YEAR(date) = ?";
    $stmt2 = $conn->prepare($lastMonthCustomersQuery);
    $stmt2->bind_param("sii", $user_name, $last_month, $last_year);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $lastTotalCustomers = $result2->fetch_assoc()['total'] ?? 0;
    $stmt2->close();

    $customer_analytics['totalCustomers'] = $currentTotalCustomers;
    $customer_analytics['totalCustomersChange'] = percent_change($lastTotalCustomers, $currentTotalCustomers);

    // -------------- New This Month -----------------
// Current month new customers
    $newThisMonthQuery = "SELECT COUNT(*) AS total FROM customer WHERE created_for = ? AND MONTH(date) = ? AND YEAR(date) = ?";
    $stmt = $conn->prepare($newThisMonthQuery);
    $stmt->bind_param("sii", $user_name, $current_month, $current_year);
    $stmt->execute();
    $currentNewThisMonth = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

    // Last month new customers
    $stmt = $conn->prepare($newThisMonthQuery);
    $stmt->bind_param("sii", $user_name, $last_month, $last_year);
    $stmt->execute();
    $lastNewThisMonth = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

    $customer_analytics['newThisMonth'] = $currentNewThisMonth;
    $customer_analytics['newThisMonthChange'] = percent_change($lastNewThisMonth, $currentNewThisMonth);

    // -------------- Average Purchase -----------------
// Current month avg purchase from invoice
    $avgPurchaseQuery = "SELECT AVG(grand_total) AS avg FROM invoice WHERE created_for = ? AND MONTH(date) = ? AND YEAR(date) = ?";
    $stmt = $conn->prepare($avgPurchaseQuery);
    $stmt->bind_param("sii", $user_name, $current_month, $current_year);
    $stmt->execute();
    $currentAvgPurchase = $stmt->get_result()->fetch_assoc()['avg'] ?? 0;

    // Last month avg purchase
    $stmt = $conn->prepare($avgPurchaseQuery);
    $stmt->bind_param("sii", $user_name, $last_month, $last_year);
    $stmt->execute();
    $lastAvgPurchase = $stmt->get_result()->fetch_assoc()['avg'] ?? 0;

    $customer_analytics['avgPurchase'] = round($currentAvgPurchase);
    $customer_analytics['avgPurchaseChange'] = percent_change($lastAvgPurchase, $currentAvgPurchase);

    // -------------- Repeat Rate -----------------
// Current month repeat rate calculation
    $repeatRateQuery = "SELECT customer_name, COUNT(*) AS purchases FROM invoice WHERE created_for = ? AND MONTH(date) = ? AND YEAR(date) = ? GROUP BY customer_name";
    $stmt = $conn->prepare($repeatRateQuery);
    $stmt->bind_param("sii", $user_name, $current_month, $current_year);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_customers = 0;
    $total_purchases = 0;
    while ($row = $result->fetch_assoc()) {
        $total_customers++;
        $total_purchases += $row['purchases'];
    }
    $currentRepeatRate = $total_customers > 0 ? ($total_purchases / $total_customers) * 100 : 0;

    // Last month repeat rate calculation
    $stmt = $conn->prepare($repeatRateQuery);
    $stmt->bind_param("sii", $user_name, $last_month, $last_year);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_customers_last = 0;
    $total_purchases_last = 0;
    while ($row = $result->fetch_assoc()) {
        $total_customers_last++;
        $total_purchases_last += $row['purchases'];
    }
    $lastRepeatRate = $total_customers_last > 0 ? ($total_purchases_last / $total_customers_last) * 100 : 0;

    $customer_analytics['repeatRate'] = round($currentRepeatRate);
    $customer_analytics['repeatRateChange'] = percent_change($lastRepeatRate, $currentRepeatRate);

    ?>


    <!-- Customer Analytics -->
    <div class="row row-cols-1 row-cols-md-4 g-4 mb-4">
        <div class="col">
            <div class="card stat-card card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Total Customers</h6>
                            <h3 class="fw-bold"><?php echo htmlspecialchars($customer_analytics['totalCustomers']); ?>
                            </h3>
                            <p class="text-xs text-muted mt-1"><?php
                            $change = $customer_analytics['totalCustomersChange'];
                            $sign = ($change >= 0) ? '+' : '';
                            echo $sign . $change . '% from last month';
                            ?></p>
                        </div>
                        <i class="fas fa-users fa-2x text-primary opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card card-border shadow-sm" style="border-left: 5px solid #198754;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">New This Month</h6>
                            <h3 class="fw-bold"><?php echo htmlspecialchars($customer_analytics['newThisMonth']); ?>
                            </h3>
                            <p class="text-xs text-muted mt-1"><?php
                            $change = $customer_analytics['newThisMonthChange'];
                            $sign = ($change >= 0) ? '+' : '';
                            echo $sign . $change . '% from last month';
                            ?></p>
                        </div>
                        <i class="fas fa-user-plus fa-2x text-success opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Avg. Purchase</h6>
                            <h3 class="fw-bold">₹<?php echo number_format($customer_analytics['avgPurchase'], 0); ?>
                            </h3>
                            <p class="text-xs text-muted mt-1"><?php
                            $change = $customer_analytics['avgPurchaseChange'];
                            $sign = ($change >= 0) ? '+' : '';
                            echo $sign . $change . '% from last month';
                            ?></p>
                        </div>
                        <i class="fas fa-rupee-sign fa-2x text-purple opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card card-border shadow-sm" style="border-left: 5px solid #ffc107;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Repeat Rate</h6>
                            <h3 class="fw-bold"><?php echo htmlspecialchars($customer_analytics['repeatRate']); ?>%</h3>
                            <p class="text-xs text-muted mt-1"><?php
                            $change = $customer_analytics['repeatRateChange'];
                            $sign = ($change >= 0) ? '+' : '';
                            echo $sign . $change . '% from last month';
                            ?></p>
                        </div>
                        <i class="fas fa-clock fa-2x text-warning opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs and Filters -->
    <div class="card card-border shadow-sm mb-4">
        <div class="card-body p-4">
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a href="?page=customers&tab=all" class="nav-link <?php echo $tab === 'all' ? 'active' : ''; ?>">All
                        Customers</a>
                </li>
                <li class="nav-item">
                    <a href="?page=customers&tab=retail"
                        class="nav-link <?php echo $tab === 'retail' ? 'active' : ''; ?>">Retail</a>
                </li>
                <li class="nav-item">
                    <a href="?page=customers&tab=wholesale"
                        class="nav-link <?php echo $tab === 'wholesale' ? 'active' : ''; ?>">Wholesale</a>
                </li>
                <li class="nav-item">
                    <a href="?page=customers&tab=contractor"
                        class="nav-link <?php echo $tab === 'contractor' ? 'active' : ''; ?>">Contractor</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Customer Tables -->
    <?php if ($tab === 'all'): ?>
        <div class="card card-border shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="mb-3">Customer Database</h5>
                <p class="text-muted mb-3">View and manage all your customers</p>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="customerTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Contact</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            // Fetch transactions from the database
                            $result = $conn->query("SELECT * FROM customer WHERE created_for = '$user_name' ORDER BY customer_Id DESC");

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['customer_Id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['contact']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>No transactions found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                     <script>
                     // Search Functionality
                        document.getElementById('searchInput').addEventListener('input', function () {
                            const searchText = this.value.toLowerCase();
                            const rows = document.querySelectorAll('#customerTable tbody tr');

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
                            const rows = document.querySelectorAll("#customerTable tr");
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
    <?php elseif ($tab === 'retail' || $tab === 'wholesale' || $tab === 'contractor'): ?>
        <div class="card card-border shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="mb-3"><?php echo htmlspecialchars(ucfirst($tab)); ?> Customers</h5>
                <p class="text-muted mb-3">Manage <?php echo htmlspecialchars($tab); ?> customers</p>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="retailTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Contact</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            // Fetch transactions from the database
                            $result = $conn->query("SELECT * FROM customer WHERE created_for = '$user_name' AND type = '$tab' ORDER BY customer_Id DESC");

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['customer_Id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['contact']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>No transactions found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                     <script>
                     // Search Functionality
                        document.getElementById('searchInput').addEventListener('input', function () {
                            const searchText = this.value.toLowerCase();
                            const rows = document.querySelectorAll('#retailTable tbody tr');

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
                            const rows = document.querySelectorAll("#retailTable tr");
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

    <?php
    // Assume $conn = MySQLi connection object
// Assume $current_user contains logged-in user identifier
    
    // 1) Customer Segmentation (percentage of each type)
    $customer_segmentation = [];
    $total_customers_query = "SELECT COUNT(*) as total FROM customer WHERE created_for = ?";
    $stmt = $conn->prepare($total_customers_query);
    $stmt->bind_param("s", $user_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_customers = $result->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    if ($total_customers > 0) {
        $seg_query = "SELECT type, COUNT(*) as count FROM customer WHERE created_for = ? GROUP BY type";
        $stmt = $conn->prepare($seg_query);
        $stmt->bind_param("s", $user_name);
        $stmt->execute();
        $res = $stmt->get_result();

        while ($row = $res->fetch_assoc()) {
            $percentage = round(($row['count'] / $total_customers) * 100, 2);
            $customer_segmentation[] = [
                'type' => $row['type'],
                'percentage' => $percentage
            ];
        }
        $stmt->close();
    }

    // 2) Top Spending Categories (percentage of total sales amount per category)
    
    $top_spending_categories = [];

    $category_sales_query = "
    SELECT i.category, SUM(inv.grand_total) AS total_sales
    FROM invoice inv
    JOIN products i ON inv.item_name = i.name
    WHERE inv.created_for = ?
    GROUP BY i.category
    ORDER BY total_sales DESC
";

    $stmt = $conn->prepare($category_sales_query);
    $stmt->bind_param("s", $user_name);
    $stmt->execute();
    $res = $stmt->get_result();

    $total_sales_all = 0;
    $category_sales = [];
    while ($row = $res->fetch_assoc()) {
        $category_sales[$row['category']] = $row['total_sales'];
        $total_sales_all += $row['total_sales'];
    }
    $stmt->close();

    if ($total_sales_all > 0) {
        foreach ($category_sales as $category => $sales) {
            $percentage = round(($sales / $total_sales_all) * 100, 2);
            $top_spending_categories[] = [
                'category' => $category,
                'percentage' => $percentage
            ];
        }
    }
    ?>


    <!-- Customer Analytics -->
    <div class="row row-cols-1 row-cols-lg-2 g-4 mb-4">
        <div class="col">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Customer Segmentation</h5>
                    <div class="row row-cols-1 row-cols-md-2 g-4 mt-4">
                        <div class="col">
                            <h6 class="text-sm font-medium mb-2">Customer Types</h6>
                            <div class="space-y-2">
                                <?php foreach ($customer_segmentation as $segment): ?>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-sm"><?php echo htmlspecialchars($segment['type']); ?></span>
                                        <span
                                            class="text-sm font-medium"><?php echo htmlspecialchars($segment['percentage']); ?>%</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="col">
                            <h6 class="text-sm font-medium mb-2">Top Spending Categories</h6>
                            <div class="space-y-2">
                                <?php foreach ($top_spending_categories as $category): ?>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-sm"><?php echo htmlspecialchars($category['category']); ?></span>
                                        <span
                                            class="text-sm font-medium"><?php echo htmlspecialchars($category['percentage']); ?>%</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php

        $top_customers = [];

        // 1. Fetch customers for the current user
        $customer_query = $conn->prepare("SELECT customer_Id, name, type FROM customer WHERE created_for = ?");
        $customer_query->bind_param("s", $user_name);
        $customer_query->execute();
        $customer_result = $customer_query->get_result();

        while ($customer = $customer_result->fetch_assoc()) {
            // 2. Get totalSpent from invoice table for this customer
            $name = $customer['name'];

            $invoice_query = $conn->prepare("SELECT SUM(grand_total) AS totalSpent FROM invoice WHERE customer_name = ? AND created_for = ?");
            $invoice_query->bind_param("ss", $name, $user_name);
            $invoice_query->execute();
            $invoice_result = $invoice_query->get_result();
            $invoice_data = $invoice_result->fetch_assoc();

            $customer['totalSpent'] = (float) $invoice_data['totalSpent'];
            $top_customers[] = $customer;
        }
        ?>

        <?php
        // Sort by totalSpent and get top 5
        usort($top_customers, function ($a, $b) {
            return $b['totalSpent'] <=> $a['totalSpent'];
        });
        $top_customers = array_slice($top_customers, 0, 5);
        ?>


        <div class="col">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Top Customers</h5>
                    <div class="space-y-3">
                        <?php foreach ($top_customers as $index => $customer): ?>
                            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                <div class="d-flex align-items-center gap-3">
                                    <div
                                        class="d-flex align-items-center justify-content-center w-8 h-8 rounded-circle bg-primary text-white font-medium">
                                        <?php echo $index + 1; ?>
                                    </div>
                                    <div>
                                        <p class="font-medium"><?php echo htmlspecialchars($customer['name']); ?></p>
                                        <p class="text-xs text-muted"><?php echo htmlspecialchars($customer['type']); ?></p>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <p class="font-bold">₹<?php echo number_format($customer['totalSpent'], 0); ?></p>
                                    <p class="text-xs text-muted">Lifetime Value</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Add Customer Form -->
    <div class="modal fade" id="addCustomer" tabindex="-1" aria-labelledby="addCustomerLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="customers.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCustomerLabel">Add Customer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">

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
                            <button type="submit" class="btn btn-primary" name="whatAction" value="add_customer">Save
                                Customer</button>
                        </div>
                </form>
            </div>
        </div>
    </div>

</div>

<style>
    .space-y-2>*+* {
        margin-top: 0.5rem;
    }

    .space-y-3>*+* {
        margin-top: 0.75rem;
    }

    .text-sm {
        font-size: 0.875rem;
    }

    .text-xs {
        font-size: 0.75rem;
    }

    .font-medium {
        font-weight: 500;
    }

    .font-bold {
        font-weight: 700;
    }

    .w-8 {
        width: 2rem;
    }

    .h-8 {
        height: 2rem;
    }

    .bg-green-subtle {
        background-color: #d4edda !important;
    }

    .text-green {
        color: #155724 !important;
    }

    .bg-gray-subtle {
        background-color: #e2e3e5 !important;
    }

    .text-gray {
        color: #41464b !important;
    }

    .bg-primary-subtle {
        background-color: #cfe2ff !important;
    }

    .text-primary {
        color: #0d6efd !important;
    }

    .bg-secondary-subtle {
        background-color: #e2e3e5 !important;
    }

    .text-secondary {
        color: #6c757d !important;
    }

    .bg-info-subtle {
        background-color: #cff4fc !important;
    }

    .text-info {
        color: #0dcaf0 !important;
    }

    .bg-purple-subtle {
        background-color: #e2d9f3 !important;
    }

    .text-purple {
        color: #6f42c1 !important;
    }
</style>