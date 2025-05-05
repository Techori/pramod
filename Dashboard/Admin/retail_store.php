<?php
include '../../_conn.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['whatAction'])) {

    // Clean input data function
    function clean($input)
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    // Transaction action
    if ($_POST['whatAction'] === 'Sales') {
        // Collect data for transaction
        $date = clean($_POST['date']);
        $customerName = clean($_POST['customerName']);
        $category = clean($_POST['category']);
        $Item = clean($_POST['item']);
        $amount = floatval($_POST['amount']);
        $payment_method = clean($_POST['Payment_Method']);
        $status = clean($_POST['Status']);

        // Validate data for transaction
        $allowedCategory = ['Wires and Cables', 'Switches and Sockets', 'Lighting', 'Fans', 'MCBs and DBs', 'Accessories'];
        $allowedStatus = ['Completed', 'Pending'];
        $allowedPayments = ['Bank Transfer', 'Cash', 'UPI', 'Cheque', 'Card'];
        if (!in_array($status, $allowedStatus) || !in_array($payment_method, $allowedPayments) || !in_array($category, $allowedCategory)) {
            header("Location: admin_dashboard.php?page=retail_store");
            echo json_encode(["success" => false, "message" => "Invalid status, payment method or category"]);
            exit;
        }

        // Start database transaction
        $conn->begin_transaction();

        try {
            // Generate a new transaction ID
            $result = $conn->query("SELECT Sales_Id FROM sales ORDER BY CAST(SUBSTRING(Sales_Id, 5) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");

            if ($result && $row = $result->fetch_assoc()) {
                $lastId = $row['Sales_Id']; // e.g. SL-005
                $num = (int) substr($lastId, 4);   // get "005" → 5
                $newNum = $num + 1;
            } else {
                $newNum = 1;
            }

            $newSalesId = 'SL-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);

            // Insert the transaction record
            $stmt = $conn->prepare("INSERT INTO sales 
                (Sales_Id, Date, Customer_Name, Item, Category, Amount, Status, payment_method) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param("sssisdss", $newSalesId, $date, $customerName, $Item, $category, $amount, $status, $payment_method);
            $stmt->execute();

            $conn->commit();
            $stmt->close();

            header("Location: admin_dashboard.php?page=retail_store");
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

    } else if ($_POST['whatAction'] === 'add_customer') {
        // Collect data for transaction
        $Name = clean($_POST['name']);
        $type = clean($_POST['type']);
        $contact = clean($_POST['contact']);
        $purchases = clean($_POST['purchases']);
        $total_spent = clean($_POST['total_spent']);
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
                (customer_Id, name, type, contact, purchases, total_spent, date) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param("ssssids", $newCustomerId, $Name, $type, $contact, $purchases, $total_spent, $current_date);
            $stmt->execute();

            $conn->commit();
            $stmt->close();

            header("Location: admin_dashboard.php?page=retail_store");
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
    } else {
        // Invalid action
        echo json_encode(["success" => false, "message" => "Invalid action"]);
        exit;
    }

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

    .progress {
        height: 10px;
    }

    .alert-card {
        border-radius: 10px;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
    }

    .stock-label {
        font-weight: 500;
    }

    .stock-count {
        font-size: 0.9rem;
        font-weight: 600;
    }

    .tabs {
        display: flex;
        border-bottom: 2px solid #ddd;
    }

    .retailStoreTab {
        padding: 10px 20px;
        cursor: pointer;
        border: none;
        background: none;
        font-size: 16px;
    }

    .retailStoreTab.active {
        border-bottom: 3px solid #007bff;
        font-weight: bold;
        color: #007bff;
    }

    .retailStore-tab-content {
        display: none;
        padding: 20px 0;
    }

    .retailStore-tab-content.active {
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

    mark.search-highlight {
        background-color: yellow;
        color: black;
        padding: 0;
        border-radius: 2px;
    }
</style>

<h1>Retail Store Dashboard</h1>
<p>Monitor retail store performance and sales</p>

<!-- Search bar & buttons -->
<div class="container-fluid d-flex justify-content-between align-items-center mb-4">


    <div class="d-flex justify-content-start">
        <div class="input-group w-100 me-2">
            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
            <input type="text" id="globalSearch" class="form-control border-start-0"
                placeholder="Search this page..." />
        </div>

    </div>

    <div class="justify-contnt-end">
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newSales"><i
                class="fa-solid fa-cart-plus"></i> New Sales</button>
        <a href="?page=billing_desk" class="btn btn-outline-primary"><i class="fa-solid fa-file-lines"></i> Billing</a>
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




<!-- To get the todays sales -->
<?php

// Get today's date in Y-m-d format
$today = date('Y-m-d');

// Prepare and execute SQL query to get today's sales summary
$sql = "SELECT 
    SUM(Amount) AS total_sales,
    COUNT(*) AS total_transactions,
    SUM(Item) AS total_items_sold
FROM sales
WHERE date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Handle nulls
$total_sales = $data['total_sales'] ?? 0;
$total_transactions = $data['total_transactions'] ?? 0;
$total_items_sold = $data['total_items_sold'] ?? 0;
$stmt->close();

// Format amount in ₹
$total_sales_formatted = "₹" . number_format($total_sales, 2);

// Prepare and execute SQL query to get today's customer count
$sql = "SELECT 
    COUNT(*) AS total_customers
FROM customer
WHERE date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Handle nulls
$total_customers = $data['total_customers'] ?? 0;
$stmt->close();
?>

<?php
$last_month_same_day = date('Y-m-d', strtotime('-1 month'));
// Last month's data from 'sales' table
$sql = "SELECT 
    SUM(Amount) AS last_month_sales,
    SUM(Item) AS last_month_items
FROM sales
WHERE date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $last_month_same_day);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$last_month_sales = $data['last_month_sales'] ?? 0;
$last_month_items = $data['last_month_items'] ?? 0;
$stmt->close();

// Last month's customers from 'customer' table
$sql = "SELECT 
    COUNT(*) AS last_month_customers
FROM customer
WHERE date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $last_month_same_day);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$last_month_customers = $data['last_month_customers'] ?? 0;
$stmt->close();

// Calculate percentage change
function calculateChangeInfo($todayValue, $lastMonthValue)
{
    if ($lastMonthValue == 0) {
        return ['text' => 'N/A', 'positive' => true]; // Avoid divide by zero
    }

    $change = (($todayValue - $lastMonthValue) / $lastMonthValue) * 100;
    $isPositive = $change >= 0;
    $changeText = number_format(abs($change), 1) . '%';

    return ['text' => $changeText, 'positive' => $isPositive];
}


$sales_info = calculateChangeInfo($total_sales, $last_month_sales);
$customers_info = calculateChangeInfo($total_transactions, $last_month_customers);
$items_info = calculateChangeInfo($total_items_sold, $last_month_items);


?>

<!-- Cards -->
<div class="row">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
            <div class="card-body">
                <h6 class="text-muted">Today's Sales</h6>
                <h3 class="fw-bold"><?php echo $total_sales_formatted; ?></h3>
                <p class="<?php echo $sales_info['positive'] ? 'text-success' : 'text-danger'; ?>">
                    <?php echo ($sales_info['positive'] ? '+' : '-') . $sales_info['text']; ?> vs last month
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
            <div class="card-body">
                <h6 class="text-muted">Customers Today</h6>
                <h3 class="fw-bold"><?php echo $total_customers; ?></h3>
                <p class="<?php echo $customers_info['positive'] ? 'text-success' : 'text-danger'; ?>">
                    <?php echo ($customers_info['positive'] ? '+' : '-') . $customers_info['text']; ?> vs last month
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
            <div class="card-body">
                <h6 class="text-muted">Items Sold</h6>
                <h3 class="fw-bold"><?php echo $total_items_sold; ?></h3>
                <p class="<?php echo $items_info['positive'] ? 'text-success' : 'text-danger'; ?>">
                    <?php echo ($items_info['positive'] ? '+' : '-') . $items_info['text']; ?> vs last month
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
            <div class="card-body">
                <h6 class="text-muted">Average Bill</h6>
                <h3 class="fw-bold">₹1,750</h3> <!-- Dynamic data -->
                <p class="text-danger">3.7% vs last month</p> <!-- Dynamic data -->
            </div>
        </div>
    </div>
</div>

<!-- Buttons -->
<div class="row justify-content-center">
    <div class="col-md-3 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100" data-bs-toggle="modal"
            data-bs-target="#newSales"><i class="fa-solid fa-cart-plus"></i> New
            Sales</button>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <a href="?page=inventory" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-box"></i> Check
            Inventory</a>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-percent"></i>
            Discounts</button>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-file-lines"></i> Daily
            Reports</button>
    </div>
</div>

<!-- New Sale form -->
<div class="modal fade" id="newSales" tabindex="-1" aria-labelledby="newSalesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="retail_store.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="newSalesLabel">Add Sales</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="customerName" class="form-label">Customer</label>
                        <input type="text" class="form-control" id="customerName" name="customerName" required>
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="Wires and Cables">Wires and Cables</option>
                            <option value="Swithces and Sockets">Switches and Sockets</option>
                            <option value="Lighting">Lighting</option>
                            <option value="Fans">Fans</option>
                            <option value="MCBs and DBs">MCBs and DBs</option>
                            <option value="Accessories">Accessories</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="item" class="form-label">Items</label>
                        <input type="number" class="form-control" id="item" name="item" required>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="text" class="form-control" id="amount" name="amount" required>
                    </div>

                    <div class="mb-3">
                        <label for="Payment_Method" class="form-label">Payment Method</label>
                        <select class="form-select" id="Payment_Method" name="Payment_Method" required>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cash">Cash</option>
                            <option value="UPI">UPI</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Card">Card</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="Status" class="form-label">Status</label>
                        <select class="form-select" id="Status" name="Status" required>
                            <option value="Completed">Completed</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" name="whatAction" value="Sales">Add Sales</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="chart-container">
    <div class="chart-box">
        <h3>Daily Sales (Last Week)</h3>
        <canvas id="barChart"></canvas>
    </div>
    <div class="chart-box">
        <h3>Sales by Category</h3>
        <div style="position: relative; width: 100%; max-width: 300px; margin: 0 auto;">
            <canvas id="pieChart"></canvas>
        </div>
    </div>
</div>

<!-- Popular products -->
<div class="col-md-12 my-4">
    <div class="card p-3 shadow-sm">
        <h5 class="mb-4">
            <strong>Popular Products</strong>
        </h5>
        <div class="row">
            <div class="col-md-4 col-sm-12 mb-2">
                <div class="card stat-card cards shadow-sm" style="background-color:rgb(125, 206, 246);">
                    <div class="card-body">
                        <h5 class="text-muted">Havells Wire</h5>
                        <p>₹65 per meter</p> <!-- Dynamic data -->
                        <p>580 units sold this month</p> <!-- Dynamic data -->
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-12 mb-2">
                <div class="card stat-card cards shadow-sm" style="background-color:rgb(225, 185, 252);">
                    <div class="card-body">
                        <h5 class="text-muted">LED Bulb 9W</h5>
                        <p>₹120 per unit</p> <!-- Dynamic data -->
                        <p>425 units sold this month</p> <!-- Dynamic data -->
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-12 mb-2">
                <div class="card stat-card cards shadow-sm" style="background-color:rgb(248, 249, 165);">
                    <div class="card-body">
                        <h5 class="text-muted">Switch Board</h5>
                        <p>₹350 per unit</p> <!-- Dynamic data -->
                        <p>320 units sold this month</p> <!-- Dynamic data -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Low stock alert bars -->
<div class="card">
    <div class="alert-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><i class="fa-solid fa-circle-exclamation"></i> Low Stock Alerts</h5>
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#requestStock">Request
                Stock</button>
        </div>

        <!-- Havells Wires -->
        <div class="mb-3">
            <div class="d-flex justify-content-between">
                <span class="stock-label">Havells Wires (1.5 mm)</span>
                <span class="text-danger stock-count">12 units left</span> <!-- Dynamic data -->
            </div>
            <div class="progress bg-light">
                <div class="progress-bar bg-primary" style="width: 25%"></div> <!-- Dynamic data -->
                <div class="progress-bar bg-danger" style="width: 75%"></div> <!-- Dynamic data -->
            </div>
        </div>

        <!-- MCB Switches -->
        <div class="mb-3">
            <div class="d-flex justify-content-between">
                <span class="stock-label">MCB Switches (32A)</span>
                <span class="text-warning stock-count">24 units left</span> <!-- Dynamic data -->
            </div>
            <div class="progress bg-light">
                <div class="progress-bar bg-primary" style="width: 60%"></div> <!-- Dynamic data -->
                <div class="progress-bar bg-warning" style="width: 40%"></div> <!-- Dynamic data -->
            </div>
        </div>

        <!-- Ceiling Fan -->
        <div>
            <div class="d-flex justify-content-between">
                <span class="stock-label">Ceiling Fan (48 inch)</span>
                <span class="text-warning stock-count">18 units left</span> <!-- Dynamic data -->
            </div>
            <div class="progress bg-light">
                <div class="progress-bar bg-primary" style="width: 70%"></div> <!-- Dynamic data -->
                <div class="progress-bar bg-warning" style="width: 30%"></div> <!-- Dynamic data -->
            </div>
        </div>
    </div>
</div>

<!-- Request Stock Form -->
<div class="modal fade" id="requestStock" tabindex="-1" aria-labelledby="requestStockLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h5 class="modal-title" id="requestStockLabel">Request Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="request_to" class="form-label">Request to</label>
                        <input type="text" class="form-control" id="request_to">
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name">
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" class="form-control" id="category">
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
                    <button type="submit" class="btn btn-primary">Request Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Recent sales table -->
<div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">

    <div id="facrtory">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Recent Sales</h1>
            </div>

            <div class="justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0 table-search" data-table="recent_sales_table"
                        placeholder="Search..." />
                </div>
            </div>

            <div class="justify-content-end">
                <button class="btn btn-outline-primary"><i class="fa-solid fa-print"></i> Print</button>
                <button class="btn btn-outline-primary"><i class="fa-solid fa-download"></i> Export</button>
            </div>

        </div>
        <table id="recent_sales_table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>INV-2025-001</td> <!-- Dynamic data -->
                    <td>Raj Kumar</td> <!-- Dynamic data -->
                    <td>13 Apr, 2025</td> <!-- Dynamic data -->
                    <td>3</td> <!-- Dynamic data -->
                    <td>₹5,850</td> <!-- Dynamic data -->
                    <td>Cash</td> <!-- Dynamic data -->
                    <td>Completed</td> <!-- Dynamic data -->
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm"><i
                                    class="fa-solid fa-magnifying-glass"></i></button> <!-- View button -->
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-print"></i></button>
                            <!-- Printing button -->
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i></button>
                            <!-- Download button -->

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Tabels -->
<div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">
    <h4><i class="fa-solid fa-shop"></i> Retail Store Management</h4>
    <p>Create, track, and manage invoices and payments</p>

    <div class="tabs">
        <button class="retailStoreTab active" onclick="showRetailStoreTab('sales')">Sales</button>
        <button class="retailStoreTab" onclick="showRetailStoreTab('inventory')">Inventory</button>
        <button class="retailStoreTab" onclick="showRetailStoreTab('customers')">Customers</button>
    </div>

    <!-- Sales -->
    <div id="sales" class="retailStore-tab-content active">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="d-flex justify-content-start">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0 table-search" data-table="sales_table"
                        placeholder="Search..." />
                </div>
                <button class="btn btn-outline-primary status-filter me-2" data-type="Completed"
                    data-table="sales_table">Completed</button>
                <button class="btn btn-outline-primary status-filter me-2" data-type="Pending"
                    data-table="sales_table">Pending</button>
                <button class="btn btn-outline-danger reset-filters me-2" data-table="sales_table">Remove
                    Filters</button>
            </div>

            <div class="justify-content-end">
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newSales"><i
                        class="fa-solid fa-cart-plus"></i> New Sale</button>
            </div>

        </div>
        <table id="sales_table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Sale ID</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Category</th>
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
                $result = $conn->query("SELECT * FROM sales ORDER BY Sales_Id DESC");

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['Sales_Id']) . "</td>";
                        echo "<td>" . date('d-M-Y', strtotime($row['Date'])) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Customer_Name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Category']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Item']) . "</td>";
                        echo "<td>₹" . number_format($row['Amount'], 2) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Payment_Method']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Status']) . "</td>";
                        echo '<td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-eye"></i></button>
                                    <!-- View button -->
                                    <button class="btn btn-outline-primary btn-sm"><i
                                            class="fa-regular fa-file-lines"></i></button> <!-- Print button -->
                                    <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i></button>
                                    <!-- Download button -->

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

        <div class="row">
            <div class="col-md-4 col-sm-12 mb-2">
                <div class="card stat-card cards shadow-sm">
                    <div class="card-body">
                        <h5 class="text-muted"><i class="fa-regular fa-credit-card"></i> Today's Sales</h5>
                        <p><?php echo $total_sales_formatted; ?></p>
                        <p><?php echo $total_transactions; ?> transactions</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-12 mb-2">
                <div class="card stat-card cards shadow-sm">
                    <div class="card-body">
                        <h5 class="text-muted"><i class="fa-solid fa-box"></i> Items Sold (Today)</h5>
                        <p><?php echo $total_items_sold; ?></p>
                        <p>Across 6 categories</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-12 mb-2">
                <div class="card stat-card cards shadow-sm">
                    <div class="card-body">
                        <h5 class="text-muted"><i class="fa-regular fa-user"></i> New Customers</h5>
                        <p><?php echo $total_customers; ?></p>
                        <p>Today</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory -->
    <div id="inventory" class="retailStore-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="d-flex justify-content-start">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0 table-search" data-table="inventory_table"
                        placeholder="Search..." />
                </div>
            </div>

            <div class="justify-content-end">
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#requestStock"><i
                        class="fa-solid fa-box"></i> Request Stock</button>
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addProductAtStore"><i
                        class="fa-solid fa-plus"></i> Add Product</button>
            </div>

        </div>
        <table id="inventory_table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Price</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>STR-001</td> <!-- Dynamic data -->
                    <td>Copper Wire (2.5mm)</td> <!-- Dynamic data -->
                    <td>Wires</td> <!-- Dynamic data -->
                    <td>680</td> <!-- Dynamic data -->
                    <td>₹85/m</td> <!-- Dynamic data -->
                    <td>Shelf A1</td> <!-- Dynamic data -->
                    <td>Completed</td> <!-- Dynamic data --> <!-- Dynamic data -->
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm">Update Stock</button> <!-- Update button -->
                            <button class="btn btn-outline-primary btn-sm"><i
                                    class="fa-regular fa-pen-to-square"></i></button> <!-- Edit button -->

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- customers -->
    <div id="customers" class="retailStore-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="d-flex justify-content-start">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0 table-search" data-table="customer_table"
                        placeholder="Search..." />
                </div>
                <button class="btn btn-outline-primary customer-filter me-2" data-type="Retail"
                    data-table="customer_table">Retail</button>
                <button class="btn btn-outline-primary customer-filter me-2" data-type="Wholesale"
                    data-table="customer_table">Wholesale</button>
                <button class="btn btn-outline-primary customer-filter me-2" data-type="Contractor"
                    data-table="customer_table">Contractor</button>
                <button class="btn btn-outline-danger reset-filters me-2" data-table="customer_table">Remove
                    Filters</button>
            </div>

            <div class="justify-content-end">
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addCustomer"><i
                        class="fa-regular fa-user"></i> Add Customer</button>
            </div>

        </div>
        <table id="customer_table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Contact</th>
                    <th>Purchases</th>
                    <th>Total Spent</th>
                    <th>Actions</th>
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
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['contact']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['purchases']) . "</td>";
                        echo "<td>₹" . number_format($row['total_spent'], 2) . "</td>";
                        echo '<td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary btn-sm"><i
                                    class="fa-regular fa-pen-to-square"></i></button> <!-- Edit button -->

                                </div>
                            </td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No transactions found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Add Product Form -->
    <div class="modal fade" id="addProductAtStore" tabindex="-1" aria-labelledby="addProductAtStoreLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form>
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProductAtStoreLabel">Add Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="productId" class="form-label">Product ID</label>
                            <input type="text" class="form-control" id="productId">
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name">
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <input type="text" class="form-control" id="category">
                        </div>

                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="stock">
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="text" class="form-control" id="price">
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location">
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

    <!-- Add Customer Form -->
    <div class="modal fade" id="addCustomer" tabindex="-1" aria-labelledby="addCustomerLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="retail_store.php" method="POST">
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

                        <div class="mb-3">
                            <label for="purchases" class="form-label">Purchases</label>
                            <input type="number" class="form-control" id="purchases" name="purchases" required>
                        </div>

                        <div class="mb-3">
                            <label for="total_spent" class="form-label">Total Spent</label>
                            <input type="number" class="form-control" id="total_spent" name="total_spent" required>
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

            //  Status Filter Buttons
            document.querySelectorAll(".status-filter").forEach(button => {
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

    <!-- To get data for bar chart -->
    <?php

    // Get sales for the last 7 days
    $query = "
    SELECT 
    DATE(Date) as sale_date, 
    SUM(Amount) as total_sales 
    FROM sales 
    WHERE date >= CURDATE() - INTERVAL 6 DAY
    GROUP BY sale_date 
    ORDER BY sale_date ASC";

    $result = $conn->query($query);

    $labels = [];
    $sales = [];

    while ($row = $result->fetch_assoc()) {
        $date = date("d-M (D)", strtotime($row['sale_date'])); // e.g., 01-May (Wed)
        $labels[] = $date;
        $sales[] = $row['total_sales'];
    }
    ?>

    <!-- To get data for pie chart -->
    <?php

    // Define category list
    $categories = ['Fans', 'Lighting', 'Wires and Cables', 'Switches and Sockets', 'MCBs and DBs', 'Accessories'];

    // Initialize array to store sales for each category
    $sales = array_fill(0, count($categories), 0);

    // Fetch total sales per category in last 7 days
    $query = "
    SELECT category, SUM(amount) AS total_sales 
    FROM sales 
    WHERE date >= CURDATE() - INTERVAL 6 DAY 
    GROUP BY category";

    $result = $conn->query($query);

    // Map results to predefined categories
    while ($row = $result->fetch_assoc()) {
        $index = array_search($row['category'], $categories);
        if ($index !== false) {
            $sales[$index] = $row['total_sales'];
        }
    }
    ?>


    <script>
        // Bar Chart
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Revenue',
                    data: <?php echo json_encode($sales); ?>,
                    backgroundColor: '#0d6efd',
                    borderColor: '#0d6efd',
                    borderWidth: 1,
                    borderRadius: 6,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 6500
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
                labels: <?php echo json_encode($categories); ?>,
                datasets: [{
                    data: <?php echo json_encode($sales); ?>,
                    backgroundColor: [
                        '#0d6efd',  // Blue (Wires & Cables)
                        '#20c997',  // Green (Switches & Sockets)
                        '#ffc107',  // Orange (Lighting)
                        '#fd7e14',  // Orange-dark (Fans)
                        '#6f42c1',   // Violet (MCBs & DBs)
                        '#C66EF9'   // Purple (Accessories)
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
                            font: { size: 14 }
                        }
                    }
                }
            }
        });

        function showRetailStoreTab(id) {
            const tabs = document.querySelectorAll('.retailStoreTab');
            const contents = document.querySelectorAll('.retailStore-tab-content');

            tabs.forEach(tab => tab.classList.remove('active'));
            contents.forEach(content => content.classList.remove('active'));

            document.querySelector(`#${id}`).classList.add('active');
            document.querySelector(`[onclick="showRetailStoreTab('${id}')"]`).classList.add('active');
        }
    </script>

    </body>

    </html>