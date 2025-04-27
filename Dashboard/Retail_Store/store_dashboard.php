<?php
session_start();

// Authentication check
if (!(isset($_SESSION["uid"]) && isset($_SESSION["user_type"]) && isset($_SESSION["session_id"]))) {
    header("location:../../login.php");
    exit;
} else {
    if (in_array($_SESSION["user_type"], ['Factory', 'Admin', 'Vendor'])) {
        header("location:../index.php");
        exit;
    } else if ($_SESSION["user_type"] != 'Store') {
        header("location:../../login.php");
        exit;
    }
}

// Page routing
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$valid_pages = ['dashboard', 'billing', 'supply', 'inventory', 'customers', 'orders', 'payments', 'after_service', 'reports', 'settings'];
if (!in_array($page, $valid_pages)) {
    $page = 'dashboard';
}

// Include database connection
require_once 'database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retail Store Dashboard - Shree Unnati Wires & Traders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../public/css/styles.css">
</head>
<body>
    <?php include '_retail_nav.php'; ?>
    
    <!-- Main Content -->
    <main>
        <?php if ($page === 'dashboard'): ?>
            <div class="main-content">
                <h1>Retail Store Dashboard</h1>
                <p>Manage store operations, sales, and inventory</p>

                <!-- Cards Row 1 -->
                <div class="row">
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
                            <div class="card-body">
                                <h6 class="text-muted">Store Visitors</h6>
                                <h3 class="fw-bold">85</h3>
                                <p class="text-success">+5.2% vs last month</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
                            <div class="card-body">
                                <h6 class="text-muted">Pending Orders</h6>
                                <h3 class="fw-bold">12</h3>
                                <p class="text-danger">3 vs last month</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
                            <div class="card-body">
                                <h6 class="text-muted">Average Basket</h6>
                                <h3 class="fw-bold">₹2,450</h3>
                                <p class="text-success">+8.3% vs last month</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cards Row 2 -->
                <div class="row">
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
                            <div class="card-body">
                                <h6 class="text-muted">Today's Sales</h6>
                                <h3 class="fw-bold">₹42,500</h3>
                                <p class="text-success">+12.5% vs last month</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
                            <div class="card-body">
                                <h6 class="text-muted">Week Sales</h6>
                                <h3 class="fw-bold">₹3,45,230</h3>
                                <p class="text-success">+3.2% vs last month</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
                            <div class="card-body">
                                <h6 class="text-muted">Month Sales</h6>
                                <h3 class="fw-bold">₹12,45,250</h3>
                                <p class="text-success">+1.7% vs last month</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bar chart & alerts -->
                <div class="row mb-4" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);">
                    <div class="col-md-8 col-sm-12 mb-4">
                        <h3>Sales Performance (Last 7 Days)</h3>
                        <p>Daily revenue breakdown</p>
                        <canvas id="barChart"></canvas>
                    </div>
                    <div class="col-md-4 col-sm-12 mb-4">
                        <h3>Alerts & Notifications</h3>
                        <div class="card stat-card shadow-sm mb-2" style="background-color:rgb(251, 243, 215);">
                            <div class="card-body">
                                <h5 class="text-muted">Low Stock Alert</h5>
                                <p>5 products below reorder level</p>
                                <a href="?page=inventory" class="text-dark">View Items</a>
                            </div>
                        </div>
                        <div class="card stat-card shadow-sm mb-2" style="background-color:rgb(212, 255, 233);">
                            <div class="card-body">
                                <h5 class="text-muted">Supply Arrival</h5>
                                <p>New inventory arriving today</p>
                                <a href="?page=supply" class="text-dark">Track Delivery</a>
                            </div>
                        </div>
                        <div class="card stat-card shadow-sm mb-2" style="background-color:rgb(192, 214, 247);">
                            <div class="card-body">
                                <h5 class="text-muted">Customer Feedback</h5>
                                <p>3 new customer reviews received</p>
                                <a href="?page=customers" class="text-dark">View Reviews</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pie charts -->
                <div class="chart-container mb-4">
                    <div class="chart-box">
                        <h3>Monthly Revenue Trend</h3>
                        <div style="position: relative; width: 100%; max-width: 300px; margin: 0 auto;">
                            <canvas id="productChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-box">
                        <h3>Sales by Category</h3>
                        <div style="position: relative; width: 100%; max-width: 300px; margin: 0 auto;">
                            <canvas id="paymentChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Inventory Status -->
                <div class="card mb-4">
                    <div class="alert-card p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Inventory Status</h5>
                            <a href="?page=inventory" class="btn btn-outline-primary btn-sm">View All</a>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="stock-label">Havells Wires (1.5 mm)</span>
                                <span class="text-danger stock-count">12 units left</span>
                            </div>
                            <div class="progress bg-light">
                                <div class="progress-bar bg-primary" style="width: 25%"></div>
                                <div class="progress-bar bg-danger" style="width: 75%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="stock-label">LED Panels (18W)</span>
                                <span class="text-warning stock-count">24 units left</span>
                            </div>
                            <div class="progress bg-light">
                                <div class="progress-bar bg-primary" style="width: 60%"></div>
                                <div class="progress-bar bg-warning" style="width: 40%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Access -->
                <div class="row justify-content-center p-2 bg-body rounded-3 mb-4 m-2" style="box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);">
                    <h5>Quick Access</h5>
                    <div class="col-md-2 col-sm-6 mb-4">
                        <a href="?page=billing" class="btn btn-outline-primary btn-lg w-100"><i class="fa-regular fa-file-lines"></i> Create Invoice</a>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-4">
                        <a href="?page=inventory" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-box"></i> Check Inventory</a>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-4">
                        <a href="?page=aftersales" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-truck-fast"></i> Process Returns</a>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-4">
                        <a href="?page=customers" class="btn btn-outline-primary btn-lg w-100"><i class="fa-regular fa-user"></i> Add Customers</a>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-4">
                        <a href="?page=reports" class="btn btn-outline-primary btn-lg w-100"><i class="fa-regular fa-file"></i> View Reports</a>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-4">
                        <a href="?page=payments" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-credit-card"></i> Process Payments</a>
                    </div>
                </div>

                <!-- Recent Sales Table -->
                <div class="card p-3 shadow-sm my-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Recent Transactions</h5>
                        <a href="?page=billing" class="btn btn-outline-primary btn-sm">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Customer</th>
                                    <th>Time</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>INV-2025-845</td>
                                    <td>Raj Kumar</td>
                                    <td>10:45 AM</td>
                                    <td>₹4,250</td>
                                    <td>UPI</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php
            $page_file = $page . '.php';
            if (file_exists($page_file)) {
                include $page_file;
            } else {
                echo '<div class="container-fluid"><h1>Page Not Found</h1><p>The requested page is not available.</p></div>';
            }
            ?>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Chart.js Scripts for Dashboard
        <?php if ($page === 'dashboard'): ?>
            // Bar Chart
            const barChartCtx = document.getElementById('barChart').getContext('2d');
            new Chart(barChartCtx, {
                type: 'bar',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Daily Sales (₹)',
                        data: [35000, 42000, 38000, 45000, 40000, 48000, 43000],
                        backgroundColor: '#0d6efd',
                        borderColor: '#0d6efd',
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            // Product Chart (Pie)
            const productChartCtx = document.getElementById('productChart').getContext('2d');
            new Chart(productChartCtx, {
                type: 'pie',
                data: {
                    labels: ['Wires', 'Lighting', 'Fans', 'Switches'],
                    datasets: [{
                        data: [40, 25, 20, 15],
                        backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#6f42c1']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });

            // Payment Chart (Pie)
            const paymentChartCtx = document.getElementById('paymentChart').getContext('2d');
            new Chart(paymentChartCtx, {
                type: 'pie',
                data: {
                    labels: ['UPI', 'Cash', 'Card', 'Credit'],
                    datasets: [{
                        data: [50, 30, 15, 5],
                        backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#6f42c1']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        <?php endif; ?>

        // Sidebar Toggle
        const hamburger = document.getElementById('hamburger');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        hamburger.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        });

        document.querySelectorAll('.sidebar nav a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('show');
                }
            });
        });
    </script>
</body>
</html>