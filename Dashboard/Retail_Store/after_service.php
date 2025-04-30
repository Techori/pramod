<?php
// Include mock database
require_once 'database.php';

// Get recent requests (mock data)
$recent_requests = [
    [
        'id' => '#SR-001',
        'description' => 'Fan Repair',
        'status' => 'In Progress',
        'date' => '23 Apr 2025',
    ],
    [
        'id' => '#WC-002',
        'description' => 'LED Bulb Replacement',
        'status' => 'Completed',
        'date' => '21 Apr 2025',
    ],
];

// Handle form submissions
$success_message = '';
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type'])) {
    $form_type = $_POST['form_type'];
    switch ($form_type) {
        case 'service_request':
            $success_message = 'Your service request has been submitted successfully.';
            break;
        case 'warranty_claim':
            $success_message = 'Your warranty claim has been submitted successfully.';
            break;
        case 'support_message':
            $success_message = 'Your support message has been submitted successfully.';
            break;
        case 'track_request':
            $success_message = 'Track request initiated successfully.';
            break;
        case 'start_chat':
            $success_message = 'Live chat initiated successfully.';
            break;
    }
}

// Get active tab
$active_tab = isset($_GET['tab']) && in_array($_GET['tab'], ['service', 'warranty', 'support', 'tracking']) ? $_GET['tab'] : 'service';

// Status badge function
function get_status_badge($status) {
    $status_config = [
        'In Progress' => ['class' => 'bg-yellow-subtle text-yellow', 'label' => 'In Progress'],
        'Completed' => ['class' => 'bg-green-subtle text-green', 'label' => 'Completed'],
    ];
    $config = isset($status_config[$status]) ? $status_config[$status] : ['class' => 'bg-secondary-subtle text-secondary', 'label' => $status];
    return "<span class='badge {$config['class']}'>{$config['label']}</span>";
}
?>

