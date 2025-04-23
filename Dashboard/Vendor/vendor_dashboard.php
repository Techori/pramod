<?php
session_start();
if (!(isset($_SESSION["uid"]) && isset($_SESSION["user_type"]) && isset($_SESSION["session_id"]))) {
    header("location:../../login.php");
    exit;
} else {
    if (in_array($_SESSION["user_type"] ,  ['Admin','Store','Factory'])) {
        header("location:../index.php");
        exit;

    } else if (!($_SESSION["user_type"] == 'Vendor')) {
        header("location:../../login.php");
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Dashboard - Shree Unnati Wires & Traders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: sans-serif; margin: 0; padding: 0; box-sizing: border-box; color: #333; }
        .sidebar { width: 200px; height: 100vh; position: fixed; top: 0; left: 0; background-color: #2c2f3e; color: #fff; padding-top: 20px; overflow-y: auto; }
        .sidebar .logo { font-size: 1.2rem; font-weight: bold; text-align: center; padding: 10px; background: #1a1d2a; }
        .sidebar nav a { display: flex; align-items: center; padding: 10px; color: #ccc; text-decoration: none; cursor: pointer; }
        .sidebar nav a i { width: 24px; }
        .sidebar nav a span { margin-left: 10px; }
        .sidebar nav a.active, .sidebar nav a:hover { background-color: #b0e0e6; color: #000; }
        .sidebar .footer { position: absolute; bottom: 0; width: 100%; padding: 10px; font-size: .8rem; background: #1a1d2a; text-align: center; }
        main { margin-left: 200px; padding: 20px; }
        header.header { margin-left: 200px; background: #fff; border-bottom: 1px solid #ddd; padding: 10px 20px; position: sticky; top: 0; z-index: 100; }
        .card { border: 1px solid #ddd; border-radius: 0.25rem; }
        .card-body { padding: 1.5rem; }
        .alert { border-radius: 0.25rem; }
        .badge { padding: 0.4rem 0.8rem; border-radius: 0.25rem; }
        table th, table td { padding: 0.8rem; border-bottom: 1px solid #ddd; }
        table th { background-color: #f8f9fa; }
        .quick-access .card { min-height: 100px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background-color 0.3s; }
        .quick-access .card:hover { background-color: #b0e0e6; }
        @media (max-width: 768px) {
            .sidebar { width: 200px; }
            main { margin-left: 200px; }
            header.header { margin-left: 200px; }
            .container-fluid { padding-left: 10px; padding-right: 10px; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">Vendor Dashboard</div>
        <nav class="nav flex-column mt-2">
            <a class="nav-link active" data-section="dashboard"><i class="bi bi-grid"></i><span>Dashboard</span></a>
            <a class="nav-link" data-section="orders"><i class="bi bi-cart"></i><span>Orders</span></a>
            <a class="nav-link" data-section="deliveries"><i class="bi bi-truck"></i><span>Deliveries</span></a>
            <a class="nav-link" data-section="products"><i class="bi bi-box"></i><span>Products</span></a>
            <a class="nav-link" data-section="payments"><i class="bi bi-wallet"></i><span>Payments</span></a>
            <a class="nav-link" data-section="invoices"><i class="bi bi-receipt"></i><span>Invoices</span></a>
            <a class="nav-link" data-section="reports"><i class="bi bi-bar-chart"></i><span>Reports</span></a>
            <a class="nav-link" data-section="settings"><i class="bi bi-gear"></i><span>Settings</span></a>
        </nav>
        <div class="footer">© 2025 Unnati Traders</div>
    </div>

    <!-- Header -->
    <header class="header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <h5 class="mb-0">Vendor Dashboard</h5>
        </div>
        <form class="d-flex" role="search">
            <input class="form-control form-control-sm me-2" type="search" placeholder="Search orders, products, or invoices..." aria-label="Search">
            <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-search"></i></button>
        </form>
        <div class="d-flex align-items-center">
            <button class="btn btn-sm btn-outline-primary me-2"><i class="bi bi-bell"></i></button>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-person-circle"></i></button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">Update Profile</a></li>
                    <li>
                        <form action="../../logout.php" method="POST">
                            <button name="logout_btn" class="btn" type="submit" value="true">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main id="main-content">
        <div class="container-fluid">
            <h4>Vendor Dashboard Overview</h4>
            <p>Monitor your business performance with key metrics and quick actions.</p>

            <!-- Metrics Row -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card p-3 border-start border-3 border-primary">
                        <small>Active Orders</small>
                        <h3>16 orders</h3>
                        <small class="text-success">+3 vs last month</small>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card p-3 border-start border-3 border-warning">
                        <small>Pending Deliveries</small>
                        <h3>8 deliveries</h3>
                        <small class="text-warning">+2 vs last month</small>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card p-3 border-start border-3 border-danger">
                        <small>Pending Payments</small>
                        <h3>₹2,85,450</h3>
                        <small class="text-danger">+12.5% vs last month</small>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card p-3 border-start border-3 border-success">
                        <small>This Month Revenue</small>
                        <h3>₹4,35,250</h3>
                        <small class="text-success">+8.7% vs last month</small>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5>Quick Actions</h5>
                    <div class="row g-3 mt-3 quick-access">
                        <div class="col-4 col-md-2">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="bi bi-cart fs-4"></i>
                                    <p class="mb-0">Add Invoice</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 col-md-2">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="bi bi-box fs-4"></i>
                                    <p class="mb-0">Check Stock</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 col-md-2">
                            <div class="card text-center">
                                <div class="card-body">
                                    <i class="bi bi-wallet fs-4"></i>
                                    <p class="mb-0">View Expenses</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Alerts -->
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="card p-3">
                        <h5>Order Trends (Last 6 Months)</h5>
                        <canvas id="orderTrendsChart" height="200"></canvas>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card p-3">
                        <h5>Recent Activity</h5>
                        <div class="alert alert-primary">
                            <i class="bi bi-bell"></i> New Order #ORD-2854 received
                            <a href="#" class="alert-link">View Details</a>
                        </div>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> Payment overdue for #INV-3845
                            <a href="#" class="alert-link">Send Reminder</a>
                        </div>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> Delivery #DEL-482 completed
                            <a href="#" class="alert-link">View Status</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="vendor_dashboard_sidebar_pages.js"> //Sidebar pages </script>
    <script>
        // Initialize Chart.js for sections with charts
        function initializeCharts() {
            const orderTrendsChart = document.getElementById('orderTrendsChart');
            if (orderTrendsChart) {
                new Chart(orderTrendsChart, {
                    type: 'bar',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Orders',
                            data: [10, 15, 7, 12, 9, 6],
                            backgroundColor: '#007bff'
                        }]
                    },
                    options: { scales: { y: { beginAtZero: true } } }
                });
            }

            const salesChart = document.getElementById('salesChart');
            if (salesChart) {
                new Chart(salesChart, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Sales (₹)',
                            data: [50000, 75000, 60000, 80000, 70000, 90000],
                            borderColor: '#007bff',
                            tension: 0.3
                        }]
                    },
                    options: { scales: { y: { beginAtZero: true } } }
                });
            }
        }

        // Handle sidebar link clicks
        document.querySelectorAll('.sidebar nav a').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const section = link.getAttribute('data-section');
                
                // Update active link
                document.querySelectorAll('.sidebar nav a').forEach(l => l.classList.remove('active'));
                link.classList.add('active');

                // Update main content
                const mainContent = document.getElementById('main-content');
                mainContent.innerHTML = contentTemplates[section]();

                // Reinitialize charts if needed
                initializeCharts();
            });
        });

        // Initialize charts on page load
        initializeCharts();
    </script>
</body>
</html>