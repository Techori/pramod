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

    th,
    td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
    }
</style>

<h1>Factory Stock Dashboard</h1>
<p>Monitor and manage production inventory</p>

<!-- Cards -->
<div class="row">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
            <div class="card-body">
                <h6 class="text-muted">Total Stock Value</h6>
                <h3 class="fw-bold">₹7,78,000</h3> <!-- Dynamic data -->
                <p class="text-success">+5.2% vs last month</p> <!-- Dynamic data -->
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
            <div class="card-body">
                <h6 class="text-muted">Low Stock Items</h6>
                <h3 class="fw-bold">12</h3> <!-- Dynamic data -->
                <p class="text-danger">3 vs last month</p> <!-- Dynamic data -->
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
            <div class="card-body">
                <h6 class="text-muted">Monthly Production</h6>
                <h3 class="fw-bold">1,250 units</h3> <!-- Dynamic data -->
                <p class="text-success">+8.5% vs last month</p> <!-- Dynamic data -->
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
            <div class="card-body">
                <h6 class="text-muted">Pending Orders</h6>
                <h3 class="fw-bold">18</h3> <!-- Dynamic data -->
            </div>
        </div>
    </div>
</div>

<!-- Buttons -->
<div class="row justify-content-center">
    <div class="col-md-3 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100" data-bs-toggle="modal"
            data-bs-target="#addStock"><i class="fa-solid fa-plus"></i> Add Stock Entry</button>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100" data-bs-toggle="modal"
            data-bs-target="#stockTransfer"><i class="fa-solid fa-arrow-trend-up"></i> Stock Transfer</button>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-file-lines"></i> Stock
            Report</button>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-clipboard"></i> Stock
            Count</button>
    </div>
</div>

<!-- Add Stock form -->
<div class="modal fade" id="addStock" tabindex="-1" aria-labelledby="addStockLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h5 class="modal-title" id="addStockLabel">Add Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="id" class="form-label">Id</label>
                        <input type="text" class="form-control" id="id">
                    </div>

                    <div class="mb-3">
                        <label for="itemName" class="form-label">Item Name</label>
                        <input type="text" class="form-control" id="itemName">
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" class="form-control" id="category">
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="text" class="form-control" id="quantity">
                    </div>

                    <div class="mb-3">
                        <label for="value" class="form-label">Value</label>
                        <input type="text" class="form-control" id="value">
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <input type="text" class="form-control" id="status">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Transfer Stock form -->
<div class="modal fade" id="stockTransfer" tabindex="-1" aria-labelledby="stockTransferLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h5 class="modal-title" id="stockTransferLabel">Stock Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="id" class="form-label">Id</label>
                        <input type="text" class="form-control" id="id">
                    </div>

                    <div class="mb-3">
                        <label for="transfer" class="form-label">Transfer to</label>
                        <input type="text" class="form-control" id="transfer">
                    </div>

                    <div class="mb-3">
                        <label for="itemName" class="form-label">Item Name</label>
                        <input type="text" class="form-control" id="itemName">
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" class="form-control" id="category">
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="text" class="form-control" id="quantity">
                    </div>

                    <div class="mb-3">
                        <label for="value" class="form-label">Value</label>
                        <input type="text" class="form-control" id="value">
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <input type="text" class="form-control" id="status">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Transfer Stock</button>
                </div>
            </form>
        </div>
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
        <canvas id="lineChart"></canvas>
    </div>
</div>

<!-- Table -->
<div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">

    <div id="facrtory">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Current Stock</h1>
            </div>

            <div class="justify-content-end">
                <button class="btn btn-outline-primary">View All</button>
            </div>

        </div>
        <table id="Table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Value</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>PRD001</td> <!-- Dynamic data -->
                    <td>Copper Wire 1.5mm</td> <!-- Dynamic data -->
                    <td>Raw Materials</td> <!-- Dynamic data -->
                    <td>450 kg</td> <!-- Dynamic data -->
                    <td>₹1,15,200</td> <!-- Dynamic data -->
                    <td>In Stock</td> <!-- Dynamic data -->
                </tr>
            </tbody>
        </table>
    </div>

    <script>

        // Pie Chart
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: ['Raw Materials', 'Components', 'Finished Goods', 'Packaging', 'Spare Parts'],
                datasets: [{
                    data: [30, 16, 44, 5, 4],
                    backgroundColor: [
                        '#0d6efd',  // Blue (Raw Materials)
                        '#20c997',  // Green (Components)
                        '#ffc107',  // Orange (Finished Goods)
                        '#fd7e14',  // Orange-dark (Packaging)
                        '#6f42c1'   // Violet (Spare Parts)
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
                    data: [620000, 680000, 740000, 780000, 820000, 778000],
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
                            stepSize: 250000
                        }
                    }
                }
            }
        });
    </script>

    </body>

    </html>