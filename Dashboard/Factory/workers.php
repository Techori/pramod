<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../_conn.php';
$user_name = $_SESSION['user_name'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['whatAction'])) {

    if ($_POST['whatAction'] === 'addWorker') {

        $name = $_POST['workerName'];
        $department = $_POST['department'];
        $role = $_POST['role'];
        $shift = $_POST['shift'];

        // Generate a new worker ID
        $result = $conn->query("SELECT id FROM factory_workers WHERE created_for = '$user_name' ORDER BY CAST(SUBSTRING(id, 5) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");

        if ($result && $row = $result->fetch_assoc()) {
            $lastId = $row['id'];
            $num = (int) substr($lastId, 4);
            $newNum = $num + 1;
        } else {
            $newNum = 1;
        }

        $newWorkerId = 'WR-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);

        $stmt = $conn->prepare("INSERT INTO factory_workers 
                (id, name, department, role, shift, created_for) 
                VALUES (?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("ssssss", $newWorkerId, $name, $department, $role, $shift, $user_name);
        $stmt->execute();

        $conn->commit();
        $stmt->close();

        @header("Location: factory_dashboard.php?page=workers");
        exit;

    }
}

?>

<h2>Workers Management</h2>
<p>Manage factory workers, shifts, and attendance</p>

<!-- Search and Add User Row -->
<div class="container-fluid d-flex justify-content-between align-items-center mb-3">
    <!-- search bar -->
    <div class="d-flex w-75">
        <div class="input-group w-100 me-2">
            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Search..." />
        </div>
    </div>

    <!-- Button -->
    <div class="d-flex gap-2">
        <!-- Attendance Button -->
        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#attendanceModal">
            Attendance
        </button>
        <!-- Attendance Modal -->
        <div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg"> <!-- modal-lg for wider layout -->
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="attendanceModalLabel">Mark Attendance</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <form id="attendanceForm">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Worker Name</th>
                                        <th>Attendance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Example 5 workers, copy for 48 -->
                                    <tr>
                                        <td>1</td>
                                        <td>Ali Khan</td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="worker1"
                                                    value="Present" required>
                                                <label class="form-check-label">Present</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="worker1"
                                                    value="Absent">
                                                <label class="form-check-label">Absent</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Maria Bano</td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="worker2"
                                                    value="Present" required>
                                                <label class="form-check-label">Present</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="worker2"
                                                    value="Absent">
                                                <label class="form-check-label">Absent</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Usman Ahmed</td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="worker3"
                                                    value="Present" required>
                                                <label class="form-check-label">Present</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="worker3"
                                                    value="Absent">
                                                <label class="form-check-label">Absent</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>Rabia Saeed</td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="worker4"
                                                    value="Present" required>
                                                <label class="form-check-label">Present</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="worker4"
                                                    value="Absent">
                                                <label class="form-check-label">Absent</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>Faisal Iqbal</td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="worker5"
                                                    value="Present" required>
                                                <label class="form-check-label">Present</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="worker5"
                                                    value="Absent">
                                                <label class="form-check-label">Absent</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Repeat for all 48 workers -->
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-success">Submit Attendance</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        <!-- Add Worker Button -->
        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#addWorkerModal">
            Add Worker
        </button>
    </div>
</div>


<!-- Modal Structure -->
<div class="modal fade" id="addWorkerModal" tabindex="-1" aria-labelledby="addWorkerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="workers.php" method="POST">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="addWorkerModalLabel">Add Worker</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Modal Body -->
                <div class="modal-body">
                    <!-- Form to add worker -->
                    <div class="mb-3">
                        <label for="workerName" class="form-label">Worker Name</label>
                        <input type="text" class="form-control" id="workerName" name="workerName" required>
                    </div>
                    <div class="mb-3">
                        <label for="department" class="form-label">Department</label>
                        <input type="text" class="form-control" id="department" name="department" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <input type="text" class="form-control" id="role" name="role" required>
                    </div>
                    <div class="mb-3">
                        <label for="shift" class="form-label">Shift</label>
                        <select class="form-select" id="shift" name="shift" required>
                            <option value="">Select Shift</option>
                            <option value="Morning">Morning</option>
                            <option value="Evening">Evening</option>
                            <option value="Night">Night</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success" name="whatAction" value="addWorker">Add Worker</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php

$worker = $conn->query("SELECT COUNT(*) as count FROM factory_workers WHERE created_for = '$user_name'")->fetch_assoc()['count'];
?>

<!-- Cards -->
<!-- <div class="row">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
            <div class="card-body">
                <h6 class="text-muted">Total Workers</h6>
                <h3 class="fw-bold"><?= $worker ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
            <div class="card-body">
                <h6 class="text-muted">Present Today</h6>
                <h3 class="fw-bold">42</h3> 
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
            <div class="card-body">
                <h6 class="text-muted">Absent</h6>
                <h3 class="fw-bold">6</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
            <div class="card-body">
                <h6 class="text-muted">Attendance Rate</h6>
                <h3 class="fw-bold">87.5%</h3>
            </div>
        </div>
    </div>
</div> -->

<!-- Production Lines Status -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div class="justify-content-start">
                <h5 class="mb-0">Attendance Management</h5>
            </div>
            <!-- Shift Schedule Button (will open the modal) -->
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#shiftModal">
                <i class="fa-solid fa-calendar-week"></i> Shift Schedule
            </button>

            <!-- Shift Schedule Modal -->
            <div class="modal fade" id="shiftModal" tabindex="-1" aria-labelledby="shiftModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title" id="shiftModalLabel">Select Shift Schedule</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <form id="shiftForm">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="shift" id="morning"
                                        value="Morning Shift (08:00 AM - 04:00 PM)">
                                    <label class="form-check-label" for="morning">Morning Shift (08:00 AM - 04:00
                                        PM)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="shift" id="evening"
                                        value="Evening Shift (04:00 PM - 12:00 AM)">
                                    <label class="form-check-label" for="evening">Evening Shift (04:00 PM - 12:00
                                        AM)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="shift" id="night"
                                        value="Night Shift (12:00 AM - 08:00 AM)">
                                    <label class="form-check-label" for="night">Night Shift (12:00 AM - 08:00
                                        AM)</label>
                                </div>
                            </form>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-primary" onclick="submitShift()">Confirm Schedule</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center border rounded my-3 p-3"
            style="background-color: #e7f3ff;">
            <div class="me-3 justify-content-start align-items-start d-flex gap-2">
                <div class="text-primary fs-4 px-3 py-2">
                    <i class="fa-regular fa-clock"></i>
                </div>
                <div>
                    <h6 class="mb-1 fw-bold text-primary">Current Shift: <span id="currentShift">Morning
                            (7AM-3PM)</span></h6>
                    <small class="text-muted" id="nextShiftChange">Next shift change at 3:00 PM</small>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <!-- Trigger Modal for Attendance -->
                <button class="btn btn-outline-dark btn-sm" data-bs-toggle="modal"
                    data-bs-target="#attendanceModal">Mark Attendance</button>
            </div>
        </div>

        <!-- Modal for Mark Attendance -->
        <div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="attendanceModalLabel">Mark Attendance</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Attendance Form -->
                        <form id="attendanceForm">
                            <h6>Morning Shift (7AM-3PM)</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="morningShift" value="Present">
                                <label class="form-check-label" for="morningShift">Present</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="morningShift" value="Absent">
                                <label class="form-check-label" for="morningShift">Absent</label>
                            </div>

                            <h6>Evening Shift (3PM-11PM)</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="eveningShift" value="Present">
                                <label class="form-check-label" for="eveningShift">Present</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="eveningShift" value="Absent">
                                <label class="form-check-label" for="eveningShift">Absent</label>
                            </div>

                            <h6>Night Shift (11PM-7AM)</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="nightShift" value="Present">
                                <label class="form-check-label" for="nightShift">Present</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="nightShift" value="Absent">
                                <label class="form-check-label" for="nightShift">Absent</label>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="submitAttendance()">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

<!-- Table -->
<div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">
    <div id="workers">
        <h1>Workers Directory</h1>
        <p>Complete list of factory workers with status and details</p>
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
                    <?php if ($hasDeletePermission): ?>
                            <th>Action</th>
                        <?php endif; ?>
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
                        if ($hasDeletePermission) {
                                echo "<td>
                                    <form method='post' action='' onsubmit='return confirm(\"Are you sure you want to delete this worker?\");'>
                                        <input type='hidden' name='worker_id' value='" . htmlspecialchars($row['id']) . "'>
                                        <button type='submit' name='deleteWorker' class='btn btn-danger btn-sm'>
                                            <i class='fa-solid fa-trash'></i> Delete
                                        </button>
                                    </form>
                                  </td>";
                            }
                            echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='" . ($hasDeletePermission ? 6 : 5) . "' class='text-center'>No transactions found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteWorker']) && $hasDeletePermission) {
        $worker_id = $conn->real_escape_string($_POST['worker_id']);

        // Prepare and execute delete query
        $deleteSql = "DELETE FROM factory_workers WHERE id = ? AND created_for = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("ss", $worker_id, $user_name);

        if ($stmt->execute()) {
            echo "<script>alert('Worker deleted successfully!'); window.location.href=window.location.href;</script>";
        } else {
            echo "<script>alert('Error deleting worker: " . $conn->error . "');</script>";
        }

        $stmt->close();
    }
    ?>

<!-- Worker Details Modal -->
<div class="modal fade" id="workerDetailsModal" tabindex="-1" aria-labelledby="workerDetailsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="workerDetailsModalLabel">Worker Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <ul class="list-group">
                    <li class="list-group-item"><strong>ID:</strong> <span id="modalId"></span></li>
                    <li class="list-group-item"><strong>Name:</strong> <span id="modalName"></span></li>
                    <li class="list-group-item"><strong>Department:</strong> <span id="modalDepartment"></span></li>
                    <li class="list-group-item"><strong>Role:</strong> <span id="modalRole"></span></li>
                    <li class="list-group-item"><strong>Shift:</strong> <span id="modalShift"></span></li>
                    <li class="list-group-item"><strong>Status:</strong> <span id="modalStatus"></span></li>
                    <li class="list-group-item"><strong>Attendance:</strong> <span id="modalAttendance"></span></li>
                </ul>
            </div>

        </div>
    </div>
</div>

<script>
    // Search Functionality
    document.getElementById('searchInput').addEventListener('input', function () {
        const searchText = this.value.toLowerCase();
        const rows = document.querySelectorAll('#supplyTable tbody tr');

        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            let match = false;
            for (let i = 0; i < cells.length; i++) {
                if (cells[i].textContent.toLowerCase().includes(searchText)) {
                    match = true;
                    break;
                }
            }
            row.style.display = match ? '' : 'none';
        });
    });
