<style>
    .tab-nav {
        background-color: #f8f9fa;
        padding: 10px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
    }

    .tab-nav a {
        text-decoration: none;
        padding: 10px 15px;
        color: #000;
        font-weight: 500;
    }

    .tab-nav a.active {
        border-bottom: 3px solid #0d6efd;
        color: #0d6efd;
    }

    .table-heading {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .table-actions i {
        margin: 0 6px;
        cursor: pointer;
    }

    .badge {
        font-size: 0.8rem;
    }
</style>


<div class="container py-4">

    <!-- Top Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Factory Supply Management</h2>
            <p class="text-muted mb-0">Track and manage raw materials and supplies for production</p>
        </div>
        <button class="btn btn-primary">New Order</button>
    </div>

    <div class="row mb-4">
        <div class="col-md-9">
            <input type="text" class="form-control" placeholder="Search supplies by ID, item, or supplier...">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-outline-secondary w-50">Filter</button>
            <button class="btn btn-outline-secondary w-50">Schedule</button>
        </div>
    </div>

    <div class="row mb-5 g-3">
        <div class="col-md-3">
            <div class="card text-center p-3">
                <div class="fs-1 mb-2">📦</div>
                <h5 class="mb-0">Pending Orders</h5>
                <h3 class="fw-bold">12</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3">
                <div class="fs-1 mb-2">🚚</div>
                <h5 class="mb-0">In Transit</h5>
                <h3 class="fw-bold">8</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3">
                <div class="fs-1 mb-2">✅</div>
                <h5 class="mb-0">Delivered This Month</h5>
                <h3 class="fw-bold">34</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3">
                <div class="fs-1 mb-2">📝</div>
                <h5 class="mb-0">Active Suppliers</h5>
                <h3 class="fw-bold">16</h3>
            </div>
        </div>
    </div>

    <!-- Recent Supply Orders -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Recent Supply Orders</h4>
        <div>
            <button class="btn btn-outline-secondary me-2">Refresh</button>
            <button class="btn btn-outline-secondary">View All</button>
        </div>
    </div>

    <div class="table-responsive mb-5">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Order ID</th>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Supplier</th>
                    <th>Delivery Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>SUP-2025-001</strong></td>
                    <td>Copper Wire 2.5mm</td>
                    <td>2000 kg</td>
                    <td>Hindalco Industries</td>
                    <td>08 Apr, 2025</td>
                    <td><span class="badge bg-success">Delivered</span></td>
                    <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
                <tr>
                    <td><strong>SUP-2025-002</strong></td>
                    <td>PVC Insulation</td>
                    <td>1500 kg</td>
                    <td>Polycab Ltd</td>
                    <td>10 Apr, 2025</td>
                    <td><span class="badge bg-primary">In Transit</span></td>
                    <td><a href="#" class="btn btn-sm btn-outline-primary">Track</a></td>
                </tr>
                <tr>
                    <td><strong>SUP-2025-003</strong></td>
                    <td>Aluminum Wire</td>
                    <td>3000 kg</td>
                    <td>Sterlite Technologies</td>
                    <td>12 Apr, 2025</td>
                    <td><span class="badge bg-warning text-dark">Ordered</span></td>
                    <td><a href="#" class="btn btn-sm btn-outline-primary">Track</a></td>
                </tr>
                <tr>
                    <td><strong>SUP-2025-004</strong></td>
                    <td>Packaging Material</td>
                    <td>500 units</td>
                    <td>Packaging Solutions</td>
                    <td>05 Apr, 2025</td>
                    <td><span class="badge bg-success">Delivered</span></td>
                    <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
                <tr>
                    <td><strong>SUP-2025-005</strong></td>
                    <td>Machine Parts</td>
                    <td>24 units</td>
                    <td>Industrial Machines Ltd</td>
                    <td>11 Apr, 2025</td>
                    <td><span class="badge bg-primary">In Transit</span></td>
                    <td><a href="#" class="btn btn-sm btn-outline-primary">Track</a></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- New Section: Low Stock and Supply Trends -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card p-3">
                <h5 class="fw-bold text-warning"><i class="bi bi-exclamation-circle"></i> Low Stock Alert
                </h5>
                <ul class="list-unstyled mt-3 mb-4">
                    <li class="mb-2">Copper Wire 1.5mm <span class="text-danger fw-bold float-end">Critical
                            (120 kg left)</span></li>
                    <li class="mb-2">PVC Insulation (Red) <span class="text-warning fw-bold float-end">Low
                            (450 kg left)</span></li>
                    <li class="mb-2">Aluminum Wire 2.0mm <span class="text-warning fw-bold float-end">Low
                            (380 kg left)</span></li>
                </ul>
                <button class="btn btn-outline-warning w-100">Order Low Stock Items</button>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3">
                <h5 class="fw-bold text-primary"><i class="bi bi-graph-up"></i> Supply Trends</h5>
                <p class="text-muted mb-4">Monthly procurement of top 3 raw materials</p>

                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Copper Wire</span><span>8,500 kg</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-primary" style="width: 90%;"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>PVC Insulation</span><span>6,200 kg</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-primary" style="width: 65%;"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Aluminum Wire</span><span>4,800 kg</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-primary" style="width: 45%;"></div>
                    </div>
                </div>

                <button class="btn btn-outline-primary w-100">View Full Report</button>
            </div>
        </div>
    </div>

    <script>
        function showTab(tab) {
            document.querySelectorAll('.tab-content').forEach(t => t.classList.add('d-none'));
            document.getElementById(tab).classList.remove('d-none');
            document.querySelectorAll('.tab-nav a').forEach(a => a.classList.remove('active'));
            document.querySelector('.tab-nav a[href="#' + tab + '"]').classList.add('active');
            document.getElementById('actionLabel').innerText = tab === 'purchase' ? 'New Order' : 'Add Supplier';
        }
    </script>

    <script>
        const ctxPie = document.getElementById('spendingChart');
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: ['Havells', 'Polycab', 'Orient', 'Bajaj', 'Anchor', 'Others'],
                datasets: [{
                    data: [28, 23, 10, 8, 12, 18],
                    backgroundColor: ['#007bff', '#20c997', '#fd7e14', '#ff5733', '#6f42c1', '#343a40']
                }]
            },
            options: {
                responsive: true
            }
        });

        const ctxLine = document.getElementById('ordersTrend');
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Orders',
                    data: [38, 32, 45, 53, 48, 42],
                    fill: false,
                    borderColor: '#007bff',
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        // Spending by Supplier (Pie Chart)
        const spendingCtx = document.getElementById('spendingChart').getContext('2d');
        const spendingChart = new Chart(spendingCtx, {
            type: 'pie',
            data: {
                labels: ['Supplier A', 'Supplier B', 'Supplier C'],
                datasets: [{
                    label: 'Spending',
                    data: [3000, 2000, 5000],
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Purchase Orders Trend (Line Chart)
        const ordersCtx = document.getElementById('ordersTrend').getContext('2d');
        const ordersTrend = new Chart(ordersCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
                datasets: [{
                    label: 'Purchase Orders',
                    data: [12, 19, 3, 5, 9],
                    fill: false,
                    borderColor: '#4BC0C0',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });


    </script>