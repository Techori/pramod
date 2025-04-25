<?php 
session_start();
if (!isset($_SESSION["uid"], $_SESSION["user_type"], $_SESSION["session_id"])) {
    header("location:../../login.php");
    exit;
}
if (in_array($_SESSION["user_type"], ['Factory', 'Store', 'Vendor'])) {
    header("location:../index.php");
    exit;
}
if ($_SESSION["user_type"] !== 'Admin') {
    header("location:../../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Shree Unnati Wires & Traders - Premium Wire Manufacturing</title>
  
  <!-- Only latest Bootstrap included -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    body {
      background-color: #f9fafa;
    }
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
    .avatar-red { background-color: #dc3545; }
    .avatar-blue { background-color: #0d6efd; }
    .avatar-green { background-color: #28a745; }
    .avatar-purple { background-color: #6f42c1; }

    .dropdown-menu {
      min-width: 200px;
    }
    .dropdown-item i {
      width: 20px;
    }
    .dropdown-item.text-danger i {
      color: #dc3545;
    }
  </style>
</head>
<body class="bg-secondary bg-opacity-10">
    <?php
        include('./_admin_nav.php');
    ?>
   
   <div class="main-content">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="fw-semibold">Reports Dashboard</h2>
      <p class="text-muted">Analyze business performance with detailed reports</p>
    </div>
    
    <div>
      <button class="btn btn-outline-secondary me-2">Export</button>
      <button class="btn btn-outline-secondary me-2">Share</button>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createReportModal">Create Report</button>
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
           <button class="nav-link active" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial" type="button" role="tab">Financial</button>
         </li>
        <li class="nav-item" role="presentation">
         <button class="nav-link" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button" role="tab">Sales</button>
       </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button" role="tab">Inventory</button>
     </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="tax-tab" data-bs-toggle="tab" data-bs-target="#tax" type="button" role="tab">Tax & GST</button>
    </li>
   </ul>

<!-- Tab Content -->
<div class="tab-content" id="adminTabContent">

  <!-- Financial Tab -->
  <div class="tab-pane fade show active" id="financial" role="tabpanel" aria-labelledby="financial-tab">
    <div class="row mt-4 mx-5">
      <div class="col-md-6">
        <div class="card p-3">
          <h6>Revenue vs Expenses Chart</h6>
          <canvas id="spendingChart1" height="220"></canvas>
        </div>
      </div>
    </div>
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
       
        <div class="d-flex justify-content-between align-items-start text-center" style="height: 150px; border: 1px solid #ccc; padding: 1rem;">
  
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

        <div class="table-responsive">
          <table class="table align-middle">
            <!-- Table content goes here -->
          </table>
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
              <td><strong><i class="fa-brands fa-salesforce"></i>Monthly Sales Summary – March 2023</strong><br><small class="text-muted">Sales Summary • REP-001</small></td>
              <td><div class="d-flex align-items-center"><div class="avatar avatar-red me-2">RK</div>Rajesh Kumar</div></td>
              <td>2023-04-01 14:30</td>
              <td><span class="badge bg-light text-success border border-success"><i class="bi bi-file-earmark-check"></i> Ready</span></td>
              <td>2.4 MB</td>
              <td>
                <i class="bi bi-eye me-2"></i><i class="bi bi-download me-2"></i>
                <div class="dropdown d-inline">
                  <button class="btn btn-sm" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                  <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><h6 class="dropdown-header">Actions</h6></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-eye"></i> View Report</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-download"></i> Download</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-printer"></i> Print</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-envelope"></i> Send Email</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-share"></i> Share</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-calendar2-plus"></i> Schedule</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash"></i> Delete</a></li>
                  </ul>
                </div>
              </td>
            </tr>

            <!-- Row 2 -->
            <tr>
              <td><strong><i class="fa-solid fa-cart-flatbed"></i> Stock Levels – Q1 2023</strong><br><small class="text-muted">Stock Summary • REP-002</small></td>
              <td><div class="d-flex align-items-center"><div class="avatar avatar-blue me-2">PS</div>Priya Sharma</div></td>
              <td>2023-03-31 09:15</td>
              <td><span class="badge bg-light text-success border border-success"><i class="bi bi-file-earmark-check"></i> Ready</span></td>
              <td>3.1 MB</td>
              <td><i class="bi bi-eye me-2"></i><i class="bi bi-download me-2"></i>
              <div class="dropdown d-inline">
                  <button class="btn btn-sm" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                  <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><h6 class="dropdown-header">Actions</h6></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-eye"></i> View Report</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-download"></i> Download</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-printer"></i> Print</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-envelope"></i> Send Email</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-share"></i> Share</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-calendar2-plus"></i> Schedule</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash"></i> Delete</a></li>
                  </ul>
                </div>
            </td>
            </tr>

            <!-- Row 3 -->
            <tr>
              <td><strong><i class="fa-solid fa-clock-rotate-left"></i>Customer Payment History – March 2023</strong><br><small class="text-muted">Payment Report • REP-003</small></td>
              <td><div class="d-flex align-items-center"><div class="avatar avatar-green me-2">AP</div>Amit Patel</div></td>
              <td>2023-03-30 11:45</td>
              <td><span class="badge bg-light text-success border border-success"><i class="bi bi-file-earmark-check"></i> Ready</span></td>
              <td>1.8 MB</td>
              <td><i class="bi bi-eye me-2"></i><i class="bi bi-download me-2"></i>
            
              <div class="dropdown d-inline">
                  <button class="btn btn-sm" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                  <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><h6 class="dropdown-header">Actions</h6></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-eye"></i> View Report</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-download"></i> Download</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-printer"></i> Print</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-envelope"></i> Send Email</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-share"></i> Share</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-calendar2-plus"></i> Schedule</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash"></i> Delete</a></li>
                  </ul>
                </div>
            </td>
            </tr>

            <!-- Row 4 -->
            <tr>
              <td><strong><i class="fa-solid fa-plus-minus"></i>Profit & Loss Statement – Q1 2023</strong><br><small class="text-muted">Financial Report • REP-004</small></td>
              <td><div class="d-flex align-items-center"><div class="avatar avatar-purple me-2">NS</div>Neha Singh</div></td>
              <td>2023-04-10 16:20</td>
              <td><span class="badge bg-light text-primary border border-primary"><i class="bi bi-hourglass-split"></i> Processing</span></td>
              <td>Calculating...</td>
              <td><i class="bi bi-eye me-2"></i><i class="bi bi-download me-2"></i>
              <div class="dropdown d-inline">
                  <button class="btn btn-sm" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                  <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><h6 class="dropdown-header">Actions</h6></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-eye"></i> View Report</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-download"></i> Download</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-printer"></i> Print</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-envelope"></i> Send Email</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-share"></i> Share</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-calendar2-plus"></i> Schedule</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash"></i> Delete</a></li>
                  </ul>
                </div>
            </td>
            </tr>

            <!-- Row 5 -->
            <tr>
              <td><strong><i class="fa-brands fa-product-hunt"></i> Product Performance Analysis – March 2023</strong><br><small class="text-muted">Sales Analysis • REP-005</small></td>
              <td><div class="d-flex align-items-center"><div class="avatar avatar-red me-2">VM</div>Vikram Mehta</div></td>
              <td>2023-03-29 13:10</td>
              <td><span class="badge bg-light text-danger border border-danger"><i class="bi bi-x-circle"></i> Failed</span></td>
              <td>N/A</td>
              <td><i class="bi bi-eye me-2"></i><i class="bi bi-download me-2"></i>
            
              <div class="dropdown d-inline">
                  <button class="btn btn-sm" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                  <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><h6 class="dropdown-header">Actions</h6></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-eye"></i> View Report</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-download"></i> Download</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-printer"></i> Print</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-envelope"></i> Send Email</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-share"></i> Share</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-calendar2-plus"></i> Schedule</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash"></i> Delete</a></li>
                  </ul>
                </div>
            </td>
            </tr>

            <!-- Row 6 -->
            <tr>
              <td><strong><i class="fa-solid fa-money-bill"></i> Vendor Payment Summary – March 2023</strong><br><small class="text-muted">Payment Report • REP-006</small></td>
              <td><div class="d-flex align-items-center"><div class="avatar avatar-red me-2">SJ</div>Sunita Joshi</div></td>
              <td>2023-03-28 10:40</td>
              <td><span class="badge bg-light text-success border border-success"><i class="bi bi-file-earmark-check"></i> Ready</span></td>
              <td>1.5 MB</td>
              <td><i class="bi bi-eye me-2"></i><i class="bi bi-download me-2"></i>
            
              <div class="dropdown d-inline">
                  <button class="btn btn-sm" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                  <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><h6 class="dropdown-header">Actions</h6></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-eye"></i> View Report</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-download"></i> Download</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-printer"></i> Print</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-envelope"></i> Send Email</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-share"></i> Share</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-calendar2-plus"></i> Schedule</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash"></i> Delete</a></li>
                  </ul>
                </div>
            </td>
            </tr>

            <!-- Row 7 -->
            <tr>
              <td><strong><i class="fa-solid fa-database"></i> GST Return Data – March 2023</strong><br><small class="text-muted">Tax Report • REP-007</small></td>
              <td><div class="d-flex align-items-center"><div class="avatar avatar-purple me-2">RV</div>Rahul Verma</div></td>
              <td>2023-04-05 09:30</td>
              <td><span class="badge bg-light text-success border border-success"><i class="bi bi-file-earmark-check"></i> Ready</span></td>
              <td>4.2 MB</td>
              <td><i class="bi bi-eye me-2"></i><i class="bi bi-download me-2"></i>
            
              <div class="dropdown d-inline">
                  <button class="btn btn-sm" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                  <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><h6 class="dropdown-header">Actions</h6></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-eye"></i> View Report</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-download"></i> Download</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-printer"></i> Print</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-envelope"></i> Send Email</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-share"></i> Share</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-calendar2-plus"></i> Schedule</a></li>
                    <li><hr class="dropdown-divider"></li>
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
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
