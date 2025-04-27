<h2>Workers Management</h2>
<p>Manage factory workers, shifts, and attendance</p>

<!-- Search and Add User Row -->
<div class="container-fluid d-flex justify-content-between align-items-center mb-3">
    <!-- search bar -->
    <div class="d-flex w-75">
        <div class="input-group w-100 me-2">
            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control border-start-0" placeholder="Search..." />
        </div>
    </div>

    <!-- Button -->
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary"><i class="fa-solid fa-filter"></i></button>
        <button class="btn btn-outline-primary"> Attendance</button>
        <button class="btn btn-outline-primary">Add Worker</button>
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
            <div class="justify-content-end">
                <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-calendar-week"></i> Shift
                    Schedule</button>
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
        <div class="d-flex justify-content-between align-items-center border rounded mb-3 p-3"
            style="background-color:#e7f3ff;">
            <div class="me-3 gustify-content-start align-items-start d-flex gap-2">
                <div class="text-primary fs-4 px-3 py-2">
                    <i class="fa-regular fa-clock"></i>
                </div>
                <div>
                    <h6 class="mb-1 fw-bold text-primary">Current Shift: Morning (7AM-3PM)</h6>
                    <!-- Dynamic value from database -->
                    <small class="text-muted">Next shift change at 3:00 PM</small> <!-- Dynamic value from database -->
                </div>
            </div>
            <div class="justify-content-end">
                <button class="btn btn-outline-dark btn-sm">Mark Attendance</button>
            </div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">

    <div id="workers">
        <h1>Workers Directory</h1>
        <p>Complete list of factory workers with status and details</p>
        <table id="Table" class="table table-bordered table-hover">
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
                    <td>W001</td> <!-- Dynamic data -->
                    <td>Raj Kumar</td> <!-- Dynamic data -->
                    <td>Wire Production</td> <!-- Dynamic data -->
                    <td>Production Line Operator</td> <!-- Dynamic data -->
                    <td>Morning (7AM-3PM)</td> <!-- Dynamic data -->
                    <td>Present</td> <!-- Dynamic data -->
                    <td>92%</td> <!-- Dynamic data -->
                    <td><button class="btn btn-outline-primary btn-sm">Details</button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

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
                <strong>Machine Safety Training</strong><br/> <!-- Dynamic data from database -->
                <small>Wire Production Department</small> <!-- Dynamic data from database -->
            </div>
            <div class="justify-content-end">
                <strong>15 Workers</strong><br/> <!-- Dynamic data from database -->
                <small>28 Apr, 2025</small> <!-- Dynamic data from database -->
            </div>
        </div>
    </div>
</div>