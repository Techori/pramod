<?php
// Include mock database
require_once 'database.php';

// Get data from database
$supply_requests = get_supply_requests();
$low_stock_items = get_low_stock_items();
$popular_products = get_popular_products();
$supply_analytics = get_supply_analytics();

// Handle actions
$success_message = '';
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        if ($action === 'create_order') {
            $success_message = 'Creating new store supply request...';
        } elseif ($action === 'track_shipment' && isset($_POST['request_id'])) {
            $success_message = "Tracking supply request {$_POST['request_id']}";
        } elseif ($action === 'refresh') {
            $success_message = 'Refreshing supply requests...';
        } elseif ($action === 'view_all') {
            $success_message = 'Viewing all supply requests...';
        } elseif ($action === 'request_low_stock') {
            $success_message = 'Requesting low stock items...';
        } elseif ($action === 'view_full_report') {
            $success_message = 'Viewing full popular products report...';
        }
    }
}

// Search and filter parameters
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) && in_array($_GET['status'], ['All', 'Delivered', 'In Transit', 'Ordered']) ? $_GET['status'] : 'All';

// Filter supply requests
$filtered_requests = array_filter($supply_requests, function ($request) use ($search_query, $status_filter) {
    $matches_search = empty($search_query) ||
        stripos($request['id'], $search_query) !== false ||
        stripos($request['item'], $search_query) !== false ||
        stripos($request['source'], $search_query) !== false;
    $matches_status = $status_filter === 'All' || $request['status'] === $status_filter;
    return $matches_search && $matches_status;
});
?>

<div class="main-content">
    <h1><i class="fas fa-boxes text-primary me-2"></i> Store Supply Management</h1>
    <p class="text-muted">Track and manage inventory supplies for the retail store</p>

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
            <form method="GET" action="?page=supply" class="d-flex align-items-center gap-2">
                <input type="hidden" name="page" value="supply">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input
                        type="text"
                        name="search"
                        class="form-control border-start-0"
                        placeholder="Search products, orders, suppliers..."
                        value="<?php echo htmlspecialchars($search_query); ?>"
                    >
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search me-1"></i> Search
                </button>
            </form>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <form method="POST" action="?page=supply" class="d-inline">
                <input type="hidden" name="action" value="filter">
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
            </form>
            <form method="POST" action="?page=supply" class="d-inline">
                <input type="hidden" name="action" value="create_order">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle me-1"></i> Request Items
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row row-cols-1 row-cols-md-4 g-4 mb-4">
        <div class="col">
            <div class="card stat-card card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Pending Requests</h6>
                            <h3 class="fw-bold"><?php echo htmlspecialchars($supply_analytics['pendingRequests']); ?></h3>
                        </div>
                        <i class="fas fa-box fa-2x text-primary opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card card-border shadow-sm" style="border-left: 5px solid #ffc107;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">In Transit</h6>
                            <h3 class="fw-bold"><?php echo htmlspecialchars($supply_analytics['inTransit']); ?></h3>
                        </div>
                        <i class="fas fa-truck fa-2x text-warning opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card card-border shadow-sm" style="border-left: 5px solid #198754;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Received This Week</h6>
                            <h3 class="fw-bold"><?php echo htmlspecialchars($supply_analytics['receivedThisWeek']); ?></h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x text-success opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card card-border shadow-sm" style="border-left: 5px solid #dc3545;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Low Stock Items</h6>
                            <h3 class="fw-bold"><?php echo htmlspecialchars($supply_analytics['lowStockItems']); ?></h3>
                        </div>
                        <i class="fas fa-exclamation-circle fa-2x text-danger opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Supply Requests -->
    <div class="card card-border shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Recent Supply Requests</h5>
                <div class="d-flex gap-2">
                    <form method="POST" action="?page=supply" class="d-inline">
                        <input type="hidden" name="action" value="refresh">
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-sync-alt me-1"></i> Refresh
                        </button>
                    </form>
                    <form method="POST" action="?page=supply" class="d-inline">
                        <input type="hidden" name="action" value="view_all">
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            View All
                        </button>
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Source</th>
                            <th>Delivery Date</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($filtered_requests)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-boxes fa-2x text-muted"></i>
                                    <p class="mt-2 text-muted">No supply requests found matching your criteria.</p>
                                    <a href="?page=supply" class="btn btn-outline-primary btn-sm">Clear Filters</a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($filtered_requests as $request): ?>
                            <tr>
                                <td class="font-medium"><?php echo htmlspecialchars($request['id']); ?></td>
                                <td><?php echo htmlspecialchars($request['item']); ?></td>
                                <td><?php echo htmlspecialchars($request['quantity']); ?></td>
                                <td><?php echo htmlspecialchars($request['source']); ?></td>
                                <td>
                                    <?php
                                        $date = new DateTime($request['date']);
                                        echo $date->format('M j, Y');
                                    ?>
                                </td>
                                <td>
                                    <span class="badge <?php
                                        echo $request['status'] === 'Delivered' ? 'bg-success text-white' :
                                            ($request['status'] === 'In Transit' ? 'bg-primary text-white' : 'bg-warning text-dark');
                                    ?>">
                                        <?php echo htmlspecialchars($request['status']); ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <form method="POST" action="?page=supply" class="d-inline">
                                        <input type="hidden" name="action" value="track_shipment">
                                        <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request['id']); ?>">
                                        <button type="submit" class="btn btn-outline-primary btn-sm">
                                            <?php echo $request['status'] !== 'Delivered' ? 'Track' : 'View'; ?>
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

    <!-- Stock Insights -->
    <div class="row row-cols-1 row-cols-md-2 g-4">
        <!-- Low Stock Alert -->
        <div class="col">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3"><i class="fas fa-exclamation-circle text-warning me-2"></i> Low Stock Alert</h5>
                    <div class="space-y-4">
                        <?php foreach ($low_stock_items as $item): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="font-medium"><?php echo htmlspecialchars($item['item']); ?></span>
                                <span class="<?php echo $item['level'] === 'Critical' ? 'text-danger' : 'text-warning'; ?> font-medium">
                                    <?php echo htmlspecialchars($item['level']); ?> (<?php echo htmlspecialchars($item['stock']); ?> left)
                                </span>
                            </div>
                        <?php endforeach; ?>
                        <form method="POST" action="?page=supply" class="d-block">
                            <input type="hidden" name="action" value="request_low_stock">
                            <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                                Request Low Stock Items
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Popular Products -->
        <div class="col">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3"><i class="fas fa-shopping-cart text-primary me-2"></i> Popular Products</h5>
                    <p class="text-muted mb-3">Most requested items this month</p>
                    <div class="space-y-3">
                        <?php foreach ($popular_products as $product): ?>
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-sm font-medium"><?php echo htmlspecialchars($product['item']); ?></span>
                                    <span class="text-sm text-muted"><?php echo htmlspecialchars($product['quantity']); ?></span>
                                </div>
                                <div class="progress bg-light h-2">
                                    <div class="progress-bar bg-primary" style="width: <?php echo htmlspecialchars($product['percentage']); ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <form method="POST" action="?page=supply" class="d-block mt-4">
                        <input type="hidden" name="action" value="view_full_report">
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                            View Full Report
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.space-y-4 > * + * {
    margin-top: 1rem;
}
.space-y-3 > * + * {
    margin-top: 0.75rem;
}
.text-sm {
    font-size: 0.875rem;
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
</style>