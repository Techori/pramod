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

  <div class="d-flex gap-2">
    <form method="POST" action="">
      <select name="store_id" onchange="this.form.submit()">
        <option value="">Select store</option>
        <?php

        $stmt = $conn->prepare("SELECT user_name FROM users WHERE user_type = ?");
        $stmt->bind_param("s", $user_type);
        $user_type = 'Store';
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<option value='" . htmlspecialchars($row['user_name'], ENT_QUOTES) . "'>" . htmlspecialchars($row['user_name']) . "</option>";
          }
        }
        ?>
      </select>
    </form>
    <button class="btn btn-outline-secondary me-2"  onclick="exportTableToCSV()" id="exportBtn" data-bs-toggle="modal"
      data-bs-target="#exportSuccessModal">
      Export
    </button>

    <button class="btn btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#shareModal">Share</button>

    <!-- Share Modal -->
    <div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
          <div class="modal-header">
            <h5 class="modal-title" id="shareModalLabel">Share This Report</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="mb-2">Copy the link below to share:</p>
            <input type="text" class="form-control mb-3" id="shareLink" value="https://yourapp.com/report/12345"
              readonly>
            <button class="btn btn-primary btn-sm" onclick="copyShareLink()">Copy Link</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Filters -->
<div class="d-flex flex-wrap gap-2 mb-4">
  <input type="search" class="form-control w-auto" id="searchInput" placeholder="Search..." />
</div>

<?php
// Monthly Revenue
$currentMonth = date('m');
$currentYear = date('Y');
$revenue = $conn->query("SELECT IFNULL(SUM(grand_total), 0) AS total FROM invoice WHERE MONTH(date) = $currentMonth AND YEAR(date) = $currentYear")->fetch_assoc();

// Last Month Revenue for % comparison
$lastMonth = date('m', strtotime('-1 month'));
$lastRevenue = $conn->query("SELECT IFNULL(SUM(grand_total), 0) AS total FROM invoice WHERE MONTH(date) = $lastMonth AND YEAR(date) = $currentYear")->fetch_assoc();
$revenueChange = ($lastRevenue['total'] > 0) ? (($revenue['total'] - $lastRevenue['total']) / $lastRevenue['total']) * 100 : 0;

// Monthly Expense
$monthly_expenses_query = "SELECT SUM(amount) as total FROM expenses WHERE MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())";
$monthly_expenses_result = $conn->query($monthly_expenses_query);
$monthly_expenses = $monthly_expenses_result ? ($monthly_expenses_result->fetch_assoc()['total'] ?? 0) : 0;
$monthly_expenses_result->free();

// Monthly Expenses comparison (last month)
$last_month_expenses_query = "SELECT SUM(amount) as total FROM expenses WHERE MONTH(date) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(date) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
$last_month_expenses_result = $conn->query($last_month_expenses_query);
$last_month_expenses = $last_month_expenses_result ? ($last_month_expenses_result->fetch_assoc()['total'] ?? 0) : 0;
$last_month_expenses_result->free();
$monthly_expenses_percent = $last_month_expenses > 0 ? round(($monthly_expenses - $last_month_expenses) / $last_month_expenses * 100, 1) : ($monthly_expenses > 0 ? 100 : 0);
$monthly_expenses_text = $monthly_expenses_percent >= 0 ? "+{$monthly_expenses_percent}%" : "{$monthly_expenses_percent}%";
$monthly_expenses_class = $monthly_expenses_percent >= 0 ? 'text-danger' : 'text-success';

// Net Profit
$profit = $revenue['total'] - $monthly_expenses;

// Profit comparison
$last_profit = $lastRevenue['total'] - $last_month_expenses;
$profit_percent = ($last_profit > 0) ? ($profit - $last_profit) / $last_profit * 100 : 0;
?>

