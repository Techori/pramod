<?php
// Include mock database
require_once 'database.php';

// Get settings data
$settings = get_store_settings();

// Handle form submissions
$success_message = '';
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['section'])) {
        $section = $_POST['section'];
        $success_message = "$section settings have been saved successfully.";
    } elseif (isset($_POST['action'])) {
        $action = $_POST['action'];
        switch ($action) {
            case 'test_printer':
                $success_message = 'Receipt printer test initiated successfully.';
                break;
            case 'test_scanner':
                $success_message = 'Barcode scanner test initiated successfully.';
                break;
            case 'test_display':
                $success_message = 'Customer display test initiated successfully.';
                break;
            case 'test_terminal':
                $success_message = 'Payment terminal test initiated successfully.';
                break;
            case 'open_drawer':
                $success_message = 'Cash drawer opened successfully.';
                break;
            case 'edit_user':
                $success_message = 'Edit user operation initiated successfully.';
                break;
            case 'add_user':
                $success_message = 'Add new user operation initiated successfully.';
                break;
            case 'edit_role':
                $success_message = 'Edit role operation initiated successfully.';
                break;
            case 'add_role':
                $success_message = 'Add new role operation initiated successfully.';
                break;
        }
    }
}

// Get active tab
$active_tab = isset($_GET['tab']) && in_array($_GET['tab'], ['general', 'billing', 'inventory', 'hardware', 'notifications', 'users', 'afterSales']) ? $_GET['tab'] : 'general';

// Status badge function
function get_status_badge($status) {
    $status_config = [
        'Active' => ['class' => 'bg-green-subtle text-green', 'label' => 'Active'],
        'On Leave' => ['class' => 'bg-yellow-subtle text-yellow', 'label' => 'On Leave']
    ];
    $config = isset($status_config[$status]) ? $status_config[$status] : ['class' => 'bg-secondary-subtle text-secondary', 'label' => $status];
    return "<span class='badge {$config['class']}'>{$config['label']}</span>";
}
?>

