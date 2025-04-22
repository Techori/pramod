<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factory Dashboard - Unnati Factory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: sans-serif; margin: 0; padding: 0; box-sizing: border-box; color: #333; }
        .sidebar { width: 60px; height: 100vh; position: fixed; top: 0; left: 0; background-color: #2c2f3e; color: #fff; padding-top: 20px; transition: width 0.3s; overflow: hidden; }
        .sidebar:hover { width: 200px; }
        .sidebar .logo { font-size: 1.2rem; font-weight: bold; text-align: center; padding: 10px; background: #1a1d2a; }
        .sidebar nav a { display: flex; align-items: center; padding: 10px; color: #ccc; text-decoration: none; transition: background 0.3s; }
        .sidebar nav a i { width: 24px; }
        .sidebar nav a span { margin-left: 10px; opacity: 0; transition: opacity 0.3s; }
        .sidebar:hover nav a span { opacity: 1; }
        .sidebar nav a.active, .sidebar nav a:hover { background-color: #b0e0e6; color: #000; }
        .sidebar .footer { position: absolute; bottom: 0; width: 100%; padding: 10px; font-size: .8rem; background: #1a1d2a; text-align: center; }
        main { margin-left: 60px; transition: margin-left 0.3s; padding: 20px; }
        .sidebar:hover ~ main { margin-left: 200px; }
        header.header { margin-left: 60px; transition: margin-left 0.3s; background: #fff; border-bottom: 1px solid #ddd; padding: 10px 20px; position: sticky; top: 0; z-index: 100; }
        .sidebar:hover ~ header.header { margin-left: 200px; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-200px); width: 200px; }
            .sidebar.open { transform: translateX(0); }
            main, header.header { margin-left: 0; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="logo">Unnati Factory</div>
        <nav class="nav flex-column mt-2">
            <a href="#" class="nav-link active"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
            <a href="#" class="nav-link"><i class="bi bi-gear"></i><span>Production</span></a>
            <a href="#" class="nav-link"><i class="bi bi-box-seam"></i><span>Inventory</span></a>
            <a href="#" class="nav-link"><i class="bi bi-truck"></i><span>Supply Management</span></a>
            <a href="#" class="nav-link"><i class="bi bi-boxes"></i><span>Raw Materials</span></a>
            <a href="#" class="nav-link"><i class="bi bi-clipboard-check"></i><span>Quality Control</span></a>
            <a href="#" class="nav-link"><i class="bi bi-receipt"></i><span>Billing System</span></a>
            <a href="#" class="nav-link"><i class="bi bi-cash-coin"></i><span>Expenses</span></a>
            <a href="#" class="nav-link"><i class="bi bi-people"></i><span>Workers</span></a>
            <a href="#" class="nav-link"><i class="bi bi-wrench"></i><span>Maintenance</span></a>
            <a href="#" class="nav-link"><i class="bi bi-bar-chart-line"></i><span>Reports</span></a>
            <a href="#" class="nav-link"><i class="bi bi-gear-fill"></i><span>Settings</span></a>
        </nav>
        <div class="footer">© 2025 Unnati Traders</div>
    </div>

    <!-- Header -->
    <header class="header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <button class="btn btn-sm btn-outline-secondary d-md-none me-2" id="toggleSidebar"><i class="bi bi-list"></i></button>
            <h5 class="mb-0">Factory Dashboard</h5>
        </div>
        <form class="d-flex" role="search">
            <input class="form-control form-control-sm me-2" type="search" placeholder="Search..." aria-label="Search">
            <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-search"></i></button>
        </form>
        <div class="d-flex align-items-center">
            <button class="btn btn-sm btn-outline-primary me-2"><i class="bi bi-bell"></i></button>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-person-circle"></i></button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">Profile</a></li>
                    <li><a class="dropdown-item" href="#">Logout</a></li>
                </ul>
            </div>
        </div>
    </header>

    <main id="mainContent">
        <div class="container-fluid">
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
                    <div class="card p-3 border-start border-3 border-purple" style="--bs-border-opacity: 1; border-color: #6f42c1;">
                        <small>Workers Present</small>
                        <h3>32 active</h3>
                    </div>
                </div>
            </div>

            <!-- Production Lines Status -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5>Production Lines Status <small class="text-muted">Real-time status of production lines</small></h5>
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
                                <tr><th>Order ID</th><th>Product</th><th>Qty</th><th>Due Date</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>PO-7845</td><td>1.5mm Wire</td><td>2,500 m</td><td>12 Apr 2025</td><td><span class="badge bg-primary">In Progress</span></td></tr>
                                <tr><td>PO-7842</td><td>2.5mm Cable</td><td>1,800 m</td><td>14 Apr 2025</td><td><span class="badge bg-warning text-dark">Queued</span></td></tr>
                                <tr><td>PO-7839</td><td>4mm Armored</td><td>950 m</td><td>15 Apr 2025</td><td><span class="badge bg-warning text-dark">Queued</span></td></tr>
                                <tr><td>PO-7835</td><td>6mm Power</td><td>750 m</td><td>18 Apr 2025</td><td><span class="badge bg-secondary">Scheduled</span></td></tr>
                                <tr><td>PO-7830</td><td>1mm Flexible</td><td>3,200 m</td><td>20 Apr 2025</td><td><span class="badge bg-secondary">Scheduled</span></td></tr>
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
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Sidebar toggle on mobile
        document.getElementById('toggleSidebar').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('open');
        });
        // Production Output Chart
        new Chart(document.getElementById('outputChart'), {
            type: 'line', data: {
                labels: ['Jan','Feb','Mar','Apr','May','Jun'],
                datasets: [{ label:'Units', data:[4300,3200,4700,4500,6000,5800], borderColor:'#007bff', tension:0.3 }]
            }, options:{ scales:{ y:{ beginAtZero:true } } }
        });
        // Raw Material Usage Chart
        new Chart(document.getElementById('usageChart'), {
            type: 'bar', data: {
                labels:['Copper','PVC','Aluminum','Rubber'],
                datasets:[{ label:'Usage (tons)', data:[32,45,15,8], backgroundColor:'#007bff' }]
            }, options:{ scales:{ y:{ beginAtZero:true } } }
        });
    </script>
</body>
</html>
