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
</div>

<!-- Create Report Modal OUTSIDE of buttons -->
<div class="modal fade" id="createReportModal" tabindex="-1" aria-labelledby="createReportLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title" id="createReportLabel">
          <i class="bi bi-file-earmark-text me-2"></i>Create Report
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted mb-3">Generate detailed reports for business insights</p>
        <div class="mb-3">
          <label class="form-label">Report Type</label>
          <select class="form-select">
            <option selected>Bill-wise Profit</option>
            <option>Sales Summary</option>
            <option>Daybook</option>
            <option>Profit and Loss</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Date Range</label>
          <select class="form-select">
            <option selected>This Month</option>
            <option>Last Month</option>
            <option>Last 3 Months</option>
            <option>Custom Range</option>
          </select>
        </div>
        <label class="form-label">Additional Options</label>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="taxInfo">
          <label class="form-check-label" for="taxInfo">Include Tax Information</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="cancelledTrans">
          <label class="form-check-label" for="cancelledTrans">Include Cancelled Transactions</label>
        </div>
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" id="detailedView">
          <label class="form-check-label" for="detailedView">Show Detailed View</label>
        </div>
        <label class="form-label">Report Format</label>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="reportFormat" id="formatDetailed" checked>
          <label class="form-check-label" for="formatDetailed">Detailed</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="reportFormat" id="formatSummary">
          <label class="form-check-label" for="formatSummary">Summary</label>
        </div>
        <div class="form-check mb-3">
          <input class="form-check-input" type="radio" name="reportFormat" id="formatConsolidated">
          <label class="form-check-label" for="formatConsolidated">Consolidated</label>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary">Generate Report</button>
      </div>
    </div>
    <!-- down part -->
    <div class="d-flex gap-3 mb-4">
  <button class="tab-btn active" data-target="financial">Financial</button>
  <button class="tab-btn" data-target="sales">Sales</button>
  <button class="tab-btn" data-target="inventory">Inventory</button>
  <button class="tab-btn" data-target="tax">Tax & GST</button>
  <button class="tab-btn" data-target="custom">Custom Reports</button>
</div>

<!-- Tab Contents -->
<div id="financial" class="tab-content active">
  <div class="card mb-4">
    <div class="card-body">
      <h5 class="card-title">Revenue vs Expenses</h5>
      <p class="text-muted">Monthly comparison for the last 6 months</p>
      <div class="card-placeholder">Revenue vs Expenses Chart</div>
    </div>
  </div>
</div>

<div id="sales" class="tab-content">
  <div class="card mb-4">
    <div class="card-body">
      <h5 class="card-title">Sales Report</h5>
      <p class="text-muted">Your custom sales content here.</p>
    </div>
  </div>
</div>

<div id="inventory" class="tab-content">
  <div class="card mb-4">
    <div class="card-body">
      <h5 class="card-title">Inventory Overview</h5>
      <p class="text-muted">Inventory related charts or tables here.</p>
    </div>
  </div>
</div>

<div id="tax" class="tab-content">
  <div class="card mb-4">
    <div class="card-body">
      <h5 class="card-title">Tax & GST Reports</h5>
      <p class="text-muted">Show GST return info here.</p>
    </div>
  </div>
</div>

<div id="custom" class="tab-content">
  <div class="card mb-4">
    <div class="card-body">
      <h5 class="card-title">Custom Reports Section</h5>
      <p class="text-muted">Upload or create custom reports.</p>
    </div>
  </div>
</div>

<!-- CSS -->
<style>
  .tab-content { display: none; }
  .tab-content.active { display: block; }
  .tab-btn.active { background-color: #0d6efd; color: white; }
</style>

<!-- JS -->
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