<!-- Metrics Cards -->
<div class="row g-4 mb-5">
  <div class="col-md-4">
    <div class="card card-metric p-3">
      <div class="card-body">
        <p class="text-success metric-title">Revenue</p>
        <div class="metric-value">₹<?= number_format($revenue['total']) ?></div>
        <small class="<?= $revenueChange < 0 ? 'text-danger' : 'text-success' ?>"><?= round($revenueChange, 2) ?>% vs
          last month</small>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-metric p-3">
      <div class="card-body">
        <p class="text-danger metric-title">Expenses</p>
        <div class="metric-value">₹<?php echo number_format($monthly_expenses, 0); ?></div>
        <small class="<?php echo $monthly_expenses_class; ?>"><?php echo htmlspecialchars($monthly_expenses_text); ?> vs
          last month</small>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-metric p-3">
      <div class="card-body">
        <p class="text-primary metric-title">Net Profit</p>
        <div class="metric-value">₹<?php echo number_format($profit, 0); ?></div>
        <small class="<?= $profit_percent < 0 ? 'text-danger' : 'text-success' ?>"><?= round($profit_percent, 2) ?>% vs
          last month</small>
      </div>
    </div>
  </div>
</div>

<!-- Nav Tabs -->
<ul class="nav nav-tabs mb-4" id="adminTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial" type="button"
      role="tab">Financial</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button"
      role="tab">Sales</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button"
      role="tab">Inventory</button>
  </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="adminTabContent">

  <!-- Financial Tab -->
  <div class="tab-pane fade show active" id="financial" role="tabpanel" aria-labelledby="financial-tab">

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
      // Refresh Button (Reload page)
      document.getElementById('refreshBtn').addEventListener('click', function () {
        location.reload();
      });

    </script>
  </div>
  <!-- Sales Tab -->
  <div class="tab-pane fade" id="sales" role="tabpanel" aria-labelledby="sales-tab">
    <div class="row mt-3 mx-3">
      <div class="container chart-container p-2">
        <h6 class="text-center mb-2">Monthly Sales</h6>
        <canvas id="monthlySalesChart" height="150"></canvas>
      </div>
    </div>

  </div>

  <?php

  // Get current and last month info
  function getMonthYear($offset = 0)
  {
    $date = new DateTime();
    $date->modify("$offset month");
    return [$date->format('m'), $date->format('Y')];
  }
  list($currMonth, $currYear) = getMonthYear(0);
  list($lastMonth, $lastYear) = getMonthYear(-1);

  function percentageChange($current, $last)
  {
    if ($last == 0)
      return 0;
    return round((($current - $last) / $last) * 100, 2);
  }

  // Get Inventory Value
  $invQuery = $conn->query("
      SELECT 
          SUM(mrp * stock_quantity) as total_value 
      FROM products
  ");
  $inv = $invQuery->fetch_assoc();
  $invAmount = $inv['total_value'] ?: 0;

  // If you want to compare with last month, you'll need to use created_at or updated_at
  $invLastQuery = $conn->prepare("
                SELECT SUM(mrp * stock_quantity) as last_value 
                FROM products 
                WHERE MONTH(updated_at) = ? AND YEAR(updated_at) = ?
            ");
  $invLastQuery->bind_param("ii", $lastMonth, $lastYear);
  $invLastQuery->execute();
  $invLast = $invLastQuery->get_result()->fetch_assoc()['last_value'] ?: 0;

  $invChange = percentageChange($invAmount, $invLast);
  $invTrend = $invChange >= 0 ? 'success' : 'danger';

  ?>

  <!-- Invetory -->
  <div class="tab-pane fade" id="inventory" role="tabpanel" aria-labelledby="inventory-tab">
    <!-- Metrics Cards -->
    <div class="row g-4 mb-5">
      <div class="col-md-4">
        <div class="card card-metric p-3">
          <div class="card-body">
            <p class="text-success metric-title">Total Inventory Value</p>
            <div class="metric-value">₹<?= number_format($invAmount, 2) ?></div>
            <div class="text-<?= $invTrend ?>">
              <?= ($invChange >= 0 ? '+' : '') . $invChange ?>% vs last month</div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-metric p-3">
          <div class="card-body">
            <p class="text-danger metric-title">Expenses</p>
            <div class="metric-value">₹<?php echo number_format($monthly_expenses, 0); ?></div>
            <div class="<?php echo $monthly_expenses_class; ?>"><?php echo htmlspecialchars($monthly_expenses_text); ?>
              vs
              last month</div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-metric p-3">
          <div class="card-body">
            <p class="text-primary metric-title">Net Profit</p>
            <div class="metric-value">₹<?php echo number_format($profit, 0); ?></div>
            <div class="<?= $profit_percent < 0 ? 'text-danger' : 'text-success' ?>"><?= round($profit_percent, 2) ?>%
              vs
              last month</div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- Revenue -->
  <?php

  $store_id = $_POST['store_id'] ?? null;
  $params = [];
  $paramTypes = "";

  // Create date range for last 6 months
  $labels = [];
  $revenues = [];

  for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $labels[] = date('M', strtotime($month)); // eg. 'Jan'
  
    // Prepare query per month
    $sql = "SELECT SUM(grand_total) as revenue FROM invoice 
            WHERE DATE_FORMAT(date, '%Y-%m') = ?";

    $params = [$month];
    $paramTypes = "s";

    if (!empty($store_id)) {
      $sql .= " AND created_for = ?";
      $params[] = $store_id;
      $paramTypes .= "s";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($paramTypes, ...$params);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $revenues[] = $result['revenue'] ?? 0;
    $stmt->close();
  }
  ?>

  <!-- Expense -->
  <?php

  $store_id = $_POST['store_id'] ?? null;
  $expenseLabels = [];
  $monthlyExpenses = [];

  for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $expenseLabels[] = date('M', strtotime($month)); // eg. 'Jan'
  
    $sql = "SELECT SUM(amount) as expense FROM expenses 
            WHERE DATE_FORMAT(date, '%Y-%m') = ?";
    $params = [$month];
    $paramTypes = "s";


    $stmt = $conn->prepare($sql);
    $stmt->bind_param($paramTypes, ...$params);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $monthlyExpenses[] = $result['expense'] ?? 0;
    $stmt->close();
  }
  ?>

  <!-- Sales -->
  <?php

  $store_id = $_POST['store_id'] ?? null;
  $salesLabels = [];
  $monthlySales = [];

  for ($i = 11; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $salesLabels[] = date('M', strtotime($month)); // eg. 'Jan'
  
    $sql = "SELECT SUM(grand_total) as total_sales FROM invoice 
            WHERE DATE_FORMAT(date, '%Y-%m') = ?";
    $params = [$month];
    $paramTypes = "s";

    if (!empty($store_id)) {
      $sql .= " AND created_for = ?";
      $params[] = $store_id;
      $paramTypes .= "i";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($paramTypes, ...$params);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $monthlySales[] = $result['total_sales'] ?? 0;
    $stmt->close();
  }
  ?>



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

    // Revenue Trend Chart
    const ctxRevenue = document.getElementById('spendingChart2').getContext('2d');
    const revenueChart = new Chart(ctxRevenue, {
      type: 'line',
      data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
          label: 'Revenue',
          data: <?= json_encode($revenues) ?>,
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
        labels: <?= json_encode($expenseLabels) ?>,
        datasets: [{
          label: 'Expenses',
          data: <?= json_encode($monthlyExpenses) ?>,
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

    // monthly chartadded
    const monthlyCtx = document.getElementById('monthlySalesChart').getContext('2d');
    new Chart(monthlyCtx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($salesLabels) ?>,
        datasets: [{
          label: 'Sales (₹)',
          data: <?= json_encode($monthlySales) ?>,
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

    document.getElementById('exportBtn').addEventListener('click', function () {
      // Here you could add actual export logic if needed before the modal shows
      console.log("Export logic triggered...");
    });

    // share btn ke liye
    function copyShareLink() {
      const linkInput = document.getElementById("shareLink");
      linkInput.select();
      document.execCommand("copy");
      alert("Link copied to clipboard!");
    }


  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
  </body>

  </html>