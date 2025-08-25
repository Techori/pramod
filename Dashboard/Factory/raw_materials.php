<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../_conn.php';
$user_name = $_SESSION['user_name'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['whatAction'])) {

    // Clean input data function
    function clean($input)
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    if ($_POST['whatAction'] === 'addItem') {
        // Collect and sanitize input
        $material = clean($_POST['materialName']);
        $category = clean($_POST['category']);
        $quantity = clean($_POST['materialquantity']);
        $cost = clean($_POST['cost']);
        $amount = $cost * $quantity; // Dynamically calculate amount
        $number = clean($_POST['number']);
        $reorder_point = clean($_POST['materialReorder']);
        $unit = !empty($_POST['customUnit']) ? $conn->real_escape_string($_POST['customUnit']) : $conn->real_escape_string($_POST['unit']);
        $Status = clean($_POST['Status']);
        $primary_supplier = clean($_POST['materialprimarysupplier']);
        $description = clean($_POST['description']);
        $date = clean($_POST['date']);
        $Payment_Method = clean($_POST['method']);
        $status = clean($_POST['status']);
        $addedBy = !empty($_POST['customCreatedBy']) ? $conn->real_escape_string($_POST['customCreatedBy']) : $conn->real_escape_string($_POST['createdBy']);

        // Optional fields
        $bankName = isset($_POST['bankName']) ? clean($_POST['bankName']) : null;
        $accountNumber = isset($_POST['accountNumber']) ? clean($_POST['accountNumber']) : null;
        $senderName = isset($_POST['senderName']) ? clean($_POST['senderName']) : null;

        // Generate new material ID
        $result = $conn->query("SELECT id FROM factory_raw_material ORDER BY CAST(SUBSTRING(id, 5) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");
        if ($result && $row = $result->fetch_assoc()) {
            $lastId = $row['id'];
            $num = (int) substr($lastId, 4);
            $newNum = $num + 1;
        } else {
            $newNum = 1;
        }
        $newMaterialId = 'RM-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);

        // Insert into factory_raw_material
        $stmt = $conn->prepare("INSERT INTO factory_raw_material 
    (id, material, category, quantity, cost, amount, number, reorder_point, unit, Status, primary_supplier, created_for) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "sssiddssssss",
            $newMaterialId,
            $material,
            $category,
            $quantity,
            $cost,
            $amount,
            $number,
            $reorder_point,
            $unit,
            $Status,
            $primary_supplier,
            $user_name
        );


        $stmt->execute();
        $stmt->close();

        // Generate new expense ID
        $result = $conn->query("SELECT id FROM factory_expenses ORDER BY CAST(SUBSTRING(id, 5) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");
        if ($result && $row = $result->fetch_assoc()) {
            $lastId = $row['id'];
            $num = (int) substr($lastId, 4);
            $newNum = $num + 1;
        } else {
            $newNum = 1;
        }
        $newExpenseId = 'EXP-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);

        // Insert into factory_expenses including optional fields
        $stmt = $conn->prepare("INSERT INTO factory_expenses 
            (id, description, category, addedBy, amount, date, Payment_Method, Status, created_for, bankName, accountNumber, senderName) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("ssssdsssssss", $newExpenseId, $description, $category, $addedBy, $amount, $date, $Payment_Method, $status, $user_name, $bankName, $accountNumber, $senderName);
        $stmt->execute();
        $stmt->close();

        $conn->commit();

        @header("Location: factory_dashboard.php?page=raw_materials");
        exit;
    }
}
?>


<style>
    .cards {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
        height: 100%;
    }

    .cards:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.1);
    }

    .card-border {
        border-radius: 0.5rem;
        border-top: none;
        border-right: none;
        border-bottom: none;
    }

    .chart-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
    }

    .chart-box {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        width: 100%;
        max-width: 600px;
        flex: 1 1 300px;
    }

    h3 {
        margin-bottom: 15px;
    }

    canvas {
        width: 100% !important;
        height: auto !important;
    }

    .progress {
        height: 10px;
    }

    .alert-card {
        border-radius: 10px;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
    }

    .stock-label {
        font-weight: 500;
    }

    .stock-count {
        font-size: 0.9rem;
        font-weight: 600;
    }

    .tabs {
        display: flex;
        border-bottom: 2px solid #ddd;
    }

    .retailStoreTab {
        padding: 10px 20px;
        cursor: pointer;
        border: none;
        background: none;
        font-size: 16px;
    }

    .retailStoreTab.active {
        border-bottom: 3px solid #007bff;
        font-weight: bold;
        color: #007bff;
    }

    .retailStore-tab-content {
        display: none;
        padding: 20px 0;
    }

    .retailStore-tab-content.active {
        display: block;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th,
    td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
    }
