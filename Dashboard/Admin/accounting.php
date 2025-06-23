<?php
include '../../_conn.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['whatAction'])) {

    // Clean input data function
    function clean($input)
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    // Transaction action
    if ($_POST['whatAction'] === 'Transaction') {
        // Collect data for transaction
        $date = clean($_POST['date']);
        $description = clean($_POST['description']);
        $type = clean($_POST['type']);
        $amount = floatval($_POST['amount']);
        $payment_method = clean($_POST['payment_method']);
        $status = clean($_POST['status']);

        // Validate data for transaction
        $allowedStatus = ['Completed', 'Pending'];
        $allowedPayments = ['Bank Transfer', 'Cash', 'UPI', 'Cheque', 'Card'];
        if (!in_array($status, $allowedStatus) || !in_array($payment_method, $allowedPayments)) {
            header("Location: admin_dashboard.php?page=accounting");
            echo json_encode(["success" => false, "message" => "Invalid status or payment method"]);
            exit;
        }

        // Start database transaction
        $conn->begin_transaction();

        try {
            // Generate a new transaction ID
            $result = $conn->query("SELECT Transaction_ID FROM transactions ORDER BY CAST(SUBSTRING(Transaction_ID, 5) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");

            if ($result && $row = $result->fetch_assoc()) {
                $lastId = $row['Transaction_ID']; // e.g. TRX-005
                $num = (int) substr($lastId, 4);   // get "005" → 5
                $newNum = $num + 1;
            } else {
                $newNum = 1;
            }

            $newTransactionId = 'TRX-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);

            // Insert the transaction record
            $stmt = $conn->prepare("INSERT INTO transactions 
                (Transaction_ID, Date, Description, Type, Amount, Status, payment_method) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param("ssssdss", $newTransactionId, $date, $description, $type, $amount, $status, $payment_method);
            $stmt->execute();

            $conn->commit();
            $stmt->close();

            header("Location: admin_dashboard.php?page=accounting");
            exit;

        } catch (Exception $e) {
            $conn->rollback();
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Transaction failed: " . $e->getMessage()
            ]);
            exit;
        }

    }
    // Account action
    else if ($_POST['whatAction'] === 'Account') {
        // Collect account details
        $businessAccount = clean($_POST['businessAccount']);
        $savingAccount = clean($_POST['savingAccount']);
        $cashAccount = clean($_POST['cashAccount']);

        // Validate account details
        if (!is_numeric($businessAccount) || !is_numeric($savingAccount) || !is_numeric($cashAccount) || $businessAccount < 0 || $savingAccount < 0 || $cashAccount < 0) {
            echo json_encode(["success" => false, "message" => "Invalid account details. Amount must be a valid number greater than or equal to 0."]);
            exit;
        }

        // Start database transaction for account
        $conn->begin_transaction();

        try {
            // Insert account details into the database
            $stmt = $conn->prepare("INSERT INTO accounts (business_account, saving_account, cash_account) VALUES (?, ?, ?)");
            $stmt->bind_param("ddd", $businessAccount, $savingAccount, $cashAccount); // 'd' stands for double (decimal numbers)

            // Execute the statement
            $stmt->execute();
            $conn->commit(); // Commit the transaction

            // Close the statement
            $stmt->close();

            header("Location: admin_dashboard.php?page=accounting");
            exit;

        } catch (Exception $e) {
            $conn->rollback(); // Rollback the transaction in case of error
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Failed to add account details: " . $e->getMessage()
            ]);
            exit;
        }
    } else {
        // Invalid action
        echo json_encode(["success" => false, "message" => "Invalid action"]);
        exit;
    }

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

    .accountingTab {
        padding: 10px 20px;
        cursor: pointer;
        border: none;
        background: none;
        font-size: 16px;
    }

    .accountingTab.active {
        border-bottom: 3px solid #007bff;
        font-weight: bold;
        color: #007bff;
    }

    .accounting-tab-content {
        display: none;
        padding: 20px 0;
    }

    .accounting-tab-content.active {
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
</style>

<h1>Accounting Dashboard</h1>
<p>Monitor financial health and transactions</p>

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
$profit_percent = ($last_profit > 0) ? ($profit - $last_profit) / $last_profit *100:0;
?>

<!-- Cards -->
<div class="row">
    <div class="col-md-4 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
            <div class="card-body">
                <h6 class="text-muted">Monthly Revenue</h6>
                <h3 class="fw-bold">₹<?= number_format($revenue['total']) ?></h3>
                <p class="<?= $revenueChange < 0 ? 'text-danger' : 'text-success' ?>"><?= round($revenueChange, 2) ?>% vs last month</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
            <div class="card-body">
                <h6 class="text-muted">Monthly Expenses</h6>
                <h3 class="fw-bold">₹<?php echo number_format($monthly_expenses, 0); ?></h3>
                <p class="<?php echo $monthly_expenses_class; ?>"><?php echo htmlspecialchars($monthly_expenses_text); ?> vs last month</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
            <div class="card-body">
                <h6 class="text-muted">Net Profit</h6>
                <h3 class="fw-bold">₹<?php echo number_format($profit, 0); ?></h3>
                <p class="<?= $profit_percent < 0 ? 'text-danger' : 'text-success' ?>"><?= round($profit_percent, 2) ?>% vs last month</p>
            </div>
        </div>
    </div>
</div>

<!-- Buttons -->
<div class="row justify-content-center">
    <div class="col-md-3 col-sm-6 mb-4">
        <a href="?page=reports" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-clipboard"></i>
            Financial Reports</a>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100" data-bs-toggle="modal"
            data-bs-target="#recordTransaction"><i class="fa-solid fa-circle-dollar-to-slot"></i> Record
            Transaction</button>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100" data-bs-toggle="modal"
            data-bs-target="#manageAccount"><i class="fa-solid fa-wallet"></i> Manage
            Accounts</button>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <button type="button" class="btn btn-outline-primary btn-lg w-100" onclick="exportTableToCSV()"><i class="fa-solid fa-download"></i> Export
            Data</button>
    </div>
</div>

<!-- Record Transaction Form -->
<div class="modal fade" id="recordTransaction" tabindex="-1" aria-labelledby="recordTransactionLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="admin_dashboard.php?page=accounting" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="recordTransactionLabel">Record Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" class="form-control" id="description" name="description" required>
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <input type="text" class="form-control" id="type" name="type" required>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="Completed">Completed</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cash">Cash</option>
                            <option value="UPI">UPI</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Card">Card</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" name="whatAction" value="Transaction">Add
                        Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Manage Account Form -->
<div class="modal fade" id="manageAccount" tabindex="-1" aria-labelledby="manageAccountLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="accounting.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="manageAccountLabel">Add Account Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="businessAccount" class="form-label">Business Account</label>
                        <input type="number" class="form-control" id="businessAccount" name="businessAccount" min="0"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="savingAccount" class="form-label">Savings Account</label>
                        <input type="number" class="form-control" id="savingAccount" name="savingAccount" min="0"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="cashAccount" class="form-label">Cash Account</label>
                        <input type="number" class="form-control" id="cashAccount" name="cashAccount" min="0"
                            required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" name="whatAction" value="Account">Add Details</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Tabels -->
<div class="col-md-12 card p-3 shadow-sm my-4 table-responsive table-responsive">

    <div class="tabs">
        <button class="accountingTab active" onclick="showaccountingTab('transaction')">Transaction</button>
        <button class="accountingTab" onclick="showaccountingTab('accounts')">Accounts</button>
        <button class="accountingTab" onclick="showaccountingTab('tax')">Tax Information</button>
        <!-- <button class="accountingTab" onclick="showaccountingTab('customer')">Customer</button> -->
    </div>

    <!-- Transaction -->
    <div id="transaction" class="accounting-tab-content active">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Recent Transactions</h1>
            </div>

            <div class="d-flex justify-content-end">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Search..." />
                </div>
            </div>
        </div>
        <table class="table table-bordered table-hover" id="supplyTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="transactionTableBody">
                <?php

                // Fetch transactions from the database
                $result = $conn->query("SELECT * FROM transactions ORDER BY Transaction_ID DESC");

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['Transaction_ID']) . "</td>";
                        echo "<td>" . date('d-M-Y', strtotime($row['Date'])) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Description']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                        echo "<td>₹" . number_format($row['Amount'], 2) . "</td>";
                        echo "<td>" . htmlspecialchars($row['payment_method']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Status']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No transactions found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <?php

    // Query the database to get account details
    $query = "SELECT business_account, saving_account, cash_account FROM accounts ORDER BY id DESC LIMIT 1";

    $result = $conn->query($query);

    // Check if the query was successful
    if (!$result) {
        die("ERROR: Could not execute query. " . $conn->error);
    }

    // Fetch the account details
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $businessAccount = $row['business_account'];
        $savingAccount = $row['saving_account'];
        $cashAccount = $row['cash_account'];
    } else {
        $businessAccount = 0;
        $savingAccount = 0;
        $cashAccount = 0;
    }
    ?>

    <!-- Accounts -->
    <div id="accounts" class="accounting-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="justify-content-start">
                <h1>Account Balances</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-sm-12 my-4">
                <div class="card stat-card cards shadow-sm" style="background-color:rgb(147, 212, 250);">
                    <div class="card-body">
                        <h5 class="text-muted">Main Business Account</h5>
                        <h4>₹<?php echo number_format($businessAccount, 0, '.', ','); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-12 my-4">
                <div class="card stat-card cards shadow-sm" style="background-color:rgb(212, 255, 233);">
                    <div class="card-body">
                        <h5 class="text-muted">Savings Account</h5>
                        <h4>₹<?php echo number_format($savingAccount, 0, '.', ','); ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-12 my-4">
                <div class="card stat-card cards shadow-sm" style="background-color:rgb(255, 251, 212);">
                    <div class="card-body">
                        <h5 class="text-muted">Cash Account</h5>
                        <h4>₹<?php echo number_format($cashAccount, 0, '.', ','); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Tax -->
    <div id="tax" class="accounting-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-content-start">
                <h1>Tax Information</h1>
            </div>
        </div>
        <div class="col-md-12">
            <div class="d-flex align-items-start border rounded mb-3 p-3" style="background-color:rgb(177, 202, 253);">

                <div>
                    <h6 class="mb-1 fw-bold text-primary">GST Information</h6>
                    <div class="d-flex" style="gap: 80px;">
                        <div style="flex: 1;">
                            <h6>GSTIN</h6>
                            <p>27AABCU9603R1ZX</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-start rounded mb-2 p-3" style="background-color:rgb(233, 221, 251);">

                <div>
                    <h6 class="mb-1 fw-bold" style="color: #6f42c1;">TDS Information</h6>
                    <div class="d-flex" style="gap: 80px;">
                        <div style="flex: 1;">
                            <h6>PAN</h6>
                            <p>AABCU9603R</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer -->
    <div id="customer" class="accounting-tab-content">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Customers</h1>
            </div>

            <div class="d-flex justify-content-end">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Search..." />
                </div>
            </div>
        </div>
        <table class="table table-bordered table-hover" id="customerTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="transactionTableBody">
                <?php

                // Fetch transactions from the database
                $result = $conn->query("SELECT * FROM transactions ORDER BY Transaction_ID DESC");

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['Transaction_ID']) . "</td>";
                        echo "<td>" . date('d-M-Y', strtotime($row['Date'])) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Description']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                        echo "<td>₹" . number_format($row['Amount'], 2) . "</td>";
                        echo "<td>" . htmlspecialchars($row['payment_method']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Status']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No transactions found</td></tr>";
                }
                ?>
            </tbody>
        </table>
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
        // Filter Functionality (Filter by "Ordered" status)
        document.getElementById('categoryFilter').addEventListener('click', function () {
            const rows = document.querySelectorAll('#supplyTable tbody tr');
            rows.forEach(row => {
                const status = row.cells[5].textContent.trim().toLowerCase();
                if (status === 'ordered') {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
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
    </script>
</div>

<script>

    function showaccountingTab(id) {
        const tabs = document.querySelectorAll('.accountingTab');
        const contents = document.querySelectorAll('.accounting-tab-content');

        tabs.forEach(tab => tab.classList.remove('active'));
        contents.forEach(content => content.classList.remove('active'));

        document.querySelector(`#${id}`).classList.add('active');
        document.querySelector(`[onclick="showaccountingTab('${id}')"]`).classList.add('active');
    }
</script>

</body>

</html>