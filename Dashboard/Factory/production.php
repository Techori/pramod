<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../_conn.php';
$user_name = $_SESSION['user_name'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['whatAction'])) {

    if ($_POST['whatAction'] === 'addProduction') {

        $product = $_POST['productInput'];
        $quantity = $_POST['quantityInput'];
        $unit = $_POST['unit'];
        $start_date = $_POST['startDateInput'];
        $end_date = $_POST['endDateInput'];
        $Status = $_POST['statusInput'];


        // Generate Expense ID
        $result = $conn->query("SELECT id FROM factory_production ORDER BY CAST(SUBSTRING(id, 5) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");

        if ($result && $row = $result->fetch_assoc()) {
            $lastId = $row['id']; // e.g. TRX-005
            $num = (int) substr($lastId, 4);   // get "005" → 5
            $newNum = $num + 1;
        } else {
            $newNum = 1;
        }

        $newProductionId = 'PRD-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);

        $stmt = $conn->prepare("INSERT INTO factory_production 
                (id, product, quantity, unit, start_date, end_date, status, created_for) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("sssdssss", $newProductionId, $product, $quantity, $unit, $start_date, $end_date, $Status, $user_name);
        $stmt->execute();

        $conn->commit();
        $stmt->close();

        @header("Location: factory_dashboard.php?page=production");
        exit;

    } else if ($_POST['whatAction'] === 'updateProduct') {
        $id = $_POST['tracking_id'];
        $status = $_POST['status'];

        $stmt = $conn->prepare("UPDATE factory_production SET status = ? WHERE id  = ?");
        $stmt->bind_param("ss", $status, $id);
        $stmt->execute();
        $stmt->close();


        @header("Location: factory_dashboard.php?page=production");
        exit;

    }
}

?>

<h1>Production Management</h1>
<p>Monitor and manage factory production lines</p>


<div class="card shadow-sm p-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="fw-bold">Production Schedule</h2>
            <p class="text-muted">Upcoming and in-progress production runs</p>
        </div>
        <div>
            <!-- Schedule Button -->
            <button class="btn btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#scheduleModal">
                <i class="bi bi-calendar-event"></i> Schedule
            </button>

        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
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

<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="production.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="scheduleModalLabel">Add Expenses</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label for="productInput" class="form-label">Product</label>
                        <input type="text" class="form-control" id="productInput" name="productInput" required>
                    </div>
                    <div class="mb-3">
                        <label for="quantityInput" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantityInput" name="quantityInput" required>
                    </div>
                    <div class="mb-3">
                        <label for="unit" class="form-label">Per Unit</label>
                        <input type="text" class="form-control" id="unit" name="unit" required>
                    </div>
                    <div class="mb-3">
                        <label for="startDateInput" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="startDateInput" name="startDateInput" required>
                    </div>
                    <div class="mb-3">
                        <label for="endDateInput" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="endDateInput" name="endDateInput" required>
                    </div>
                    <div class="mb-3">
                        <label for="statusInput" class="form-label">Status</label>
                        <select class="form-select" id="statusInput" name="statusInput" required>
                            <option selected disabled value="">Choose...</option>
                            <option value="Pending">Pending</option>
                            <option value="Scheduled">Scheduled</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" name="whatAction"
                            value="addProduction">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>