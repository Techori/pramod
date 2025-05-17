<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../_conn.php';
$user_name = $_SESSION['user_name'];

// Get active tab
$active_tab = isset($_GET['tab']) && in_array($_GET['tab'], ['service', 'warranty']) ? $_GET['tab'] : 'service';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['form_type'] === 'service_request') {
    $customerName = $_POST['customerName'];
    $contactNumber = $_POST['contactNumber'];
    $productName = $_POST['productName'];
    $purchaseDate = $_POST['purchaseDate'];
    $issueType = $_POST['issueType'];
    $description = $_POST['description'];

    // Check if request already exists for this contact number + product (you can change condition as per requirement)
    $check_stmt = $conn->prepare("SELECT id FROM store_service_requests WHERE contact_number = ? AND product_name = ?");
    $check_stmt->bind_param("ss", $contactNumber, $productName);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Update existing entry
        $row = $check_result->fetch_assoc();
        $update_stmt = $conn->prepare("UPDATE store_service_requests SET customer_name = ?, purchase_date = ?, issue_type = ?, description = ?, updated_at = NOW() WHERE id = ?");
        $update_stmt->bind_param("ssssi", $customerName, $purchaseDate, $issueType, $description, $row['id']);
        $update_stmt->execute();
    } else {
        // Insert new entry
        $insert_stmt = $conn->prepare("INSERT INTO store_service_requests (customer_name, contact_number, product_name, purchase_date, issue_type, description, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("sssssss", $customerName, $contactNumber, $productName, $purchaseDate, $issueType, $description, $user_name);
        $insert_stmt->execute();
    }
}

// Fetch old data if exists
$service_prefill  = [
    'customer_name' => '',
    'contact_number' => '',
    'product_name' => '',
    'purchase_date' => '',
    'issue_type' => '',
    'description' => ''
];

