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

    .bill-modal {
        position: fixed;
        z-index: 1050;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .bill-modal-dialog {
        margin: 5% auto;
        max-width: 800px;
    }
</style>

<h1>Billing Dashboard</h1>
<p>Complete billing desk for invoices, bills, and payments</p>


<!-- Cards -->
<div class="row">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
            <div class="card-body">
                <h6 class="text-muted">Pending Invoices</h6>
                <h3 class="fw-bold">₹86,450</h3>
                <p>12 invoices pending</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
            <div class="card-body">
                <h6 class="text-muted">Month's Revenue</h6>
                <h3 class="fw-bold">₹2,47,850</h3>
                <p class="text-danger">12.5% vs last month</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
            <div class="card-body">
                <h6 class="text-muted">Purchase Orders</h6>
                <h3 class="fw-bold">₹1,35,250</h3>
                <p>8 orders this month</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
            <div class="card-body">
                <h6 class="text-muted">Returns & Credit</h6>
                <h3 class="fw-bold">₹18,250</h3>
                <p>5 returns processed</p>
            </div>
        </div>
    </div>
</div>

<!-- Buttons -->
<div class="row justify-content-center">
    <div class="col-md-3 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100" onclick="openInvoiceModal(event)"
            id="newInvoice"><i class="fa-solid fa-file"></i> Create
            Invoice</button>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100" onclick="openSalesModal(event)" id="creditNote"><i class="fa-solid fa-file-export"></i> Issue
            Credit Note</button>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100" data-bs-toggle="modal"
            data-bs-target="#recordMovement"><i class="fa-solid fa-pager"></i> Record
            Payment</button>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <a href="?page=reports" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-clipboard-list"></i>
            Generate Report</a>
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

<!-- Charts -->
<div class="chart-container">
    <div class="chart-box">
        <h3>Monthly Billing Count</h3>
        <canvas id="barChart"></canvas>
    </div>
    <div class="chart-box">
        <h3>Payment Methods</h3>
        <div style="position: relative; width: 100%; max-width: 300px; margin: 0 auto;">
            <canvas id="pieChart"></canvas>
        </div>
    </div>
</div>

<!-- Create Invoice form -->
<div id="invoiceModal" class="modal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content p-3">
            <div class="modal-header">
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
                    <textarea class="form-control" placeholder="Additional notes, payment terms..." rows="3"></textarea>
                </div>

                <div class="text-end">
                    <p>Subtotal: ₹<span id="subtotal">0.00</span></p>
                    <p class="gst-section">GST (<span id="gstPercent">18</span>%): ₹<span id="gstAmount">0.00</span></p>
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

<!-- Sales Invoice Form -->
<div class="modal" id="salesModal" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content p-3">
            <div class="modal-header">
                <button type="button" class="btn-close"
                    onclick="document.getElementById('salesModal').style.display='none'"></button>
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

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Date:</label>
                        <input type="date" id="invoiceDate" class="form-control">
                    </div>
                    <div class="col-md-4 gst-section">
                        <label class="form-label">Tax Rate:</label>
                        <select id="gsttaxRate" class="form-select" onchange="calculateSalesTotals()">
                            <option value="5">GST 5%</option>
                            <option value="12">GST 12%</option>
                            <option value="18">GST 18%</option>
                            <option value="28">GST 28%</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered" id="salesItemTable">
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
                    <button class="btn btn-sm btn-outline-primary" onclick="addSalesItem()">+ Add Item</button>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes:</label>
                    <textarea class="form-control" placeholder="Additional notes, payment terms..." rows="3"></textarea>
                </div>

                <div class="text-end">
                    <p>Subtotal: ₹<span id="subTotal">0.00</span></p>
                    <p class="gst-section">GST (<span id="taxLabel">18%</span>): ₹<span id="gstTax">0.00</span></p>
                    <h5>Total: ₹<span id="grandTotal">0.00</span></h5>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary"
                    onclick="document.getElementById('salesModal').style.display='none'">Cancel</button>
                <button class="btn btn-primary">Create Invoice</button>
            </div>
        </div>
    </div>
</div>


<!-- Tabels -->
<div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">

    <div class="tabs">
        <button class="billingTab active" onclick="showbillingTab('invoice')">Invoice</button>
        <button class="billingTab" onclick="showbillingTab('sales')">Sales Return</button>
        <button class="billingTab" onclick="showbillingTab('credit')">Credit Note</button>
        <button class="billingTab" onclick="showbillingTab('quotation')">Quotation</button>
        <button class="billingTab" onclick="showbillingTab('delivery')">Delivery Challan</button>
        <button class="billingTab" onclick="showbillingTab('proforma')">Proforma</button>
        <button class="billingTab" onclick="showbillingTab('auto')">Auto Bill</button>
        <button class="billingTab" onclick="showbillingTab('counter')">Counter Purchase</button>
        <button class="billingTab" onclick="showbillingTab('payment')">Payment Out</button>
        <button class="billingTab" onclick="showbillingTab('purchase')">Purchase Return</button>
        <button class="billingTab" onclick="showbillingTab('debit')">Debit Note</button>
        <button class="billingTab" onclick="showbillingTab('purchase_order')">Purchase Order</button>
    </div>

    <!-- Invoice table -->
    <div id="invoice" class="billing-tab-content active">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Invoices</h1>
            </div>

            <div class="d-flex justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0" placeholder="Search..." />
                </div>
                <button class="btn btn-outline-primary me-2"><i class="fa-solid fa-filter"></i></button>
                <button class="btn btn-outline-primary"><i class="fa-regular fa-calendar"></i></button>
            </div>

            <div class="justify-contnt-end">
                <button class="btn btn-outline-primary" onclick="openInvoiceModal(event)" id="newInvoice">Create New
                    Invoice</button>
            </div>

        </div>
        <table id="Table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>INV-2025-001</td>
                    <td>Rajesh Electronics</td>
                    <td>12 Apr, 2025</td>
                    <td>12 items</td>
                    <td>₹24,500</td>
                    <td>Paid</td>
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-eye"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i
                                    class="fa-regular fa-pen-to-square"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-print"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-ellipsis"></i></button>

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Sales table -->
    <div id="sales" class="billing-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Sales Returns</h1>
            </div>

            <div class="d-flex justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0" placeholder="Search..." />
                </div>
                <button class="btn btn-outline-primary me-2"><i class="fa-solid fa-filter"></i></button>
                <button class="btn btn-outline-primary"><i class="fa-regular fa-calendar"></i></button>
            </div>

            <div class="justify-contnt-end">
                <button class="btn btn-outline-primary" onclick="openSalesModal(event)" id="salesReturn">Create Sales
                    Return</button>
            </div>

        </div>
        <table id="Table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>SR-2025-001</td>
                    <td>Rajesh Electronics</td>
                    <td>12 Apr, 2025</td>
                    <td>12 items</td>
                    <td>₹24,500</td>
                    <td>Completed</td>
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-eye"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i
                                    class="fa-regular fa-pen-to-square"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-print"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-ellipsis"></i></button>

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Credit table -->
    <div id="credit" class="billing-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Credit Notes</h1>
            </div>

            <div class="d-flex justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0" placeholder="Search..." />
                </div>
                <button class="btn btn-outline-primary me-2"><i class="fa-solid fa-filter"></i></button>
                <button class="btn btn-outline-primary"><i class="fa-regular fa-calendar"></i></button>
            </div>

            <div class="justify-contnt-end">
                <button class="btn btn-outline-primary" onclick="openSalesModal(event)" id="creditNote">Create Credit Note</button>
            </div>

        </div>
        <table id="Table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>CN-2025-001</td>
                    <td>Rajesh Electronics</td>
                    <td>12 Apr, 2025</td>
                    <td>Credit for INV-2025-001</td>
                    <td>₹24,500</td>
                    <td>Processed</td>
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-eye"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i
                                    class="fa-regular fa-pen-to-square"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-print"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-ellipsis"></i></button>

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Quotation table -->
    <div id="quotation" class="billing-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Quotations / Estimates</h1>
            </div>

            <div class="d-flex justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0" placeholder="Search..." />
                </div>
                <button class="btn btn-outline-primary me-2"><i class="fa-solid fa-filter"></i></button>
                <button class="btn btn-outline-primary"><i class="fa-regular fa-calendar"></i></button>
            </div>

            <div class="justify-contnt-end">
                <button class="btn btn-outline-primary" onclick="openInvoiceModal(event)" id="quotation">Create
                    Quotation</button>
            </div>

        </div>
        <table id="Table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>QT-2025-001</td>
                    <td>Rajesh Electronics</td>
                    <td>12 Apr, 2025</td>
                    <td>12 items</td>
                    <td>₹24,500</td>
                    <td>Sent</td>
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-eye"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i
                                    class="fa-regular fa-pen-to-square"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-print"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-ellipsis"></i></button>

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Delivery table -->
    <div id="delivery" class="billing-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Delivery Challans</h1>
            </div>

            <div class="d-flex justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0" placeholder="Search..." />
                </div>
                <button class="btn btn-outline-primary me-2"><i class="fa-solid fa-filter"></i></button>
                <button class="btn btn-outline-primary"><i class="fa-regular fa-calendar"></i></button>
            </div>

            <div class="justify-contnt-end">
                <button class="btn btn-outline-primary" onclick="openSalesModal(event)" id="deliveryChallan">Create Delivery Challan</button>
            </div>

        </div>
        <table id="Table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>DC-2025-001</td>
                    <td>Rajesh Electronics</td>
                    <td>12 Apr, 2025</td>
                    <td>12 items</td>
                    <td>₹24,500</td>
                    <td>Delivered</td>
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-eye"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i
                                    class="fa-regular fa-pen-to-square"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-print"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-ellipsis"></i></button>

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Proforma table -->
    <div id="proforma" class="billing-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Proforma Invoices</h1>
            </div>

            <div class="d-flex justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0" placeholder="Search..." />
                </div>
                <button class="btn btn-outline-primary me-2"><i class="fa-solid fa-filter"></i></button>
                <button class="btn btn-outline-primary"><i class="fa-regular fa-calendar"></i></button>
            </div>

            <div class="justify-contnt-end">
                <button class="btn btn-outline-primary" onclick="openInvoiceModal(event)" id="proformaInvoice">Create
                    Proforma Invoice</button>
            </div>

        </div>
        <table id="Table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>PI-2025-001</td>
                    <td>Rajesh Electronics</td>
                    <td>12 Apr, 2025</td>
                    <td>12 items</td>
                    <td>₹24,500</td>
                    <td>Pending</td>
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-eye"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i
                                    class="fa-regular fa-pen-to-square"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-print"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-ellipsis"></i></button>

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Auto table -->
    <div id="auto" class="billing-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Automated Bills</h1>
            </div>

            <div class="d-flex justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0" placeholder="Search..." />
                </div>
                <button class="btn btn-outline-primary me-2"><i class="fa-solid fa-filter"></i></button>
                <button class="btn btn-outline-primary"><i class="fa-regular fa-calendar"></i></button>
            </div>

            <div class="justify-contnt-end">
                <button class="btn btn-outline-primary" onclick="openSalesModal(event)" id="automatedBill">Create Automated Bills</button>
            </div>

        </div>
        <table id="Table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Description</th>
                    <th>Generation Date</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>AB-2025-001</td>
                    <td>Monthly Subscription</td>
                    <td>12 Apr, 2025</td>
                    <td>Electricity Subscription</td>
                    <td>₹24,500</td>
                    <td>Generated</td>
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-eye"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i
                                    class="fa-regular fa-pen-to-square"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-print"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-ellipsis"></i></button>

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- counter -->
    <div id="counter" class="billing-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Counter Purchases</h1>
            </div>

            <div class="d-flex justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0" placeholder="Search..." />
                </div>
                <button class="btn btn-outline-primary me-2"><i class="fa-solid fa-filter"></i></button>
                <button class="btn btn-outline-primary"><i class="fa-regular fa-calendar"></i></button>
            </div>

            <div class="justify-contnt-end">
                <button class="btn btn-outline-primary" onclick="openSalesModal(event)" id="counterPurchase">Create Counter Purchases</button>
            </div>

        </div>
        <table id="Table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>CP-2025-001</td>
                    <td>Rajesh Electronics</td>
                    <td>12 Apr, 2025</td>
                    <td>12 items</td>
                    <td>₹24,500</td>
                    <td>Completed</td>
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-eye"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i
                                    class="fa-regular fa-pen-to-square"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-print"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-ellipsis"></i></button>

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Payment table -->
    <div id="payment" class="billing-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Payments Out</h1>
            </div>

            <div class="d-flex justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0" placeholder="Search..." />
                </div>
                <button class="btn btn-outline-primary me-2"><i class="fa-solid fa-filter"></i></button>
                <button class="btn btn-outline-primary"><i class="fa-regular fa-calendar"></i></button>
            </div>

            <div class="justify-contnt-end">
                <button class="btn btn-outline-primary" onclick="openSalesModal(event)" id="paymentOut">Create Payments Out</button>
            </div>

        </div>
        <table id="Table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vendor</th>
                    <th>Date</th>
                    <th>Reference</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>PO-2025-001</td>
                    <td>Havells India Ltd.</td>
                    <td>12 Apr, 2025</td>
                    <td>Against Invoice HVL-458</td>
                    <td>₹24,500</td>
                    <td>Processed</td>
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-eye"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i
                                    class="fa-regular fa-pen-to-square"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-print"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-ellipsis"></i></button>

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Purchase table -->
    <div id="purchase" class="billing-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Purchase Returns</h1>
            </div>

            <div class="d-flex justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0" placeholder="Search..." />
                </div>
                <button class="btn btn-outline-primary me-2"><i class="fa-solid fa-filter"></i></button>
                <button class="btn btn-outline-primary"><i class="fa-regular fa-calendar"></i></button>
            </div>

            <div class="justify-contnt-end">
                <button class="btn btn-outline-primary" onclick="openSalesModal(event)" id="purchaseReturn">Create Purchase Returns</button>
            </div>

        </div>
        <table id="Table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vendor</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>PR-2025-001</td>
                    <td>Havells India Ltd.</td>
                    <td>12 Apr, 2025</td>
                    <td>Damaged LED Panels</td>
                    <td>₹24,500</td>
                    <td>Processed</td>
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-eye"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i
                                    class="fa-regular fa-pen-to-square"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-print"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-ellipsis"></i></button>

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Debit table -->
    <div id="debit" class="billing-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Debit Notes</h1>
            </div>

            <div class="d-flex justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0" placeholder="Search..." />
                </div>
                <button class="btn btn-outline-primary me-2"><i class="fa-solid fa-filter"></i></button>
                <button class="btn btn-outline-primary"><i class="fa-regular fa-calendar"></i></button>
            </div>

            <div class="justify-contnt-end">
                <button class="btn btn-outline-primary" onclick="openSalesModal(event)" id="debitNote">Create Debit Notes</button>
            </div>

        </div>
        <table id="Table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vendor</th>
                    <th>Date</th>
                    <th>Reference</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>DN-2025-001</td>
                    <td>Havells India Ltd.</td>
                    <td>12 Apr, 2025</td>
                    <td>For PR-2025-001</td>
                    <td>₹24,500</td>
                    <td>Processed</td>
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-eye"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i
                                    class="fa-regular fa-pen-to-square"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-print"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-ellipsis"></i></button>

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Purchase order table-->
    <div id="purchase_order" class="billing-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Purchase Orders</h1>
            </div>

            <div class="d-flex justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0" placeholder="Search..." />
                </div>
                <button class="btn btn-outline-primary me-2"><i class="fa-solid fa-filter"></i></button>
                <button class="btn btn-outline-primary"><i class="fa-regular fa-calendar"></i></button>
            </div>

            <div class="justify-contnt-end">
                <button class="btn btn-outline-primary" onclick="openInvoiceModal(event)" id="purchaseOrder">Create
                    Purchase Orders</button>
            </div>

        </div>
        <table id="Table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>PO-2025-001</td>
                    <td>Havells India Ltd.</td>
                    <td>12 Apr, 2025</td>
                    <td>Copper Wires</td>
                    <td>₹24,500</td>
                    <td>Received</td>
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-eye"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i
                                    class="fa-regular fa-pen-to-square"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-print"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-ellipsis"></i></button>

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>

        // Bar Chart
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Revenue',
                    data: [15, 18, 25, 22, 28, 32],
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
                            stepSize: 8
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
                labels: ['Cash', 'UPI', 'Card', 'BNPL'],
                datasets: [{
                    data: [45, 30, 15, 10],
                    backgroundColor: [
                        '#0d6efd',  // Blue (Cash)
                        '#20c997',  // Green (UPI)
                        '#ffc107',  // Orange (Card)
                        '#fd7e14',  // Orange-dark (BNPL)
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

        function showbillingTab(id) {
            const tabs = document.querySelectorAll('.billingTab');
            const contents = document.querySelectorAll('.billing-tab-content');

            tabs.forEach(tab => tab.classList.remove('active'));
            contents.forEach(content => content.classList.remove('active'));

            document.querySelector(`#${id}`).classList.add('active');
            document.querySelector(`[onclick="showbillingTab('${id}')"]`).classList.add('active');
        }

        // Create invoice form 

        // let itemIndex = 0;

        // To open form
        function openInvoiceModal(event) {
            const clickedInvoiceButtonId = event.target.id; // To store clicked button ID

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
                <td><input placeholder="Description"/></td>
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

        // To open sales form
        function openSalesModal(event) {
            const clickedSalesButtonId = event.target.id; // To store clicked button ID

            document.getElementById('salesModal').style.display = 'block';

            const tbody = document.querySelector('#salesItemTable tbody');
            if (tbody.children.length === 0) {
                addSalesItem();
            }
        }

        function addSalesItem() {
            const tbody = document.querySelector('#salesItemTable tbody');
            const row = document.createElement('tr');
            row.innerHTML = `
            <td>
                <select onchange="calculateSalesTotals()">
                    <option value="">Select Product</option>
                    <option value="Product A">Product A</option> // Dynamic data from database
                    <option value="Product B">Product B</option> // Dynamic data from database
                    <option value="Product C">Product C</option> // Dynamic data from database
                </select>
            </td>
            <td><input type="text" placeholder="Description"></td>
            <td><input type="number" class="qty" value="1" min="1" oninput="calculateSalesTotals()"></td>
            <td><input type="number" class="price" value="0" min="0" oninput="calculateSalesTotals()"></td>
            <td class="itemTotal">₹0.00</td>
            <td><button class="btn btn-sm btn-outline-danger" onclick="deleteSalesRow(this)">Delete</button></td>
            `;
            tbody.appendChild(row);
            calculateSalesTotals();
        }

        function deleteSalesRow(btn) {
            btn.closest('tr').remove();
            calculateSalesTotals();
        }

        function calculateSalesTotals() {
            let subTotal = 0;
            const rows = document.querySelectorAll('#salesItemTable tbody tr');

            rows.forEach(row => {
                const qty = parseFloat(row.querySelector('.qty')?.value || 0);
                const price = parseFloat(row.querySelector('.price')?.value || 0);
                const total = qty * price;
                row.querySelector('.itemTotal').textContent = `₹${total.toFixed(2)}`;
                subTotal += total;
            });

            const gsttaxRate = parseFloat(document.getElementById('gsttaxRate').value || 0);
            const gst = subTotal * (gsttaxRate / 100);
            const totalWithTax = subTotal + gst;

            document.getElementById('subTotal').textContent = subTotal.toFixed(2);
            document.getElementById('gstTax').textContent = gst.toFixed(2);
            document.getElementById('grandTotal').textContent = totalWithTax.toFixed(2);
            document.getElementById('taxLabel').textContent = `${gsttaxRate}%`;
        }

    </script>