</script>

<script>

    // shift schedule ke liye
    function submitShift() {
        const selectedShift = document.querySelector('input[name="shift"]:checked');
        if (selectedShift) {
            // Show a confirmation alert with the selected shift
            alert("Shift Scheduled: " + selectedShift.value);

            // Clear the radio button selection
            document.querySelectorAll('input[name="shift"]').forEach(radio => {
                radio.checked = false;  // Uncheck all radio buttons
            });

            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('shiftModal'));
            modal.hide();
        } else {
            alert("Please select a shift.");
        }
    }

    // shift wise attendance
    function submitAttendance() {
        // Get all selected attendance values
        const morningShift = document.querySelector('input[name="morningShift"]:checked');
        const eveningShift = document.querySelector('input[name="eveningShift"]:checked');
        const nightShift = document.querySelector('input[name="nightShift"]:checked');

        if (morningShift && eveningShift && nightShift) {
            // Display selected attendance data
            alert(`Morning Shift: ${morningShift.value}\nEvening Shift: ${eveningShift.value}\nNight Shift: ${nightShift.value}`);

            // Reset form and close the modal
            document.getElementById('attendanceForm').reset();
            const modal = new bootstrap.Modal(document.getElementById('attendanceModal'));
            modal.hide();
        } else {
            alert("Please mark attendance for all shifts.");
        }
    }
</script>