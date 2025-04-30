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

    .tabs {
        display: flex;
        border-bottom: 2px solid #ddd;
    }

    .retailStoreTab {
        padding: 10px 20px;
        cursor: pointer;
        border: none;
        background: none;
        font-size: 16px;
    }

    .retailStoreTab.active {
        border-bottom: 3px solid #007bff;
        font-weight: bold;
        color: #007bff;
    }

    .retailStore-tab-content {
        display: none;
        padding: 20px 0;
    }

    .retailStore-tab-content.active {
        display: block;
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
<div>
    <h1>Raw Materials Management
    </h1>
    <p>Monitor and manage factory raw materials inventory</p>
    <!-- Search bar & buttons -->
    <div class="container-fluid d-flex justify-content-between align-items-center mb-4">


        <div class="d-flex justify-content-start">
            <div class="input-group w-100 me-2">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control border-start-0" placeholder="Search..." />
            </div>
        </div>

        <div class="justify-contnt-end">
            <button class="btn btn-outline-primary"> Filters</button>
            <button class="btn btn-outline-primary"></i> Reports</button>
            <button class="btn btn-primary" type="submit">Add Materials</button>
        </div>
    </div>

    <!-- Cards -->
    <div class="row">
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
                <div class="card-body">
                    <h6 class="text-muted">Total Materials</h6>
                    <h3 class="fw-bold">38</h3>
                    <p class="text-success">+2</p><!-- Dynamic data -->
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
                <div class="card-body">
                    <h6 class="text-muted">Low Stock Items</h6>
                    <h3 class="fw-bold">7</h3>
                    <p class="text-success">+3</p> <!-- Dynamic data -->
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
                <div class="card-body">
                    <h6 class="text-muted"> Pending Orders</h6>
                    <h3 class="fw-bold">85</h3> <!-- Dynamic data -->
                    <p class="text-success">-12</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular products -->
    <div class="col-md-12 my-4">
        <div class="card p-3 shadow-sm">
            <h5 class="mb-4">
                <strong>Low Stock Alert</strong>
            </h5>
            <div class="row">
                <div class="col-md-4 col-sm-12 mb-2">
                    <div class="card stat-card cards shadow-sm" style="background-color:rgb(125, 206, 246);">
                        <div class="card-body">
                            <h5 class="text-muted">2 raw materials are below their reorder points and require immediate
                                attention.</h5>
                            <button type="button" class="btn btn-outline-danger">View All Low Stock</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">Raw Material Stock</h4>
                    <p class="text-muted mb-0" style="font-size: 14px;">Current raw materials inventory status</p>
                </div>
                <div class="header-buttons">
                    <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-clockwise"></i>
                        Refresh</button>
                    <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-bar-chart"></i> Usage
                        Chart</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Material</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Reorder Point</th>
                            <th>Status</th>
                            <th>Primary Supplier</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>RM-001</strong></td>
                            <td>Copper Wire 1.5mm</td>
                            <td>Metals</td>
                            <td>1,250 kg</td>
                            <td>500 kg</td>
                            <td><span class="status-badge in-stock">In Stock</span></td>
                            <td>Hindalco Industries Ltd.</td>
                            <td><button class="btn btn-outline-primary btn-action">Update</button></td>
                        </tr>
                        <tr>
                            <td><strong>RM-002</strong></td>
                            <td>PVC Compound</td>
                            <td>Polymers</td>
                            <td>850 kg</td>
                            <td>400 kg</td>
                            <td><span class="status-badge in-stock">In Stock</span></td>
                            <td>Reliance Polymers</td>
                            <td><button class="btn btn-outline-primary btn-action">Update</button></td>
                        </tr>
                        <tr>
                            <td><strong>RM-003</strong></td>
                            <td>Aluminum Wire</td>
                            <td>Metals</td>
                            <td>320 kg</td>
                            <td>400 kg</td>
                            <td><span class="status-badge low-stock">Low Stock</span></td>
                            <td>Jindal Aluminium</td>
                            <td><button class="btn btn-warning btn-action">Order Now</button></td>
                        </tr>
                        <tr>
                            <td><strong>RM-004</strong></td>
                            <td>Rubber Insulation</td>
                            <td>Polymers</td>
                            <td>210 kg</td>
                            <td>300 kg</td>
                            <td><span class="status-badge low-stock">Low Stock</span></td>
                            <td>MRF Rubber Co.</td>
                            <td><button class="btn btn-warning btn-action">Order Now</button></td>
                        </tr>
                        <tr>
                            <td><strong>RM-005</strong></td>
                            <td>PVC Conduit Pieces</td>
                            <td>Components</td>
                            <td>2,500 units</td>
                            <td>1,000 units</td>
                            <td><span class="status-badge in-stock">In Stock</span></td>
                            <td>Finolex Pipes</td>
                            <td><button class="btn btn-outline-primary btn-action">Update</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row g-4">
            <!-- Material Consumption Rates -->
            <div class="col-md-6">
                <div class="card">
                    <h5 class="mb-1">Material Consumption Rates</h5>
                    <p class="text-muted" style="font-size: 14px;">Weekly consumption of key raw materials</p>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between">
                            <span>Copper Wire</span>
                            <small class="text-muted">245 kg/week</small>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 80%;" aria-valuenow="80"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between">
                            <span>PVC Compound</span>
                            <small class="text-muted">180 kg/week</small>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 65%;" aria-valuenow="65"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between">
                            <span>Aluminum Wire</span>
                            <small class="text-muted">95 kg/week</small>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 30%;" aria-valuenow="30"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <div>
                        <div class="d-flex justify-content-between">
                            <span>Rubber Insulation</span>
                            <small class="text-muted">65 kg/week</small>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 20%;" aria-valuenow="20"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Upcoming Material Deliveries -->
            <div class="col-md-6">
                <div class="card">
                    <h5 class="mb-1">Upcoming Material Deliveries</h5>
                    <p class="text-muted" style="font-size: 14px;">Expected raw material shipments</p>

                    <div class="delivery-item d-flex justify-content-between align-items-start">
                        <div>
                            <div class="material-title">Copper Wire (5mm)</div>
                            <div class="material-supplier">Supplier: Hindalco Industries Ltd.</div>
                        </div>
                        <div class="text-end">
                            <div class="material-weight">500 kg</div>
                            <div class="material-date">28 Apr 2025</div>
                        </div>
                    </div>

                    <div class="delivery-item d-flex justify-content-between align-items-start">
                        <div>
                            <div class="material-title">Aluminum Wire (2mm)</div>
                            <div class="material-supplier">Supplier: Jindal Aluminium</div>
                        </div>
                        <div class="text-end">
                            <div class="material-weight">350 kg</div>
                            <div class="material-date">30 Apr 2025</div>
                        </div>
                    </div>

                    <div class="delivery-item d-flex justify-content-between align-items-start">
                        <div>
                            <div class="material-title">PVC Insulation (Red)</div>
                            <div class="material-supplier">Supplier: Reliance Polymers</div>
                        </div>
                        <div class="text-end">
                            <div class="material-weight">750 kg</div>
                            <div class="material-date">2 May 2025</div>
                        </div>
                    </div>

                </div>
            </div>

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
                data: [12500, 9800, 15200, 11300, 18400, 25600, 16800],
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
                        stepSize: 6500
                    }
                }
            }
        }
    });

    // Pie Chart
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: ['Wires & Cables', 'Switches & Sockets', 'Lighting', 'Fans', 'MCBs & DBs', 'Accessories'],
            datasets: [{
                data: [21, 16, 25, 19, 11, 9],
                backgroundColor: [
                    '#0d6efd',  // Blue (Wires & Cables)
                    '#20c997',  // Green (Switches & Sockets)
                    '#ffc107',  // Orange (Lighting)
                    '#fd7e14',  // Orange-dark (Fans)
                    '#6f42c1',   // Violet (MCBs & DBs)
                    '#C66EF9'   // Purple (Accessories)
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

    function showRetailStoreTab(id) {
        const tabs = document.querySelectorAll('.retailStoreTab');
        const contents = document.querySelectorAll('.retailStore-tab-content');

        tabs.forEach(tab => tab.classList.remove('active'));
        contents.forEach(content => content.classList.remove('active'));

        document.querySelector(`#${id}`).classList.add('active');
        document.querySelector(`[onclick="showRetailStoreTab('${id}')"]`).classList.add('active');
    }
</script>