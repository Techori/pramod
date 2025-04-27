<?php
// Include mock database
require_once 'database.php';

// Get data from database
$inventory_items = get_inventory_items();
$inventory_categories = get_inventory_categories();
$inventory_analytics = get_inventory_analytics();
$inventory_stats = get_inventory_stats();
$inventory_activities = get_inventory_activities();

// Handle actions
$success_message = '';
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $item_name = isset($_POST['item_name']) ? $_POST['item_name'] : '';
        switch ($action) {
            case 'add_item':
                $success_message = 'Add New Item operation initiated successfully.';
                break;
            case 'request_stock':
                $success_message = 'Request Stock operation initiated successfully.';
                break;
            case 'export_inventory':
                $success_message = 'Export Inventory operation initiated successfully.';
                break;
            case 'update_item':
                $success_message = 'Update Item operation initiated successfully.';
                break;
            case 'generate_report':
                $success_message = 'Generate Report operation initiated successfully.';
                break;
            case 'delete_item':
                $success_message = 'Delete Item operation initiated successfully.';
                break;
            case 'order_item':
                $success_message = "Order $item_name operation initiated successfully.";
                break;
            case 'view_low_stock':
                $success_message = 'View Low Stock operation initiated successfully.';
                break;
        }
    }
}

// Search and filter parameters
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) && in_array($_GET['category'], $inventory_categories) ? $_GET['category'] : 'All Categories';
$tab = isset($_GET['tab']) && in_array($_GET['tab'], ['all', 'low', 'out']) ? $_GET['tab'] : 'all';

// Filter inventory items
$filtered_items = array_filter($inventory_items, function ($item) use ($search_query, $category_filter, $tab) {
    $matches_search = empty($search_query) ||
        stripos($item['id'], $search_query) !== false ||
        stripos($item['name'], $search_query) !== false ||
        stripos($item['category'], $search_query) !== false;
    $matches_category = $category_filter === 'All Categories' || $item['category'] === $category_filter;
    $matches_tab = $tab === 'all' || ($tab === 'low' && $item['status'] === 'Low Stock') || ($tab === 'out' && $item['stock'] == 0);
    return $matches_search && $matches_category && $matches_tab;
});
?>

