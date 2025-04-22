<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Dashboard - Shree Unnati Wires & Traders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            line-height: 1.6;
            color: #333;
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .sidebar {
            width: 60px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #2c2f3e;
            color: #fff;
            padding-top: 20px;
            transition: width 0.3s ease-in-out;
            z-index: 1000;
            overflow: hidden;
        }

        .sidebar:hover {
            width: 250px;
        }

        .sidebar .logo {
            padding: 10px 20px;
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            background-color: #1a1d2a;
            position: relative;
            transition: opacity 0.3s ease-in-out;
        }

        .sidebar:not(:hover) .logo {
            opacity: 0;
            pointer-events: none;
        }

        .sidebar .close-btn {
            position: absolute;
            top: 10px;
            right: 20px;
            color: #fff;
            border: none;
            background: none;
            font-size: 1.5rem;
            cursor: pointer;
            display: none;
        }

        .sidebar:hover .close-btn {
            display: none;
            /* Hide close button when hovering on desktop */
        }

        .sidebar .nav-item {
            padding: 10px 20px;
            transition: padding 0.3s ease-in-out;
        }

        .sidebar:not(:hover) .nav-item {
            padding: 10px 0;
            text-align: center;
        }

        .sidebar .nav-link {
            color: #ccc;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            transition: justify-content 0.3s ease-in-out;
            padding: 8px 12px;
            border-radius: 5px;
        }

        .sidebar:hover .nav-link {
            color: #ccc;
        }

        .sidebar:not(:hover) .nav-link {
            justify-content: center;
        }

        .sidebar .nav-link span {
            margin-left: 10px;
            transition: opacity 0.3s ease-in-out;
        }

        .sidebar:not(:hover) .nav-link span {
            opacity: 0;
            pointer-events: none;
        }

        .sidebar .nav-link i {
            width: 20px;
            transition: margin 0.3s ease-in-out;
        }

        .sidebar:not(:hover) .nav-link i {
            margin: 0;
        }

        .sidebar .nav-link.active {
            color: #fff;
            background-color: #b0e0e6;
        }

        .sidebar .nav-link:hover {
            color: #007bff;
            background-color: #b0e0e6;
        }

        .sidebar .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 10px 20px;
            font-size: 0.8rem;
            color: #ccc;
            background-color: #1a1d2a;
            transition: opacity 0.3s ease-in-out;
        }

        .sidebar:not(:hover) .footer {
            opacity: 0;
            pointer-events: none;
        }

        @media (max-width: 767.98px) {
            .sidebar {
                transform: translateX(-250px);
                width: 250px;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar .close-btn {
                display: block;
                top: 10px;
                right: 20px;
            }

            .sidebar:hover {
                width: 250px;
                /* No hover effect on mobile, full width when open */
            }

            .sidebar:not(:hover) .nav-link span {
                opacity: 1;
                /* Show text when open on mobile */
            }

            .sidebar:not(:hover) .logo {
                opacity: 1;
                /* Show logo when open on mobile */
            }

            .sidebar:not(:hover) .footer {
                opacity: 1;
                /* Show footer when open on mobile */
            }

            .sidebar:not(:hover) .nav-item {
                padding: 10px 20px;
                /* Normal padding when open on mobile */
            }
        }

        main {
            margin-left: 60px;
            padding: 20px;
            transition: margin-left 0.3s ease-in-out;
        }

        .sidebar:hover~main {
            margin-left: 250px;
        }

        .header {
            background-color: #e3f2fd;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
            transition: padding-left 0.3s ease-in-out, margin-left 0.3s ease-in-out;
            z-index: 900;
        }

        .sidebar:hover~.header {
            padding-left: 20px;
            margin-left: 250px;
        }

        .header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .toggle-btn {
            display: none;
            background-color: #007bff;
            color: #fff;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.1s ease;
        }

        .toggle-btn:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .toggle-btn:active {
            transform: scale(0.95);
        }

        @media (max-width: 767.98px) {
            .toggle-btn {
                display: block;
            }

            main {
                margin-left: 0;
            }

            .sidebar:hover~main {
                margin-left: 0;
            }

            .header {
                margin-left: 0;
            }

            .sidebar:hover~.header {
                margin-left: 0;
            }
        }

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
            border: 1px solid #eee;
            border-radius: 5px;
            padding: 1.5rem;
        }

        .quick-access .card {
            min-height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            background-color: #fff;
            transition: background-color 0.3s ease;
        }

        .quick-access .card:hover {
            background-color: #b0e0e6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th,
        td {
            padding: 0.8rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #f0f8ff;
            color: #222;
        }

        .alert {
            margin-bottom: 1rem;
            border-radius: 5px;
            border: 1px solid transparent;
        }

        .badge {
            padding: 0.4rem 0.8rem;
            border-radius: 5px;
        }

        .profile-dropdown {
            position: relative;
            display: inline-block;
        }

        .profile-dropdown .dropdown-toggle {
            background-color: transparent;
            border: none;
            color: #555;
            cursor: pointer;
            transition: color 0.3s ease;
            padding: 8px 12px;
            border-radius: 5px;
        }

        .profile-dropdown .dropdown-toggle:hover {
            color: #007bff;
            background-color: #b0e0e6;
        }

        .profile-dropdown .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            z-index: 1000;
            display: none;
            min-width: 160px;
            padding: 0.5rem 0;
            margin: 0.125rem 0 0;
            background-color: #fff;
            border: 1px solid #eee;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-dropdown .dropdown-item {
            display: block;
            width: 100%;
            padding: 0.25rem 1.5rem;
            clear: both;
            font-weight: 400;
            color: #555;
            text-align: inherit;
            text-decoration: none;
            white-space: nowrap;
            background-color: transparent;
            border: 0;
            transition: background-color 0.3s ease;
        }

        .profile-dropdown .dropdown-item:hover {
            background-color: #b0e0e6;
            color: #007bff;
        }

        .profile-dropdown .dropdown-menu.show {
            display: block;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="logo">Vendor Dashboard
            <button class="close-btn" id="closeSidebar"><i class="bi bi-x-lg"></i></button>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link active" href="#" data-tooltip="Dashboard"><i
                    class="bi bi-grid"></i><span>Dashboard</span></a>
            <a class="nav-link" href="#" data-tooltip="Orders"><i class="bi bi-cart"></i><span>Orders</span></a>
            <a class="nav-link" href="#" data-tooltip="Deliveries"><i
                    class="bi bi-truck"></i><span>Deliveries</span></a>
            <a class="nav-link" href="#" data-tooltip="Products"><i class="bi bi-box"></i><span>Products</span></a>
            <a class="nav-link" href="#" data-tooltip="Payments"><i class="bi bi-wallet"></i><span>Payments</span></a>
            <a class="nav-link" href="#" data-tooltip="Invoices"><i class="bi bi-receipt"></i><span>Invoices</span></a>
            <a class="nav-link" href="#" data-tooltip="Reports"><i class="bi bi-bar-chart"></i><span>Reports</span></a>
            <a class="nav-link" href="#" data-tooltip="Settings"><i class="bi bi-gear"></i><span>Settings</span></a>
        </nav>
        <div class="footer">
            © 2025 Unnati Traders
        </div>
    </div>

    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="logo">
                <button class="toggle-btn" id="toggleSidebar"><i class="bi bi-list"></i></button>
                <span>Manage orders, deliveries, and payment status</span>
            </div>
            <div>
                <form class="d-flex">
                    <input class="form-control" type="search" placeholder="Search orders, products, or invoices..."
                        aria-label="Search">
                    <button class="btn btn-primary" type="submit">Search</button>
                </form>
            </div>
            <div>

                <button class="btn btn-primary me-2">Update Products</button>

                <button class="btn btn-primary">Track Orders</button>

            </div>

            <div class="profile-dropdown">
                <button class="dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="bi bi-person-circle"></i> Profile
                </button>
                <ul class="dropdown-menu" aria-labelledby="profileDropdown">
                    <li><a class="dropdown-item" href="#" onclick="showAlert('Update Profile')">Update Profile</a></li>
                    <li><a class="dropdown-item" href="#" onclick="showAlert('Logout')">Logout</a></li>
                </ul>
            </div>
        </div>
        <div class="container">
            <div class="d-flex justify-content-between" style="margin-top: 1rem; color: #555;">
                <div>Active Orders <span class="badge bg-primary">16</span> <span>+3 vs last month</span></div>
                <div>Pending Deliveries <span class="badge bg-warning">8</span> <span>+2 vs last month</span></div>
                <div>Pending Payments <span class="badge bg-danger">₹2,85,450</span> <span>+12.5% vs last month</span>
                </div>
                <div>This Month Revenue <span class="badge bg-success">₹4,35,250</span> <span>+8.7% vs last month</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main id="mainContent">
        <div class="container">
            <!-- Row 1: Order Trends & Alerts -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <h3 class="section-title">Order Trends (Last 6 Months)</h3>
                        <p class="hero-subtitle">Monthly order count</p>
                        <canvas id="orderTrendsChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <h3 class="section-title">Alerts & Notifications</h3>
                        <div class="alert alert-primary">
                            <i class="bi bi-bell"></i> New Order Received<br>Order #ORD-2854 for ₹24,500<br><a href="#"
                                class="alert-link">View Order</a>
                        </div>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> Order Deadline<br>Order #ORD-2485 due in 2
                            days<br><a href="#" class="alert-link">Process Now</a>
                        </div>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> Payment Received<br>₹36,500 for invoice #INV-3845<br><a
                                href="#" class="alert-link">View Details</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2: Products by Category & Payment Status -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <h3 class="section-title">Products by Category</h3>
                        <canvas id="productsChart"></canvas>
                        <p class="hero-subtitle"><small>Wires: 40%, Lights: 25%, Switches: 20%, Others: 15%</small></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <h3 class="section-title">Payment Status</h3>
                        <p>Pending Payments: <span class="text-danger">₹43,250</span></p>
                        <p>Payments Received: <span class="text-success">₹2,85,450</span></p>
                        <div class="progress">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 60%" aria-valuenow="60"
                                aria-valuemin="0" aria-valuemax="100">60% Received</div>
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 40%"
                                aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 3: Quick Access -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <h3 class="section-title">Quick Access</h3>
                        <div class="quick-access">
                            <div class="row">
                                <div class="col-2">
                                    <div class="card">
                                        <div class="card-body">
                                            <i class="bi bi-cart fs-4"></i>
                                            <p class="mb-0">Manage Orders</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="card">
                                        <div class="card-body">
                                            <i class="bi bi-truck fs-4"></i>
                                            <p class="mb-0">Deliveries</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="card">
                                        <div class="card-body">
                                            <i class="bi bi-box fs-4"></i>
                                            <p class="mb-0">Products</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="card">
                                        <div class="card-body">
                                            <i class="bi bi-wallet fs-4"></i>
                                            <p class="mb-0">Payments</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="card">
                                        <div class="card-body">
                                            <i class="bi bi-receipt fs-4"></i>
                                            <p class="mb-0">Invoices</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="card">
                                        <div class="card-body">
                                            <i class="bi bi-bar-chart fs-4"></i>
                                            <p class="mb-0">Reports</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 4: Recent Orders & Upcoming Deliveries -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <h3 class="section-title">Recent Orders</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer Name</th>
                                    <th>Order Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>ORD-2854</td>
                                    <td>Unnati Traders</td>
                                    <td>12 Apr 2025</td>
                                    <td>₹24,500</td>
                                    <td><span class="badge bg-secondary">New</span></td>
                                </tr>
                                <tr>
                                    <td>ORD-2853</td>
                                    <td>Modern Electricals</td>
                                    <td>10 Apr 2025</td>
                                    <td>₹8,750</td>
                                    <td><span class="badge bg-warning">Processing</span></td>
                                </tr>
                                <tr>
                                    <td>ORD-2852</td>
                                    <td>City Lights</td>
                                    <td>08 Apr 2025</td>
                                    <td>₹12,300</td>
                                    <td><span class="badge bg-success">Shipped</span></td>
                                </tr>
                                <tr>
                                    <td>ORD-2851</td>
                                    <td>Sharma Electronics</td>
                                    <td>05 Apr 2025</td>
                                    <td>₹9,800</td>
                                    <td><span class="badge bg-success">Delivered</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <h3 class="section-title">Upcoming Deliveries</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Delivery ID</th>
                                    <th>Order ID</th>
                                    <th>Customer Name</th>
                                    <th>Delivery Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>DEL-485</td>
                                    <td>ORD-2846</td>
                                    <td>Modern Electricals</td>
                                    <td>14 Apr 2025</td>
                                    <td><span class="badge bg-info">Scheduled</span></td>
                                </tr>
                                <tr>
                                    <td>DEL-484</td>
                                    <td>ORD-2840</td>
                                    <td>City Lights</td>
                                    <td>13 Apr 2025</td>
                                    <td><span class="badge bg-info">Scheduled</span></td>
                                </tr>
                                <tr>
                                    <td>DEL-483</td>
                                    <td>ORD-2830</td>
                                    <td>Sharma Electronics</td>
                                    <td>13 Apr 2025</td>
                                    <td><span class="badge bg-warning">In Transit</span></td>
                                </tr>
                                <tr>
                                    <td>DEL-482</td>
                                    <td>ORD-2850</td>
                                    <td>Premium Switches</td>
                                    <td>12 Apr 2025</td>
                                    <td><span class="badge bg-warning">In Transit</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function showAlert(buttonName) {
            alert(`You clicked the "${buttonName}" button/link! This functionality would be implemented with further development to navigate to different pages or perform specific actions.`);
        }

        // Sidebar toggle functionality for mobile
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleSidebar = document.getElementById('toggleSidebar');
        const closeSidebar = document.getElementById('closeSidebar');

        function toggleSidebarState() {
            sidebar.classList.toggle('open');
            if (window.innerWidth < 768) {
                mainContent.style.marginLeft = sidebar.classList.contains('open') ? '250px' : '0';
            }
        }

        toggleSidebar.addEventListener('click', toggleSidebarState);
        closeSidebar.addEventListener('click', toggleSidebarState);

        // Ensure sidebar state updates on window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('open');
                mainContent.style.marginLeft = '60px';
            } else {
                if (sidebar.classList.contains('open')) {
                    mainContent.style.marginLeft = '250px';
                } else {
                    mainContent.style.marginLeft = '0';
                }
            }
        });

        // Initial check on load
        if (window.innerWidth < 768) {
            mainContent.style.marginLeft = '0';
        }

        // Order Trends Chart
        var ctx = document.getElementById('orderTrendsChart').getContext('2d');
        var orderTrendsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Orders',
                    data: [10, 15, 7, 12, 9, 6],
                    backgroundColor: '#007bff',
                    borderColor: '#0056b3',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Products by Category Chart
        var ctx2 = document.getElementById('productsChart').getContext('2d');
        var productsChart = new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: ['Wires', 'Lights', 'Switches', 'Others'],
                datasets: [{
                    data: [40, 25, 20, 15],
                    backgroundColor: ['#007bff', '#ffc107', '#28a745', '#dc3545']
                }]
            },
            options: {
                responsive: true
            }
        });
    </script>
</body>

</html>