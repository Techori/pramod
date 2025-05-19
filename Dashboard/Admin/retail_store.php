<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../_conn.php';
$user_name = $_SESSION['user_name'];

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
        $created_for = clean($_POST['created_for']);
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

            $stmt->bind_param("sssssss", $newCustomerId, $Name, $type, $contact, $current_date, $user_name, $created_for);
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
    }

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

    } else if ($_POST['whatAction'] === 'editPrice') {
        $itemId = clean($_POST['itemId']);
        $newPrice = clean($_POST['newPrice']);
        $inventory_Of = clean($_POST['inventory_Of']);

        $stmt = $conn->prepare("UPDATE retail_invetory SET price = ?, last_updated = NOW() WHERE Id = ? AND inventory_of = ?");
        $stmt->bind_param("dss", $newPrice, $itemId, $inventory_Of);
        $stmt->execute();
        $stmt->close();

        @header("Location: admin_dashboard.php?page=retail_store");

    } else if ($_POST['whatAction'] === 'deleteItem') {
        $itemId = clean($_POST['itemId']);
        $inventory_of = clean($_POST['inventory_of']);

        $stmt = $conn->prepare("DELETE FROM retail_invetory WHERE Id = ? AND inventory_of = ?");
        $stmt->bind_param("ss", $itemId, $inventory_of);
        $stmt->execute();
        $stmt->close();

        @header("Location: admin_dashboard.php?page=retail_store");

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
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Payment Method:</label>
                        <select class="form-select" id="invoicePaymentMethod" name="invoicePaymentMethod" required>
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
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes:</label>
                    <textarea class="form-control" id="textarea" name="textarea"
                        placeholder="Additional notes, payment terms..." rows="3"></textarea>
                </div>

                <div class="text-end">
                    <p>Subtotal: ₹<span id="subtotal">0.00</span></p>
                    <p class="gst-section">GST (<span id="gstPercent">18</span>%): ₹<span id="gstAmount">0.00</span></p>
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

<!-- Buttons -->
<div class="row justify-content-center">
    <div class="col-md-4 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100" onclick="openInvoiceModal(event)"
            id="invoice"><i class="fa-solid fa-cart-plus"></i> New
            Sales</button>
    </div>
    <div class="col-md-4 col-sm-6 mb-4">
        <a href="?page=inventory" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-box"></i> Check
            Inventory</a>
    </div>
    <div class="col-md-4 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-file-lines"></i> Daily
            Reports</button>
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

<!-- Low stock alert bars -->
<div class="card mt-4">
    <div class="alert-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><i class="fa-solid fa-circle-exclamation"></i> Low Stock Alerts</h5>
        </div>

        <div class="space-y-4">
            <?php

            // Fetch items for the user
            $sql = "SELECT item_name, stock, reorder_point FROM retail_invetory";
            $stmt = $conn->prepare($sql);
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
        // print krane ke liye

        function printSection(sectionId) {
            const printContent = document.getElementById(sectionId).innerHTML;
            const originalContent = document.body.innerHTML;

            document.body.innerHTML = printContent;
            window.print();
            document.body.innerHTML = originalContent;
        }

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
                    <input type="text" id="searchInput" class="form-control border-start-0 table-search"
                        data-table="supplyTable" placeholder="Search..." />
                </div>
                <button class="btn btn-outline-primary status-filter me-2" data-type="Completed"
                    data-table="sales_table">Completed</button>
                <button class="btn btn-outline-primary status-filter me-2" data-type="Pending"
                    data-table="sales_table">Pending</button>
                <button class="btn btn-outline-danger reset-filters me-2" data-table="sales_table">Remove
                    Filters</button>
            </div>

            <div class="justify-content-end">
                <button class="btn btn-outline-primary" onclick="openInvoiceModal(event)" id="invoice"><i
                        class="fa-solid fa-cart-plus"></i> New Sale</button>
            </div>

        </div>
        <table id="supplyTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Sales ID</th>
                    <th>Invoice ID</th>
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
                        echo "<td>" . htmlspecialchars($row['invoice_id']) . "</td>";
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
                    echo "<tr><td colspan='10' class='text-center'>No transactions found</td></tr>";
                }
                ?>
            </tbody>
            <div id="pagination" class="mt-3 d-flex justify-content-center gap-2"></div>
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
        </script>
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
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addItem"><i
                        class="fa-solid fa-plus"></i> Add Product</button>
            </div>

        </div>
        <table id="inventory_table" class="table table-bordered table-hover">
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

                                    <form method="POST" action="retail_store.php" style="display:inline;">
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

    <!-- Add New Item Form -->
    <div class="modal fade" id="addItem" tabindex="-1" aria-labelledby="addItemLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="retail_store.php" method="POST">
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
                <form action="retail_store.php" method="POST">
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
            <form method="POST" action="retail_store.php" class="modal-content">
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
                <form action="retail_store.php" method="POST">
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
                        const docType = row.children[8]?.innerText.trim().toLowerCase();
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
        window.onclick = function (event) {
            const modal = document.getElementById('invoiceModal');
            if (event.target === modal) {
                closeInvoiceModal();
            }
        };

        function collectInvoiceData() {
            let item_names = [], descriptions = [], quantities = [], prices = [], totals = [];

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
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var editModal = document.getElementById('editPriceModal');
            editModal.addEventListener('show.bs.modal', function (event) {
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
    </script>

    </body>

    </html>