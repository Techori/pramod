<h2>Factory Expenses</h2>
<p>Track and manage all factory-related expenditures</p>

<!-- Search and Add User Row -->
<div class="container-fluid d-flex justify-content-between align-items-center mb-3">
    <!-- search bar -->
    <div class="d-flex w-75">
        <div class="input-group w-100 me-2">
            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Search..." />
        </div>
    </div>

    <!-- Button -->
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary"><i class="fa-solid fa-filter"></i></button>
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#dataRange">Data Range</button>
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newExpenses">New
            Expenses</button>
    </div>
</div>

<!-- Data Range Form -->
<div class="modal fade" id="dataRange" tabindex="-1" aria-labelledby="dataRangeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h5 class="modal-title" id="dataRangeLabel">Range</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="from" class="form-label">From</label>
                        <input type="date" class="form-control" id="from">
                    </div>

                    <div class="mb-3">
                        <label for="to" class="form-label">TO</label>
                        <input type="date" class="form-control" id="to">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Expenses Form -->
<div class="modal fade" id="newExpenses" tabindex="-1" aria-labelledby="newExpensesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h5 class="modal-title" id="newExpensesLabel">Add Expenses</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="Id" class="form-label">ID</label>
                        <input type="text" class="form-control" id="Id">
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" class="form-control" id="category">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" class="form-control" id="description">
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="text" class="form-control" id="amount">
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date">
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

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Customer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cards -->
<div class="row">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
            <div class="card-body">
                <h6 class="text-muted">Monthly Expenses</h6>
                <h3 class="fw-bold">₹7,63,800</h3> <!-- Dynamic value from database -->
                <p class="text-success">5.2% vs last month</p> <!-- Dynamic value from database -->
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
            <div class="card-body">
                <h6 class="text-muted">Raw Materials</h6>
                <h3 class="fw-bold">₹2,45,000</h3> <!-- Dynamic value from database -->
                <p class="text-danger">3.8% vs last month</p> <!-- Dynamic value from database -->
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
            <div class="card-body">
                <h6 class="text-muted">Utilities</h6>
                <h3 class="fw-bold">₹1,35,600</h3> <!-- Dynamic value from database -->
                <p class="text-success">2.1% vs last month</p> <!-- Dynamic value from database -->
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
            <div class="card-body">
                <h6 class="text-muted">Pending Payments</h6>
                <h3 class="fw-bold">₹42,800</h3> <!-- Dynamic value from database -->
                <p class="text-danger">1 pending invoice</p> <!-- Dynamic value from database -->
            </div>
        </div>
    </div>
</div>

<!-- Expense Distribution -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div class="justify-content-start">
                <h5 class="mb-0">Expense Distribution</h5>
            </div>
            <!-- <div class="justify-content-end">
                <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-chart-column"></i> Detailed
                    Report</button>
            </div> -->
        </div>
        <small class="text-muted">Breakdown of expenses by category</small>
        <!-- Expense progress bars -->
        <div class="mt-2 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="justify-content-start">
                    <strong>Raw Materials</strong>
                </div>
                <div class="justify-content-end">
                    <small>₹2,45,000 (32%)</small> <!-- Dynamic value from database -->
                </div>
            </div>
            <div class="progress mt-1">
                <div class="progress-bar bg-primary" style="width: 32%"></div> <!-- Dynamic value -->
            </div>
        </div>

        <div class="mt-2 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="justify-content-start">
                    <strong>Salaries & Wages</strong>
                </div>
                <div class="justify-content-end">
                    <small>₹3,25,000 (43%)</small> <!-- Dynamic value from database -->
                </div>
            </div>
            <div class="progress mt-1">
                <div class="progress-bar bg-primary" style="width: 43%"></div> <!-- Dynamic value -->
            </div>
        </div>

        <div class="mt-2 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="justify-content-start">
                    <strong>Utilities</strong>
                </div>
                <div class="justify-content-end">
                    <small>₹1,35,600 (18%)</small> <!-- Dynamic value from database -->
                </div>
            </div>
            <div class="progress mt-1">
                <div class="progress-bar bg-primary" style="width: 18%"></div> <!-- Dynamic value -->
            </div>
        </div>

        <div class="mt-2 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="justify-content-start">
                    <strong>Maintenance</strong>
                </div>
                <div class="justify-content-end">
                    <small>₹58,200 (7%)</small> <!-- Dynamic value from database -->
                </div>
            </div>
            <div class="progress mt-1">
                <div class="progress-bar bg-primary" style="width: 7%"></div> <!-- Dynamic value -->
            </div>
        </div>

        <!-- Cards -->
        <div class="row g-2 mt-2">
            <div class="col-md-3 col-sm-6 mb-2">
                <div class="p-3 border rounded text-center">
                    <small>YTD Expenses</small><br />
                    <strong>₹42,58,400</strong> <!-- Dynamic value from database -->
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-2">
                <div class="p-3 border rounded text-center">
                    <small>Avg. Monthly</small><br />
                    <strong>₹7,09,733</strong> <!-- Dynamic value from database -->
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-2">
                <div class="p-3 border rounded text-center">
                    <small>YoY Change</small><br />
                    <strong>4.3%</strong> <!-- Dynamic value from database -->
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-2">
                <div class="p-3 border rounded text-center">
                    <small>Cost per Unit</small><br />
                    <strong>₹524</strong> <!-- Dynamic value from database -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Expenses table -->