$prefill_stmt = $conn->prepare("SELECT * FROM store_service_requests WHERE created_by = ? ORDER BY id DESC LIMIT 1");
$prefill_stmt->bind_param("s", $user_name);
$prefill_stmt->execute();
$result = $prefill_stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $service_prefill  = [
        'customer_name' => $row['customer_name'],
        'contact_number' => $row['contact_number'],
        'product_name' => $row['product_name'],
        'purchase_date' => $row['purchase_date'],
        'issue_type' => $row['issue_type'],
        'description' => $row['description']
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['form_type'] === 'warranty_claim') {
    $customerName = $_POST['warrantyCustomerName'];
    $warrantyNumber = $_POST['warrantyNumber'];
    $productName = $_POST['warrantyProduct'];
    $serialNumber = $_POST['serialNumber'];
    $claimType = $_POST['claimType'];
    $claimDetails = $_POST['claimDetails'];

    // Check if a warranty claim already exists for this warranty number
    $check_stmt = $conn->prepare("SELECT id FROM store_warranty_claims WHERE warranty_number = ?");
    $check_stmt->bind_param("s", $warrantyNumber);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Update existing entry
        $row = $check_result->fetch_assoc();
        $update_stmt = $conn->prepare("UPDATE store_warranty_claims 
            SET customer_name = ?, product_name = ?, serial_number = ?, claim_type = ?, claim_details = ?, created_by = ?
            WHERE id = ?");
        $update_stmt->bind_param("ssssssi", $customerName, $productName, $serialNumber, $claimType, $claimDetails, $user_name, $row['id']);
        $update_stmt->execute();
    } else {
        // Insert new entry
        $insert_stmt = $conn->prepare("INSERT INTO store_warranty_claims 
            (customer_name, warranty_number, product_name, serial_number, claim_type, claim_details, created_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("sssssss", $customerName, $warrantyNumber, $productName, $serialNumber, $claimType, $claimDetails, $user_name);
        $insert_stmt->execute();
    }
}

// Prefill data if it exists
$prefill = [
    'customer_name' => '',
    'warranty_number' => '',
    'product_name' => '',
    'serial_number' => '',
    'claim_type' => '',
    'claim_details' => ''
];

$prefill_stmt = $conn->prepare("SELECT * FROM store_warranty_claims WHERE created_by = ? ORDER BY id DESC LIMIT 1");
$prefill_stmt->bind_param("s", $user_name);
$prefill_stmt->execute();
$result = $prefill_stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $prefill = [
        'customer_name' => $row['customer_name'],
        'warranty_number' => $row['warranty_number'],
        'product_name' => $row['product_name'],
        'serial_number' => $row['serial_number'],
        'claim_type' => $row['claim_type'],
        'claim_details' => $row['claim_details']
    ];
}


?>

<div class="main-content">
    <h1><i class="fas fa-headphones text-primary me-2"></i> After-Sales Service</h1>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-4" id="afterServiceTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $active_tab === 'service' ? 'active' : ''; ?>"
                href="?page=after_service&tab=service" role="tab">Service Requests</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $active_tab === 'warranty' ? 'active' : ''; ?>"
                href="?page=after_service&tab=warranty" role="tab">Warranty Claims</a>
        </li>
    </ul>

    <div class="tab-content" id="afterServiceTabsContent">
        <!-- Service Requests Tab -->
        <div class="tab-pane fade <?php echo $active_tab === 'service' ? 'show active' : ''; ?>" id="service"
            role="tabpanel">
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
                                <input type="text" class="form-control" id="customerName" name="customerName"
                                    value="<?php echo htmlspecialchars($service_prefill ['customer_name']); ?>"
                                    placeholder="Enter customer name">
                            </div>
                            <div class="col-md-6">
                                <label for="contactNumber" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="contactNumber" name="contactNumber"
                                    value="<?php echo htmlspecialchars($service_prefill ['contact_number']); ?>"
                                    placeholder="Enter contact number">
                            </div>
                            <div class="col-md-6">
                                <label for="productName" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="productName" name="productName"
                                    value="<?php echo htmlspecialchars($service_prefill ['product_name']); ?>"
                                    placeholder="Enter product name">
                            </div>
                            <div class="col-md-6">
                                <label for="purchaseDate" class="form-label">Purchase Date</label>
                                <input type="date" class="form-control" id="purchaseDate" name="purchaseDate"
                                    value="<?php echo htmlspecialchars($service_prefill ['purchase_date']); ?>">
                            </div>
                            <div class="col-12">
                                <label for="issueType" class="form-label">Type of Issue</label>
                                <select class="form-select" id="issueType" name="issueType">
                                    <option value="" disabled <?php echo empty($service_prefill ['issue_type']) ? 'selected' : ''; ?>>Select issue type</option>
                                    <option value="repair" <?php echo ($service_prefill ['issue_type'] == 'repair') ? 'selected' : ''; ?>>Repair</option>
                                    <option value="maintenance" <?php echo ($service_prefill ['issue_type'] == 'maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                                    <option value="replacement" <?php echo ($service_prefill ['issue_type'] == 'replacement') ? 'selected' : ''; ?>>Replacement</option>
                                    <option value="installation" <?php echo ($service_prefill ['issue_type'] == 'installation') ? 'selected' : ''; ?>>Installation</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label">Issue Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"
                                    placeholder="Describe the issue in detail"><?php echo htmlspecialchars($service_prefill ['description']); ?></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Submit Service Request</button>
                    </form>

                </div>
            </div>
        </div>

        <!-- Warranty Claims Tab -->
        <div class="tab-pane fade <?php echo $active_tab === 'warranty' ? 'show active' : ''; ?>" id="warranty"
            role="tabpanel">
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
                                <input type="text" class="form-control" id="warrantyCustomerName"
                                    name="warrantyCustomerName" placeholder="Enter customer name"
                                    value="<?= htmlspecialchars($prefill['customer_name']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="warrantyNumber" class="form-label">Warranty Card Number</label>
                                <input type="text" class="form-control" id="warrantyNumber" name="warrantyNumber"
                                    placeholder="Enter warranty number"
                                    value="<?= htmlspecialchars($prefill['warranty_number']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="warrantyProduct" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="warrantyProduct" name="warrantyProduct"
                                    placeholder="Enter product name"
                                    value="<?= htmlspecialchars($prefill['product_name']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="serialNumber" class="form-label">Serial Number</label>
                                <input type="text" class="form-control" id="serialNumber" name="serialNumber"
                                    placeholder="Enter serial number"
                                    value="<?= htmlspecialchars($prefill['serial_number']) ?>">
                            </div>
                            <div class="col-12">
                                <label for="claimType" class="form-label">Claim Type</label>
                                <select class="form-select" id="claimType" name="claimType">
                                    <option value="" disabled>Select claim type</option>
                                    <option value="defect" <?php echo ($prefill['claim_type'] == 'defect') ? 'selected' : '' ?>>
                                        Manufacturing Defect</option>
                                    <option value="malfunction" <?php echo ($prefill['claim_type'] == 'malfunction') ? 'selected' : '' ?>>Product Malfunction</option>
                                    <option value="damage" <?php echo ($prefill['claim_type'] == 'damage') ? 'selected' : '' ?>>
                                        Physical Damage</option>
                                    <option value="other" <?php echo ($prefill['claim_type'] == 'other') ? 'selected' : '' ?>>Other
                                    </option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="claimDetails" class="form-label">Claim Details</label>
                                <textarea class="form-control" id="claimDetails" name="claimDetails" rows="4"
                                    placeholder="Describe your warranty claim"><?= htmlspecialchars($prefill['claim_details']) ?></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Submit Warranty Claim</button>
                    </form>

                </div>
            </div>
        </div>

    </div>
</div>