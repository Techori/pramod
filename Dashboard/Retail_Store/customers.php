<?php
// Include mock database
require_once 'database.php';

// Get data from database
$customers = get_customers();
$customer_analytics = get_customer_analytics();
$customer_segmentation = get_customer_segmentation();
$top_spending_categories = get_top_spending_categories();
$recent_activity = get_recent_activity();

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
$tab = isset($_GET['tab']) && in_array($_GET['tab'], ['all', 'retail', 'wholesale', 'contractors']) ? $_GET['tab'] : 'all';

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
        ($tab === 'contractors' && $customer['type'] === 'Contractor');
    return $matches_search && $matches_tab;
});

// Status badge function
function get_status_badge($status) {
    $status_config = [
        'Active' => ['class' => 'bg-green-subtle text-green', 'label' => 'Active'],
        'Inactive' => ['class' => 'bg-gray-subtle text-gray', 'label' => 'Inactive']
    ];
    $config = isset($status_config[$status]) ? $status_config[$status] : $status_config['Active'];
    return "<span class='badge {$config['class']}'>{$config['label']}</span>";
}

// Type badge function
function get_type_badge($type) {
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
                    <input
                        type="text"
                        name="search"
                        class="form-control border-start-0"
                        placeholder="Search customers..."
                        value="<?php echo htmlspecialchars($search_query); ?>"
                    >
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search me-1"></i> Search
                </button>
            </form>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <form method="POST" action="?page=customers" class="d-inline">
                <input type="hidden" name="action" value="add_customer">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-user-plus me-1"></i> Add Customer
                </button>
            </form>
            <form method="POST" action="?page=customers" class="d-inline">
                <input type="hidden" name="action" value="export_customers">
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-download me-1"></i> Export
                </button>
            </form>
            <div class="dropdown">
                <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <form method="POST" action="?page=customers">
                            <input type="hidden" name="action" value="filter_type">
                            <button type="submit" class="dropdown-item">By Customer Type</button>
                        </form>
                    </li>
                    <li>
                        <form method="POST" action="?page=customers">
                            <input type="hidden" name="action" value="filter_status">
                            <button type="submit" class="dropdown-item">By Status</button>
                        </form>
                    </li>
                    <li>
                        <form method="POST" action="?page=customers">
                            <input type="hidden" name="action" value="filter_purchase_date">
                            <button type="submit" class="dropdown-item">By Purchase Date</button>
                        </form>
                    </li>
                    <li>
                        <form method="POST" action="?page=customers">
                            <input type="hidden" name="action" value="filter_spend">
                            <button type="submit" class="dropdown-item">By Spend Amount</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Customer Analytics -->
    <div class="row row-cols-1 row-cols-md-4 g-4 mb-4">
        <div class="col">
            <div class="card stat-card card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Total Customers</h6>
                            <h3 class="fw-bold"><?php echo htmlspecialchars($customer_analytics['totalCustomers']); ?></h3>
                            <p class="text-xs text-muted mt-1">+12% from previous month</p>
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
                            <h3 class="fw-bold"><?php echo htmlspecialchars($customer_analytics['newThisMonth']); ?></h3>
                            <p class="text-xs text-muted mt-1">+5% from previous month</p>
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
                            <h3 class="fw-bold">₹<?php echo number_format($customer_analytics['avgPurchase'], 0); ?></h3>
                            <p class="text-xs text-muted mt-1">+3.2% from previous month</p>
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
                            <p class="text-xs text-muted mt-1">+8% from previous month</p>
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
                    <a href="?page=customers&tab=all" class="nav-link <?php echo $tab === 'all' ? 'active' : ''; ?>">All Customers</a>
                </li>
                <li class="nav-item">
                    <a href="?page=customers&tab=retail" class="nav-link <?php echo $tab === 'retail' ? 'active' : ''; ?>">Retail</a>
                </li>
                <li class="nav-item">
                    <a href="?page=customers&tab=wholesale" class="nav-link <?php echo $tab === 'wholesale' ? 'active' : ''; ?>">Wholesale</a>
                </li>
                <li class="nav-item">
                    <a href="?page=customers&tab=contractors" class="nav-link <?php echo $tab === 'contractors' ? 'active' : ''; ?>">Contractors</a>
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
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Type</th>
                            <th>Contact</th>
                            <th>Total Spent</th>
                            <th>Last Purchase</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($filtered_customers)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-users fa-2x text-muted"></i>
                                    <p class="mt-2 text-muted">No customers found matching your criteria.</p>
                                    <a href="?page=customers&tab=all" class="btn btn-outline-primary btn-sm">Clear Filters</a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($filtered_customers as $customer): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <?php echo htmlspecialchars($customer['name'][0]); ?>
                                        </div>
                                        <div>
                                            <p class="font-medium"><?php echo htmlspecialchars($customer['name']); ?></p>
                                            <p class="text-xs text-muted"><?php echo htmlspecialchars($customer['id']); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo get_type_badge($customer['type']); ?></td>
                                <td>
                                    <div class="space-y-1">
                                        <div class="d-flex align-items-center text-xs text-muted gap-1">
                                            <i class="fas fa-phone fa-xs"></i> <?php echo htmlspecialchars($customer['phone']); ?>
                                        </div>
                                        <div class="d-flex align-items-center text-xs text-muted gap-1">
                                            <i class="fas fa-envelope fa-xs"></i> <?php echo htmlspecialchars($customer['email']); ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="font-medium">₹<?php echo number_format($customer['totalSpent'], 0); ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-1 text-muted">
                                        <i class="fas fa-calendar-alt fa-xs"></i>
                                        <?php
                                            $date = new DateTime($customer['lastPurchase']);
                                            echo $date->format('M j, Y');
                                        ?>
                                    </div>
                                </td>
                                <td><?php echo get_status_badge($customer['status']); ?></td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <form method="POST" action="?page=customers" class="d-inline">
                                            <input type="hidden" name="action" value="view_customer">
                                            <input type="hidden" name="customer_name" value="<?php echo htmlspecialchars($customer['name']); ?>">
                                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="?page=customers" class="d-inline">
                                            <input type="hidden" name="action" value="edit_customer">
                                            <input type="hidden" name="customer_name" value="<?php echo htmlspecialchars($customer['name']); ?>">
                                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="?page=customers" class="d-inline">
                                            <input type="hidden" name="action" value="new_sale">
                                            <input type="hidden" name="customer_name" value="<?php echo htmlspecialchars($customer['name']); ?>">
                                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-shopping-bag"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php elseif ($tab === 'retail' || $tab === 'wholesale' || $tab === 'contractors'): ?>
    <div class="card card-border shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="mb-3"><?php echo htmlspecialchars(ucfirst($tab)); ?> Customers</h5>
            <p class="text-muted mb-3">Manage <?php echo htmlspecialchars($tab); ?> customers</p>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Total Spent</th>
                            <th>Last Purchase</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($filtered_customers)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-users fa-2x text-muted"></i>
                                    <p class="mt-2 text-muted">No <?php echo htmlspecialchars($tab); ?> customers found.</p>
                                    <a href="?page=customers&tab=<?php echo htmlspecialchars($tab); ?>" class="btn btn-outline-primary btn-sm">Clear Filters</a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($filtered_customers as $customer): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <?php echo htmlspecialchars($customer['name'][0]); ?>
                                        </div>
                                        <div>
                                            <p class="font-medium"><?php echo htmlspecialchars($customer['name']); ?></p>
                                            <p class="text-xs text-muted"><?php echo htmlspecialchars($customer['id']); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="space-y-1">
                                        <div class="d-flex align-items-center text-xs text-muted gap-1">
                                            <i class="fas fa-phone fa-xs"></i> <?php echo htmlspecialchars($customer['phone']); ?>
                                        </div>
                                        <div class="d-flex align-items-center text-xs text-muted gap-1">
                                            <i class="fas fa-envelope fa-xs"></i> <?php echo htmlspecialchars($customer['email']); ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="font-medium">₹<?php echo number_format($customer['totalSpent'], 0); ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-1 text-muted">
                                        <i class="fas fa-calendar-alt fa-xs"></i>
                                        <?php
                                            $date = new DateTime($customer['lastPurchase']);
                                            echo $date->format('M j, Y');
                                        ?>
                                    </div>
                                </td>
                                <td><?php echo get_status_badge($customer['status']); ?></td>
                                <td class="text-end">
                                    <form method="POST" action="?page=customers" class="d-inline">
                                        <input type="hidden" name="action" value="new_sale">
                                        <input type="hidden" name="customer_name" value="<?php echo htmlspecialchars($customer['name']); ?>">
                                        <button type="submit" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-shopping-bag me-1"></i> New Sale
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Customer Analytics -->
    <div class="row row-cols-1 row-cols-lg-2 g-4 mb-4">
        <div class="col">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Customer Segmentation</h5>
                    <div class="bg-light rounded py-5 text-center">
                        <i class="fas fa-chart-bar fa-2x text-muted me-2"></i>
                        <span class="text-muted">Customer Segmentation Chart Placeholder</span>
                    </div>
                    <div class="row row-cols-1 row-cols-md-2 g-4 mt-4">
                        <div class="col">
                            <h6 class="text-sm font-medium mb-2">Customer Types</h6>
                            <div class="space-y-2">
                                <?php foreach ($customer_segmentation as $segment): ?>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-sm"><?php echo htmlspecialchars($segment['type']); ?></span>
                                        <span class="text-sm font-medium"><?php echo htmlspecialchars($segment['percentage']); ?>%</span>
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
                                        <span class="text-sm font-medium"><?php echo htmlspecialchars($category['percentage']); ?>%</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Top Customers</h5>
                    <div class="space-y-3">
                        <?php
                            // Sort customers by totalSpent and get top 5
                            usort($customers, function ($a, $b) {
                                return $b['totalSpent'] - $a['totalSpent'];
                            });
                            $top_customers = array_slice($customers, 0, 5);
                            foreach ($top_customers as $index => $customer):
                        ?>
                            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="d-flex align-items-center justify-content-center w-8 h-8 rounded-circle bg-primary text-white font-medium">
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
                    <div class="mt-4 pt-4 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-sm font-medium">Recent Activity</h6>
                            <form method="POST" action="?page=customers" class="d-inline">
                                <input type="hidden" name="action" value="view_all_activity">
                                <button type="submit" class="btn btn-link btn-sm text-primary">View All</button>
                            </form>
                        </div>
                        <div class="space-y-3">
                            <?php foreach ($recent_activity as $activity): ?>
                                <div class="text-xs p-2 border-l-4 <?php echo htmlspecialchars($activity['borderColor']); ?>">
                                    <p class="text-muted">
                                        <?php
                                            $date = new DateTime($activity['timestamp']);
                                            echo $date->format('M j, Y, h:i A');
                                        ?>
                                    </p>
                                    <p class="font-medium"><?php echo htmlspecialchars($activity['message']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.space-y-2 > * + * {
    margin-top: 0.5rem;
}
.space-y-3 > * + * {
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