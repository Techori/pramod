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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../public/css/styles.css">
    <style>    </style>
</head>

<body>
    <?php include '_vendor_nav.php'; ?>
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
                                <a href="?page=products" style="text-decoration: none;" class="text-dark">View Products
                                    →</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 mb-4">
                        <div class="card stat-card cards shadow-sm" style="background-color: #d4ffe9;">
                            <div class="card-body">
                                <h5 class="text-muted">Recent Payments</h5>
                                <p>3 payments received today totaling ₹28,450.</p>
                                <a href="?page=payments" style="text-decoration: none;" class="text-dark">View Payments
                                    →</a>
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
                                <div
                                    class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                    <div class="input-group w-auto flex-grow-1" style="max-width: 300px;">
                                        <span class="input-group-text bg-light border-end-0"><i
                                                class="fas fa-search"></i></span>
                                        <input type="text" class="form-control border-start-0"
                                            placeholder="Search invoices..." />
                                    </div>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button class="btn btn-outline-primary btn-sm">All</button>
                                        <button class="btn btn-outline-primary btn-sm"><i
                                                class="fas fa-check-circle text-success me-1"></i> Paid</button>
                                        <button class="btn btn-outline-primary btn-sm"><i
                                                class="fas fa-clock text-warning me-1"></i> Pending</button>
                                        <button class="btn btn-outline-primary btn-sm"><i
                                                class="fas fa-exclamation-circle text-danger me-1"></i> Overdue</button>
                                    </div>
                                    <button class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i> Create
                                        Invoice</button>
                                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#generateInvoiceModal">
                                        <i class="fas fa-plus"></i> Generate Invoice
                                    </button>
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
                                                            <button class="btn btn-outline-primary btn-sm" title="View"><i
                                                                    class="fas fa-eye"></i></button>
                                                            <button class="btn btn-outline-primary btn-sm" title="Edit"><i
                                                                    class="fas fa-pen-to-square"></i></button>
                                                            <button class="btn btn-outline-primary btn-sm" title="Download"
                                                                onclick="alert('Downloading Document: <?php echo htmlspecialchars($invoice['id']); ?>')"><i
                                                                    class="fas fa-download"></i></button>
                                                            <button class="btn btn-outline-primary btn-sm" title="Print"
                                                                onclick="alert('Printing Document: <?php echo htmlspecialchars($invoice['id']); ?>')"><i
                                                                    class="fas fa-print"></i></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center mt-4">
                                    <a href="#" class="btn btn-outline-primary btn-sm">View All Invoices <i
                                            class="fas fa-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="payments">
                                <div
                                    class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                    <div class="input-group w-auto flex-grow-1" style="max-width: 300px;">
                                        <span class="input-group-text bg-light border-end-0"><i
                                                class="fas fa-search"></i></span>
                                        <input type="text" class="form-control border-start-0"
                                            placeholder="Search payments..." />
                                    </div>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button class="btn btn-outline-primary btn-sm">All Payments</button>
                                        <button class="btn btn-outline-primary btn-sm">This Month</button>
                                        <button class="btn btn-outline-primary btn-sm">Last Month</button>
                                    </div>
                                    <button class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i> Record
                                        Payment</button>
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
                                                            <button class="btn btn-outline-primary btn-sm" title="View"><i
                                                                    class="fas fa-eye"></i></button>
                                                            <button class="btn btn-outline-primary btn-sm" title="Download"
                                                                onclick="alert('Downloading Document: <?php echo htmlspecialchars($payment['id']); ?>')"><i
                                                                    class="fas fa-download"></i></button>
                                                            <button class="btn btn-outline-primary btn-sm" title="Print"
                                                                onclick="alert('Printing Document: <?php echo htmlspecialchars($payment['id']); ?>')"><i
                                                                    class="fas fa-print"></i></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center mt-4">
                                    <a href="#" class="btn btn-outline-primary btn-sm">View Payment History <i
                                            class="fas fa-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="quickbill">
                                <div class="row">
                                    <div class="col-md-8 mb-4">
                                        <div class="card shadow-sm cards card-border"
                                            style="border-left: 5px solid #198754;">
                                            <div class="card-body">
                                                <h5 class="card-title">Quick Bill</h5>
                                                <form id="quickBillForm" onsubmit="handleQuickBillSubmit(event)">
                                                    <div class="mb-3">
                                                        <label for="billTotal" class="form-label">Total Amount</label>
                                                        <input type="text" class="form-control" id="billTotal"
                                                            placeholder="Enter total amount" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary btn-sm"><i
                                                            class="fas fa-receipt me-1"></i> Generate Bill</button>
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
                                        <div class="card shadow-sm cards card-border"
                                            style="border-left: 5px solid #ffc107;">
                                            <div class="card-body">
                                                <h5 class="card-title d-flex align-items-center">
                                                    <i class="fas fa-receipt me-2 text-warning"></i> Recent Quick Bills
                                                </h5>
                                                <div class="mt-3" style="max-height: 400px; overflow-y: auto;">
                                                    <?php foreach (get_recent_bills() as $bill): ?>
                                                        <div class="border rounded p-3 mb-2 hover-bg-light">
                                                            <div class="d-flex justify-content-between">
                                                                <div>
                                                                    <p class="fw-medium mb-1">
                                                                        <?php echo htmlspecialchars($bill['id']); ?>
                                                                    </p>
                                                                    <p class="text-muted small">
                                                                        <?php echo htmlspecialchars($bill['customer']); ?>
                                                                    </p>
                                                                </div>
                                                                <span
                                                                    class="text-success fw-semibold"><?php echo htmlspecialchars($bill['amount']); ?></span>
                                                            </div>
                                                            <div class="d-flex justify-content-between text-muted small">
                                                                <span><?php echo htmlspecialchars($bill['date']); ?></span>
                                                                <span><?php echo htmlspecialchars($bill['items']); ?> items •
                                                                    <?php echo htmlspecialchars($bill['method']); ?></span>
                                                            </div>
                                                            <div class="d-flex gap-2 mt-2">
                                                                <button class="btn btn-outline-primary btn-sm"
                                                                    onclick="alert('Printing Document: <?php echo htmlspecialchars($bill['id']); ?>')"><i
                                                                        class="fas fa-print me-1"></i> Print</button>
                                                                <button class="btn btn-outline-primary btn-sm"
                                                                    onclick="alert('Downloading Document: <?php echo htmlspecialchars($bill['id']); ?>')"><i
                                                                        class="fas fa-download me-1"></i> Download</button>
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
                                        <div class="card shadow-sm cards card-border"
                                            style="border-left: 5px solid #0d6efd;">
                                            <div class="card-body">
                                                <h5 class="card-title">Monthly Revenue</h5>
                                                <div class="d-flex align-items-center justify-content-center bg-light rounded"
                                                    style="height: 192px;">
                                                    <p class="text-muted">Revenue Chart Placeholder</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="card shadow-sm cards card-border"
                                            style="border-left: 5px solid #6f42c1;">
                                            <div class="card-body">
                                                <h5 class="card-title">Payment Methods</h5>
                                                <div class="d-flex align-items-center justify-content-center bg-light rounded"
                                                    style="height: 192px;">
                                                    <p class="text-muted">Payment Methods Chart Placeholder</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card shadow-sm cards card-border"
                                            style="border-left: 5px solid #dc3545;">
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
                                                                    <td><?php echo htmlspecialchars($payment['customer']); ?>
                                                                    </td>
                                                                    <td><?php echo htmlspecialchars($payment['amount']); ?></td>
                                                                    <td><?php echo htmlspecialchars($payment['due_date']); ?>
                                                                    </td>
                                                                    <td
                                                                        class="<?php echo $payment['days_overdue'] !== '-' ? 'text-danger' : ''; ?>">
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
                                    <a href="?page=orders"><button class="btn btn-outline-primary btn-sm"><i
                                                class="fas fa-plus"></i> Add New Order</button></a>
                                    <a href="?page=deliveries"><button class="btn btn-outline-primary btn-sm"><i
                                                class="fas fa-truck"></i> Schedule Delivery</button></a>
                                    <button class="btn btn-outline-primary btn-sm"><i class="fas fa-file-invoice"></i>
                                        Generate Invoice</button>
                                    <button class="btn btn-outline-primary btn-sm"><i class="fas fa-bell"></i>
                                        Payments</button>
                                    <button class="btn btn-outline-primary btn-sm"><i class="fas fa-chart-line"></i> View
                                        Reports</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($page === 'orders'): ?>
            <?php include 'orders.php'; ?>

        <?php elseif ($page === 'deliveries'): ?>
            <?php include 'deliveries.php'; ?>

        <?php elseif ($page === 'products'): ?>
            <?php include 'products.php'; ?>

        <?php elseif ($page === 'payments'): ?>
            <?php include 'payments.php'; ?>

        <?php elseif ($page === 'invoices'): ?>
            <?php include 'invoices.php'; ?>

        <?php elseif ($page === 'reports'): ?>
            <?php include 'reports.php'; ?>

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
                                <a href="?page=products" style="text-decoration: none;" class="text-dark">View Products
                                    →</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 mb-4">
                        <div class="card stat-card cards shadow-sm" style="background-color: #d4ffe9;">
                            <div class="card-body">
                                <h5 class="text-muted">Recent Payments</h5>
                                <p>3 payments received today totaling ₹28,450.</p>
                                <a href="?page=payments" style="text-decoration: none;" class="text-dark">View Payments
                                    →</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <!-- Generate Invoice Modal -->
    <div class="modal fade" id="generateInvoiceModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate New Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="invoiceForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Invoice Type</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="invoiceType" id="gstInvoice"
                                            value="gst" checked>
                                        <label class="form-check-label" for="gstInvoice">GST Invoice</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="invoiceType"
                                            id="nonGstInvoice" value="non-gst">
                                        <label class="form-check-label" for="nonGstInvoice">Non-GST Invoice</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Invoice Date</label>
                                <input type="date" class="form-control" id="invoiceDate" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Customer Name</label>
                                <input type="text" class="form-control" id="customerName" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Customer GSTIN</label>
                                <input type="text" class="form-control" id="customerGstin">
                            </div>
                        </div>

                        <div id="invoiceItems">
                            <h6>Items</h6>
                            <div class="item-row row mb-2">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" placeholder="Item Name" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" class="form-control quantity" placeholder="Qty" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" class="form-control price" placeholder="Price" required>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" class="form-control gst-rate" placeholder="GST %" required>
                                </div>
                                <div class="col-md-2">
                                    <span class="item-total">₹0.00</span>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addInvoiceItem()">
                            <i class="fas fa-plus"></i> Add Item
                        </button>

                        <div class="row mt-3">
                            <div class="col-md-6 offset-md-6">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span id="subtotal">₹0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>GST:</span>
                                    <span id="totalGst">₹0.00</span>
                                </div>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Total:</span>
                                    <span id="grandTotal">₹0.00</span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="generateInvoice()">Generate Invoice</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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

    </script>
</body>

</html>