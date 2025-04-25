<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel UI</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .tab-nav {
      background-color: #f8f9fa;
      padding: 10px 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      display: flex;
      justify-content: space-between;
    }
    .tab-nav a {
      text-decoration: none;
      padding: 10px 15px;
      color: #000;
      font-weight: 500;
    }
    .tab-nav a.active {
      border-bottom: 3px solid #0d6efd;
      color: #0d6efd;
    }
    .table-heading {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 15px;
    }
    .table-actions i {
      margin: 0 6px;
      cursor: pointer;
    }
    .badge {
      font-size: 0.8rem;
    }
  </style>
</head>
<body class="p-4">
</head>
<body class="bg-secondary bg-opacity-10">
    <?php
        include('./_admin_nav.php');
    ?>
<div class="main-content">
<h2 class="mb-1">Suppliers Dashboard</h2>
        <p class="text-muted">Manage supplier relationships and orders</p>

        <div class="row g-3">
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6>Active Suppliers</h6>
                            <h4>38</h4>
                            <small class="text-success">+3 vs last month</small>
                        </div>
                        <div class="card-icon">🚚</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6>Open Orders</h6>
                            <h4>12</h4>
                        </div>
                        <div class="card-icon">📦</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6>This Month Spending</h6>
                            <h4>₹2,85,400</h4>
                            <small class="text-danger">8.5% vs last month</small>
                        </div>
                        <div class="card-icon">💰</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6>Delivery Success</h6>
                            <h4>95.2%</h4>
                            <small class="text-success">+2.3% vs last month</small>
                        </div>
                        <div class="card-icon">📅</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-3">
                <button class="btn btn-primary dashboard-btn">Create Purchase Order</button>
            </div>
            <div class="col-md-3">
                <button class="btn btn-light dashboard-btn">Add Supplier</button>
            </div>
            <div class="col-md-3">
                <button class="btn btn-light dashboard-btn">Supplier Report</button>
            </div>
            <div class="col-md-3">
                <button class="btn btn-light dashboard-btn">Order Status</button>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card p-3">
                    <h6>Spending by Supplier</h6>
                    <canvas id="spendingChart" height="220"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-3">
                    <h6>Purchase Orders Trend</h6>
                    <canvas id="ordersTrend" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

   
    </script>
