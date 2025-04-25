<?php
// Include mock database
require_once 'database.php';

// Get deliveries from database
$deliveries = get_deliveries();

// Handle form submissions
$success_message = '';
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['track_delivery'])) {
        $tracking_id = trim($_POST['tracking_id'] ?? '');
        if (empty($tracking_id)) {
            $error_message = 'Please enter a valid tracking ID';
        } else {
            $delivery = array_filter($deliveries, function ($d) use ($tracking_id) {
                return $d['trackingId'] === $tracking_id;
            });
            if ($delivery) {
                $delivery = reset($delivery);
                $success_message = "Tracking information for {$tracking_id}: Status - {$delivery['status']}, Last Update - {$delivery['lastUpdate']}";
            } else {
                $error_message = "No delivery found for tracking ID {$tracking_id}";
            }
        }
    } elseif (isset($_POST['confirm_delivery'])) {
        $delivery_id = trim($_POST['delivery_id'] ?? '');
        if (empty($delivery_id)) {
            $error_message = 'Please enter a valid delivery ID';
        } else {
            $result = confirm_delivery($delivery_id);
            if ($result['success']) {
                $success_message = $result['message'];
                $deliveries = get_deliveries(); // Refresh deliveries
            } else {
                $error_message = $result['message'];
            }
        }
    } elseif (isset($_POST['confirm_action'])) {
        $delivery_id = trim($_POST['delivery_id'] ?? '');
        if (empty($delivery_id)) {
            $error_message = 'Invalid delivery ID';
        } else {
            $result = confirm_delivery($delivery_id);
            if ($result['success']) {
                $success_message = $result['message'];
                $deliveries = get_deliveries(); // Refresh deliveries
            } else {
                $error_message = $result['message'];
            }
        }
    }
}

// Filter and search parameters
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) && in_array($_GET['status'], ['All', 'Processing', 'In Transit', 'Out for Delivery', 'Delivered']) ? $_GET['status'] : 'All';
$factory_filter = isset($_GET['factory']) ? trim($_GET['factory']) : 'All Factories';

// Get unique factory names
$factories = array_unique(array_column($deliveries, 'factoryName'));
$factories = array_merge(['All Factories'], $factories);

// Filter deliveries
$filtered_deliveries = array_filter($deliveries, function ($delivery) use ($search_query, $status_filter, $factory_filter) {
    $matches_search = empty($search_query) ||
        stripos($delivery['id'], $search_query) !== false ||
        stripos($delivery['trackingId'], $search_query) !== false ||
        stripos($delivery['orderId'], $search_query) !== false;
    $matches_status = $status_filter === 'All' || $delivery['status'] === $status_filter;
    $matches_factory = $factory_filter === 'All Factories' || $delivery['factoryName'] === $factory_filter;
    return $matches_search && $matches_status && $matches_factory;
});

// Statuses for filter
$statuses = ['All', 'Processing', 'In Transit', 'Out for Delivery', 'Delivered'];
?>

<h4><i class="fas fa-truck text-primary"></i> Deliveries (<?php echo count($filtered_deliveries); ?>)</h4>
<p>Track and manage your deliveries from factories.</p>

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

<!-- Tracking Section -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Track Delivery</h5>
        <p class="text-muted">Enter a tracking ID to check delivery status</p>
        <form method="POST" action="?page=deliveries" class="d-flex flex-column flex-md-row gap-3">
            <input type="hidden" name="track_delivery" value="1">
            <div class="flex-grow-1">
                <input
                    type="text"
                    name="tracking_id"
                    class="form-control"
                    placeholder="Enter tracking ID (e.g., TR124578965)"
                    required
                >
            </div>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-search me-1"></i> Track Order
            </button>
        </form>
    </div>
</div>

