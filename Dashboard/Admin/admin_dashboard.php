<?php
session_start();
if (!(isset($_SESSION["uid"]) && isset($_SESSION["user_type"]) && isset($_SESSION["session_id"]))) {
    header("location:../../login.php");
    exit;
} else {
    if (in_array($_SESSION["user_type"], ['Factory', 'Store', 'Vendor'])) {
        header("location:../index.php");
        exit;

    } else if (!($_SESSION["user_type"] == 'Admin')) {
        header("location:../../login.php");
        exit;
    }
}

$page = isset($_GET['page']) ? $_GET['page'] : 'admin_dashboard';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="unnati">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../public/css/styles.css">
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

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .modal-content {
            border-radius: 0.5rem;
        }

        .gst-section {
            display: block;
        }

        #itemTable input {
            width: 100px;
        }

        .text-end {
            text-align: right;
        }

        textarea {
            width: 100%;
            height: 60px;
            margin-top: 10px;
        }

        .bill-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: none;
            border-color: #0d6efd;
        }

        .btn-outline-danger:hover {
            color: white;
        }

        .total-section {
            background: #f1f1f1;
            padding: 15px;
            border-radius: 8px;
        }
    </style>
</head>

<body class="bg-secondary bg-opacity-10">
    <?php
    include './_admin_nav.php';
    ?>

    <main>
        <?php if ($page === 'admin_dashboard'): ?>

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
                    <div class="card stat-card cards shadow-sm" style="background-color:rgb(253, 247, 226);">
                        <div class="card-body">
                            <h5 class="text-muted">Low Stock Alert</h5>
                            <p>5 products are below minimum stock levels. Review inventory soon.</p>
                            <a href="?page=inventory" style="text-decoration: none;" class="text-dark">View Inventory →</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12 my-4">
                    <div class="card stat-card cards shadow-sm" style="background-color: #e7f3ff;">
                        <div class="card-body">
                            <h5 class="text-muted">Inventory Value</h5>
                            <p>3 customer payments were received today totaling ₹28,450.</p>
                            <a href="?page=inventory" style="text-decoration: none;" class="text-dark">View Inventory →</a>
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

                    <div class="d-flex align-items-start border rounded mb-3 p-3"
                        style="background-color:rgb(177, 202, 253);">
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
                            <small class="text-muted" style="color: #6f42c1;">Quarterly inventory check at main
                                warehouse</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create Invoice form -->
            <div id="invoiceModal" class="modal">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content p-3">
                        <div class="modal-header">
                            <h5 class="modal-title">Create New Invoice</h5>
                            <button type="button" class="btn-close" onclick="closeInvoiceModal()"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Customer:</label>
                                <select class="form-select">
                                    <option>Select customer</option>
                                    <option>Customer A</option>
                                    <option>Customer B</option>
                                    <option>Customer C</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label d-block">Document Type:</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="docType" value="withGST" checked
                                        onchange="toggleGST()">
                                    <label class="form-check-label">With GST</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="docType" value="withoutGST"
                                        onchange="toggleGST()">
                                    <label class="form-check-label">Without GST</label>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Date:</label>
                                    <input type="date" id="invoiceDate" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Due Date:</label>
                                    <input type="date" id="dueDate" class="form-control">
                                </div>
                                <div class="col-md-4 gst-section">
                                    <label class="form-label">Tax Rate:</label>
                                    <select id="taxRate" class="form-select" onchange="updateTotals()">
                                        <option value="5">GST 5%</option>
                                        <option value="12">GST 12%</option>
                                        <option value="18">GST 18%</option>
                                        <option value="28">GST 28%</option>
                                    </select>
                                </div>
                            </div>

                            <div class="table-responsive mb-3">
                                <table class="table table-bordered" id="itemTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Item</th>
                                            <th>Description</th>
                                            <th>Qty</th>
                                            <th>Price (₹)</th>
                                            <th>Total (₹)</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                <button class="btn btn-sm btn-outline-primary" onclick="addItem()">+ Add Item</button>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes:</label>
                                <textarea class="form-control" placeholder="Additional notes, payment terms..."
                                    rows="3"></textarea>
                            </div>

                            <div class="text-end">
                                <p>Subtotal: ₹<span id="subtotal">0.00</span></p>
                                <p class="gst-section">GST (<span id="gstPercent">18</span>%): ₹<span
                                        id="gstAmount">0.00</span></p>
                                <h5>Total: ₹<span id="totalAmount">0.00</span></h5>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-secondary" onclick="closeInvoiceModal()">Cancel</button>
                            <button class="btn btn-primary">Create Invoice</button>
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
                            <button class="btn btn-outline-primary"><i class="fa-regular fa-circle-check text-success"></i>
                                Paid</button>
                            <button class="btn btn-outline-primary"><i class="fa-regular fa-clock text-warning"></i>
                                Pending</button>
                            <button class="btn btn-outline-primary"><i
                                    class="fa-solid fa-circle-exclamation text-danger"></i> Overdue</button>
                        </div>

                        <div class="justify-contnt-end">
                            <button class="btn btn-outline-primary" onclick="openInvoiceModal()"><i
                                    class="fa-solid fa-plus"></i> Create Invoice</button>
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
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-primary btn-sm"><i
                                                class="fa-regular fa-eye"></i></button>
                                        <button class="btn btn-outline-primary btn-sm"><i
                                                class="fa-solid fa-pen-to-square"></i></button>
                                        <button class="btn btn-outline-primary btn-sm"><i
                                                class="fa-solid fa-download"></i></button>
                                        <button class="btn btn-outline-primary btn-sm"><i
                                                class="fa-solid fa-print"></i></button>

                                    </div>
                                </td>
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
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                <i class="fa-solid fa-plus"></i> Record Payment
                            </button>
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
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-primary btn-sm"><i
                                                class="fa-regular fa-eye"></i></button>
                                        <button class="btn btn-outline-primary btn-sm"><i
                                                class="fa-solid fa-download"></i></button>
                                        <button class="btn btn-outline-primary btn-sm"><i
                                                class="fa-solid fa-print"></i></button>

                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Quick bills -->
                <div id="quickbill" class="billing-tab-content bill-card">
                    <h5><i class="fas fa-receipt me-2"></i>Quick Bill</h5>
                    <small class="text-muted mb-3 d-block">Create and print bills quickly for retail customers</small>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Customer Name</label>
                            <input type="text" class="form-control" value="Walk-in Customer" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Number (Optional)</label>
                            <input type="text" class="form-control" placeholder="Phone Number" />
                        </div>
                    </div>

                    <h6 class="mt-3">🧾 Items</h6>
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th style="width: 40%">Item</th>
                                <th style="width: 15%">Qty</th>
                                <th style="width: 20%">Price</th>
                                <th style="width: 20%">Total</th>
                                <th style="width: 5%"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsTable">
                            <!-- Item rows will be added here -->
                        </tbody>
                    </table>
                    <button class="btn btn-outline-primary mb-3" onclick="addItemRow()">+ Add Item</button>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Payment Method</label>
                            <select class="form-select" id="paymentMethod">
                                <option>Cash</option>
                                <option>UPI</option>
                                <option>Card</option>
                                <option>Bank Transfer</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Discount (%)</label>
                            <input type="number" class="form-control" id="discount" value="0" oninput="calculateTotal()" />
                        </div>
                    </div>

                    <div class="my-3">
                        <label class="form-label">Note (Optional)</label>
                        <textarea class="form-control" placeholder="Additional information"></textarea>
                    </div>

                    <div class="total-section mb-3">
                        <p>Subtotal: ₹<span id="subTotal">0.00</span></p>
                        <p class="text-danger">Discount: ₹<span id="discountAmt">0.00</span></p>
                        <h5 class="text-primary">Total: ₹<span id="grandTotal">0.00</span></h5>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button class="btn btn-secondary">Cancel</button>
                        <button class="btn btn-primary">Generate Bill</button>
                    </div>
                </div>

                <!-- Reports -->
                <div id="reports" class="billing-tab-content">
                    <h4>Outstanding Payments</h4>
                    <table id="Table" class="table table-bordered table-hover">
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
                            <tr>
                                <td>INV-0002</td>
                                <td>Sharma Electrical</td>
                                <td>₹36,750</td>
                                <td>25/04/2025</td>
                                <td>15 days</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Form -->
            <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form>
                            <div class="modal-header">
                                <h5 class="modal-title" id="paymentModalLabel">Record New Payment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="paymentId" class="form-label">Payment ID</label>
                                    <input type="text" class="form-control" id="paymentId" placeholder="e.g. PAY-0002">
                                </div>

                                <div class="mb-3">
                                    <label for="invoiceId" class="form-label">Invoice</label>
                                    <input type="text" class="form-control" id="invoiceId" placeholder="e.g. INV-0002">
                                </div>

                                <div class="mb-3">
                                    <label for="customerName" class="form-label">Customer</label>
                                    <input type="text" class="form-control" id="customerName" placeholder="Customer Name">
                                </div>

                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount</label>
                                    <input type="number" class="form-control" id="amount" placeholder="e.g. 10000">
                                </div>

                                <div class="mb-3">
                                    <label for="method" class="form-label">Method</label>
                                    <select class="form-select" id="method">
                                        <option>Bank Transfer</option>
                                        <option>Cash</option>
                                        <option>UPI</option>
                                        <option>Cheque</option>
                                        <option>Card</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="date">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Payment</button>
                            </div>
                        </form>
                    </div>
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
                        </div>


                        <div class="justify-contnt-end">
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#productModal"><i class="fa-solid fa-plus"></i> Add Product</button>
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
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-primary btn-sm"><i
                                                class="fa-solid fa-pen-to-square"></i></button>
                                        <button class="btn btn-outline-primary btn-sm"><i
                                                class="fa-regular fa-trash-can"></i></button>

                                    </div>
                                </td>
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
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#recordMovement"><i class="fa-solid fa-plus"></i> Record
                                Movement</button>
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

            <!-- Add product Form -->
            <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form>
                            <div class="modal-header">
                                <h5 class="modal-title" id="productModalLabel">Add New Product</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="productId" class="form-label">Product ID</label>
                                    <input type="text" class="form-control" id="productId">
                                </div>

                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name">
                                </div>

                                <div class="mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <input type="text" class="form-control" id="category">
                                </div>

                                <div class="mb-3">
                                    <label for="stock" class="form-label">Stock</label>
                                    <input type="number" class="form-control" id="stock">
                                </div>

                                <div class="mb-3">
                                    <label for="unit" class="form-label">Unit</label>
                                    <input type="number" class="form-control" id="unit">
                                </div>

                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="location">
                                </div>

                                <div class="mb-3">
                                    <label for="update" class="form-label">Last Updated</label>
                                    <input type="text" class="form-control" id="update">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Product</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Record Movement Form -->
            <div class="modal fade" id="recordMovement" tabindex="-1" aria-labelledby="recordMovementLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form>
                            <div class="modal-header">
                                <h5 class="modal-title" id="recordMovementLabel">Record Movement</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="transactionId" class="form-label">Transaction ID</label>
                                    <input type="text" class="form-control" id="transactionId">
                                </div>

                                <div class="mb-3">
                                    <label for="product" class="form-label">Product</label>
                                    <input type="text" class="form-control" id="product">
                                </div>

                                <div class="mb-3">
                                    <label for="type" class="form-label">Type</label>
                                    <input type="text" class="form-control" id="type">
                                </div>

                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <input type="number" class="form-control" id="quantity">
                                </div>

                                <div class="mb-3">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="date">
                                </div>

                                <div class="mb-3">
                                    <label for="source" class="form-label">Source</label>
                                    <input type="text" class="form-control" id="source">
                                </div>

                                <div class="mb-3">
                                    <label for="refrence" class="form-label">Reference</label>
                                    <input type="text" class="form-control" id="refrence">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Movement</button>
                            </div>
                        </form>
                    </div>
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
                            <button class="btn btn-outline-primary"><i class="fa-regular fa-clock text-warning"></i>
                                Pending</button>
                            <button class="btn btn-outline-primary"><i class="fa-regular fa-circle-play"></i> In
                                Progress</button>
                            <button class="btn btn-outline-primary"><i class="fa-regular fa-circle-check text-success"></i>
                                Complete</button>
                        </div>


                        <div class="justify-contnt-end">
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#productionOrder"><i class="fa-solid fa-plus"></i> New Production
                                Order</button>
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
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-pause"></i>
                                            Pause</button>
                                        <button class="btn btn-outline-primary btn-sm"><i
                                                class="fa-solid fa-pen-to-square"></i></button>
                                        <button class="btn btn-outline-primary btn-sm"><i
                                                class="fa-solid fa-print"></i></button>

                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Raw material -->
                <div id="material" class="factory-tab-content">
                    <div class="container-fluid d-flex justify-content-between align-items-center">

                        <div class="d-flex justify-content-start gap-2">
                            <button class="btn btn-outline-primary"><i
                                    class="fa-solid fa-triangle-exclamation text-warning"></i> Low Stock</button>
                            <button class="btn btn-outline-primary">All Materials</button>
                        </div>

                        <div class="d-flex gap-2 justify-contnt-end">
                            <button class="btn btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#orderMaterial"><i class="fa-solid fa-box"></i> Oreder Material</button>
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addMaterial"><i class="fa-solid fa-plus"></i> Add Material</button>
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
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-primary btn-sm">Update Stock</button>
                                        <button class="btn btn-outline-primary btn-sm"><i
                                                class="fa-solid fa-pen-to-square"></i></button>

                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Quality -->
                <div id="quality" class="factory-tab-content">
                    <div class="container-fluid d-flex justify-content-between align-items-center">

                        <div class="d-flex justify-content-start gap-2">
                            <button class="btn btn-outline-primary"><i class="fa-regular fa-circle-check text-success"></i>
                                Passed</button>
                            <button class="btn btn-outline-primary"><i
                                    class="fa-solid fa-triangle-exclamation text-danger"></i> Failed</button>
                            <button class="btn btn-outline-primary">All Tests</button>
                        </div>

                        <div class="justify-contnt-end">
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#qualityTest"><i class="fa-regular fa-clipboard"></i> New Quality
                                Test</button>
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

             <!-- Production Order Form -->
             <div class="modal fade" id="productionOrder" tabindex="-1" aria-labelledby="productionOrderLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form>
                            <div class="modal-header">
                                <h5 class="modal-title" id="productionOrderLabel">Production Order</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="orderId" class="form-label">Order ID</label>
                                    <input type="text" class="form-control" id="orderId">
                                </div>

                                <div class="mb-3">
                                    <label for="product" class="form-label">Product</label>
                                    <input type="text" class="form-control" id="product">
                                </div>

                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <input type="text" class="form-control" id="quantity">
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <input type="text" class="form-control" id="status">
                                </div>

                                <div class="mb-3">
                                    <label for="progress" class="form-label">Progress</label>
                                    <input type="text" class="form-control" id="progress">
                                </div>

                                <div class="mb-3">
                                    <label for="startDate" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="startDate">
                                </div>

                                <div class="mb-3">
                                    <label for="endDate" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="endDate">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Production</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

             <!-- Order Material Form -->
             <div class="modal fade" id="orderMaterial" tabindex="-1" aria-labelledby="orderMaterialLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form>
                            <div class="modal-header">
                                <h5 class="modal-title" id="orderMaterialLabel">Order Material</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="material" class="form-label">Material</label>
                                    <input type="text" class="form-control" id="material">
                                </div>

                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <input type="number" class="form-control" id="quantity">
                                </div>

                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="location">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Order Material</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

             <!-- Add Material Form -->
             <div class="modal fade" id="addMaterial" tabindex="-1" aria-labelledby="addMaterialLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form>
                            <div class="modal-header">
                                <h5 class="modal-title" id="addMaterialLabel">Add Material</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="Id" class="form-label">ID</label>
                                    <input type="text" class="form-control" id="Id">
                                </div>

                                <div class="mb-3">
                                    <label for="material" class="form-label">Material</label>
                                    <input type="text" class="form-control" id="material">
                                </div>

                                <div class="mb-3">
                                    <label for="currentStock" class="form-label">Current Stock</label>
                                    <input type="number" class="form-control" id="currentStock">
                                </div>

                                <div class="mb-3">
                                    <label for="unit" class="form-label">Unit</label>
                                    <input type="text" class="form-control" id="unit">
                                </div>

                                <div class="mb-3">
                                    <label for="orderLevel" class="form-label">Reorder Level</label>
                                    <input type="number" class="form-control" id="orderLevel">
                                </div>

                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="location">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add Material</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

             <!-- Quality Test Form -->
             <div class="modal fade" id="qualityTest" tabindex="-1" aria-labelledby="qualityTestLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form>
                            <div class="modal-header">
                                <h5 class="modal-title" id="qualityTestLabel">Quality Test</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="testId" class="form-label">Test ID</label>
                                    <input type="text" class="form-control" id="testId">
                                </div>

                                <div class="mb-3">
                                    <label for="product" class="form-label">Product</label>
                                    <input type="text" class="form-control" id="product">
                                </div>

                                <div class="mb-3">
                                    <label for="batchNumber" class="form-label">Batch Number</label>
                                    <input type="text" class="form-control" id="batchNumber">
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <input type="text" class="form-control" id="status">
                                </div>

                                <div class="mb-3">
                                    <label for="testedBy" class="form-label">Tested By</label>
                                    <input type="text" class="form-control" id="testedBy">
                                </div>

                                <div class="mb-3">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="date">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Report</button>
                            </div>
                        </form>
                    </div>
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
                        </div>


                        <div class="justify-contnt-end">
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addSales"><i class="fa-solid fa-cart-plus"></i> New Sales</button>
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
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-primary btn-sm"><i
                                                class="fa-regular fa-eye"></i></button>
                                        <button class="btn btn-outline-primary btn-sm"><i
                                                class="fa-regular fa-file-lines"></i></button>
                                        <button class="btn btn-outline-primary btn-sm"><i
                                                class="fa-solid fa-download"></i></button>

                                    </div>
                                </td>
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
                            <button class="btn btn-outline-primary"><i
                                    class="fa-solid fa-circle-exclamation text-warning"></i> Low Stock</button>
                        </div>


                        <div class="d-flex gap-2 justify-contnt-end">
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#requestStock"><i class="fa-solid fa-box"></i> Request Stock</button>
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addProductAtStore"><i class="fa-solid fa-cart-plus"></i> Add
                                Product</button>
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
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-primary btn-sm">Update Stock</button>
                                        <button class="btn btn-outline-primary btn-sm"><i
                                                class="fa-solid fa-pen-to-square"></i></button>

                                    </div>
                                </td>
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
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addCustomer"><i class="fa-regular fa-user"></i> Add Customer</button>
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
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-primary btn-sm"><i
                                                class="fa-regular fa-eye"></i></button>
                                        <button class="btn btn-outline-primary btn-sm"><i
                                                class="fa-regular fa-pen-to-square"></i></button>
                                        <button class="btn btn-outline-primary btn-sm"><i
                                                class="fa-solid fa-cart-plus"></i></button>

                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

             <!-- Sales Form -->
             <div class="modal fade" id="addSales" tabindex="-1" aria-labelledby="addSalesLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form>
                            <div class="modal-header">
                                <h5 class="modal-title" id="addSalesLabel">Add Sales</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="salesId" class="form-label">Sales ID</label>
                                    <input type="text" class="form-control" id="salesId">
                                </div>

                                <div class="mb-3">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="date">
                                </div>

                                <div class="mb-3">
                                    <label for="customer" class="form-label">Customer</label>
                                    <input type="text" class="form-control" id="customer">
                                </div>

                                <div class="mb-3">
                                    <label for="item" class="form-label">Items</label>
                                    <input type="number" class="form-control" id="item">
                                </div>

                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount</label>
                                    <input type="text" class="form-control" id="amount">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="method" class="form-label">Method</label>
                                    <select class="form-select" id="method">
                                        <option>Bank Transfer</option>
                                        <option>Cash</option>
                                        <option>UPI</option>
                                        <option>Cheque</option>
                                        <option>Card</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <input type="text" class="form-control" id="status">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Sales</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

             <!-- Request Stock Form -->
             <div class="modal fade" id="requestStock" tabindex="-1" aria-labelledby="requestStockLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form>
                            <div class="modal-header">
                                <h5 class="modal-title" id="requestStockLabel">Request Stock</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name">
                                </div>

                                <div class="mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <input type="text" class="form-control" id="category">
                                </div>

                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <input type="number" class="form-control" id="quantity">
                                </div>

                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="location">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Request Stock</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

             <!-- Add Product Form -->
            <div class="modal fade" id="addProductAtStore" tabindex="-1" aria-labelledby="addProductAtStoreLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form>
                            <div class="modal-header">
                                <h5 class="modal-title" id="addProductAtStoreLabel">Add Product</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="productId" class="form-label">Product ID</label>
                                    <input type="text" class="form-control" id="productId">
                                </div>

                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name">
                                </div>

                                <div class="mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <input type="text" class="form-control" id="category">
                                </div>

                                <div class="mb-3">
                                    <label for="stock" class="form-label">Stock</label>
                                    <input type="number" class="form-control" id="stock">
                                </div>

                                <div class="mb-3">
                                    <label for="price" class="form-label">Price</label>
                                    <input type="text" class="form-control" id="price">
                                </div>

                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="location">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add Product</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

             <!-- Add Customer Form -->
             <div class="modal fade" id="addCustomer" tabindex="-1" aria-labelledby="addCustomerLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form>
                            <div class="modal-header">
                                <h5 class="modal-title" id="addCustomerLabel">Add Customer</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="Id" class="form-label">ID</label>
                                    <input type="text" class="form-control" id="Id">
                                </div>

                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name">
                                </div>

                                <div class="mb-3">
                                    <label for="type" class="form-label">Type</label>
                                    <input type="text" class="form-control" id="type">
                                </div>

                                <div class="mb-3">
                                    <label for="contact" class="form-label">Contact</label>
                                    <input type="tel" class="form-control" id="contact">
                                </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Customer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


        <?php elseif ($page === 'billing_desk'): ?>
            <?php include 'billing_desk.php'; ?>

        <?php elseif ($page === 'accounting'): ?>
            <?php include 'accounting.php'; ?>

        <?php elseif ($page === 'inventory'): ?>
            <?php include 'inventory.php'; ?>

        <?php elseif ($page === 'expenses_dashboard'): ?>
            <?php include 'expenses_dashboard.php'; ?>

        <?php elseif ($page === 'factory_stock'): ?>
            <?php include 'factory_stock.php'; ?>

        <?php elseif ($page === 'retail_store'): ?>
            <?php include 'retail_store.php'; ?>

        <?php elseif ($page === 'After_sales_service'): ?>
            <?php include 'After_sales_service.php'; ?>

        <?php elseif ($page === 'suppliers'): ?>
            <?php include 'suppliers.php'; ?>

        <?php elseif ($page === 'reports'): ?>
            <?php include 'reports.php'; ?>

        <?php elseif ($page === 'user_management'): ?>
            <?php include 'user_management.php'; ?>

        <?php elseif ($page === 'settings'): ?>
            <?php include 'settings.php'; ?>

        <?php endif; ?>

    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

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

        function addItemRow() {
            const table = document.getElementById('itemsTable');
            const row = document.createElement('tr');
            row.innerHTML = `
        <td><input type="text" class="form-control item-name" placeholder="Item name" /></td>
        <td><input type="number" class="form-control qty" value="1" min="1" oninput="updateRowTotal(this)" /></td>
        <td><input type="number" class="form-control price" value="0" min="0" oninput="updateRowTotal(this)" /></td>
        <td class="item-total">₹0.00</td>
        <td><button class="btn btn-outline-danger btn-sm" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
      `;
            table.appendChild(row);
            updateRowTotal(row.querySelector('.qty'));
        }

        function removeRow(btn) {
            btn.closest('tr').remove();
            calculateTotal();
        }

        function updateRowTotal(input) {
            const row = input.closest('tr');
            const qty = parseFloat(row.querySelector('.qty').value) || 0;
            const price = parseFloat(row.querySelector('.price').value) || 0;
            const total = qty * price;
            row.querySelector('.item-total').textContent = `₹${total.toFixed(2)}`;
            calculateTotal();
        }

        function calculateTotal() {
            let subtotal = 0;
            document.querySelectorAll('#itemsTable tr').forEach(row => {
                const qty = parseFloat(row.querySelector('.qty')?.value || 0);
                const price = parseFloat(row.querySelector('.price')?.value || 0);
                subtotal += qty * price;
            });

            const discountPercent = parseFloat(document.getElementById('discount').value) || 0;
            const discountAmt = subtotal * (discountPercent / 100);
            const grandTotal = subtotal - discountAmt;

            document.getElementById('subTotal').textContent = subtotal.toFixed(2);
            document.getElementById('discountAmt').textContent = discountAmt.toFixed(2);
            document.getElementById('grandTotal').textContent = grandTotal.toFixed(2);
        }

        // Add one initial row
        window.onload = () => addItemRow();

        // Create invoice form 

        let itemIndex = 0;

        // To open form
        function openInvoiceModal() {
            const modal = document.getElementById('invoiceModal');
            modal.style.display = 'block';
            modal.classList.add('show');

            if (document.querySelectorAll("#itemTable tbody tr").length === 0) {
                addItem();
            }
        }

        // To close form
        function closeInvoiceModal() {
            const modal = document.getElementById('invoiceModal');
            modal.style.display = 'none';
            modal.classList.remove('show');

            document.querySelector('#itemTable tbody').innerHTML = '';
            updateTotals();
        }

        // For add item row
        function addItem() {
            const tbody = document.querySelector("#itemTable tbody");
            const tr = document.createElement("tr");
            tr.innerHTML = `
        <td>
            <select onchange="updateTotals()">
                <option value="">Select Product</option>
                <option value="Product A">Product A</option> // Dynamic data from database
                <option value="Product B">Product B</option> // Dynamic data from database
                <option value="Product C">Product C</option> // Dynamic data from database
            </select>
        </td>
        <td><input placeholder="Description" /></td>
        <td><input type="number" value="1" min="1" oninput="updateTotals()" /></td>
        <td><input type="number" value="0" step="0.01" oninput="updateTotals()" /></td>
        <td class="itemTotal">₹0.00</td>
        <td><button class="btn btn-sm btn-outline-danger" onclick="removeItem(this)">Delete</button></td>
    `;
            tbody.appendChild(tr);
            updateTotals();
        }

        // To remove item row
        function removeItem(btn) {
            btn.closest("tr").remove();
            updateTotals();
        }

        // For GST 
        function toggleGST() {
            const withGST = document.querySelector('input[name="docType"]:checked').value === 'withGST';
            document.querySelectorAll(".gst-section").forEach(el => {
                el.style.display = withGST ? 'block' : 'none';
            });
            updateTotals();
        }

        // For calculate total amount
        function updateTotals() {
            let subtotal = 0;
            document.querySelectorAll("#itemTable tbody tr").forEach(row => {
                const qty = parseFloat(row.children[2].querySelector('input').value || 0);
                const price = parseFloat(row.children[3].querySelector('input').value || 0);
                const total = qty * price;
                subtotal += total;
                row.children[4].innerText = "₹" + total.toFixed(2);
            });

            const taxRate = parseFloat(document.getElementById('taxRate')?.value || 0);
            const gstEnabled = document.querySelector('input[name="docType"]:checked').value === 'withGST';
            const gstAmount = gstEnabled ? (subtotal * taxRate / 100) : 0;

            document.getElementById('subtotal').innerText = subtotal.toFixed(2);
            document.getElementById('gstPercent').innerText = taxRate;
            document.getElementById('gstAmount').innerText = gstAmount.toFixed(2);
            document.getElementById('totalAmount').innerText = (subtotal + gstAmount).toFixed(2);
        }

        // Close form when clicking outside of it
        window.onclick = function (event) {
            const modal = document.getElementById('invoiceModal');
            if (event.target === modal) {
                closeInvoiceModal();
            }
        };
    </script>

</body>

</html>