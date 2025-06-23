<?php


// Include the database connection file
require_once '../../_conn.php';

// Initialize variables for form submission status
$show_success_popup = false;
$success_message = '';

// Handle Add Expense form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_expense'])) {
    $date = mysqli_real_escape_string($conn, $_POST['date'] ?? '');
    $category = mysqli_real_escape_string($conn, $_POST['category'] ?? '');
    $name = !empty($_POST['customCreatedBy']) ? mysqli_real_escape_string($conn, $_POST['customCreatedBy'] ?? '') : mysqli_real_escape_string($conn, $_POST['createdBy'] ?? '');
    $amount = mysqli_real_escape_string($conn, $_POST['amount'] ?? '');
    $vendor = mysqli_real_escape_string($conn, $_POST['vendor'] ?? '');
    $status = mysqli_real_escape_string($conn, $_POST['status'] ?? '');
    $method = mysqli_real_escape_string($conn, $_POST['method'] ?? '');

    // Basic validation
    if (empty($date) || empty($category) || empty($name) || empty($amount) || empty($vendor) || empty($status) || empty($method)) {
        $error_message = 'All fields are required.';
    } else {
        // Generate expense ID (EXP-YYYY-NNN)
        $year = date('Y', strtotime($date));
        $id_query = "SELECT COUNT(*) as count FROM expenses WHERE YEAR(date) = '$year'";
        $id_result = $conn->query($id_query);
        $count = $id_result ? ($id_result->fetch_assoc()['count'] + 1) : 1;
        $id = "EXP-$year-" . str_pad($count, 3, '0', STR_PAD_LEFT);
        $id_result->free();

        // Insert into database
        $sql = "INSERT INTO expenses (id, date, category, addedBy, amount, vendor, status, method) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssssdsss", $id, $date, $category, $name, $amount, $vendor, $status, $method);
            if ($stmt->execute()) {
                $show_success_popup = true;
                $success_message = 'Expense added successfully.';
            } else {
                $error_message = 'Error: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = 'Error preparing statement: ' . $conn->error;
        }
    }
}

// Handle Edit Expense form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_expense'])) {
    $expense_id = mysqli_real_escape_string($conn, $_POST['expense_id'] ?? '');
    $amount = mysqli_real_escape_string($conn, $_POST['amount'] ?? '');
    $status = mysqli_real_escape_string($conn, $_POST['status'] ?? '');

    // Basic validation
    if (empty($expense_id) || empty($amount) || empty($status)) {
        $error_message = 'All fields are required.';
    } else {
        $sql = "UPDATE expenses SET amount = ?, status = ?, updated_at = CURRENT_TIMESTAMP WHERE expense_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("dsi", $amount, $status, $expense_id);
            if ($stmt->execute()) {
                $show_success_popup = true;
                $success_message = 'Expense updated successfully.';
            } else {
                $error_message = 'Error: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = 'Error preparing statement: ' . $conn->error;
        }
    }
}

// Card 1: Monthly Expenses
$monthly_expenses_query = "SELECT SUM(amount) as total FROM expenses WHERE MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())";
$monthly_expenses_result = $conn->query($monthly_expenses_query);
$monthly_expenses = $monthly_expenses_result ? ($monthly_expenses_result->fetch_assoc()['total'] ?? 0) : 0;
$monthly_expenses_result->free();

// Monthly Expenses comparison (last month)
$last_month_expenses_query = "SELECT SUM(amount) as total FROM expenses WHERE MONTH(date) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(date) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
$last_month_expenses_result = $conn->query($last_month_expenses_query);
$last_month_expenses = $last_month_expenses_result ? ($last_month_expenses_result->fetch_assoc()['total'] ?? 0) : 0;
$last_month_expenses_result->free();
$monthly_expenses_percent = $last_month_expenses > 0 ? round(($monthly_expenses - $last_month_expenses) / $last_month_expenses * 100, 1) : ($monthly_expenses > 0 ? 100 : 0);
$monthly_expenses_text = $monthly_expenses_percent >= 0 ? "+{$monthly_expenses_percent}%" : "{$monthly_expenses_percent}%";
$monthly_expenses_class = $monthly_expenses_percent >= 0 ? 'text-danger' : 'text-success';