<div class="main-content">
    <h1><i class="fas fa-cog text-primary me-2"></i> Store Settings</h1>

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
    <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $active_tab === 'general' ? 'active' : ''; ?>" href="?page=settings&tab=general" role="tab">General</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $active_tab === 'billing' ? 'active' : ''; ?>" href="?page=settings&tab=billing" role="tab">Billing</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $active_tab === 'inventory' ? 'active' : ''; ?>" href="?page=settings&tab=inventory" role="tab">Inventory</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $active_tab === 'hardware' ? 'active' : ''; ?>" href="?page=settings&tab=hardware" role="tab">Hardware</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $active_tab === 'notifications' ? 'active' : ''; ?>" href="?page=settings&tab=notifications" role="tab">Notifications</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $active_tab === 'users' ? 'active' : ''; ?>" href="?page=settings&tab=users" role="tab">Users & Permissions</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link <?php echo $active_tab === 'afterSales' ? 'active' : ''; ?>" href="?page=settings&tab=afterSales" role="tab">After-Sales Service</a>
        </li>
    </ul>

    <div class="tab-content" id="settingsTabsContent">
        <!-- General Settings -->
        <div class="tab-pane fade <?php echo $active_tab === 'general' ? 'show active' : ''; ?>" id="general" role="tabpanel">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">General Settings</h5>
                    <p class="text-muted mb-4">Configure your store information and preferences</p>
                    <form method="POST" action="?page=settings&tab=general">
                        <input type="hidden" name="section" value="General">
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="store_name" class="form-label">Store Name</label>
                                <input type="text" class="form-control" id="store_name" name="store_name" value="<?php echo htmlspecialchars($settings['general']['store_name']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="store_code" class="form-label">Store Code</label>
                                <input type="text" class="form-control" id="store_code" name="store_code" value="<?php echo htmlspecialchars($settings['general']['store_code']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="store_phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="store_phone" name="store_phone" value="<?php echo htmlspecialchars($settings['general']['store_phone']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="store_email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="store_email" name="store_email" value="<?php echo htmlspecialchars($settings['general']['store_email']); ?>">
                            </div>
                            <div class="col-12">
                                <label for="store_address" class="form-label">Store Address</label>
                                <textarea class="form-control" id="store_address" name="store_address" rows="4"><?php echo htmlspecialchars($settings['general']['store_address']); ?></textarea>
                            </div>
                            <div class="col-12">
                                <label for="store_manager" class="form-label">Store Manager</label>
                                <input type="text" class="form-control" id="store_manager" name="store_manager" value="<?php echo htmlspecialchars($settings['general']['store_manager']); ?>">
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <label for="store_active" class="form-label">Store Active</label>
                                    <p class="text-muted small">Enable or disable this store location</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="store_active" name="store_active" <?php echo $settings['general']['store_active'] ? 'checked' : ''; ?>>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <div>
                                    <label for="accept_online_orders" class="form-label">Accept Online Orders</label>
                                    <p class="text-muted small">Allow customers to place orders online for this store</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="accept_online_orders" name="accept_online_orders" <?php echo $settings['general']['accept_online_orders'] ? 'checked' : ''; ?>>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Billing Settings -->
        <div class="tab-pane fade <?php echo $active_tab === 'billing' ? 'show active' : ''; ?>" id="billing" role="tabpanel">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Billing Settings</h5>
                    <p class="text-muted mb-4">Configure invoicing and payment options</p>
                    <form method="POST" action="?page=settings&tab=billing">
                        <input type="hidden" name="section" value="Billing">
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="invoice_prefix" class="form-label">Invoice Number Prefix</label>
                                <input type="text" class="form-control" id="invoice_prefix" name="invoice_prefix" value="<?php echo htmlspecialchars($settings['billing']['invoice_prefix']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="receipt_prefix" class="form-label">Receipt Number Prefix</label>
                                <input type="text" class="form-control" id="receipt_prefix" name="receipt_prefix" value="<?php echo htmlspecialchars($settings['billing']['receipt_prefix']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="currency" class="form-label">Currency</label>
                                <input type="text" class="form-control" id="currency" name="currency" value="<?php echo htmlspecialchars($settings['billing']['currency']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="tax_rate" class="form-label">Default Tax Rate (%)</label>
                                <input type="number" class="form-control" id="tax_rate" name="tax_rate" value="<?php echo htmlspecialchars($settings['billing']['tax_rate']); ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Accepted Payment Methods</label>
                                <div class="row g-3">
                                    <?php
                                    $available_methods = ['Cash', 'Card', 'UPI', 'Cheque'];
                                    foreach ($available_methods as $method):
                                    ?>
                                    <div class="col-md-3 col-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="<?php echo strtolower($method); ?>" name="accepted_payment_methods[]" value="<?php echo $method; ?>" <?php echo in_array($method, $settings['billing']['accepted_payment_methods']) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="<?php echo strtolower($method); ?>"><?php echo $method; ?></label>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <label for="digital_receipt" class="form-label">Digital Receipts</label>
                                    <p class="text-muted small">Send receipts via email or SMS</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="digital_receipt" name="digital_receipt" <?php echo $settings['billing']['digital_receipt'] ? 'checked' : ''; ?>>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <div>
                                    <label for="invoice_logo" class="form-label">Show Logo on Invoice</label>
                                    <p class="text-muted small">Display store logo on printed invoices</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="invoice_logo" name="invoice_logo" <?php echo $settings['billing']['invoice_logo'] ? 'checked' : ''; ?>>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <div>
                                    <label for="terms_on_invoice" class="form-label">Show Terms & Conditions</label>
                                    <p class="text-muted small">Display T&C on printed invoices</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="terms_on_invoice" name="terms_on_invoice" <?php echo $settings['billing']['terms_on_invoice'] ? 'checked' : ''; ?>>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="invoice_terms" class="form-label">Invoice Terms & Conditions</label>
                                <textarea class="form-control" id="invoice_terms" name="invoice_terms" rows="4"><?php echo htmlspecialchars($settings['billing']['invoice_terms']); ?></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Inventory Settings -->
        <div class="tab-pane fade <?php echo $active_tab === 'inventory' ? 'show active' : ''; ?>" id="inventory" role="tabpanel">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Inventory Settings</h5>
                    <p class="text-muted mb-4">Configure inventory management preferences</p>
                    <form method="POST" action="?page=settings&tab=inventory">
                        <input type="hidden" name="section" value="Inventory">
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label for="low_stock_threshold" class="form-label">Low Stock Threshold (%)</label>
                                <input type="number" class="form-control" id="low_stock_threshold" name="low_stock_threshold" value="<?php echo htmlspecialchars($settings['inventory']['low_stock_threshold']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="reorder_point" class="form-label">Default Reorder Point</label>
                                <input type="number" class="form-control" id="reorder_point" name="reorder_point" value="<?php echo htmlspecialchars($settings['inventory']['reorder_point']); ?>">
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <label for="auto_reorder" class="form-label">Automatic Reordering</label>
                                    <p class="text-muted small">Automatically create purchase orders for low stock items</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="auto_reorder" name="auto_reorder" <?php echo $settings['inventory']['auto_reorder'] ? 'checked' : ''; ?>>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <div>
                                    <label for="track_serial_numbers" class="form-label">Track Serial Numbers</label>
                                    <p class="text-muted small">Record serial numbers for applicable products</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="track_serial_numbers" name="track_serial_numbers" <?php echo $settings['inventory']['track_serial_numbers'] ? 'checked' : ''; ?>>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <div>
                                    <label for="allow_negative_stock" class="form-label">Allow Negative Stock</label>
                                    <p class="text-muted small">Allow sales when inventory count is zero</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="allow_negative_stock" name="allow_negative_stock" <?php echo $settings['inventory']['allow_negative_stock'] ? 'checked' : ''; ?>>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <div>
                                    <label for="barcode_scanning" class="form-label">Barcode Scanning</label>
                                    <p class="text-muted small">Use barcode scanner for inventory management</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="barcode_scanning" name="barcode_scanning" <?php echo $settings['inventory']['barcode_scanning'] ? 'checked' : ''; ?>>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="inventory_method" class="form-label">Inventory Valuation Method</label>
                                <select class="form-select" id="inventory_method" name="inventory_method">
                                    <option value="fifo" <?php echo $settings['inventory']['inventory_method'] === 'fifo' ? 'selected' : ''; ?>>FIFO (First In, First Out)</option>
                                    <option value="lifo" <?php echo $settings['inventory']['inventory_method'] === 'lifo' ? 'selected' : ''; ?>>LIFO (Last In, First Out)</option>
                                    <option value="avg" <?php echo $settings['inventory']['inventory_method'] === 'avg' ? 'selected' : ''; ?>>Moving Average</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="stock_count_frequency" class="form-label">Stock Count Frequency</label>
                                <select class="form-select" id="stock_count_frequency" name="stock_count_frequency">
                                    <option value="weekly" <?php echo $settings['inventory']['stock_count_frequency'] === 'weekly' ? 'selected' : ''; ?>>Weekly</option>
                                    <option value="biweekly" <?php echo $settings['inventory']['stock_count_frequency'] === 'biweekly' ? 'selected' : ''; ?>>Bi-weekly</option>
                                    <option value="monthly" <?php echo $settings['inventory']['stock_count_frequency'] === 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                                    <option value="quarterly" <?php echo $settings['inventory']['stock_count_frequency'] === 'quarterly' ? 'selected' : ''; ?>>Quarterly</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Hardware Settings -->
        <div class="tab-pane fade <?php echo $active_tab === 'hardware' ? 'show active' : ''; ?>" id="hardware" role="tabpanel">
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
                                        <input class="form-check-input" type="checkbox" id="receipt_printer" name="receipt_printer" <?php echo $settings['hardware']['receipt_printer'] ? 'checked' : ''; ?>>
                                    </div>
                                    <button type="submit" name="action" value="test_printer" class="btn btn-outline-primary btn-sm"><i class="fas fa-print me-1"></i> Test</button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="printer_model" class="form-label">Printer Model</label>
                                <select class="form-select" id="printer_model" name="printer_model">
                                    <option value="epson" <?php echo $settings['hardware']['printer_model'] === 'epson' ? 'selected' : ''; ?>>Epson TM-T88VI</option>
                                    <option value="star" <?php echo $settings['hardware']['printer_model'] === 'star' ? 'selected' : ''; ?>>Star Micronics TSP143III</option>
                                    <option value="citizen" <?php echo $settings['hardware']['printer_model'] === 'citizen' ? 'selected' : ''; ?>>Citizen CT-S310II</option>
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
                                        <input class="form-check-input" type="checkbox" id="barcode_scanner" name="barcode_scanner" <?php echo $settings['hardware']['barcode_scanner'] ? 'checked' : ''; ?>>
                                    </div>
                                    <button type="submit" name="action" value="test_scanner" class="btn btn-outline-primary btn-sm">Test</button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="scanner_model" class="form-label">Scanner Model</label>
                                <select class="form-select" id="scanner_model" name="scanner_model">
                                    <option value="honeywell" <?php echo $settings['hardware']['scanner_model'] === 'honeywell' ? 'selected' : ''; ?>>Honeywell Voyager 1250g</option>
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
                                        <input class="form-check-input" type="checkbox" id="customer_display" name="customer_display" <?php echo $settings['hardware']['customer_display'] ? 'checked' : ''; ?>>
                                    </div>
                                    <button type="submit" name="action" value="test_display" class="btn btn-outline-primary btn-sm">Test</button>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <label for="payment_terminal" class="form-label">Payment Terminal</label>
                                    <p class="text-muted small">Enable integrated card payment terminal</p>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="payment_terminal" name="payment_terminal" <?php echo $settings['hardware']['payment_terminal'] ? 'checked' : ''; ?>>
                                    </div>
                                    <button type="submit" name="action" value="test_terminal" class="btn btn-outline-primary btn-sm"><i class="fas fa-credit-card me-1"></i> Test</button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="terminal_provider" class="form-label">Payment Terminal Provider</label>
                                <select class="form-select" id="terminal_provider" name="terminal_provider">
                                    <option value="pine" <?php echo $settings['hardware']['terminal_provider'] === 'pine' ? 'selected' : ''; ?>>Pine Labs</option>
                                    <option value="paytm" <?php echo $settings['hardware']['terminal_provider'] === 'paytm' ? 'selected' : ''; ?>>Paytm</option>
                                    <option value="razorpay" <?php echo $settings['hardware']['terminal_provider'] === 'razorpay' ? 'selected' : ''; ?>>Razorpay</option>
                                    <option value="custom" <?php echo $settings['hardware']['terminal_provider'] === 'custom' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <label for="cash_drawer" class="form-label">Cash Drawer</label>
                                    <p class="text-muted small">Enable automatic cash drawer</p>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="cash_drawer" name="cash_drawer" <?php echo $settings['hardware']['cash_drawer'] ? 'checked' : ''; ?>>
                                    </div>
                                    <button type="submit" name="action" value="open_drawer" class="btn btn-outline-primary btn-sm"><i class="fas fa-receipt me-1"></i> Open</button>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="tab-pane fade <?php echo $active_tab === 'notifications' ? 'show active' : ''; ?>" id="notifications" role="tabpanel">
            <div class="card card-border shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">Notification Settings</h5>
                    <p class="text-muted mb-4">Configure alerts and notifications</p>
                    <form method="POST" action="?page=settings&tab=notifications">
                        <input type="hidden" name="section" value="Notifications">
                        <div class="mb-4">
                            <h6 class="font-medium mb-3">Email Notifications</h6>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="low_stock_email" class="form-label">Low Stock Alerts</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="low_stock_email" name="low_stock_email" <?php echo $settings['notifications']['email_notifications']['low_stock_email'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="new_order_email" class="form-label">New Order Notifications</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="new_order_email" name="new_order_email" <?php echo $settings['notifications']['email_notifications']['new_order_email'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="end_of_day_email" class="form-label">End of Day Reports</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="end_of_day_email" name="end_of_day_email" <?php echo $settings['notifications']['email_notifications']['end_of_day_email'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="customer_feedback_email" class="form-label">Customer Feedback</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="customer_feedback_email" name="customer_feedback_email" <?php echo $settings['notifications']['email_notifications']['customer_feedback_email'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                            </div>

                            <div class="border-top pt-4">
                                <h6 class="font-medium mb-3">SMS Notifications</h6>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="order_sms" class="form-label">Order Status Updates</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="order_sms" name="order_sms" <?php echo $settings['notifications']['sms_notifications']['order_sms'] ? 'checked' : ''; ?>>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="delivery_sms" class="form-label">Delivery Notifications</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="delivery_sms" name="delivery_sms" <?php echo $settings['notifications']['sms_notifications']['delivery_sms'] ? 'checked' : ''; ?>>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="promotional_sms" class="form-label">Promotional Messages</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="promotional_sms" name="promotional_sms" <?php echo $settings['notifications']['sms_notifications']['promotional_sms'] ? 'checked' : ''; ?>>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="border-top pt-4">
                                <h6 class="font-medium mb-3">System Notifications</h6>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="dashboard_notifications" class="form-label">Dashboard Alerts</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="dashboard_notifications" name="dashboard_notifications" <?php echo $settings['notifications']['system_notifications']['dashboard_notifications'] ? 'checked' : ''; ?>>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="browser_notifications" class="form-label">Browser Notifications</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="browser_notifications" name="browser_notifications" <?php echo $settings['notifications']['system_notifications']['browser_notifications'] ? 'checked' : ''; ?>>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="mobile_notifications" class="form-label">Mobile App Notifications</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="mobile_notifications" name="mobile_notifications" <?php echo $settings['notifications']['system_notifications']['mobile_notifications'] ? 'checked' : ''; ?>>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4">
                                <label for="notification_emails" class="form-label">Email Recipients</label>
                                <textarea class="form-control" id="notification_emails" name="notification_emails" rows="4" placeholder="Enter email addresses separated by commas"><?php echo htmlspecialchars($settings['notifications']['notification_emails']); ?></textarea>
                                <p class="text-muted small mt-1">These emails will receive all notifications</p>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Users & Permissions Settings -->
        <div class="tab-pane fade <?php echo $active_tab === 'users' ? 'show active' : ''; ?>" id="users" role="tabpanel">
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
                                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                        <i class="fas fa-users text-muted"></i>
                                                    </div>
                                                    <div class="ms-3">
                                                        <p class="mb-0 font-medium"><?php echo htmlspecialchars($user['name']); ?></p>
                                                        <p class="mb-0 text-muted small"><?php echo htmlspecialchars($user['email']); ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3"><?php echo htmlspecialchars($user['role']); ?></td>
                                            <td class="px-4 py-3"><?php echo get_status_badge($user['status']); ?></td>
                                            <td class="px-4 py-3"><?php echo htmlspecialchars($user['last_login']); ?></td>
                                            <td class="px-4 py-3 text-end">
                                                <button type="submit" name="action" value="edit_user" class="btn btn-outline-primary btn-sm">Edit</button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Add New User Button -->
                            <button type="submit" name="action" value="add_user" class="btn btn-outline-primary mb-4">
                                <i class="fas fa-users me-2"></i> Add New User
                            </button>

                            <!-- Role Permissions -->
                            <div class="border-top pt-4">
                                <h6 class="font-medium mb-3">Role Permissions</h6>
                                <?php foreach ($settings['roles'] as $role): ?>
                                <div class="border rounded p-4 mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h6 class="font-medium"><?php echo htmlspecialchars($role['name']); ?></h6>
                                            <p class="text-muted small"><?php echo htmlspecialchars($role['description']); ?></p>
                                        </div>
                                        <button type="submit" name="action" value="edit_role" class="btn btn-outline-primary btn-sm">Edit Role</button>
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
                                        <?php endforeach; } ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>

                                <!-- Add New Role Button -->
                                <button type="submit" name="action" value="add_role" class="btn btn-outline-primary">
                                    <i class="fas fa-shield-alt me-2"></i> Add New Role
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- After-Sales Service Settings -->
        <div class="tab-pane fade <?php echo $active_tab === 'afterSales' ? 'show active' : ''; ?>" id="afterSales" role="tabpanel">
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
                                        <label for="default_warranty" class="form-label">Default Warranty Period (months)</label>
                                        <input type="number" class="form-control" id="default_warranty" name="default_warranty" value="<?php echo htmlspecialchars($settings['after_sales']['warranty']['default_warranty']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="extended_warranty" class="form-label">Extended Warranty Period (months)</label>
                                        <input type="number" class="form-control" id="extended_warranty" name="extended_warranty" value="<?php echo htmlspecialchars($settings['after_sales']['warranty']['extended_warranty']); ?>">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <label for="warranty_tracking" class="form-label">Enable Warranty Tracking</label>
                                        <p class="text-muted small">Track warranty status based on product serial numbers</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="warranty_tracking" name="warranty_tracking" <?php echo $settings['after_sales']['warranty']['warranty_tracking'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                            </div>

                            <!-- Returns Policy -->
                            <div class="border-top pt-4">
                                <h6 class="font-medium mb-3">Returns Policy</h6>
                                <div class="row g-4 mb-3">
                                    <div class="col-md-6">
                                        <label for="return_period" class="form-label">Return Period (days)</label>
                                        <input type="number" class="form-control" id="return_period" name="return_period" value="<?php echo htmlspecialchars($settings['after_sales']['returns']['return_period']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="return_policy" class="form-label">Return Processing Method</label>
                                        <select class="form-select" id="return_policy" name="return_policy">
                                            <option value="exchange" <?php echo $settings['after_sales']['returns']['return_policy'] === 'exchange' ? 'selected' : ''; ?>>Exchange Only</option>
                                            <option value="refund" <?php echo $settings['after_sales']['returns']['return_policy'] === 'refund' ? 'selected' : ''; ?>>Refund Only</option>
                                            <option value="both" <?php echo $settings['after_sales']['returns']['return_policy'] === 'both' ? 'selected' : ''; ?>>Exchange or Refund</option>
                                            <option value="credit" <?php echo $settings['after_sales']['returns']['return_policy'] === 'credit' ? 'selected' : ''; ?>>Store Credit</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="returns_conditions" class="form-label">Returns Conditions</label>
                                    <textarea class="form-control" id="returns_conditions" name="returns_conditions" rows="4"><?php echo htmlspecialchars($settings['after_sales']['returns']['returns_conditions']); ?></textarea>
                                </div>
                            </div>

                            <!-- Service Centers -->
                            <div class="border-top pt-4">
                                <h6 class="font-medium mb-3">Service Centers</h6>
                                <div class="mb-3">
                                    <label for="service_centers" class="form-label">Authorized Service Centers</label>
                                    <textarea class="form-control" id="service_centers" name="service_centers" rows="4"><?php echo htmlspecialchars($settings['after_sales']['service_centers']['centers']); ?></textarea>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <label for="doorstep_service" class="form-label">Offer Doorstep Service</label>
                                        <p class="text-muted small">Provide on-site service for eligible products</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="doorstep_service" name="doorstep_service" <?php echo $settings['after_sales']['service_centers']['doorstep_service'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <label for="express_service" class="form-label">Express Service Option</label>
                                        <p class="text-muted small">Offer premium service with faster turnaround</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="express_service" name="express_service" <?php echo $settings['after_sales']['service_centers']['express_service'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                            </div>

                            <!-- Customer Support -->
                            <div class="border-top pt-4">
                                <h6 class="font-medium mb-3">Customer Support</h6>
                                <div class="row g-4 mb-3">
                                    <div class="col-md-6">
                                        <label for="support_phone" class="form-label">Support Phone Number</label>
                                        <input type="text" class="form-control" id="support_phone" name="support_phone" value="<?php echo htmlspecialchars($settings['after_sales']['customer_support']['support_phone']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="support_email" class="form-label">Support Email</label>
                                        <input type="email" class="form-control" id="support_email" name="support_email" value="<?php echo htmlspecialchars($settings['after_sales']['customer_support']['support_email']); ?>">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <label for="customer_portal" class="form-label">Enable Customer Support Portal</label>
                                        <p class="text-muted small">Allow customers to submit and track service requests online</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="customer_portal" name="customer_portal" <?php echo $settings['after_sales']['customer_support']['customer_portal'] ? 'checked' : ''; ?>>
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