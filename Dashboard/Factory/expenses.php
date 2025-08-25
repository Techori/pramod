<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../_conn.php';
$user_name = $_SESSION['user_name'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['whatAction'])) {
    if ($_POST['whatAction'] === 'addExpense') {

        $category = $_POST['category'];
        $description = $_POST['description'];
        $amount = $_POST['amount'];
        $date = $_POST['date'];
        $Payment_Method = $_POST['method'];
        $Status = $_POST['status'];
        $addedBy = !empty($_POST['customCreatedBy']) ? $conn->real_escape_string($_POST['customCreatedBy']) : $conn->real_escape_string($_POST['createdBy']);

        // Optional fields
        $bankName = $_POST['bankName'] ?? null;
        $accountNumber = $_POST['accountNumber'] ?? null;
        $senderName = $_POST['senderName'] ?? null;

        // Generate unique ID
        $result = $conn->query("SELECT id FROM factory_expenses ORDER BY CAST(SUBSTRING(id, 5) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");

        if ($result && $row = $result->fetch_assoc()) {
            $lastId = $row['id'];
            $num = (int) substr($lastId, 4);
            $newNum = $num + 1;
        } else {
            $newNum = 1;
        }

        $newExpenseId = 'EXP-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);

        // INSERT with extra fields
        $stmt = $conn->prepare("INSERT INTO factory_expenses 
            (id, description, category, addedBy, amount, date, Payment_Method, Status, created_for, bankName, accountNumber, senderName) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("ssssdsssssss", $newExpenseId, $description, $category, $addedBy, $amount, $date, $Payment_Method, $Status, $user_name, $bankName, $accountNumber, $senderName);
        $stmt->execute();

        $conn->commit();
        $stmt->close();

        header("Location: factory_dashboard.php?page=expenses");
        exit;

    } else if ($_POST['whatAction'] === 'editExpense') {
        $expense_id = mysqli_real_escape_string($conn, $_POST['expense_id'] ?? '');
        $amount = mysqli_real_escape_string($conn, $_POST['amount'] ?? '');
        $status = mysqli_real_escape_string($conn, $_POST['status'] ?? '');

        if (empty($expense_id) || empty($amount) || empty($status)) {
            $error_message = 'All fields are required.';
        } else {
            $sql = "UPDATE factory_expenses SET amount = ?, Status = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("dss", $amount, $status, $expense_id);
                $stmt->execute();
                $conn->commit();
                $stmt->close();
                header("Location: factory_dashboard.php?page=expenses");
                exit;
            } else {
                $error_message = 'Error preparing statement: ' . $conn->error;
            }
        }
    }
}
?>

<h2>Factory Expenses</h2>
<p>Track and manage all factory-related expenditures</p>

<!-- Search and Add User Row -->
<div class="container-fluid d-flex justify-content-between align-items-center mb-3">
    <!-- search bar -->
    <div class="d-flex w-75">
        <div class="input-group w-100 me-2">
            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
            <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search in table...">
        </div>
    </div>

    <!-- Button -->
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newExpenses">New
            Expenses</button>
    </div>
</div>

<?php
// Get names for Add expense form dropdown
$itemSql = "SELECT DISTINCT addedBy FROM factory_expenses ORDER BY addedBy";
$itemResult = $conn->query($itemSql);
$items = [];
if ($itemResult->num_rows > 0) {
    while ($row = $itemResult->fetch_assoc()) {
        $items[] = $row['addedBy'];
    }
}
?>

