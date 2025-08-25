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
    $type = clean($_POST['type']);
    $password = clean($_POST['password']);
    $confirm_password = clean($_POST['confirm_password']);
    $status = 'Active';

    // Capture permissions
    $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];
    $permissionsJson = json_encode($permissions);

    if ($password !== $confirm_password) {
      die("Passwords do not match.");
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

      $salt = bin2hex(random_bytes(16));
      $saltedPW =  $password . $salt;
      $hashedPW = hash('sha256', $saltedPW);

      $userstmt = $conn->prepare("INSERT INTO users (email , password, salt, user_type, user_roll, user_name) VALUES (?, ?, ?, ?, ?, ?)");
      $userstmt->bind_param("ssssss", $userEmail, $hashedPW, $salt, $type, $role, $userName);
      $userstmt->execute();
      $userstmt->close();

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

      header("Location: admin_dashboard.php?page=user_management");
    } catch (Exception $e) {
      echo "Error updating permissions: " . $e->getMessage();
    }
  } else if ($_POST['whatAction'] === 'deleteItem') {
    $itemId = clean($_POST['itemId']);

    $stmt = $conn->prepare("DELETE FROM user_management WHERE User_ID = ?");
    $stmt->bind_param("s", $itemId);
    $stmt->execute();
    $stmt->close();

    @header("Location: admin_dashboard.php?page=user_management");

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



<!-- Tab Content -->
<div class="tab-content" id="adminTabContent">
  <!-- USERS TAB -->
  <div class="tab-pane fade show active" id="users" role="tabpanel">
    <div class="d-flex justify-content-between mb-3">
      <div>
        <button class="btn btn-outline-secondary" id="refreshBtn">Refresh</button>
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
                    <label for="type" class="form-label">Type</label>
                    <select class="form-select" id="type" name="type" required>
                      <option value="Factory">Factory</option>
                      <option value="Vendor">Vendor</option>
                      <option value="Store">Store</option>
                    </select>
                  </div>

                  <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role" required>
                      <option value="Owner">Owner</option>
                      <option value="Manager">Manager</option>
                      <option value="Accountant">Accountant</option>
                    </select>
                  </div>

                  <div>
                    <label for="password">Password</label>
                    <input type="password" name="password" class="form-control mb-3" required>
                  </div>

                  <div>
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control mb-3" required>
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


    <?php

    // Query to fetch user data
    $query = "SELECT User_ID, User_Name, Email, Role, Status, Last_Login, Permission FROM user_management";
    $result = $conn->query($query);
    
    $query1 = "SELECT user_type FROM users";
    $userType = $conn->query($query1);
    ?>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-3">Users</h5>
        <div class="table-responsive">
          <table class="table align-middle" id="supplyTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>User</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
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
                  <td><?php echo $row['User_ID']; ?></td>
                  <td><?php echo $row['User_Name']; ?></td>
                  <td><?php echo $row['Email']; ?></td>
                  <td><?php echo $row['Role']; ?></td>
                  <td><?php echo $row['Status']; ?></td>
                  <td>
                    <div class="d-flex gap-2">
                      <!-- Permission button -->
                      <button class="btn btn-info btn-sm permissionBtn" data-bs-toggle="modal"
                      data-bs-target="#permissionModal" data-userid="<?php echo $row['User_ID']; ?>"
                      data-username="<?php echo $row['User_Name']; ?>"
                      data-permissions='<?php echo htmlspecialchars(json_encode($permissions), ENT_QUOTES, 'UTF-8'); ?>'>
                      Set Permissions
                    </button>

                      <form method="POST" action="user_management.php"
                        onsubmit="return confirm('Are you sure you want to delete this user?');">
                        <input type="hidden" name="whatAction" value="deleteItem">
                        <input type="hidden" name="itemId" value="<?php echo $row['User_ID']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm ms-1">Delete</button>
                      </form>
                    </div>
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
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="delete" name="permissions[]" value="Delete">
                <label class="form-check-label" for="delete">Delete</label>
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
      document.addEventListener('DOMContentLoaded', function () {
        // Get all permission buttons
        const permissionBtns = document.querySelectorAll('.permissionBtn');

        permissionBtns.forEach(button => {
          button.addEventListener('click', function () {
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
        document.getElementById('savePermissionBtn').addEventListener('click', function () {
          const userId = this.getAttribute('data-userid');
          const selectedPermissions = [];
          const checkboxes = document.querySelectorAll('#permissionForm input[type="checkbox"]:checked');

          checkboxes.forEach(checkbox => {
            selectedPermissions.push(checkbox.value);
          });
          // console.log('User ID:', userId);
          // console.log('Selected Permissions:', selectedPermissions);


          // Send the selected permissions to the server via AJAX or a simple form submission
          const formData = new FormData();
          formData.append('whatAction', 'UpdatePermissions');
          formData.append('userId', userId);
          formData.append('permissions', JSON.stringify(selectedPermissions));

          fetch('user_management.php', {
            method: 'POST',
            body: formData
          })
            .then(response => response.text())
            .then(data => {
              // alert(data); 
              // $('#permissionModal').modal('hide');
              location.reload();  // Reload the page to see changes
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
      // Export table data to CSV
function exportTableToCSV(filename = 'table-data.csv') {
  const rows = document.querySelectorAll("#supplyTable tr");
  let csv = [];

  rows.forEach(row => {
    let cols = Array.from(row.querySelectorAll("th, td"))
      .map(col => `"${col.innerText.trim()}"`);
    csv.push(cols.join(","));
  });

  // Create a Blob from the CSV string
  let csvFile = new Blob([csv.join("\n")], { type: "text/csv" });

  // Create a temporary link to trigger download
  let downloadLink = document.createElement("a");
  downloadLink.download = filename;
  downloadLink.href = window.URL.createObjectURL(csvFile);
  downloadLink.style.display = "none";
  document.body.appendChild(downloadLink);

  downloadLink.click();
  document.body.removeChild(downloadLink);
}

      // Refresh Button (Reload page)
      document.getElementById('refreshBtn').addEventListener('click', function () {
        location.reload();
      });

    </script>
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
    const permissions = ['maindashbord', 'billingdesk', 'accounting', 'investory', 'expenses', 'factorystock', 'retailstore', 'aftersellservice', 'suppliers', 'reports', 'settings', 'delete'];
    const selected = {};
    permissions.forEach(id => {
      selected[id] = document.getElementById(id).checked;
    });

    const user = users[selectedUserIndex];
    console.log(`Saved permissions for ${user.name}:`, selected);

    const modal = bootstrap.Modal.getInstance(document.getElementById('permissionModal'));
    modal.hide();
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