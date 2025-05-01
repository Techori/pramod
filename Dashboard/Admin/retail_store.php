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

    .progress {
        height: 10px;
    }

    .alert-card {
        border-radius: 10px;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
    }

    .stock-label {
        font-weight: 500;
    }

    .stock-count {
        font-size: 0.9rem;
        font-weight: 600;
    }

    .tabs {
        display: flex;
        border-bottom: 2px solid #ddd;
    }

    .retailStoreTab {
        padding: 10px 20px;
        cursor: pointer;
        border: none;
        background: none;
        font-size: 16px;
    }

    .retailStoreTab.active {
        border-bottom: 3px solid #007bff;
        font-weight: bold;
        color: #007bff;
    }

    .retailStore-tab-content {
        display: none;
        padding: 20px 0;
    }

    .retailStore-tab-content.active {
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
</style>

<h1>Retail Store Dashboard</h1>
<p>Monitor retail store performance and sales</p>

<!-- Search bar & buttons -->
<div class="container-fluid d-flex justify-content-between align-items-center mb-4">


    <div class="d-flex justify-content-start">
        <div class="input-group w-100 me-2">
            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control border-start-0" placeholder="Search..." />
        </div>
    </div>

    <div class="justify-contnt-end">
        <button class="btn btn-outline-primary" data-bs-toggle="modal"
        data-bs-target="#newSales"><i class="fa-solid fa-cart-plus"></i> New Sales</button>
        <a href="?page=billing_desk" class="btn btn-outline-primary"><i class="fa-solid fa-file-lines"></i> Billing</a>
    </div>
</div>

<!-- Cards -->
<div class="row">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
            <div class="card-body">
                <h6 class="text-muted">Today's Sales</h6>
                <h3 class="fw-bold">₹28,450</h3> <!-- Dynamic data -->
                <p class="text-success">+12.5% vs last month</p> <!-- Dynamic data -->
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
            <div class="card-body">
                <h6 class="text-muted">Customers Today</h6>
                <h3 class="fw-bold">32</h3> <!-- Dynamic data -->
                <p class="text-success">+8.2% vs last month</p> <!-- Dynamic data -->
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
            <div class="card-body">
                <h6 class="text-muted">Items Sold</h6>
                <h3 class="fw-bold">85</h3> <!-- Dynamic data -->
                <p class="text-success">+12.8% vs last month</p> <!-- Dynamic data -->
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
            <div class="card-body">
                <h6 class="text-muted">Average Bill</h6>
                <h3 class="fw-bold">₹1,750</h3> <!-- Dynamic data -->
                <p class="text-danger">3.7% vs last month</p> <!-- Dynamic data -->
            </div>
        </div>
    </div>
</div>

<!-- Buttons -->
<div class="row justify-content-center">
    <div class="col-md-3 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100" data-bs-toggle="modal"
            data-bs-target="#newSales"><i class="fa-solid fa-cart-plus"></i> New
            Sales</button>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <a href="?page=inventory" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-box"></i> Check
            Inventory</a>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-percent"></i>
            Discounts</button>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-file-lines"></i> Daily
            Reports</button>
    </div>
</div>

<!-- New Sale form -->
<div class="modal fade" id="newSales" tabindex="-1" aria-labelledby="newSalesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h5 class="modal-title" id="newSalesLabel">Add Sales</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="saleId" class="form-label">Sale Id</label>
                        <input type="text" class="form-control" id="saleId">
                    </div>

                    <div class="mb-3">
                        <label for="customerName" class="form-label">Customer</label>
                        <input type="text" class="form-control" id="customerName">
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date">
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
                    <button type="submit" class="btn btn-primary">Transfer Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="chart-container">
    <div class="chart-box">
        <h3>Daily Sales (Last Week)</h3>
        <canvas id="barChart"></canvas>
    </div>
    <div class="chart-box">
        <h3>Sales by Category</h3>
        <div style="position: relative; width: 100%; max-width: 300px; margin: 0 auto;">
            <canvas id="pieChart"></canvas>
        </div>
    </div>
</div>

<!-- Popular products -->
<div class="col-md-12 my-4">
    <div class="card p-3 shadow-sm">
        <h5 class="mb-4">
            <strong>Popular Products</strong>
        </h5>
        <div class="row">
            <div class="col-md-4 col-sm-12 mb-2">
                <div class="card stat-card cards shadow-sm" style="background-color:rgb(125, 206, 246);">
                    <div class="card-body">
                        <h5 class="text-muted">Havells Wire</h5>
                        <p>₹65 per meter</p> <!-- Dynamic data -->
                        <p>580 units sold this month</p> <!-- Dynamic data -->
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-12 mb-2">
                <div class="card stat-card cards shadow-sm" style="background-color:rgb(225, 185, 252);">
                    <div class="card-body">
                        <h5 class="text-muted">LED Bulb 9W</h5>
                        <p>₹120 per unit</p> <!-- Dynamic data -->
                        <p>425 units sold this month</p> <!-- Dynamic data -->
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-12 mb-2">
                <div class="card stat-card cards shadow-sm" style="background-color:rgb(248, 249, 165);">
                    <div class="card-body">
                        <h5 class="text-muted">Switch Board</h5>
                        <p>₹350 per unit</p> <!-- Dynamic data -->
                        <p>320 units sold this month</p> <!-- Dynamic data -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Low stock alert bars -->
<div class="card">
    <div class="alert-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><i class="fa-solid fa-circle-exclamation"></i> Low Stock Alerts</h5>
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#requestStock">Request Stock</button>
        </div>

        <!-- Havells Wires -->
        <div class="mb-3">
            <div class="d-flex justify-content-between">
                <span class="stock-label">Havells Wires (1.5 mm)</span>
                <span class="text-danger stock-count">12 units left</span> <!-- Dynamic data -->
            </div>
            <div class="progress bg-light">
                <div class="progress-bar bg-primary" style="width: 25%"></div> <!-- Dynamic data -->
                <div class="progress-bar bg-danger" style="width: 75%"></div> <!-- Dynamic data -->
            </div>
        </div>

        <!-- MCB Switches -->
        <div class="mb-3">
            <div class="d-flex justify-content-between">
                <span class="stock-label">MCB Switches (32A)</span>
                <span class="text-warning stock-count">24 units left</span> <!-- Dynamic data -->
            </div>
            <div class="progress bg-light">
                <div class="progress-bar bg-primary" style="width: 60%"></div> <!-- Dynamic data -->
                <div class="progress-bar bg-warning" style="width: 40%"></div> <!-- Dynamic data -->
            </div>
        </div>

        <!-- Ceiling Fan -->
        <div>
            <div class="d-flex justify-content-between">
                <span class="stock-label">Ceiling Fan (48 inch)</span>
                <span class="text-warning stock-count">18 units left</span> <!-- Dynamic data -->
            </div>
            <div class="progress bg-light">
                <div class="progress-bar bg-primary" style="width: 70%"></div> <!-- Dynamic data -->
                <div class="progress-bar bg-warning" style="width: 30%"></div> <!-- Dynamic data -->
            </div>
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

<!-- Recent sales table -->
<div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">

    <div id="facrtory">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Recent Sales</h1>
            </div>

            <div class="justify-content-center">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0" placeholder="Search..." />
                </div>
            </div>

            <div class="justify-content-end">
                <button class="btn btn-outline-primary"><i class="fa-solid fa-print"></i> Print</button>
                <button class="btn btn-outline-primary"><i class="fa-solid fa-download"></i> Export</button>
            </div>

        </div>
        <table id="Table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>INV-2025-001</td> <!-- Dynamic data -->
                    <td>Raj Kumar</td> <!-- Dynamic data -->
                    <td>13 Apr, 2025</td> <!-- Dynamic data -->
                    <td>3</td> <!-- Dynamic data -->
                    <td>₹5,850</td> <!-- Dynamic data -->
                    <td>Cash</td> <!-- Dynamic data -->
                    <td>Completed</td> <!-- Dynamic data -->
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm"><i
                                    class="fa-solid fa-magnifying-glass"></i></button> <!-- View button -->
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-print"></i></button>
                            <!-- Printing button -->
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i></button>
                            <!-- Download button -->

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Tabels -->
<div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">
    <h4><i class="fa-solid fa-shop"></i> Retail Store Management</h4>
    <p>Create, track, and manage invoices and payments</p>

    <div class="tabs">
        <button class="retailStoreTab active" onclick="showRetailStoreTab('sales')">Sales</button>
        <button class="retailStoreTab" onclick="showRetailStoreTab('inventory')">Inventory</button>
        <button class="retailStoreTab" onclick="showRetailStoreTab('customers')">Customers</button>
    </div>

    <!-- Sales -->
    <div id="sales" class="retailStore-tab-content active">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="d-flex justify-content-start">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0" placeholder="Search..." />
                </div>
                <button class="btn btn-outline-primary me-2"><i class="fa-solid fa-filter"></i></button>
            </div>

            <div class="justify-content-end">
                <button class="btn btn-outline-primary" data-bs-toggle="modal"
                data-bs-target="#newSales"><i class="fa-solid fa-cart-plus"></i> New Sale</button>
            </div>

        </div>
        <table id="Table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Sale ID</th>
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
                    <td>SL-0001</td> <!-- Dynamic data -->
                    <td>11/04/2025</td> <!-- Dynamic data -->
                    <td>Priya Sharma</td> <!-- Dynamic data -->
                    <td>3</td> <!-- Dynamic data -->
                    <td>₹8,750</td> <!-- Dynamic data -->
                    <td>Cash</td> <!-- Dynamic data -->
                    <td>Completed</td> <!-- Dynamic data -->
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-eye"></i></button>
                            <!-- View button -->
                            <button class="btn btn-outline-primary btn-sm"><i
                                    class="fa-regular fa-file-lines"></i></button> <!-- Print button -->
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i></button>
                            <!-- Download button -->

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="row">
            <div class="col-md-4 col-sm-12 mb-2">
                <div class="card stat-card cards shadow-sm">
                    <div class="card-body">
                        <h5 class="text-muted"><i class="fa-regular fa-credit-card"></i> Today's Sales</h5>
                        <p>₹23,070</p> <!-- Dynamic data -->
                        <p>8 transactions</p> <!-- Dynamic data -->
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-12 mb-2">
                <div class="card stat-card cards shadow-sm">
                    <div class="card-body">
                        <h5 class="text-muted"><i class="fa-solid fa-box"></i> Items Sold (Today)</h5>
                        <p>25</p> <!-- Dynamic data -->
                        <p>Across 8 categories</p> <!-- Dynamic data -->
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-12 mb-2">
                <div class="card stat-card cards shadow-sm">
                    <div class="card-body">
                        <h5 class="text-muted"><i class="fa-regular fa-user"></i> New Customers</h5>
                        <p>3</p> <!-- Dynamic data -->
                        <p>Today</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory -->
    <div id="inventory" class="retailStore-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="d-flex justify-content-start">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0" placeholder="Search..." />
                </div>
                <button class="btn btn-outline-primary me-2"><i class="fa-solid fa-filter"></i> Low Stock</button>
            </div>

            <div class="justify-content-end">
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#requestStock"><i class="fa-solid fa-box"></i> Request Stock</button>
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addProductAtStore"><i class="fa-solid fa-plus"></i> Add Product</button>
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
                    <td>STR-001</td> <!-- Dynamic data -->
                    <td>Copper Wire (2.5mm)</td> <!-- Dynamic data -->
                    <td>Wires</td> <!-- Dynamic data -->
                    <td>680</td> <!-- Dynamic data -->
                    <td>₹85/m</td> <!-- Dynamic data -->
                    <td>Shelf A1</td> <!-- Dynamic data -->
                    <td>Completed</td> <!-- Dynamic data --> <!-- Dynamic data -->
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm">Update Stock</button> <!-- Update button -->
                            <button class="btn btn-outline-primary btn-sm"><i
                                    class="fa-regular fa-pen-to-square"></i></button> <!-- Edit button -->

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- customers -->
    <div id="customers" class="retailStore-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="d-flex justify-content-start">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0" placeholder="Search..." />
                </div>
                <button class="btn btn-outline-primary me-2">All Customers</button>
                <button class="btn btn-outline-primary me-2">Retail</button>
                <button class="btn btn-outline-primary me-2">Wholesale</button>
            </div>

            <div class="justify-content-end">
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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>CUST-001</td> <!-- Dynamic data -->
                    <td>Priya Sharma</td> <!-- Dynamic data -->
                    <td>Retail</td> <!-- Dynamic data -->
                    <td>9876543210</td> <!-- Dynamic data -->
                    <td>8</td> <!-- Dynamic data -->
                    <td>₹23,450</td> <!-- Dynamic data -->
                    <td>11/04/2025</td> <!-- Dynamic data -->
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-eye"></i></button>
                            <!-- View button -->
                            <button class="btn btn-outline-primary btn-sm"><i
                                    class="fa-regular fa-pen-to-square"></i></button> <!-- Edit button -->
                            <button class="btn btn-outline-primary btn-sm"><i
                                    class="fa-solid fa-cart-plus"></i></button> <!-- New Sale button -->

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
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

            
    <script>
        // Bar Chart
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Revenue',
                    data: [12500, 9800, 15200, 11300, 18400, 25600, 16800],
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
                            stepSize: 6500
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
                labels: ['Wires & Cables', 'Switches & Sockets', 'Lighting', 'Fans', 'MCBs & DBs', 'Accessories'],
                datasets: [{
                    data: [21, 16, 25, 19, 11, 9],
                    backgroundColor: [
                        '#0d6efd',  // Blue (Wires & Cables)
                        '#20c997',  // Green (Switches & Sockets)
                        '#ffc107',  // Orange (Lighting)
                        '#fd7e14',  // Orange-dark (Fans)
                        '#6f42c1',   // Violet (MCBs & DBs)
                        '#C66EF9'   // Purple (Accessories)
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

        function showRetailStoreTab(id) {
            const tabs = document.querySelectorAll('.retailStoreTab');
            const contents = document.querySelectorAll('.retailStore-tab-content');

            tabs.forEach(tab => tab.classList.remove('active'));
            contents.forEach(content => content.classList.remove('active'));

            document.querySelector(`#${id}`).classList.add('active');
            document.querySelector(`[onclick="showRetailStoreTab('${id}')"]`).classList.add('active');
        }
    </script>

    </body>

    </html>