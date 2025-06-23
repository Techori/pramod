<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../_conn.php';
$user_name = $_SESSION['user_name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['section'] === 'General') {

    $factory_name = $_POST['factory_name'];
    $factory_address = $_POST['factory_address'];
    $factory_number = $_POST['phone_number'];
    $factory_manager = $_POST['factory_manager'];

    // Check if general settings already exist for this user
    $check_stmt = $conn->prepare("SELECT id FROM factory_general_settings WHERE created_by = ?");
    $check_stmt->bind_param("s", $user_name);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Update
        $row = $result->fetch_assoc();
        $update_stmt = $conn->prepare("UPDATE factory_general_settings SET 
            factory_name = ?, address = ?, number = ?, manager = ?
            WHERE id = ?");
        $update_stmt->bind_param("ssisi", $factory_name, $factory_address, $factory_number, $factory_manager, $row['id']);
        $update_stmt->execute();
        header("Location: factory_dashboard.php?page=settings");
    } else {
        // Insert
        $insert_stmt = $conn->prepare("INSERT INTO factory_general_settings 
            (factory_name, address, number, manager, created_by)
            VALUES (?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("ssiss", $factory_name, $factory_address, $factory_number, $factory_manager, $user_name);
        $insert_stmt->execute();
        header("Location: factory_dashboard.php?page=settings");
    }

}

// Fetch existing data to show in form
$result = $conn->query("SELECT * FROM factory_general_settings WHERE id = 1");
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['section'] === 'production') {

    $capacity = $_POST['daily_capacity'];
    $efficiency = $_POST['target_efficiency'];
    $shift = $_POST['shift_duration'];

    // Check if general settings already exist for this user
    $check_stmt = $conn->prepare("SELECT id FROM factory_production_setting WHERE created_by = ?");
    $check_stmt->bind_param("s", $user_name);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Update
        $row = $result->fetch_assoc();
        $update_stmt = $conn->prepare("UPDATE factory_production_setting SET 
            capacity = ?, efficiency = ?, shift = ?
            WHERE id = ?");
        $update_stmt->bind_param("iiii", $capacity, $efficiency, $shift, $row['id']);
        $update_stmt->execute();
        header("Location: factory_dashboard.php?page=settings");
    } else {
        // Insert
        $insert_stmt = $conn->prepare("INSERT INTO factory_production_setting 
            (capacity, efficiency, shift, created_by)
            VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("iiis", $capacity, $efficiency, $shift, $user_name);
        $insert_stmt->execute();
        header("Location: factory_dashboard.php?page=settings");
    }

}

// Fetch existing data to show in form
$result2 = $conn->query("SELECT * FROM factory_production_setting WHERE id = 1");
$production = $result2->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['section'] === 'inventory') {

    $stock_buffer = $_POST['stock_buffer'];
    $fifo_method = isset($_POST['fifo_method']) ? 1 : 0;
    $batch_tracking = isset($_POST['batch_tracking']) ? 1 : 0;
    $material_expiry = isset($_POST['material_expiry']) ? 1 : 0;

    // Check if general settings already exist for this user
    $check_stmt = $conn->prepare("SELECT id FROM factory_inventory_setting WHERE created_by = ?");
    $check_stmt->bind_param("s", $user_name);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Update
        $row = $result->fetch_assoc();
        $update_stmt = $conn->prepare("UPDATE factory_inventory_setting SET 
            stock_buffer = ?, fifo_method = ?, batch_tracking = ?, material_expiry = ?
            WHERE id = ?");
        $update_stmt->bind_param("iiiii", $stock_buffer, $fifo_method, $batch_tracking, $material_expiry, $row['id']);
        $update_stmt->execute();
        header("Location: factory_dashboard.php?page=settings");
    } else {
        // Insert
        $insert_stmt = $conn->prepare("INSERT INTO factory_inventory_setting 
            (stock_buffer, fifo_method, batch_tracking, material_expiry, created_by)
            VALUES (?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("iiiis", $stock_buffer, $fifo_method, $batch_tracking, $material_expiry, $user_name);
        $insert_stmt->execute();
        header("Location: factory_dashboard.php?page=settings");
    }

}

// Fetch existing data to show in form
$result3 = $conn->query("SELECT * FROM factory_inventory_setting WHERE id = 1");
$inventory = $result3->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['section'] === 'billing') {

    $payment_terms = $_POST['payment_terms'];
    $phone_number_general = $_POST['phone_number_general'];
    $tax_rate = $_POST['tax_rate'];
    $downtime_tracking = isset($_POST['downtime_tracking']) ? 1 : 0;

    // Check if general settings already exist for this user
    $check_stmt = $conn->prepare("SELECT id FROM factory_billing_setting WHERE created_by = ?");
    $check_stmt->bind_param("s", $user_name);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Update
        $row = $result->fetch_assoc();
        $update_stmt = $conn->prepare("UPDATE factory_billing_setting SET 
            payment_terms = ?, phone_number_general = ?, tax_rate = ?, downtime_tracking = ?
            WHERE id = ?");
        $update_stmt->bind_param("isiii", $payment_terms, $phone_number_general, $tax_rate, $downtime_tracking, $row['id']);
        $update_stmt->execute();
        header("Location: factory_dashboard.php?page=settings");
    } else {
        // Insert
        $insert_stmt = $conn->prepare("INSERT INTO factory_billing_setting 
            (payment_terms, phone_number_general, tax_rate, downtime_tracking, created_by)
            VALUES (?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("isiis", $payment_terms, $phone_number_general, $tax_rate, $downtime_tracking, $user_name);
        $insert_stmt->execute();
        header("Location: factory_dashboard.php?page=settings");
    }

}

// Fetch existing data to show in form
$result4 = $conn->query("SELECT * FROM factory_billing_setting WHERE id = 1");
$billing = $result4->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['section'] === 'workers') {

    $daily_capacity = $_POST['daily_capacity'];
    $target_efficiency = $_POST['target_efficiency'];
    $shift_duration = $_POST['shift_duration'];
    $overtime_rate = $_POST['overtime_rate'];
    $downtime_tracking = isset($_POST['downtime_tracking']) ? 1 : 0;

    // Check if general settings already exist for this user
    $check_stmt = $conn->prepare("SELECT id FROM factory_workers_setting WHERE created_by = ?");
    $check_stmt->bind_param("s", $user_name);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Update
        $row = $result->fetch_assoc();
        $update_stmt = $conn->prepare("UPDATE factory_workers_setting SET 
            daily_capacity = ?, target_efficiency = ?, shift_duration = ?, overtime_rate = ?, downtime_tracking = ?
            WHERE id = ?");
        $update_stmt->bind_param("iiisii", $daily_capacity, $target_efficiency, $shift_duration, $overtime_rate, $downtime_tracking, $row['id']);
        $update_stmt->execute();
        header("Location: factory_dashboard.php?page=settings");
    } else {
        // Insert
        $insert_stmt = $conn->prepare("INSERT INTO factory_workers_setting 
            (daily_capacity, target_efficiency, shift_duration, overtime_rate, downtime_tracking, created_by)
            VALUES (?, ?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("iiisis", $daily_capacity, $target_efficiency, $shift_duration, $overtime_rate, $downtime_tracking, $user_name);
        $insert_stmt->execute();
        header("Location: factory_dashboard.php?page=settings");
    }

}

// Fetch existing data to show in form
$result5 = $conn->query("SELECT * FROM factory_workers_setting WHERE id = 1");
$workers = $result5->fetch_assoc();

?>

<style>
    .tabs {
        display: flex;
        border-bottom: 2px solid #ddd;
    }

    .settingTab {
        padding: 10px 20px;
        cursor: pointer;
        border: none;
        background: none;
        font-size: 16px;
    }

    .settingTab.active {
        border-bottom: 3px solid #007bff;
        font-weight: bold;
        color: #007bff;
    }

    .setting-tab-content {
        display: none;
        padding: 20px 0;
    }

    .setting-tab-content.active {
        display: block;
    }
</style>

<div class="main-content container my-4">
    <h1>Factory Settings</h1>
    <p>Configure factory operations and preferences</p>

    <div class="col-md-12 card p-4 shadow-sm my-4">
        <div class="tabs">
            <button class="settingTab active" onclick="showsettingTab('general')">General</button>
            <button class="settingTab" onclick="showsettingTab('production')">Production</button>
            <button class="settingTab" onclick="showsettingTab('inventory')">Inventory</button>
            <button class="settingTab" onclick="showsettingTab('billing')">Billing</button>
            <button class="settingTab" onclick="showsettingTab('workers')">Workers</button>
        </div>

        <!-- General Settings -->
        <div class="setting-tab-content active" id="general">
            <h3>Company Profile</h3>
            <p>Update your company information and branding</p>
            <div>
                <h5>Business Details</h5>

                <form action="settings.php" method="POST">
                    <input type="hidden" name="section" value="General">
                    <div class="mb-3 m-3 col">
                        <label for="factory-name">Factory Name</label>
                        <input type="text" class="form-control" id="factory_name" name="factory_name"
                            placeholder="Unnati Electrical Factory" value="<?php echo $row['factory_name'] ?? ''; ?>">
                    </div>

                    <div class="mb-3 m-3 col">
                        <label for="factory-address">Full Address</label>
                        <textarea class="form-control" id="factory_address" name="factory_address"
                            placeholder="123 Main Street, Industrial Area, Mumbai"
                            style="height: 100px"> <?php echo $row['address'] ?? ''; ?></textarea>
                    </div>

                    <div class="mb-3 m-3 col">
                        <label for="phone-number">Phone Number</label>
                        <input type="number" class="form-control" id="phone_number" name="phone_number"
                            placeholder="9876543210" value="<?php echo $row['number'] ?? ''; ?>">
                    </div>

                    <div class="mb-3 m-3 col">
                        <label for="factory-manager">Factory Manager</label>
                        <input type="text" class="form-control" id="factory_manager" name="factory_manager"
                            placeholder="Rajesh Kumar" value="<?php echo $row['manager'] ?? ''; ?>">
                    </div>
            </div>

            <div class="text-end position-relative">
                <button type="submit" class="btn btn-info text-white">
                    <i class="bi bi-save"></i> Save Settings
                </button>
            </div>
            </form>
        </div>

        <!-- Production Settings -->
        <div class="setting-tab-content" id="production">
            <h3>Production Settings</h3>
            <p>Manage production schedules, shift timings, and machinery settings.</p>

            <form action="settings.php" method="POST">
                <input type="hidden" name="section" value="production">
                <div class="mb-3 m-3 col">
                    <label for="daily-capacity">Daily Production Capacity (units)</label>
                    <input type="number" id="daily_capacity" name="daily_capacity" class="form-control"
                        placeholder="Enter Capacity (units)..." min="0" step="1"
                        value="<?php echo $production['capacity'] ?? ''; ?>">
                </div>

                <div class="mb-3 m-3 col">
                    <label for="target-efficiency">Target Efficiency (%)</label>
                    <input type="number" id="target_efficiency" name="target_efficiency" class="form-control"
                        placeholder="Enter Efficiency (%)..." min="0" step="1"
                        value="<?php echo $production['efficiency'] ?? ''; ?>">
                </div>

                <div class="mb-3 m-3 col">
                    <label for="shift-duration">Standard Shift Duration (hours)</label>
                    <input type="number" id="shift_duration" name="shift_duration" class="form-control"
                        placeholder="Enter Duration (hours)..." min="0" step="1"
                        value="<?php echo $production['shift'] ?? ''; ?>">
                </div>

                <div class="text-end position-relative">
                    <button type="submit" class="btn btn-info text-white">
                        <i class="bi bi-save"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Inventory Settings -->
        <div class="setting-tab-content" id="inventory">
            <h3>Inventory Settings</h3>
            <p>Configure raw materials and finished goods inventory parameters</p>

            <form action="settings.php" method="POST">
                <input type="hidden" name="section" value="inventory">
                <div class="mb-3 m-3 col">
                    <label for="stock-buffer">Stock Buffer (% above minimum)</label>
                    <input type="number" id="stock_buffer" name="stock_buffer" class="form-control"
                        placeholder="Enter Stock Buffer (%)..." min="0" step="1"
                        value="<?php echo $inventory['stock_buffer'] ?? ''; ?>">
                </div>

                <div class="form-check form-switch mx-3">
                    <h5>Use FIFO Method</h5>
                    <input class="form-check-input" type="checkbox" id="fifo_method" name="fifo_method" <?php echo (!empty($inventory['fifo_method'])) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="fifo-method">First In, First Out inventory management</label>
                </div>

                <div class="form-check form-switch mx-3">
                    <h5>Batch Tracking</h5>
                    <input class="form-check-input" type="checkbox" id="batch_tracking" name="batch_tracking" <?php echo (!empty($inventory['batch_tracking'])) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="batch-tracking">Track inventory by production batch</label>
                </div>

                <div class="form-check form-switch mx-3">
                    <h5>Track Material Expiry</h5>
                    <input class="form-check-input" type="checkbox" id="material_expiry" name="material_expiry" <?php echo (!empty($inventory['material_expiry'])) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="material-expiry">Monitor expiration dates of raw
                        materials</label>
                </div>

                <div class="text-end position-relative">
                    <button type="submit" class="btn btn-info text-white">
                        <i class="bi bi-save"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Billing Settings -->
        <div class="setting-tab-content" id="billing">
            <h3>Billing Settings</h3>
            <p>Configure invoicing and payment options for factory operations</p>

            <form action="settings.php" method="POST">
                <input type="hidden" name="section" value="billing">
                <div class="mb-3 m-3 col">

                    <label for="payment-terms">Default Payment Terms (days)</label>
                    <input type="number" id="payment_terms" name="payment_terms" class="form-control"
                        placeholder="Enter Payment Terms..." min="0" step="1"
                        value="<?php echo $billing['payment_terms'] ?? ''; ?>">
                </div>
                <div class="mb-3 m-3 col">
                    <label for="-number-general">Currency</label>
                    <input type="text" class="form-control" id="phone_number_general" name="phone_number_general"
                        placeholder="INR (₹)" value="<?php echo $billing['phone_number_general'] ?? ''; ?>">
                </div>
                <div class="mb-3 m-3 col">
                    <label for="tax-rate">Default Tax Rate (%)</label>
                    <input type="number" id="tax_rate" name="tax_rate" class="form-control"
                        placeholder="Enter Tax Rate (%)..." min="0" step="1"
                        value="<?php echo $billing['tax_rate'] ?? ''; ?>">
                </div>

                <div class="form-check form-switch mx-3">
                    <h5>Downtime Tracking</h5>
                    <input class="form-check-input" type="checkbox" id="downtime_tracking" name="downtime_tracking"
                        <?php echo (!empty($billing['downtime_tracking'])) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="downtime-tracking">Track reasons and duration of production
                        line
                        downtime</label>
                </div>
                <div class="text-end position-relative">
                    <button type="submit" class="btn btn-info text-white">
                        <i class="bi bi-save"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- workers Settings -->
        <div class="setting-tab-content" id="workers">
            <h3>Workers Settings</h3>
            <p>Configure API access, automation, and custom factory integrations.</p>

            <form action="settings.php" method="POST">
                <input type="hidden" name="section" value="workers">
                <div class="mb-3 m-3 col">
                    <label for="daily-capacity">Standard Shift Hours</label>
                    <input type="number" id="daily_capacity" name="daily_capacity" class="form-control"
                        placeholder="Standard Shift Hours..." min="0" step="1"
                        value="<?php echo $workers['daily_capacity'] ?? ''; ?>">
                </div>

                <div class="mb-3 m-3 col">
                    <label for="target-efficiency"> Overtime Rate (x regular pay)</label>
                    <input type="number" id="target_efficiency" name="target_efficiency" class="form-control"
                        placeholder="Enter Overtime Rate..." min="0" step="1"
                        value="<?php echo $workers['target_efficiency'] ?? ''; ?>">
                </div>

                <div class="mb-3 m-3 col">
                    <label for="shift-duration">Lateness Threshold (minutes)</label>
                    <input type="number" id="shift_duration" name="shift_duration" class="form-control"
                        placeholder="Enter Lateness Threshold..." min="0" step="1"
                        value="<?php echo $workers['shift_duration'] ?? ''; ?>">
                </div>

                <div class="mb-3 m-3 col">
                    <label for="overtime-rate">Attendance Tracking Method</label>
                    <select id="overtime_rate" name="overtime_rate" class="form-select">
                        <option value="" <?php echo empty($attendance['overtime_rate']) ? 'selected' : ''; ?> disabled>Select</option>
                        <option value="Biometric">Biometric</option>
                        <option value="RFID Card" <?php echo ($workers['overtime_rate'] == 'RFID Card') ? 'selected' : ''; ?>>RFID Card</option>
                        <option value="Manual Entry" <?php echo ($workers['overtime_rate'] == 'Manual Entry') ? 'selected' : ''; ?>>Manual Entry</option>
                        <option value="Mobile App" <?php echo ($workers['overtime_rate'] == 'Mobile App') ? 'selected' : ''; ?>>Mobile App</option>
                    </select>
                </div>

                <div class="form-check form-switch mx-3">
                    <h5>Enable Skill Tracking
                    </h5>
                    <input class="form-check-input" type="checkbox" id="downtime_tracking" name="downtime_tracking"
                        <?php echo (!empty($workers['downtime_tracking'])) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="downtime-tracking">Track worker skills and
                        certifications</label>
                </div>
                <div class="text-end position-relative">
                    <button type="submit" class="btn btn-info text-white">
                        <i class="bi bi-save"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>

    </div>


    <script>
        function showsettingTab(tabId) {
            // Remove active class from all tabs
            document.querySelectorAll('.settingTab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.setting-tab-content').forEach(content => content.classList.remove('active'));

            // Add active class to clicked tab
            document.querySelector(`.settingTab[onclick="showsettingTab('${tabId}')"]`).classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }


    </script>