<!-- Filters and Search -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex flex-column flex-md-row gap-3 align-items-md-end">
            <!-- Search -->
            <div class="flex-grow-1">
                <label class="form-label text-muted">Search Deliveries</label>
                <form method="GET" action="?page=deliveries" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="page" value="deliveries">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                        <input
                            type="text"
                            name="search"
                            class="form-control border-start-0"
                            placeholder="Search by delivery ID, tracking ID or order ID..."
                            value="<?php echo htmlspecialchars($search_query); ?>"
                        >
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                </form>
            </div>
            <!-- Filters -->
            <div class="d-flex flex-column gap-3">
                <!-- Status Filter -->
                <div>
                    <label class="form-label text-muted">Status</label>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($statuses as $status): ?>
                        <a href="?page=deliveries&status=<?php echo urlencode($status); ?>&factory=<?php echo urlencode($factory_filter); ?>&search=<?php echo urlencode($search_query); ?>">
                            <span class="badge <?php echo $status_filter === $status ? 'bg-primary text-white' : 'bg-light text-dark'; ?> px-3 py-1 rounded-pill">
                                <?php echo htmlspecialchars($status); ?>
                            </span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!-- Factory Filter -->
                <div>
                    <label class="form-label text-muted">Factory</label>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($factories as $factory): ?>
                        <a href="?page=deliveries&status=<?php echo urlencode($status_filter); ?>&factory=<?php echo urlencode($factory); ?>&search=<?php echo urlencode($search_query); ?>">
                            <span class="badge <?php echo $factory_filter === $factory ? 'bg-primary text-white' : 'bg-light text-dark'; ?> px-3 py-1 rounded-pill">
                                <?php echo htmlspecialchars($factory); ?>
                            </span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <!-- Clear Filters -->
            <div>
                <a href="?page=deliveries" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-filter me-1"></i> Clear Filters
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Deliveries Table -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Recent Deliveries</h5>
        <p class="text-muted">Track your recent and ongoing deliveries from factories</p>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Delivery ID</th>
                        <th>Tracking ID</th>
                        <th>Order ID</th>
                        <th>Factory</th>
                        <th>Estimated Delivery</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($filtered_deliveries)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-truck fa-2x text-muted"></i>
                                <p class="mt-2 text-muted">No deliveries found matching your criteria.</p>
                                <a href="?page=deliveries" class="btn btn-outline-primary btn-sm">Clear Filters</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($filtered_deliveries as $delivery): ?>
                        <tr>
                            <td><a href="#" class="text-primary"><?php echo htmlspecialchars($delivery['id']); ?></a></td>
                            <td><?php echo htmlspecialchars($delivery['trackingId']); ?></td>
                            <td><?php echo htmlspecialchars($delivery['orderId']); ?></td>
                            <td><?php echo htmlspecialchars($delivery['factoryName']); ?></td>
                            <td>
                                <?php
                                    $date = new DateTime($delivery['estimatedDelivery']);
                                    echo $date->format('M j, Y');
                                ?>
                            </td>
                            <td>
                                <span class="badge <?php
                                    echo $delivery['status'] === 'Delivered' ? 'bg-success' :
                                        ($delivery['status'] === 'In Transit' ? 'bg-primary' :
                                        ($delivery['status'] === 'Out for Delivery' ? 'bg-warning' : 'bg-purple'));
                                ?> text-white">
                                    <i class="fas <?php
                                        echo $delivery['status'] === 'Delivered' ? 'fa-check-circle' :
                                            ($delivery['status'] === 'In Transit' ? 'fa-truck' :
                                            ($delivery['status'] === 'Out for Delivery' ? 'fa-truck-loading' : 'fa-sync-alt'));
                                    ?> me-1"></i>
                                    <?php echo htmlspecialchars($delivery['status']); ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <button
                                        class="btn btn-outline-primary btn-sm"
                                        title="View"
                                        onclick="alert('Viewing details for delivery <?php echo htmlspecialchars($delivery['id']); ?>: <?php echo htmlspecialchars($delivery['lastUpdate']); ?>')"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if ($delivery['status'] === 'In Transit' || $delivery['status'] === 'Out for Delivery'): ?>
                                        <form method="POST" action="?page=deliveries" class="d-inline">
                                            <input type="hidden" name="confirm_action" value="1">
                                            <input type="hidden" name="delivery_id" value="<?php echo htmlspecialchars($delivery['id']); ?>">
                                            <button
                                                type="submit"
                                                class="btn btn-outline-success btn-sm"
                                                title="Confirm Delivery"
                                            >
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
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

