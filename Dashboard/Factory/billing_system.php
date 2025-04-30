<h2>Factory Billing System</h2>
<p>Manage all factory-related invoices, receipts and payments</p>

<!-- Search and Add User Row -->
<div class="container-fluid d-flex justify-content-between align-items-center mb-3">
    <!-- search bar -->
    <div class="d-flex w-75">
        <div class="input-group w-100 me-2">
            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control border-start-0" placeholder="Search..." />
        </div>
    </div>

    <!-- Button -->
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary"><i class="fa-solid fa-filter"></i></button>
        <button class="btn btn-outline-primary"> Data Range</button>
        <button class="btn btn-outline-primary">Create Invoice</button>
    </div>
</div>

<!-- Buttons -->
<div class="row justify-content-center">
    <div class="col-md-3 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-file"></i> Create
            Factory Invoice</button>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-regular fa-file-word"></i>
            Generate Receipt</button>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-download"></i> Wxport
            Records</button>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-regular fa-circle-check"></i>
            Reconcile Payments</button>
    </div>
</div>

<!-- Factory Invoice table -->
<div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">

    <div id="workers">
        <div class="d-flex justify-content-between align-items-center">
            <div class="justify-content-start">
                <h5 class="mb-0">Factory Invoices</h5>
            </div>
            <div class="justify-content-end">
                <button class="btn btn-outline-primary btn-sm">View All</button>
            </div>
        </div>
        <table id="Table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Invoice ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>FB-2025-001</td> <!-- Dynamic data -->
                    <td>Metro Electric Corp</td> <!-- Dynamic data -->
                    <td>08 Apr, 2025</td> <!-- Dynamic data -->
                    <td>₹1,24,500</td> <!-- Dynamic data -->
                    <td>Production</td> <!-- Dynamic data -->
                    <td>Paid</td> <!-- Dynamic data -->
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-print"></i></button>
                            <button class="btn btn-outline-primary btn-sm"><i
                                    class="fa-regular fa-file-lines"></i></button>

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Reminders -->
<div class="row">
    <div class="col-md-6 col-sm-12 my-4">
        <div class="card stat-card cards shadow-sm" style="background-color:rgb(255, 250, 232);">
            <div class="card-body">
                <h5 class="text-warning"><i class="fa-solid fa-triangle-exclamation"></i> Payment Reminders</h5>
                <p>2 factory invoices are overdue and require immediate attention</p>
                <button type="button" class="btn btn-outline-warning">Send Reminders</button>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-12 my-4">
        <div class="card stat-card cards shadow-sm" style="background-color: #e7f3ff;">
            <div class="card-body">
                <h5 class="text-primary"><i class="fa-regular fa-circle-check"></i> This Month's Production Billing</h5>
                <p>₹18,42,850 billed this month for production services</p>
                <button type="button" class="btn btn-outline-primary">View Details</button>
            </div>
        </div>
    </div>
</div>