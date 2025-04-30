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

<h1>Inventory Dashboard</h1>
<p>Manage stock, products and suppliers</p>

<!-- Cards -->
<div class="row">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
            <div class="card-body">
                <h6 class="text-muted">Total Products</h6>
                <h3 class="fw-bold">254</h3> <!-- Dynamic data -->
                <p>Across 12 categories</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
            <div class="card-body">
                <h6 class="text-muted">Low Stock Items</h6>
                <h3 class="fw-bold">8</h3> <!-- Dynamic data -->
                <p class="text-danger">Requires attention</p> <!-- Dynamic data -->
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
            <div class="card-body">
                <h6 class="text-muted">Recent Sales</h6>
                <h3 class="fw-bold">₹48,560</h3> <!-- Dynamic data -->
                <p class="text-success">Last 7 days</p> <!-- Dynamic data -->
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
            <div class="card-body">
                <h6 class="text-muted">Pending Orders</h6>
                <h3 class="fw-bold">5</h3> <!-- Dynamic data -->
                <p class="text-info-emphasis">From 3 suppliers</p> <!-- Dynamic data -->
            </div>
        </div>
    </div>
</div>

<!-- Search bar & buttons -->
<div class="container-fluid d-flex justify-content-between align-items-center">

    <div class="d-flex">
        <div class="input-group w-100 me-2">
            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control border-start-0" placeholder="Search..." />
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary d-flex" data-bs-toggle="modal" data-bs-target="#addStock"><i
                    class="fa-solid fa-circle-plus"></i><span> Add</span><span> Stock</span></button>
            <a href="?page=reports" class="btn btn-outline-primary d-flex"><i class="fa-solid fa-chart-column"></i>
                Report</a>
            <button class="btn btn-outline-primary d-flex"><i class="fa-solid fa-arrows-rotate"></i> Refresh</button>
        </div>

    </div>

</div>

<!-- Add Stock form -->
<div class="modal fade" id="addStock" tabindex="-1" aria-labelledby="addStockLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h5 class="modal-title" id="addStockLabel">Add Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="id" class="form-label">Id</label>
                        <input type="text" class="form-control" id="id">
                    </div>

                    <div class="mb-3">
                        <label for="productName" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="productName">
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" class="form-control" id="category">
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="text" class="form-control" id="quantity">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Table -->
<div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">

    <div id="inventory">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Inventory Items</h1>
            </div>

            <div class="justify-content-end">
                <button class="btn btn-outline-primary">View All</button>
            </div>

        </div>
        <table id="Table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Supplier</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>P001</td> <!-- Dynamic data -->
                    <td>2.5mm Copper Wire</td> <!-- Dynamic data -->
                    <td>Wire</td> <!-- Dynamic data -->
                    <td>1250 meters</td> <!-- Dynamic data -->
                    <td>In Stock</td> <!-- Dynamic data -->
                    <td>Copper India Ltd.</td> <!-- Dynamic data -->
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm"><i
                                    class="fa-regular fa-square-plus"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-box"></i></button>

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    </body>

    </html>