
  <style>
    .card-metric {
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      border: none;
      transition: transform 0.2s ease;
    }

    .card-metric:hover {
      transform: translateY(-4px);
    }

    .metric-title {
      font-weight: 500;
      color: #444;
    }

    .metric-value {
      font-size: 1.8rem;
      font-weight: bold;
    }

    .quick-access .card {
      cursor: pointer;
      transition: all 0.3s;
    }

    .quick-access .card:hover {
      background-color: #f1f5f9;
    }

    .btn-custom {
      background-color: #007bff;
      color: white;
    }


    .avatar {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      font-size: 0.75rem;
      font-weight: 600;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .avatar-red {
      background-color: #dc3545;
    }

    .avatar-blue {
      background-color: #0d6efd;
    }

    .avatar-green {
      background-color: #28a745;
    }

    .avatar-purple {
      background-color: #6f42c1;
    }

    .dropdown-menu {
      min-width: 200px;
    }

    .dropdown-item i {
      width: 20px;
    }

    .dropdown-item.text-danger i {
      color: #dc3545;
    }

    .chart-container {
      max-width: 800px;
      margin: 50px auto;
      padding: 20px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
  </style>

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="fw-semibold">Reports Dashboard</h2>
        <p class="text-muted">Analyze business performance with detailed reports</p>
      </div>

      <div>
        <button class="btn btn-outline-secondary me-2">Export</button>
        <button class="btn btn-outline-secondary me-2">Share</button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createReportModal">Create
          Report</button>
      </div>
    </div>

    <!-- Filters -->
    <div class="d-flex flex-wrap gap-2 mb-4">
      <input type="search" class="form-control w-auto" placeholder="Search..." />
      <button class="btn btn-outline-secondary">₹</button>
      <select class="form-select w-auto">
        <option>Last 6 Months</option>
      </select>
      <button class="btn btn-outline-secondary"><i class="bi bi-calendar"></i></button>
      <button class="btn btn-outline-secondary"><i class="bi bi-funnel"></i></button>
    </div>

    <!-- Metrics Cards -->
    <div class="row g-4 mb-5">
      <div class="col-md-4">
        <div class="card card-metric p-3">
          <div class="card-body">
            <p class="text-success metric-title">Revenue</p>
            <div class="metric-value">₹27.35L</div>
            <small class="text-muted">Last 6 Months</small>
            <div class="text-success mt-1">▲ 8.2%</div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-metric p-3">
          <div class="card-body">
            <p class="text-danger metric-title">Expenses</p>
            <div class="metric-value">₹23.85L</div>
            <small class="text-muted">Last 6 Months</small>
            <div class="text-danger mt-1">▲ 5.4%</div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-metric p-3">
          <div class="card-body">
            <p class="text-primary metric-title">Net Profit</p>
            <div class="metric-value">₹3.5L</div>
            <small class="text-muted">Last 6 Months</small>
            <div class="text-success mt-1">▲ 12.8%</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Access -->
    <h5 class="mb-3 fw-semibold">Quick Access Reports</h5>
    <div class="row g-3 quick-access">
      <div class="col-md-3">
        <div class="card p-3">
          <div><strong>Bill-wise Profit</strong></div>
          <small class="text-muted">Analyze profit margins per bill/invoice</small>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card p-3">
          <div><strong>Sales Summary</strong></div>
          <small class="text-muted">Overview of sales performance</small>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card p-3">
          <div><strong>Daybook</strong></div>
          <small class="text-muted">Daily transaction record</small>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card p-3">
          <div><strong>Profit and Loss</strong></div>
          <small class="text-muted">Financial performance analysis</small>
        </div>
      </div>
    </div>

    <!-- Nav Tabs -->
    <ul class="nav nav-tabs mb-4" id="adminTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial"
          type="button" role="tab">Financial</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button"
          role="tab">Sales</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button"
          role="tab">Inventory</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tax-tab" data-bs-toggle="tab" data-bs-target="#tax" type="button" role="tab">Tax &
          GST</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="custom-tab" data-bs-toggle="tab" data-bs-target="#custom" type="button"
          role="tab">Custom Reports</button>
      </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="adminTabContent">

      <!-- Financial Tab -->
      <div class="tab-pane fade show active" id="financial" role="tabpanel" aria-labelledby="financial-tab">
        <div class="row mt-4 mx-5">

          <div class="container chart-container">
            <h3 class="text-center mb-4">Revenue vs Expenses</h3>
            <canvas id="revenueExpensesChart"></canvas>
          </div>
        </div>
        <!-- added graph -->
        <div class="row mt-4">
          <div class="col-md-6">
            <div class="card p-3">
              <h6>Revenue Trend</h6>
              <canvas id="spendingChart2" height="220"></canvas>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card p-3">
              <h6>Expense Trend</h6>
              <canvas id="spendingChart3" height="220"></canvas>
            </div>
          </div>
        </div>
        <div class="card mt-4">
          <div class="card-body">
            <h5 class="card-title mb-3">Financial Summary</h5>

            <div class="d-flex justify-content-between align-items-start text-center"
              style="height: 150px; border: 1px solid #ccc; padding: 1rem;">

              <!-- Left Aligned Box -->
              <div>
                <p>Gross Margin</p>
                <h5>32.5%</h5>
              </div>

              <!-- Center Aligned Box -->
              <div>
                <p>Gross Margin</p>
                <h5>32.5%</h5>
              </div>

              <!-- Right Aligned Box -->
              <div>
                <p>Gross Margin</p>
                <h5>32.5%</h5>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h4 class="mb-0">Recent Financial Reports</h4>
              <button class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh
              </button>
            </div>
            <div class="table-responsive">
              <table class="table align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Report</th>
                    <th>Created By</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Size</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>

                  <!-- Row 1 -->
                  <tr>
                    <td><strong><i class="fa-brands fa-salesforce"></i>Monthly Sales Summary – March
                        2023</strong><br><small class="text-muted">Sales Summary • REP-001</small></td>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-red me-2">RK</div>Rajesh Kumar
                      </div>
                    </td>
                    <td>2023-04-01 14:30</td>
                    <td><span class="badge bg-light text-success border border-success"><i
                          class="bi bi-file-earmark-check"></i> Ready</span></td>
                    <td>2.4 MB</td>
                    <td>
                      <i class="bi bi-eye me-2"></i><i class="bi bi-download me-2"></i>
                      <div class="dropdown d-inline">
                        <button class="btn btn-sm" data-bs-toggle="dropdown"><i
                            class="bi bi-three-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                          <li>
                            <h6 class="dropdown-header">Actions</h6>
                          </li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-eye"></i> View Report</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-download"></i> Download</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-printer"></i> Print</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-envelope"></i> Send Email</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-share"></i> Share</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-calendar2-plus"></i> Schedule</a></li>
                          <li>
                            <hr class="dropdown-divider">
                          </li>
                          <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash"></i> Delete</a></li>
                        </ul>
                      </div>
                    </td>
                  </tr>

                  <!-- Row 2 -->
                  <tr>
                    <td><strong><i class="fa-solid fa-cart-flatbed"></i> Stock Levels – Q1 2023</strong><br><small
                        class="text-muted">Stock Summary • REP-002</small></td>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-blue me-2">PS</div>Priya Sharma
                      </div>
                    </td>
                    <td>2023-03-31 09:15</td>
                    <td><span class="badge bg-light text-success border border-success"><i
                          class="bi bi-file-earmark-check"></i> Ready</span></td>
                    <td>3.1 MB</td>
                    <td><i class="bi bi-eye me-2"></i><i class="bi bi-download me-2"></i>
                      <div class="dropdown d-inline">
                        <button class="btn btn-sm" data-bs-toggle="dropdown"><i
                            class="bi bi-three-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                          <li>
                            <h6 class="dropdown-header">Actions</h6>
                          </li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-eye"></i> View Report</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-download"></i> Download</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-printer"></i> Print</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-envelope"></i> Send Email</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-share"></i> Share</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-calendar2-plus"></i> Schedule</a></li>
                          <li>
                            <hr class="dropdown-divider">
                          </li>
                          <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash"></i> Delete</a></li>
                        </ul>
                      </div>
                    </td>
                  </tr>

                  <!-- Row 3 -->
                  <tr>
                    <td><strong><i class="fa-solid fa-clock-rotate-left"></i>Customer Payment History – March
                        2023</strong><br><small class="text-muted">Payment Report • REP-003</small></td>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-green me-2">AP</div>Amit Patel
                      </div>
                    </td>
                    <td>2023-03-30 11:45</td>
                    <td><span class="badge bg-light text-success border border-success"><i
                          class="bi bi-file-earmark-check"></i> Ready</span></td>
                    <td>1.8 MB</td>
                    <td><i class="bi bi-eye me-2"></i><i class="bi bi-download me-2"></i>

                      <div class="dropdown d-inline">
                        <button class="btn btn-sm" data-bs-toggle="dropdown"><i
                            class="bi bi-three-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                          <li>
                            <h6 class="dropdown-header">Actions</h6>
                          </li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-eye"></i> View Report</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-download"></i> Download</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-printer"></i> Print</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-envelope"></i> Send Email</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-share"></i> Share</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-calendar2-plus"></i> Schedule</a></li>
                          <li>
                            <hr class="dropdown-divider">
                          </li>
                          <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash"></i> Delete</a></li>
                        </ul>
                      </div>
                    </td>
                  </tr>

                  <!-- Row 4 -->
                  <tr>
                    <td><strong><i class="fa-solid fa-plus-minus"></i>Profit & Loss Statement – Q1
                        2023</strong><br><small class="text-muted">Financial Report • REP-004</small></td>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-purple me-2">NS</div>Neha Singh
                      </div>
                    </td>
                    <td>2023-04-10 16:20</td>
                    <td><span class="badge bg-light text-primary border border-primary"><i
                          class="bi bi-hourglass-split"></i> Processing</span></td>
                    <td>Calculating...</td>
                    <td><i class="bi bi-eye me-2"></i><i class="bi bi-download me-2"></i>
                      <div class="dropdown d-inline">
                        <button class="btn btn-sm" data-bs-toggle="dropdown"><i
                            class="bi bi-three-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                          <li>
                            <h6 class="dropdown-header">Actions</h6>
                          </li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-eye"></i> View Report</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-download"></i> Download</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-printer"></i> Print</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-envelope"></i> Send Email</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-share"></i> Share</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-calendar2-plus"></i> Schedule</a></li>
                          <li>
                            <hr class="dropdown-divider">
                          </li>
                          <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash"></i> Delete</a></li>
                        </ul>
                      </div>
                    </td>
                  </tr>

                  <!-- Row 5 -->
                  <tr>
                    <td><strong><i class="fa-brands fa-product-hunt"></i> Product Performance Analysis – March
                        2023</strong><br><small class="text-muted">Sales Analysis • REP-005</small></td>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-red me-2">VM</div>Vikram Mehta
                      </div>
                    </td>
                    <td>2023-03-29 13:10</td>
                    <td><span class="badge bg-light text-danger border border-danger"><i class="bi bi-x-circle"></i>
                        Failed</span></td>
                    <td>N/A</td>
                    <td><i class="bi bi-eye me-2"></i><i class="bi bi-download me-2"></i>

                      <div class="dropdown d-inline">
                        <button class="btn btn-sm" data-bs-toggle="dropdown"><i
                            class="bi bi-three-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                          <li>
                            <h6 class="dropdown-header">Actions</h6>
                          </li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-eye"></i> View Report</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-download"></i> Download</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-printer"></i> Print</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-envelope"></i> Send Email</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-share"></i> Share</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-calendar2-plus"></i> Schedule</a></li>
                          <li>
                            <hr class="dropdown-divider">
                          </li>
                          <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash"></i> Delete</a></li>
                        </ul>
                      </div>
                    </td>
                  </tr>

                  <!-- Row 6 -->
                  <tr>
                    <td><strong><i class="fa-solid fa-money-bill"></i> Vendor Payment Summary – March
                        2023</strong><br><small class="text-muted">Payment Report • REP-006</small></td>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-red me-2">SJ</div>Sunita Joshi
                      </div>
                    </td>
                    <td>2023-03-28 10:40</td>
                    <td><span class="badge bg-light text-success border border-success"><i
                          class="bi bi-file-earmark-check"></i> Ready</span></td>
                    <td>1.5 MB</td>
                    <td><i class="bi bi-eye me-2"></i><i class="bi bi-download me-2"></i>

                      <div class="dropdown d-inline">
                        <button class="btn btn-sm" data-bs-toggle="dropdown"><i
                            class="bi bi-three-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                          <li>
                            <h6 class="dropdown-header">Actions</h6>
                          </li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-eye"></i> View Report</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-download"></i> Download</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-printer"></i> Print</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-envelope"></i> Send Email</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-share"></i> Share</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-calendar2-plus"></i> Schedule</a></li>
                          <li>
                            <hr class="dropdown-divider">
                          </li>
                          <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash"></i> Delete</a></li>
                        </ul>
                      </div>
                    </td>
                  </tr>

                  <!-- Row 7 -->
                  <tr>
                    <td><strong><i class="fa-solid fa-database"></i> GST Return Data - March 2023</strong><br><small
                        class="text-muted">Tax Report • REP-007</small></td>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="avatar avatar-purple me-2">RV</div>Rahul Verma
                      </div>
                    </td>
                    <td>2023-04-05 09:30</td>
                    <td><span class="badge bg-light text-success border border-success"><i
                          class="bi bi-file-earmark-check"></i> Ready</span></td>
                    <td>4.2 MB</td>
                    <td><i class="bi bi-eye me-2"></i><i class="bi bi-download me-2"></i>

                      <div class="dropdown d-inline">
                        <button class="btn btn-sm" data-bs-toggle="dropdown"><i
                            class="bi bi-three-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                          <li>
                            <h6 class="dropdown-header">Actions</h6>
                          </li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-eye"></i> View Report</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-download"></i> Download</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-printer"></i> Print</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-envelope"></i> Send Email</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-share"></i> Share</a></li>
                          <li><a class="dropdown-item" href="#"><i class="bi bi-calendar2-plus"></i> Schedule</a></li>
                          <li>
                            <hr class="dropdown-divider">
                          </li>
                          <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash"></i> Delete</a></li>
                        </ul>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <!-- Sales Tab -->
      <div class="tab-pane fade" id="sales" role="tabpanel" aria-labelledby="sales-tab">
        <div class="row mt-4 mx-5">
          <div class="container chart-container">
            <h6 class="text-center mb-4">Monthly Sales</h6>
            <canvas id="monthlySalesChart" height="220"></canvas>
          </div>
        </div>
        <div class="row mt-4">
          <div class="col-md-6">
            <div class="card p-3">
              <h6>Online Sales</h6>
              <canvas id="onlineSalesChart" height="220"></canvas>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card p-3">
              <h6>Retail Sales</h6>
              <canvas id="retailSalesChart" height="220"></canvas>
            </div>
          </div>
        </div>
        <div class="row mt-4 mx-5">
          <div class="container chart-container">
            <h6 class="text-center mb-4">Top Selling Products</h6>
            <canvas id="topProductsChart" height="220"></canvas>
          </div>
        </div>
      </div>
      <!-- Invetory -->
      <div class="tab-pane fade" id="inventory" role="tabpanel" aria-labelledby="inventory-tab">
        <!-- Metrics Cards -->
        <div class="row g-4 mb-5">
          <div class="col-md-4">
            <div class="card card-metric p-3">
              <div class="card-body">
                <p class="text-success metric-title">Total Inventory Value</p>
                <div class="metric-value">₹18.45L</div>
                <small class="text-muted">3.2%</small>
                <div class="text-success mt-1">▲ 8.2%</div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card card-metric p-3">
              <div class="card-body">
                <p class="text-danger metric-title">Expenses</p>
                <div class="metric-value">₹23.85L</div>
                <small class="text-muted">Last 6 Months</small>
                <div class="text-danger mt-1">▲ 5.4%</div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card card-metric p-3">
              <div class="card-body">
                <p class="text-primary metric-title">Net Profit</p>
                <div class="metric-value">₹3.5L</div>
                <small class="text-muted">Last 6 Months</small>
                <div class="text-success mt-1">▲ 12.8%</div>
              </div>
            </div>
          </div>
        </div>
        <div class="card mt-4">
          <div class="card-body">
            <h5 class="card-title mb-3">Financial Summary</h5>
            <div class="btn-group btn-group-lg" role="group" aria-label="Large button group">
              <button type="button" class="btn btn-outline-primary py-4 px-5 fs-4" style="min-width: 200px;">
                <i class="fa-solid fa-money-bill-trend-up"></i> Stock Summary
              </button>
            </div>
            <div class="btn-group btn-group-lg" role="group" aria-label="Large button group">
              <button type="button" class="btn btn-outline-primary py-4 px-5 fs-4" style="min-width: 200px;">
                <i class="fa-solid fa-money-bill"></i>Price & Stock Summary
              </button>
            </div>
            <div class="btn-group btn-group-lg" role="group" aria-label="Large button group">
              <button type="button" class="btn btn-outline-primary py-4 px-5 fs-4" style="min-width: 200px;">
                <i class="fa-solid fa-graduation-cap"></i> Inventory Valuation
              </button>
            </div>
            <div class="btn-group btn-group-lg" role="group" aria-label="Large button group">
              <button type="button" class="btn btn-outline-primary py-4 px-5 fs-4" style="min-width: 200px;">
                <i class="fa-solid fa-traffic-light"></i> Slow Moving Items
              </button>
            </div>
            <div class="btn-group btn-group-lg" role="group" aria-label="Large button group">
              <button type="button" class="btn btn-outline-primary py-4 px-5 fs-4" style="min-width: 200px;">
                <i class="fa-solid fa-money-bill-transfer"></i> Stock Transfer Report
              </button>
            </div>
            <div class="btn-group btn-group-lg" role="group" aria-label="Large button group">
              <button type="button" class="btn btn-outline-primary py-4 px-5 fs-4" style="min-width: 200px;">
                <i class="fa-solid fa-bath"></i> Batch Wise Stock
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- Tax & GST -->
      <div class="tab-pane fade" id="tax" role="tabpanel" aria-labelledby="tax-tab">
        <!-- Metrics Cards -->
        <div class="row g-4 mb-5">
          <div class="col-md-4">
            <div class="card card-metric p-3">
              <div class="card-body">
                <p class="text-success metric-title">GST Payable</p>
                <div class="metric-value">₹18.45L</div>
                <small class="text-muted">3.2%</small>
                <div class="text-success mt-1">▲ 8.2%</div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card card-metric p-3">
              <div class="card-body">
                <p class="text-danger metric-title">GST Receivable</p>
                <div class="metric-value">₹23.85L</div>
                <small class="text-muted">Last 6 Months</small>
                <div class="text-danger mt-1">▲ 5.4%</div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card card-metric p-3">
              <div class="card-body">
                <p class="text-primary metric-title">Net GST</p>
                <div class="metric-value">₹3.5L</div>
                <small class="text-muted">Last 6 Months</small>
                <div class="text-success mt-1">▲ 12.8%</div>
              </div>
            </div>
          </div>
        </div>
        <div class="card mt-4">
          <div class="card-body">
            <h5 class="card-title mb-3">Tax Reports</h5>
            <div class="btn-group btn-group-lg" role="group" aria-label="Large button group">
              <button type="button" class="btn btn-outline-primary py-4 px-5 fs-4" style="min-width: 200px;">
                <i class="fa-solid fa-money-bill-trend-up"></i> GSTR-1 Summary
              </button>
            </div>
            <div class="btn-group btn-group-lg" role="group" aria-label="Large button group">
              <button type="button" class="btn btn-outline-primary py-4 px-5 fs-4" style="min-width: 200px;">
                <i class="fa-solid fa-money-bill"></i>GSTR-2 Summary
              </button>
            </div>
            <div class="btn-group btn-group-lg" role="group" aria-label="Large button group">
              <button type="button" class="btn btn-outline-primary py-4 px-5 fs-4" style="min-width: 200px;">
                <i class="fa-solid fa-graduation-cap"></i> GSTR-3B Summary
              </button>
            </div>
            <div class="btn-group btn-group-lg" role="group" aria-label="Large button group">
              <button type="button" class="btn btn-outline-primary py-4 px-5 fs-4" style="min-width: 200px;">
                <i class="fa-solid fa-traffic-light"></i> HSN Summary
              </button>
            </div>
            <div class="btn-group btn-group-lg" role="group" aria-label="Large button group">
              <button type="button" class="btn btn-outline-primary py-4 px-5 fs-4" style="min-width: 200px;">
                <i class="fa-solid fa-money-bill-transfer"></i> E-way Bill Register
              </button>
            </div>
            <div class="btn-group btn-group-lg" role="group" aria-label="Large button group">
              <button type="button" class="btn btn-outline-primary py-4 px-5 fs-4" style="min-width: 200px;">
                <i class="fa-solid fa-bath"></i> Tax Liability Report
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- Custom Reports -->
      <div class="tab-pane fade" id="custom" role="tabpanel" aria-labelledby="custom-tab">
        <div class="container my-5">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Custom Reports</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createReportModal">
              + Create New Report
            </button>
          </div>

          <input type="text" class="form-control mb-4" placeholder="Search saved reports...">

          <div class="row g-3">
            <div class="col-md-6">
              <div class="report-card">
                <h5>Monthly Customer Retention</h5>
                <p>Created on 10 Apr, 2025</p>
                <div class="report-actions">
                  <div class="dropdown">
                    <button class="btn btn-link" type="button" data-bs-toggle="dropdown">
                      <i class="bi bi-download"></i>
                    </button>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item">Excel Format</a></li>
                      <li><a class="dropdown-item">PDF Format</a></li>
                    </ul>
                  </div>
                  <div> <button class="btn btn-link" type="button" data-bs-toggle="dropdown">
                      <i class="bi bi-eye"></i>
                    </button></div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="report-card">
                <h5>Product Line Performance</h5>
                <p>Created on 05 Apr, 2025</p>
                <div class="report-actions">
                  <div class="dropdown">
                    <button class="btn btn-link" type="button" data-bs-toggle="dropdown">
                      <i class="bi bi-download"></i>
                    </button>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item">Excel Format</a></li>
                      <li><a class="dropdown-item">PDF Format</a></li>
                    </ul>
                  </div>
                  <div> <button class="btn btn-link" type="button" data-bs-toggle="dropdown">
                      <i class="bi bi-eye"></i>
                    </button></div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="report-card">
                <h5>Vendor Performance Analysis</h5>
                <p>Created on 01 Apr, 2025</p>
                <div class="report-actions">
                  <div class="dropdown">
                    <button class="btn btn-link" type="button" data-bs-toggle="dropdown">
                      <i class="bi bi-download"></i>
                    </button>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item">Excel Format</a></li>
                      <li><a class="dropdown-item">PDF Format</a></li>
                    </ul>
                  </div>
                  <div> <button class="btn btn-link" type="button" data-bs-toggle="dropdown">
                      <i class="bi bi-eye"></i>
                    </button></div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="report-card">
                <h5>Marketing Campaign ROI</h5>
                <p>Created on 25 Mar, 2025</p>
                <div class="report-actions">
                  <div class="dropdown">
                    <button class="btn btn-link" type="button" data-bs-toggle="dropdown">
                      <i class="bi bi-download"></i>
                    </button>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item">Excel Format</a></li>
                      <li><a class="dropdown-item">PDF Format</a></li>
                    </ul>
                  </div>
                  <div> <button class="btn btn-link" type="button" data-bs-toggle="dropdown">
                      <i class="bi bi-eye"></i>
                    </button></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Popup -->
        <div class="modal fade" id="createReportModal" tabindex="-1" aria-labelledby="createReportModalLabel"
          aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Create Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <small class="text-muted">Generate detailed reports for business insights</small>
                <div class="mt-3">
                  <label class="form-label">Report Type</label>
                  <select class="form-select">
                    <option>Bill-wise Profit</option>
                    <option>Product Performance</option>
                    <option>Customer Retention</option>
                  </select>
                </div>
                <div class="mt-3">
                  <label class="form-label">Date Range</label>
                  <select class="form-select">
                    <option>This Month</option>
                    <option>Last Month</option>
                    <option>This Year</option>
                  </select>
                </div>
                <div class="mt-3">
                  <label class="form-label">Additional Options</label><br>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="taxInfo">
                    <label class="form-check-label" for="taxInfo">Include Tax Information</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="cancelledTransactions">
                    <label class="form-check-label" for="cancelledTransactions">Include Cancelled Transactions</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="detailedView">
                    <label class="form-check-label" for="detailedView">Show Detailed View</label>
                  </div>
                </div>
                <div class="mt-3">
                  <label class="form-label">Report Format</label><br>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="reportFormat" id="detailed" checked>
                    <label class="form-check-label" for="detailed">Detailed</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="reportFormat" id="summary">
                    <label class="form-check-label" for="summary">Summary</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="reportFormat" id="consolidated">
                    <label class="form-check-label" for="consolidated">Consolidated</label>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Generate Report</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    <script>
      const tabs = document.querySelectorAll('.tab-btn');
      const contents = document.querySelectorAll('.tab-content');

      tabs.forEach(tab => {
        tab.addEventListener('click', () => {
          // Remove active class from all buttons and contents
          tabs.forEach(btn => btn.classList.remove('active'));
          contents.forEach(content => content.classList.remove('active'));

          // Add active class to current button and its target content
          tab.classList.add('active');
          const targetId = tab.getAttribute('data-target');
          document.getElementById(targetId).classList.add('active');
        });
      });

      // Revenue vs Expenses Chart
      const ctx = document.getElementById('revenueExpensesChart').getContext('2d');
      const revenueExpensesChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['January', 'February', 'March', 'April', 'May'],
          datasets: [
            {
              label: 'Revenue',
              data: [12000, 15000, 13000, 17000, 16000],
              backgroundColor: 'rgba(54, 162, 235, 0.6)',
              borderColor: 'rgba(54, 162, 235, 1)',
              borderWidth: 1
            },
            {
              label: 'Expenses',
              data: [8000, 9000, 7000, 11000, 9500],
              backgroundColor: 'rgba(255, 99, 132, 0.6)',
              borderColor: 'rgba(255, 99, 132, 1)',
              borderWidth: 1
            }
          ]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });

      // Revenue Trend Chart
      const ctxRevenue = document.getElementById('spendingChart2').getContext('2d');
      const revenueChart = new Chart(ctxRevenue, {
        type: 'line',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
          datasets: [{
            label: 'Revenue',
            data: [10000, 12000, 14000, 13000, 16000, 18000],
            backgroundColor: 'rgba(54, 162, 235, 0.1)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointRadius: 4,
            pointBackgroundColor: 'rgba(54, 162, 235, 1)'
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: { beginAtZero: false }
          }
        }
      });

      // Expense Trend Chart
      const ctxExpense = document.getElementById('spendingChart3').getContext('2d');
      const expenseChart = new Chart(ctxExpense, {
        type: 'line',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
          datasets: [{
            label: 'Expenses',
            data: [7000, 8000, 7500, 9000, 8500, 9500],
            backgroundColor: 'rgba(255, 99, 132, 0.1)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointRadius: 4,
            pointBackgroundColor: 'rgba(255, 99, 132, 1)'
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: { beginAtZero: false }
          }
        }
      });

      // pie graph added
      // Online Sales Pie Chart
      const pieCtx = document.getElementById('onlineSalesChart').getContext('2d');
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

      // Retail Sales Pie Chart
      const retailCtx = document.getElementById('retailSalesChart').getContext('2d');
      new Chart(retailCtx, {
        type: 'pie',
        data: {
          labels: ['In-Store', 'Distributors', 'Pop-up Stores'],
          datasets: [{
            label: 'Retail Sales',
            data: [40, 35, 25], // Sample values in %
            backgroundColor: ['#4BC0C0', '#9966FF', '#FF9F40']
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'bottom'
            }
          }
        }
      });

      // monthly chartadded
      const monthlyCtx = document.getElementById('monthlySalesChart').getContext('2d');
      new Chart(monthlyCtx, {
        type: 'bar',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
          datasets: [{
            label: 'Sales (₹)',
            data: [12000, 15000, 14000, 18000, 22000, 20000, 25000, 23000, 21000, 24000, 26000, 30000],
            backgroundColor: '#36A2EB'
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
      // topselling products chart added
      const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
      new Chart(topProductsCtx, {
        type: 'bar',
        data: {
          labels: ['Product A', 'Product B', 'Product C', 'Product D', 'Product E'],
          datasets: [{
            label: 'Units Sold',
            data: [120, 100, 90, 80, 60],
            backgroundColor: [
              '#FF6384',
              '#36A2EB',
              '#FFCE56',
              '#4BC0C0',
              '#9966FF'
            ]
          }]
        },
        options: {
          indexAxis: 'y', // Makes it horizontal
          responsive: true,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            x: {
              beginAtZero: true
            }
          }
        }
      });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>