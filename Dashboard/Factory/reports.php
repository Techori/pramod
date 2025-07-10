<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../_conn.php';
$user_name = $_SESSION['user_name'];

?>

<style>
    .tabs {
        display: flex;
        border-bottom: 2px solid #ddd;
    }

    .reportTab {
        padding: 10px 20px;
        cursor: pointer;
        border: none;
        background: none;
        font-size: 16px;
    }

    .reportTab.active {
        border-bottom: 3px solid #007bff;
        font-weight: bold;
        color: #007bff;
    }

    .report-tab-content {
        display: none;
        padding: 20px 0;
    }

    .report-tab-content.active {
        display: block;
    }
</style>

<h2>Factory Reports</h2>
<p>Analyze production performance and factory operations</p>

<script>
    function exportProductionTable() {
        const table = document.getElementById("productionTable");
        let csv = [];
        for (let row of table.rows) {
            let cols = Array.from(row.cells)
                .slice(0, -1) // exclude last 'Actions' column
                .map(cell => `"${cell.innerText.replace(/"/g, '""')}"`);
            csv.push(cols.join(","));
        }
        let csvContent = csv.join("\n");
        let blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });

        // Download link
        let link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = "Production_detail.csv";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>

<!-- Report table -->
<div class="col-md-12  card p-3 shadow-sm my-4 table-responsive">

    <div class="tabs">
        <button class="reportTab active" onclick="showReportTab('production')">Production</button>
        <button class="reportTab" onclick="showReportTab('raw_materials')">Raw Materials</button>
        <button class="reportTab" onclick="showReportTab('workers')">Workers</button>
    </div>

    <!-- Production table -->
    <div id="production" class="report-tab-content active">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="fw-bold">Production Schedule</h2>
                <p class="text-muted">Upcoming and in-progress production runs</p>
            </div>
            <div>
                <!-- Schedule Button -->
                <button class="btn btn-outline-secondary me-2" onclick="exportProductionTable()">
                    Export
                </button>

            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle" id="productionTable">
                <thead>
                    <tr class="text-muted">
                        <th>ID</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    // Fetch transactions from the database
                    $result = $conn->query("SELECT * FROM factory_production WHERE created_for = '$user_name' ORDER BY id DESC");

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $status = htmlspecialchars($row['status']);
                            $id = htmlspecialchars($row['id']);

                            echo "<tr>";
                            echo "<td>" . $id . "</td>";
                            echo "<td>" . htmlspecialchars($row['product']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['quantity']) . " " . htmlspecialchars($row['unit']) . "</td>";
                            echo "<td>" . date('d-M-Y', strtotime($row['start_date'])) . "</td>";
                            echo "<td>" . date('d-M-Y', strtotime($row['end_date'])) . "</td>";
                            echo "<td>" . $status . "</td>";
                            echo "<td>";
                            if ($status !== 'Completed') {
                                echo '<button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#statusModal' . $id . '">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </button>';
                            } else {
                                echo '<button class="btn btn-outline-secondary btn-sm" disabled>
                                <i class="fa-regular fa-pen-to-square"></i>
                            </button>';
                            }
                            echo "</td>";

                            // Modal for updating status
                            if ($status !== 'Completed') {
                                ?>
                    <div class="modal fade" id="statusModal<?= $id ?>" tabindex="-1"
                        aria-labelledby="statusModalLabel<?= $id ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="POST" action="production.php">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="statusModalLabel<?= $id ?>">Update Status</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="tracking_id" value="<?= $id ?>">
                                        <label class="form-label">Status</label>
                                        <!-- <input type="date" name="delivery_date" class="form-control"
                                                    placeholder="Delivery Date" required> -->
                                        <select class="form-select" name="status" required>
                                            <option value="">Select Status</option>
                                            <?php if ($status === 'Scheduled') {
                                                ?>
                                            <option value="Pending">Pending</option>
                                            <?php } else if ($status === 'Pending') { ?>
                                            <option value="Scheduled">Scheduled</option>
                                            <?php } ?>
                                            <option value="Completed">Completed</option>
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary" name="whatAction"
                                            value="updateProduct">Update</button>
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php
                            }
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center'>No production found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Raw Materials table -->
    <div id="raw_materials" class="report-tab-content">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="fw-bold">Raw Material Stock</h2>
                <p class="text-muted">Current raw materials inventory status</p>
            </div>
            <div>
                <button class="btn btn-outline-secondary me-2" onclick="exportTableToCSV('rawmaterialsTable')">
                    Export
                </button>

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
                        <th>Reorder Point</th>
                        <th>Status</th>
                        <th>Primary Supplier</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    // Fetch transactions from the database
                    $result = $conn->query("SELECT * FROM factory_raw_material WHERE created_for  = '$user_name' ORDER BY id DESC");

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['material']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['quantity']) . " " . htmlspecialchars($row['unit']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['reorder_point']) . " " . htmlspecialchars($row['unit']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Status']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['primary_supplier']) . "</td>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center'>No material found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Workers table -->
    <div id="workers" class="report-tab-content">
        <div id="workers">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="fw-bold">Workers Directory</h2>
                    <p class="text-muted">Complete list of factory workers with status and details</p>
                </div>
                <div>
                    <button class="btn btn-outline-secondary me-2" onclick="exportTableToCSV('supplyTable')">
                        Export
                    </button>

                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="supplyTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Worker Name</th>
                            <th>Department</th>
                            <th>Role</th>
                            <th>Shift</th>
                            <!-- <th>Status</th>
                            <th>Attendance</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        // Fetch transactions from the database
                        $result = $conn->query("SELECT * FROM factory_workers WHERE created_for  = '$user_name' ORDER BY id DESC");

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['department']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['shift']) . "</td>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>No transactions found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</div>



<script>
    // For retail store section
    function showReportTab(id) {
        const tabs = document.querySelectorAll('.reportTab');
        const contents = document.querySelectorAll('.report-tab-content');

        tabs.forEach(tab => tab.classList.remove('active'));
        contents.forEach(content => content.classList.remove('active'));

        document.querySelector(`#${id}`).classList.add('active');
        document.querySelector(`[onclick="showReportTab('${id}')"]`).classList.add('active');
    }
</script>

<!-- ✅ JavaScript to Export Table -->
<script>
    function exportTableToCSV(tableId) {
        const table = document.getElementById(tableId);
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
        link.download = "detail.csv";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>