<!-- Delivery Statistics -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Pending Deliveries</h5>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="h3 font-weight-bold">
                            <?php
                                echo count(array_filter($deliveries, function ($d) {
                                    return $d['status'] !== 'Delivered';
                                }));
                            ?>
                        </p>
                        <p class="text-muted">Awaiting delivery</p>
                    </div>
                    <div class="p-3 bg-warning bg-opacity-10 rounded-circle">
                        <i class="fas fa-truck text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Items In Transit</h5>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="h3 font-weight-bold">
                            <?php
                                echo array_sum(array_map(function ($d) {
                                    return in_array($d['status'], ['In Transit', 'Out for Delivery']) ? $d['items'] : 0;
                                }, $deliveries));
                            ?>
                        </p>
                        <p class="text-muted">Currently in transit</p>
                    </div>
                    <div class="p-3 bg-primary bg-opacity-10 rounded-circle">
                        <i class="fas fa-box text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">On-Time Delivery Rate</h5>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="h3 font-weight-bold">95%</p>
                        <p class="text-muted">Last 30 days</p>
                    </div>
                    <div class="p-3 bg-success bg-opacity-10 rounded-circle">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delivery Confirmation Form -->
<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title">Confirm Delivery Receipt</h5>
        <p class="text-muted">Manually confirm a delivery that has been received</p>
        <div class="row">
            <div class="col-md-6">
                <form method="POST" action="?page=deliveries">
                    <input type="hidden" name="confirm_delivery" value="1">
                    <div class="mb-3">
                        <label for="deliveryId" class="form-label">Delivery ID</label>
                        <input
                            type="text"
                            class="form-control"
                            id="deliveryId"
                            name="delivery_id"
                            placeholder="Enter Delivery ID (e.g., DEL-2025-001)"
                            required
                        >
                    </div>
                    <div class="mb-3">
                        <label for="trackingId" class="form-label">Tracking ID</label>
                        <input
                            type="text"
                            class="form-control"
                            id="trackingId"
                            name="tracking_id"
                            placeholder="Enter Tracking ID"
                        >
                    </div>
                    <div class="mb-3">
                        <label for="receivedDate" class="form-label">Date Received</label>
                        <input
                            type="date"
                            class="form-control"
                            id="receivedDate"
                            name="received_date"
                            value="<?php echo date('Y-m-d'); ?>"
                            required
                        >
                    </div>
                    <div class="mb-3">
                        <label for="receivedBy" class="form-label">Received By</label>
                        <input
                            type="text"
                            class="form-control"
                            id="receivedBy"
                            name="received_by"
                            placeholder="Name of person who received the delivery"
                            required
                        >
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-check-circle me-1"></i> Confirm Receipt
                    </button>
                </form>
            </div>
            <div class="col-md-6 border-start ps-4 d-none d-md-block">
                <h5 class="font-weight-bold">Delivery Confirmation Guidelines</h5>
                <ul class="list-unstyled mt-3">
                    <li class="d-flex align-items-start mb-2">
                        <i class="fas fa-clipboard-list text-primary me-2 mt-1"></i>
                        <span>Check all items against the delivery note before confirming.</span>
                    </li>
                    <li class="d-flex align-items-start mb-2">
                        <i class="fas fa-clipboard-list text-primary me-2 mt-1"></i>
                        <span>Report any damages or discrepancies immediately.</span>
                    </li>
                    <li class="d-flex align-items-start mb-2">
                        <i class="fas fa-clipboard-list text-primary me-2 mt-1"></i>
                        <span>Keep delivery notes and packaging until quality is verified.</span>
                    </li>
                    <li class="d-flex align-items-start mb-2">
                        <i class="fas fa-clipboard-list text-primary me-2 mt-1"></i>
                        <span>Confirmation must be done within 24 hours of receipt.</span>
                    </li>
                </ul>
                <div class="p-3 bg-warning bg-opacity-10 border border-warning rounded mt-4">
                    <p class="font-weight-bold text-warning">Important Notice</p>
                    <p class="text-muted small">
                        Once a delivery is confirmed as received, it cannot be disputed for missing items. Please ensure a thorough check before confirmation.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-purple {
    background-color: #6f42c1;
}
.text-purple {
    color: #6f42c1;
}
.btn-outline-purple {
    border-color: #6f42c1;
    color: #6f42c1;
}
.btn-outline-purple:hover {
    background-color: #6f42c1;
    color: #fff;
}
.badge {
    font-size: 0.85rem;
    padding: 4px 8px;
}
</style>