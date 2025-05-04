<?php
include '../../_conn.php'; // Your DB connection file
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Function to clean input data
function clean($input)
{
  return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['whatAction'])) {

  // ===== ADD USER =====
  if ($_POST['whatAction'] === 'AddUser') {

    $userName = clean($_POST['userName']);
    $userEmail = clean($_POST['userEmail']);
    $role = clean($_POST['role']);
    $status = 'Active';

    // Capture permissions
    $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];
    $permissionsJson = json_encode($permissions);

    $allowedRoles = ['Manager', 'Accountant', 'Store', 'Admin'];

    if (!in_array($role, $allowedRoles)) {
      die("Invalid role selected.");
    }

    try {
      $conn->begin_transaction();

      // Get latest User_ID
      $query = "SELECT User_ID FROM user_management 
                ORDER BY CAST(SUBSTRING(User_ID, 5) AS UNSIGNED) DESC 
                LIMIT 1 FOR UPDATE";
      $result = $conn->query($query);

      $newNum = 1;
      if ($row = $result->fetch_assoc()) {
        $lastId = $row['User_ID'];
        $newNum = (int) substr($lastId, 4) + 1;
      }

      $newUserId = 'USR-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);

      // Insert user
      $stmt = $conn->prepare("INSERT INTO user_management (User_ID, User_Name, Email, Role, Status, Permission) 
                              VALUES (?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("ssssss", $newUserId, $userName, $userEmail, $role, $status, $permissionsJson);
      $stmt->execute();
      $conn->commit();
      $stmt->close();

      header("Location: admin_dashboard.php?page=user_management");
      exit;

    } catch (Exception $e) {
      $conn->rollback();
      echo "Error adding user: " . $e->getMessage();
    }

  }

  // ===== UPDATE PERMISSIONS =====
  elseif ($_POST['whatAction'] === 'UpdatePermissions') {

    $userId = clean($_POST['userId']);

    // If permissions come as JSON string (from JS), decode them
    if (isset($_POST['permissions']) && is_string($_POST['permissions'])) {
      $permissions = json_decode($_POST['permissions'], true);
    } else {
      $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];
    }

    $permissionsJson = json_encode($permissions);

    try {
      $stmt = $conn->prepare("UPDATE user_management SET Permission = ? WHERE User_ID = ?");
      $stmt->bind_param("ss", $permissionsJson, $userId);
      $stmt->execute();
      $stmt->close();

      echo "Permissions updated successfully!";
    } catch (Exception $e) {
      echo "Error updating permissions: " . $e->getMessage();
    }
  }

} else {
  echo "Invalid request.";
}
?>



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

  th,
  td {
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
      <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Search..." />
    </div>
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
    <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button"
      role="tab">Users</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles" type="button" role="tab">Roles
      & Permissions</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button"
      role="tab">User Activity</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="access-tab" data-bs-toggle="tab" data-bs-target="#access" type="button"
      role="tab">Access Control</button>
  </li>
</ul>

<!-- Tab Content -->
<div class="tab-content" id="adminTabContent">
  <!-- USERS TAB -->
  <div class="tab-pane fade show active" id="users" role="tabpanel">
    <div class="d-flex justify-content-between mb-3">
      <div>
        <button class="btn btn-outline-secondary" id="refreshBtn">Refresh</button>
        <!-- Export Button -->
        <!-- Export Button -->
        <button class="btn btn-outline-secondary" onclick="exportTableToCSV()">Export</button>

        <!-- Export Success Modal -->
        <div class="modal fade" id="exportSuccessModal" tabindex="-1" aria-labelledby="exportSuccessLabel"
          aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
              <div class="modal-header">
                <h5 class="modal-title" id="exportSuccessLabel">Export Complete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                ✅ Your table has been successfully exported!
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="okBtn" data-bs-dismiss="modal">OK</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Add User Button -->
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">+ Add User</button>

        <!-- Modal Structure -->
        <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="addUserLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

              <div class="modal-header">
                <h5 class="modal-title" id="addUserLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              <div class="modal-body">
                <!-- Form Fields for New User -->
                <form id="userForm" method="POST" action="user_management.php">
                  <input type="hidden" name="whatAction" value="AddUser">

                  <div class="mb-3">
                    <label for="userName" class="form-label">User Name</label>
                    <input type="text" class="form-control" id="userName" name="userName" placeholder="Enter User Name"
                      required>
                  </div>

                  <div class="mb-3">
                    <label for="userEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="userEmail" name="userEmail"
                      placeholder="Enter User Email" required>
                  </div>

                  <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role" required>
                      <option value="Manager">Manager</option>
                      <option value="Accountant">Accountant</option>
                      <option value="Store">Store</option>
                      <option value="Admin">Admin</option>
                    </select>
                  </div>
                  <input type="hidden" name="whatAction" value="AddUser">
                  <!-- Footer Buttons -->
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <!-- Submit button - no auto-dismiss -->
                    <button type="submit" class="btn btn-primary">Save User</button>
                  </div>
                </form>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="mb-3">
      <span>7 users selected</span>
      <button class="btn btn-danger btn-sm float-end" onclick="alert('Delete Successful')">Delete Selected</button>
    </div>

    <?php
    // Include your DB connection file
    include '../../_conn.php';

    // Query to fetch user data
    $query = "SELECT User_ID, User_Name, Email, Role, Status, Last_Login, Permission FROM user_management";
    $result = $conn->query($query);
    ?>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-3">Users</h5>
        <div class="table-responsive">
          <table class="table align-middle" id="supplyTable">
            <thead>
              <tr>
                <th><input type="checkbox" id="selectAll"></th>
                <th>ID</th>
                <th>User</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Last Login</th>
                <th>Actions</th>
                <th>Permission</th>
              </tr>
            </thead>
            <tbody id="userTableBody">
              <?php
              // Loop through the results and display user data
              while ($row = $result->fetch_assoc()) {
                // Decode permissions from JSON
                $permissions = json_decode($row['Permission'], true);
                $permissionList = $permissions ? implode(', ', $permissions) : 'No permissions set'; // Display permissions list
                ?>
                <tr>
                  <td><input type="checkbox" class="selectUser" data-id="<?php echo $row['User_ID']; ?>"></td>
                  <td><?php echo $row['User_ID']; ?></td>
                  <td><?php echo $row['User_Name']; ?></td>
                  <td><?php echo $row['Email']; ?></td>
                  <td><?php echo $row['Role']; ?></td>
                  <td><?php echo $row['Status']; ?></td>
                  <td><?php echo $row['Last_Login']; ?></td>
                  <td>
                    <!-- Permission button -->
                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#permissionModal"
                      data-userid="<?php echo $row['User_ID']; ?>" data-username="<?php echo $row['User_Name']; ?>"
                      data-permissions='<?php echo htmlspecialchars(json_encode($permissions)); ?>'
                      id="permissionBtn_<?php echo $row['User_ID']; ?>">Set Permissions</button>
                  </td>
                  <td><?php echo $permissionList; ?></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Permission Modal -->
    <div class="modal fade" id="permissionModal" tabindex="-1" aria-labelledby="permissionModalLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="permissionModalLabel">Set Permissions</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="permissionForm" method="POST">
              <!-- Checkboxes for permissions -->
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="maindashbord" name="permissions[]"
                  value="Main Dashboard">
                <label class="form-check-label" for="maindashbord">Main Dashboard</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="billingdesk" name="permissions[]"
                  value="Billing Desk">
                <label class="form-check-label" for="billingdesk">Billing Desk</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="accounting" name="permissions[]" value="Accounting">
                <label class="form-check-label" for="accounting">Accounting</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="investory" name="permissions[]" value="Inventory">
                <label class="form-check-label" for="investory">Inventory</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="expenses" name="permissions[]" value="Expenses">
                <label class="form-check-label" for="expenses">Expenses</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="factorystock" name="permissions[]"
                  value="Factory Stock">
                <label class="form-check-label" for="factorystock">Factory Stock</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="retailstore" name="permissions[]"
                  value="Retail Store">
                <label class="form-check-label" for="retailstore">Retail Store</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="aftersellservice" name="permissions[]"
                  value="After-Sell Service">
                <label class="form-check-label" for="aftersellservice">After-Sell Service</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="suppliers" name="permissions[]" value="Suppliers">
                <label class="form-check-label" for="suppliers">Suppliers</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="reports" name="permissions[]" value="Reports">
                <label class="form-check-label" for="reports">Reports</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="settings" name="permissions[]" value="Settings">
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
  // Get all permission buttons
  const permissionBtns = document.querySelectorAll('[id^="permissionBtn_"]');

  permissionBtns.forEach(button => {
    button.addEventListener('click', function() {
      // Retrieve the user data
      const userId = this.getAttribute('data-userid');
      const userName = this.getAttribute('data-username');
      const permissions = JSON.parse(this.getAttribute('data-permissions'));

      // Set modal title
      document.getElementById('permissionModalLabel').textContent = `Set Permissions for ${userName}`;

      // Reset all checkboxes
      const checkboxes = document.querySelectorAll('#permissionForm input[type="checkbox"]');
      checkboxes.forEach(checkbox => {
        checkbox.checked = false;
      });

      // Pre-check the checkboxes based on the user's permissions
      permissions.forEach(permission => {
        const checkbox = document.querySelector(`#permissionForm input[value="${permission}"]`);
        if (checkbox) {
          checkbox.checked = true;
        }
      });

      // Attach the userId to the save button for later use
      document.getElementById('savePermissionBtn').setAttribute('data-userid', userId);
    });
  });

  // Handle saving updated permissions
  document.getElementById('savePermissionBtn').addEventListener('click', function() {
    const userId = this.getAttribute('data-userid');
    const selectedPermissions = [];
    const checkboxes = document.querySelectorAll('#permissionForm input[type="checkbox"]:checked');
    
    checkboxes.forEach(checkbox => {
      selectedPermissions.push(checkbox.value);
    });

    // Send the selected permissions to the server via AJAX or a simple form submission
    const formData = new FormData();
    formData.append('whatAction', 'UpdatePermissions');
    formData.append('userId', userId);
    formData.append('permissions', JSON.stringify(selectedPermissions));

    fetch('update_permissions.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.text())
    .then(data => {
      alert(data);  // You can display success or failure messages
      $('#permissionModal').modal('hide');  // Hide the modal after saving
    })
    .catch(error => console.error('Error:', error));
  });
});
</script>
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

<!-- JAVASCRIPT -->
<script>


  // Select/Deselect All Checkboxes
  document.getElementById('selectAll').addEventListener('change', function () {
    const isChecked = this.checked;
    const checkboxes = document.querySelectorAll('#supplyTable tbody input[type="checkbox"]');

    checkboxes.forEach(function (checkbox) {
      checkbox.checked = isChecked;
    });
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
  // export btn ke liye
  function exportTableToCSV(filename = 'table-data.csv') {
    const rows = document.querySelectorAll("#supplyTable tr");
    let csv = [];

    rows.forEach(row => {
      let cols = Array.from(row.querySelectorAll("th, td"))
        .map(col => `"${col.innerText.trim()}"`);
      csv.push(cols.join(","));
    });

    const csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
    const downloadLink = document.createElement("a");
    downloadLink.href = URL.createObjectURL(csvFile);
    downloadLink.download = filename;
    downloadLink.click();

    // ⏱️ Show modal after 2 seconds (simulate save complete)
    setTimeout(() => {
      const exportModal = new bootstrap.Modal(document.getElementById('exportSuccessModal'));
      exportModal.show();
    }, 2000); // 2 second delay
  }

  // Event listener for 'OK' button
  document.getElementById("okBtn").addEventListener("click", function () {
    alert("You clicked OK, export is complete!");
    // Optionally, you can trigger another action here if needed
  });

  // Event listener for 'Cancel' button (will not do anything if clicked)
  const cancelBtns = document.querySelectorAll("[data-bs-dismiss='modal']");
  cancelBtns.forEach((btn) => {
    btn.addEventListener("click", function () {
      // Do nothing or log if you need
      console.log("Modal closed without action");
    });
  });

</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>