// Card 2: YTD Expenses
$ytd_expenses_query = "SELECT SUM(amount) as total FROM expenses WHERE YEAR(date) = YEAR(CURDATE())";
$ytd_expenses_result = $conn->query($ytd_expenses_query);
$ytd_expenses = $ytd_expenses_result ? ($ytd_expenses_result->fetch_assoc()['total'] ?? 0) : 0;
$ytd_expenses_result->free();

// YTD Expenses comparison (last year)
$last_year_expenses_query = "SELECT SUM(amount) as total FROM expenses WHERE YEAR(date) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 YEAR))";
$last_year_expenses_result = $conn->query($last_year_expenses_query);
$last_year_expenses = $last_year_expenses_result ? ($last_year_expenses_result->fetch_assoc()['total'] ?? 0) : 0;
$last_year_expenses_result->free();
$ytd_expenses_percent = $last_year_expenses > 0 ? round(($ytd_expenses - $last_year_expenses) / $last_year_expenses * 100, 1) : ($ytd_expenses > 0 ? 100 : 0);
$ytd_expenses_text = $ytd_expenses_percent >= 0 ? "+{$ytd_expenses_percent}%" : "{$ytd_expenses_percent}%";
$ytd_expenses_class = $ytd_expenses_percent >= 0 ? 'text-success' : 'text-danger';

// Card 3: Pending Approvals
$pending_approvals_query = "SELECT COUNT(*) as count FROM expenses WHERE status = 'Pending'";
$pending_approvals_result = $conn->query($pending_approvals_query);
$pending_approvals = $pending_approvals_result ? ($pending_approvals_result->fetch_assoc()['count'] ?? 0) : 0;
$pending_approvals_result->free();

// Card 4: Monthly Savings (Placeholder: Assume budget of ₹2,00,000)
$budget = 200000; // Fixed budget for demo
$monthly_savings = $budget - $monthly_expenses;
$last_month_savings = $budget - $last_month_expenses;
$monthly_savings_percent = $last_month_savings > 0 ? round(($monthly_savings - $last_month_savings) / $last_month_savings * 100, 1) : ($monthly_savings > 0 ? 100 : 0);
$monthly_savings_text = $monthly_savings_percent >= 0 ? "+{$monthly_savings_percent}%" : "{$monthly_savings_percent}%";
$monthly_savings_class = $monthly_savings_percent >= 0 ? 'text-success' : 'text-danger';

// Card 5: Today's Expenses
$today_expenses_query = "
    SELECT SUM(amount) as total 
    FROM expenses 
    WHERE DATE(date) = CURDATE()
";
$today_expenses_result = $conn->query($today_expenses_query);
$today_expenses = $today_expenses_result ? ($today_expenses_result->fetch_assoc()['total'] ?? 0) : 0;
$today_expenses_result->free();

// Yesterday's Expenses (for comparison)
$yesterday_expenses_query = "
    SELECT SUM(amount) as total 
    FROM expenses 
    WHERE DATE(date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
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

// Card 6: Current Week Expenses
$current_week_expenses_query = "
    SELECT SUM(amount) as total 
    FROM expenses 
    WHERE YEARWEEK(date, 1) = YEARWEEK(CURDATE(), 1)
";
$current_week_expenses_result = $conn->query($current_week_expenses_query);
$current_week_expenses = $current_week_expenses_result ? ($current_week_expenses_result->fetch_assoc()['total'] ?? 0) : 0;
$current_week_expenses_result->free();

// Last Week Expenses
$last_week_expenses_query = "
    SELECT SUM(amount) as total 
    FROM expenses 
    WHERE YEARWEEK(date, 1) = YEARWEEK(DATE_SUB(CURDATE(), INTERVAL 1 WEEK), 1)
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


// Pie Chart: Expenses by Category (also used for analysis)
$pie_query = "SELECT category, SUM(amount) as total FROM expenses WHERE YEAR(date) = YEAR(CURDATE()) GROUP BY category";
$pie_result = $conn->query($pie_query);
$pie_labels = [];
$pie_data = [];
$pie_colors = ['#0d6efd', '#20c997', '#ffc107', '#fd7e14', '#6f42c1', '#C66EF9'];
$category_expenses = []; // For analysis table
$color_index = 0;
if ($pie_result) {
    while ($row = $pie_result->fetch_assoc()) {
        $pie_labels[] = $row['category'];
        $pie_data[] = $row['total'];
        $category_expenses[] = [
            'category' => $row['category'],
            'total' => $row['total']
        ];
        $color_index = ($color_index + 1) % count($pie_colors); // Cycle through colors
    }
    $pie_result->free();
}

// Bar Chart: Monthly Expenses (Last 6 Months)
$bar_query = "SELECT MONTH(date) as month_num, YEAR(date) as year_num, SUM(amount) as total 
              FROM expenses 
              WHERE date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) 
              GROUP BY YEAR(date), MONTH(date) 
              ORDER BY YEAR(date) DESC, MONTH(date) DESC";
