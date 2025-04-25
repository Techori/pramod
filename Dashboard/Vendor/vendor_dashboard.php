<?php
session_start();

if (!(isset($_SESSION["uid"]) && isset($_SESSION["user_type"]) && isset($_SESSION["session_id"]))) {
    header("location:../../login.php");
    exit;
} else {
    if (in_array($_SESSION["user_type"], ['Factory', 'Store', 'Admin'])) {
        header("location:../index.php");
        exit;
    } else if (!($_SESSION["user_type"] == 'Vendor')) {
        header("location:../../login.php");
        exit;
    }
}
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Include the mock database and can be replaced with actual database connection
// and queries in the future in the databse.php file.
require_once 'database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Dashboard - Shree Unnati Wires & Traders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
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
            overflow-x: hidden;
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
            transition: transform 0.3s ease-in-out;
            z-index: 1000;
            box-shadow: -5px 0 15px rgba(233, 111, 3, 0.78), 0 2px 6px rgba(0, 0, 0, 0.05);
            animation: glowingEffect 2.5s infinite;
        }
        .sidebar-header {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #ddd;
        }
        .sidebar-header img {
            width: 50px;
            height: auto;
            margin-bottom: 10px;
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
            transition: margin-left 0.3s ease-in-out;
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
            transition: margin-left 0.3s ease-in-out;
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
            font-size: 0.9rem;
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
        .blue-bg {
            background-color: #d1e7ff;
            color: #004085;
            padding: 4px 10px;
            border-radius: 10px;
        }
        .purple-bg {
            background-color: #e2d9f3;
            color: #4c2889;
            padding: 4px 10px;
            border-radius: 10px;
        }
        .alert {
            border-radius: 0.5rem;
            padding: 15px;
            font-size: 0.9rem;
        }
        .hamburger {
            display: none;
            font-size: 1.5rem;
            background: none;
            border: none;
            color: #0d6efd;
            cursor: pointer;
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
                box-shadow: -5px 0 15px rgba(233, 111, 3, 0.78), 0 0 25px rgba(0, 0, 0, 0.95);
                animation: glowingEffect 2.5s infinite;
            }
            main, header.header {
                margin-left: 0;
            }
            .hamburger {
                display: block;
            }
            .overlay.show {
                display: block;
            }
            .container-fluid {
                padding-left: 10px;
                padding-right: 10px;
            }
            .chart-box {
                flex: 1 1 100%;
            }
            th, td {
                font-size: 0.85rem;
                padding: 8px;
            }
            .table-responsive {
                overflow-x: auto;
            }
            .card-body {
                padding: 15px;
            }
            .alert {
                font-size: 0.85rem;
            }
            .btn-sm {
                font-size: 0.8rem;
                padding: 5px 10px;
            }
        }
        .notification-dropdown {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .notification-item {
            transition: background-color 0.2s ease;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .notification-item:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .notification-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="../../public/unnati_logo.png" alt="Logo" class="img-fluid" style="width: auto; height: auto;">
            <h6 class="mb-0">Unnati Vendor Portal</h6>
            <small class="text-muted" style="font-size: 0.8rem;">Manage your business</small>
        </div>
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

    <!-- Overlay for Mobile -->
    <div class="overlay" id="overlay"></div>

    <!-- Header -->
    <header class="header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <button class="hamburger me-3" id="hamburger"><i class="fas fa-bars"></i></button>
            <h5 class="mb-0 fw-bold">Hey! <?php echo isset($_SESSION['user']) ? $_SESSION['user'] : 'Vendor'; ?></h5>
        </div>
        <form class="d-flex" role="search" method="GET" action="search.php">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                <input class="form-control border-start-0" type="search" placeholder="Search orders, products, or invoices..." aria-label="Search">
            </div>
        </form>
        <div class="d-flex align-items-center">
            <div class="dropdown me-2">
                <button class="btn btn-outline-primary btn-sm position-relative" data-bs-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    <?php
                    $notifications = get_notifications();
                    $unread = array_filter($notifications, function($n) { return !$n['read']; });
                    if (count($unread) > 0):
                    ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?php echo count($unread); ?>
                        <span class="visually-hidden">unread notifications</span>
                    </span>
                    <?php endif; ?>
                </button>
                <div class="dropdown-menu dropdown-menu-end notification-dropdown p-0" style="width: 320px; max-height: 400px; overflow-y: auto;">
                    <div class="p-2 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Notifications</h6>
                        <?php if (count($unread) > 0): ?>
                        <button class="btn btn-link btn-sm text-decoration-none">Mark all read</button>
                        <?php endif; ?>
                    </div>
                    <div class="notifications-list">
                        <?php foreach ($notifications as $notification): ?>
                        <div class="dropdown-item notification-item p-2 <?php echo $notification['read'] ? 'bg-light' : ''; ?>">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <span class="fa-stack fa-sm">
                                        <i class="fas fa-circle fa-stack-2x text-<?php echo $notification['color']; ?> opacity-25"></i>
                                        <i class="fas <?php echo $notification['icon']; ?> fa-stack-1x text-<?php echo $notification['color']; ?>"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <h6 class="mb-0 fw-semibold"><?php echo htmlspecialchars($notification['title']); ?></h6>
                                    <p class="mb-0 small"><?php echo htmlspecialchars($notification['message']); ?></p>
                                    <small class="text-muted"><?php echo htmlspecialchars($notification['time']); ?></small>
                                </div>
                                <?php if (!$notification['read']): ?>
                                <div class="flex-shrink-0 ms-2">
                                    <span class="badge bg-primary rounded-pill">New</span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="p-2 border-top text-center">
                        <a href="#" class="text-decoration-none small">View all notifications</a>
                    </div>
                </div>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-user-circle"></i></button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <small class="text-muted text-center d-block" style="font-size: 0.8rem;"><?php echo isset($_SESSION['user_email']) ? $_SESSION['user_email'] : 'vendor@unnati.com'; ?></small>
                    <li><a class="dropdown-item" href="?page=settings">Update Profile</a></li>
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
                    <div class="col-md-6 col-sm-12 mb-4">
                        <div class="card stat-card cards shadow-sm" style="background-color: #fbf3d7;">
                            <div class="card-body">
                                <h5 class="text-muted">Low Stock Alert</h5>
                                <p>2 products are below minimum stock levels. Review inventory soon.</p>
                                <a href="?page=products" style="text-decoration: none;" class="text-dark">View Products →</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 mb-4">
                        <div class="card stat-card cards shadow-sm" style="background-color: #d4ffe9;">
                            <div class="card-body">
                                <h5 class="text-muted">Recent Payments</h5>
                                <p>3 payments received today totaling ₹28,450.</p>
                                <a href="?page=payments" style="text-decoration: none;" class="text-dark">View Payments →</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm cards card-border" style="border-left: 5px solid #0d6efd;">
                    <div class="card-body">
                        <h5 class="card-title d-flex align-items-center">
                            <i class="fas fa-file-text me-2 text-primary"></i> Billing & Invoice Management
                        </h5>
                        <p class="card-text text-muted">Create, track, and manage invoices and payments</p>
                        <ul class="nav nav-tabs mb-4">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#invoices">Invoices</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#payments">Payments</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#quickbill">Quick Bill</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#reports">Reports</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="invoices">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                    <div class="input-group w-auto flex-grow-1" style="max-width: 300px;">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control border-start-0" placeholder="Search invoices..." />
                                    </div>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button class="btn btn-outline-primary btn-sm">All</button>
                                        <button class="btn btn-outline-primary btn-sm"><i class="fas fa-check-circle text-success me-1"></i> Paid</button>
                                        <button class="btn btn-outline-primary btn-sm"><i class="fas fa-clock text-warning me-1"></i> Pending</button>
                                        <button class="btn btn-outline-primary btn-sm"><i class="fas fa-exclamation-circle text-danger me-1"></i> Overdue</button>
                                    </div>
                                    <button class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i> Create Invoice</button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Invoice #</th>
                                                <th>Customer</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach (get_invoices() as $invoice): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($invoice['id']); ?></td>
                                                    <td><?php echo htmlspecialchars($invoice['customer']); ?></td>
                                                    <td><?php echo htmlspecialchars($invoice['amount']); ?></td>
                                                    <td>
                                                        <span class="<?php
                                                            echo $invoice['status'] === 'Paid' ? 'green-bg' :
                                                                 ($invoice['status'] === 'Pending' ? 'orange-bg' : 'red-bg');
                                                        ?>">
                                                            <?php echo htmlspecialchars($invoice['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($invoice['date']); ?></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button class="btn btn-outline-primary btn-sm" title="View"><i class="fas fa-eye"></i></button>
                                                            <button class="btn btn-outline-primary btn-sm" title="Edit"><i class="fas fa-pen-to-square"></i></button>
                                                            <button class="btn btn-outline-primary btn-sm" title="Download" onclick="alert('Downloading Document: <?php echo htmlspecialchars($invoice['id']); ?>')"><i class="fas fa-download"></i></button>
                                                            <button class="btn btn-outline-primary btn-sm" title="Print" onclick="alert('Printing Document: <?php echo htmlspecialchars($invoice['id']); ?>')"><i class="fas fa-print"></i></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center mt-4">
                                    <a href="#" class="btn btn-outline-primary btn-sm">View All Invoices <i class="fas fa-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="payments">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                    <div class="input-group w-auto flex-grow-1" style="max-width: 300px;">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control border-start-0" placeholder="Search payments..." />
                                    </div>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button class="btn btn-outline-primary btn-sm">All Payments</button>
                                        <button class="btn btn-outline-primary btn-sm">This Month</button>
                                        <button class="btn btn-outline-primary btn-sm">Last Month</button>
                                    </div>
                                    <button class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i> Record Payment</button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Payment ID</th>
                                                <th>Invoice</th>
                                                <th>Customer</th>
                                                <th>Amount</th>
                                                <th>Method</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach (get_payments() as $payment): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($payment['id']); ?></td>
                                                    <td><?php echo htmlspecialchars($payment['invoice']); ?></td>
                                                    <td><?php echo htmlspecialchars($payment['customer']); ?></td>
                                                    <td><?php echo htmlspecialchars($payment['amount']); ?></td>
                                                    <td><?php echo htmlspecialchars($payment['method']); ?></td>
                                                    <td><?php echo htmlspecialchars($payment['date']); ?></td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button class="btn btn-outline-primary btn-sm" title="View"><i class="fas fa-eye"></i></button>
                                                            <button class="btn btn-outline-primary btn-sm" title="Download" onclick="alert('Downloading Document: <?php echo htmlspecialchars($payment['id']); ?>')"><i class="fas fa-download"></i></button>
                                                            <button class="btn btn-outline-primary btn-sm" title="Print" onclick="alert('Printing Document: <?php echo htmlspecialchars($payment['id']); ?>')"><i class="fas fa-print"></i></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center mt-4">
                                    <a href="#" class="btn btn-outline-primary btn-sm">View Payment History <i class="fas fa-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="quickbill">
                                <div class="row">
                                    <div class="col-md-8 mb-4">
                                        <div class="card shadow-sm cards card-border" style="border-left: 5px solid #198754;">
                                            <div class="card-body">
                                                <h5 class="card-title">Quick Bill</h5>
                                                <form id="quickBillForm" onsubmit="handleQuickBillSubmit(event)">
                                                    <div class="mb-3">
                                                        <label for="billTotal" class="form-label">Total Amount</label>
                                                        <input type="text" class="form-control" id="billTotal" placeholder="Enter total amount" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-receipt me-1"></i> Generate Bill</button>
                                                </form>
                                                <script>
                                                    function handleQuickBillSubmit(event) {
                                                        event.preventDefault();
                                                        const total = document.getElementById('billTotal').value;
                                                        alert(`Quick Bill Generated: Bill for ₹${total} created successfully.`);
                                                        document.getElementById('quickBillForm').reset();
                                                    }
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <div class="card shadow-sm cards card-border" style="border-left: 5px solid #ffc107;">
                                            <div class="card-body">
                                                <h5 class="card-title d-flex align-items-center">
                                                    <i class="fas fa-receipt me-2 text-warning"></i> Recent Quick Bills
                                                </h5>
                                                <div class="mt-3" style="max-height: 400px; overflow-y: auto;">
                                                    <?php foreach (get_recent_bills() as $bill): ?>
                                                        <div class="border rounded p-3 mb-2 hover-bg-light">
                                                            <div class="d-flex justify-content-between">
                                                                <div>
                                                                    <p class="fw-medium mb-1"><?php echo htmlspecialchars($bill['id']); ?></p>
                                                                    <p class="text-muted small"><?php echo htmlspecialchars($bill['customer']); ?></p>
                                                                </div>
                                                                <span class="text-success fw-semibold"><?php echo htmlspecialchars($bill['amount']); ?></span>
                                                            </div>
                                                            <div class="d-flex justify-content-between text-muted small">
                                                                <span><?php echo htmlspecialchars($bill['date']); ?></span>
                                                                <span><?php echo htmlspecialchars($bill['items']); ?> items • <?php echo htmlspecialchars($bill['method']); ?></span>
                                                            </div>
                                                            <div class="d-flex gap-2 mt-2">
                                                                <button class="btn btn-outline-primary btn-sm" onclick="alert('Printing Document: <?php echo htmlspecialchars($bill['id']); ?>')"><i class="fas fa-print me-1"></i> Print</button>
                                                                <button class="btn btn-outline-primary btn-sm" onclick="alert('Downloading Document: <?php echo htmlspecialchars($bill['id']); ?>')"><i class="fas fa-download me-1"></i> Download</button>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="reports">
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <div class="card shadow-sm cards card-border" style="border-left: 5px solid #0d6efd;">
                                            <div class="card-body">
                                                <h5 class="card-title">Monthly Revenue</h5>
                                                <div class="d-flex align-items-center justify-content-center bg-light rounded" style="height: 192px;">
                                                    <p class="text-muted">Revenue Chart Placeholder</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="card shadow-sm cards card-border" style="border-left: 5px solid #6f42c1;">
                                            <div class="card-body">
                                                <h5 class="card-title">Payment Methods</h5>
                                                <div class="d-flex align-items-center justify-content-center bg-light rounded" style="height: 192px;">
                                                    <p class="text-muted">Payment Methods Chart Placeholder</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card shadow-sm cards card-border" style="border-left: 5px solid #dc3545;">
                                            <div class="card-body">
                                                <h5 class="card-title">Outstanding Payments</h5>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>Invoice #</th>
                                                                <th>Customer</th>
                                                                <th>Amount</th>
                                                                <th>Due Date</th>
                                                                <th>Days Overdue</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach (get_outstanding_payments() as $payment): ?>
                                                                <tr>
                                                                    <td><?php echo htmlspecialchars($payment['id']); ?></td>
                                                                    <td><?php echo htmlspecialchars($payment['customer']); ?></td>
                                                                    <td><?php echo htmlspecialchars($payment['amount']); ?></td>
                                                                    <td><?php echo htmlspecialchars($payment['due_date']); ?></td>
                                                                    <td class="<?php echo $payment['days_overdue'] !== '-' ? 'text-danger' : ''; ?>">
                                                                        <?php echo htmlspecialchars($payment['days_overdue']); ?>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <h5 class="text-muted">Quick Actions</h5>
                    <p>Manage your orders, deliveries, and payments quickly.</p>
                    <div class="col-md-12 col-sm-12 mb-4">
                        <div class="card stat-card cards shadow-sm" style="background-color: #f8d7da;">
                            <div class="card-body">
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="?page=orders"><button class="btn btn-outline-primary btn-sm"><i class="fas fa-plus"></i> Add New Order</button></a>
                                    <a href="?page=deliveries"><button class="btn btn-outline-primary btn-sm"><i class="fas fa-truck"></i> Schedule Delivery</button></a>
                                    <button class="btn btn-outline-primary btn-sm"><i class="fas fa-file-invoice"></i> Generate Invoice</button>
                                    <button class="btn btn-outline-primary btn-sm"><i class="fas fa-bell"></i> Payments</button>
                                    <button class="btn btn-outline-primary btn-sm"><i class="fas fa-chart-line"></i> View Reports</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($page === 'orders'): ?>
            <?php include 'orders.php'; ?>
        <?php elseif ($page === 'deliveries'): ?>
            <div class="container-fluid">
                <h4><i class="fas fa-truck text-primary"></i> Delivery Management</h4>
                <p>Monitor and schedule vendor deliveries.</p>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-muted">Upcoming Deliveries</h5>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#scheduleDeliveryModal"><i class="fas fa-truck"></i> Schedule Delivery</button>
                                <!-- Schedule Delivery Modal -->
                                <div class="modal fade" id="scheduleDeliveryModal" tabindex="-1" aria-labelledby="scheduleDeliveryModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="scheduleDeliveryModalLabel">Schedule New Delivery</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form id="deliveryForm">
                                                    <div class="mb-3">
                                                        <label for="orderSelect" class="form-label">Select Order</label>
                                                        <select class="form-select" id="orderSelect" required>
                                                            <option value="">Choose an order...</option>
                                                            <option value="ORD-2846">ORD-2846 - Modern Electricals</option>
                                                            <option value="ORD-2840">ORD-2840 - City Lights</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="deliveryDate" class="form-label">Delivery Date</label>
                                                        <input type="date" class="form-control" id="deliveryDate" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="timeSlot" class="form-label">Time Slot</label>
                                                        <select class="form-select" id="timeSlot" required>
                                                            <option value="">Choose time slot...</option>
                                                            <option value="morning">Morning (9 AM - 12 PM)</option>
                                                            <option value="afternoon">Afternoon (1 PM - 4 PM)</option>
                                                            <option value="evening">Evening (4 PM - 7 PM)</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="deliveryNotes" class="form-label">Delivery Notes</label>
                                                        <textarea class="form-control" id="deliveryNotes" rows="3" placeholder="Enter any special instructions..."></textarea>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary" onclick="scheduleDelivery()">Schedule Delivery</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    function scheduleDelivery() {
                                        const order = document.getElementById('orderSelect').value;
                                        const date = document.getElementById('deliveryDate').value;
                                        const timeSlot = document.getElementById('timeSlot').value;
                                        const notes = document.getElementById('deliveryNotes').value;
                                        if (!order || !date || !timeSlot) {
                                            alert('Please fill in all required fields');
                                            return;
                                        }
                                        alert('Delivery scheduled successfully!\nOrder: ' + order + '\nDate: ' + date + '\nTime Slot: ' + timeSlot);
                                        const modal = bootstrap.Modal.getInstance(document.getElementById('scheduleDeliveryModal'));
                                        modal.hide();
                                        document.getElementById('deliveryForm').reset();
                                    }
                                </script>
                                <button class="btn btn-outline-primary btn-sm"><i class="fas fa-eye"></i> View Delivery Status</button>
                            </div>
                        </div>
                        <div class="table-responsive">
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
                                    <?php foreach (get_deliveries() as $delivery): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($delivery['id']); ?></td>
                                            <td><?php echo htmlspecialchars($delivery['order_id']); ?></td>
                                            <td><?php echo htmlspecialchars($delivery['customer']); ?></td>
                                            <td><?php echo htmlspecialchars($delivery['date']); ?></td>
                                            <td><span class="<?php echo $delivery['status'] === 'Scheduled' ? 'green-bg' : 'orange-bg'; ?>">
                                                <?php echo htmlspecialchars($delivery['status']); ?>
                                            </span></td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-outline-primary btn-sm"><i class="fas fa-eye"></i></button>
                                                    <button class="btn btn-outline-primary btn-sm"><i class="fas fa-pen-to-square"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($page === 'products'): ?>
            <?php include 'products.php'; ?>

        <?php elseif ($page === 'payments'): ?>
            <div class="container-fluid">
                <h4><i class="fas fa-wallet text-primary"></i> Payment Management</h4>
                <p>Track vendor payments and BNPL transactions.</p>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <div class="d-flex gap-2 flex-grow-1">
                                <div class="input-group w-auto flex-grow-1">
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
                        <div class="table-responsive">
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
                                    <?php foreach (get_transactions() as $transaction): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($transaction['id']); ?></td>
                                            <td><?php echo htmlspecialchars($transaction['vendor']); ?></td>
                                            <td><?php echo htmlspecialchars($transaction['amount']); ?></td>
                                            <td><?php echo htmlspecialchars($transaction['date']); ?></td>
                                            <td><span class="<?php echo $transaction['status'] === 'Paid' ? 'green-bg' : 'red-bg'; ?>">
                                                <?php echo htmlspecialchars($transaction['status']); ?>
                                            </span></td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-outline-primary btn-sm"><i class="fas fa-eye"></i></button>
                                                    <button class="btn btn-outline-primary btn-sm"><i class="fas fa-download"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
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
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <div class="d-flex gap-2 flex-grow-1">
                                <div class="input-group w-auto flex-grow-1">
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
                        <div class="d-flex justify-content-start gap-2 mb-3 flex-wrap">
                            <button class="btn btn-outline-primary btn-sm">All</button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-check-circle text-success"></i> Paid</button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-clock text-warning"></i> Pending</button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-exclamation-circle text-danger"></i> Overdue</button>
                        </div>
                        <div class="table-responsive">
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
                                    <?php foreach (get_invoices_page() as $invoice): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($invoice['id']); ?></td>
                                            <td><?php echo htmlspecialchars($invoice['customer']); ?></td>
                                            <td><?php echo htmlspecialchars($invoice['amount']); ?></td>
                                            <td><?php echo htmlspecialchars($invoice['date']); ?></td>
                                            <td><span class="<?php echo $invoice['status'] === 'Paid' ? 'green-bg' : 'orange-bg'; ?>">
                                                <?php echo htmlspecialchars($invoice['status']); ?>
                                            </span></td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-outline-primary btn-sm"><i class="fas fa-eye"></i></button>
                                                    <button class="btn btn-outline-primary btn-sm"><i class="fas fa-pen-to-square"></i></button>
                                                    <button class="btn btn-outline-primary btn-sm"><i class="fas fa-download"></i></button>
                                                    <button class="btn btn-outline-primary btn-sm"><i class="fas fa-print"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
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
                        <div class="d-flex gap-2 mt-3 flex-wrap">
                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-chart-bar"></i> Generate Sales Report</button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fas fa-file-alt"></i> View Profit & Loss</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($page === 'settings'): ?>
            <?php include 'settings.php'; ?>
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
                    <div class="col-md-6 col-sm-12 mb-4">
                        <div class="card stat-card cards shadow-sm" style="background-color: #fbf3d7;">
                            <div class="card-body">
                                <h5 class="text-muted">Low Stock Alert</h5>
                                <p>2 products are below minimum stock levels. Review inventory soon.</p>
                                <a href="?page=products" style="text-decoration: none;" class="text-dark">View Products →</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 mb-4">
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
        // Sidebar Toggle
        const hamburger = document.getElementById('hamburger');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        hamburger.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        });

        // Close sidebar when clicking a nav link on mobile
        document.querySelectorAll('.sidebar nav a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('show');
                }
            });
        });

        // Chart.js Scripts
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
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false }
                    },
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
                    maintainAspectRatio: true,
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