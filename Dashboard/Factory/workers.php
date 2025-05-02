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
        <button class="btn btn-outline-primary"><i class="fa-solid fa-filter"></i></button>
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

        <!-- Modal Structure -->
        <div class="modal fade" id="addWorkerModal" tabindex="-1" aria-labelledby="addWorkerModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="addWorkerModalLabel">Add Worker</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <form id="workerForm">
                            <div class="mb-3">
                                <label for="workerName" class="form-label">Worker Name</label>
                                <input type="text" class="form-control" id="workerName" required>
                            </div>
                            <div class="mb-3">
                                <label for="department" class="form-label">Department</label>
                                <input type="text" class="form-control" id="department" required>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <input type="text" class="form-control" id="role" required>
                            </div>
                            <div class="mb-3">
                                <label for="shift" class="form-label">Shift</label>
                                <select class="form-select" id="shift" required>
                                    <option value="">Select Shift</option>
                                    <option value="Morning">Morning</option>
                                    <option value="Evening">Evening</option>
                                    <option value="Night">Night</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Add Worker</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cards -->
<div class="row">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
            <div class="card-body">
                <h6 class="text-muted">Total Workers</h6>
                <h3 class="fw-bold">48</h3> <!-- Dynamic value from database -->
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
            <div class="card-body">
                <h6 class="text-muted">Present Today</h6>
                <h3 class="fw-bold">42</h3> <!-- Dynamic value from database -->
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
            <div class="card-body">
                <h6 class="text-muted">Absent</h6>
                <h3 class="fw-bold">6</h3> <!-- Dynamic value from database -->
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
            <div class="card-body">
                <h6 class="text-muted">Attendance Rate</h6>
                <h3 class="fw-bold">87.5%</h3> <!-- Dynamic value from database -->
            </div>
        </div>
    </div>
</div>

<!-- Production Lines Status -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div class="justify-content-start">
                <h5 class="mb-0">Production Lines Status</h5>
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
            <input class="form-check-input" type="radio" name="shift" id="morning" value="Morning Shift (08:00 AM - 04:00 PM)">
            <label class="form-check-label" for="morning">Morning Shift (08:00 AM - 04:00 PM)</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="shift" id="evening" value="Evening Shift (04:00 PM - 12:00 AM)">
            <label class="form-check-label" for="evening">Evening Shift (04:00 PM - 12:00 AM)</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="shift" id="night" value="Night Shift (12:00 AM - 08:00 AM)">
            <label class="form-check-label" for="night">Night Shift (12:00 AM - 08:00 AM)</label>
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
        <small class="text-muted">Workers currently on the factory floor by department</small>
        <div class="row g-2 mt-2">
            <div class="col-md-3 col-sm-6 mb-2">
                <div class="p-3 border rounded">
                    <strong>Wire Production</strong>
                    <div class="mt-2">
                        <small>12 workers present</small> <!-- Dynamic value from database -->
                        <div class="progress mt-1">
                            <div class="progress-bar bg-primary" style="width: 85%"></div> <!-- Dynamic value -->
                        </div>
                        <small class="text-muted">85% of team present</small> <!-- Dynamic value -->
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-2">
                <div class="p-3 border rounded">
                    <strong>Cable Assembly</strong>
                    <div class="mt-2">
                        <small>9 workers present</small> <!-- Dynamic value from database -->
                        <div class="progress mt-1">
                            <div class="progress-bar bg-primary" style="width: 90%"></div> <!-- Dynamic value -->
                        </div>
                        <small class="text-muted">90% of team present</small> <!-- Dynamic value -->
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-2">
                <div class="p-3 border rounded">
                    <strong>Quality Control</strong>
                    <div class="mt-2">
                        <small>6 workers present</small> <!-- Dynamic value from database -->
                        <div class="progress mt-1">
                            <div class="progress-bar bg-primary" style="width: 100%"></div> <!-- Dynamic value -->
                        </div>
                        <small class="text-muted">100% of team present</small> <!-- Dynamic value -->
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-2">
                <div class="p-3 border rounded">
                    <strong>Packaging</strong>
                    <div class="mt-2">
                        <small>7 workers present</small> <!-- Dynamic value from database -->
                        <div class="progress mt-1">
                            <div class="progress-bar bg-primary" style="width: 78%"></div> <!-- Dynamic value -->
                        </div>
                        <small class="text-muted">78% of team present</small> <!-- Dynamic value -->
                    </div>
                </div>
            </div>

        </div>
        <div class="d-flex justify-content-between align-items-center border rounded mb-3 p-3" style="background-color: #e7f3ff;">
    <div class="me-3 justify-content-start align-items-start d-flex gap-2">
        <div class="text-primary fs-4 px-3 py-2">
            <i class="fa-regular fa-clock"></i>
        </div>
        <div>
            <h6 class="mb-1 fw-bold text-primary">Current Shift: <span id="currentShift">Morning (7AM-3PM)</span></h6>
            <small class="text-muted" id="nextShiftChange">Next shift change at 3:00 PM</small>
        </div>
    </div>
    <div class="d-flex justify-content-end">
        <!-- Trigger Modal for Attendance -->
        <button class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#attendanceModal">Mark Attendance</button>
    </div>
