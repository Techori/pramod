
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

        <h1>User Management</h1>
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
  <!-- USERS TAB -->
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
                <th>Permission</th>
              </tr>
            </thead>
            <tbody id="userTableBody">
              <!-- User rows added dynamically -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- ROLES TAB -->
  <div class="tab-pane fade" id="roles" role="tabpanel">
    <div class="text-center py-5">
      <h4>Roles & Permissions</h4>
      <p>Manage roles, create custom permission sets, and control access to different areas of the system.</p>
      <button class="btn btn-outline-primary">Manage Roles</button>
    </div>
  </div>

  <!-- ACTIVITY TAB -->
  <div class="tab-pane fade" id="activity" role="tabpanel">
    <div class="text-center py-5">
      <h4>Activity Tracking</h4>
      <p>View detailed logs of user actions, login history, and system changes for audit purposes.</p>
      <button class="btn btn-outline-primary">View Activity Logs</button>
    </div>
  </div>

  <!-- ACCESS TAB -->
  <div class="tab-pane fade" id="access" role="tabpanel">
    <div class="text-center py-5">
      <h4>Security Settings</h4>
      <p>Configure password policies, two-factor authentication, IP restrictions, and session timeouts.</p>
      <button class="btn btn-outline-primary">Security Settings</button>
    </div>
  </div>
</div>

<!-- Permission Modal -->
<div class="modal fade" id="permissionModal" tabindex="-1" aria-labelledby="permissionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="permissionModalLabel">Set Permissions</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="permissionForm">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="maindashbord">
            <label class="form-check-label" for="maindashbord">Main Dashboard</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="billingdesk">
            <label class="form-check-label" for="billingdesk">Billing Desk</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="accounting">
            <label class="form-check-label" for="accounting">Accounting</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="investory">
            <label class="form-check-label" for="investory">Inventory</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="expenses">
            <label class="form-check-label" for="expenses">Expenses</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="factorystock">
            <label class="form-check-label" for="factorystock">Factory Stock</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="retailstore">
            <label class="form-check-label" for="retailstore">Retail Store</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="aftersellservice">
            <label class="form-check-label" for="aftersellservice">After-Sell Service</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="suppliers">
            <label class="form-check-label" for="suppliers">Suppliers</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="reports">
            <label class="form-check-label" for="reports">Reports</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="settings">
            <label class="form-check-label" for="settings">Settings</label>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="savePermissionBtn">Save</button>
      </div>
    </div>
  </div>
</div>


<!-- JAVASCRIPT -->
<script>
function showsettingTab(id) {
  const contents = document.querySelectorAll('.tab-pane');
  const tabs = document.querySelectorAll('.settingTab');

  tabs.forEach(tab => tab.classList.remove('active'));
  contents.forEach(content => content.classList.remove('show', 'active'));

  document.getElementById(id).classList.add('show', 'active');
  document.querySelector(`[onclick="showsettingTab('${id}')"]`).classList.add('active');
}

const users = [
  { initials: "RK", name: "Rajesh Kumar", email: "rajesh@unnatitraders.com", id: "USR-001", role: "Admin", roleClass: "bg-light text-purple", status: "Active", statusClass: "bg-success text-white", login: "2023-04-10 09:45 AM" },
  { initials: "PS", name: "Priya Sharma", email: "priya@unnatitraders.com", id: "USR-002", role: "Manager", roleClass: "bg-primary text-white", status: "Active", statusClass: "bg-success text-white", login: "2023-04-10 11:30 AM" },
  { initials: "AP", name: "Amit Patel", email: "amit@unnatitraders.com", id: "USR-003", role: "Accountant", roleClass: "bg-warning text-dark", status: "Active", statusClass: "bg-success text-white", login: "2023-04-09 04:15 PM" },
  { initials: "NS", name: "Neha Singh", email: "neha@unnatitraders.com", id: "USR-004", role: "Store", roleClass: "bg-success text-white", status: "Inactive", statusClass: "bg-danger text-white", login: "2023-04-01 10:22 AM" },
  { initials: "RV", name: "Rahul Verma", email: "rahul@unnatitraders.com", id: "USR-005", role: "User", roleClass: "bg-secondary text-white", status: "Pending", statusClass: "bg-warning text-dark", login: "Never logged in" },
  { initials: "SJ", name: "Sunita Joshi", email: "sunita@unnatitraders.com", id: "USR-006", role: "Manager", roleClass: "bg-primary text-white", status: "Active", statusClass: "bg-success text-white", login: "2023-04-10 02:15 PM" },
];

const tbody = document.getElementById('userTableBody');
users.forEach((user, index) => {
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
      <td>
        <button class="btn btn-sm btn-outline-primary open-permission-btn" data-user-index="${index}" data-bs-toggle="modal" data-bs-target="#permissionModal">
          Allow
        </button>
      </td>
    </tr>
  `;
});

let selectedUserIndex = null;
document.addEventListener('click', function (e) {
  if (e.target.classList.contains('open-permission-btn')) {
    selectedUserIndex = e.target.getAttribute('data-user-index');
    document.querySelectorAll('#permissionForm input[type="checkbox"]').forEach(cb => cb.checked = false);
  }
});

document.getElementById('savePermissionBtn').addEventListener('click', function () {
  const permissions = ['maindashbord', 'billingdesk', 'accounting', 'investory', 'expenses', 'factorystock', 'retailstore', 'aftersellservice', 'suppliers', 'reports', 'settings'];
  const selected = {};
  permissions.forEach(id => {
    selected[id] = document.getElementById(id).checked;
  });

  const user = users[selectedUserIndex];
  console.log(`Saved permissions for ${user.name}:`, selected);

  const modal = bootstrap.Modal.getInstance(document.getElementById('permissionModal'));
  modal.hide();
});
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>