</style>
<h1>Raw Materials Management
</h1>
<p>Monitor and manage factory raw materials inventory</p>


<div class="row mb-2">

    <!-- Search and Filters -->
    <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center mb-4">
        <div class="flex-grow-1">
            <input type="hidden" name="page" value="billing">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0" id="searchInput"><i
                        class="fas fa-search"></i></span>
                <input type="text" class="form-control border-start-0 table-search" data-table="rawmaterialsTable"
                    placeholder="Search..." />
            </div>
        </div>
        <div class="d-flex gap-2">
            <div>
                <button class="btn btn-outline-primary gst-filter me-2" data-type="In Stock"
                    data-table="rawmaterialsTable">In Stock</button>
            </div>
            <div>
                <button class="btn btn-outline-primary gst-filter me-2" data-type="Low Stock"
                    data-table="rawmaterialsTable">Low Stock</button>
            </div>
            <div>
                <button class="btn btn-outline-primary gst-filter me-2" data-type="Out Of Stock"
                    data-table="rawmaterialsTable">Out Of Stock</button>
            </div>
            <div>
                <button class="btn btn-outline-danger reset-filters me-2" data-table="rawmaterialsTable">Remove
                    Filters</button>
            </div>
        </div>
    </div>
    <script>


        document.addEventListener("DOMContentLoaded", () => {

            // 🔍 Live Search Function
            document.querySelectorAll(".table-search").forEach(input => {
                input.addEventListener("input", () => {
                    const tableId = input.dataset.table;
                    const value = input.value.toLowerCase();
                    const rows = document.querySelectorAll(`#${tableId} tbody tr`);
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(value) ? "" : "none";
                    });
                });
            });

            // 🧾 GST Filter Buttons
            document.querySelectorAll(".gst-filter").forEach(button => {
                button.addEventListener("click", () => {
                    const type = button.dataset.type.toLowerCase();
                    const tableId = button.dataset.table;
                    const rows = document.querySelectorAll(`#${tableId} tbody tr`);
                    rows.forEach(row => {
                        const docType = row.children[5]?.innerText.trim().toLowerCase();
                        row.style.display = docType === type ? "" : "none";
                    });
                });
            });

            // ❌ Remove Filters Button
            document.querySelectorAll(".reset-filters").forEach(button => {
                button.addEventListener("click", () => {
                    const tableId = button.dataset.table;
                    const rows = document.querySelectorAll(`#${tableId} tbody tr`);
                    rows.forEach(row => {
                        row.style.display = "";
                    });

                    // Also clear search inputs for that table
                    document.querySelectorAll(`.table-search[data-table='${tableId}']`).forEach(input => {
                        input.value = "";
                    });
                });
            });

            // ✅ Filter Helper Function
            function filterTable(tableId, conditionFn) {
                const rows = document.querySelectorAll(`#${tableId} tbody tr`);
                rows.forEach(row => {
                    row.style.display = conditionFn(row) ? "" : "none";
                });
            }
        });
    </script>
</div>

