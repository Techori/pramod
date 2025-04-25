<?php
session_start();
if (!(isset($_SESSION["uid"]) && isset($_SESSION["user_type"]) && isset($_SESSION["session_id"]))) {
    header("location:../../login.php");
    exit;
} else {
    if (in_array($_SESSION["user_type"] ,  ['Factory','Store','Vendor'])) {
        header("location:../index.php");
        exit;

    } else if (!($_SESSION["user_type"] == 'Admin')) {
        header("location:../../login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="unnati">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Shree Unnati Wires & Traders - Premium Wire Manufacturing</title>
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

        .tabs {
            display: flex;
            border-bottom: 2px solid #ddd;
        }
        .billingTab {
            padding: 10px 20px;
            cursor: pointer;
            border: none;
            background: none;
            font-size: 16px;
        }
        .billingTab.active {
            border-bottom: 3px solid #007bff;
            font-weight: bold;
            color: #007bff;
        }
        .billing-tab-content {
            display: none;
            padding: 20px 0;
        }
        .billing-tab-content.active {
            display: block;
        }
        .inventoryTab {
            padding: 10px 20px;
            cursor: pointer;
            border: none;
            background: none;
            font-size: 16px;
        }
        .inventoryTab.active {
            border-bottom: 3px solid #007bff;
            font-weight: bold;
            color: #007bff;
        }
        .inventory-tab-content {
            display: none;
            padding: 20px 0;
        }
        .inventory-tab-content.active {
            display: block;
        }
        .factoryTab {
            padding: 10px 20px;
            cursor: pointer;
            border: none;
            background: none;
            font-size: 16px;
        }
        .factoryTab.active {
            border-bottom: 3px solid #007bff;
            font-weight: bold;
            color: #007bff;
        }
        .factory-tab-content {
            display: none;
            padding: 20px 0;
        }
        .factory-tab-content.active {
            display: block;
        }
        .retailTab {
            padding: 10px 20px;
            cursor: pointer;
            border: none;
            background: none;
            font-size: 16px;
        }
        .retailTab.active {
            border-bottom: 3px solid #007bff;
            font-weight: bold;
            color: #007bff;
        }
        .retail-tab-content {
            display: none;
            padding: 20px 0;
        }
        .retail-tab-content.active {
            display: block;
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

    </style>
</head>
<body class="bg-secondary bg-opacity-10">
    <?php
        include('./_admin_nav.php');
    ?>

    <div class="main-content">
        <h1>Dashboard</h1>
        <p>Welcome back to your business management dashboard</p>
        

        <!-- Cards -->
        <div class="row">
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
                <div class="card-body">
                    <h6 class="text-muted">Total Sales</h6>
                    <h3 class="fw-bold">₹4,35,600</h3>
                    <p class="text-success">+12.5% vs last month</p>
                </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
                <div class="card-body">
                    <h6 class="text-muted">Inventory Value</h6>
                    <h3 class="fw-bold">₹12,45,230</h3>
                    <p class="text-success">+3.2% vs last month</p>
                </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
                <div class="card-body">
                    <h6 class="text-muted">BNPL Outstanding</h6>
                    <h3 class="fw-bold">₹85,450</h3>
                    <p class="text-danger">5.7% vs last month</p>
                </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
                <div class="card-body">
                    <h6 class="text-muted">Active Suppliers</h6>
                    <h3 class="fw-bold">34</h3>
                </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="chart-container">
            <div class="chart-box">
                <h3>Monthly Revenue Trend</h3>
                <canvas id="lineChart"></canvas>
            </div>
            <div class="chart-box">
                <h3>Sales by Category</h3>
                <div style="position: relative; width: 100%; max-width: 300px; margin: 0 auto;">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        <div class="row">
            <div class="col-md-6 col-sm-12 my-4">
                <div class="card stat-card cards shadow-sm" style="background-color:rgb(251, 243, 215);">
                    <div class="card-body">
                        <h5 class="text-muted">Low Stock Alert</h5>
                        <p>5 products are below minimum stock levels. Review inventory soon.</p>
                        <a href="@" style="text-decoration: none;" class="text-dark">View Inventory →</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-12 my-4">
                <div class="card stat-card cards shadow-sm" style="background-color:rgb(212, 255, 233);">
                    <div class="card-body">
                        <h5 class="text-muted">Inventory Value</h5>
                        <p>3 customer payments were received today totaling ₹28,450.</p>
                        <a href="@" style="text-decoration: none;" class="text-dark">View Inventory →</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Events -->
        <div class="col-md-12">
            <div class="card p-3 shadow-sm">
                <h5 class="mb-4">
                <i class="bi bi-calendar-event text-primary me-2"></i>
                <strong>Upcoming Events</strong>
                </h5>

                <div class="d-flex align-items-start border rounded mb-3 p-3" style="background-color:rgb(177, 202, 253);">
                    <div class="text-center me-3">
                        <div class="bg-primary text-white fw-bold rounded px-3 py-2">
                            <div style="font-size: 0.75rem;">APR</div>
                            <div style="font-size: 1.25rem;">15</div>
                        </div>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold text-primary">Supplier Meeting</h6>
                        <small class="text-muted">Review contracts with Havells India Ltd.</small>
                    </div>
                </div>

                <div class="d-flex align-items-start rounded mb-2 p-3" style="background-color:rgb(233, 221, 251);">
                    <div class="text-center me-3">
                        <div class="fw-bold rounded px-3 py-2" style="background-color:rgb(190, 146, 248);">
                            <div style="font-size: 0.75rem;">APR</div>
                            <div style="font-size: 1.25rem;">18</div>
                        </div>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold" style="color: #6f42c1;">Inventory Audit</h6>
                        <small class="text-muted" style="color: #6f42c1;">Quarterly inventory check at main warehouse</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Billing table -->
        <div class="col-md-12  card p-3 shadow-sm my-4 table-responsive">
            <h4><i class="bi bi-receipt-cutoff text-primary"></i> Billing & Invoice Management</h4>
            <p>Create, track, and manage invoices and payments</p>

            <div class="tabs">
                <button class="billingTab active" onclick="showBillingTab('invoices')">Invoices</button>
                <button class="billingTab" onclick="showBillingTab('payments')">Payments</button>
                <button class="billingTab" onclick="showBillingTab('quickbill')">Quick Bill</button>
                <button class="billingTab" onclick="showBillingTab('reports')">Reports</button>
            </div>

            <!-- Invoice table -->
            <div id="invoices" class="billing-tab-content active">
                <div class="container-fluid d-flex justify-content-between align-items-center">

                    <div class="d-flex justify-content-start">
                    <div class="input-group w-100">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control border-start-0" placeholder="Search..." />
                    </div>
                    </div>

                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-outline-primary">All</button>
                        <button class="btn btn-outline-primary"><i class="fa-regular fa-circle-check text-success"></i> Paid</button>
                        <button class="btn btn-outline-primary"><i class="fa-regular fa-clock text-warning"></i> Pending</button>
                        <button class="btn btn-outline-primary"><i class="fa-solid fa-circle-exclamation text-danger"></i> Overdue</button>
                    </div>

                    <div class="justify-contnt-end">
                    <button class="btn btn-outline-primary"><i class="fa-solid fa-plus"></i> Create Invoice</button>
                    </div>
                </div>
                <table id="Table" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Invoie #</th>
                            <th>Custome</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>INV-0001</td>
                            <td>Rajesh Electronics</td>
                            <td>₹24,500</td>
                            <td>Paid</td>
                            <td>11/04/2025</td>
                            <td><div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-eye"></i></button>
                                <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i></button>
                                <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-print"></i></button>

                            </div></td>
                        </tr>
                    </tbody>
                    <div id="pagination" class="mt-3 d-flex justify-content-center gap-2"></div>
                </table>
            </div>

            <!-- Payments table -->
            <div id="payments" class="billing-tab-content">
                <div class="container-fluid d-flex justify-content-between align-items-center">

                    <div class="d-flex justify-content-start">
                        <div class="input-group w-100">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-start-0" placeholder="Search..." />
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-outline-primary">All Payments</button>
                        <button class="btn btn-outline-primary">This Month</button>
                        <button class="btn btn-outline-primary">Last Month</button>
                    </div>

                    <div class="justify-contnt-end">
                        <button class="btn btn-outline-primary"><i class="fa-solid fa-plus"></i> Record Payment</button>
                    </div>
                </div>
                <table id="Table" class="table table-bordered table-hover">
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
                    <tr>
                        <td>PAY-0001</td>
                        <td>INV-0001</td>
                        <td>Rajesh Electronics</td>
                        <td>₹24,500</td>
                        <td>Bank Transfer</td>
                        <td>11/04/2025</td>
                        <td><div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-eye"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-print"></i></button>

                        </div></td>
                    </tr>
                </tbody>
                </table>
            </div>

            <!-- Quick bills -->
            <div id="quickbill" class="billing-tab-content">
            <p>Quick Bill Form Content</p>
            </div>

            <!-- Reports -->
            <div id="reports" class="billing-tab-content">
            <p>Reports Content</p>
            </div>
        </div>

        <!-- Inventory management -->
        <div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">
            <h4><i class="fa-solid fa-box text-primary"></i> Inventory Management</h4>
            <p>Track, monitor and manage your product inventory</p>

            <div class="tabs">
                <button class="inventoryTab active" onclick="showInventoryTab('products')">Products</button>
                <button class="inventoryTab" onclick="showInventoryTab('stock')">Stock Movement</button>
                <button class="inventoryTab" onclick="showInventoryTab('alerts')">Alerts</button>
            </div>

            <!-- Product  -->
            <div id="products" class="inventory-tab-content active">
                <div class="container-fluid d-flex justify-content-between align-items-center">

                
                    <div class="d-flex justify-content-start">
                        <div class="input-group w-100 me-2">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-start-0" placeholder="Search..." />
                        </div>
                        <button class="btn btn-outline-primary">Filter</button>
                    </div>


                    <div class="justify-contnt-end">
                    <button class="btn btn-outline-primary"><i class="fa-solid fa-plus"></i> Add Product</button>
                    </div>
                </div>
                <table id="Table" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Stock</th>
                            <th>Unit</th>
                            <th>Location</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>PROD-0001</td>
                            <td>Copper Wire (2.5mm)</td>
                            <td>Wires</td>
                            <td>1560</td>
                            <td>meters</td>
                            <td>Warehouse A</td>
                            <td>10/04/2025</td>
                            <td><div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-trash-can"></i></button>

                            </div></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Stock -->
            <div id="stock" class="inventory-tab-content">
                <div class="container-fluid d-flex justify-content-between align-items-center">

                    <div class="d-flex justify-content-start gap-2">
                        <button class="btn btn-outline-primary">Stock In</button>
                        <button class="btn btn-outline-primary">Stock Out</button>
                        <button class="btn btn-outline-primary">All Movements</button>
                    </div>

                    <div class="justify-contnt-end">
                        <button class="btn btn-outline-primary"><i class="fa-solid fa-plus"></i> Record Movement</button>
                    </div>
                </div>
                <table id="Table" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Date</th>
                        <th>Source</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>TRX-0001</td>
                        <td>Copper Wire (2.5mm)</td>
                        <td>Stock In</td>
                        <td>500</td>
                        <td>10/04/2025</td>
                        <td>Supplier Delivery</td>
                        <td>PO-0023</td>
                    </tr>
                </tbody>
                </table>
            </div>

            <!-- Alerts -->
            <div id="alerts" class="inventory-tab-content">
                <table id="Table" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                            <th>Min Stock</th>
                            <th>Unit</th>
                            <th>Location</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>PROD-0006</td>
                            <td>LED Bulb (9W)</td>
                            <td>Lighting</td>
                            <td>25</td>
                            <td>50</td>
                            <td>pieces</td>
                            <td>Warehouse C</td>
                            <td><button class="btn btn-outline-primary btn-sm">Order Now</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Factory management -->
        <div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">
            <h4><i class="fa-solid fa-industry text-primary"></i> Factory Production Management</h4>
            <p>Manage production orders, materials, and quality control</p>

            <div class="tabs">
                <button class="factoryTab active" onclick="showFactoryTab('production')">Production</button>
                <button class="factoryTab" onclick="showFactoryTab('material')">Raw Materials</button>
                <button class="factoryTab" onclick="showFactoryTab('quality')">Quality Control</button>
            </div>

            <!-- Production -->
            <div id="production" class="factory-tab-content active">
                <div class="container-fluid d-flex justify-content-between align-items-center">

                
                    <div class="d-flex gap-2 justify-content-start">
                        <button class="btn btn-outline-primary"><i class="fa-regular fa-clock text-warning"></i> Pending</button>
                        <button class="btn btn-outline-primary"><i class="fa-regular fa-circle-play"></i> In Progress</button>
                        <button class="btn btn-outline-primary"><i class="fa-regular fa-circle-check text-success"></i> Complete</button>
                    </div>


                    <div class="justify-contnt-end">
                    <button class="btn btn-outline-primary"><i class="fa-solid fa-plus"></i> New Production Order</button>
                    </div>
                </div>
                <table id="Table" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>PROD-0001</td>
                            <td>Copper Wire (2.5mm)</td>
                            <td>5,000 meters</td>
                            <td>In Progress</td>
                            <td>65%</td>
                            <td>08/04/2025</td>
                            <td>14/04/2025</td>
                            <td><div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-pause"></i> Pause</button>
                                <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-print"></i></button>

                            </div></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Raw material -->
            <div id="material" class="factory-tab-content">
                <div class="container-fluid d-flex justify-content-between align-items-center">

                    <div class="d-flex justify-content-start gap-2">
                        <button class="btn btn-outline-primary"><i class="fa-solid fa-triangle-exclamation text-warning"></i> Low Stock</button>
                        <button class="btn btn-outline-primary">All Materials</button>
                    </div>

                    <div class="d-flex gap-2 justify-contnt-end">
                        <button class="btn btn-outline-primary"><i class="fa-solid fa-box"></i> Oreder Material</button>
                        <button class="btn btn-outline-primary"><i class="fa-solid fa-plus"></i> Add Material</button>
                    </div>
                </div>
                <table id="Table" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Material</th>
                        <th>Current Stock</th>
                        <th>Unit</th>
                        <th>Reorder Level</th>
                        <th>Location</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>RM-0001</td>
                        <td>Copper (99.9%)</td>
                        <td>2,500</td>
                        <td>kg</td>
                        <td>500</td>
                        <td>Storage A</td>
                        <td><div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm">Update Stock</button>
                                <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-pen-to-square"></i></button>

                            </div></td>
                    </tr>
                </tbody>
                </table>
            </div>

            <!-- Quality -->
            <div id="quality" class="factory-tab-content">
                <div class="container-fluid d-flex justify-content-between align-items-center">

                    <div class="d-flex justify-content-start gap-2">
                        <button class="btn btn-outline-primary"><i class="fa-regular fa-circle-check text-success"></i> Passed</button>
                        <button class="btn btn-outline-primary"><i class="fa-solid fa-triangle-exclamation text-danger"></i> Failed</button>
                        <button class="btn btn-outline-primary">All Tests</button>
                    </div>

                    <div class="justify-contnt-end">
                        <button class="btn btn-outline-primary"><i class="fa-regular fa-clipboard"></i> New Quality Test</button>
                    </div>
                </div>
                <table id="Table" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Test ID</th>
                            <th>Product</th>
                            <th>Batch Number</th>
                            <th>Status</th>
                            <th>Tested By</th>
                            <th>Date</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>QC-0001</td>
                            <td>Copper Wire (2.5mm)</td>
                            <td>B20250408A</td>
                            <td>Passed</td>
                            <td>Rajiv Kumar</td>
                            <td>09/04/2025</td>
                            <td><a href="#" style="text-decoration: none;">View Details</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Retail stor management -->
        <div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">
            <h4><i class="fa-solid fa-store text-primary"></i> Retail Store Management</h4>
            <p>Manage sales, inventory, and customers in your retail store</p>

            <div class="tabs">
                <button class="retailTab active" onclick="showRetailTab('sales')">Sales</button>
                <button class="retailTab" onclick="showRetailTab('inventory')">Inventory</button>
                <button class="retailTab" onclick="showRetailTab('customers')">Customers</button>
            </div>

            <!-- Sales -->
            <div id="sales" class="retail-tab-content active">
                <div class="container-fluid d-flex justify-content-between align-items-center">

                
                    <div class="d-flex justify-content-start">
                        <div class="input-group w-100 me-2">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-start-0" placeholder="Search..." />
                        </div>
                        <button class="btn btn-outline-primary">Filter</button>
                    </div>


                    <div class="justify-contnt-end">
                    <button class="btn btn-outline-primary"><i class="fa-solid fa-cart-plus"></i> New Sales</button>
                    </div>
                </div>
                <table id="Table" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Sales ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>SL-0001</td>
                            <td>11/04/2025</td>
                            <td>Priya Sharma</td>
                            <td>3</td>
                            <td>₹8,750</td>
                            <td>Cash</td>
                            <td>Completed</td>
                            <td><div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-eye"></i></button>
                                <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-file-lines"></i></button>
                                <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i></button>

                            </div></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Inventory -->
            <div id="inventory" class="retail-tab-content">
                <div class="container-fluid d-flex justify-content-between align-items-center">

                    <div class="d-flex justify-content-start">
                        <div class="input-group w-100 me-2">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-start-0" placeholder="Search..." />
                        </div>
                        <button class="btn btn-outline-primary"><i class="fa-solid fa-circle-exclamation text-warning"></i> Low Stock</button>
                    </div>


                    <div class="d-flex gap-2 justify-contnt-end">
                    <button class="btn btn-outline-primary"><i class="fa-solid fa-box"></i> Request Stock</button>
                    <button class="btn btn-outline-primary"><i class="fa-solid fa-cart-plus"></i> Add Product</button>
                    </div>
                </div>
                <table id="Table" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Price</th>
                        <th>Location</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>STR-001</td>
                        <td>Copper Wire (2.5mm)</td>
                        <td>Wires</td>
                        <td>680</td>
                        <td>₹85/m</td>
                        <td>Shelf A1</td>
                        <td><div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm">Update Stock</button>
                                <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-pen-to-square"></i></button>

                            </div></td>
                    </tr>
                </tbody>
                </table>
            </div>

            <!-- Customer -->
            <div id="customers" class="retail-tab-content">
                <div class="container-fluid d-flex justify-content-between align-items-center">

                    <div class="d-flex justify-content-start">
                        <div class="input-group w-100 me-2">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-start-0" placeholder="Search..." />
                        </div>
                        <button class="btn btn-outline-primary me-2">All Customers</button>
                        <button class="btn btn-outline-primary me-2">Retail</button>
                        <button class="btn btn-outline-primary">Wholesale</button>
                    </div>


                    <div class="justify-contnt-end">
                    <button class="btn btn-outline-primary"><i class="fa-regular fa-user"></i> Add Customer</button>
                    </div>
                </div>
                <table id="Table" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Contact</th>
                            <th>Purchases</th>
                            <th>Total Spent</th>
                            <th>Last Visit</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>CUST-001</td>
                            <td>Priya Sharma</td>
                            <td>Retail</td>
                            <td>9876543210</td>
                            <td>8</td>
                            <td>₹23,450</td>
                            <td>11/04/2025</td>
                            <td><div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-eye"></i></button>
                                <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-pen-to-square"></i></button>
                                <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-cart-plus"></i></button>

                                </div></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


    </div>

    <script>
        // Line Chart
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue',
                data: [430000, 460000, 475000, 440000, 450000, 470000],
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
                    stepSize: 150000
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
            labels: ['Electrical', 'Lighting', 'Wiring', 'Switches', 'Others'],
            datasets: [{
                data: [35, 23, 18, 16, 8],
                backgroundColor: [
                '#0d6efd',  // Blue (Electrical)
                '#20c997',  // Green (Lighting)
                '#ffc107',  // Orange (Wiring)
                '#fd7e14',  // Orange-dark (Switches)
                '#6f42c1'   // Violet (Others)
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

        // For billing section
        function showBillingTab(id) {
            const tabs = document.querySelectorAll('.billingTab');
            const contents = document.querySelectorAll('.billing-tab-content');

            tabs.forEach(tab => tab.classList.remove('active'));
            contents.forEach(content => content.classList.remove('active'));

            document.querySelector(`#${id}`).classList.add('active');
            document.querySelector(`[onclick="showBillingTab('${id}')"]`).classList.add('active');
        }

        // For inventory section
        function showInventoryTab(id) {
            const tabs = document.querySelectorAll('.inventoryTab');
            const contents = document.querySelectorAll('.inventory-tab-content');

            tabs.forEach(tab => tab.classList.remove('active'));
            contents.forEach(content => content.classList.remove('active'));

            document.querySelector(`#${id}`).classList.add('active');
            document.querySelector(`[onclick="showInventoryTab('${id}')"]`).classList.add('active');
        }

        // For factory section
        function showFactoryTab(id) {
            const tabs = document.querySelectorAll('.factoryTab');
            const contents = document.querySelectorAll('.factory-tab-content');

            tabs.forEach(tab => tab.classList.remove('active'));
            contents.forEach(content => content.classList.remove('active'));

            document.querySelector(`#${id}`).classList.add('active');
            document.querySelector(`[onclick="showFactoryTab('${id}')"]`).classList.add('active');
        }

        // For retail store section
        function showRetailTab(id) {
            const tabs = document.querySelectorAll('.retailTab');
            const contents = document.querySelectorAll('.retail-tab-content');

            tabs.forEach(tab => tab.classList.remove('active'));
            contents.forEach(content => content.classList.remove('active'));

            document.querySelector(`#${id}`).classList.add('active');
            document.querySelector(`[onclick="showRetailTab('${id}')"]`).classList.add('active');
        }
    </script>

</body>
</html>