<div class="main-content">
    <h1><i class="fas fa-warehouse text-primary me-2"></i> Store Inventory Management</h1>
    <p class="text-muted">Manage and track your retail store inventory</p>

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
            <form method="GET" action="?page=inventory" class="d-flex align-items-center gap-2">
                <input type="hidden" name="page" value="inventory">
                <input type="hidden" name="tab" value="<?php echo htmlspecialchars($tab); ?>">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input
                        type="text"
                        name="search"
                        class="form-control border-start-0"
                        placeholder="Search inventory..."
                        value="<?php echo htmlspecialchars($search_query); ?>"
                    >
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search me-1"></i> Search
                </button>
            </form>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <form method="POST" action="?page=inventory" class="d-inline">
                <input type="hidden" name="action" value="add_item">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle me-1"></i> Add New Item
                </button>
            </form>
            <form method="POST" action="?page=inventory" class="d-inline">
                <input type="hidden" name="action" value="request_stock">
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-truck me-1"></i> Request Stock
                </button>
            </form>
            <form method="POST" action="?page=inventory" class="d-inline">
                <input type="hidden" name="action" value="export_inventory">
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-download me-1"></i> Export
                </button>
            </form>
        </div>
    </div>

    <!-- Tabs and Filters -->
    <div class="card card-border shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <ul class="nav nav-tabs mb-3 mb-md-0">
                    <li class="nav-item">
                        <a href="?page=inventory&tab=all" class="nav-link <?php echo $tab === 'all' ? 'active' : ''; ?>">All Items</a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=inventory&tab=low" class="nav-link <?php echo $tab === 'low' ? 'active' : ''; ?>">Low Stock</a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=inventory&tab=out" class="nav-link <?php echo $tab === 'out' ? 'active' : ''; ?>">Out of Stock</a>
                    </li>
                </ul>
                <div class="d-flex flex-wrap gap-2">
                    <form method="GET" action="?page=inventory" class="d-flex align-items-center gap-2">
                        <input type="hidden" name="page" value="inventory">
                        <input type="hidden" name="tab" value="<?php echo htmlspecialchars($tab); ?>">
                        <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                            <?php foreach ($inventory_categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category); ?>" <?php echo $category === $category_filter ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-filter"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Tables -->
    <?php if ($tab === 'all'): ?>
    <div class="card card-border shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="mb-3">Inventory Items</h5>
            <p class="text-muted mb-3">Manage all your store inventory items</p>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Last Updated</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($filtered_items)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-warehouse fa-2x text-muted"></i>
                                    <p class="mt-2 text-muted">No inventory items found matching your criteria.</p>
                                    <a href="?page=inventory&tab=all" class="btn btn-outline-primary btn-sm">Clear Filters</a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($filtered_items as $item): ?>
                            <tr>
                                <td class="font-medium"><?php echo htmlspecialchars($item['id']); ?></td>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo htmlspecialchars($item['category']); ?></td>
                                <td><?php echo htmlspecialchars($item['stock']); ?></td>
                                <td><?php echo htmlspecialchars($item['price']); ?></td>
                                <td>
                                    <?php
                                        $date = new DateTime($item['lastUpdated']);
                                        echo $date->format('M j, Y');
                                    ?>
                                </td>
                                <td>
                                    <span class="badge <?php echo $item['status'] === 'In Stock' ? 'bg-success text-white' : 'bg-danger text-white'; ?>">
                                        <?php if ($item['status'] !== 'In Stock'): ?>
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                        <?php endif; ?>
                                        <?php echo htmlspecialchars($item['status']); ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <form method="POST" action="?page=inventory" class="d-inline">
                                            <input type="hidden" name="action" value="update_item">
                                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="?page=inventory" class="d-inline">
                                            <input type="hidden" name="action" value="generate_report">
                                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-file-alt"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="?page=inventory" class="d-inline">
                                            <input type="hidden" name="action" value="delete_item">
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash"></i>
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
    <?php elseif ($tab === 'low'): ?>
    <div class="card card-border shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="mb-3">Low Stock Items</h5>
            <p class="text-muted mb-3">Items that need to be restocked soon</p>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                            <th>Reorder Point</th>
                            <th>Price</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($filtered_items)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-warehouse fa-2x text-muted"></i>
                                    <p class="mt-2 text-muted">No low stock items found.</p>
                                    <a href="?page=inventory&tab=low" class="btn btn-outline-primary btn-sm">Clear Filters</a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($filtered_items as $item): ?>
                            <tr>
                                <td class="font-medium"><?php echo htmlspecialchars($item['id']); ?></td>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo htmlspecialchars($item['category']); ?></td>
                                <td class="text-danger font-medium"><?php echo htmlspecialchars($item['stock']); ?></td>
                                <td>25</td>
                                <td><?php echo htmlspecialchars($item['price']); ?></td>
                                <td class="text-end">
                                    <form method="POST" action="?page=inventory" class="d-inline">
                                        <input type="hidden" name="action" value="order_item">
                                        <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($item['name']); ?>">
                                        <button type="submit" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-truck me-1"></i> Order Now
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
    <?php elseif ($tab === 'out'): ?>
    <div class="card card-border shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="mb-3">Out of Stock Items</h5>
            <p class="text-muted mb-3">Items that need immediate attention</p>
            <div class="text-center py-5 bg-light rounded">
                <i class="fas fa-box fa-3x text-muted mb-3"></i>
                <h5>No Out of Stock Items</h5>
                <p class="text-muted mb-3">All products currently have some inventory.</p>
                <form method="POST" action="?page=inventory" class="d-inline">
                    <input type="hidden" name="action" value="view_low_stock">
                    <button type="submit" class="btn btn-outline-primary btn-sm">
                        View Low Stock Items
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Stock Level Summary -->
    <div class="row row-cols-1 row-cols-lg-2 g-4 mb-4">
        <div class="col">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Stock Level Summary</h5>
                    <div class="space-y-4">
                        <?php foreach ($inventory_analytics as $category): ?>
                            <div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-sm font-medium"><?php echo htmlspecialchars($category['name']); ?></span>
                                    <span class="text-sm font-medium <?php
                                        echo $category['percent'] < 25 ? 'text-danger' :
                                            ($category['percent'] < 50 ? 'text-warning' : 'text-success');
                                    ?>">
                                        <?php echo htmlspecialchars($category['stock']); ?> units
                                    </span>
                                </div>
                                <div class="progress bg-light h-2">
                                    <div class="progress-bar <?php
                                        echo $category['percent'] < 25 ? 'bg-danger' :
                                            ($category['percent'] < 50 ? 'bg-warning' : 'bg-success');
                                    ?>" style="width: <?php echo htmlspecialchars($category['percent']); ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Inventory Statistics</h5>
                    <div class="row row-cols-1 row-cols-md-2 g-4">
                        <div class="col">
                            <div class="bg-primary-subtle p-3 rounded">
                                <h6 class="text-primary text-sm font-medium mb-1">Total Items</h6>
                                <p class="fs-4 font-bold"><?php echo htmlspecialchars($inventory_stats['totalItems']); ?></p>
                                <p class="text-xs text-muted mt-1">Across 7 categories</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="bg-warning-subtle p-3 rounded">
                                <h6 class="text-warning text-sm font-medium mb-1">Low Stock Items</h6>
                                <p class="fs-4 font-bold"><?php echo htmlspecialchars($inventory_stats['lowStockItems']); ?></p>
                                <p class="text-xs text-muted mt-1">Need reordering soon</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="bg-success-subtle p-3 rounded">
                                <h6 class="text-success text-sm font-medium mb-1">Items in Transit</h6>
                                <p class="fs-4 font-bold"><?php echo htmlspecialchars($inventory_stats['itemsInTransit']); ?></p>
                                <p class="text-xs text-muted mt-1">Arriving this week</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="bg-purple-subtle p-3 rounded">
                                <h6 class="text-purple text-sm font-medium mb-1">Inventory Value</h6>
                                <p class="fs-4 font-bold"><?php echo htmlspecialchars($inventory_stats['inventoryValue']); ?></p>
                                <p class="text-xs text-muted mt-1">At retail prices</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-top">
                        <h6 class="text-sm font-medium mb-3">Inventory Movements (Last 7 Days)</h6>
                        <div class="d-flex align-items-center justify-content-center bg-light rounded py-5">
                            <i class="fas fa-chart-bar fa-2x text-muted me-2"></i>
                            <span class="text-muted">Inventory Movement Chart Placeholder</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Inventory Activities -->
    <div class="card card-border shadow-sm">
        <div class="card-body p-4">
            <h5 class="mb-3">Recent Inventory Activities</h5>
            <div class="space-y-4">
                <?php foreach ($inventory_activities as $activity): ?>
                    <div class="d-flex align-items-start gap-3 p-3 rounded <?php echo htmlspecialchars($activity['bgColor']); ?>">
                        <div class="p-2 rounded-circle">
                            <i class="fas <?php echo htmlspecialchars($activity['icon']); ?> fa-lg <?php echo htmlspecialchars($activity['iconColor']); ?>"></i>
                        </div>
                        <div>
                            <p class="font-medium"><?php echo htmlspecialchars($activity['type']); ?></p>
                            <p class="text-sm text-muted"><?php echo htmlspecialchars($activity['message']); ?></p>
                            <p class="text-xs text-muted mt-1">
                                <?php
                                    $date = new DateTime($activity['timestamp']);
                                    echo $date->format('M j, Y • h:i A');
                                ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
.space-y-4 > * + * {
    margin-top: 1rem;
}
.text-sm {
    font-size: 0.875rem;
}
.fs-4 {
    font-size: 1.5rem;
}
.font-medium {
    font-weight: 500;
}
.font-bold {
    font-weight: 700;
}
.h-2 {
    height: 0.5rem;
}
.bg-purple-subtle {
    background-color: #6f42c1 !important;
    color: #fff;
}
.text-purple {
    color: #6f42c1;
}
</style>