<div class="main-content">
        <h1>Accounting Dashboard</h1>
        <p>Monitor financial health and transactions</p>
        
       <!-- Search and Add User Row -->
  <div class="tab-nav">
    <div>
      <a href="#purchase" class="active" onclick="showTab('purchase')">Purchase Orders</a>
      <a href="#suppliers" onclick="showTab('suppliers')">Suppliers</a>
    </div>
    <div>
      <input type="text" placeholder="Search..." class="form-control d-inline-block w-auto me-2">
      <button class="btn btn-outline-secondary me-2"><i class="bi bi-funnel"></i></button>
      <button class="btn btn-primary"><i class="bi bi-plus-circle"></i> <span id="actionLabel">New Order</span></button>
    </div>
  </div>

  <div id="purchase" class="tab-content">
    <div class="table-heading">Purchase Orders</div>
    <table class="table table-bordered">
      <thead class="table-light">
        <tr>
          <th>Order ID</th><th>Supplier</th><th>Date</th><th>Items</th><th>Amount</th><th>Status</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr><td>PO-2025-001</td><td>Havells India Ltd.</td><td>08 Apr, 2025</td><td>Copper Wires</td><td>₹45,800</td><td><span class="badge bg-success">Received</span></td><td class="table-actions"><i class="bi bi-eye"></i><i class="bi bi-pencil"></i><i class="bi bi-printer"></i><i class="bi bi-download"></i><i class="bi bi-three-dots"></i></td></tr>
        <tr><td>PO-2025-002</td><td>Orient Electric</td><td>07 Apr, 2025</td><td>LED Panels</td><td>₹28,500</td><td><span class="badge bg-primary">In Transit</span></td><td class="table-actions"><i class="bi bi-eye"></i><i class="bi bi-pencil"></i><i class="bi bi-printer"></i><i class="bi bi-download"></i><i class="bi bi-three-dots"></i></td></tr>
        <tr><td>PO-2025-003</td><td>Polycab Wires</td><td>06 Apr, 2025</td><td>FRLSH Cables</td><td>₹65,200</td><td><span class="badge bg-warning text-dark">Ordered</span></td><td class="table-actions"><i class="bi bi-eye"></i><i class="bi bi-pencil"></i><i class="bi bi-printer"></i><i class="bi bi-download"></i><i class="bi bi-three-dots"></i></td></tr>
        <tr><td>PO-2025-004</td><td>Anchor Electricals</td><td>05 Apr, 2025</td><td>Switch Boards</td><td>₹18,400</td><td><span class="badge bg-success">Received</span></td><td class="table-actions"><i class="bi bi-eye"></i><i class="bi bi-pencil"></i><i class="bi bi-printer"></i><i class="bi bi-download"></i><i class="bi bi-three-dots"></i></td></tr>
        <tr><td>PO-2025-005</td><td>Bajaj Electricals</td><td>04 Apr, 2025</td><td>Ceiling Fans</td><td>₹35,200</td><td><span class="badge bg-primary">In Transit</span></td><td class="table-actions"><i class="bi bi-eye"></i><i class="bi bi-pencil"></i><i class="bi bi-printer"></i><i class="bi bi-download"></i><i class="bi bi-three-dots"></i></td></tr>
      </tbody>
    </table>
  </div>

  <div id="suppliers" class="tab-content d-none">
    <div class="table-heading">Suppliers</div>
    <table class="table table-bordered">
      <thead class="table-light">
        <tr>
          <th>ID</th><th>Supplier Name</th><th>Type</th><th>Items</th><th>Orders</th><th>Spending</th><th>Rating</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr><td>SUP001</td><td>Havells India Ltd.</td><td>Manufacturer</td><td>Wires, Switches</td><td>32</td><td>₹3,45,200</td><td>⭐ 4.8</td><td class="table-actions"><i class="bi bi-box-arrow-up-right"></i><i class="bi bi-pencil-square"></i></td></tr>
        <tr><td>SUP002</td><td>Polycab Wires Pvt Ltd.</td><td>Manufacturer</td><td>Wires, Cables</td><td>28</td><td>₹2,85,600</td><td>⭐ 4.7</td><td class="table-actions"><i class="bi bi-box-arrow-up-right"></i><i class="bi bi-pencil-square"></i></td></tr>
        <tr><td>SUP003</td><td>Orient Electric</td><td>Manufacturer</td><td>Fans, Lights</td><td>15</td><td>₹1,25,800</td><td>⭐ 4.5</td><td class="table-actions"><i class="bi bi-box-arrow-up-right"></i><i class="bi bi-pencil-square"></i></td></tr>
        <tr><td>SUP004</td><td>Bajaj Electricals</td><td>Distributor</td><td>Appliances</td><td>12</td><td>₹95,400</td><td>⭐ 4.3</td><td class="table-actions"><i class="bi bi-box-arrow-up-right"></i><i class="bi bi-pencil-square"></i></td></tr>
        <tr><td>SUP005</td><td>Anchor Electricals</td><td>Manufacturer</td><td>Switches, Sockets</td><td>18</td><td>₹1,45,200</td><td>⭐ 4.6</td><td class="table-actions"><i class="bi bi-box-arrow-up-right"></i><i class="bi bi-pencil-square"></i></td></tr>
      </tbody>
    </table>
  </div>

  <script>
  function showTab(tab) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.add('d-none'));
    document.getElementById(tab).classList.remove('d-none');
    document.querySelectorAll('.tab-nav a').forEach(a => a.classList.remove('active'));
    document.querySelector('.tab-nav a[href="#' + tab + '"]').classList.add('active');
    document.getElementById('actionLabel').innerText = tab === 'purchase' ? 'New Order' : 'Add Supplier';
  }
</script>

<script>
  const ctxPie = document.getElementById('spendingChart');
  new Chart(ctxPie, {
    type: 'pie',
    data: {
      labels: ['Havells', 'Polycab', 'Orient', 'Bajaj', 'Anchor', 'Others'],
      datasets: [{
        data: [28, 23, 10, 8, 12, 18],
        backgroundColor: ['#007bff', '#20c997', '#fd7e14', '#ff5733', '#6f42c1', '#343a40']
      }]
    },
    options: {
      responsive: true
    }
  });

  const ctxLine = document.getElementById('ordersTrend');
  new Chart(ctxLine, {
    type: 'line',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
      datasets: [{
        label: 'Orders',
        data: [38, 32, 45, 53, 48, 42],
        fill: false,
        borderColor: '#007bff',
        tension: 0.3
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          display: false
        }
      }
    }
  });
</script>

</body>
</html>
