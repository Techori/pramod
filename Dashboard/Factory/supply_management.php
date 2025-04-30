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
    <div class="container py-4">
        <!-- Top Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Factory Supply Management</h2>
                <p class="text-muted mb-0">Track and manage raw materials and supplies for production</p>
            </div>
            <!-- New Order Button with Modal Trigger -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newOrderModal">New Order</button>
        </div>

        <!-- New Order Modal -->
        <div class="modal fade" id="newOrderModal" tabindex="-1" aria-labelledby="newOrderModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newOrderModalLabel">Create New Order</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="newOrderForm">
                            <div class="mb-3">
                                <label for="orderItem" class="form-label">Item</label>
                                <input type="text" class="form-control" id="orderItem" placeholder="Enter item name"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="orderQuantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="orderQuantity"
                                    placeholder="Enter quantity" required>
                            </div>
                            <div class="mb-3">
                                <label for="orderSupplier" class="form-label">Supplier</label>
                                <input type="text" class="form-control" id="orderSupplier"
                                    placeholder="Enter supplier name" required>
                            </div>
                            <div class="mb-3">
                                <label for="orderDelivery" class="form-label">Delivery Date</label>
                                <input type="date" class="form-control" id="orderDelivery" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" form="newOrderForm" id="saveOrderBtn">Save
                            Order</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-9">
                <input type="text" id="searchInput" class="form-control"
                    placeholder="Search supplies by ID, item, or supplier...">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <div class="d-flex mb-3">
                    <select id="categoryFilter" class="form-select me-2 w-50">
                        <option value="">All Categories</option>
                        <option value="Metals">Copper Wire 1.5mm</option>
                        <option value="Polymers">PVC Compound</option>
                        <option value="Components">Aluminum Wire</option>
                    </select>
                    <button id="filterBtn" class="btn btn-outline-secondary w-50">Filter</button>
                </div>
                <button id="scheduleBtn" class="btn btn-outline-secondary w-50">Schedule</button>
            </div>
        </div>

        <div class="row mb-5 g-3">
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <div class="fs-1 mb-2"><i class="fa-regular fa-hourglass-half"></i></div>
                    <h5 class="mb-0">Pending Orders</h5>
                    <h3 class="fw-bold">12</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <div class="fs-1 mb-2"><i class="fa-solid fa-truck-arrow-right"></i></div>
                    <h5 class="mb-0">In Transit</h5>
                    <h3 class="fw-bold">8</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <div class="fs-1 mb-2"><i class="fa-solid fa-check"></i></div>
                    <h5 class="mb-0">Delivered This Month</h5>
                    <h3 class="fw-bold">34</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <div class="fs-1 mb-2"><i class="fa-brands fa-creative-commons-by"></i></div>
                    <h5 class="mb-0">Active Suppliers</h5>
                    <h3 class="fw-bold">16</h3>
                </div>
            </div>
        </div>

        <!-- Recent Supply Orders -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold">Recent Supply Orders</h4>
            <div>
                <button id="refreshBtn" class="btn btn-outline-secondary me-2">Refresh</button>
                <button id="viewAllBtn" class="btn btn-outline-secondary">View All</button>
            </div>
        </div>

        <div class="table-responsive mb-5">
            <table id="supplyTable" class="table table-hover align-middle">
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

            // Schedule Functionality (Filter by "In Transit" status)
            document.getElementById('scheduleBtn').addEventListener('click', function () {
                const rows = document.querySelectorAll('#supplyTable tbody tr');
                rows.forEach(row => {
                    const status = row.cells[5].textContent.trim().toLowerCase();
                    if (status === 'in transit') {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            // View All Button (Reveals hidden rows)
            document.getElementById('viewAllBtn').addEventListener('click', function () {
                const rows = document.querySelectorAll('#supplyTable tbody tr');
                rows.forEach(row => row.style.display = '');
                this.style.display = 'none';  // Hide the "View All" button
            });

            // Refresh Button (Reload page)
            document.getElementById('refreshBtn').addEventListener('click', function () {
                location.reload();
            });

            // Handle form submission (for demo purposes, just log the form data)
            document.getElementById('newOrderForm').addEventListener('submit', function (e) {
                e.preventDefault(); // Prevent the form from submitting normally

                // Collect form data
                const orderItem = document.getElementById('orderItem').value;
                const orderQuantity = document.getElementById('orderQuantity').value;
                const orderSupplier = document.getElementById('orderSupplier').value;
                const orderDelivery = document.getElementById('orderDelivery').value;

                // Log the data (in a real scenario, you'd send this to a server)
                console.log('New Order Details:', {
                    item: orderItem,
                    quantity: orderQuantity,
                    supplier: orderSupplier,
                    deliveryDate: orderDelivery
                });

                // Close the modal after saving
                const modal = bootstrap.Modal.getInstance(document.getElementById('newOrderModal'));
                modal.hide();

                // Optionally, reset the form
                document.getElementById('newOrderForm').reset();
            });
        </script>
    </div>
    <!-- New Section: Low Stock and Supply Trends -->
    <div class="row g-4">
        <!-- Low Stock Alert Section -->
        <div class="col-md-6">
            <div class="card p-3">
                <h5 class="fw-bold text-warning"><i class="bi bi-exclamation-circle"></i> Low Stock Alert</h5>
                <ul class="list-unstyled mt-3 mb-4">
                    <li class="mb-2">Copper Wire 1.5mm <span class="text-danger fw-bold float-end">Critical (120 kg
                            left)</span></li>
                    <li class="mb-2">PVC Insulation (Red) <span class="text-warning fw-bold float-end">Low (450 kg
                            left)</span></li>
                    <li class="mb-2">Aluminum Wire 2.0mm <span class="text-warning fw-bold float-end">Low (380 kg
                            left)</span></li>
                </ul>
                <button class="btn btn-outline-warning w-100" data-bs-toggle="modal"
                    data-bs-target="#orderLowStockModal">
                    Order Low Stock Items
                </button>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="orderLowStockModal" tabindex="-1" aria-labelledby="orderLowStockModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="orderLowStockModalLabel">Confirm Order</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Do you want to place an order for the low stock items listed?
                        <ul class="mt-3">
                            <li>Copper Wire 1.5mm – 120 kg</li>
                            <li>PVC Insulation (Red) – 450 kg</li>
                            <li>Aluminum Wire 2.0mm – 380 kg</li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-warning" onclick="confirmOrder()">Confirm Order</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Supply Trends Card -->
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

                <button class="btn btn-outline-primary w-100" data-bs-toggle="modal"
                    data-bs-target="#fullReportModal">View Full Report</button>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="fullReportModal" tabindex="-1" aria-labelledby="fullReportModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="fullReportModalLabel">Supply Trends - Full Report</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>This report contains the full monthly procurement data of all key raw materials:</p>
                        <ul>
                            <li><strong>Copper Wire:</strong> 8,500 kg (↑ 12% from last month)</li>
                            <li><strong>PVC Insulation:</strong> 6,200 kg (↔ Same as last month)</li>
                            <li><strong>Aluminum Wire:</strong> 4,800 kg (↓ 5% from last month)</li>
                            <li><strong>Packaging Material:</strong> 2,300 units (↑ 8%)</li>
                            <li><strong>Machine Parts:</strong> 150 units (New entry)</li>
                        </ul>
                    </div>
                </div>
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


        // Handle form submission (for demo purposes, just log the form data)
        document.getElementById('newOrderForm').addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent the form from submitting normally

            // Collect form data
            const orderItem = document.getElementById('orderItem').value;
            const orderQuantity = document.getElementById('orderQuantity').value;
            const orderSupplier = document.getElementById('orderSupplier').value;
            const orderDelivery = document.getElementById('orderDelivery').value;

            // Log the data (in a real scenario, you'd send this to a server)
            console.log('New Order Details:', {
                item: orderItem,
                quantity: orderQuantity,
                supplier: orderSupplier,
                deliveryDate: orderDelivery
            });

            // Close the modal after saving
            const modal = bootstrap.Modal.getInstance(document.getElementById('newOrderModal'));
            modal.hide();

            // Optionally, reset the form
            document.getElementById('newOrderForm').reset();
        });

        // Low Stock Alert btn ke liye
        function confirmOrder() {
            alert("Low stock items order placed successfully!");
            const modal = bootstrap.Modal.getInstance(document.getElementById('orderLowStockModal'));
            modal.hide();
        }
    </script>