<div class="main-content">
    <h1><i class="fas fa-headphones text-primary me-2"></i> After-Sales Service</h1>

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

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-4" id="afterServiceTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $active_tab === 'service' ? 'active' : ''; ?>" href="?page=after_service&tab=service" role="tab">Service Requests</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $active_tab === 'warranty' ? 'active' : ''; ?>" href="?page=after_service&tab=warranty" role="tab">Warranty Claims</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $active_tab === 'support' ? 'active' : ''; ?>" href="?page=after_service&tab=support" role="tab">Customer Support</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $active_tab === 'tracking' ? 'active' : ''; ?>" href="?page=after_service&tab=tracking" role="tab">Request Tracking</a>
        </li>
    </ul>

    <div class="tab-content" id="afterServiceTabsContent">
        <!-- Service Requests Tab -->
        <div class="tab-pane fade <?php echo $active_tab === 'service' ? 'show active' : ''; ?>" id="service" role="tabpanel">
            <div class="card card-border shadow-sm">
                <div class="card-header p-4">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-ticket-alt me-2"></i> Create Service Request
                    </h5>
                    <p class="text-muted small mt-1">Submit a new service request for product repair or maintenance</p>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="?page=after_service&tab=service">
                        <input type="hidden" name="form_type" value="service_request">
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="customerName" class="form-label">Customer Name</label>
                                <input type="text" class="form-control" id="customerName" name="customerName" placeholder="Enter customer name">
                            </div>
                            <div class="col-md-6">
                                <label for="contactNumber" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="contactNumber" name="contactNumber" placeholder="Enter contact number">
                            </div>
                            <div class="col-md-6">
                                <label for="productName" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="productName" name="productName" placeholder="Enter product name">
                            </div>
                            <div class="col-md-6">
                                <label for="purchaseDate" class="form-label">Purchase Date</label>
                                <input type="date" class="form-control" id="purchaseDate" name="purchaseDate">
                            </div>
                            <div class="col-12">
                                <label for="issueType" class="form-label">Type of Issue</label>
                                <select class="form-select" id="issueType" name="issueType">
                                    <option value="" disabled selected>Select issue type</option>
                                    <option value="repair">Repair</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="replacement">Replacement</option>
                                    <option value="installation">Installation</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label">Issue Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4" placeholder="Describe the issue in detail"></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Submit Service Request</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Warranty Claims Tab -->
        <div class="tab-pane fade <?php echo $active_tab === 'warranty' ? 'show active' : ''; ?>" id="warranty" role="tabpanel">
            <div class="card card-border shadow-sm">
                <div class="card-header p-4">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-shield-alt me-2"></i> File Warranty Claim
                    </h5>
                    <p class="text-muted small mt-1">Submit a warranty claim for eligible products</p>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="?page=after_service&tab=warranty">
                        <input type="hidden" name="form_type" value="warranty_claim">
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="warrantyCustomerName" class="form-label">Customer Name</label>
                                <input type="text" class="form-control" id="warrantyCustomerName" name="warrantyCustomerName" placeholder="Enter customer name">
                            </div>
                            <div class="col-md-6">
                                <label for="warrantyNumber" class="form-label">Warranty Card Number</label>
                                <input type="text" class="form-control" id="warrantyNumber" name="warrantyNumber" placeholder="Enter warranty number">
                            </div>
                            <div class="col-md-6">
                                <label for="warrantyProduct" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="warrantyProduct" name="warrantyProduct" placeholder="Enter product name">
                            </div>
                            <div class="col-md-6">
                                <label for="serialNumber" class="form-label">Serial Number</label>
                                <input type="text" class="form-control" id="serialNumber" name="serialNumber" placeholder="Enter serial number">
                            </div>
                            <div class="col-12">
                                <label for="claimType" class="form-label">Claim Type</label>
                                <select class="form-select" id="claimType" name="claimType">
                                    <option value="" disabled selected>Select claim type</option>
                                    <option value="defect">Manufacturing Defect</option>
                                    <option value="malfunction">Product Malfunction</option>
                                    <option value="damage">Physical Damage</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="claimDetails" class="form-label">Claim Details</label>
                                <textarea class="form-control" id="claimDetails" name="claimDetails" rows="4" placeholder="Describe your warranty claim"></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Submit Warranty Claim</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Customer Support Tab -->
        <div class="tab-pane fade <?php echo $active_tab === 'support' ? 'show active' : ''; ?>" id="support" role="tabpanel">
            <div class="card card-border shadow-sm">
                <div class="card-header p-4">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-headphones me-2"></i> Contact Customer Support
                    </h5>
                    <p class="text-muted small mt-1">Get help from our customer support team</p>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="card card-border shadow-sm h-100">
                                <div class="card-header p-3">
                                    <h6 class="mb-0 d-flex align-items-center">
                                        <i class="fas fa-phone me-2"></i> Call Support
                                    </h6>
                                </div>
                                <div class="card-body p-3">
                                    <p class="fs-4 font-medium mb-2">1800-123-4567</p>
                                    <p class="text-muted small">Available 24/7</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-border shadow-sm h-100">
                                <div class="card-header p-3">
                                    <h6 class="mb-0 d-flex align-items-center">
                                        <i class="fas fa-comment me-2"></i> Chat Support
                                    </h6>
                                </div>
                                <div class="card-body p-3">
                                    <form method="POST" action="?page=after_service&tab=support">
                                        <input type="hidden" name="form_type" value="start_chat">
                                        <button type="submit" class="btn btn-primary w-100">Start Live Chat</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card card-border shadow-sm">
                        <div class="card-header p-3">
                            <h6 class="mb-0">Send a Message</h6>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="?page=after_service&tab=support">
                                <input type="hidden" name="form_type" value="support_message">
                                <div class="row g-4 mb-4">
                                    <div class="col-12">
                                        <label for="supportEmail" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="supportEmail" name="supportEmail" placeholder="Enter your email">
                                    </div>
                                    <div class="col-12">
                                        <label for="supportSubject" class="form-label">Subject</label>
                                        <input type="text" class="form-control" id="supportSubject" name="supportSubject" placeholder="Enter subject">
                                    </div>
                                    <div class="col-12">
                                        <label for="supportMessage" class="form-label">Message</label>
                                        <textarea class="form-control" id="supportMessage" name="supportMessage" rows="4" placeholder="Type your message here"></textarea>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Send Message</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Request Tracking Tab -->
        <div class="tab-pane fade <?php echo $active_tab === 'tracking' ? 'show active' : ''; ?>" id="tracking" role="tabpanel">
            <div class="card card-border shadow-sm">
                <div class="card-header p-4">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-clipboard-list me-2"></i> Track Your Request
                    </h5>
                    <p class="text-muted small mt-1">Check the status of your service request or warranty claim</p>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="?page=after_service&tab=tracking">
                        <input type="hidden" name="form_type" value="track_request">
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="requestType" class="form-label">Request Type</label>
                                <select class="form-select" id="requestType" name="requestType">
                                    <option value="" disabled selected>Select request type</option>
                                    <option value="service">Service Request</option>
                                    <option value="warranty">Warranty Claim</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="trackingId" class="form-label">Reference Number</label>
                                <input type="text" class="form-control" id="trackingId" name="trackingId" placeholder="Enter reference number">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Track Request</button>
                    </form>
                    <div class="mt-5">
                        <h6 class="font-medium mb-4">Recent Requests</h6>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($recent_requests as $request): ?>
                            <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                                <div>
                                    <p class="mb-0 font-medium"><?php echo htmlspecialchars($request['id']); ?></p>
                                    <p class="mb-0 text-muted small"><?php echo htmlspecialchars($request['description']); ?></p>
                                </div>
                                <div class="text-end">
                                    <?php if ($request['status'] === 'Completed'): ?>
                                    <span class="badge bg-green-subtle text-green">
                                        <i class="fas fa-check-circle me-1"></i> <?php echo htmlspecialchars($request['status']); ?>
                                    </span>
                                    <?php else: ?>
                                    <?php echo get_status_badge($request['status']); ?>
                                    <?php endif; ?>
                                    <p class="mb-0 text-muted small mt-1"><?php echo htmlspecialchars($request['date']); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>