<div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">

    <div id="workers">
        <div class="d-flex justify-content-between align-items-center">
            <div class="justify-content-start">
                <h5 class="mb-0">Recent Expenses</h5>
            </div>
            <div class="justify-content-end">
                <button class="btn btn-outline-primary btn-sm">View All</button>
            </div>
        </div>
        <p>Track all factory expenses and payments</p>
        <table id="Table" class="table table-bordered table-hover" id="supplyTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>EXP-001</td> <!-- Dynamic data -->
                    <td>Raw Materials</td> <!-- Dynamic data -->
                    <td>Copper Wire Procurement</td> <!-- Dynamic data -->
                    <td>₹2,45,000</td> <!-- Dynamic data -->
                    <td>12 Apr, 2025</td> <!-- Dynamic data -->
                    <td>Bank Transfer</td> <!-- Dynamic data -->
                    <td>Paid</td> <!-- Dynamic data -->
                    <td><button class="btn btn-outline-primary btn-sm">View</button></td>
                </tr>
            </tbody>
        </table>
        <script>
  // Search Functionality
  document.getElementById('searchInput').addEventListener('input', function () {
    const searchText = this.value.toLowerCase();
    const rows = document.querySelectorAll('#supplyTable tbody tr');

    rows.forEach(row => {
      const cells = row.getElementsByTagName('td');
      let match = false;
      for (let i = 0; i < cells.length; i++) {
        if (cells[i].textContent.toLowerCase().includes(searchText)) {
          match = true;
          break;
        }
      }
      row.style.display = match ? '' : 'none';
    });
  });
  </script>
    </div>
</div>


<!-- Monthly Expense Comparison table -->
<div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">

    <div id="workers">
        <h5>Monthly Expense Comparison</h5>
        <p>Track monthly expenses by category</p>
        <table id="Table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Raw Materials</th>
                    <th>Utilities</th>
                    <th>Maintenance</th>
                    <th>Salaries</th>
                    <th>Total</th>
                    <th>Change</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Jan</td> <!-- Dynamic data -->
                    <td>₹2,30,000</td> <!-- Dynamic data -->
                    <td>₹1,45,000</td> <!-- Dynamic data -->
                    <td>₹65,000</td> <!-- Dynamic data -->
                    <td>₹3,20,000</td> <!-- Dynamic data -->
                    <td><strong>₹7,60,000</strong></td> <!-- Dynamic data -->
                    <td class="text-success">0.0%</td> <!-- Dynamic data -->
                </tr>
            </tbody>
        </table>
         <script>
  // Search Functionality
  document.getElementById('searchInput').addEventListener('input', function () {
    const searchText = this.value.toLowerCase();
    const rows = document.querySelectorAll('#Table tbody tr');

    rows.forEach(row => {
      const cells = row.getElementsByTagName('td');
      let match = false;
      for (let i = 0; i < cells.length; i++) {
        if (cells[i].textContent.toLowerCase().includes(searchText)) {
          match = true;
          break;
        }
      }
      row.style.display = match ? '' : 'none';
    });
  });
  </script>
    </div>
</div>