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
        .accountingTab {
            padding: 10px 20px;
            cursor: pointer;
            border: none;
            background: none;
            font-size: 16px;
        }
        .accountingTab.active {
            border-bottom: 3px solid #007bff;
            font-weight: bold;
            color: #007bff;
        }
        .accounting-tab-content {
            display: none;
            padding: 20px 0;
        }
        .accounting-tab-content.active {
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
        <h1>Accounting Dashboard</h1>
        <p>Monitor financial health and transactions</p>
        
        <!-- Cards -->
        <div class="row">
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
                <div class="card-body">
                    <h6 class="text-muted">Monthly Revenue</h6>
                    <h3 class="fw-bold">₹4,80,000</h3> <!-- Dynamic data -->
                    <p class="text-success">+6.5% vs last month</p> <!-- Dynamic data -->
                </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
                <div class="card-body">
                    <h6 class="text-muted">Monthly Expenses</h6>
                    <h3 class="fw-bold">₹4,25,000</h3> <!-- Dynamic data -->
                    <p class="text-danger">3.2% vs last month</p> <!-- Dynamic data --> <!-- Dynamic data -->
                </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
                <div class="card-body">
                    <h6 class="text-muted">Net Profit</h6>
                    <h3 class="fw-bold">₹55,000</h3> <!-- Dynamic data --> <!-- Dynamic data -->
                    <p class="text-success">+12.8% vs last month</p> <!-- Dynamic data --> <!-- Dynamic data -->
                </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
                <div class="card-body">
                    <h6 class="text-muted">Profit Margin</h6>
                    <h3 class="fw-bold">11.5%</h3> <!-- Dynamic data -->
                    <p class="text-danger">0.7% vs last month</p> <!-- Dynamic data -->
                </div>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="row justify-content-center">
            <div class="col-md-3 col-sm-6 mb-4">
                <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-clipboard"></i> Financial Reports</button>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-circle-dollar-to-slot"></i> Record Transaction</button>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-wallet"></i> Manage Accounts</button>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-download"></i> Export Data</button>
            </div>
        </div>

        <!-- Charts -->
        <div class="chart-container">
            <div class="chart-box">
                <h3>Revenue vs Expenses</h3>
            </div>
            <div class="chart-box">
                <h3>Profit Margin Trend</h3>
                <canvas id="lineChart"></canvas>
            </div>
        </div>

        <!-- Tabels -->
        <div class="col-md-12 card p-3 shadow-sm my-4 table-responsive table-responsive">

            <div class="tabs">
                <button class="accountingTab active" onclick="showaccountingTab('transaction')">Transaction</button>
                <button class="accountingTab" onclick="showaccountingTab('accounts')">Accounts</button>
                <button class="accountingTab" onclick="showaccountingTab('tax')">Tax Information</button>
            </div>

            <!-- Transaction -->
            <div id="transaction" class="accounting-tab-content active">
                <div class="container-fluid d-flex justify-content-between align-items-center">

                    <div class="justify-contnt-start">
                        <h1>Recent Transactions</h1>
                    </div>
                
                    <div class="d-flex justify-content-end">
                        <div class="input-group w-100 me-2">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-start-0" placeholder="Search..." />
                        </div>
                        <button class="btn btn-outline-primary me-2"><i class="fa-solid fa-filter"></i></button>
                        <button class="btn btn-outline-primary"><i class="fa-regular fa-calendar"></i></button>
                    </div>

                </div>
                <table id="Table" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>TRX-001</td> <!-- Dynamic data -->
                            <td>12 Apr, 2025</td> <!-- Dynamic data -->
                            <td>Supplier Payment - Havells</td> <!-- Dynamic data -->
                            <td>Expense</td> <!-- Dynamic data -->
                            <td>₹45,600</td> <!-- Dynamic data -->
                            <td>Completed</td> <!-- Dynamic data -->
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Accounts -->
            <div id="accounts" class="accounting-tab-content">
                <div class="container-fluid d-flex justify-content-between align-items-center">

                    <div class="justify-content-start">
                        <h1>Account Balances</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12 my-4">
                        <div class="card stat-card cards shadow-sm" style="background-color:rgb(147, 212, 250);">
                            <div class="card-body">
                                <h5 class="text-muted">Main Business Account</h5>
                                <h4>₹3,24,560</h4> <!-- Dynamic data -->
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 my-4">
                        <div class="card stat-card cards shadow-sm" style="background-color:rgb(212, 255, 233);">
                            <div class="card-body">
                                <h5 class="text-muted">Savings Account</h5>
                                <h4>₹1,85,200</h4> <!-- Dynamic data -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tax -->
            <div id="tax" class="accounting-tab-content">
                <div class="container-fluid d-flex justify-content-between align-items-center">

                    <div class="justify-content-start">
                        <h1>Tax Information</h1>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="d-flex align-items-start border rounded mb-3 p-3" style="background-color:rgb(177, 202, 253);">
                            
                             <div>
                                <h6 class="mb-1 fw-bold text-primary">GST Information</h6>
                                <div class="d-flex" style="gap: 80px;">
                                    <div style="flex: 1;">
                                        <h6>GSTIN</h6>
                                        <p>27AABCU9603R1ZX</p>
                                    </div>
                                    <div style="flex: 1;">
                                        <h6>Next Filing Due</h6>
                                        <p>20 Apr, 2025</p> <!-- Dynamic data -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-start rounded mb-2 p-3" style="background-color:rgb(233, 221, 251);">
                            
                            <div>
                                <h6 class="mb-1 fw-bold" style="color: #6f42c1;">TDS Information</h6>
                                <div class="d-flex" style="gap: 80px;">
                                    <div style="flex: 1;">
                                        <h6>PAN</h6>
                                        <p>AABCU9603R</p>
                                    </div>
                                    <div style="flex: 1;">
                                        <h6>TDS Deducted YTD</h6>
                                        <p>₹18,450</p> <!-- Dynamic data -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    </div>

    <script>
        // Line Chart
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue',
                data: [10.5, 16.5, 14.6, 11.6, 12.2, 11.5],
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
                    stepSize: 5
                }
                }
            }
            }
        });

        function showaccountingTab(id) {
            const tabs = document.querySelectorAll('.accountingTab');
            const contents = document.querySelectorAll('.accounting-tab-content');

            tabs.forEach(tab => tab.classList.remove('active'));
            contents.forEach(content => content.classList.remove('active'));

            document.querySelector(`#${id}`).classList.add('active');
            document.querySelector(`[onclick="showaccountingTab('${id}')"]`).classList.add('active');
        }
    </script>

</body>
</html>