<!-- Expenses Form -->
<div class="modal fade" id="newExpenses" tabindex="-1" aria-labelledby="newExpensesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="expenses.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="newExpensesLabel">Add Expenses</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" class="form-control" id="category" name="category" required>
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
                        <label for="description" class="form-label">Description</label>
                        <input type="text" class="form-control" id="description" name="description" required>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="text" class="form-control" id="amount" name="amount" required>
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>

                    <div class="container mt-5">
                        <form>
                            <!-- Payment Method Dropdown -->
                            <div class="mb-3">
                                <label for="method" class="form-label">Method</label>
                                <select class="form-select" id="method" name="method" required
                                    onchange="togglePaymentFields()">
                                    <option value="" disabled selected>Select Payment Method</option>
                                    <option value="Digital payment">Digital payment</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Payment gateway">Payment gateway</option>
                                </select>
                            </div>

                            <!-- Fields for Digital Payment -->
                            <div id="digitalFields" style="display: none;">
                                <div class="mb-3">
                                    <label for="bankName" class="form-label">Bank Account Name</label>
                                    <input type="text" class="form-control" id="bankName" name="bankName">
                                </div>
                                <div class="mb-3">
                                    <label for="accountNumber" class="form-label">Account Number</label>
                                    <input type="text" class="form-control" id="accountNumber" name="accountNumber">
                                </div>
                            </div>

                            <!-- Field for Cash -->
                            <div id="cashFields" style="display: none;">
                                <div class="mb-3">
                                    <label for="senderName" class="form-label">Sender Name</label>
                                    <input type="text" class="form-control" id="senderName" name="senderName">
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- ✅ JavaScript Code -->
                    <script>
                        function togglePaymentFields() {
                            const method = document.getElementById("method").value;
                            const digitalFields = document.getElementById("digitalFields");
                            const cashFields = document.getElementById("cashFields");

                            digitalFields.style.display = (method === "Digital payment") ? "block" : "none";
                            cashFields.style.display = (method === "Cash") ? "block" : "none";
                        }
                    </script>


                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="" disabled selected>Select Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" name="whatAction" value="addExpense">Save
                            Customer</button>
                    </div>
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
        SUM(CASE WHEN MONTH(date) = ? AND YEAR(date) = ? THEN amount ELSE 0 END) as current_sales,
        SUM(CASE WHEN MONTH(date) = ? AND YEAR(date) = ? THEN amount ELSE 0 END) as last_sales
    FROM factory_expenses WHERE created_for = '$user_name'
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
        SUM(amount) as total_value 
    FROM factory_expenses WHERE created_for = '$user_name'
");
$inv = $invQuery->fetch_assoc();
$invAmount = $inv['total_value'] ?: 0;

