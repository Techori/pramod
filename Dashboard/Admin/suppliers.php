<?php
include '../../_conn.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// 1. Active Suppliers
$activeSuppliers = 0;
$sql = "SELECT COUNT(*) AS count FROM suppliers WHERE Actions = 'Active'";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) {
  $activeSuppliers = $row['count'];
}

// 2. Open Orders
$openOrders = 0;
$sql = "SELECT COUNT(*) AS count FROM purchase_order WHERE Status IN ('Ordered', 'In Transit')";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) {
  $openOrders = $row['count'];
}

// 3. This Month's Spending
$thisMonthSpending = 0;
$currentMonthStart = date('Y-m-01');
$currentMonthEnd = date('Y-m-t'); // Last day of the month
$sql = "SELECT SUM(Amount) AS total FROM purchase_order WHERE Date BETWEEN ? AND ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $currentMonthStart, $currentMonthEnd);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $thisMonthSpending = $row['total'] ?? 0;
}
$stmt->close();

// 4. Delivery Success Rate
$successRate = 0;
$totalOrders = 0;
$successfulDeliveries = 0;

$sql = "SELECT COUNT(*) AS total FROM purchase_order";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) {
  $totalOrders = $row['total'];
}

$sql = "SELECT COUNT(*) AS delivered FROM purchase_order WHERE Status = 'Received'";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) {
  $successfulDeliveries = $row['delivered'];
}