</div>

<!-- Modal for Mark Attendance -->
<div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
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
                    <th>Status</th>
                    <th>Attendance</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>W001</td>
                    <td>Raj Kumar</td>
                    <td>Wire Production</td>
                    <td>Production Line Operator</td>
                    <td>Morning (7AM-3PM)</td>
                    <td>Present</td>
                    <td>92%</td>
                    <td><button class="btn btn-outline-primary btn-sm viewDetailsBtn">Details</button></td>
                </tr>
                <!-- Add more rows here -->
            </tbody>
        </table>
    </div>
</div>

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
<!-- Productivity -->
<div class="row mb-4">
    <div class="card shadow-sm col-md-6 col-sm-6 mx-3 mb-2">
        <h5 class="mt-3">Productivity By Department</h5>
        <p>Output efficiency by department for current week</p>
        <div class="mt-2">
            <div class="d-flex justify-content-between align-items-center">
                <div class="justify-content-start">
                    <strong>Wire Production</strong>
                </div>
                <div class="justify-content-end">
                    <small>92% efficiency</small> <!-- Dynamic value from database -->
                </div>
            </div>
            <div class="progress mt-1">
                <div class="progress-bar bg-primary" style="width: 92%"></div> <!-- Dynamic value -->
            </div>
        </div>
        <div class="mt-2">
            <div class="d-flex justify-content-between align-items-center">
                <div class="justify-content-start">
                    <strong>Cable Assembly</strong>
                </div>
                <div class="justify-content-end">
                    <small>86% efficiency</small> <!-- Dynamic value from database -->
                </div>
            </div>
            <div class="progress mt-1">
                <div class="progress-bar bg-primary" style="width: 86%"></div> <!-- Dynamic value -->
            </div>
        </div>
        <div class="mt-2">
            <div class="d-flex justify-content-between align-items-center">
                <div class="justify-content-start">
                    <strong>Quality Control</strong>
                </div>
                <div class="justify-content-end">
                    <small>95% efficiency</small> <!-- Dynamic value from database -->
                </div>
            </div>
            <div class="progress mt-1">
                <div class="progress-bar bg-primary" style="width: 95%"></div> <!-- Dynamic value -->
            </div>
        </div>
        <div class="mt-2 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="justify-content-start">
                    <strong>Packaging</strong>
                </div>
                <div class="justify-content-end">
                    <small>88% efficiency</small> <!-- Dynamic value from database -->
                </div>
            </div>
            <div class="progress mt-1">
                <div class="progress-bar bg-primary" style="width: 88%"></div> <!-- Dynamic value -->
            </div>
        </div>
    </div>

    <!-- Upcoming Training Sessions -->
    <div class="card shadow-sm col-md-5 col-sm-6 ms-2 mb-2">
        <h5 class="mt-3">Upcoming Training Sessions</h5>
        <p>Scheduled training and skill development programs</p>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="justify-content-start">
                <strong>Machine Safety Training</strong><br /> <!-- Dynamic data from database -->
                <small>Wire Production Department</small> <!-- Dynamic data from database -->
            </div>
            <div class="justify-content-end">
                <strong>15 Workers</strong><br /> <!-- Dynamic data from database -->
                <small>28 Apr, 2025</small> <!-- Dynamic data from database -->
            </div>
        </div>
    </div>
</div>
<script>
    <!-- workers ko add krane ke liye-->

    document.getElementById('workerForm').addEventListener('submit', function (e) {
        e.preventDefault();
        alert('Worker added successfully!');
        // Collect values if needed
        // let name = document.getElementById('workerName').value;
    });

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

    // data show krane ke liye details-btn ke through
    // Event listener for all "Details" buttons
    document.querySelectorAll('.viewDetailsBtn').forEach(button => {
        button.addEventListener('click', function () {
            const row = this.closest('tr');
            const cells = row.querySelectorAll('td');

            // Fill modal with row data
            document.getElementById('modalId').textContent = cells[0].textContent;
            document.getElementById('modalName').textContent = cells[1].textContent;
            document.getElementById('modalDepartment').textContent = cells[2].textContent;
            document.getElementById('modalRole').textContent = cells[3].textContent;
            document.getElementById('modalShift').textContent = cells[4].textContent;
            document.getElementById('modalStatus').textContent = cells[5].textContent;
            document.getElementById('modalAttendance').textContent = cells[6].textContent;

            // Show the modal
            var modal = new bootstrap.Modal(document.getElementById('workerDetailsModal'));
            modal.show();
        });
    });
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