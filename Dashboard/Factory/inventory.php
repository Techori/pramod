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
<h1>Finished Goods Inventory
</h1>
<p>Manage manufactured products ready for distribution</p>
<!-- Search bar & buttons -->
<div class="container-fluid d-flex justify-content-between align-items-center mb-4">


    <div class="d-flex justify-content-start ">
        <div class="input-group w-100 me-2">
            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
            <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Search..." />
        </div>
    </div>

    <div class="justify-contnt-end">
        <div class="d-flex mb-3">
            <select id="categoryFilter" class="form-select me-2 w-50">
                <option value="">All Categories</option>
                <option value="Metals">Electrical Wire</option>
                <option value="Polymers"> Armored Cable</option>
                <option value="Components"> Flexible Wire</option>
            </select>
            <button id="filterBtn" class="btn btn-outline-secondary w-50">Filter</button>
        </div>
        <!-- Button to open modal -->
        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#shipmentModal">
            New Shipment
        </button>

        <!-- Modal Structure -->
        <div class="modal fade" id="shipmentModal" tabindex="-1" aria-labelledby="shipmentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="shipmentModalLabel">New Shipment Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <form id="shipmentForm">
                            <div class="mb-3">
                                <label for="productId" class="form-label">Product ID</label>
                                <input type="text" class="form-control" id="productId" required>
                            </div>
                            <div class="mb-3">
                                <label for="productName" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="productName" required>
                            </div>
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <input type="text" class="form-control" id="category" required>
                            </div>
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="quantity" required>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" required>
                                    <option value="">Select</option>
                                    <option value="Ready">Ready</option>
                                    <option value="Pending">Pending</option>
                                    <option value="In Transit">In Transit</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="destination" class="form-label">Destination</label>
                                <input type="text" class="form-control" id="destination" required>
                            </div>

                            <button type="submit" class="btn btn-success">Submit Shipment</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- Cards -->
<div class="row">
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
            <div class="card-body">
                <h6 class="text-muted">Total Products</h6>
                <h3 class="fw-bold">54 items
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
            <div class="card-body">
                <h6 class="text-muted">Ready for Shipment</h6>
                <h3 class="fw-bold">28 items</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
            <div class="card-body">
                <h6 class="text-muted">Retail Allocation</h6>
                <h3 class="fw-bold">18 items</h3>
            </div>
        </div>
    </div>
</div>