if ($totalOrders > 0) {
  $successRate = round(($successfulDeliveries / $totalOrders) * 100, 1); // percentage
}
function clean($input)
{
  return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['whatAction'])) {

  // ================= PURCHASE =================
  if ($_POST['whatAction'] === 'Purchase') {
    $name = clean($_POST['supplier']);
    $amount = floatval($_POST['amount']);
    $date = clean($_POST['date']);
    $item = clean($_POST['item']);
    $unit = clean($_POST['unit']);
    $payment_method = clean($_POST['payment_method']);
    $status = clean($_POST['status']);

    $allowedStatus = ['Received', 'In Transit', 'Ordered'];
    $allowedPayments = ['Bank Transfer', 'Cash', 'UPI', 'Cheque', 'Card'];
    if (!in_array($status, $allowedStatus) || !in_array($payment_method, $allowedPayments)) {
      die("Invalid status or payment method");
    }

    try {
      $conn->begin_transaction();

      $currentYear = date('Y');
      $query = "SELECT Purchase_Id FROM purchase_order 
                WHERE Purchase_Id LIKE 'PO-$currentYear-%' 
                ORDER BY CAST(SUBSTRING(Purchase_Id, 9) AS UNSIGNED) DESC 
                LIMIT 1 FOR UPDATE";
      $result = $conn->query($query);

      $newNum = 1;
      if ($row = $result->fetch_assoc()) {
        $lastId = $row['Purchase_Id'];
        $newNum = (int) substr($lastId, 9) + 1;
      }

      $newPurchaseId = 'PO-' . $currentYear . '-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);

      $stmt = $conn->prepare("INSERT INTO purchase_order 
        (Purchase_ID, Customer_Name, Amount, Date, Item, Unit, Payment_Method, Status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("ssdsssss", $newPurchaseId, $name, $amount, $date, $item, $unit, $payment_method, $status);
      $stmt->execute();

      $conn->commit();
      $stmt->close();

      header("Location: admin_dashboard.php?page=suppliers#purchase");
      exit;

    } catch (Exception $e) {
      $conn->rollback();
      error_log("Purchase insert error: " . $e->getMessage());
      echo "Error processing purchase order.";
    }
  }

  // ================ ADD SUPPLIER =================
  elseif ($_POST['whatAction'] === 'AddSupplier') {
    $name = clean($_POST['supplierName']);
    $type = clean($_POST['type']);
    $items = clean($_POST['items']);
    $orders = intval($_POST['orders']);
    $spending = floatval($_POST['spending']);

    $allowedTypes = ['Manufacturer', 'Distributor'];
    $allowedItems = ['Wires, Switches', 'Wires, Cables', 'Fans, Lights', 'Appliances', 'Switches, Sockets'];

    if (!in_array($type, $allowedTypes) || !in_array($items, $allowedItems)) {
      die("Invalid supplier type or items");
    }

    try {
      $conn->begin_transaction();

      $query = "SELECT Supplier_ID FROM suppliers 
              ORDER BY CAST(SUBSTRING(Supplier_ID, 4) AS UNSIGNED) DESC 
              LIMIT 1 FOR UPDATE";
      $result = $conn->query($query);

      $newNum = 1;
      if ($row = $result->fetch_assoc()) {
        $lastId = $row['Supplier_ID']; // SUP001
        $newNum = (int) substr($lastId, 3) + 1;
      }

      $newSupplierId = 'SUP' . str_pad($newNum, 3, '0', STR_PAD_LEFT);

      // Optional: define $actions or remove it if not needed
      $actions = 'Active'; // or null, or handle as per your logic

      $stmt = $conn->prepare("INSERT INTO suppliers 
      (Supplier_ID, Supplier_Name, Type, Items, Orders, Spending, Actions) 
      VALUES (?, ?, ?, ?, ?, ?, ?)");

      $stmt->bind_param("ssssids", $newSupplierId, $name, $type, $items, $orders, $spending, $actions);
      // sssids: 7 variables, correct data types

      $stmt->execute();
      $conn->commit();
      $stmt->close();

      header("Location: admin_dashboard.php?page=suppliers#purchase");

    } catch (Exception $e) {
      $conn->rollback();
      error_log("Supplier insert error: " . $e->getMessage());
      echo "Error adding supplier.";
    }

  }

}
?>


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

<h2 class="mb-1">Suppliers Dashboard</h2>
<p class="text-muted">Manage supplier relationships and orders</p>
<div class="row g-3">
  <div class="col-md-3">
    <div class="card p-3">
      <div class="d-flex justify-content-between">
        <div>
          <h6>Active Suppliers</h6>
          <h4><?= $activeSuppliers ?></h4>
          <!-- Optional: add trend comparison -->
        </div>
        <div class="card-icon"><i class="fa-brands fa-creative-commons-by"></i></div>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card p-3">
      <div class="d-flex justify-content-between">
        <div>
          <h6>Open Orders</h6>
          <h4><?= $openOrders ?></h4>
        </div>
        <div class="card-icon"><i class="fa-solid fa-door-open"></i></div>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card p-3">
      <div class="d-flex justify-content-between">
        <div>
          <h6>This Month Spending</h6>
          <h4>₹<?= number_format($thisMonthSpending, 2) ?></h4>
        </div>
        <div class="card-icon"><i class="fa-solid fa-calendar-days"></i></div>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card p-3">
      <div class="d-flex justify-content-between">
        <div>
          <h6>Delivery Success</h6>
          <h4><?= $successRate ?>%</h4>
        </div>
        <div class="card-icon"><i class="fa-solid fa-check"></i></div>
      </div>
    </div>
  </div>
</div>

<div class="row mt-4">
  <div class="col-md-3">
    <button class="btn btn-primary dashboard-btn" data-bs-toggle="modal" data-bs-target="#purchaseModal">Create Purchase
      Order</button>
  </div>
  <div class="col-md-3">
    <button class="btn btn-light dashboard-btn" data-bs-toggle="modal" data-bs-target="#suppliersModal">Add
      Supplier</button>
  </div>
  <!-- <div class="col-md-3">
    <button class="btn btn-light dashboard-btn">Supplier Report</button>
  </div>
  <div class="col-md-3">
    <button class="btn btn-light dashboard-btn">Order Status</button>
  </div> -->
</div>

<div class="row mt-4">
  <div class="col-md-4 col-sm-6">
    <div class="card p-3">
      <h6>Items Supplied</h6>
      <canvas id="spendingChart" height="150"></canvas> <!-- Adjust height of the canvas -->
    </div>
  </div>
  <div class="col-md-6">
    <div class="card p-3">
      <h6>Purchase Orders Trend</h6>
      <canvas id="ordersTrend" height="220"></canvas>
    </div>
  </div>
</div>

<!-- Search and Add User Row -->
<div class="tab-nav mt-4">
  <div>
    <a href="#purchase" class="active" onclick="showTab('purchase')">Purchase Orders</a>
    <a href="#suppliers" onclick="showTab('suppliers')">Suppliers</a>
  </div>
  <div>
    <input type="text" placeholder="Search..." class="form-control d-inline-block w-auto me-2" id="searchInput">
    <button id="modalTriggerButton" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#purchaseModal">
      <i class="bi bi-plus-circle"></i> <span id="actionLabel">New Order</span>
    </button>

    <!-- Modal Structure -->
    <div class="modal fade" id="purchaseModal" tabindex="-1" aria-labelledby="newOrderModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form id="orderForm" method="POST" action="suppliers.php">
            <div class="modal-header">
              <h5 class="modal-title" id="newOrderModalLabel">Add New Order</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
              <div class="mb-3">
                <label for="supplier" class="form-label">Supplier</label>
                <select class="form-select" id="supplier" name="supplier" required>
                  <option>Select supplier</option>
                  <?php
                  $result = $conn->query("SELECT Supplier_Name FROM suppliers");
                  if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                      echo '<option value="' . $row['Supplier_Name'] . '">' . $row['Supplier_Name'] . '</option>';
                    }
                  }
                  ?>
                </select>
              </div>

              <div class="mb-3">
                <label for="orderDate" class="form-label">Date</label>
                <input type="date" name="date" class="form-control" id="orderDate" required>
              </div>

              <div class="mb-3">
                <label for="items" class="form-label">Items</label>
                <input type="text" name="item" class="form-control" id="items" required>
              </div>

              <div class="mb-3">
                <label for="unit" class="form-label">Unit</label>
                <input type="text" name="unit" class="form-control" id="unit" required>
              </div>

              <div class="mb-3">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" name="amount" class="form-control" id="amount" required>
              </div>

              <div class="mb-3">
                <label for="payment_method" class="form-label">Payment</label>
                <select class="form-select" id="payment_method" name="payment_method" required>
                  <option value="Bank Transfer">Bank Transfer</option>
                  <option value="UPI">UPI</option>
                  <option value="Cheque">Cheque</option>
                  <option value="Cash">Cash</option>
                  <option value="Card">Card</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                  <option value="Received">Received</option>
                  <option value="In Transit">In Transit</option>
                  <option value="Ordered">Ordered</option>
                </select>
              </div>
              <input type="hidden" name="whatAction" value="Purchase">
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Save Order</button>
            </div>
          </form>
        </div>
      </div>
    </div>


  </div>
</div>

<div id="purchase" class="tab-content">
  <table class="table table-bordered table-responsive" id="purchaseTable">
    <thead class="table-light">
      <tr>
        <th>Order ID</th>
        <th>Supplier Name</th>
        <th>Date</th>
        <th>Item Name</th>
        <th>Amount</th>
        <th>Payment Method</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $result = $conn->query("SELECT * FROM purchase_order ORDER BY Purchase_Id DESC");

      if ($result && $result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
          ?>
          <tr>
            <td><?= htmlspecialchars($row['Purchase_Id']) ?></td>
            <td><?= htmlspecialchars($row['Customer_Name']) ?></td>
            <td><?= htmlspecialchars($row['Date']) ?></td>
            <td><?= htmlspecialchars($row['Item']) ?></td>
            <td><?= htmlspecialchars($row['Amount']) ?></td>
            <td><?= htmlspecialchars($row['Payment_Method']) ?></td>
            <td><?= htmlspecialchars($row['Status']) ?></td>
          </tr>
          <?php
        endwhile;
      else:
        ?>
        <tr>
          <td colspan="7" class="text-center">No purchase orders found.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Modal Structure -->
<div class="modal fade" id="suppliersModal" tabindex="-1" aria-labelledby="addSuppliersLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addSuppliersLabel">Add New Supplier</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Form Fields for New Supplier -->
        <form id="supplierForm" method="POST" action="suppliers.php">
          <input type="hidden" name="whatAction" value="AddSupplier">

          <div class="mb-3">
            <label for="supplierName" class="form-label">Supplier Name</label>
            <input type="text" class="form-control" id="supplierName" name="supplierName"
              placeholder="Enter Supplier Name" required>
          </div>

          <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <select class="form-select" id="type" name="type" required>
              <option value="Manufacturer">Manufacturer</option>
              <option value="Distributor">Distributor</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="items" class="form-label">Items</label>
            <select class="form-select" id="items" name="items" required>
              <option value="Wires, Switches">Wires, Switches</option>
              <option value="Wires, Cables">Wires, Cables</option>
              <option value="Fans, Lights">Fans, Lights</option>
              <option value="Appliances">Appliances</option>
              <option value="Switches, Sockets">Switches, Sockets</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="orders" class="form-label">Orders</label>
            <input type="number" class="form-control" id="orders" name="orders" placeholder="Enter Orders" required>
          </div>

          <div class="mb-3">
            <label for="spending" class="form-label">Spending</label>
            <input type="number" class="form-control" id="spending" name="spending" placeholder="Enter Spending"
              required>
          </div>
          <input type="hidden" name="suppliers" value="AddSupplier">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" data-bs-dismiss="modal" id="saveOrderBtn">Save Order</button>
      </div>
      </form>
    </div>
  </div>
</div>

<div id="suppliers" class="tab-content d-none">
  <div class="table-heading">Suppliers</div>
  <table class="table table-bordered" id="suppliersTable">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Supplier Name</th>
        <th>Type</th>
        <th>Items</th>
        <th>Orders</th>
        <th>Spending</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $result = $conn->query("SELECT * FROM suppliers ORDER BY Supplier_ID DESC");

      if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          echo "<tr>
                  <td>{$row['Supplier_ID']}</td>
                  <td>{$row['Supplier_Name']}</td>
                  <td>{$row['Type']}</td>
                  <td>{$row['Items']}</td>
                  <td>{$row['Orders']}</td>
                  <td>{$row['Spending']}</td>
                  <td>{$row['Actions']}</td>
                </tr>";
        }
      } else {
        echo "<tr><td colspan='7'>No suppliers found.</td></tr>";
      }
      ?>
    </tbody>
  </table>
