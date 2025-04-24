<?php
session_start();
if (!(isset($_SESSION["uid"]) && isset($_SESSION["user_type"]) && isset($_SESSION["session_id"]))) {
    header("location:../../login.php");
    exit;
} else {
    if (in_array($_SESSION["user_type"] ,  ['Factory','Store','Vendor'])) {
        header("location:../index.php");
        exit;

    } else if (!($_SESSION["user_type"] == 'Admin')) {
        header("location:../../login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="unnati">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Shree Unnati Wires & Traders - Premium Wire Manufacturing</title>
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
        .settingTab {
            padding: 10px 20px;
            cursor: pointer;
            border: none;
            background: none;
            font-size: 16px;
        }
        .settingTab.active {
            border-bottom: 3px solid #007bff;
            font-weight: bold;
            color: #007bff;
        }
        .setting-tab-content {
            display: none;
            padding: 20px 0;
        }
        .setting-tab-content.active {
            display: block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .green-bg {
            background-color: #d4edda;
            color: #155724;
            padding: 4px 10px;
            border-radius: 10px;
        }
        .orange-bg {
            background-color: #fff3cd;
            color: #856404;
            padding: 4px 10px;
            border-radius: 10px;
        }
        .red-bg {
            background-color: #f8d7da;
            color: #721c24;
            padding: 4px 10px;
            border-radius: 10px;
        }

    </style>
</head>
<body class="bg-secondary bg-opacity-10">
    <?php
        include('./_admin_nav.php');
    ?>

<div class="main-content">
        <h1>Accounting Dashboard</h1>
        <p>Monitor financial health and transactions</p>
        
       <!-- Search and Add User Row -->
<div class="container-fluid d-flex justify-content-between align-items-center mb-3">
    <!-- Search Bar -->
    <div class="d-flex w-75">
        <div class="input-group w-100 me-2">
            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control border-start-0" placeholder="Search..." />
        </div>
    </div>

    <!-- Add User Button -->
    <div>
        <button class="btn btn-outline-primary">
            <i class="fa-solid fa-user-plus"></i> Add User
        </button>
    </div>
</div>

<!-- Cards Row -->
<div class="container-fluid d-flex flex-wrap gap-3">
    <!-- Card 1 -->
    <div class="card w-25">
        <div class="card-body">
            <h6 class="card-subtitle mb-2 text-muted">Total Users</h6>
            <i class="fa-solid fa-user-group fa-lg mb-2"></i>
            <h5 class="card-title">42</h5>
            <p class="card-text">Active users</p>
            <h5>36</h5>
        </div>
    </div>

    <!-- Card 2 -->
    <div class="card w-25">
        <div class="card-body">
            <h6 class="card-subtitle mb-2 text-muted">User Roles</h6>
            <i class="fa-solid fa-user-large"></i>
            <h5 class="card-title">8</h5>
            <p class="card-text">Custom Roles</p>
            <h5>3</h5>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="card w-25">
        <div class="card-body">
            <h6 class="card-subtitle mb-2 text-muted">Recent Logins</h6>
            <i class="fa-solid fa-dolly"></i>
            <h5 class="card-title">26</h5>
            <p class="card-text">Today</p>
            <h5>12</h5>
        </div>
    </div>
</div>

          <!-- Nav Tabs -->
    <ul class="nav nav-tabs mb-4" id="adminTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">Users</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles" type="button" role="tab">Roles & Permissions</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab">User Activity</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="access-tab" data-bs-toggle="tab" data-bs-target="#access" type="button" role="tab">Access Control</button>
      </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="adminTabContent">
      <!-- Users Tab -->
      <div class="tab-pane fade show active" id="users" role="tabpanel">
        <div class="d-flex justify-content-between mb-3">
          <div>
            <input type="text" class="form-control d-inline-block w-auto" placeholder="Search users...">
            <select class="form-select d-inline-block w-auto ms-2">
              <option>All Roles</option>
            </select>
            <select class="form-select d-inline-block w-auto ms-2">
              <option>All Status</option>
            </select>
          </div>
          <div>
            <button class="btn btn-outline-secondary">Refresh</button>
            <button class="btn btn-outline-secondary">Export</button>
            <button class="btn btn-primary">+ Add User</button>
          </div>
        </div>

        <div class="mb-3">
          <span>7 users selected</span>
          <button class="btn btn-danger btn-sm float-end">Delete Selected</button>
        </div>

        <div class="card">
          <div class="card-body">
            <h5 class="card-title mb-3">Users</h5>
            <div class="table-responsive">
              <table class="table align-middle">
                <thead>
                  <tr>
                    <th><input type="checkbox" checked></th>
                    <th>User</th>
                    <th>ID</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="userTableBody">
                  <!-- Dynamically populated -->
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Roles & Permissions Tab -->
      <div class="tab-pane fade" id="roles" role="tabpanel">
        <div class="text-center py-5">
          <h4>Roles & Permissions</h4>
          <p>Manage roles, create custom permission sets, and control access to different areas of the system.</p>
          <button class="btn btn-outline-primary">Manage Roles</button>
        </div>
      </div>

      <!-- User Activity Tab -->
      <div class="tab-pane fade" id="activity" role="tabpanel">
        <div class="text-center py-5">
          <h4>Activity Tracking</h4>
          <p>View detailed logs of user actions, login history, and system changes for audit purposes.</p>
          <button class="btn btn-outline-primary">View Activity Logs</button>
        </div>
      </div>

      <!-- Access Control Tab -->
      <div class="tab-pane fade" id="access" role="tabpanel">
        <div class="text-center py-5">
          <h4>Security Settings</h4>
          <p>Configure password policies, two-factor authentication, IP restrictions, and session timeouts.</p>
          <button class="btn btn-outline-primary">Security Settings</button>
        </div>
      </div>
    </div>
  </div>

    <script>

        function showsettingTab(id) {
            const tabs = document.querySelectorAll('.settingTab');
            const contents = document.querySelectorAll('.setting-tab-content');

            tabs.forEach(tab => tab.classList.remove('active'));
            contents.forEach(content => content.classList.remove('active'));

            document.querySelector(`#${id}`).classList.add('active');
            document.querySelector(`[onclick="showsettingTab('${id}')"]`).classList.add('active');
        }
// addedd of user
const users = [
  { initials: "RK", name: "Rajesh Kumar", email: "rajesh@unnatitraders.com", id: "USR-001", role: "Admin", roleClass: "bg-light text-purple", status: "Active", statusClass: "bg-success text-white", login: "2023-04-10 09:45 AM" },
  { initials: "PS", name: "Priya Sharma", email: "priya@unnatitraders.com", id: "USR-002", role: "Manager", roleClass: "bg-primary text-white", status: "Active", statusClass: "bg-success text-white", login: "2023-04-10 11:30 AM" },
  { initials: "AP", name: "Amit Patel", email: "amit@unnatitraders.com", id: "USR-003", role: "Accountant", roleClass: "bg-warning text-dark", status: "Active", statusClass: "bg-success text-white", login: "2023-04-09 04:15 PM" },
  { initials: "NS", name: "Neha Singh", email: "neha@unnatitraders.com", id: "USR-004", role: "Store", roleClass: "bg-success text-white", status: "Inactive", statusClass: "bg-danger text-white", login: "2023-04-01 10:22 AM" },
  { initials: "RV", name: "Rahul Verma", email: "rahul@unnatitraders.com", id: "USR-005", role: "User", roleClass: "bg-secondary text-white", status: "Pending", statusClass: "bg-warning text-dark", login: "Never logged in" },
  { initials: "SJ", name: "Sunita Joshi", email: "sunita@unnatitraders.com", id: "USR-006", role: "Manager", roleClass: "bg-primary text-white", status: "Active", statusClass: "bg-success text-white", login: "2023-04-10 02:15 PM" },
];

const tbody = document.getElementById('userTableBody');
users.forEach(user => {
  tbody.innerHTML += `
    <tr>
      <td><input type="checkbox" checked></td>
      <td>
        <div class="d-flex align-items-center">
          <div class="rounded-circle bg-secondary text-white text-center me-2" style="width:32px;height:32px;line-height:32px;">${user.initials}</div>
          <div>
            <div>${user.name}</div>
            <small class="text-muted">${user.email}</small>
          </div>
        </div>
      </td>
      <td>${user.id}</td>
      <td><span class="badge ${user.roleClass}">${user.role}</span></td>
      <td><span class="badge ${user.statusClass}">${user.status}</span></td>
      <td>${user.login}</td>
      <td><button class="btn btn-sm btn-light">⋮</button></td>
    </tr>
  `;
});


    </script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>