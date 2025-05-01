<?php
session_start();
if (!(isset($_SESSION["uid"]) && isset($_SESSION["user_type"]) && isset($_SESSION["session_id"]))) {
    header("location:../../login.php");
    exit;
} else {
    if (in_array($_SESSION["user_type"], ['Faculty', 'Store', 'Vendor'])) {
        header("location:../index.php");
        exit;

    } else if (!($_SESSION["user_type"] == 'Factory')) {
        header("location:../../login.php");
        exit;
    }
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factory Dashboard - Unnati Factory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../public/css/styles.css">.
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            color: #333;
        }
    </style>
</head>

<body class="bg-secondary bg-opacity-10">
    <?php
    include '_factory_nav.php';
    ?>
    <main>
        <?php if ($page === 'dashboard'): ?>
            <!-- Search and Add User Row -->
            <div class="container-fluid d-flex justify-content-between align-items-center mb-3">
                <!-- search bar -->
                <div class="d-flex w-75">
                    <div class="input-group w-100 me-2">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control border-start-0" placeholder="Search..." />
                    </div>
                </div>

                <!-- Add User Button -->
                <!-- Button -->
                <div>
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#materialRequestModal">
                        <i class="fa-solid fa-user-plus"></i> Request Materials
                    </button>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="schedulingModal" tabindex="-1" aria-labelledby="schedulingModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content rounded-4 shadow">
                            <div class="modal-header">
                                <h5 class="modal-title" id="schedulingModalLabel">Production Scheduling</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Scheduling Form -->
                                <form>
                                    <div class="mb-3">
                                        <label for="scheduleDate" class="form-label">Select Date</label>
                                        <input type="date" class="form-control" id="scheduleDate" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="shift" class="form-label">Shift</label>
                                        <select class="form-select" id="shift" required>
                                            <option value="">Choose Shift</option>
                                            <option>Morning</option>
                                            <option>Evening</option>
                                            <option>Night</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="quantity" class="form-label">Quantity to Produce</label>
                                        <input type="number" class="form-control" id="quantity"
                                            placeholder="e.g., 1000 units" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Schedule</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#materialRequestModal">
                        <i class="fa-solid fa-user-plus"></i> Request Materials
                    </button>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="materialRequestModal" tabindex="-1" aria-labelledby="materialRequestLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content rounded-4 shadow">
                            <div class="modal-header">
                                <h5 class="modal-title" id="materialRequestLabel">Material Request</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <div class="mb-3">
                                        <label for="materialName" class="form-label">Material Name</label>
                                        <input type="text" class="form-control" id="materialName"
                                            placeholder="e.g., Copper Wire" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="quantity" class="form-label">Quantity Needed</label>
                                        <input type="number" class="form-control" id="quantity" placeholder="e.g., 200"
                                            required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit Request</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="container-fluid mt-3">
                <!-- Metrics Row -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card p-3 border-start border-3 border-primary">
                            <small>Today's Production</small>
                            <h3>1,450 units</h3>
                            <small class="text-success">+8.5% vs last month</small>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card p-3 border-start border-3 border-success">
                            <small>Raw Material Stock</small>
                            <h3>24.5 tons</h3>
                            <small class="text-danger">-3.2% vs last month</small>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card p-3 border-start border-3 border-warning">
                            <small>Production Queue</small>
                            <h3>8 orders</h3>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card p-3 border-start border-3 border-purple"
                            style="--bs-border-opacity: 1; border-color: #6f42c1;">
                            <small>Workers Present</small>
                            <h3>32 active</h3>
                        </div>
                    </div>
                </div>

                <!-- Production Lines Status -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5>Production Lines Status</h5>
                        <small class="text-muted">Real-time status of production lines</small>
                        <div class="row g-3 mt-3">
                            <div class="col-md-4">
                                <div class="p-3 border rounded">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                            <strong>Wire Line</strong>
                                        </div>
                                        <span class="badge bg-success">Active</span>
                                    </div>
                                    <div class="mt-2">
                                        <small>Efficiency: 85%</small>
                                        <div class="progress mt-1">
                                            <div class="progress-bar bg-primary" style="width: 81%"></div>
                                        </div>
                                        <small class="text-muted">650/800 units</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                                            <strong>Cable Assembly</strong>
                                        </div>
                                        <span class="badge bg-warning text-dark">Maintenance</span>
                                    </div>
                                    <div class="mt-2">
                                        <small>Efficiency: 45%</small>
                                        <div class="progress mt-1">
                                            <div class="progress-bar bg-info" style="width: 45%"></div>
                                        </div>
                                        <small class="text-muted">270/600 units</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                            <strong>Quality Control</strong>
                                        </div>
                                        <span class="badge bg-success">Active</span>
                                    </div>
                                    <div class="mt-2">
                                        <small>Pass Rate: 92%</small>
                                        <div class="progress mt-1">
                                            <div class="progress-bar bg-success" style="width: 92%"></div>
                                        </div>
                                        <small class="text-muted">580 inspected</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts & Alerts -->
                <div class="row">
                <div class="col-lg-8 mb-4">
            <div class="card p-3">
                <h5>Production Output (Last 6 months)</h5>
                <canvas id="outputChart" height="200"></canvas>
            </div>
        </div>
                    <div class="col-lg-4 mb-4">
                        <div class="card p-3">
                            <h5>Alerts & Notifications</h5>
                            <div class="alert alert-danger">
                                <i class="bi bi-bug-fill"></i> Extruder #2 error - maintenance required
                            </div>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-circle"></i> Low Aluminum stock - order materials
                            </div>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Order PO-7845 completed
                            </div>
                        </div>
                    </div>
                </div>

                <div class="btn-group" role="group" aria-label="Default button group">
                    <!-- Button -->
                    <button type="button" class="btn btn-outline-primary shadow-sm rounded-3 p-4" style="min-width: 120px;"
                        data-bs-toggle="modal" data-bs-target="#schedulingModal">
                        Production Scheduling
                    </button>
                    <!-- Modal -->
                    <div class="modal fade" id="schedulingModal" tabindex="-1" aria-labelledby="schedulingModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content rounded-4 shadow">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="schedulingModalLabel">Production Scheduling</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Your scheduling form goes here -->
                                    <form>
                                        <div class="mb-3">
                                            <label for="scheduleDate" class="form-label">Select Date</label>
                                            <input type="date" class="form-control" id="scheduleDate">
                                        </div>
                                        <div class="mb-3">
                                            <label for="shift" class="form-label">Shift</label>
                                            <select class="form-select" id="shift">
                                                <option>Select Date</option>
                                                <option>Shift</option>
                                                <option>Night</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Save Schedule</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Button -->
                    <button type="button" class="btn btn-outline-primary shadow-sm rounded-3 p-4" style="min-width: 120px;"
                        data-bs-toggle="modal" data-bs-target="#rawMaterialsModal">
                        Raw Materials
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="rawMaterialsModal" tabindex="-1" aria-labelledby="rawMaterialsModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content rounded-4 shadow">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="rawMaterialsModalLabel">Raw Materials Entry</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Raw Materials Form -->
                                    <form>
                                        <div class="mb-3">
                                            <label for="materialName" class="form-label">Material Name</label>
                                            <input type="text" class="form-control" id="materialName"
                                                placeholder="e.g., Steel">
                                        </div>
                                        <div class="mb-3">
                                            <label for="quantity" class="form-label">Quantity (kg)</label>
                                            <input type="number" class="form-control" id="quantity" placeholder="e.g., 500">
                                        </div>
                                        <div class="mb-3">
                                            <label for="supplier" class="form-label">Supplier</label>
                                            <input type="text" class="form-control" id="supplier"
                                                placeholder="e.g., ABC Corp">
                                        </div>
                                        <button type="submit" class="btn btn-primary">Add Material</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="workers.php" class="btn btn-outline-primary shadow-sm rounded-3 p-4" style="min-width: 120px;">
                        Workers
                    </a>
                    <a href="reports.php" class="btn btn-outline-primary shadow-sm rounded-3 p-4" style="min-width: 120px;">
                        Reports
                    </a>
                </div>

                <!-- Raw Material Usage & Status -->
                <div class="row mb-4">
                    <div class="col-lg-6">
                        <div class="card p-3">
                            <h5>Raw Material Usage</h5>
                            <canvas id="usageChart" height="200"></canvas>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card p-3">
                            <h5>Raw Material Status</h5>
                            <ul class="list-unstyled">
                                <li class="d-flex justify-content-between align-items-center mb-2">
                                    Copper Wire <span>12.4/20 tons</span>
                                </li>
                                <li class="d-flex justify-content-between align-items-center mb-2">
                                    PVC Compound <span>8.2/15 tons</span>
                                </li>
                                <li class="d-flex justify-content-between align-items-center mb-2">
                                    Aluminum <span class="text-danger">1.8/10 tons</span>
                                </li>
                                <li class="d-flex justify-content-between align-items-center">
                                    Rubber Insulation <span>2.1/5 tons</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Pending Orders & Machine Status -->
                <div class="row">
                    <div class="col-lg-8 mb-4">
                        <div class="card p-3">
                            <h5>Pending Production Orders</h5>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>PO-7845</td>
                                        <td>1.5mm Wire</td>
                                        <td>2,500 m</td>
                                        <td>12 Apr 2025</td>
                                        <td><span class="badge bg-primary">In Progress</span></td>
                                    </tr>
                                    <tr>
                                        <td>PO-7842</td>
                                        <td>2.5mm Cable</td>
                                        <td>1,800 m</td>
                                        <td>14 Apr 2025</td>
                                        <td><span class="badge bg-warning text-dark">Queued</span></td>
                                    </tr>
                                    <tr>
                                        <td>PO-7839</td>
                                        <td>4mm Armored</td>
                                        <td>950 m</td>
                                        <td>15 Apr 2025</td>
                                        <td><span class="badge bg-warning text-dark">Queued</span></td>
                                    </tr>
                                    <tr>
                                        <td>PO-7835</td>
                                        <td>6mm Power</td>
                                        <td>750 m</td>
                                        <td>18 Apr 2025</td>
                                        <td><span class="badge bg-secondary">Scheduled</span></td>
                                    </tr>
                                    <tr>
                                        <td>PO-7830</td>
                                        <td>1mm Flexible</td>
                                        <td>3,200 m</td>
                                        <td>20 Apr 2025</td>
                                        <td><span class="badge bg-secondary">Scheduled</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4">
                        <div class="card p-3">
                            <h5>Machine Status</h5>
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Extruder Machine <span class="badge bg-success">Operational</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Winding Machine <span class="badge bg-success">Operational</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Coating Unit <span class="badge bg-warning text-dark">Maintenance</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Testing Equipment <span class="badge bg-success">Operational</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
            </div>
        <?php elseif ($page === 'production'): ?>
            <?php include 'production.php'; ?>

        <?php elseif ($page === 'billing_system'): ?>
            <?php include 'billing_system.php'; ?>

        <?php elseif ($page === 'supply_management'): ?>
            <?php include 'supply_management.php'; ?>

        <?php elseif ($page === 'raw_materials'): ?>
            <?php include 'raw_materials.php'; ?>

        <?php elseif ($page === 'inventory'): ?>
            <?php include 'inventory.php'; ?>

        <?php elseif ($page === 'workers'): ?>
            <?php include 'workers.php'; ?>

        <?php elseif ($page === 'expenses'): ?>
            <?php include 'expenses.php'; ?>

        <?php elseif ($page === 'after_sales_service'): ?>
            <?php include 'after_sales_service.php'; ?>

        <?php elseif ($page === 'reports'): ?>
            <?php include 'reports.php'; ?>

        <?php elseif ($page === 'settings'): ?>
            <?php include 'settings.php'; ?>

        <?php endif; ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle on mobile
        document.getElementById('toggleSidebar').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('open');
        });
        // Production Output Chart
        new Chart(document.getElementById('outputChart'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Units',
                    data: [4300, 3200, 4700, 4500, 6000, 5800],
                    borderColor: '#007bff',
                    tension: 0.3,
                    fill: false
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Raw Material Usage Chart
        new Chart(document.getElementById('usageChart'), {
            type: 'bar',
            data: {
                labels: ['Copper', 'PVC', 'Aluminum', 'Rubber'],
                datasets: [{
                    label: 'Usage (tons)',
                    data: [32, 45, 15, 8],
                    backgroundColor: '#007bff'
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>