// If you want to compare with last month, you'll need to use created_at or updated_at
$invLastQuery = $conn->prepare("
    SELECT SUM(amount) as last_value 
    FROM factory_expenses 
    WHERE MONTH(date) = ? AND YEAR(date) = ? AND category IN ('Raw Materials', 'raw materials')
");
$invLastQuery->bind_param("ii", $lastMonth, $lastYear);
$invLastQuery->execute();
$invLast = $invLastQuery->get_result()->fetch_assoc()['last_value'] ?: 0;

$invChange = percentageChange($invAmount, $invLast);
$invTrend = $invChange >= 0 ? 'success' : 'danger';

// Get Inventory Value
$utilities = $conn->query("
    SELECT 
        SUM(amount) as total_value 
    FROM factory_expenses WHERE category IN ('Utilities', 'utilities') AND created_for = '$user_name'
");
$utl = $utilities->fetch_assoc();
$utlAmount = $utl['total_value'] ?: 0;

// If you want to compare with last month, you'll need to use created_at or updated_at
$utlLastQuery = $conn->prepare("
    SELECT SUM(amount) as last_value 
    FROM factory_expenses 
    WHERE MONTH(date) = ? AND YEAR(date) = ? AND category IN ('Utilities', 'utilities') AND created_for = '$user_name'
");
$utlLastQuery->bind_param("ii", $lastMonth, $lastYear);
$utlLastQuery->execute();
$utlLast = $utlLastQuery->get_result()->fetch_assoc()['last_value'] ?: 0;

$utlChange = percentageChange($utlAmount, $utlLast);
$utlTrend = $utlChange >= 0 ? 'success' : 'danger';



// Get Active Suppliers
$suppliers = $conn->query("SELECT COUNT(*) as count FROM factory_expenses WHERE Status = 'Pending' AND created_for = '$user_name'")->fetch_assoc();
$supplierCount = $suppliers['count'] ?: 0;

$pending = $conn->query("SELECT SUM(amount) as amount FROM factory_expenses WHERE Status = 'Pending' AND created_for = '$user_name'")->fetch_assoc();
$pendingCount = $pending['amount'] ?: 0;

?>


<!-- Cards -->
<div class="row">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
            <div class="card-body">
                <h6 class="text-muted">Monthly Expenses</h6>
                <h3 class="fw-bold">₹<?= number_format($salesAmount, 2) ?></h3>
                <p class="text-<?= $salesTrend ?>"><?= ($salesChange >= 0 ? '+' : '') . $salesChange ?>% vs last month
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
            <div class="card-body">
                <h6 class="text-muted">Total Expense</h6>
                <h3 class="fw-bold">₹<?= number_format($invAmount, 2) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
            <div class="card-body">
                <h6 class="text-muted">Utilities</h6>
                <h3 class="fw-bold">₹<?= number_format($utlAmount, 2) ?></h3>
                <p class="text-<?= $invTrend ?>">
                    <?= ($utlChange >= 0 ? '+' : '') . $utlChange ?>% vs last month
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
            <div class="card-body">
                <h6 class="text-muted">Pending Payments</h6>
                <h3 class="fw-bold">₹<?= $pendingCount ?></h3>
                <p class="text-danger"><?= $supplierCount ?> pending invoice</p>
            </div>
        </div>
    </div>
</div>

<?php
// Card 1: Today's Expenses
$today_expenses_query = "
    SELECT SUM(amount) as total 
    FROM factory_expenses 
    WHERE DATE(date) = CURDATE() AND created_for = '$user_name'
";
$today_expenses_result = $conn->query($today_expenses_query);
$today_expenses = $today_expenses_result ? ($today_expenses_result->fetch_assoc()['total'] ?? 0) : 0;
$today_expenses_result->free();

// Yesterday's Expenses (for comparison)
$yesterday_expenses_query = "
    SELECT SUM(amount) as total 
    FROM factory_expenses 
    WHERE DATE(date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND created_for = '$user_name'
";
$yesterday_expenses_result = $conn->query($yesterday_expenses_query);
$yesterday_expenses = $yesterday_expenses_result ? ($yesterday_expenses_result->fetch_assoc()['total'] ?? 0) : 0;
$yesterday_expenses_result->free();

// Calculate Percentage Difference
$today_expense_percent = $yesterday_expenses > 0
    ? round(($today_expenses - $yesterday_expenses) / $yesterday_expenses * 100, 1)
    : ($today_expenses > 0 ? 100 : 0);

// Format Percentage Text and Class
$today_expense_text = $today_expense_percent >= 0
    ? "+{$today_expense_percent}%"
    : "{$today_expense_percent}%";

$today_expense_class = $today_expense_percent >= 0
    ? 'text-success'
    : 'text-danger';

// Card 2: Current Week Expenses
$current_week_expenses_query = "
    SELECT SUM(amount) as total 
    FROM factory_expenses 
    WHERE YEARWEEK(date, 1) = YEARWEEK(CURDATE(), 1) AND created_for = '$user_name'
";
$current_week_expenses_result = $conn->query($current_week_expenses_query);
$current_week_expenses = $current_week_expenses_result ? ($current_week_expenses_result->fetch_assoc()['total'] ?? 0) : 0;
$current_week_expenses_result->free();

// Last Week Expenses
$last_week_expenses_query = "
    SELECT SUM(amount) as total 
    FROM factory_expenses 
    WHERE YEARWEEK(date, 1) = YEARWEEK(DATE_SUB(CURDATE(), INTERVAL 1 WEEK), 1) AND created_for = '$user_name'
";
$last_week_expenses_result = $conn->query($last_week_expenses_query);
$last_week_expenses = $last_week_expenses_result ? ($last_week_expenses_result->fetch_assoc()['total'] ?? 0) : 0;
$last_week_expenses_result->free();

// Calculate Percentage Difference
$week_expense_percent = $last_week_expenses > 0
    ? round(($current_week_expenses - $last_week_expenses) / $last_week_expenses * 100, 1)
    : ($current_week_expenses > 0 ? 100 : 0);

// Format Percentage Text and Class
$week_expense_text = $week_expense_percent >= 0
    ? "+{$week_expense_percent}%"
    : "{$week_expense_percent}%";

$week_expense_class = $week_expense_percent >= 0
    ? 'text-success'
    : 'text-danger';

// Card 3: Monthly Expenses
$monthly_expenses_query = "SELECT SUM(amount) as total FROM factory_expenses WHERE MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE()) AND created_for = '$user_name'";
$monthly_expenses_result = $conn->query($monthly_expenses_query);
$monthly_expenses = $monthly_expenses_result ? ($monthly_expenses_result->fetch_assoc()['total'] ?? 0) : 0;
$monthly_expenses_result->free();

// Monthly Expenses comparison (last month)
$last_month_expenses_query = "SELECT SUM(amount) as total FROM factory_expenses WHERE MONTH(date) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(date) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND created_for = '$user_name'";
$last_month_expenses_result = $conn->query($last_month_expenses_query);
$last_month_expenses = $last_month_expenses_result ? ($last_month_expenses_result->fetch_assoc()['total'] ?? 0) : 0;
$last_month_expenses_result->free();
$monthly_expenses_percent = $last_month_expenses > 0 ? round(($monthly_expenses - $last_month_expenses) / $last_month_expenses * 100, 1) : ($monthly_expenses > 0 ? 100 : 0);
$monthly_expenses_text = $monthly_expenses_percent >= 0 ? "+{$monthly_expenses_percent}%" : "{$monthly_expenses_percent}%";
$monthly_expenses_class = $monthly_expenses_percent >= 0 ? 'text-danger' : 'text-success';

// Card 4: YTD Expenses
$ytd_expenses_query = "SELECT SUM(amount) as total FROM factory_expenses WHERE YEAR(date) = YEAR(CURDATE()) AND created_for = '$user_name'";
$ytd_expenses_result = $conn->query($ytd_expenses_query);
$ytd_expenses = $ytd_expenses_result ? ($ytd_expenses_result->fetch_assoc()['total'] ?? 0) : 0;
$ytd_expenses_result->free();

// YTD Expenses comparison (last year)
$last_year_expenses_query = "SELECT SUM(amount) as total FROM factory_expenses WHERE YEAR(date) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 YEAR)) AND created_for = '$user_name'";
$last_year_expenses_result = $conn->query($last_year_expenses_query);
$last_year_expenses = $last_year_expenses_result ? ($last_year_expenses_result->fetch_assoc()['total'] ?? 0) : 0;
$last_year_expenses_result->free();
$ytd_expenses_percent = $last_year_expenses > 0 ? round(($ytd_expenses - $last_year_expenses) / $last_year_expenses * 100, 1) : ($ytd_expenses > 0 ? 100 : 0);
$ytd_expenses_text = $ytd_expenses_percent >= 0 ? "+{$ytd_expenses_percent}%" : "{$ytd_expenses_percent}%";
$ytd_expenses_class = $ytd_expenses_percent >= 0 ? 'text-success' : 'text-danger';
?>

<!-- Cards -->
<div class="row">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
            <div class="card-body">
                <h6 class="text-muted">Daily Expenses</h6>
                <h3 class="fw-bold">₹<?php echo number_format($today_expenses, 0); ?></h3>
                <p class="<?php echo $today_expense_class; ?>"><?php echo htmlspecialchars($today_expense_text); ?> vs
                    yesterday</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
            <div class="card-body">
                <h6 class="text-muted">Weekly Expenses</h6>
                <h3 class="fw-bold">₹<?php echo number_format($current_week_expenses, 0); ?></h3>
                <p class="<?php echo $week_expense_class; ?>"><?php echo htmlspecialchars($week_expense_text); ?> vs
                    last week</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
            <div class="card-body">
                <h6 class="text-muted">Monthly Expenses</h6>
                <h3 class="fw-bold">₹<?php echo number_format($monthly_expenses, 0); ?></h3>
                <p class="<?php echo $monthly_expenses_class; ?>">
                    <?php echo htmlspecialchars($monthly_expenses_text); ?> vs last month
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
            <div class="card-body">
                <h6 class="text-muted">Yearly Expenses</h6>
                <h3 class="fw-bold">₹<?php echo number_format($ytd_expenses, 0); ?></h3>
                <p class="<?php echo $ytd_expenses_class; ?>"><?php echo htmlspecialchars($ytd_expenses_text); ?> vs
                    last year</p>
            </div>
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

<!-- Recent Expenses table -->
<div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">

    <div id="workers">
        <div class="d-flex justify-content-between align-items-center">
            <div class="justify-content-start">
                <h5 class="mb-0">Recent Expenses</h5>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-3">
                <input type="text" id="filterCategory" class="form-control" placeholder="Filter by Category">
            </div>
            <div class="col-md-3">
                <input type="text" id="filterDate" class="form-control"
                    placeholder="Filter by Date (e.g. 11 Jul, 2025)">
            </div>
        </div>
        <script>
            $(document).ready(function () {
                var table = $('#supplyTable').DataTable({
                    // You can add options here
                });

                // Category filter (Column index 1)
                $('#filterCategory').on('keyup change', function () {
                    table.column(1).search(this.value).draw();
                });

                // Date filter (Column index 5)
                $('#filterDate').on('keyup change', function () {
                    table.column(5).search(this.value).draw();
                });
            });
        </script>

        <p>Track all factory expenses and payments</p>
        <table class="table table-bordered table-hover" id="supplyTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Expensed By</th>
                    <th>Date</th>
                    <th>Payment Method</th>
                    <th>Bank Name</th>
                    <th>Account No.</th>
                    <th>Sender Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php
                $expenses_query = "SELECT * FROM factory_expenses WHERE created_for = '$user_name' ORDER BY id DESC";
                $expenses_result = $conn->query($expenses_query);
                if ($expenses_result && $expenses_result->num_rows > 0): ?>
                    <?php while ($expense = $expenses_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($expense['id']); ?></td>
                            <td><?php echo htmlspecialchars($expense['category']); ?></td>
                            <td><?php echo htmlspecialchars($expense['description']); ?></td>
                            <td>₹<?php echo number_format($expense['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($expense['addedBy']); ?></td>
                            <td><?php echo htmlspecialchars(date('d M, Y', strtotime($expense['date']))); ?></td>
                            <td><?php echo htmlspecialchars($expense['Payment_Method']); ?></td>
                            <td><?php echo htmlspecialchars($expense['bankName'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($expense['accountNumber'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($expense['senderName'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($expense['Status']); ?></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary btn-sm edit-expense-btn" data-bs-toggle="modal"
                                        data-bs-target="#editExpense"
                                        data-expense-id="<?php echo htmlspecialchars($expense['id']); ?>"
                                        data-amount="<?php echo htmlspecialchars($expense['amount']); ?>"
                                        data-status="<?php echo htmlspecialchars($expense['Status']); ?>">
                                        <i class="fa-solid fa-edit"></i> Edit
                                    </button>
                                    <?php if ($hasDeletePermission): ?>
                                        <form method="post" action="" onsubmit="return confirm('Are you sure you want to delete this expense item?');">
                                            <input type="hidden" name="expense_id" value="<?php echo htmlspecialchars($expense['id']); ?>">
                                            <button type="submit" name="deleteExpense" class="btn btn-danger btn-sm">
                                                <i class="fa-solid fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="12">No expenses found.</td>
                    </tr>
                <?php endif; ?>
                <?php $expenses_result->free(); ?>
            </tbody>

        </table>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const searchInput = document.getElementById('searchInput');
                const rows = document.querySelectorAll('#supplyTable tbody tr');

                searchInput.addEventListener('input', function () {
                    const searchText = this.value.trim().toLowerCase();

                    rows.forEach(row => {
                        const cells = row.querySelectorAll('td');
                        let matchFound = false;

                        cells.forEach(cell => {
                            if (cell.textContent.toLowerCase().includes(searchText)) {
                                matchFound = true;
                            }
                        });

                        row.style.display = matchFound ? '' : 'none';
                    });
                });
            });
        </script>

    </div>
</div>

<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteExpense']) && $hasDeletePermission) {
        $expense_id = $conn->real_escape_string($_POST['expense_id']);

        // Prepare and execute delete query
        $deleteSql = "DELETE FROM factory_expenses WHERE id = ? AND created_for = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("ss", $expense_id, $user_name);

        if ($stmt->execute()) {
            echo "<script>alert('Expense item deleted successfully!'); window.location.href=window.location.href;</script>";
        } else {
            echo "<script>alert('Error deleting expense: " . $conn->error . "');</script>";
        }

        $stmt->close();
    }
    ?>

<!-- Edit Expense Form -->
<div class="modal fade" id="editExpense" tabindex="-1" aria-labelledby="editExpenseLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="expenses.php">
                <input type="hidden" name="whatAction" value="editExpense">
                <input type="hidden" name="expense_id" id="editExpenseId">
                <div class="modal-header">
                    <h5 class="modal-title" id="editExpenseLabel">Edit Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editAmount" class="form-label">Amount</label>
                        <input type="number" step="0.01" class="form-control" id="editAmount" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="editStatus" class="form-label">Status</label>
                        <select class="form-control" id="editStatus" name="status" required>
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Handle Edit button click to populate modal
    document.querySelectorAll('.edit-expense-btn').forEach(button => {
        button.addEventListener('click', function () {
            const expenseId = this.dataset.expenseId;
            const amount = this.dataset.amount;
            const status = this.dataset.status;

            document.getElementById('editExpenseId').value = expenseId;
            document.getElementById('editAmount').value = amount;
            document.getElementById('editStatus').value = status;
        });
    });
</script>