<div class="mb-3">

    <button class="btn btn-outline-primary" onclick="exportTableToCSV()"></i> Reports</button>
    <!-- Add Material Button -->
    <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
        Add Materials
    </button>

    <?php
    // Get names for Add expense form dropdown
    $itemSql = "SELECT DISTINCT addedBy FROM factory_expenses ORDER BY addedBy";
    $itemResult = $conn->query($itemSql);
    $items = [];
    if ($itemResult->num_rows > 0) {
        while ($row = $itemResult->fetch_assoc()) {
            $items[] = $row['addedBy'];
        }
    }
    ?>
    <?php
    // Get names for Add expense form dropdown
    $unitSql = "SELECT DISTINCT unit FROM factory_raw_material ORDER BY unit";
    $unitResult = $conn->query($unitSql);
    $units = [];
    if ($unitResult->num_rows > 0) {
        while ($row = $unitResult->fetch_assoc()) {
            $units[] = $row['unit'];
        }
    }
    ?>

    <!-- Modal Structure -->
    <div class="modal fade" id="addMaterialModal" tabindex="-1" aria-labelledby="addMaterialModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="addMaterialModalLabel">Add Materials</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Modal Body -->
                <div class="modal-body">
                    <!-- Form to add materials -->
                    <form action="raw_materials.php" method="POST">
                        <div class="mb-3">
                            <label for="materialName" class="form-label">Material Name</label>
                            <input type="text" class="form-control" id="materialName" name="materialName" required>
                        </div>
                        <div class="mb-3">
                            <label for="materialName" class="form-label">Category</label>
                            <input type="text" class="form-control" id="category" name="category" required>
                        </div>
                        <div class="mb-3">
                            <label for="createdBy" class="form-label">Created by</label>
                            <select class="form-control" id="createdBy" name="createdBy" onchange="toggleItemInput()">
                                <option value="">Select Name</option>
                                <?php foreach ($items as $item): ?>
                                    <option value="<?php echo htmlspecialchars($item); ?>">
                                        <?php echo htmlspecialchars($item); ?>
                                    </option>
                                <?php endforeach; ?>
                                <option value="Other">Other</option>
                            </select>
                            <input type="text" class="form-control mt-2" id="customCreatedBy" name="customCreatedBy"
                                style="display:none;" placeholder="Enter new name">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="description" name="description" required>
                        </div>
                        <!-- <div class="mb-3">
                            <label for="unit" class="form-label">Per Unit</label>
                            <input type="text" class="form-control" id="unit" name="unit" required>
                        </div>  -->
                        <div class="mb-3">
                            <label for="unit" class="form-label">Per Unit</label>
                            <select class="form-control" id="unit" name="unit" onchange="toggleUnitInput()">
                                <option value="">Select Unit</option>
                                <?php foreach ($units as $unit): ?>
                                    <option value="<?php echo htmlspecialchars($unit); ?>">
                                        <?php echo htmlspecialchars($unit); ?>
                                    </option>
                                <?php endforeach; ?>
                                <option value="Other">Other</option>
                            </select>
                            <input type="text" class="form-control mt-2" id="customUnit" name="customUnit"
                                style="display:none;" placeholder="Enter new unit">
                        </div>
                        <div class="mb-3">
                            <label for="materialquantity" class="form-label">Quantity</label>
                            <input type="number" step="0.01" class="form-control" id="materialquantity"
                                name="materialquantity">
                        </div>

                        <div class="mb-3">
                            <label for="cost" class="form-label">Cost per Unit</label>
                            <input type="number" step="0.01" class="form-control" id="cost" name="cost">
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" readonly>
                        </div>
                        <script>
                            const quantityInput = document.getElementById('materialquantity');
                            const costInput = document.getElementById('cost');
                            const amountInput = document.getElementById('amount');

                            function updateAmount() {
                                const quantity = parseFloat(quantityInput.value) || 0;
                                const cost = parseFloat(costInput.value) || 0;
                                const amount = quantity * cost;
                                amountInput.value = amount.toFixed(2);
                            }

                            quantityInput.addEventListener('input', updateAmount);
                            costInput.addEventListener('input', updateAmount);
                        </script>
                        <div class="mb-3">
                            <label for="number" class="form-label">Mobile Number</label>
                            <input type="number" class="form-control" id="number" name="number">
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>

                        <div class="container mt-5">
                            <!-- <form method="POST" action="your_php_file.php"> replace with actual PHP handler -->
                            <!-- Payment Method Dropdown -->
                            <div class="mb-3">
                                <label for="method" class="form-label">Method</label>
                                <select class="form-select" id="method" name="method" required
                                    onchange="togglePaymentFields()">
                                    <option value="" disabled selected>Select Payment Method</option>
                                    <option value="Digital payment">Digital payment</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Payment gateway">Payment gateway</option>
                                </select>
                            </div>

                            <!-- Fields for Digital Payment -->
                            <div id="digitalFields" style="display: none;">
                                <div class="mb-3">
                                    <label for="bankName" class="form-label">Bank Account Name</label>
                                    <input type="text" class="form-control" id="bankName" name="bankName">
                                </div>
                                <div class="mb-3">
                                    <label for="accountNumber" class="form-label">Account Number</label>
                                    <input type="text" class="form-control" id="accountNumber" name="accountNumber">
                                </div>
                            </div>

                            <!-- Field for Cash -->
                            <div id="cashFields" style="display: none;">
                                <div class="mb-3">
                                    <label for="senderName" class="form-label">Sender Name</label>
                                    <input type="text" class="form-control" id="senderName" name="senderName">
                                </div>
                            </div>
                            <!-- ✅ JavaScript for conditional fields -->
                            <script>
                                function togglePaymentFields() {
                                    const method = document.getElementById("method").value;
                                    const digitalFields = document.getElementById("digitalFields");
                                    const cashFields = document.getElementById("cashFields");

                                    digitalFields.style.display = (method === "Digital payment") ? "block" : "none";
                                    cashFields.style.display = (method === "Cash") ? "block" : "none";
                                }
                            </script>
                            <!-- Material Fields -->
                            <div class="mb-3">
                                <label for="Status" class="form-label">Stock Status</label>
                                <select class="form-select" id="Status" name="Status" required>
                                    <option value="In stock">In stock</option>
                                    <option value="Low stock">Low stock</option>
                                    <option value="Out Of stock">Out Of stock</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Expense Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="" disabled selected>Select Status</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Rejected">Rejected</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="materialprimarysupplier" class="form-label">Primary Supplier</label>
                                <input type="text" class="form-control" id="materialprimarysupplier"
                                    name="materialprimarysupplier" required>
                            </div>

                            <div class="mb-3">
                                <label for="materialReorder" class="form-label">Reorder Point</label>
                                <input type="text" class="form-control" id="materialReorder" name="materialReorder"
                                    required>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary" name="whatAction" value="addItem">Add
                                Material</button>



                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <script>
        function toggleItemInput() {
            const select = document.getElementById('createdBy');
            const customInput = document.getElementById('customCreatedBy');

            if (select && customInput) {
                if (select.value === 'Other') {
                    customInput.style.display = 'block';
                    customInput.required = true;
                } else {
                    customInput.style.display = 'none';
                    customInput.required = false;
                }
            }
        }

        // Run this once on page load in case "Other" is already selected
        window.addEventListener('DOMContentLoaded', toggleItemInput);

        function toggleUnitInput() {
            const unit = document.getElementById('unit');
            const customUnit = document.getElementById('customUnit');

            if (unit && customUnit) {
                if (unit.value === 'Other') {
                    customUnit.style.display = 'block';
                    customUnit.required = true;
                } else {
                    customUnit.style.display = 'none';
                    customUnit.required = false;
                }
            }
        }

        // Run this once on page load in case "Other" is already selected
        window.addEventListener('DOMContentLoaded', toggleUnitInput);
    </script>

    <!-- ✅ JavaScript to Export Table -->
    <script>
        function exportTableToCSV() {
            const table = document.getElementById("rawmaterialsTable");
            let csv = [];
            for (let row of table.rows) {
                let cols = Array.from(row.cells)
                    .map(cell => `"${cell.innerText.replace(/"/g, '""')}"`);
                csv.push(cols.join(","));
            }
            let csvContent = csv.join("\n");
            let blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });

            // Download link
            let link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = "Raw_Material_detail.csv";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>

    <?php
