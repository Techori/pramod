<?php
session_start();
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Dashboard - Shree Unnati Wires & Traders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="unnati">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            color: #333;
            background-color: #f8f9fa;
        }
        .sidebar {
            width: 200px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #ffffff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            padding-top: 20px;
            overflow-y: auto;
        }
        .sidebar .logo {
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
            padding: 15px;
            color: #0d6efd;
            border-bottom: 1px solid #ddd;
        }
        .sidebar nav a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #333;
            text-decoration: none;
            font-size: 0.95rem;
        }
        .sidebar nav a i {
            width: 24px;
            margin-right: 10px;
        }
        .sidebar nav a:hover, .sidebar nav a.active {
            background-color: #e9ecef;
            color: #0d6efd;
            font-weight: bold;
        }
        .sidebar .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 10px;
            font-size: 0.8rem;
            text-align: center;
            color: #6c757d;
            border-top: 1px solid #ddd;
        }
        main {
            margin-left: 200px;
            padding: 20px;
        }
        header.header {
            margin-left: 200px;
            background: #fff;
            border-bottom: 1px solid #ddd;
            padding: 10px 20px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
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
        .green-bg {
            background-color: #d4edda;
            color: #155724;
            padding: 4px 10px;
            border-radius: 10px;
        }
        .orange-bg {
            background-color: #fff3cd;
            color: #856404;
            padding: 4px 10px;
            border-radius: 10px;
        }
        .red-bg {
            background-color: #f8d7da;
            color: #721c24;
            padding: 4px 10px;
            border-radius: 10px;
        }
        .alert {
            border-radius: 0.5rem;
            padding: 15px;
        }
        .quick-access .card {
            min-height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                box-shadow: none;
            }
            main, header.header {
                margin-left: 0;
            }
            .container-fluid {
                padding-left: 10px;
                padding-right: 10px;
            }
            .chart-box {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">Vendor Dashboard</div>
        <nav class="nav flex-column mt-2">
            <a href="?page=dashboard" class="nav-link <?php echo $page === 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="?page=orders" class="nav-link <?php echo $page === 'orders' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i> Orders
            </a>
            <a href="?page=deliveries" class="nav-link <?php echo $page === 'deliveries' ? 'active' : ''; ?>">
                <i class="fas fa-truck"></i> Deliveries
            </a>
            <a href="?page=products" class="nav-link <?php echo $page === 'products' ? 'active' : ''; ?>">
                <i class="fas fa-box"></i> Products
            </a>
            <a href="?page=payments" class="nav-link <?php echo $page === 'payments' ? 'active' : ''; ?>">
                <i class="fas fa-wallet"></i> Payments
            </a>
            <a href="?page=invoices" class="nav-link <?php echo $page === 'invoices' ? 'active' : ''; ?>">
                <i class="fas fa-file-invoice"></i> Invoices
            </a>
            <a href="?page=reports" class="nav-link <?php echo $page === 'reports' ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar"></i> Reports
            </a>
            <a href="?page=settings" class="nav-link <?php echo $page === 'settings' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i> Settings
            </a>
        </nav>
        <div class="footer">© 2025 Unnati Traders</div>
    </div>

    <!-- Header -->
    <header class="header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <h5 class="mb-0 fw-bold">Vendor Dashboard</h5>
        </div>
        <form class="d-flex" role="search">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                <input class="form-control border-start-0" type="search" placeholder="Search orders, products, or invoices..." aria-label="Search">
            </div>
        </form>
        <div class="d-flex align-items-center">
            <button class="btn btn-outline-primary btn-sm me-2"><i class="fas fa-bell"></i></button>
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-user-circle"></i></button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">Update Profile</a></li>
                    <li>
                        <form action="../../logout.php" method="POST" class="d-inline">
                            <input type="hidden" name="logout_btn" value="logout">
                            <button type="submit" class="dropdown-item">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <?php if ($page === 'dashboard'): ?>
            <div class="container-fluid">
                <h1>Dashboard</h1>
                <p>Welcome back to your vendor management dashboard</p>
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
                            <div class="card-body">
                                <h6 class="text-muted">Active Orders</h6>
                                <h3 class="fw-bold">16 orders</h3>
                                <p class="text-success">+3 vs last month</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
                            <div class="card-body">
                                <h6 class="text-muted">Pending Deliveries</h6>
                                <h3 class="fw-bold">8 deliveries</h3>
                                <p class="text-warning">+2 vs last month</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
                            <div class="card-body">
                                <h6 class="text-muted">Pending Payments</h6>
                                <h3 class="fw-bold">₹2,85,450</h3>
                                <p class="text-danger">+12.5% vs last month</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
                            <div class="card-body">
                                <h6 class="text-muted">This Month Revenue</h6>
                                <h3 class="fw-bold">₹4,35,250</h3>
                                <p class="text-success">+8.7% vs last month</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="chart-container">
                    <div class="chart-box">
                        <h3>Order Trends (Last 6 Months)</h3>
                        <canvas id="orderTrendsChart"></canvas>
                    </div>
                    <div class="chart-box">
                        <h3>Recent Activity</h3>
                        <div class="alert alert-primary">
                            <i class="fas fa-bell"></i> New Order #ORD-2854 received
                            <a href="#" class="alert-link">View Details</a>
                        </div>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Payment overdue for #INV-3845
                            <a href="#" class="alert-link">Send Reminder</a>
                        </div>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> Delivery #DEL-482 completed
                            <a href="#" class="alert-link">View Status</a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12 my-4">
                        <div class="card stat-card cards shadow-sm" style="background-color: #fbf3d7;">
                            <div class="card-body">
                                <h5 class="text-muted">Low Stock Alert</h5>
                                <p>2 products are below minimum stock levels. Review inventory soon.</p>
                                <a href="?page=products" style="text-decoration: none;" class="text-dark">View Products →</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 my-4">
                        <div class="card stat-card cards shadow-sm" style="background-color: #d4ffe9;">
                            <div class="card-body">
                                <h5 class="text-muted">Recent Payments</h5>
                                <p>3 payments received today totaling ₹28,450.</p>
                                <a href="?page=payments" style="text-decoration: none;" class="text-dark">View Payments →</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($page === 'orders'): ?>
            <div class="container-fluid">
                <h4><i class="fas fa-shopping-cart text-primary"></i> Order Management</h4>
                <p>Track and manage vendor orders efficiently.</p>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-muted">Recent Orders</h5>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm"><i class="fas fa-plus"></i> Add New Order</button>
                                <button class="btn btn-outline-primary btn-sm"><i class="fas fa-list"></i> View All Orders</button>
                            </div>
                        </div>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer Name</th>
                                    <th>Order Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>ORD-2854</td>
                                    <td>Unnati Traders</td>
                                    <td>12 Apr 2025</td>
                                    <td>₹24,500</td>
                                    <td><span class="green-bg">New</span></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-eye"></i></button>
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-pen-to-square"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>ORD-2853</td>
                                    <td>Modern Electricals</td>
                                    <td>10 Apr 2025</td>
                                    <td>₹8,750</td>
                                    <td><span class="orange-bg">Processing</span></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-eye"></i></button>
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-pen-to-square"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>ORD-2852</td>
                                    <td>City Lights</td>
                                    <td>08 Apr 2025</td>
                                    <td>₹12,300</td>
                                    <td><span class="green-bg">Shipped</span></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-eye"></i></button>
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-pen-to-square"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php elseif ($page === 'deliveries'): ?>
            <div class="container-fluid">
                <h4><i class="fas fa-truck text-primary"></i> Delivery Management</h4>
                <p>Monitor and schedule vendor deliveries.</p>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-muted">Upcoming Deliveries</h5>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm"><i class="fas fa-truck"></i> Schedule Delivery</button>
                                <button class="btn btn-outline-primary btn-sm"><i class="fas fa-eye"></i> View Delivery Status</button>
                            </div>
                        </div>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Delivery ID</th>
                                    <th>Order ID</th>
                                    <th>Customer Name</th>
                                    <th>Delivery Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>DEL-485</td>
                                    <td>ORD-2846</td>
                                    <td>Modern Electricals</td>
                                    <td>14 Apr 2025</td>
                                    <td><span class="green-bg">Scheduled</span></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-eye"></i></button>
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-pen-to-square"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>DEL-484</td>
                                    <td>ORD-2840</td>
                                    <td>City Lights</td>
                                    <td>13 Apr 2025</td>
                                    <td><span class="orange-bg">In Transit</span></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-eye"></i></button>
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-pen-to-square"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php elseif ($page === 'products'): ?>
            <div class="container-fluid">
                <h4><i class="fas fa-box text-primary"></i> Product Management</h4>
                <p>Manage your product inventory and stock levels.</p>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex gap-2">
                                <div class="input-group w-100 me-2">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control border-start-0" placeholder="Search products..." />
                                </div>
                                <button class="btn btn-outline-primary btn-sm">Filter</button>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm"><i class="fas fa-plus"></i> Add New Product</button>
                                <button class="btn btn-outline-primary btn-sm"><i class="fas fa-file-alt"></i> Generate Stock Report</button>
                            </div>
                        </div>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Stock</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>PROD-001</td>
                                    <td>1.5mm Wire</td>
                                    <td>Wires</td>
                                    <td>500 m</td>
                                    <td>₹50/m</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-pen-to-square"></i></button>
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-trash-can"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>PROD-002</td>
                                    <td>LED Bulb</td>
                                    <td>Lights</td>
                                    <td>200 units</td>
                                    <td>₹150/unit</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-pen-to-square"></i></button>
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-trash-can"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php elseif ($page === 'payments'): ?>
            <div class="container-fluid">
                <h4><i class="fas fa-wallet text-primary"></i> Payment Management</h4>
                <p>Track vendor payments and BNPL transactions.</p>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex gap-2">
                                <div class="input-group w-100 me-2">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control border-start-0" placeholder="Search payments..." />
                                </div>
                                <button class="btn btn-outline-primary btn-sm">Filter</button>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm"><i class="fas fa-user"></i> View Vendors</button>
                                <button class="btn btn-outline-primary btn-sm"><i class="fas fa-cog"></i> Set Credit Limit</button>
                                <button class="btn btn-outline-primary btn-sm"><i class="fas fa-bell"></i> Send Payment Reminder</button>
                            </div>
                        </div>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Vendor</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>TRX-001</td>
                                    <td>Modern Electricals</td>
                                    <td>₹36,500</td>
                                    <td>10 Apr 2025</td>
                                    <td><span class="green-bg">Paid</span></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-eye"></i></button>
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-download"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>TRX-002</td>
                                    <td>City Lights</td>
                                    <td>₹43,250</td>
                                    <td>08 Apr 2025</td>
                                    <td><span class="red-bg">Overdue</span></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-eye"></i></button>
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-download"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="mt-3">
                            <h5 class="text-muted">BNPL Overview</h5>
                            <p>Outstanding: ₹2,85,450 | Interest Accrued: ₹5,250</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($page === 'invoices'): ?>
            <div class="container-fluid">
                <h4><i class="fas fa-file-invoice text-primary"></i> Invoice Management</h4>
                <p>Create and track GST & Non-GST invoices.</p>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex gap-2">
                                <div class="input-group w-100 me-2">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control border-start-0" placeholder="Search invoices..." />
                                </div>
                                <button class="btn btn-outline-primary btn-sm">Filter</button>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm"><i class="fas fa-plus"></i> Generate Invoice</button>
                                <button class="btn btn-outline-primary btn-sm"><i class="fas fa-download"></i> Download Invoice</button>
                            </div>
                        </div>
                        <div class="d-flex justify-content-start gap-2 mb-3">
                            <button class="btn btn-outline-primary btn-sm">All</button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-check-circle text-success"></i> Paid</button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-clock text-warning"></i> Pending</button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-exclamation-circle text-danger"></i> Overdue</button>
                        </div>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Invoice ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>INV-3845</td>
                                    <td>Unnati Traders</td>
                                    <td>₹36,500</td>
                                    <td>12 Apr 2025</td>
                                    <td><span class="green-bg">Paid</span></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-eye"></i></button>
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-pen-to-square"></i></button>
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-download"></i></button>
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-print"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>INV-3844</td>
                                    <td>City Lights</td>
                                    <td>₹24,500</td>
                                    <td>10 Apr 2025</td>
                                    <td><span class="orange-bg">Pending</span></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-eye"></i></button>
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-pen-to-square"></i></button>
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-download"></i></button>
                                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-print"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php elseif ($page === 'reports'): ?>
            <div class="container-fluid">
                <h4><i class="fas fa-chart-bar text-primary"></i> Reports & Analytics</h4>
                <p>Analyze sales, BNPL, and financial performance.</p>
                <div class="chart-container">
                    <div class="chart-box">
                        <h3>Sales Trends</h3>
                        <canvas id="salesChart"></canvas>
                    </div>
                    <div class="chart-box">
                        <h3>BNPL Recovery</h3>
                        <p>Outstanding: ₹2,85,450 | Recovered: ₹1,50,000</p>
                        <div class="d-flex gap-2 mt-3">
                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-chart-bar"></i> Generate Sales Report</button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-file-alt"></i> View Profit & Loss</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($page === 'settings'): ?>
            <div class="container-fluid">
                <h4><i class="fas fa-cog text-primary"></i> Settings</h4>
                <p>Configure vendor profile and system preferences.</p>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="text-muted">Vendor Profile</h5>
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
            </div>
        <?php else: ?>
            <div class="container-fluid">
                <h1>Dashboard</h1>
                <p>Welcome back to your vendor management dashboard</p>
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
                            <div class="card-body">
                                <h6 class="text-muted">Active Orders</h6>
                                <h3 class="fw-bold">16 orders</h3>
                                <p class="text-success">+3 vs last month</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
                            <div class="card-body">
                                <h6 class="text-muted">Pending Deliveries</h6>
                                <h3 class="fw-bold">8 deliveries</h3>
                                <p class="text-warning">+2 vs last month</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
                            <div class="card-body">
                                <h6 class="text-muted">Pending Payments</h6>
                                <h3 class="fw-bold">₹2,85,450</h3>
                                <p class="text-danger">+12.5% vs last month</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
                            <div class="card-body">
                                <h6 class="text-muted">This Month Revenue</h6>
                                <h3 class="fw-bold">₹4,35,250</h3>
                                <p class="text-success">+8.7% vs last month</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="chart-container">
                    <div class="chart-box">
                        <h3>Order Trends (Last 6 Months)</h3>
                        <canvas id="orderTrendsChart"></canvas>
                    </div>
                    <div class="chart-box">
                        <h3>Recent Activity</h3>
                        <div class="alert alert-primary">
                            <i class="fas fa-bell"></i> New Order #ORD-2854 received
                            <a href="#" class="alert-link">View Details</a>
                        </div>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Payment overdue for #INV-3845
                            <a href="#" class="alert-link">Send Reminder</a>
                        </div>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> Delivery #DEL-482 completed
                            <a href="#" class="alert-link">View Status</a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12 my-4">
                        <div class="card stat-card cards shadow-sm" style="background-color: #fbf3d7;">
                            <div class="card-body">
                                <h5 class="text-muted">Low Stock Alert</h5>
                                <p>2 products are below minimum stock levels. Review inventory soon.</p>
                                <a href="?page=products" style="text-decoration: none;" class="text-dark">View Products →</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 my-4">
                        <div class="card stat-card cards shadow-sm" style="background-color: #d4ffe9;">
                            <div class="card-body">
                                <h5 class="text-muted">Recent Payments</h5>
                                <p>3 payments received today totaling ₹28,450.</p>
                                <a href="?page=payments" style="text-decoration: none;" class="text-dark">View Payments →</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        <?php if ($page === 'dashboard'): ?>
            const orderTrendsCtx = document.getElementById('orderTrendsChart').getContext('2d');
            new Chart(orderTrendsCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Orders',
                        data: [10, 15, 7, 12, 9, 6],
                        backgroundColor: '#0d6efd',
                        borderColor: '#0d6efd',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        <?php endif; ?>
        <?php if ($page === 'reports'): ?>
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Sales (₹)',
                        data: [50000, 75000, 60000, 80000, 70000, 90000],
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
                        y: { beginAtZero: true }
                    }
                }
            });
        <?php endif; ?>
    </script>
</body>
</html>