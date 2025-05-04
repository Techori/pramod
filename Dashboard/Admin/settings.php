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
            <button class="settingTab" onclick="showsettingTab('notifications')">Notifications</button>
            <button class="settingTab" onclick="showsettingTab('job_roles')">Job Roles</button>
        </div>

        <!-- General Settings -->
        <div class="setting-tab-content active" id="general">
            <h3>Company Profile</h3>
            <p>Update your company information and branding</p>
            <div>
                <h5>Business Details</h5>

                <div class="mb-3 m-3 col">
                    <label for="factory-name">Factory Name</label>
                    <input type="text" class="form-control" id="factory-name" placeholder="Unnati Electrical Factory">
                </div>

                <div class="mb-3 m-3 col">
                    <label for="factory-address">Full Address</label>
                    <textarea class="form-control" id="factory-address"
                        placeholder="123 Main Street, Industrial Area, Mumbai" style="height: 100px"></textarea>
                </div>

                <div class="mb-3 m-3 col">
                    <label for="factory-location">Location</label>
                    <input type="text" class="form-control" id="factory-location"
                        placeholder="Industrial Area Phase II, Mumbai">
                </div>

                <div class="mb-3 m-3 col">
                    <label for="phone-number">Phone Number</label>
                    <input type="number" class="form-control" id="phone-number" placeholder="9876543210">
                </div>

                <div class="mb-3 m-3 col">
                    <label for="factory-manager">Factory Manager</label>
                    <input type="text" class="form-control" id="factory-manager" placeholder="Rajesh Kumar">
                </div>
            </div>

            <div class="form-check form-switch mx-3">
                <h5>24/7 Operation</h5>
                <input class="form-check-input" type="checkbox" id="operation-24x7">
                <label class="form-check-label" for="operation-24x7">Keep factory operational 24 hours per day</label>
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

        <!-- Production Settings -->
        <div class="setting-tab-content" id="production">
            <h3>Production Settings</h3>
            <p>Manage production schedules, shift timings, and machinery settings.</p>

            <div class="mb-3 m-3 col">
                <label for="daily-capacity">Daily Production Capacity (units)</label>
                <input type="number" id="daily-capacity" class="form-control" placeholder="Enter Capacity (units)..."
                    min="0" step="1">
            </div>

            <div class="mb-3 m-3 col">
                <label for="target-efficiency">Target Efficiency (%)</label>
                <input type="number" id="target-efficiency" class="form-control" placeholder="Enter Efficiency (%)..."
                    min="0" step="1">
            </div>

            <div class="mb-3 m-3 col">
                <label for="shift-duration">Standard Shift Duration (hours)</label>
                <input type="number" id="shift-duration" class="form-control" placeholder="Enter Duration (hours)..."
                    min="0" step="1">
            </div>

            <div class="form-check form-switch mx-3">
                <h5>Automatic Production Scheduling</h5>
                <input class="form-check-input" type="checkbox" id="auto-scheduling">
                <label class="form-check-label" for="auto-scheduling">Automatically schedule production based on
                    orders</label>
            </div>

            <div class="form-check form-switch mx-3">
                <h5>Quality Control Alerts</h5>
                <input class="form-check-input" type="checkbox" id="quality-alerts">
                <label class="form-check-label" for="quality-alerts">Send alerts when quality metrics fall below
                    threshold</label>
            </div>

            <div class="form-check form-switch mx-3">
                <h5>Downtime Tracking</h5>
                <input class="form-check-input" type="checkbox" id="downtime-tracking">
                <label class="form-check-label" for="downtime-tracking">Track reasons and duration of production line
                    downtime</label>
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

        <!-- Inventory Settings -->
        <div class="setting-tab-content" id="inventory">
            <h3>Inventory Settings</h3>
            <p>Configure raw materials and finished goods inventory parameters</p>

            <div class="mb-3 m-3 col">
                <label for="stock-buffer">Stock Buffer (% above minimum)</label>
                <input type="number" id="stock-buffer" class="form-control" placeholder="Enter Stock Buffer (%)..."
                    min="0" step="1">
            </div>

            <div class="mb-3 m-3 col">
                <label for="lead-time">Default Reorder Lead Time (days)</label>
                <input type="number" id="lead-time" class="form-control" placeholder="Enter Lead Time (days)..." min="0"
                    step="1">
            </div>

            <div class="form-check form-switch mx-3">
                <h5>Automatic Reordering</h5>
                <input class="form-check-input" type="checkbox" id="auto-reorder">
                <label class="form-check-label" for="auto-reorder">Automatically generate purchase orders for low stock
                    items</label>
            </div>

            <div class="form-check form-switch mx-3">
                <h5>Use FIFO Method</h5>
                <input class="form-check-input" type="checkbox" id="fifo-method">
                <label class="form-check-label" for="fifo-method">First In, First Out inventory management</label>
            </div>

            <div class="form-check form-switch mx-3">
                <h5>Batch Tracking</h5>
                <input class="form-check-input" type="checkbox" id="batch-tracking">
                <label class="form-check-label" for="batch-tracking">Track inventory by production batch</label>
            </div>

            <div class="form-check form-switch mx-3">
                <h5>Track Material Expiry</h5>
                <input class="form-check-input" type="checkbox" id="material-expiry">
                <label class="form-check-label" for="material-expiry">Monitor expiration dates of raw materials</label>
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

        <!-- Billing Settings -->
        <div class="setting-tab-content" id="billing">
            <h3>Billing Settings</h3>
            <p>Configure invoicing and payment options for factory operations</p>
            <div class="mb-3 m-3 col">
                <label for="-number-general"> Standard Shift Hours</label>
                <input type="text" class="form-control" id="Standard-billing" placeholder="UNT-FAC-INV">
            </div>
            <div class="mb-3 m-3 col">

                <label for="payment-terms">Default Payment Terms (days)</label>
                <input type="number" id="payment-terms" class="form-control" placeholder="Enter Payment Terms..."
                    min="0" step="1">
            </div>
            <div class="mb-3 m-3 col">
                <label for="-number-general">Currency</label>
                <input type="number" class="form-control" id="phone-number-general" placeholder="INR (₹)">
            </div>
            <div class="mb-3 m-3 col">
                <label for="tax-rate">Default Tax Rate (%)</label>
                <input type="number" id="tax-rate" class="form-control" placeholder="Enter Tax Rate (%)..." min="0"
                    step="1">
            </div>

            <div class="form-check form-switch mx-3">
                <h5>Automatic Production Scheduling</h5>
                <input class="form-check-input" type="checkbox" id="auto-scheduling">
                <label class="form-check-label" for="auto-scheduling">Automatically schedule production based on
                    orders</label>
            </div>

            <div class="form-check form-switch mx-3">
                <h5>Quality Control Alerts</h5>
                <input class="form-check-input" type="checkbox" id="quality-alerts">
                <label class="form-check-label" for="quality-alerts">Send alerts when quality metrics fall below
                    threshold</label>
            </div>

            <div class="form-check form-switch mx-3">
                <h5>Downtime Tracking</h5>
                <input class="form-check-input" type="checkbox" id="downtime-tracking">
                <label class="form-check-label" for="downtime-tracking">Track reasons and duration of production line
                    downtime</label>
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

        <!-- Inventory Settings -->
        <div class="setting-tab-content" id="inventory">
            <h3>Inventory Settings</h3>
            <p>Configure raw materials and finished goods inventory parameters</p>

            <div class="mb-3 m-3 col">
                <label for="stock-buffer">Stock Buffer (% above minimum)</label>
                <input type="number" id="stock-buffer" class="form-control" placeholder="Enter Stock Buffer (%)..."
                    min="0" step="1">
            </div>

            <div class="mb-3 m-3 col">
                <label for="lead-time">Default Reorder Lead Time (days)</label>
                <input type="number" id="lead-time" class="form-control" placeholder="Enter Lead Time (days)..." min="0"
                    step="1">
            </div>

            <div class="form-check form-switch mx-3">
                <h5>Accepted Payment Methods</h5>
                <input class="form-check-input" type="checkbox" id="bank-transfer">
                <label class="form-check-label" for="auto-reorder">Bank Transfer</label>
            </div>
            <div class="modal-body">
                <form id="permissionForm">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="maindashbord">
                        <label class="form-check-label" for="maindashbord">Cheque</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="maindashbord">
                        <label class="form-check-label" for="maindashbord">UPI</label>
                    </div>

                    < <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="maindashbord">
                        <label class="form-check-label" for="maindashbord">Credit Card</label>
            </div>
            </form>
        </div>
        <div class="form-check form-switch mx-3">
            <h5>Automatic Invoice Generation</h5>
            <input class="form-check-input" type="checkbox" id="quality-alerts">
            <label class="form-check-label" for="quality-alerts">Automatically generate invoices for completed
                orders</label>
        </div>

        <div class="form-check form-switch mx-3">
            <h5>Enable Digital Signature
            </h5>
            <input class="form-check-input" type="checkbox" id="downtime-tracking">
            <label class="form-check-label" for="downtime-tracking">Include digital signature on invoices</label>
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

    <!-- workers Settings -->
    <div class="setting-tab-content" id="workers">
        <h3>Workers Settings</h3>
        <p>Configure API access, automation, and custom factory integrations.</p>

        <div class="mb-3 m-3 col">
            <label for="daily-capacity">Standard Shift Hours</label>
            <input type="number" id="daily-capacity" class="form-control" placeholder="Standard Shift Hours..." min="0"
                step="1">
        </div>

        <div class="mb-3 m-3 col">
            <label for="target-efficiency"> Overtime Rate (x regular pay)</label>
            <input type="number" id="target-efficiency" class="form-control" placeholder="Enter Overtime Rate..."
                min="0" step="1">
        </div>

        <div class="mb-3 m-3 col">
            <label for="shift-duration">Lateness Threshold (minutes)</label>
            <input type="number" id="shift-duration" class="form-control" placeholder="Enter Lateness Threshold..."
                min="0" step="1">
        </div>

        <div class="mb-3 m-3 col">
            <label for="overtime-rate">Attendance Tracking Method</label>
            <select id="overtime-rate" class="form-select">
                <option value="" selected disabled>Biometric</option>
                <option value="1.5">RFID Card</option>
                <option value="2">Manual Entry</option>
                <option value="2.5">Mobile App</option>
            </select>
        </div>
        <div class="form-check form-switch mx-3">
            <h5>Automatic Timesheet Generation </h5>
            <input class="form-check-input" type="checkbox" id="quality-alerts">
            <label class="form-check-label" for="quality-alerts">Generate timesheets based on attendance records
            </label>
        </div>

        <div class="form-check form-switch mx-3">
            <h5>Enable Skill Tracking
            </h5>
            <input class="form-check-input" type="checkbox" id="downtime-tracking">
            <label class="form-check-label" for="downtime-tracking">Track worker skills and certifications</label>
        </div>
        <div class="form-check form-switch mx-3">
            <h5>Safety Compliance Alerts</h5>
            <input class="form-check-input" type="checkbox" id="bank-transfer">
            <label class="form-check-label" for="auto-reorder">Send alerts for safety training expirations</label>
        </div>
        <div class="text-end position-relative">
            <button type="submit" class="btn btn-info text-white" onclick="showSavePopup(event)">
                <i class="bi bi-save"></i> Save Settings
            </button>
        </div>
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
                            </script>                            <div class="text-end position-relative">
                                <button type="submit" class="btn btn-info text-white" onclick="showSavePopup(event)">
                                    <i class="bi bi-save"></i> Save Settings
                                </button>
                                <div id="savePopup" class="position-absolute bg-success text-white px-3 py-2 rounded shadow"
                                    style="top: 100%; right: 0; display: none; z-index: 1000;">
                                    Your settings have been updated successfully.
                                </div>
                            </div>                            </div>
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


        <script>
            function showsettingTab(tabId) {
                // Remove active class from all tabs
                document.querySelectorAll('.settingTab').forEach(tab => tab.classList.remove('active'));
                document.querySelectorAll('.setting-tab-content').forEach(content => content.classList.remove('active'));

                // Add active class to clicked tab
                document.querySelector(.settingTab[onclick="showsettingTab('${tabId}')"]).classList.add('active');
                document.getElementById(tabId).classList.add('active');
            }


            const users = [
                { initials: "RK", name: "Rajesh Kumar", email: "rajesh@unnatitraders.com", id: "USR-001", role: "Admin", roleClass: "bg-light text-purple", status: "Active", statusClass: "bg-success text-white", login: "2023-04-10 09:45 AM" },
                { initials: "PS", name: "Priya Sharma", email: "priya@unnatitraders.com", id: "USR-002", role: "Manager", roleClass: "bg-primary text-white", status: "Active", statusClass: "bg-success text-white", login: "2023-04-10 11:30 AM" },
                { initials: "AP", name: "Amit Patel", email: "amit@unnatitraders.com", id: "USR-003", role: "Accountant", roleClass: "bg-warning text-dark", status: "Active", statusClass: "bg-success text-white", login: "2023-04-09 04:15 PM" },
                { initials: "NS", name: "Neha Singh", email: "neha@unnatitraders.com", id: "USR-004", role: "Store", roleClass: "bg-success text-white", status: "Inactive", statusClass: "bg-danger text-white", login: "2023-04-01 10:22 AM" },
                { initials: "RV", name: "Rahul Verma", email: "rahul@unnatitraders.com", id: "USR-005", role: "User", roleClass: "bg-secondary text-white", status: "Pending", statusClass: "bg-warning text-dark", login: "Never logged in" },
                { initials: "SJ", name: "Sunita Joshi", email: "sunita@unnatitraders.com", id: "USR-006", role: "Manager", roleClass: "bg-primary text-white", status: "Active", statusClass: "bg-success text-white", login: "2023-04-10 02:15 PM" },
            ];

            const tbody = document.getElementById('userTableBody');
            users.forEach((user, index) => {
                tbody.innerHTML += `
    <tr>
      <td><input type="checkbox" checked></td>
      <td>
        <div class="d-flex align-items-center">
          <div class="rounded-circle bg-secondary text-white text-center me-2" style="width:32px;height:32px;line-height:32px;">${user.initials}</div>
          <div>
            <div>${user.name}</div>
            <small class="text-muted">${user.email}</small>
          </div>
        </div>
      </td>
      <td>${user.id}</td>
      <td><span class="badge ${user.roleClass}">${user.role}</span></td>
      <td><span class="badge ${user.statusClass}">${user.status}</span></td>
      <td>${user.login}</td>
      <td><button class="btn btn-sm btn-light">⋮</button></td>
      <td>
        <button class="btn btn-sm btn-outline-primary open-permission-btn" data-user-index="${index}" data-bs-toggle="modal" data-bs-target="#permissionModal">
          Allow
        </button>
      </td>
    </tr>
  `;
            });

            // save btn kke liye
            function showSavePopup(event) {
                event.preventDefault(); // Prevent form submission if needed
                const popup = document.getElementById('savePopup');

                popup.style.display = 'block';

                // Hide after 3 seconds
                setTimeout(() => {
                    popup.style.display = 'none';
                }, 3000);
            }


        </script>