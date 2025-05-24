<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../_conn.php';
$user_name = $_SESSION['user_name'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['whatAction'])) {

    if ($_POST['whatAction'] === 'updateDeliveryDate') {
        $trackingId = $_POST['tracking_id'];
        $deliveryDate = $_POST['delivery_date'];
        $status = 'In Transit';

        // Generate a new delivery ID
        $result = $conn->query("SELECT delivery_id FROM retail_store_stock_request ORDER BY CAST(SUBSTRING(delivery_id, 6) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");

        if ($result && $row = $result->fetch_assoc()) {
            $lastId = $row['delivery_id']; // e.g. SL-005
            $num = (int) substr($lastId, 5);   // get "005" → 5
            $newNum = $num + 1;
        } else {
            $newNum = 1;
        }

        $newDeliveryId = 'DELS-' . str_pad($newNum, 3, '0', STR_PAD_LEFT);

        $stmt = $conn->prepare("UPDATE retail_store_stock_request SET delivery_id = ?, delivery_date = ?, status = ? WHERE tracking_id  = ?");
        $stmt->bind_param("ssss", $newDeliveryId, $deliveryDate, $status, $trackingId);
        $stmt->execute();
        $stmt->close();

        $quantityFetch = $conn->query("SELECT * FROM retail_store_stock_request WHERE tracking_id = '$trackingId' LIMIT 1");

        while ($row = $quantityFetch->fetch_assoc()) {
            $quantity = (int) $row["quantity"];
            $itemName = $row["item_name"];

            $updateStockStmt = $conn->prepare("
                UPDATE vendor_product 
                SET stock = stock - ? 
                WHERE product_name = ?
            ");
            $updateStockStmt->bind_param("is", $quantity, $itemName);
            $updateStockStmt->execute();
            $updateStockStmt->close();
        }

        @header("Location: vendor_dashboard.php?page=orders");

    }

}

?>

<h4><i class="fas fa-shopping-cart text-primary"></i> Order Management</h4>
<p>Manage and process orders from factories.</p>


<!-- Header with New Order Button -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="text-muted">Order List</h5>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary btn-sm" onclick="exportTableToCSV()">
            <i class="fas fa-file-export"></i> Export
        </button>
        <button class="btn btn-outline-primary btn-sm" id="refreshBtn">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
        <script>
            // Refresh Button (Reload page)
            document.getElementById('refreshBtn').addEventListener('click', function () {
                location.reload();
            });
        </script>
    </div>
</div>


<!-- Orders Table -->
<div class="card shadow-sm">
    <div class="card-body">
        <!-- Search and Filters -->
        <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center mb-4">
            <div class="flex-grow-1">
                <input type="hidden" name="page" value="billing">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" id="searchInput"><i
                            class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0 table-search" data-table="ordersTable"
                        placeholder="Search..." />
                </div>
            </div>
            <div class="d-flex gap-2">
                <div>
                    <button class="btn btn-outline-primary gst-filter me-2" data-type="Ordered"
                        data-table="ordersTable">Ordered</button>
                </div>
                <div>
                    <button class="btn btn-outline-primary gst-filter me-2" data-type="In Transit"
                        data-table="ordersTable">In Transit</button>
                </div>
                <div>
                    <button class="btn btn-outline-primary gst-filter me-2" data-type="Received"
                        data-table="ordersTable">Received</button>
                </div>
                <div>
                    <button class="btn btn-outline-danger reset-filters me-2" data-table="ordersTable">Remove
                        Filters</button>
                </div>
            </div>
        </div>

        <script>


            document.addEventListener("DOMContentLoaded", () => {

                // 🔍 Live Search Function
                document.querySelectorAll(".table-search").forEach(input => {
                    input.addEventListener("input", () => {
                        const tableId = input.dataset.table;
                        const value = input.value.toLowerCase();
                        const rows = document.querySelectorAll(`#${tableId} tbody tr`);
                        rows.forEach(row => {
                            const text = row.textContent.toLowerCase();
                            row.style.display = text.includes(value) ? "" : "none";
                        });
                    });
                });

                // 🧾 GST Filter Buttons
                document.querySelectorAll(".gst-filter").forEach(button => {
                    button.addEventListener("click", () => {
                        const type = button.dataset.type.toLowerCase();
                        const tableId = button.dataset.table;
                        const rows = document.querySelectorAll(`#${tableId} tbody tr`);
                        rows.forEach(row => {
                            const docType = row.children[13]?.innerText.trim().toLowerCase();
                            row.style.display = docType === type ? "" : "none";
                        });
                    });
                });

                // ❌ Remove Filters Button
                document.querySelectorAll(".reset-filters").forEach(button => {
                    button.addEventListener("click", () => {
                        const tableId = button.dataset.table;
                        const rows = document.querySelectorAll(`#${tableId} tbody tr`);
                        rows.forEach(row => {
                            row.style.display = "";
                        });

                        // Also clear search inputs for that table
                        document.querySelectorAll(`.table-search[data-table='${tableId}']`).forEach(input => {
                            input.value = "";
                        });
                    });
                });

                // ✅ Filter Helper Function
                function filterTable(tableId, conditionFn) {
                    const rows = document.querySelectorAll(`#${tableId} tbody tr`);
                    rows.forEach(row => {
                        row.style.display = conditionFn(row) ? "" : "none";
                    });
                }
            });
        </script>
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="ordersTable">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Tracking ID</th>
                        <th>Delivery ID</th>
                        <th>Date</th>
                        <th>Shop Name</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Location</th>
                        <th>Requested By</th>
                        <th>Received By</th>
                        <th>Delivery Date</th>
                        <th>Received Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch all rows from retail_store_stock_request
                    // Fetch rows related to the logged-in user
                    $result = $conn->query("SELECT * FROM retail_store_stock_request WHERE request_to = '$user_name' ORDER BY request_id DESC");

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $status = htmlspecialchars($row['status']);
                            $id = htmlspecialchars($row['tracking_id']);

                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['request_id']) . "</td>";
                            echo "<td>" . $id . "</td>";
                            echo "<td>" . htmlspecialchars($row['delivery_id'] ?? '-') . "</td>";
                            echo "<td>" . date('d-M-Y', strtotime($row['date'])) . "</td>";
                            echo "<td>" . htmlspecialchars($row['shop_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['requested_by']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['received_by'] ?? '-') . "</td>";
                            echo "<td>" . (!empty($row['delivery_date']) ? date('d-M-Y', strtotime($row['delivery_date'])) : '-') . "</td>";
                            echo "<td>" . (!empty($row['received_date']) ? date('d-M-Y', strtotime($row['received_date'])) : '-') . "</td>";
                            echo "<td>" . $status . "</td>";

                            echo "<td>";
                            if ($status === 'Ordered') {
                                echo '<button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#statusModal' . $id . '">
                        <i class="fa-regular fa-pen-to-square"></i>
                    </button>';
                            } else {
                                echo '<button class="btn btn-outline-secondary btn-sm" disabled>
                        <i class="fa-regular fa-pen-to-square"></i>
                    </button>';
                            }
                            echo "</td>";
                            echo "</tr>";

                            // Modal for updating status
                            if ($status === 'Ordered') {
                                ?>
                                <div class="modal fade" id="statusModal<?= $id ?>" tabindex="-1"
                                    aria-labelledby="statusModalLabel<?= $id ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form method="POST" action="orders.php">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="statusModalLabel<?= $id ?>">Update Status</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="tracking_id" value="<?= $id ?>">
                                                    <label class="form-label">Delivery Date</label>
                                                    <input type="date" name="delivery_date" class="form-control"
                                                        placeholder="Delivery Date" required>
                                                    <!-- <select name="status" class="form-select" required>
                                                        <option value="">Select Status</option>
                                                        <option value="Dispatched">Dispatched</option>
                                                        <option value="Delivered">Delivered</option>
                                                        <option value="Cancelled">Cancelled</option>
                                                    </select> -->
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary" name="whatAction"
                                                        value="updateDeliveryDate">Update</button>
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                    } else {
                        echo "<tr><td colspan='15' class='text-center'>No stock requests found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>


            <script>
                // Search Functionality
                document.getElementById('ordersSearch').addEventListener('input', function () {
                    const searchText = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#ordersTable tbody tr');

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
                    const rows = document.querySelectorAll("#ordersTable tr");
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
    </div>
</div>


<style>
    .bg-purple {
        background-color: #6f42c1;
    }

    .text-purple {
        color: #6f42c1;
    }

    .btn-outline-purple {
        border-color: #6f42c1;
        color: #6f42c1;
    }

    .btn-outline-purple:hover {
        background-color: #6f42c1;
        color: #fff;
    }

    .badge {
        font-size: 0.85rem;
        padding: 4px 8px;
    }
</style>