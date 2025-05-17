<?php
include '../../_conn.php';

$action = $_POST['whatAction'] ?? '';

if ($action === "updateBusinessDetails") {
    // Create default row if not exists
    $conn->query("INSERT INTO admin_business_details (id) SELECT 1 FROM DUAL WHERE NOT EXISTS (SELECT * FROM admin_business_details WHERE id = 1)");

    $factory_name = $_POST['factory_name'];
    $factory_address = $_POST['factory_address'];
    $factory_location = $_POST['factory_location'];
    $phone_number = $_POST['phone_number'];
    $factory_manager = $_POST['factory_manager'];

    $stmt = $conn->prepare("UPDATE admin_business_details SET factory_name=?, factory_address=?, factory_location=?, phone_number=?, factory_manager=? WHERE id = 1");
    $stmt->bind_param("sssss", $factory_name, $factory_address, $factory_location, $phone_number, $factory_manager);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_dashboard.php?page=settings");

}

// Fetch existing data to show in form
$result = $conn->query("SELECT * FROM admin_business_details WHERE id = 1");
$row = $result->fetch_assoc();

if ($action === "updateProductionSettings") {
    // Insert default row if not exists
    $conn->query("INSERT INTO admin_production_settings (id) SELECT 1 FROM DUAL WHERE NOT EXISTS (SELECT * FROM admin_production_settings WHERE id = 1)");

    $daily_capacity = $_POST['daily_capacity'];
    $target_efficiency = $_POST['target_efficiency'];
    $shift_duration = $_POST['shift_duration'];
    $auto_scheduling = isset($_POST['auto_scheduling']) ? 1 : 0;
    $downtime_tracking = isset($_POST['downtime_tracking']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE admin_production_settings SET daily_capacity=?, target_efficiency=?, shift_duration=?, auto_scheduling=?, downtime_tracking=? WHERE id = 1");
    $stmt->bind_param("iiiii", $daily_capacity, $target_efficiency, $shift_duration, $auto_scheduling, $downtime_tracking);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_dashboard.php?page=settings");
}

// Fetch production data to pre-fill fields
$result2 = $conn->query("SELECT * FROM admin_production_settings WHERE id = 1");
$production = $result2->fetch_assoc();

if ($action === "updateInventorySettings") {
    // Ensure row exists
    $conn->query("INSERT INTO admin_inventory_settings (id) SELECT 1 FROM DUAL WHERE NOT EXISTS (SELECT * FROM admin_inventory_settings WHERE id = 1)");

    $stock_buffer = $_POST['stock_buffer'];
    $lead_time = $_POST['lead_time'];
    $auto_reorder = isset($_POST['auto_reorder']) ? 1 : 0;
    $fifo_method = isset($_POST['fifo_method']) ? 1 : 0;
    $batch_tracking = isset($_POST['batch_tracking']) ? 1 : 0;
    $material_expiry = isset($_POST['material_expiry']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE admin_inventory_settings SET stock_buffer=?, lead_time=?, auto_reorder=?, fifo_method=?, batch_tracking=?, material_expiry=? WHERE id = 1");
    $stmt->bind_param("iiiiii", $stock_buffer, $lead_time, $auto_reorder, $fifo_method, $batch_tracking, $material_expiry);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_dashboard.php?page=settings");
}

$result3 = $conn->query("SELECT * FROM admin_inventory_settings WHERE id = 1");
$inventory = $result3->fetch_assoc();

if ($action === "updateGeneralSettings") {
    // Ensure row exists
    $conn->query("INSERT INTO admin_billing_settings (id) SELECT 1 FROM DUAL WHERE NOT EXISTS (SELECT * FROM admin_billing_settings WHERE id = 1)");

    $standard_shift_hours = $_POST['standard_shift_hours'];
    $payment_terms = $_POST['payment_terms'];
    $tax_rate = $_POST['tax_rate'];

    $stmt = $conn->prepare("UPDATE admin_billing_settings SET standard_shift_hours=?, payment_terms=?, tax_rate=? WHERE id = 1");
    $stmt->bind_param("sii", $standard_shift_hours, $payment_terms, $tax_rate);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_dashboard.php?page=settings");
}

$result4 = $conn->query("SELECT * FROM admin_billing_settings WHERE id = 1");
$general = $result4->fetch_assoc();

if ($action === "updateAttendanceSettings") {
    $conn->query("INSERT INTO admin_workers_settings (id) SELECT 1 FROM DUAL WHERE NOT EXISTS (SELECT * FROM admin_workers_settings WHERE id = 1)");

    $standard_shift_hours = $_POST['standard_shift_hours'];
    $overtime_rate = $_POST['overtime_rate'];
    $lateness_threshold = $_POST['lateness_threshold'];
    $attendance_method = $_POST['attendance_method'];
    $auto_timesheet = isset($_POST['auto_timesheet']) ? 1 : 0;
    $skill_tracking = isset($_POST['skill_tracking']) ? 1 : 0;
    $safety_alerts = isset($_POST['safety_alerts']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE admin_workers_settings SET 
        standard_shift_hours = ?, 
        overtime_rate = ?, 
        lateness_threshold = ?, 
        attendance_method = ?, 
        auto_timesheet = ?, 
        skill_tracking = ?, 
        safety_alerts = ? 
        WHERE id = 1");

    $stmt->bind_param("idissii", $standard_shift_hours, $overtime_rate, $lateness_threshold, $attendance_method, $auto_timesheet, $skill_tracking, $safety_alerts);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_dashboard.php?page=settings");
}

$result5 = $conn->query("SELECT * FROM admin_workers_settings WHERE id = 1");
$attendance = $result5 && $result5->num_rows > 0 ? $result5->fetch_assoc() : [];


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
    <h1>Admin Settings</h1>
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
            <form method="POST" action="settings.php">
                <input type="hidden" name="whatAction" value="updateBusinessDetails" />

                <div>
                    <h5>Business Details</h5>

                    <div class="mb-3 m-3 col">
                        <label for="factory-name">Factory Name</label>
                        <input type="text" class="form-control" id="factory-name" name="factory_name"
                            placeholder="Unnati Electrical Factory" value="<?php echo $row['factory_name'] ?? ''; ?>">
                    </div>

                    <div class="mb-3 m-3 col">
                        <label for="factory-address">Full Address</label>
                        <textarea class="form-control" id="factory-address" name="factory_address"
                            placeholder="123 Main Street, Industrial Area, Mumbai"
                            style="height: 100px"><?php echo $row['factory_address'] ?? ''; ?></textarea>
                    </div>

                    <div class="mb-3 m-3 col">
                        <label for="factory-location">Location</label>
                        <input type="text" class="form-control" id="factory-location" name="factory_location"
                            placeholder="Industrial Area Phase II, Mumbai"
                            value="<?php echo $row['factory_location'] ?? ''; ?>">
                    </div>

                    <div class="mb-3 m-3 col">
                        <label for="phone-number">Phone Number</label>
                        <input type="number" class="form-control" id="phone-number" name="phone_number"
                            placeholder="9876543210" value="<?php echo $row['phone_number'] ?? ''; ?>">
                    </div>

                    <div class="mb-3 m-3 col">
                        <label for="factory-manager">Factory Manager</label>
                        <input type="text" class="form-control" id="factory-manager" name="factory_manager"
                            placeholder="Rajesh Kumar" value="<?php echo $row['factory_manager'] ?? ''; ?>">
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
            
            <form method="POST" action="settings.php">
      <input type="hidden" name="whatAction" value="updateProductionSettings" />
    <div class="mb-3 m-3 col">
      <label for="daily-capacity">Daily Production Capacity (units)</label>
      <input type="number" id="daily-capacity" name="daily_capacity" class="form-control"
        placeholder="Enter Capacity (units)..." min="0" step="1" 
        value="<?php echo $production['daily_capacity'] ?? ''; ?>">
    </div>

    <div class="mb-3 m-3 col">
      <label for="target-efficiency">Target Efficiency (%)</label>
      <input type="number" id="target-efficiency" name="target_efficiency" class="form-control"
        placeholder="Enter Efficiency (%)..." min="0" step="1" 
        value="<?php echo $production['target_efficiency'] ?? ''; ?>">
    </div>

    <div class="mb-3 m-3 col">
      <label for="shift-duration">Standard Shift Duration (hours)</label>
      <input type="number" id="shift-duration" name="shift_duration" class="form-control"
        placeholder="Enter Duration (hours)..." min="0" step="1"
        value="<?php echo $production['shift_duration'] ?? ''; ?>">
    </div>

    <div class="form-check form-switch mx-3">
      <h5>Automatic Production Scheduling</h5>
      <input class="form-check-input" type="checkbox" id="auto-scheduling" name="auto_scheduling"
        <?php echo (!empty($production['auto_scheduling'])) ? 'checked' : ''; ?>>
      <label class="form-check-label" for="auto-scheduling">Automatically schedule production based on orders</label>
    </div>

    <div class="form-check form-switch mx-3">
      <h5>Downtime Tracking</h5>
      <input class="form-check-input" type="checkbox" id="downtime-tracking" name="downtime_tracking"
        <?php echo (!empty($production['downtime_tracking'])) ? 'checked' : ''; ?>>
      <label class="form-check-label" for="downtime-tracking">Track reasons and duration of production line downtime</label>
    </div>

    <div class="text-end position-relative mt-3">
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

            <form method="POST" action="settings.php">
  <input type="hidden" name="whatAction" value="updateInventorySettings">

  <div class="mb-3 m-3 col">
    <label for="stock-buffer">Stock Buffer (% above minimum)</label>
    <input type="number" id="stock-buffer" name="stock_buffer" class="form-control"
      placeholder="Enter Stock Buffer (%)..." min="0" step="1"
      value="<?php echo $inventory['stock_buffer'] ?? ''; ?>">
  </div>

  <div class="mb-3 m-3 col">
    <label for="lead-time">Default Reorder Lead Time (days)</label>
    <input type="number" id="lead-time" name="lead_time" class="form-control"
      placeholder="Enter Lead Time (days)..." min="0" step="1"
      value="<?php echo $inventory['lead_time'] ?? ''; ?>">
  </div>

  <div class="form-check form-switch mx-3">
    <h5>Automatic Reordering</h5>
    <input class="form-check-input" type="checkbox" id="auto-reorder" name="auto_reorder"
      <?php echo (!empty($inventory['auto_reorder'])) ? 'checked' : ''; ?>>
    <label class="form-check-label" for="auto-reorder">Automatically generate purchase orders for low stock items</label>
  </div>

  <div class="form-check form-switch mx-3">
    <h5>Use FIFO Method</h5>
    <input class="form-check-input" type="checkbox" id="fifo-method" name="fifo_method"
      <?php echo (!empty($inventory['fifo_method'])) ? 'checked' : ''; ?>>
    <label class="form-check-label" for="fifo-method">First In, First Out inventory management</label>
  </div>

  <div class="form-check form-switch mx-3">
    <h5>Batch Tracking</h5>
    <input class="form-check-input" type="checkbox" id="batch-tracking" name="batch_tracking"
      <?php echo (!empty($inventory['batch_tracking'])) ? 'checked' : ''; ?>>
    <label class="form-check-label" for="batch-tracking">Track inventory by production batch</label>
  </div>

  <div class="form-check form-switch mx-3">
    <h5>Track Material Expiry</h5>
    <input class="form-check-input" type="checkbox" id="material-expiry" name="material_expiry"
      <?php echo (!empty($inventory['material_expiry'])) ? 'checked' : ''; ?>>
    <label class="form-check-label" for="material-expiry">Monitor expiration dates of raw materials</label>
  </div>

  <div class="text-end position-relative mt-3">
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
            
            <form method="POST" action="settings.php">
                <input type="hidden" name="whatAction" value="updateGeneralSettings">

                <div class="mb-3 m-3 col">
                    <label for="standard_shift_hours">Standard Shift Hours</label>
                    <input type="text" class="form-control" id="Standard-billing" name="standard_shift_hours"
                    placeholder="UNT-FAC-INV"
                    value="<?php echo $general['standard_shift_hours'] ?? ''; ?>">
                </div>

                <div class="mb-3 m-3 col">
                    <label for="payment-terms">Default Payment Terms (days)</label>
                    <input type="number" id="payment-terms" name="payment_terms" class="form-control"
                    placeholder="Enter Payment Terms..." min="0" step="1"
                    value="<?php echo $general['payment_terms'] ?? ''; ?>">
                </div>

                <div class="mb-3 m-3 col">
                    <label for="tax-rate">Default Tax Rate (%)</label>
                    <input type="number" id="tax-rate" name="tax_rate" class="form-control"
                    placeholder="Enter Tax Rate (%)..." min="0" step="1"
                    value="<?php echo $general['tax_rate'] ?? ''; ?>">
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

            <form method="POST" action="settings.php">
                <input type="hidden" name="whatAction" value="updateAttendanceSettings">

                <div class="mb-3 m-3 col">
                    <label>Standard Shift Hours</label>
                    <input type="number" name="standard_shift_hours" class="form-control"
                    placeholder="Standard Shift Hours..." min="0" step="1"
                    value="<?php echo $attendance['standard_shift_hours'] ?? ''; ?>">
                </div>

                <div class="mb-3 m-3 col">
                    <label>Overtime Rate (x regular pay)</label>
                    <input type="number" name="overtime_rate" class="form-control"
                    placeholder="Enter Overtime Rate..." min="0" step="0.1"
                    value="<?php echo $attendance['overtime_rate'] ?? ''; ?>">
                </div>

                <div class="mb-3 m-3 col">
                    <label>Lateness Threshold (minutes)</label>
                    <input type="number" name="lateness_threshold" class="form-control"
                    placeholder="Enter Lateness Threshold..." min="0" step="1"
                    value="<?php echo $attendance['lateness_threshold'] ?? ''; ?>">
                </div>

                <div class="mb-3 m-3 col">
                    <label>Attendance Tracking Method</label>
                    <select name="attendance_method" class="form-select">
                    <option disabled <?php echo empty($attendance['attendance_method']) ? 'selected' : ''; ?>>Select Method</option>
                    <option value="Biometric" <?php echo ($attendance['attendance_method'] == 'Biometric') ? 'selected' : ''; ?>>Biometric</option>
                    <option value="RFID Card" <?php echo ($attendance['attendance_method'] == 'RFID Card') ? 'selected' : ''; ?>>RFID Card</option>
                    <option value="Manual Entry" <?php echo ($attendance['attendance_method'] == 'Manual Entry') ? 'selected' : ''; ?>>Manual Entry</option>
                    </select>
                </div>

                <div class="form-check form-switch mx-3">
                    <h5>Automatic Timesheet Generation</h5>
                    <input class="form-check-input" type="checkbox" name="auto_timesheet"
                    <?php echo ($attendance['auto_timesheet'] ?? 0) ? 'checked' : ''; ?>>
                    <label class="form-check-label">Generate timesheets based on attendance records</label>
                </div>

                <div class="form-check form-switch mx-3">
                    <h5>Enable Skill Tracking</h5>
                    <input class="form-check-input" type="checkbox" name="skill_tracking"
                    <?php echo ($attendance['skill_tracking'] ?? 0) ? 'checked' : ''; ?>>
                    <label class="form-check-label">Track worker skills and certifications</label>
                </div>

                <div class="form-check form-switch mx-3">
                    <h5>Safety Compliance Alerts</h5>
                    <input class="form-check-input" type="checkbox" name="safety_alerts"
                    <?php echo ($attendance['safety_alerts'] ?? 0) ? 'checked' : ''; ?>>
                    <label class="form-check-label">Send alerts for safety training expirations</label>
                </div>

                <div class="text-end position-relative">
                    <button type="submit" class="btn btn-info text-white">
                    <i class="bi bi-save"></i> Save Settings
                    </button>
                </div>
            </form>

        </div>

        <!-- Notifications Settings -->
        <div class="setting-tab-content" id="notifications">
            <h3>Notification Settings</h3>
            <p>Configure alerts and notifications for factory events</p>
            <h4>Email Notifications</h4>
            <div class="form-check form-switch mx-3">
                <input class="form-check-input" type="checkbox" id="quality-alerts">
                <label class="form-check-label" for="quality-alerts">Low Stock Alerts
                </label>
            </div>

            <div class="form-check form-switch mx-3">
                <input class="form-check-input" type="checkbox" id="downtime-tracking">
            </div>
            <div class="form-check form-switch mx-3">
                <input class="form-check-input" type="checkbox" id="bank-transfer">
                <label class="form-check-label" for="auto-reorder">Production Milestones</label>
            </div>

            <div class="form-check form-switch mx-3">
                <input class="form-check-input" type="checkbox" id="quality-alerts">
                <label class="form-check-label" for="quality-alerts">Equipment Maintenance
                </label>
            </div>

            <div class="form-check form-switch mx-3">
                <input class="form-check-input" type="checkbox" id="downtime-tracking">
                <label class="form-check-label" for="downtime-tracking">New Orders</label>
            </div>
            <div class="form-check form-switch mx-3">
                <input class="form-check-input" type="checkbox" id="bank-transfer">
                <label class="form-check-label" for="auto-reorder">Invoice Generation</label>
            </div>
            <h4>SMS Notifications</h4>
            <div class="form-check form-switch mx-3">
                <input class="form-check-input" type="checkbox" id="quality-alerts">
                <label class="form-check-label" for="quality-alerts">Emergency Alerts
                </label>
            </div>

            <div class="form-check form-switch mx-3">
                <input class="form-check-input" type="checkbox" id="downtime-tracking">
            </div>
            <div class="form-check form-switch mx-3">
                <input class="form-check-input" type="checkbox" id="bank-transfer">
                <label class="form-check-label" for="auto-reorder">Critical Production Issues</label>
            </div>

            <div class="form-check form-switch mx-3">
                <input class="form-check-input" type="checkbox" id="quality-alerts">
                <label class="form-check-label" for="quality-alerts">Urgent Maintenance
                </label>
            </div>
            <h4>System Notifications</h4>
            <div class="form-check form-switch mx-3">
                <input class="form-check-input" type="checkbox" id="downtime-tracking">
                <label class="form-check-label" for="downtime-tracking">Dashboard Alerts</label>
            </div>
            <div class="form-check form-switch mx-3">
                <input class="form-check-input" type="checkbox" id="bank-transfer">
                <label class="form-check-label" for="auto-reorder">Browser Notifications</label>
            </div>
            <div class="form-check form-switch mx-3">
                <input class="form-check-input" type="checkbox" id="quality-alerts">
                <label class="form-check-label" for="quality-alerts">Mobile App Notifications
                </label>
            </div>
            <div class="mb-3 m-3 col">
                <label for="factory-address">Email Recipients</label>
                <textarea class="form-control" id="factory-address"
                    placeholder="factory.manager@unnati.com, production.head@unnati.com, quality.control@unnati.com"
                    style="height: 100px"></textarea>
                <p>These emails will receive all notifications</p>
            </div>
            <div class="text-end position-relative">
                <button type="submit" class="btn btn-info text-white" onclick="showSavePopup(event)">
                    <i class="bi bi-save"></i> Save Settings
                </button>
                <div id="savePopup" class="position-absolute bg-success text-white px-3 py-2 rounded shadow"
                    style="top: 100%; right: 0; display: none; z-index: 1000;">
                    Your settings have been updated successfully.
                </div>
            </div>
        </div>

        <!-- Job Roles Settings -->
        <div class="setting-tab-content" id="job_roles">
            <h3>Job Roles Settings</h3>
            <p>Configure factory job roles and permissions</p>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Users</h5>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" checked></th>
                                    <th>User</th>
                                    <th>ID</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th>Actions</th>
                                    <th>Permission</th>
                                </tr>
                            </thead>
                            <tbody id="userTableBody">
                                <!-- User rows added dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Permission Modal -->
            <div class="modal fade" id="permissionModal" tabindex="-1" aria-labelledby="permissionModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="permissionModalLabel">Set Permissions</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="permissionForm"> <!-- Yeh pehle galti se < tha, use <form kiya gaya hai -->
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="maindashbord">
                                    <label class="form-check-label" for="maindashbord">Main Dashboard</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="billingdesk">
                                    <label class="form-check-label" for="billingdesk">Production</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="accounting">
                                    <label class="form-check-label" for="accounting">Billing System</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="investory">
                                    <label class="form-check-label" for="investory">Supply Management</label>
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
                                    <div class="text-end position-relative">
                                        <button type="submit" class="btn btn-info text-white"
                                            onclick="showSavePopup(event)">
                                            <i class="bi bi-save"></i> Save Settings
                                        </button>
                                        <div id="savePopup"
                                            class="position-absolute bg-success text-white px-3 py-2 rounded shadow"
                                            style="top: 100%; right: 0; display: none; z-index: 1000;">
                                            Your settings have been updated successfully.
                                        </div>
                                    </div>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="expenses">
                                    <label class="form-check-label" for="expenses">Raw Materials</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="factorystock">
                                    <label class="form-check-label" for="factorystock">Invetory</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="retailstore">
                                    <label class="form-check-label" for="retailstore">Workers</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="aftersellservice">
                                    <label class="form-check-label" for="aftersellservice">Maintenance</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="suppliers">
                                    <label class="form-check-label" for="suppliers">Expances</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="reports">
                                    <label class="form-check-label" for="reports">Reports</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="settings">
                                    <label class="form-check-label" for="settings">Settings</label>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="savePermissions()">Save</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>


        <script>
            function showsettingTab(tabId) {
                // Remove active class from all tabs
                document.querySelectorAll('.settingTab').forEach(tab => tab.classList.remove('active'));
                document.querySelectorAll('.setting-tab-content').forEach(content => content.classList.remove('active'));

                // Add active class to clicked tab
                document.querySelector(.settingTab[onclick = "showsettingTab('${tabId}')"]).classList.add('active');
                document.getElementById(tabId).classList.add('active');
            }


        </script>