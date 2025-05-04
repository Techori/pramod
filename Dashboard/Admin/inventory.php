<?php
include '../../_conn.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['whatAction'])) {

    // Clean input data function
    function clean($input)
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    // Transaction action
    if ($_POST['whatAction'] === 'Inventory') {
        // Collect data for transaction
        $Product_Name = clean($_POST['Product_Name']);
        $Category = clean($_POST['Category']);
        $Stock = clean($_POST['Stock']);
        $Status = clean($_POST['Status']);
        $Supplier = clean($_POST['Supplier']);

        // Validate data for transaction
        $allowedStatus = ['In Stock', 'Low Stock'];
        if (!in_array($Status, $allowedStatus)) {
            echo json_encode(["success" => false, "message" => "Invalid status"]);
            header("Location: admin_dashboard.php?page=inventory");
            exit;
        }

        // Start database transaction
        $conn->begin_transaction();

        try {
            // Generate a new transaction ID
            $result = $conn->query("SELECT Id FROM inventory ORDER BY CAST(SUBSTRING(Id, 5) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");
            if ($result && $row = $result->fetch_assoc()) {
                $lastId = $row['Id']; // e.g. TRX-005
                $num = (int) substr($lastId, 4);   // get "005" → 5
                $newNum = $num + 1;
            } else {
                $newNum = 1;
            }

            $newTransactionId = 'TRX-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);

            // Insert the transaction record
            $stmt = $conn->prepare("INSERT INTO inventory 
                (Id, Product_Name, Category, Stock, Status, Supplier) 
                VALUES (?, ?, ?, ?, ?, ?)");

$stmt->bind_param("ssssss", $newTransactionId, $Product_Name, $Category, $Stock, $Status, $Supplier);

            $stmt->execute();

            $conn->commit();
            $stmt->close();

            header("Location: admin_dashboard.php?page=inventory");
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

<h1>Inventory Dashboard</h1>
<p>Manage stock, products and suppliers</p>

<!-- Cards -->
<?php
// Fetch dynamic card data
$totalProducts = $conn->query("SELECT COUNT(*) as total FROM inventory")->fetch_assoc()['total'];
$lowStock = $conn->query("SELECT COUNT(*) as low FROM inventory WHERE Status = 'Low Stock'")->fetch_assoc()['low'];
$categoryCount = $conn->query("SELECT COUNT(DISTINCT Category) as cat FROM inventory")->fetch_assoc()['cat'];
$supplierCount = $conn->query("SELECT COUNT(DISTINCT Supplier) as supp FROM inventory")->fetch_assoc()['supp'];
?>

<div class="row">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
            <div class="card-body">
                <h6 class="text-muted">Total Products</h6>
                <h3 class="fw-bold"><?= $totalProducts ?></h3>
                <p>Across <?= $categoryCount ?> categories</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
            <div class="card-body">
                <h6 class="text-muted">Low Stock Items</h6>
                <h3 class="fw-bold"><?= $lowStock ?></h3>
                <p class="text-danger">Requires attention</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
            <div class="card-body">
                <h6 class="text-muted">Recent Sales</h6>
                <h3 class="fw-bold">₹0</h3> <!-- Update this if you have sales table -->
                <p class="text-success">Last 7 days</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
            <div class="card-body">
                <h6 class="text-muted">Pending Orders</h6>
                <h3 class="fw-bold">0</h3> <!-- Update if you have orders table -->
                <p class="text-info-emphasis">From <?= $supplierCount ?> suppliers</p>
            </div>
        </div>
    </div>
</div>

<!-- Search bar & buttons -->
<div class="container-fluid d-flex justify-content-between align-items-center">
    <div class="d-flex">
        <div class="input-group w-100 me-2">
            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control border-start-0" placeholder="Search..." id="searchInput" />

        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary d-flex" data-bs-toggle="modal" data-bs-target="#addStock">
                <i class="fa-solid fa-circle-plus"></i><span> Add</span><span> Stock</span>
            </button>
            <a href="?page=reports" class="btn btn-outline-primary d-flex">
                <i class="fa-solid fa-chart-column"></i> Report
            </a>
            <button class="btn btn-outline-primary d-flex" id="refreshBtn"><i class="fa-solid fa-arrows-rotate"></i> Refresh</button>

        </div>
    </div>
</div>

<!-- Add Stock form -->
<div class="modal fade" id="addStock" tabindex="-1" aria-labelledby="addStockLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="inventory.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStockLabel">Add Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- <div class="mb-3">
                        <label for="id" class="form-label">Id</label>
                        <input type="text" class="form-control" id="id">
                    </div> -->

                    <div class="mb-3">
                        <label for="Product_Name" class="form-label">Product_Name</label>
                        <input type="text" class="form-control" id="product_Name" name="Product_Name" required>
                    </div>

                    <div class="mb-3">
                        <label for="Category" class="form-label">Category</label>
                        <input type="text" class="form-control" id="Category" name="Category" required>
                    </div>

                    <div class="mb-3">
                        <label for="Stock" class="form-label">Stock</label>
                        <input type="text" class="form-control" id="Stock" name="Stock" required>
                    </div>

                    <div class="mb-3">
                        <label for="Status" class="form-label">Status</label>
                        <select class="form-select" id="Status" name="Status" required>
                            <option value="In Stock">In Stock</option>
                            <option value="Low Stock">Low Stock</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="Supplier" class="form-label">Supplier</label>
                        <input type="text" class="form-control" id="Supplier" name="Supplier" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" name="whatAction" value="Inventory">Add
                        Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Table -->
<div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">

    <div id="inventory">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <div class="justify-contnt-start">
                <h1>Inventory Items</h1>
            </div>

            <div class="justify-content-end">
                <!-- <button class="btn btn-outline-primary">View All</button> -->
                <button class="btn btn-outline-primary" id="viewAllBtn">View All</button>
            </div>

        </div>
        <table id="Table" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th value="Id">ID</th>
                    <th value="Product_Name">Product Name</th>
                    <th value="Category">Category</th>
                    <th value="Stock">Stock</th>
                    <th value="Status">Status</th>
                    <th value="Supplier">Supplier</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php

                // Fetch transactions from the database
                $result = $conn->query("SELECT * FROM inventory ORDER BY Id DESC");

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['Id']) . "</td>";
                        // echo "<td>" . date('d-M-Y', strtotime($row['Date'])) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Product_Name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Category']) . "</td>";
                        // echo "<td>₹" . number_format($row['Amount'], 2) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Stock']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Status']) . "</td>";
                        echo "<td>" . htmlspecialchars($row["Supplier"]) . "</td>";
                        echo '<td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary btn-sm"><i class="fa-regular fa-eye"></i></button>
                                    <!-- View button -->
                                    <button class="btn btn-outline-primary btn-sm"><i
                                            class="fa-regular fa-file-lines"></i></button> <!-- Print button -->
                                    <button class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-download"></i></button>
                                    <!-- Download button -->

                                </div>
                            </td>';
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>No inventory found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
// Search filter function
document.getElementById('searchInput').addEventListener('keyup', function () {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#Table tbody tr');
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});

// Refresh button function
document.getElementById('refreshBtn').addEventListener('click', function () {
    window.location.reload(); // simple page reload
});

// View All button function
document.getElementById('viewAllBtn')?.addEventListener('click', function () {
    document.getElementById('searchInput').value = ''; // Clear the search box
    let rows = document.querySelectorAll('#Table tbody tr');
    rows.forEach(row => row.style.display = ''); // Show all rows
});

</script>
</body>
</html>


    </body>

    </html>