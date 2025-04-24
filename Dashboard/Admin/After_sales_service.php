<?php
session_start();
if (!(isset($_SESSION["uid"]) && isset($_SESSION["user_type"]) && isset($_SESSION["session_id"]))) {
    header("location:../../login.php");
    exit;
} else {
    if (in_array($_SESSION["user_type"] ,  ['Factory','Store','Vendor'])) {
        header("location:../index.php");
        exit;

    } else if (!($_SESSION["user_type"] == 'Admin')) {
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="unnati">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Shree Unnati Wires & Traders - Premium Wire Manufacturing</title>
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

        .tabs {
            display: flex;
            border-bottom: 2px solid #ddd;
        }
        .afterSalesTab {
            padding: 10px 20px;
            cursor: pointer;
            border: none;
            background: none;
            font-size: 16px;
        }
        .afterSalesTab.active {
            border-bottom: 3px solid #007bff;
            font-weight: bold;
            color: #007bff;
        }
        .afterSales-tab-content {
            display: none;
            padding: 20px 0;
        }
        .afterSales-tab-content.active {
            display: block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

    </style>
</head>
<body class="bg-secondary bg-opacity-10">
    <?php
        include('./_admin_nav.php');
    ?>

    <div class="main-content">
        <h1>After-Sales Service</h1>
        <p>Manage customer service and support tickets</p>

        <!-- Cards -->
        <div class="row">
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
                <div class="card-body">
                    <h6 class="text-muted">Open Tickets</h6>
                    <h3 class="fw-bold">12</h3> <!-- Dynamic data -->
                    <p class="text-danger">2 vs last month</p> <!-- Dynamic data -->
                </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
                <div class="card-body">
                    <h6 class="text-muted">Avg. Resolution Time</h6>
                    <h3 class="fw-bold">28 hours</h3> <!-- Dynamic data -->
                    <p class="text-success">+4h vs last month</p> <!-- Dynamic data -->
                </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
                <div class="card-body">
                    <h6 class="text-muted">Resolved This Week</h6>
                    <h3 class="fw-bold">24</h3> <!-- Dynamic data -->
                    <p class="text-success">+12.8% vs last month</p> <!-- Dynamic data -->
                </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
                <div class="card-body">
                    <h6 class="text-muted">Customer Satisfaction</h6>
                    <h3 class="fw-bold">92%</h3> <!-- Dynamic data -->
                    <p class="text-success">+3.7% vs last month</p> <!-- Dynamic data --> <!-- Dynamic data --> <!-- Dynamic data -->
                </div>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="row justify-content-center">
            <div class="col-md-3 col-sm-6 mb-4">
                <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-regular fa-calendar-check"></i> Create Service Ticket</button>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-user-plus"></i> Asign Technician</button>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-wrench"></i> Manage Parts</button>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-file-lines"></i> Service Reports</button>
            </div>
        </div>

        <!-- Charts -->
        <div class="chart-container">
            <div class="chart-box">
                <h3>Sales by Category</h3>
                <div style="position: relative; width: 100%; max-width: 300px; margin: 0 auto;">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
            <div class="chart-box">
                <h3>Daily Sales (Last Week)</h3>
                <canvas id="lineChart"></canvas>
            </div>
        </div>

        <!-- Tabels -->
        <div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">

            <div class="tabs">
                <button class="afterSalesTab active" onclick="showAfterSalesTab('allTickets')">All Tickets</button>
                <button class="afterSalesTab" onclick="showAfterSalesTab('open')">Open</button>
                <button class="afterSalesTab" onclick="showAfterSalesTab('inProgress')">In Progress</button>
                <button class="afterSalesTab" onclick="showAfterSalesTab('resolved')">Resolved</button>
            </div>

            <!-- All Tickets -->
            <div id="allTickets" class="afterSales-tab-content active">
                <div class="container-fluid d-flex justify-content-between align-items-center">
                
                    <div class="d-flex justify-content-start">
                        <div class="input-group w-100 me-2">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-start-0" placeholder="Search..." />
                        </div>
                        <button class="btn btn-outline-primary me-2"><i class="fa-solid fa-filter"></i></button>
                        <button class="btn btn-outline-primary me-2"><i class="fa-solid fa-plus"></i> New Ticket</button>
                    </div>

                </div>
                <table id="Table" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Issue</th>
                            <th>Product</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>SRV-001</td> <!-- Dynamic data --> <!-- Dynamic data -->
                            <td>Raj Electronics</td> <!-- Dynamic data --> <!-- Dynamic data -->
                            <td>08 Apr, 2025</td> <!-- Dynamic data --> <!-- Dynamic data -->
                            <td>Faulty LED Lights</td> <!-- Dynamic data --> <!-- Dynamic data -->
                            <td>Orient LED Panel 24W</td> <!-- Dynamic data --> <!-- Dynamic data -->
                            <td>High</td> <!-- Dynamic data --> <!-- Dynamic data -->
                            <td>Open</td> <!-- Dynamic data --> <!-- Dynamic data -->
                            <td><div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-wrench"></i></button> <!-- Edit ticket -->
                                <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-circle-check text-success"></i></button>

                            </div></td>
                        </tr>
                    </tbody>
                </table>
                
            </div>

            <!-- Open -->
            <div id="open" class="afterSales-tab-content">
                <div class="container-fluid d-flex justify-content-between align-items-center">
                
                    <div class="d-flex justify-content-start">
                        <div class="input-group w-100 me-2">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-start-0" placeholder="Search..." />
                        </div>
                        <button class="btn btn-outline-primary me-2"><i class="fa-solid fa-filter"></i></button>
                        <button class="btn btn-outline-primary me-2"><i class="fa-solid fa-plus"></i> New Ticket</button>
                    </div>

                </div>
                <table id="Table" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Issue</th>
                            <th>Product</th>
                            <th>Priority</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>SRV-001</td> <!-- Dynamic data -->
                            <td>Raj Electronics</td> <!-- Dynamic data -->
                            <td>08 Apr, 2025</td> <!-- Dynamic data -->
                            <td>Faulty LED Lights</td> <!-- Dynamic data -->
                            <td>Orient LED Panel 24W</td> <!-- Dynamic data -->
                            <td>High</td> <!-- Dynamic data -->
                            <td><div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-wrench"></i></button> <!-- Edit ticket -->
                                <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-circle-check text-success"></i></button>

                            </div></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- In Progress -->
            <div id="inProgress" class="afterSales-tab-content">
                <div class="container-fluid d-flex justify-content-between align-items-center">
                
                    <div class="d-flex justify-content-start">
                        <div class="input-group w-100 me-2">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-start-0" placeholder="Search..." />
                        </div>
                        <button class="btn btn-outline-primary me-2"><i class="fa-solid fa-filter"></i></button>
                        <button class="btn btn-outline-primary me-2"><i class="fa-solid fa-plus"></i> New Ticket</button>
                    </div>

                </div>
            </div>

            <!-- Resolved -->
            <div id="resolved" class="afterSales-tab-content">
                <div class="container-fluid d-flex justify-content-between align-items-center">
                
                    <div class="d-flex justify-content-start">
                        <div class="input-group w-100 me-2">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-start-0" placeholder="Search..." />
                        </div>
                        <button class="btn btn-outline-primary me-2"><i class="fa-solid fa-filter"></i></button> 
                        <button class="btn btn-outline-primary me-2"><i class="fa-solid fa-plus"></i> New Ticket</button>
                    </div>

                </div>
            </div>

    </div>

    <script>
         
        // Pie Chart
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
            labels: ['Open', 'In Progress', 'Resolved', 'Closed'],
            datasets: [{
                data: [19, 13, 39, 29],
                backgroundColor: [
                '#0d6efd',  // Blue (Open)
                '#20c997',  // Green (In Progress)
                '#ffc107',  // Orange (Resolved)
                '#fd7e14',  // Orange-dark (Closed)
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

         // Line Chart
         const lineCtx = document.getElementById('lineChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue',
                data: [48, 42, 36, 32, 28, 24],
                fill: false,
                borderColor: '#0d6efd',
                backgroundColor: '#0d6efd',
                tension: 0.3,
                pointRadius: 5,
                pointHoverRadius: 6
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
                    stepSize: 15
                }
                }
            }
            }
        });

        function showAfterSalesTab(id) {
            const tabs = document.querySelectorAll('.afterSalesTab');
            const contents = document.querySelectorAll('.afterSales-tab-content');

            tabs.forEach(tab => tab.classList.remove('active'));
            contents.forEach(content => content.classList.remove('active'));

            document.querySelector(`#${id}`).classList.add('active');
            document.querySelector(`[onclick="showAfterSalesTab('${id}')"]`).classList.add('active');
        }
    </script>

</body>
</html>