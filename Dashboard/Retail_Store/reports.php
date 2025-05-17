<?php
// Include mock database
require_once 'database.php';

// Get data from database
$reports = get_reports();
$monthly_sales_data = get_monthly_sales_data();
$product_category_data = get_product_category_data();
$payment_method_data = get_payment_method_data();
$customer_visit_data = get_customer_visit_data();

// Handle actions
$success_message = '';
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $report_id = isset($_POST['report_id']) ? $_POST['report_id'] : '';
        switch ($action) {
            case 'generate_new_report':
                $success_message = 'Generate New Report operation initiated successfully.';
                break;
            case 'schedule_reports':
                $success_message = 'Schedule Reports operation initiated successfully.';
                break;
            case 'filter_reports':
                $success_message = 'Filter Reports operation initiated successfully.';
                break;
            case 'view_report':
                $success_message = "View $report_id operation initiated successfully.";
                break;
            case 'download_report':
                $success_message = "Download $report_id operation initiated successfully.";
                break;
            case 'generate_sales_report':
                $success_message = 'Generate Sales Report operation initiated successfully.';
                break;
            case 'generate_inventory_report':
                $success_message = 'Generate Inventory Report operation initiated successfully.';
                break;
            case 'generate_customer_report':
                $success_message = 'Generate Customer Report operation initiated successfully.';
                break;
            case 'generate_financial_report':
                $success_message = 'Generate Financial Report operation initiated successfully.';
                break;
        }
    }
}

// Filter parameters
$period = isset($_GET['period']) && in_array($_GET['period'], ['apr2025', 'mar2025', 'feb2025', 'jan2025', 'q12025']) ? $_GET['period'] : 'apr2025';

// Filter reports (mock filtering based on period)
$filtered_reports = array_filter($reports, function ($report) use ($period) {
    if ($period === 'q12025') {
        return $report['date'] === 'Q1 2025';
    }
    $month_map = [
        'apr2025' => 'Apr 2025',
        'mar2025' => 'Mar 2025',
        'feb2025' => 'Feb 2025',
        'jan2025' => 'Jan 2025'
    ];
    return $report['date'] === ($month_map[$period] ?? 'Apr 2025');
});

// Report type badge function
function get_type_badge($type) {
    return "<span class='badge bg-light border text-dark'>$type</span>";
}

// Status badge function
function get_status_badge($status) {
    $status_config = [
        'Generated' => ['class' => 'bg-green-subtle text-green', 'label' => 'Generated'],
        'Pending' => ['class' => 'bg-secondary-subtle text-secondary', 'label' => 'Pending']
    ];
    $config = isset($status_config[$status]) ? $status_config[$status] : $status_config['Pending'];
    return "<span class='badge {$config['class']}'>{$config['label']}</span>";
}
?>

