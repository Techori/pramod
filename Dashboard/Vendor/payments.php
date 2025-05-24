<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../_conn.php';

$user_name = $_SESSION['user_name'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['whatAction'])) {

    if ($_POST['whatAction'] === 'editStatus') {

        // Get data from the form
        $invoice_id = $_POST['invoice_id'] ?? '';
        $status = $_POST['status'] ?? '';

        // Basic validation
        if (!empty($invoice_id) && !empty($status)) {
            // Prepare and execute the update query
            $stmt = $conn->prepare("UPDATE invoice SET status = ? WHERE invoice_id = ?");
            $stmt->bind_param("ss", $status, $invoice_id);

            if ($stmt->execute()) {
                @header("Location: vendor_dashboard.php?page=payments");
            } else {
                echo "Error updating status: " . $conn->error;
            }

            $stmt->close();
        } else {
            echo "Invalid input.";
        }
    }
}


?>

<h4><i class="fas fa-credit-card text-primary"></i> Payments</h4>
<p>Track payments and manage financial transactions.</p>

<?php

// Fetch total Revenue
$gst_sql = "SELECT SUM(grand_total) AS total_gst FROM invoice WHERE created_for = '$user_name'";
$gst_result = $conn->query($gst_sql);
$gst_count = $gst_result->fetch_assoc()['total_gst'] ?? 0;

// Fetch total Pending Payments
$outstanding_sql = "SELECT SUM(grand_total) AS total_outstanding FROM invoice WHERE status = 'Pending' AND created_for = '$user_name'";
$outstanding_result = $conn->query($outstanding_sql);
$outstanding_amount = $outstanding_result->fetch_assoc()['total_outstanding'] ?? 0;

?>

<div class="mb-4">
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Revenue</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="h3 font-weight-bold">₹<?= number_format($gst_count, 2) ?></p>
                            <p class="text-muted">Total amount received</p>
                        </div>
                        <div class="p-3 bg-success bg-opacity-10 rounded-circle">
                            <i class="fas fa-rupee-sign text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Pending Payments</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="h3 font-weight-bold">₹<?= number_format($outstanding_amount, 2) ?></p>
                            <p class="text-muted">Total amount pending</p>
                        </div>
                        <div class="p-3 bg-warning bg-opacity-10 rounded-circle">
                            <i class="fas fa-clock text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Payment Methods</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="h3 font-weight-bold">4</p>
                            <p class="text-muted">Payment methods used</p>
                        </div>
                        <div class="p-3 bg-primary bg-opacity-10 rounded-circle">
                            <i class="fas fa-credit-card text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Recent Transactions</h5>
            <!-- Search and Filters -->
            <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center mb-4">
                <div class="flex-grow-1">
                    <input type="hidden" name="page" value="billing">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0" id="searchInput"><i
                                class="fas fa-search"></i></span>
                        <input type="text" class="form-control border-start-0 table-search" data-table="paymentsTable"
                            placeholder="Search..." />
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <div>
                        <button class="btn btn-outline-primary gst-filter me-2" data-type="Completed"
                            data-table="paymentsTable">Completed</button>
                    </div>
                    <div>
                        <button class="btn btn-outline-primary gst-filter me-2" data-type="Pending"
                            data-table="paymentsTable">Pending</button>
                    </div>
                    <div>
                        <button class="btn btn-outline-primary gst-filter me-2" data-type="Refund"
                            data-table="paymentsTable">Refund</button>
                    </div>
                    <div>
                        <button class="btn btn-outline-danger reset-filters me-2" data-table="paymentsTable">Remove
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
                                const docType = row.children[6]?.innerText.trim().toLowerCase();
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
                <table class="table table-bordered table-hover" id="paymentsTable">
                    <thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>Invoice ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Actions</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        // Fetch transactions from the database
                        $result = $conn->query("SELECT * FROM invoice WHERE created_for = '$user_name' ORDER BY invoice_id DESC");

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $status = htmlspecialchars($row['status']);
                                $id = $row['invoice_id'];

                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['payment_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['invoice_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                                echo "<td>" . date('d-M-Y', strtotime($row['date'])) . "</td>";
                                echo "<td>₹" . number_format($row['grand_total'], 2) . "</td>";
                                echo "<td>" . htmlspecialchars($row['payment_method']) . "</td>";
                                echo "<td>" . $status . "</td>";

                                echo "<td>";
                                if ($status === 'Pending') {
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

                                // Modal only for pending rows
                                if ($status === 'Pending') {
                                    ?>
                                    <div class="modal fade" id="statusModal<?= $id ?>" tabindex="-1"
                                        aria-labelledby="statusModalLabel<?= $id ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form method="POST" action="payments.php">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="statusModalLabel<?= $id ?>">Update Status</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="invoice_id" value="<?= $id ?>">
                                                        <select name="status" class="form-select" required>
                                                            <option value="">Select Status</option>
                                                            <option value="Completed">Completed</option>
                                                            <option value="Refund">Refund</option>
                                                        </select>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary" name="whatAction" value="editStatus">Update</button>
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
                            echo "<tr><td colspan='8' class='text-center'>No transactions found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Payment Process -->
<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title">Payment Process</h5>
        <div class="mt-3">
            <div class="d-flex align-items-center mb-3">
                <div class="p-2 bg-primary bg-opacity-10 rounded-circle">
                    <i class="fas fa-check text-primary"></i>
                </div>
                <div class="ms-3">
                    <p class="font-weight-bold mb-0">Select payment method</p>
                    <p class="text-muted small">Choose your preferred payment option</p>
                </div>
            </div>
            <div class="d-flex align-items-center mb-3">
                <div class="p-2 bg-success bg-opacity-10 rounded-circle">
                    <i class="fas fa-check text-success"></i>
                </div>
                <div class="ms-3">
                    <p class="font-weight-bold mb-0">Enter payment details</p>
                    <p class="text-muted small">Provide necessary payment information</p>
                </div>
            </div>
            <div class="d-flex align-items-center mb-3">
                <div class="p-2 bg-warning bg-opacity-10 rounded-circle">
                    <i class="fas fa-check text-warning"></i>
                </div>
                <div class="ms-3">
                    <p class="font-weight-bold mb-0">Review and confirm</p>
                    <p class="text-muted small">Verify the payment details before submitting</p>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <div class="p-2 bg-secondary bg-opacity-10 rounded-circle">
                    <i class="fas fa-check text-secondary"></i>
                </div>
                <div class="ms-3">
                    <p class="font-weight-bold mb-0">Payment successful</p>
                    <p class="text-muted small">Confirmation of successful payment</p>
                </div>
            </div>
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