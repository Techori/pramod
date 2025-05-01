
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

        <h1>Expenses Dashboard</h1>
        <p>Track and manage all business expenses</p>
        
        <!-- Cards -->
        <div class="row">
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
                <div class="card-body">
                    <h6 class="text-muted">Monthly Expenses</h6>
                    <h3 class="fw-bold">₹1,21,700</h3> <!-- Dynamic data -->
                    <p class="text-danger">8.5% vs last month</p> <!-- Dynamic data -->
                </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
                <div class="card-body">
                    <h6 class="text-muted">YTD Expenses</h6>
                    <h3 class="fw-bold">₹8,45,200</h3> <!-- Dynamic data -->
                    <p class="text-danger">12.2% vs last month</p> <!-- Dynamic data -->
                </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
                <div class="card-body">
                    <h6 class="text-muted">Pending Approvals</h6>
                    <h3 class="fw-bold">8</h3> <!-- Dynamic data -->
                </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
                <div class="card-body">
                    <h6 class="text-muted">Monthly Savings</h6>
                    <h3 class="fw-bold">₹12,500</h3> <!-- Dynamic data -->
                    <p class="text-success">+3.8% vs last month</p> <!-- Dynamic data --> <!-- Dynamic data -->
                </div>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="row justify-content-center">
            <div class="col-md-3 col-sm-6 mb-4">
                <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-plus"></i> Add New Expenses</button>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-regular fa-file-word"></i> Scan Receipt</button>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-file-lines"></i> Generate Report</button>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-chart-column"></i> View Analysis</button>
            </div>
        </div>

        <!-- Charts -->
        <div class="chart-container">
            <div class="chart-box">
                <h3>Stock Value by Category</h3>
                <div style="position: relative; width: 100%; max-width: 300px; margin: 0 auto;">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
            <div class="chart-box">
                <h3>Stock Value Trend (Last 6 months)</h3>
                <canvas id="barChart"></canvas>
            </div>
        </div>

        <!-- Table -->
        <div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">

            <div id="expenses">
                <div class="container-fluid d-flex justify-content-between align-items-center">

                    <div class="justify-contnt-start">
                        <h1>Recent Expenses</h1>
                    </div>
                
                    <div class="justify-content-end">
                        <button class="btn btn-outline-primary">View All</button>
                    </div>

                </div>
                <table id="Table" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Vendor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>EXP-2025-001</td> <!-- Dynamic data -->
                            <td>08 Apr, 2025</td> <!-- Dynamic data -->
                            <td>Raw Materials</td> <!-- Dynamic data -->
                            <td>₹15,800</td> <!-- Dynamic data -->
                            <td>Copper Supplies Ltd.</td> <!-- Dynamic data -->
                            <td>Approved</td> <!-- Dynamic data -->
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    <script>

        // Pie Chart
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
            labels: ['Raw Materials', 'Utilities', 'Salaries', 'Transport', 'Office', 'Others'],
            datasets: [{
                data: [32, 10, 35, 13, 6, 4],
                backgroundColor: [
                '#0d6efd',  // Blue (Raw Materials)
                '#20c997',  // Green (Utilities)
                '#ffc107',  // Orange (Salaries)
                '#fd7e14',  // Orange-dark (Transport)
                '#6f42c1',   // Violet (Office)
                '#C66EF9'   // Purple (Others)
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

        // Bar Chart
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Revenue',
                    data: [72500, 68000, 71000, 83000, 78000, 74000],
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
                            stepSize: 25000
                        }
                    }
                }
            }
        });
    </script>

</body>
</html>