<div class="containerv mx-1">
    <div class="card p-4 shadow-sm">
        <h4 class="mb-1">Distribution Channels</h4>
        <p class="text-muted small mb-4">Product allocation by destination</p>

        <div class="row g-3">

            <div class="col-md-3 col-sm-6">
                <div class="distribution-card">
                    <h6 class="fw-semibold">Retail Stores</h6>
                    <p class="text-muted small mb-2">42% of inventory</p>
                    <div class="progress">
                        <div class="progress-bar" style="width: 42%;"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="distribution-card">
                    <h6 class="fw-semibold">Wholesalers</h6>
                    <p class="text-muted small mb-2">28% of inventory</p>
                    <div class="progress">
                        <div class="progress-bar" style="width: 28%;"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="distribution-card">
                    <h6 class="fw-semibold">Industrial Clients</h6>
                    <p class="text-muted small mb-2">18% of inventory</p>
                    <div class="progress">
                        <div class="progress-bar" style="width: 18%;"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="distribution-card">
                    <h6 class="fw-semibold">Distributors</h6>
                    <p class="text-muted small mb-2">12% of inventory</p>
                    <div class="progress">
                        <div class="progress-bar" style="width: 12%;"></div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

    <div class="card p-4 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Finished Products Inventory</h4>
                <p class="text-muted small mb-0">Current inventory of finished products ready for distribution</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm" id="refreshBtn" onclick="refreshTable()">
                    Refresh</button>
                <button id="viewAllBtn" class="btn btn-outline-secondary">View All</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle" id="supplyTable">
                <thead class="table-light">
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Destination</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="inventoryTable">
                    <tr>
                        <td><strong>FG-001</strong></td>
                        <td>1.5mm House Wire (100m Roll)</td>
                        <td>Electrical Wire</td>
                        <td>450 units<br><small class="text-muted">Min: 200</small></td>
                        <td><span class="status-badge in-stock">In Stock</span></td>
                        <td>Retail Stores</td>
                        <td>
                            <button class="btn btn-outline-primary action-btn">Ship</button>
                            <button class="btn btn-outline-secondary action-btn">Allocate</button>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>FG-002</strong></td>
                        <td>2.5mm Industrial Cable (200m Roll)</td>
                        <td>Electrical Cable</td>
                        <td>175 units<br><small class="text-muted">Min: 150</small></td>
                        <td><span class="status-badge in-stock"> In Stock</span></td>
                        <td>Wholesalers</td>
                        <td>
                            <button class="btn btn-outline-primary action-btn">Ship</button>
                            <button class="btn btn-outline-secondary action-btn">Allocate</button>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>FG-003</strong></td>
                        <td>Four-Core Armored Cable (50m)</td>
                        <td>Armored Cable</td>
                        <td>85 units<br><small class="text-muted">Min: 100</small></td>
                        <td><span class="status-badge low-stock"> Low Stock</span></td>
                        <td>Industrial Clients</td>
                        <td>
                            <button class="btn btn-outline-primary action-btn">Ship</button>
                            <button class="btn btn-outline-secondary action-btn">Allocate</button>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>FG-004</strong></td>
                        <td>Submersible Pump Cable (30m)</td>
                        <td>Special Cables</td>
                        <td>120 units<br><small class="text-muted">Min: 75</small></td>
                        <td><span class="status-badge in-stock">In Stock</span></td>
                        <td>Distributors</td>
                        <td>
                            <button class="btn btn-outline-primary action-btn">Ship</button>
                            <button class="btn btn-outline-secondary action-btn">Allocate</button>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>FG-005</strong></td>
                        <td>Flexible Multi-strand Wire (50m)</td>
                        <td>Flexible Wire</td>
                        <td>210 units<br><small class="text-muted">Min: 150</small></td>
                        <td><span class="status-badge in-stock">In Stock</span></td>
                        <td>Retail Stores</td>
                        <td>
                            <button class="btn btn-outline-primary action-btn">Ship</button>
                            <button class="btn btn-outline-secondary action-btn">Allocate</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>,
<script>
    // Search Functionality
    document.getElementById('searchInput').addEventListener('input', function () {
        const searchText = this.value.toLowerCase();
        const rows = document.querySelectorAll('#supplyTable tbody tr');

        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            let match = false;
            for (let i = 0; i < cells.length; i++) {
                if (cells[i].textContent.toLowerCase().includes(searchText)) {
                    match = true;
                    break;
                }
            }
            row.style.display = match ? '' : 'none';
        });
    });

    // Filter Functionality (Filter by "Ordered" status)
    document.getElementById('filterBtn').addEventListener('click', function () {
        const rows = document.querySelectorAll('#supplyTable tbody tr');
        rows.forEach(row => {
            const status = row.cells[5].textContent.trim().toLowerCase();
            if (status === 'ordered') {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    // Refresh Button (Reload page)
    document.getElementById('refreshBtn').addEventListener('click', function () {
        location.reload();
    });

    // View All Button (Reveals hidden rows)
    document.getElementById('viewAllBtn').addEventListener('click', function () {
        const rows = document.querySelectorAll('#supplyTable tbody tr');
        rows.forEach(row => row.style.display = '');
        this.style.display = 'none';  // Hide the "View All" button
    });
</script>

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
<script>
document.getElementById('shipmentForm').addEventListener('submit', function(e) {
  e.preventDefault();
  alert('Shipment added successfully!');
  // You can now fetch field values using:
  // document.getElementById('productId').value, etc.
});
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