$bar_result = $conn->query($bar_query);
$bar_labels = [];
$bar_data = [];
$last_six_months = [];
for ($i = 0; $i < 6; $i++) {
    $month = date('Y-m', strtotime("-$i months"));
    $last_six_months[$month] = ['label' => date('M', strtotime($month)), 'total' => 0];
}
if ($bar_result) {
    while ($row = $bar_result->fetch_assoc()) {
        $month_key = sprintf("%d-%02d", $row['year_num'], $row['month_num']);
        $month_label = date('M', mktime(0, 0, 0, $row['month_num'], 1, $row['year_num']));
        if (isset($last_six_months[$month_key])) {
            $last_six_months[$month_key]['total'] = $row['total'];
            $last_six_months[$month_key]['label'] = $month_label;
        }
    }
    $bar_result->free();
}
foreach ($last_six_months as $month) {
    $bar_labels[] = $month['label'];
    $bar_data[] = $month['total'];
}

// Analysis Data: Trend Insights
$highest_spending_month = '';
$highest_spending_amount = 0;
$lowest_spending_month = '';
$lowest_spending_amount = PHP_FLOAT_MAX;
foreach ($last_six_months as $month_key => $month) {
    if ($month['total'] > $highest_spending_amount) {
        $highest_spending_amount = $month['total'];
        $highest_spending_month = $month['label'];
    }
    if ($month['total'] < $lowest_spending_amount && $month['total'] > 0) {
        $lowest_spending_amount = $month['total'];
        $lowest_spending_month = $month['label'];
    }
}
$trend_insight = "Highest spending was in {$highest_spending_month} (₹" . number_format($highest_spending_amount, 0) . "). ";
if ($lowest_spending_amount != PHP_FLOAT_MAX) {
    $trend_insight .= "Lowest spending was in {$lowest_spending_month} (₹" . number_format($lowest_spending_amount, 0) . ").";
} else {
    $trend_insight .= "No spending recorded in some months.";
}

// Analysis Data: Budget vs. Actual
$budget_status = $monthly_expenses > $budget ? 'Over Budget' : 'Within Budget';
$budget_difference = abs($budget - $monthly_expenses);
$budget_status_text = $budget_status === 'Over Budget' 
    ? "You are over budget by ₹" . number_format($budget_difference, 0) 
    : "You are under budget by ₹" . number_format($budget_difference, 0);

// Analysis Data: Savings Insights
$savings_insight = $monthly_savings >= 0 
    ? "Your savings are positive at ₹" . number_format($monthly_savings, 0) . " this month."
    : "You have a deficit of ₹" . number_format(abs($monthly_savings), 0) . " this month.";

// Fetch recent expenses for table
$expenses_query = "SELECT expense_id, id, date, category, addedBy, amount, vendor, status FROM expenses ORDER BY date DESC LIMIT 5";
$expenses_result = $conn->query($expenses_query);


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

    .analysis-section {
        margin-bottom: 20px;
    }

    .analysis-section h5 {
        border-bottom: 1px solid #ddd;
        padding-bottom: 5px;
        margin-bottom: 10px;
    }
</style>