<div class="main-content">
    <h1><i class="fas fa-chart-bar text-primary me-2"></i> Store Reports</h1>
    <p class="text-muted">Generate and view comprehensive store reports</p>

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

    <!-- Report Actions -->
    <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-2">
            <form method="GET" action="?page=reports" class="d-inline">
                <input type="hidden" name="page" value="reports">
                <select
                    name="period"
                    class="form-select form-select-sm"
                    style="width: 180px;"
                    onchange="this.form.submit()"
                >
                    <option value="apr2025" <?php echo $period === 'apr2025' ? 'selected' : ''; ?>>April 2025</option>
                    <option value="mar2025" <?php echo $period === 'mar2025' ? 'selected' : ''; ?>>March 2025</option>
                    <option value="feb2025" <?php echo $period === 'feb2025' ? 'selected' : ''; ?>>February 2025</option>
                    <option value="jan2025" <?php echo $period === 'jan2025' ? 'selected' : ''; ?>>January 2025</option>
                    <option value="q12025" <?php echo $period === 'q12025' ? 'selected' : ''; ?>>Q1 2025</option>
                </select>
            </form>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <form method="POST" action="?page=reports" class="d-inline">
                <input type="hidden" name="action" value="generate_new_report">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-file-alt me-1"></i> Generate Report
                </button>
            </form>
        </div>
    </div>

    <!-- Charts and Analytics -->
    <div class="row row-cols-1 row-cols-lg-2 g-4 mb-4">
        <div class="col">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3 d-flex align-items-center gap-2">
                        <i class="fas fa-chart-line text-primary"></i> Monthly Sales Trend
                    </h5>
                    <div class="bg-light rounded py-5 text-center">
                        <i class="fas fa-chart-bar fa-2x text-muted me-2"></i>
                        <span class="text-muted">Monthly Sales Bar Chart Placeholder</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3 d-flex align-items-center gap-2">
                        <i class="fas fa-chart-pie text-primary"></i> Sales by Product Category
                    </h5>
                    <div class="bg-light rounded py-5 text-center">
                        <i class="fas fa-chart-pie fa-2x text-muted me-2"></i>
                        <span class="text-muted">Product Category Pie Chart Placeholder</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-lg-2 g-4 mb-4">
        <div class="col">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3 d-flex align-items-center gap-2">
                        <i class="fas fa-credit-card text-primary"></i> Payment Methods
                    </h5>
                    <div class="bg-light rounded py-5 text-center">
                        <i class="fas fa-chart-pie fa-2x text-muted me-2"></i>
                        <span class="text-muted">Payment Methods Pie Chart Placeholder</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3 d-flex align-items-center gap-2">
                        <i class="fas fa-users text-primary"></i> Customer Visits (Last 7 Days)
                    </h5>
                    <div class="bg-light rounded py-5 text-center">
                        <i class="fas fa-chart-line fa-2x text-muted me-2"></i>
                        <span class="text-muted">Customer Visits Line Chart Placeholder</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Templates -->
    <div class="card card-border shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="mb-3">Report Templates</h5>
            <p class="text-muted mb-3">Quick access to common report types</p>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
                <div class="col">
                    <div class="card card-border shadow-sm border-2 border-dashed border-primary-hover">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-shopping-bag fa-3x text-primary mb-3"></i>
                            <h5 class="font-medium">Sales Report</h5>
                            <p class="text-sm text-muted mt-1">Comprehensive sales analytics</p>
                        </div>
                        <form method="POST" action="?page=reports">
                            <input type="hidden" name="action" value="generate_sales_report">
                            <button type="submit" class="btn btn-outline-primary btn-sm w-100 border-0">Select</button>
                        </form>
                    </div>
                </div>
                <div class="col">
                    <div class="card card-border shadow-sm border-2 border-dashed border-primary-hover">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-box fa-3x text-primary mb-3"></i>
                            <h5 class="font-medium">Inventory Report</h5>
                            <p class="text-sm text-muted mt-1">Stock levels and movements</p>
                        </div>
                        <form method="POST" action="?page=reports">
                            <input type="hidden" name="action" value="generate_inventory_report">
                            <button type="submit" class="btn btn-outline-primary btn-sm w-100 border-0">Select</button>
                        </form>
                    </div>
                </div>
                <div class="col">
                    <div class="card card-border shadow-sm border-2 border-dashed border-primary-hover">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-users fa-3x text-primary mb-3"></i>
                            <h5 class="font-medium">Customer Report</h5>
                            <p class="text-sm text-muted mt-1">Customer demographics and behavior</p>
                        </div>
                        <form method="POST" action="?page=reports">
                            <input type="hidden" name="action" value="generate_customer_report">
                            <button type="submit" class="btn btn-outline-primary btn-sm w-100 border-0">Select</button>
                        </form>
                    </div>
                </div>
                <div class="col">
                    <div class="card card-border shadow-sm border-2 border-dashed border-primary-hover">
                        <div class="card-body p-4 text-center">
                            <i class="fas fa-credit-card fa-3x text-primary mb-3"></i>
                            <h5 class="font-medium">Financial Report</h5>
                            <p class="text-sm text-muted mt-1">Revenue, expenses and profits</p>
                        </div>
                        <form method="POST" action="?page=reports">
                            <input type="hidden" name="action" value="generate_financial_report">
                            <button type="submit" class="btn btn-outline-primary btn-sm w-100 border-0">Select</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.w-120px {
    width: 120px;
}
.text-sm {
    font-size: 0.875rem;
}
.font-medium {
    font-weight: 500;
}
.bg-green-subtle {
    background-color: #d4edda !important;
}
.text-green {
    color: #155724 !important;
}
.bg-secondary-subtle {
    background-color: #e2e3e5 !important;
}
.text-secondary {
    color: #41464b !important;
}
.border-primary-hover:hover {
    border-color: #0d6efd !important;
    background-color: rgba(13, 110, 253, 0.05) !important;
}
</style>