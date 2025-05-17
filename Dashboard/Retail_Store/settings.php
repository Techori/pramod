<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../_conn.php';
$user_name = $_SESSION['user_name'];

// Get active tab
$active_tab = isset($_GET['tab']) && in_array($_GET['tab'], ['general', 'inventory', 'hardware', 'notifications', 'users', 'afterSales']) ? $_GET['tab'] : 'general';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['section'] === 'General') {
    $store_name = $_POST['store_name'];
    $store_code = $_POST['store_code'];
    $store_phone = $_POST['store_phone'];
    $store_email = $_POST['store_email'];
    $store_address = $_POST['store_address'];
    $store_manager = $_POST['store_manager'];
    $store_active = isset($_POST['store_active']) ? 1 : 0;
    $accept_online_orders = isset($_POST['accept_online_orders']) ? 1 : 0;

    // Check if general settings already exist for this user
    $check_stmt = $conn->prepare("SELECT id FROM store_settings_general WHERE created_by = ?");
    $check_stmt->bind_param("s", $user_name);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Update
        $row = $result->fetch_assoc();
        $update_stmt = $conn->prepare("UPDATE store_settings_general SET 
            store_name = ?, store_code = ?, store_phone = ?, store_email = ?, store_address = ?, 
            store_manager = ?, store_active = ?, accept_online_orders = ?
            WHERE id = ?");
        $update_stmt->bind_param("ssssssiii", $store_name, $store_code, $store_phone, $store_email, $store_address, $store_manager, $store_active, $accept_online_orders, $row['id']);
        $update_stmt->execute();
    } else {
        // Insert
        $insert_stmt = $conn->prepare("INSERT INTO store_settings_general 
            (store_name, store_code, store_phone, store_email, store_address, store_manager, store_active, accept_online_orders, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("ssssssiis", $store_name, $store_code, $store_phone, $store_email, $store_address, $store_manager, $store_active, $accept_online_orders, $user_name);
        $insert_stmt->execute();
    }
}

// Preload existing settings into $settings['general']
$settings['general'] = [
    'store_name' => '',
    'store_code' => '',
    'store_phone' => '',
    'store_email' => '',
    'store_address' => '',
    'store_manager' => '',
    'store_active' => 0,
    'accept_online_orders' => 0
];

$prefill_stmt = $conn->prepare("SELECT * FROM store_settings_general WHERE created_by = ? LIMIT 1");
$prefill_stmt->bind_param("s", $user_name);
$prefill_stmt->execute();
$prefill_result = $prefill_stmt->get_result();
if ($row = $prefill_result->fetch_assoc()) {
    $settings['general'] = [
        'store_name' => $row['store_name'],
        'store_code' => $row['store_code'],
        'store_phone' => $row['store_phone'],
        'store_email' => $row['store_email'],
        'store_address' => $row['store_address'],
        'store_manager' => $row['store_manager'],
        'store_active' => $row['store_active'],
        'accept_online_orders' => $row['accept_online_orders']
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['section'] === 'Inventory') {
    $lowStock = $_POST['low_stock_threshold'];
    $reorderPoint = $_POST['reorder_point'];
    $trackSerial = isset($_POST['track_serial_numbers']) ? 1 : 0;
    $allowNegative = isset($_POST['allow_negative_stock']) ? 1 : 0;
    $barcodeScan = isset($_POST['barcode_scanning']) ? 1 : 0;
    $inventoryMethod = $_POST['inventory_method'];
    $stockFreq = $_POST['stock_count_frequency'];

    // Check if a setting already exists for this user
    $check_stmt = $conn->prepare("SELECT id FROM store_inventory_settings WHERE created_by = ?");
    $check_stmt->bind_param("s", $user_name);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Update existing entry
        $row = $check_result->fetch_assoc();
        $update_stmt = $conn->prepare("UPDATE store_inventory_settings SET low_stock_threshold = ?, reorder_point = ?, track_serial_numbers = ?, allow_negative_stock = ?, barcode_scanning = ?, inventory_method = ?, stock_count_frequency = ?, updated_at = NOW() WHERE id = ?");
        $update_stmt->bind_param("iiiiiisi", $lowStock, $reorderPoint, $trackSerial, $allowNegative, $barcodeScan, $inventoryMethod, $stockFreq, $row['id']);
        $update_stmt->execute();
    } else {
        // Insert new entry
        $insert_stmt = $conn->prepare("INSERT INTO store_inventory_settings (low_stock_threshold, reorder_point, track_serial_numbers, allow_negative_stock, barcode_scanning, inventory_method, stock_count_frequency, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("iiiiiiss", $lowStock, $reorderPoint, $trackSerial, $allowNegative, $barcodeScan, $inventoryMethod, $stockFreq, $user_name);
        $insert_stmt->execute();
    }
}

// Pre-fill data for form if already submitted
$prefill = [
    'low_stock_threshold' => '',
    'reorder_point' => '',
    'track_serial_numbers' => 0,
    'allow_negative_stock' => 0,
    'barcode_scanning' => 0,
    'inventory_method' => '',
    'stock_count_frequency' => ''
];

$prefill_stmt = $conn->prepare("SELECT * FROM store_inventory_settings WHERE created_by = ? ORDER BY id DESC LIMIT 1");
$prefill_stmt->bind_param("s", $user_name);
$prefill_stmt->execute();
$result = $prefill_stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $prefill = [
        'low_stock_threshold' => $row['low_stock_threshold'],
        'reorder_point' => $row['reorder_point'],
        'track_serial_numbers' => $row['track_serial_numbers'],
        'allow_negative_stock' => $row['allow_negative_stock'],
        'barcode_scanning' => $row['barcode_scanning'],
        'inventory_method' => $row['inventory_method'],
        'stock_count_frequency' => $row['stock_count_frequency']
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['section'] === 'Hardware') {
    $receiptPrinter = isset($_POST['receipt_printer']) ? 1 : 0;
    $printerModel = $_POST['printer_model'];
    $barcodeScanner = isset($_POST['barcode_scanner']) ? 1 : 0;
    $scannerModel = $_POST['scanner_model'];
    $customerDisplay = isset($_POST['customer_display']) ? 1 : 0;
    $paymentTerminal = isset($_POST['payment_terminal']) ? 1 : 0;
    $cashDrawer = isset($_POST['cash_drawer']) ? 1 : 0;

    // Check if entry exists for this user
    $check_stmt = $conn->prepare("SELECT id FROM store_hardware_settings WHERE created_by = ?");
    $check_stmt->bind_param("s", $user_name);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Update existing entry
        $row = $check_result->fetch_assoc();
        $update_stmt = $conn->prepare("UPDATE store_hardware_settings SET receipt_printer = ?, printer_model = ?, barcode_scanner = ?, scanner_model = ?, customer_display = ?, payment_terminal = ?, cash_drawer = ?, updated_at = NOW() WHERE id = ?");
        $update_stmt->bind_param("isisiisi", $receiptPrinter, $printerModel, $barcodeScanner, $scannerModel, $customerDisplay, $paymentTerminal, $cashDrawer, $row['id']);
        $update_stmt->execute();
    } else {
        // Insert new entry
        $insert_stmt = $conn->prepare("INSERT INTO store_hardware_settings (receipt_printer, printer_model, barcode_scanner, scanner_model, customer_display, payment_terminal, cash_drawer, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("isisiiss", $receiptPrinter, $printerModel, $barcodeScanner, $scannerModel, $customerDisplay, $paymentTerminal, $cashDrawer, $user_name);
        $insert_stmt->execute();
    }
}

// Pre-fill data for form
$settings['hardware'] = [
    'receipt_printer' => 0,
    'printer_model' => '',
    'barcode_scanner' => 0,
    'scanner_model' => '',
    'customer_display' => 0,
    'payment_terminal' => 0,
    'cash_drawer' => 0
];

$prefill_stmt = $conn->prepare("SELECT * FROM store_hardware_settings WHERE created_by = ? ORDER BY id DESC LIMIT 1");
$prefill_stmt->bind_param("s", $user_name);
$prefill_stmt->execute();
$result = $prefill_stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $settings['hardware'] = [
        'receipt_printer' => $row['receipt_printer'],
        'printer_model' => $row['printer_model'],
        'barcode_scanner' => $row['barcode_scanner'],
        'scanner_model' => $row['scanner_model'],
        'customer_display' => $row['customer_display'],
        'payment_terminal' => $row['payment_terminal'],
        'cash_drawer' => $row['cash_drawer']
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['section'] === 'After-Sales Service') {
    $defaultWarranty = intval($_POST['default_warranty']);
    $extendedWarranty = intval($_POST['extended_warranty']);
    $warrantyTracking = isset($_POST['warranty_tracking']) ? 1 : 0;

    $returnPeriod = intval($_POST['return_period']);
    $returnPolicy = $_POST['return_policy'];
    $returnsConditions = $_POST['returns_conditions'];

    $serviceCenters = $_POST['service_centers'];
    $doorstepService = isset($_POST['doorstep_service']) ? 1 : 0;
    $expressService = isset($_POST['express_service']) ? 1 : 0;

    $supportPhone = $_POST['support_phone'];
    $supportEmail = $_POST['support_email'];
    $customerPortal = isset($_POST['customer_portal']) ? 1 : 0;

    // Check if entry exists for this user
    $check_stmt = $conn->prepare("SELECT id FROM store_after_sales_settings WHERE created_by = ?");
    $check_stmt->bind_param("s", $user_name);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Update existing entry
        $row = $check_result->fetch_assoc();
        $update_stmt = $conn->prepare("UPDATE store_after_sales_settings SET 
            default_warranty = ?, 
            extended_warranty = ?, 
            warranty_tracking = ?, 
            return_period = ?, 
            return_policy = ?, 
            returns_conditions = ?, 
            service_centers = ?, 
            doorstep_service = ?, 
            express_service = ?, 
            support_phone = ?, 
            support_email = ?, 
            customer_portal = ?, 
            updated_at = NOW() 
            WHERE id = ?");
        $update_stmt->bind_param(
            "iiiisssiissii",
            $defaultWarranty,
            $extendedWarranty,
            $warrantyTracking,
            $returnPeriod,
            $returnPolicy,
            $returnsConditions,
            $serviceCenters,
            $doorstepService,
            $expressService,
            $supportPhone,
            $supportEmail,
            $customerPortal,
            $row['id']
        );
        $update_stmt->execute();
    } else {
        // Insert new entry
        $insert_stmt = $conn->prepare("INSERT INTO store_after_sales_settings (
            default_warranty, extended_warranty, warranty_tracking, 
            return_period, return_policy, returns_conditions, 
            service_centers, doorstep_service, express_service, 
            support_phone, support_email, customer_portal, created_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_stmt->bind_param(
            "iiiisssiissis",
            $defaultWarranty,
            $extendedWarranty,
            $warrantyTracking,
            $returnPeriod,
            $returnPolicy,
            $returnsConditions,
            $serviceCenters,
            $doorstepService,
            $expressService,
            $supportPhone,
            $supportEmail,
            $customerPortal,
            $user_name
        );
        $insert_stmt->execute();
    }
}

$settings['after_sales'] = [
    'default_warranty' => '',
    'extended_warranty' => '',
    'warranty_tracking' => 0,
    'return_period' => '',
    'return_policy' => '',
    'returns_conditions' => '',
    'service_centers' => '',
    'doorstep_service' => 0,
    'express_service' => 0,
    'support_phone' => '',
    'support_email' => '',
    'customer_portal' => 0
];

$prefill_stmt = $conn->prepare("SELECT * FROM store_after_sales_settings WHERE created_by = ? ORDER BY id DESC LIMIT 1");
$prefill_stmt->bind_param("s", $user_name);
$prefill_stmt->execute();
$result = $prefill_stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $settings['after_sales'] = [
        'default_warranty' => $row['default_warranty'],
        'extended_warranty' => $row['extended_warranty'],
        'warranty_tracking' => $row['warranty_tracking'],
        'return_period' => $row['return_period'],
        'return_policy' => $row['return_policy'],
        'returns_conditions' => $row['returns_conditions'],
        'service_centers' => $row['service_centers'],
        'doorstep_service' => $row['doorstep_service'],
        'express_service' => $row['express_service'],
        'support_phone' => $row['support_phone'],
        'support_email' => $row['support_email'],
        'customer_portal' => $row['customer_portal']
    ];
}




?>

<div class="main-content">
    <h1><i class="fas fa-cog text-primary me-2"></i> Store Settings</h1>


    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $active_tab === 'general' ? 'active' : ''; ?>"
                href="?page=settings&tab=general" role="tab">General</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $active_tab === 'inventory' ? 'active' : ''; ?>"
                href="?page=settings&tab=inventory" role="tab">Inventory</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $active_tab === 'hardware' ? 'active' : ''; ?>"
                href="?page=settings&tab=hardware" role="tab">Hardware</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $active_tab === 'users' ? 'active' : ''; ?>" href="?page=settings&tab=users"
                role="tab">Users & Permissions</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $active_tab === 'afterSales' ? 'active' : ''; ?>"
                href="?page=settings&tab=afterSales" role="tab">After-Sales Service</a>
        </li>
    </ul>

    <div class="tab-content" id="settingsTabsContent">
        <!-- General Settings -->
        <div class="tab-pane fade <?php echo $active_tab === 'general' ? 'show active' : ''; ?>" id="general"
            role="tabpanel">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">General Settings</h5>
                    <p class="text-muted mb-4">Configure your store information and preferences</p>
                    <form method="POST" action="?page=settings&tab=general">
                        <input type="hidden" name="section" value="General">
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="store_name" class="form-label">Store Name</label>
                                <input type="text" class="form-control" id="store_name" name="store_name"
                                    value="<?php echo htmlspecialchars($settings['general']['store_name']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="store_code" class="form-label">Store Code</label>
                                <input type="text" class="form-control" id="store_code" name="store_code"
                                    value="<?php echo htmlspecialchars($settings['general']['store_code']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="store_phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="store_phone" name="store_phone"
                                    value="<?php echo htmlspecialchars($settings['general']['store_phone']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="store_email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="store_email" name="store_email"
                                    value="<?php echo htmlspecialchars($settings['general']['store_email']); ?>">
                            </div>
                            <div class="col-12">
                                <label for="store_address" class="form-label">Store Address</label>
                                <textarea class="form-control" id="store_address" name="store_address"
                                    rows="4"><?php echo htmlspecialchars($settings['general']['store_address']); ?></textarea>
                            </div>
                            <div class="col-12">
                                <label for="store_manager" class="form-label">Store Manager</label>
                                <input type="text" class="form-control" id="store_manager" name="store_manager"
                                    value="<?php echo htmlspecialchars($settings['general']['store_manager']); ?>">
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <label for="store_active" class="form-label">Store Active</label>
                                    <p class="text-muted small">Enable or disable this store location</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="store_active"
                                        name="store_active" <?php echo $settings['general']['store_active'] ? 'checked' : ''; ?>>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <div>
                                    <label for="accept_online_orders" class="form-label">Accept Online Orders</label>
                                    <p class="text-muted small">Allow customers to place orders online for this store
                                    </p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="accept_online_orders"
                                        name="accept_online_orders" <?php echo $settings['general']['accept_online_orders'] ? 'checked' : ''; ?>>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Inventory Settings -->
        <div class="tab-pane fade <?php echo $active_tab === 'inventory' ? 'show active' : ''; ?>" id="inventory"
            role="tabpanel">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Inventory Settings</h5>
                    <p class="text-muted mb-4">Configure inventory management preferences</p>
                    <form method="POST" action="?page=settings&tab=inventory">
                        <input type="hidden" name="section" value="Inventory">
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="low_stock_threshold" class="form-label">Low Stock Threshold (%)</label>
                                <input type="number" class="form-control" id="low_stock_threshold"
                                    name="low_stock_threshold"
                                    value="<?php echo htmlspecialchars($prefill['low_stock_threshold']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="reorder_point" class="form-label">Default Reorder Point</label>
                                <input type="number" class="form-control" id="reorder_point" name="reorder_point"
                                    value="<?php echo htmlspecialchars($prefill['reorder_point']); ?>">
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <div>
                                    <label for="track_serial_numbers" class="form-label">Track Serial Numbers</label>
                                    <p class="text-muted small">Record serial numbers for applicable products</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="track_serial_numbers"
                                        name="track_serial_numbers" <?php echo $prefill['track_serial_numbers'] ? 'checked' : ''; ?>>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <div>
                                    <label for="allow_negative_stock" class="form-label">Allow Negative Stock</label>
                                    <p class="text-muted small">Allow sales when inventory count is zero</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="allow_negative_stock"
                                        name="allow_negative_stock" <?php echo $prefill['allow_negative_stock'] ? 'checked' : ''; ?>>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <div>
                                    <label for="barcode_scanning" class="form-label">Barcode Scanning</label>
                                    <p class="text-muted small">Use barcode scanner for inventory management</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="barcode_scanning"
                                        name="barcode_scanning" <?php echo $prefill['barcode_scanning'] ? 'checked' : ''; ?>>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="inventory_method" class="form-label">Inventory Valuation Method</label>
                                <select class="form-select" id="inventory_method" name="inventory_method">
                                    <option value="fifo" <?php echo $prefill['inventory_method'] === 'fifo' ? 'selected' : ''; ?>>FIFO (First In, First Out)</option>
                                    <option value="lifo" <?php echo $prefill['inventory_method'] === 'lifo' ? 'selected' : ''; ?>>LIFO (Last In, First Out)</option>
                                    <option value="avg" <?php echo $prefill['inventory_method'] === 'avg' ? 'selected' : ''; ?>>Moving Average</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="stock_count_frequency" class="form-label">Stock Count Frequency</label>
                                <select class="form-select" id="stock_count_frequency" name="stock_count_frequency">
                                    <option value="weekly" <?php echo $prefill['stock_count_frequency'] === 'weekly' ? 'selected' : ''; ?>>Weekly</option>
                                    <option value="biweekly" <?php echo $prefill['stock_count_frequency'] === 'biweekly' ? 'selected' : ''; ?>>Bi-weekly</option>
                                    <option value="monthly" <?php echo $prefill['stock_count_frequency'] === 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                                    <option value="quarterly" <?php echo $prefill['stock_count_frequency'] === 'quarterly' ? 'selected' : ''; ?>>Quarterly</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Hardware Settings -->
        <div class="tab-pane fade <?php echo $active_tab === 'hardware' ? 'show active' : ''; ?>" id="hardware"
            role="tabpanel">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Hardware Settings</h5>
                    <p class="text-muted mb-4">Configure POS hardware and peripherals</p>
                    <form method="POST" action="?page=settings&tab=hardware">
                        <input type="hidden" name="section" value="Hardware">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <label for="receipt_printer" class="form-label">Receipt Printer</label>
                                    <p class="text-muted small">Enable receipt printer for POS</p>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="receipt_printer"
                                            name="receipt_printer" <?php echo $settings['hardware']['receipt_printer'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="printer_model" class="form-label">Printer Model</label>
                                <select class="form-select" id="printer_model" name="printer_model">
                                    <option value="epson" <?php echo $settings['hardware']['printer_model'] === 'epson' ? 'selected' : ''; ?>>Epson TM-T88VI</option>
                                    <option value="star" <?php echo $settings['hardware']['printer_model'] === 'star' ? 'selected' : ''; ?>>Star Micronics TSP143III</option>
                                    <option value="citizen" <?php echo $settings['hardware']['printer_model'] === 'citizen' ? 'selected' : ''; ?>>Citizen
                                        CT-S310II</option>
                                    <option value="custom" <?php echo $settings['hardware']['printer_model'] === 'custom' ? 'selected' : ''; ?>>Custom Printer</option>
                                </select>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <label for="barcode_scanner" class="form-label">Barcode Scanner</label>
                                    <p class="text-muted small">Enable barcode scanner for POS</p>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="barcode_scanner"
                                            name="barcode_scanner" <?php echo $settings['hardware']['barcode_scanner'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="scanner_model" class="form-label">Scanner Model</label>
                                <select class="form-select" id="scanner_model" name="scanner_model">
                                    <option value="honeywell" <?php echo $settings['hardware']['scanner_model'] === 'honeywell' ? 'selected' : ''; ?>>
                                        Honeywell Voyager 1250g</option>
                                    <option value="zebra" <?php echo $settings['hardware']['scanner_model'] === 'zebra' ? 'selected' : ''; ?>>Zebra DS2208</option>
                                    <option value="symbol" <?php echo $settings['hardware']['scanner_model'] === 'symbol' ? 'selected' : ''; ?>>Symbol LS2208</option>
                                    <option value="custom" <?php echo $settings['hardware']['scanner_model'] === 'custom' ? 'selected' : ''; ?>>Custom Scanner</option>
                                </select>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <label for="customer_display" class="form-label">Customer Display</label>
                                    <p class="text-muted small">Enable secondary display for customers</p>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="customer_display"
                                            name="customer_display" <?php echo $settings['hardware']['customer_display'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <label for="payment_terminal" class="form-label">Payment Terminal</label>
                                    <p class="text-muted small">Enable integrated card payment terminal</p>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="payment_terminal"
                                            name="payment_terminal" <?php echo $settings['hardware']['payment_terminal'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <label for="cash_drawer" class="form-label">Cash Drawer</label>
                                    <p class="text-muted small">Enable automatic cash drawer</p>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="cash_drawer"
                                            name="cash_drawer" <?php echo $settings['hardware']['cash_drawer'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Users & Permissions Settings -->
        <div class="tab-pane fade <?php echo $active_tab === 'users' ? 'show active' : ''; ?>" id="users"
            role="tabpanel">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Users & Permissions</h5>
                    <p class="text-muted mb-4">Manage user access and permissions</p>
                    <form method="POST" action="?page=settings&tab=users">
                        <input type="hidden" name="section" value="Users & Permissions">
                        <div class="mb-4">
                            <!-- Users Table -->
                            <div class="table-responsive border rounded mb-4">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="px-4 py-3 text-uppercase small">User</th>
                                            <th class="px-4 py-3 text-uppercase small">Role</th>
                                            <th class="px-4 py-3 text-uppercase small">Status</th>
                                            <th class="px-4 py-3 text-uppercase small">Last Login</th>
                                            <th class="px-4 py-3 text-uppercase small text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($settings['users'] as $user): ?>
                                            <tr>
                                                <td class Grandfather="px-4 py-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                                            style="width: 32px; height: 32px;">
                                                            <i class="fas fa-users text-muted"></i>
                                                        </div>
                                                        <div class="ms-3">
                                                            <p class="mb-0 font-medium">
                                                                <?php echo htmlspecialchars($user['name']); ?>
                                                            </p>
                                                            <p class="mb-0 text-muted small">
                                                                <?php echo htmlspecialchars($user['email']); ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3"><?php echo htmlspecialchars($user['role']); ?></td>
                                                <td class="px-4 py-3"><?php echo htmlspecialchars($user['status']); ?></td>
                                                <td class="px-4 py-3"><?php echo htmlspecialchars($user['last_login']); ?>
                                                </td>
                                                <td class="px-4 py-3 text-end">
                                                    <button type="submit" name="action" value="edit_user"
                                                        class="btn btn-outline-primary btn-sm">Edit</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>


                            <!-- Role Permissions -->
                            <div class="border-top pt-4">
                                <h6 class="font-medium mb-3">Role Permissions</h6>
                                <?php foreach ($settings['roles'] as $role): ?>
                                    <div class="border rounded p-4 mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h6 class="font-medium"><?php echo htmlspecialchars($role['name']); ?></h6>
                                                <p class="text-muted small">
                                                    <?php echo htmlspecialchars($role['description']); ?>
                                                </p>
                                            </div>
                                            <button type="submit" name="action" value="edit_role"
                                                class="btn btn-outline-primary btn-sm">Edit Role</button>
                                        </div>
                                        <div class="row g-2">
                                            <?php foreach ($role['permissions'] as $permission): ?>
                                                <div class="col-md-6 col-12">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-shield-alt text-success me-2"></i>
                                                        <span class="small"><?php echo htmlspecialchars($permission); ?></span>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                            <?php
                                            // List disabled permissions for Sales Associate
                                            if ($role['name'] === 'Sales Associate') {
                                                $all_permissions = [
                                                    'Sales & Billing',
                                                    'Inventory Management',
                                                    'Customer Management',
                                                    'Reports & Analytics',
                                                    'User Management',
                                                    'Settings & Configuration'
                                                ];
                                                $disabled_permissions = array_diff($all_permissions, $role['permissions']);
                                                foreach ($disabled_permissions as $permission):
                                                    ?>
                                                    <div class="col-md-6 col-12">
                                                        <div class="d-flex align-items-center opacity-50">
                                                            <i class="fas fa-cog me-2"></i>
                                                            <span class="small"><?php echo htmlspecialchars($permission); ?></span>
                                                        </div>
                                                    </div>
                                                <?php endforeach;
                                            } ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- After-Sales Service Settings -->
        <div class="tab-pane fade <?php echo $active_tab === 'afterSales' ? 'show active' : ''; ?>" id="afterSales"
            role="tabpanel">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">After-Sales Service Settings</h5>
                    <p class="text-muted mb-4">Configure warranty, returns and service policies</p>
                    <form method="POST" action="?page=settings&tab=afterSales">
                        <input type="hidden" name="section" value="After-Sales Service">
                        <div class="mb-4">
                            <!-- Warranty Settings -->
                            <div class="pt-2">
                                <h6 class="font-medium mb-3">Warranty Settings</h6>
                                <div class="row g-4 mb-3">
                                    <div class="col-md-6">
                                        <label for="default_warranty" class="form-label">Default Warranty Period
                                            (months)</label>
                                        <input type="number" class="form-control" id="default_warranty"
                                            name="default_warranty"
                                            value="<?php echo htmlspecialchars($settings['after_sales']['default_warranty']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="extended_warranty" class="form-label">Extended Warranty Period
                                            (months)</label>
                                        <input type="number" class="form-control" id="extended_warranty"
                                            name="extended_warranty"
                                            value="<?php echo htmlspecialchars($settings['after_sales']['extended_warranty']); ?>">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <label for="warranty_tracking" class="form-label">Enable Warranty
                                            Tracking</label>
                                        <p class="text-muted small">Track warranty status based on product serial
                                            numbers</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="warranty_tracking"
                                            name="warranty_tracking" <?php echo $settings['after_sales']['warranty_tracking'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                            </div>

                            <!-- Returns Policy -->
                            <div class="border-top pt-4">
                                <h6 class="font-medium mb-3">Returns Policy</h6>
                                <div class="row g-4 mb-3">
                                    <div class="col-md-6">
                                        <label for="return_period" class="form-label">Return Period (days)</label>
                                        <input type="number" class="form-control" id="return_period"
                                            name="return_period"
                                            value="<?php echo htmlspecialchars($settings['after_sales']['return_period']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="return_policy" class="form-label">Return Processing Method</label>
                                        <select class="form-select" id="return_policy" name="return_policy">
                                            <option value="exchange" <?php echo $settings['after_sales']['return_policy'] === 'exchange' ? 'selected' : ''; ?>>Exchange Only</option>
                                            <option value="refund" <?php echo $settings['after_sales']['return_policy'] === 'refund' ? 'selected' : ''; ?>>Refund Only</option>
                                            <option value="both" <?php echo $settings['after_sales']['return_policy'] === 'both' ? 'selected' : ''; ?>>Exchange or Refund</option>
                                            <option value="credit" <?php echo $settings['after_sales']['return_policy'] === 'credit' ? 'selected' : ''; ?>>Store Credit</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="returns_conditions" class="form-label">Returns Conditions</label>
                                    <textarea class="form-control" id="returns_conditions" name="returns_conditions"
                                        rows="4"><?php echo htmlspecialchars($settings['after_sales']['returns_conditions']); ?></textarea>
                                </div>
                            </div>

                            <!-- Service Centers -->
                            <div class="border-top pt-4">
                                <h6 class="font-medium mb-3">Service Centers</h6>
                                <div class="mb-3">
                                    <label for="service_centers" class="form-label">Authorized Service Centers</label>
                                    <textarea class="form-control" id="service_centers" name="service_centers"
                                        rows="4"><?php echo htmlspecialchars($settings['after_sales']['service_centers']); ?></textarea>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <label for="doorstep_service" class="form-label">Offer Doorstep Service</label>
                                        <p class="text-muted small">Provide on-site service for eligible products</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="doorstep_service"
                                            name="doorstep_service" <?php echo $settings['after_sales']['doorstep_service'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <label for="express_service" class="form-label">Express Service Option</label>
                                        <p class="text-muted small">Offer premium service with faster turnaround</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="express_service"
                                            name="express_service" <?php echo $settings['after_sales']['express_service'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                            </div>

                            <!-- Customer Support -->
                            <div class="border-top pt-4">
                                <h6 class="font-medium mb-3">Customer Support</h6>
                                <div class="row g-4 mb-3">
                                    <div class="col-md-6">
                                        <label for="support_phone" class="form-label">Support Phone Number</label>
                                        <input type="text" class="form-control" id="support_phone" name="support_phone"
                                            value="<?php echo htmlspecialchars($settings['after_sales']['support_phone']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="support_email" class="form-label">Support Email</label>
                                        <input type="email" class="form-control" id="support_email" name="support_email"
                                            value="<?php echo htmlspecialchars($settings['after_sales']['support_email']); ?>">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <label for="customer_portal" class="form-label">Enable Customer Support
                                            Portal</label>
                                        <p class="text-muted small">Allow customers to submit and track service requests
                                            online</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="customer_portal"
                                            name="customer_portal" <?php echo $settings['after_sales']['customer_portal'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>