<!-- JavaScript to handle modals -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show success popup
        <?php if ($show_success_popup): ?>
            var successModal = new bootstrap.Modal(document.getElementById('successPopup'));
            successModal.show();
        <?php endif; ?>

        // Handle Edit button click to populate modal
        document.querySelectorAll('.edit-expense-btn').forEach(button => {
            button.addEventListener('click', function() {
                const expenseId = this.dataset.expenseId;
                const amount = this.dataset.amount;
                const status = this.dataset.status;

                document.getElementById('editExpenseId').value = expenseId;
                document.getElementById('editAmount').value = amount;
                document.getElementById('editStatus').value = status;
            });
        });

        // Handle Generate Report button
        document.getElementById('generateReportBtn').addEventListener('click', function() {
            const reportType = document.querySelector('#createReportModal select[name="reportType"]').value;
            const dateRange = document.querySelector('#createReportModal select[name="dateRange"]').value;
            const taxInfo = document.querySelector('#taxInfo').checked ? 'Yes' : 'No';
            const cancelledTransactions = document.querySelector('#cancelledTransactions').checked ? 'Yes' : 'No';
            const detailedView = document.querySelector('#detailedView').checked ? 'Yes' : 'No';
            const reportFormat = document.querySelector('input[name="reportFormat"]:checked').value;

            const reportDetails = `
                Report Type: ${reportType}<br>
                Date Range: ${dateRange}<br>
                Include Tax Information: ${taxInfo}<br>
                Include Cancelled Transactions: ${cancelledTransactions}<br>
                Show Detailed View: ${detailedView}<br>
                Report Format: ${reportFormat}
            `;
            document.getElementById('successPopupMessage').innerHTML = 'Report Generated:<br>' + reportDetails;
            var successModal = new bootstrap.Modal(document.getElementById('successPopup'));
            successModal.show();
        });
    });
</script>

<!-- Error Message (if any) -->
<?php if (isset($error_message)): ?>
    <div class="alert alert-danger" role="alert">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
<?php endif; ?>