</div>

<script>

  document.getElementById('searchInput').addEventListener('input', function () {
    const searchText = this.value.toLowerCase();

    let activeTab = document.querySelector('.tab-nav a.active').getAttribute('href').substring(1);
    let tableId = activeTab === 'purchase' ? 'purchaseTable' : 'suppliersTable';

    const rows = document.querySelectorAll(`#${tableId} tbody tr`);

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


  function showTab(tab) {
    // Hide all tab-content sections
    document.querySelectorAll('.tab-content').forEach(t => t.classList.add('d-none'));

    // Show selected tab
    document.getElementById(tab).classList.remove('d-none');

    // Remove active class from all links
    document.querySelectorAll('.tab-nav a').forEach(a => a.classList.remove('active'));

    // Add active class to clicked tab
    document.querySelector(`.tab-nav a[href="#${tab}"]`).classList.add('active');

    // Change button label & modal target
    let actionBtn = document.getElementById('modalTriggerButton');
    if (tab === 'purchase') {
      document.getElementById('actionLabel').innerText = 'New Order';
      actionBtn.setAttribute('data-bs-target', '#purchaseModal');
    } else {
      document.getElementById('actionLabel').innerText = 'Add Supplier';
      actionBtn.setAttribute('data-bs-target', '#suppliersModal');
    }
  }


</script>

<!-- Items supplied -->
<?php
$currentYear = date('Y');
$sql = "SELECT Item, SUM(Unit) AS total_quantity
        FROM purchase_order
        WHERE YEAR(Date) = ?
        GROUP BY Item";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $currentYear);
$stmt->execute();
$result = $stmt->get_result();

$labels = [];
$data = [];
while ($row = $result->fetch_assoc()) {
  $labels[] = $row['Item'];
  $data[] = (int) $row['total_quantity'];
}
$stmt->close();
?>

<!-- Purchase order trend -->
<?php
$currentDate = date('Y-m-01'); // Current month ka first date
$startDate = date('Y-m-01', strtotime('-5 months')); // 6 months range

$sql = "SELECT DATE_FORMAT(Date, '%b') AS month_name, 
               COUNT(*) AS order_count
        FROM purchase_order
        WHERE Date >= ? AND Date <= LAST_DAY(?)
        GROUP BY YEAR(Date), MONTH(Date)
        ORDER BY YEAR(Date), MONTH(Date)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $startDate, $currentDate);
$stmt->execute();
$result = $stmt->get_result();

$labels1 = [];
$data1 = [];
while ($row = $result->fetch_assoc()) {
  $labels1[] = $row['month_name'];
  $data1[] = (int) $row['order_count'];
}
$stmt->close();
?>



<script>
  const labels = <?php echo json_encode($labels); ?>;
  const data = <?php echo json_encode($data); ?>;
  console.log(labels, data);

  const ctxPie = document.getElementById('spendingChart');
  new Chart(ctxPie, {
    type: 'pie',
    data: {
      labels: labels,
      datasets: [{
        data: data,
        backgroundColor: ['#007bff', '#20c997', '#fd7e14', '#ff5733', '#6f42c1', '#343a40']
      }]
    },
    options: {
      responsive: true
    }
  });

  const labels1 = <?php echo json_encode($labels1); ?>;
  const data1 = <?php echo json_encode($data1); ?>;

  const ctxLine = document.getElementById('ordersTrend');
  new Chart(ctxLine, {
    type: 'line',
    data: {
      labels: labels1,
      datasets: [{
        label: 'Orders',
        data: data1,
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