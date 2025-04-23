const contentTemplates = {

    dashboard: () => `

        <div class="container-fluid">

            <h4>Vendor Dashboard Overview</h4>

            <p>Monitor your business performance with key metrics and quick actions.</p>

            <div class="row g-3 mb-4">

                <div class="col-sm-6 col-lg-3">

                    <div class="card p-3 border-start border-3 border-primary">

                        <small>Active Orders</small>

                        <h3>16 orders</h3>

                        <small class="text-success">+3 vs last month</small>

                    </div>

                </div>

                <div class="col-sm-6 col-lg-3">

                    <div class="card p-3 border-start border-3 border-warning">

                        <small>Pending Deliveries</small>

                        <h3>8 deliveries</h3>

                        <small class="text-warning">+2 vs last month</small>

                    </div>

                </div>

                <div class="col-sm-6 col-lg-3">

                    <div class="card p-3 border-start border-3 border-danger">

                        <small>Pending Payments</small>

                        <h3>₹2,85,450</h3>

                        <small class="text-danger">+12.5% vs last month</small>

                    </div>

                </div>

                <div class="col-sm-6 col-lg-3">

                    <div class="card p-3 border-start border-3 border-success">

                        <small>This Month Revenue</small>

                        <h3>₹4,35,250</h3>

                        <small class="text-success">+8.7% vs last month</small>

                    </div>

                </div>

            </div>

            <div class="card mb-4">

                <div class="card-body">

                    <h5>Quick Actions</h5>

                    <div class="row g-3 mt-3 quick-access">

                        <div class="col-4 col-md-2">

                            <div class="card text-center">

                                <div class="card-body">

                                    <i class="bi bi-cart fs-4"></i>

                                    <p class="mb-0">Add Invoice</p>

                                </div>

                            </div>

                        </div>

                        <div class="col-4 col-md-2">

                            <div class="card text-center">

                                <div class="card-body">

                                    <i class="bi bi-box fs-4"></i>

                                    <p class="mb-0">Check Stock</p>

                                </div>

                            </div>

                        </div>

                        <div class="col-4 col-md-2">

                            <div class="card text-center">

                                <div class="card-body">

                                    <i class="bi bi-wallet fs-4"></i>

                                    <p class="mb-0">View Expenses</p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="row mb-4">

                <div class="col-lg-8">

                    <div class="card p-3">

                        <h5>Order Trends (Last 6 Months)</h5>

                        <canvas id="orderTrendsChart" height="200"></canvas>

                    </div>

                </div>

                <div class="col-lg-4">

                    <div class="card p-3">

                        <h5>Recent Activity</h5>

                        <div class="alert alert-primary">

                            <i class="bi bi-bell"></i> New Order #ORD-2854 received

                            <a href="#" class="alert-link">View Details</a>

                        </div>

                        <div class="alert alert-warning">

                            <i class="bi bi-exclamation-triangle"></i> Payment overdue for #INV-3845

                            <a href="#" class="alert-link">Send Reminder</a>

                        </div>

                        <div class="alert alert-success">

                            <i class="bi bi-check-circle"></i> Delivery #DEL-482 completed

                            <a href="#" class="alert-link">View Status</a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    `,

    orders: () => `

        <div class="container-fluid">

            <h4>Order Management</h4>

            <p>Track and manage vendor orders efficiently.</p>

            <div class="card mb-4">

                <div class="card-body">

                    <div class="d-flex justify-content-between mb-3">

                        <h5>Recent Orders</h5>

                        <div>

                            <button class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> Add New Order</button>

                            <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-list"></i> View All Orders</button>

                        </div>

                    </div>

                    <table class="table table-sm">

                        <thead>

                            <tr><th>Order ID</th><th>Customer Name</th><th>Order Date</th><th>Amount</th><th>Status</th></tr>

                        </thead>

                        <tbody>

                            <tr><td>ORD-2854</td><td>Unnati Traders</td><td>12 Apr 2025</td><td>₹24,500</td><td><span class="badge bg-secondary">New</span></td></tr>

                            <tr><td>ORD-2853</td><td>Modern Electricals</td><td>10 Apr 2025</td><td>₹8,750</td><td><span class="badge bg-warning">Processing</span></td></tr>

                            <tr><td>ORD-2852</td><td>City Lights</td><td>08 Apr 2025</td><td>₹12,300</td><td><span class="badge bg-success">Shipped</span></td></tr>

                        </tbody>

                    </table>

                </div>

            </div>

            <div class="card">

                <div class="card-body">

                    <h5>Order Statistics</h5>

                    <p>Total Orders: 25 | Completed: 18 | Pending: 7</p>

                </div>

            </div>

        </div>

    `,

    deliveries: () => `

        <div class="container-fluid">

            <h4>Delivery Management</h4>

            <p>Monitor and schedule vendor deliveries.</p>

            <div class="card mb-4">

                <div class="card-body">

                    <div class="d-flex justify-content-between mb-3">

                        <h5>Upcoming Deliveries</h5>

                        <div>

                            <button class="btn btn-primary btn-sm"><i class="bi bi-truck"></i> Schedule Delivery</button>

                            <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-eye"></i> View Delivery Status</button>

                        </div>

                    </div>

                    <table class="table table-sm">

                        <thead>

                            <tr><th>Delivery ID</th><th>Order ID</th><th>Customer Name</th><th>Delivery Date</th><th>Status</th></tr>

                        </thead>

                        <tbody>

                            <tr><td>DEL-485</td><td>ORD-2846</td><td>Modern Electricals</td><td>14 Apr 2025</td><td><span class="badge bg-info">Scheduled</span></td></tr>

                            <tr><td>DEL-484</td><td>ORD-2840</td><td>City Lights</td><td>13 Apr 2025</td><td><span class="badge bg-warning">In Transit</span></td></tr>

                        </tbody>

                    </table>

                </div>

            </div>

            <div class="card">

                <div class="card-body">

                    <h5>Delivery Metrics</h5>

                    <p>On-Time: 85% | Delayed: 10% | Pending: 5%</p>

                </div>

            </div>

        </div>

    `,

    products: () => `

        <div class="container-fluid">

            <h4>Product Management</h4>

            <p>Manage your product inventory and stock levels.</p>

            <div class="card mb-4">

                <div class="card-body">

                    <div class="d-flex justify-content-between mb-3">

                        <h5>Product List</h5>

                        <div>

                            <button class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> Add New Product</button>

                            <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-list"></i> View Inventory</button>

                            <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-file-earmark"></i> Generate Stock Report</button>

                        </div>

                    </div>

                    <table class="table table-sm">

                        <thead>

                            <tr><th>Product ID</th><th>Name</th><th>Category</th><th>Stock</th><th>Price</th></tr>

                        </thead>

                        <tbody>

                            <tr><td>PROD-001</td><td>1.5mm Wire</td><td>Wires</td><td>500 m</td><td>₹50/m</td></tr>

                            <tr><td>PROD-002</td><td>LED Bulb</td><td>Lights</td><td>200 units</td><td>₹150/unit</td></tr>

                        </tbody>

                    </table>

                </div>

            </div>

            <div class="card">

                <div class="card-body">

                    <h5>Stock Alerts</h5>

                    <p class="text-danger">Low Stock: LED Bulb (200 units remaining)</p>

                </div>

            </div>

        </div>

    `,

    payments: () => `

        <div class="container-fluid">

            <h4>Payment Management</h4>

            <p>Track vendor payments and BNPL transactions.</p>

            <div class="card mb-4">

                <div class="card-body">

                    <div class="d-flex justify-content-between mb-3">

                        <h5>Payment Transactions</h5>

                        <div>

                            <button class="btn btn-primary btn-sm"><i class="bi bi-person"></i> View Vendors</button>

                            <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-gear"></i> Set Credit Limit</button>

                            <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-bell"></i> Send Payment Reminder</button>

                        </div>

                    </div>

                    <table class="table table-sm">

                        <thead>

                            <tr><th>Transaction ID</th><th>Vendor</th><th>Amount</th><th>Date</th><th>Status</th></tr>

                        </thead>

                        <tbody>

                            <tr><td>TRX-001</td><td>Modern Electricals</td><td>₹36,500</td><td>10 Apr 2025</td><td><span class="badge bg-success">Paid</span></td></tr>

                            <tr><td>TRX-002</td><td>City Lights</td><td>₹43,250</td><td>08 Apr 2025</td><td><span class="badge bg-danger">Overdue</span></td></tr>

                        </tbody>

                    </table>

                </div>

            </div>

            <div class="card">

                <div class="card-body">

                    <h5>BNPL Overview</h5>

                    <p>Outstanding: ₹2,85,450 | Interest Accrued: ₹5,250</p>

                </div>

            </div>

        </div>

    `,

    invoices: () => `

        <div class="container-fluid">

            <h4>Invoice Management</h4>

            <p>Create and track GST & Non-GST invoices.</p>

            <div class="card mb-4">

                <div class="card-body">

                    <div class="d-flex justify-content-between mb-3">

                        <h5>Invoice List</h5>

                        <div>

                            <button class="btn btn-primary btn-sm"><i class="bi bi-plus"></i> Generate Invoice</button>

                            <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-wallet"></i> View Pending Payments</button>

                            <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-download"></i> Download Invoice</button>

                        </div>

                    </div>

                    <table class="table table-sm">

                        <thead>

                            <tr><th>Invoice ID</th><th>Customer</th><th>Amount</th><th>Date</th><th>Status</th></tr>

                        </thead>

                        <tbody>

                            <tr><td>INV-3845</td><td>Unnati Traders</td><td>₹36,500</td><td>12 Apr 2025</td><td><span class="badge bg-success">Paid</span></td></tr>

                            <tr><td>INV-3844</td><td>City Lights</td><td>₹24,500</td><td>10 Apr 2025</td><td><span class="badge bg-warning">Pending</span></td></tr>

                        </tbody>

                    </table>

                </div>

            </div>

            <div class="card">

                <div class="card-body">

                    <h5>Invoice Summary</h5>

                    <p>GST Invoices: 15 | Non-GST Invoices: 10 | Total: ₹4,50,000</p>

                </div>

            </div>

        </div>

    `,

    reports: () => `

        <div class="container-fluid">

            <h4>Reports & Analytics</h4>

            <p>Analyze sales, BNPL, and financial performance.</p>

            <div class="card mb-4">

                <div class="card-body">

                    <div class="d-flex justify-content-between mb-3">

                        <h5>Sales Trends</h5>

                        <div>

                            <button class="btn btn-primary btn-sm"><i class="bi bi-bar-chart"></i> Generate Sales Report</button>

                            <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-file-earmark"></i> View Profit & Loss</button>

                            <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-gear"></i> Custom Report</button>

                        </div>

                    </div>

                    <canvas id="salesChart" height="200"></canvas>

                </div>

            </div>

            <div class="card">

                <div class="card-body">

                    <h5>BNPL Recovery</h5>

                    <p>Outstanding: ₹2,85,450 | Recovered: ₹1,50,000</p>

                </div>

            </div>

        </div>

    `,

    settings: () => `

        <div class="container-fluid">

            <h4>Settings</h4>

            <p>Configure vendor profile and system preferences.</p>

            <div class="card mb-4">

                <div class="card-body">

                    <h5>Vendor Profile</h5>

                    <form>

                        <div class="mb-3">

                            <label class="form-label">Business Name</label>

                            <input type="text" class="form-control" value="Shree Unnati Wires & Traders">

                        </div>

                        <div class="mb-3">

                            <label class="form-label">Credit Limit (BNPL)</label>

                            <input type="number" class="form-control" value="500000">

                        </div>

                        <div class="mb-3">

                            <label class="form-label">Notification Preferences</label>

                            <select class="form-select">

                                <option>Email & WhatsApp</option>

                                <option>Email Only</option>

                                <option>WhatsApp Only</option>

                            </select>

                        </div>

                        <button type="submit" class="btn btn-primary btn-sm">Update Settings</button>

                    </form>

                </div>

            </div>

            <div class="card">

                <div class="card-body">

                    <h5>System Actions</h5>

                    <button class="btn btn-outline-secondary btn-sm me-2"><i class="bi bi-person"></i> Manage Users</button>

                    <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-cloud-download"></i> Backup Data</button>

                </div>

            </div>

        </div>

    `

};