<!-- Success Popup Modal -->
<div class="modal fade" id="successPopup" tabindex="-1" aria-labelledby="successPopupLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successPopupLabel">Success</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="successPopupMessage">
                <?php echo htmlspecialchars($success_message ?: 'Operation successful.'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<h1>Expenses Dashboard</h1>
<p>Track and manage all business expenses</p>

<!-- Cards -->
<div class="row">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
            <div class="card-body">
                <h6 class="text-muted">Monthly Expenses</h6>
                <h3 class="fw-bold">₹<?php echo number_format($monthly_expenses, 0); ?></h3>
                <p class="<?php echo $monthly_expenses_class; ?>"><?php echo htmlspecialchars($monthly_expenses_text); ?> vs last month</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
            <div class="card-body">
                <h6 class="text-muted">YTD Expenses</h6>
                <h3 class="fw-bold">₹<?php echo number_format($ytd_expenses, 0); ?></h3>
                <p class="<?php echo $ytd_expenses_class; ?>"><?php echo htmlspecialchars($ytd_expenses_text); ?> vs last year</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
            <div class="card-body">
                <h6 class="text-muted">Pending Approvals</h6>
                <h3 class="fw-bold"><?php echo htmlspecialchars($pending_approvals); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
            <div class="card-body">
                <h6 class="text-muted">Monthly Savings</h6>
                <h3 class="fw-bold">₹<?php echo number_format($monthly_savings, 0); ?></h3>
                <p class="<?php echo $monthly_savings_class; ?>"><?php echo htmlspecialchars($monthly_savings_text); ?> vs last month</p>
            </div>
        </div>
    </div>
</div>

<!-- Cards -->
<div class="row">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
            <div class="card-body">
                <h6 class="text-muted">Daily Expenses</h6>
                <h3 class="fw-bold">₹<?php echo number_format($today_expenses, 0); ?></h3>
                <p class="<?php echo $today_expense_class; ?>"><?php echo htmlspecialchars($today_expense_text); ?> vs yesterday</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
            <div class="card-body">
                <h6 class="text-muted">Weekly Expenses</h6>
                <h3 class="fw-bold">₹<?php echo number_format($current_week_expenses, 0); ?></h3>
                <p class="<?php echo $week_expense_class; ?>"><?php echo htmlspecialchars($week_expense_text); ?> vs last week</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
            <div class="card-body">
                <h6 class="text-muted">Monthly Expenses</h6>
                <h3 class="fw-bold">₹<?php echo number_format($monthly_expenses, 0); ?></h3>
                <p class="<?php echo $monthly_expenses_class; ?>"><?php echo htmlspecialchars($monthly_expenses_text); ?> vs last month</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
            <div class="card-body">
                <h6 class="text-muted">Yearly Expenses</h6>
                <h3 class="fw-bold">₹<?php echo number_format($ytd_expenses, 0); ?></h3>
                <p class="<?php echo $ytd_expenses_class; ?>"><?php echo htmlspecialchars($ytd_expenses_text); ?> vs last year</p>
            </div>
        </div>
    </div>
</div>

<!-- Buttons -->
<div class="row justify-content-center">
    <div class="col-md-3 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100" data-bs-toggle="modal" data-bs-target="#addExpense">
            <i class="fa-solid fa-plus"></i> Add New Expenses
        </button>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100">
            <i class="fa-regular fa-file-word"></i> Scan Receipt
        </button>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100" data-bs-toggle="modal" data-bs-target="#createReportModal">
            <i class="fa-solid fa-file-lines"></i> Generate Report
        </button>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100" data-bs-toggle="modal" data-bs-target="#viewAnalysisModal">
            <i class="fa-solid fa-chart-column"></i> View Analysis
        </button>
    </div>
</div>

<!-- Add Expense Form -->
<div class="modal fade" id="addExpense" tabindex="-1" aria-labelledby="addExpenseLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post">
                <input type="hidden" name="add_expense" value="1">
                <div class="modal-header">
                    <h5 class="modal-title" id="addExpenseLabel">Add Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-control" id="category" name="category" required>
                            <option value="Raw Materials">Raw Materials</option>
                            <option value="Utilities">Utilities</option>
                            <option value="Salaries">Salaries</option>
                            <option value="Transport">Transport</option>
                            <option value="Office">Office</option>
                            <option value="Others">Others</option>
                        </select>
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
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="vendor" class="form-label">Vendor</label>
                        <input type="text" class="form-control" id="vendor" name="vendor" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="method" class="form-label">Method</label>
                        <select class="form-control" id="method" name="method" required>
                            <option value="Cash">Cash</option>
                            <option value="Credit">Credit</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Expense</button>
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

<!-- Edit Expense Form -->
<div class="modal fade" id="editExpense" tabindex="-1" aria-labelledby="editExpenseLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post">
                <input type="hidden" name="edit_expense" value="1">
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

<!-- Generate Report Modal -->
<div class="modal fade" id="createReportModal" tabindex="-1" aria-labelledby="createReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <small class="text-muted">Generate detailed reports for business insights</small>
                <div class="mt-3">
                    <label class="form-label">Report Type</label>
                    <select class="form-select" name="reportType">
                        <option>Bill-wise Profit</option>
                        <option>Product Performance</option>
                        <option>Customer Retention</option>
                    </select>
                </div>
                <div class="mt-3">
                    <label class="form-label">Date Range</label>
                    <select class="form-select" name="dateRange">
                        <option>This Month</option>
                        <option>Last Month</option>
                        <option>This Year</option>
                    </select>
                </div>
                <div class="mt-3">
                    <label class="form-label">Additional Options</label><br>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="taxInfo">
                        <label class="form-check-label" for="taxInfo">Include Tax Information</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="cancelledTransactions">
                        <label class="form-check-label" for="cancelledTransactions">Include Cancelled Transactions</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="detailedView">
                        <label class="form-check-label" for="detailedView">Show Detailed View</label>
                    </div>
                </div>
                <div class="mt-3">
                    <label class="form-label">Report Format</label><br>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="reportFormat" id="detailed" value="Detailed" checked>
                        <label class="form-check-label" for="detailed">Detailed</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="reportFormat" id="summary" value="Summary">
                        <label class="form-check-label" for="summary">Summary</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="reportFormat" id="consolidated" value="Consolidated">
                        <label class="form-check-label" for="consolidated">Consolidated</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="generateReportBtn">Generate Report</button>
            </div>
        </div>
    </div>
</div>

<!-- View Analysis Modal -->
<div class="modal fade" id="viewAnalysisModal" tabindex="-1" aria-labelledby="viewAnalysisModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewAnalysisModalLabel">Financial Analysis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <small class="text-muted">Detailed insights into your expenses and savings</small>

                <!-- Budget vs. Actual Spending -->
                <div class="analysis-section">
                    <h5>Budget vs. Actual Spending (This Month)</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Metric</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Budget</td>
                                <td>₹<?php echo number_format($budget, 0); ?></td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>Actual Spending</td>
                                <td>₹<?php echo number_format($monthly_expenses, 0); ?></td>
                                <td class="<?php echo $budget_status === 'Over Budget' ? 'text-danger' : 'text-success'; ?>">
                                    <?php echo htmlspecialchars($budget_status_text); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Category-wise Breakdown -->
                <div class="analysis-section">
                    <h5>Category-wise Breakdown (This Year)</h5>
                    <?php if (!empty($category_expenses)): ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Total Expenses</th>
                                    <th>Percentage of YTD Expenses</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($category_expenses as $cat): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($cat['category']); ?></td>
                                        <td>₹<?php echo number_format($cat['total'], 0); ?></td>
                                        <td>
                                            <?php 
                                                $percentage = $ytd_expenses > 0 ? round(($cat['total'] / $ytd_expenses) * 100, 1) : 0;
                                                echo $percentage . '%';
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No expenses recorded this year.</p>
                    <?php endif; ?>
                </div>

                <!-- Trend Analysis -->
                <div class="analysis-section">
                    <h5>Expense Trends (Last 6 Months)</h5>
                    <p><?php echo htmlspecialchars($trend_insight); ?></p>
                </div>

                <!-- Savings Insights -->
                <div class="analysis-section">
                    <h5>Savings Insights</h5>
                    <p><?php echo htmlspecialchars($savings_insight); ?></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="chart-container">
    <div class="chart-box">
        <h3>Expenses by Category</h3>
        <div style="position: relative; width: 100%; max-width: 300px; margin: 0 auto;">
            <canvas id="pieChart"></canvas>
        </div>
    </div>
    <div class="chart-box">
        <h3>Expenses Trend (Last 6 Months)</h3>
        <canvas id="barChart"></canvas>
    </div>
</div>

<!-- Table -->
<div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">
    <div id="expenses">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="justify-content-start">
                <h1>Recent Expenses</h1>
            </div>
        </div>
        <table id="Table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Expensed By</th>
                    <th>Amount</th>
                    <th>Vendor</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($expenses_result && $expenses_result->num_rows > 0): ?>
                    <?php while ($expense = $expenses_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($expense['id']); ?></td>
                            <td><?php echo htmlspecialchars(date('d M, Y', strtotime($expense['date']))); ?></td>
                            <td><?php echo htmlspecialchars($expense['category']); ?></td>
                            <td><?php echo htmlspecialchars($expense['addedBy']); ?></td>
                            <td>₹<?php echo number_format($expense['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($expense['vendor']); ?></td>
                            <td><?php echo htmlspecialchars($expense['status']); ?></td>
                            <td>
                                <button class="btn btn-outline-primary btn-sm edit-expense-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editExpense"
                                        data-expense-id="<?php echo htmlspecialchars($expense['expense_id']); ?>"
                                        data-amount="<?php echo htmlspecialchars($expense['amount']); ?>"
                                        data-status="<?php echo htmlspecialchars($expense['status']); ?>">
                                    <i class="fa-solid fa-edit"></i> Edit
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No expenses found.</td>
                    </tr>
                <?php endif; ?>
                <?php $expenses_result->free(); ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Pie Chart
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

    // Bar Chart
    const barCtx = document.getElementById('barChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($bar_labels); ?>,
            datasets: [{
                label: 'Expenses',
                data: <?php echo json_encode($bar_data); ?>,
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
                        stepSize: 25000,
                        callback: function(value) {
                            return '₹' + value.toLocaleString();
                        }
                    },
                    title: {
                        display: true,
                        text: 'Amount'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Month'
                    }
                }
            }
        }
    });
</script>