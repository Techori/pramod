<?php
session_start();
if (!(isset($_SESSION["uid"]) && isset($_SESSION["user_type"]) && isset($_SESSION["session_id"]))) {
    header("location:../../login.php");
    exit;
} else {
    if (in_array($_SESSION["user_type"], ['Factory', 'Admin', 'Vendor'])) {
        header("location:../index.php");
        exit;

    } else if (!($_SESSION["user_type"] == 'Store')) {
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
    <title>Shree Unnati Wires & Traders - Premium Wire Manufacturing</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="unnati">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
</head>

<body class="bg-secondary bg-opacity-10">
    <?php
    include('./_retail_nav.php');
    ?>

    <div class="main-content">
        <h1>Retail Store Dashboard</h1>
        <p>Manage store operations, sales, and inventory</p>


        <!-- Cards  Row 1-->
        <div class="row">
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
                    <div class="card-body">
                        <h6 class="text-muted">Store Visitors</h6>
                        <h3 class="fw-bold">85</h3> <!-- Dynamic Data -->
                        <p class="text-success">+5.2% vs last month</p> <!-- Dynamic Data -->
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
                    <div class="card-body">
                        <h6 class="text-muted">Pending Orders</h6>
                        <h3 class="fw-bold">12</h3> <!-- Dynamic Data -->
                        <p class="text-danger">3 vs last month</p> <!-- Dynamic Data -->
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
                    <div class="card-body">
                        <h6 class="text-muted">Average Basket</h6>
                        <h3 class="fw-bold">₹2,450</h3> <!-- Dynamic Data -->
                        <p class="text-success">+8.3% vs last month</p> <!-- Dynamic Data -->
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
                        <h3 class="fw-bold">₹42,500</h3> <!-- Dynamic Data -->
                        <p class="text-success">+12.5% vs last month</p> <!-- Dynamic Data -->
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
                    <div class="card-body">
                        <h6 class="text-muted">Week Sales</h6>
                        <h3 class="fw-bold">₹3,45,230</h3> <!-- Dynamic Data -->
                        <p class="text-success">+3.2% vs last month</p> <!-- Dynamic Data -->
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
                    <div class="card-body">
                        <h6 class="text-muted">Month Sales</h6>
                        <h3 class="fw-bold">₹12,45,250</h3> <!-- Dynamic Data -->
                        <p class="text-success">+1.7% vs last month</p> <!-- Dynamic Data -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Bar chart & alerts -->
        <div class="row mb-4"
            style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);">
            <div class="col-md-8 col-sm-6 mb-4">
                <h3>Sales Performance (Last 7 Days)</h3>
                <p>Daily revenue breakdown</p>
                <canvas id="barChart"></canvas>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
                <h3>Alerts & Notifications</h3>
                <div class="card stat-card shadow-sm mb-2"
                    style="background-color:rgb(251, 243, 215); transition: transform 0.3s ease, box-shadow 0.3s ease; cursor: pointer;">
                    <div class="card-body">
                        <h5 class="text-muted">Low Stock Alert</h5>
                        <p>5 products below reorder level</p>
                        <a href="@" style="text-decoration: none;" class="text-dark">View Items</a>
                    </div>
                </div>
                <div class="card stat-card shadow-sm mb-2"
                    style="background-color:rgb(212, 255, 233); transition: transform 0.3s ease, box-shadow 0.3s ease; cursor: pointer;">
                    <div class="card-body">
                        <h5 class="text-muted">Supply Arrival</h5>
                        <p>New inventory arriving today</p>
                        <a href="@" style="text-decoration: none;" class="text-dark">Track Delivery</a>
                    </div>
                </div>
                <div class="card stat-card shadow-sm mb-2"
                    style="background-color:rgb(192, 214, 247); transition: transform 0.3s ease, box-shadow 0.3s ease; cursor: pointer;">
                    <div class="card-body">
                        <h5 class="text-muted">Customer Feedback</h5>
                        <p>3 new customer reviews received</p>
                        <a href="@" style="text-decoration: none;" class="text-dark">View Reviews</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- PPie charts -->
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

        <!-- Inventory Status bars -->
        <div class="card mb-4">
            <div class="alert-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Inventory Status</h5>
                    <button class="btn btn-outline-primary btn-sm">View All</button>
                </div>

                <!-- Havells Wires -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="stock-label">Havells Wires (1.5 mm)</span>
                        <span class="text-danger stock-count">12 units left</span> <!-- Dynamic data -->
                    </div>
                    <div class="progress bg-light">
                        <div class="progress-bar bg-primary" style="width: 25%"></div> <!-- Dynamic data -->
                        <div class="progress-bar bg-danger" style="width: 75%"></div> <!-- Dynamic data -->
                    </div>
                </div>

                <!-- LED Panels -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="stock-label">LED Panels (18W)</span>
                        <span class="text-warning stock-count">24 units left</span> <!-- Dynamic data -->
                    </div>
                    <div class="progress bg-light">
                        <div class="progress-bar bg-primary" style="width: 60%"></div> <!-- Dynamic data -->
                        <div class="progress-bar bg-warning" style="width: 40%"></div> <!-- Dynamic data -->
                    </div>
                </div>

                <!-- Ceiling Fan -->
                <div>
                    <div class="d-flex justify-content-between">
                        <span class="stock-label">Ceiling Fans (Standard)</span>
                        <span class="text-warning stock-count">65 units left</span> <!-- Dynamic data -->
                    </div>
                    <div class="progress bg-light">
                        <div class="progress-bar bg-primary" style="width: 70%"></div> <!-- Dynamic data -->
                        <div class="progress-bar bg-warning" style="width: 30%"></div> <!-- Dynamic data -->
                    </div>
                </div>

                <!-- Switches -->
                <div>
                    <div class="d-flex justify-content-between">
                        <span class="stock-label">Switches (Modular)</span>
                        <span class="text-warning stock-count">150 units left</span> <!-- Dynamic data -->
                    </div>
                    <div class="progress bg-light">
                        <div class="progress-bar bg-primary" style="width: 90%"></div> <!-- Dynamic data -->
                        <div class="progress-bar bg-warning" style="width: 10%"></div> <!-- Dynamic data -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access -->
        <div class="row justify-content-center p-2 bg-body rounded-3 mb-4 m-2"
            style="box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);">
            <h5>Quick Access</h5>
            <div class="col-md-2 col-sm-6 mb-4">
                <button type="button" class="btn btn-outline-primary btn-lg w-100"><i
                        class="fa-regular fa-file-lines"></i> Create Invoice</button>
            </div>
            <div class="col-md-2 col-sm-6 mb-4">
                <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-box"></i> Check
                    Inventory</button>
            </div>
            <div class="col-md-2 col-sm-6 mb-4">
                <button type="button" class="btn btn-outline-primary btn-lg w-100"><i
                        class="fa-solid fa-truck-fast"></i> Process Returns</button>
            </div>
            <div class="col-md-2 col-sm-6 mb-4">
                <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-regular fa-user"></i>
                    Add Customers</button>
            </div>
            <div class="col-md-2 col-sm-6 mb-4">
                <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-regular fa-file"></i>
                    View Reports</button>
            </div>
            <div class="col-md-2 col-sm-6 mb-4">
                <button type="button" class="btn btn-outline-primary btn-lg w-100"><i
                        class="fa-solid fa-credit-card"></i> Process Payments</button>
            </div>
        </div>

        <!-- Recent sales table -->
        <div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">

            <div id="facrtory">
                <div class="container-fluid d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Inventory Status</h5>
                    <button class="btn btn-outline-primary btn-sm">View All</button>

                </div>
                <table id="Table" class="table table-bordered table-hover">
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
                            <td>INV-2025-845</td> <!-- Dynamic data -->
                            <td>Raj Kumar</td> <!-- Dynamic data -->
                            <td>10:45 AM</td> <!-- Dynamic data -->
                            <td>₹4,250</td> <!-- Dynamic data -->
                            <td>UPI</td> <!-- Dynamic data -->
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    <script>
        // Bar Chart
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Revenue',
                    data: [28500, 22000, 31000, 26000, 34000, 42000, 31500],
                    backgroundColor: '#0d6efd',
                    borderColor: '#0d6efd',
                    borderWidth: 1,
                    borderRadius: 6,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 15000
                        }
                    }
                }
            }
        });

        // Product Chart
        const productCtx = document.getElementById('productChart').getContext('2d');
        new Chart(productCtx, {
            type: 'pie',
            data: {
                labels: ['Wires', 'Switches', 'Lights', 'Fans', 'Others'],
                datasets: [{
                    data: [35, 25, 20, 15, 5],
                    backgroundColor: [
                        '#0d6efd',  // Blue (Wires)
                        '#20c997',  // Green (Switches)
                        '#ffc107',  // Orange (Lights)
                        '#fd7e14',  // Orange-dark (Fans)
                        '#C84CE4'   // Purple (Others),
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#333',
                            font: { size: 14 }
                        }
                    }
                }
            }
        });

        // Payment Chart
        const paymentCtx = document.getElementById('paymentChart').getContext('2d');
        new Chart(paymentCtx, {
            type: 'pie',
            data: {
                labels: ['Cash', 'UPI', 'Card', 'Credit'],
                datasets: [{
                    data: [45, 35, 15, 5],
                    backgroundColor: [
                        '#0d6efd',  // Blue (Cash)
                        '#20c997',  // Green (UPI)
                        '#ffc107',  // Orange (Card)
                        '#fd7e14',  // Orange-dark (Credit)
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#333',
                            font: { size: 14 }
                        }
                    }
                }
            }
        });

    </script>
</body>

</html>