// Check if user has Delete permission
$hasDeletePermission = false;
$permissionSql = "SELECT Permission FROM user_management WHERE User_Name = '$user_name'";
$permissionResult = $conn->query($permissionSql);
if ($permissionResult->num_rows > 0) {
    $permissionRow = $permissionResult->fetch_assoc();
    $permissions = json_decode($permissionRow['Permission'], true);
    $hasDeletePermission = in_array('Delete', $permissions);
}
?>

    <div class="card p-3 my-4 shadow-sm">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="fw-bold">Raw Material Stock</h2>
                <p class="text-muted">Current raw materials inventory status</p>
            </div>
            <div>
                <button id="refreshBtn" class="btn btn-outline-secondary me-2">Refresh</button>
                <script>
                    // Refresh Button (Reload page)
                    document.getElementById('refreshBtn').addEventListener('click', function () {
                        location.reload();
                    });
                </script>

            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle" id="rawmaterialsTable">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Material</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Cost</th>
                        <th>Amount</th>
                        <th>Number</th>
                        <th>Reorder Point</th>
                        <th>Primary Supplier</th>
                        <th>Status</th>
                        <?php if ($hasDeletePermission): ?>
                            <th>Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    // Fetch transactions from the database
                    $result = $conn->query("SELECT * FROM factory_raw_material WHERE created_for = '$user_name' ORDER BY id DESC");

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['material']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['quantity']) . " " . htmlspecialchars($row['unit']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['cost']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['amount']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['number']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['reorder_point']) . " " . htmlspecialchars($row['unit']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['primary_supplier']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Status']) . "</td>";
                            if ($hasDeletePermission) {
                                echo "<td>
                                    <form method='post' action='' onsubmit='return confirm(\"Are you sure you want to delete this raw material item?\");'>
                                        <input type='hidden' name='raw_id' value='" . htmlspecialchars($row['id']) . "'>
                                        <button type='submit' name='deleteRawMaterial' class='btn btn-danger btn-sm'>
                                            <i class='fa-solid fa-trash'></i> Delete
                                        </button>
                                    </form>
                                  </td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='" . ($hasDeletePermission ? 11 : 10) . "' class='text-center'>No material found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteRawMaterial']) && $hasDeletePermission) {
        $raw_id = $conn->real_escape_string($_POST['raw_id']);

        // Prepare and execute delete query
        $deleteSql = "DELETE FROM factory_raw_material WHERE id = ? AND created_for = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("ss", $raw_id, $user_name);

        if ($stmt->execute()) {
            echo "<script>alert('Raw material item deleted successfully!'); window.location.href=window.location.href;</script>";
        } else {
            echo "<script>alert('Error deleting raw material: " . $conn->error . "');</script>";
        }

        $stmt->close();
    }
    ?>