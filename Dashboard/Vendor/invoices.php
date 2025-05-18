<?php

// Include mock database
require_once 'database.php';

// Get invoices and factories from database
$invoices = get_invoices();
$factories = get_factories();

// Handle actions
$success_message = '';
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        if ($action === 'generate_invoice') {
            // Form submission for new invoice
            $invoice_data = [
                'customer' => isset($_POST['customer']) ? trim($_POST['customer']) : '',
                'amount' => isset($_POST['amount']) ? trim($_POST['amount']) : '',
                'type' => isset($_POST['type']) ? trim($_POST['type']) : '',
                'gstNumber' => isset($_POST['gstNumber']) ? trim($_POST['gstNumber']) : '',
                'date' => isset($_POST['date']) ? trim($_POST['date']) : '',
                'dueDate' => isset($_POST['dueDate']) ? trim($_POST['dueDate']) : ''
            ];
            $result = save_invoice($invoice_data);
            if ($result['success']) {
                $success_message = $result['message'];
            } else {
                $error_message = $result['message'];
            }
        } elseif ($action === 'download_invoice' && isset($_POST['invoice_id'])) {
            $invoice_id = trim($_POST['invoice_id']);
            $success_message = "Downloading invoice $invoice_id";
        } elseif ($action === 'view_invoice' && isset($_POST['invoice_id'])) {
            $invoice_id = trim($_POST['invoice_id']);
            $success_message = "Viewing invoice $invoice_id";
        } elseif ($action === 'download_all') {
            $success_message = 'Downloading all invoices';
        } elseif ($action === 'gst_reports') {
            $success_message = 'Generating GST reports';
        } elseif ($action === 'filter_templates') {
            $success_message = 'Applying filter templates';
        }
    }
}

// Filter and search parameters
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) && in_array($_GET['status'], ['All', 'Paid', 'Pending', 'Overdue']) ? $_GET['status'] : 'All';
$type_filter = isset($_GET['type']) && in_array($_GET['type'], ['All', 'GST', 'Non-GST']) ? $_GET['type'] : 'All';

// Filter invoices
$filtered_invoices = array_filter($invoices, function ($invoice) use ($search_query, $status_filter, $type_filter) {
    $matches_search = empty($search_query) || stripos($invoice['id'], $search_query) !== false;
    $matches_status = $status_filter === 'All' || $invoice['status'] === $status_filter;
    $matches_type = $type_filter === 'All' || $invoice['type'] === $type_filter;
    return $matches_search && $matches_status && $matches_type;
});

// Statuses and types for filter
$statuses = ['All', 'Paid', 'Pending', 'Overdue'];
$types = ['All', 'GST', 'Non-GST'];

// Default dates
$today = '2025-04-25';
$default_due_date = (new DateTime($today))->modify('+30 days')->format('Y-m-d');
?>

<h4><i class="fas fa-file-invoice text-primary"></i> Invoices (<?php echo count($filtered_invoices); ?>)</h4>
<p>Generate and manage invoices.</p>

<?php if ($success_message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($success_message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($error_message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Header with Generate Invoice Button -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <button type="button" class="btn btn-primary btn-sm" onclick="exportTableToCSV()">
        <i class="fas fa-plus me-1"></i> Generate Invoice
    </button>
</div>

<!-- Filters and Search -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex flex-column flex-md-row gap-3 align-items-md-end">
            <!-- Search -->
            <div class="flex-grow-1">
                <label class="form-label text-muted">Search Invoices</label>
                <form method="GET" action="?page=invoices" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="page" value="invoices">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" id="invoicesSearch"
                            placeholder="Search invoices by ID..."
                            value="<?php echo htmlspecialchars($search_query); ?>">
                    </div>
                </form>
            </div>
            <!-- Filters -->
            <div class="d-flex flex-column gap-3">
                <!-- Status Filter -->
                <div>
                    <label class="form-label text-muted">Status</label>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($statuses as $status): ?>
                            <a
                                href="?page=invoices&status=<?php echo urlencode($status); ?>&type=<?php echo urlencode($type_filter); ?>&search=<?php echo urlencode($search_query); ?>">
                                <span
                                    class="badge <?php echo $status_filter === $status ? 'bg-primary text-white' : 'bg-light text-dark'; ?> px-3 py-1 rounded-pill">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!-- Type Filter -->
                <div>
                    <label class="form-label text-muted">Invoice Type</label>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($types as $type): ?>
                            <a
                                href="?page=invoices&status=<?php echo urlencode($status_filter); ?>&type=<?php echo urlencode($type); ?>&search=<?php echo urlencode($search_query); ?>">
                                <span
                                    class="badge <?php echo $type_filter === $type ? 'bg-primary text-white' : 'bg-light text-dark'; ?> px-3 py-1 rounded-pill">
                                    <?php echo htmlspecialchars($type); ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <!-- Clear Filters -->
            <div>
                <a href="?page=invoices" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-filter me-1"></i> Clear Filters
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Invoices Table -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Recent Invoices</h5>
        <p class="text-muted">View and manage your invoices</p>
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="invoicesTable">
                <thead>
                    <tr>
                        <th>Invoice ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Due Date</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($filtered_invoices)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-file-invoice fa-2x text-muted"></i>
                                <p class="mt-2 text-muted">No invoices found matching your criteria.</p>
                                <a href="?page=invoices" class="btn btn-outline-primary btn-sm">Clear Filters</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($filtered_invoices as $invoice): ?>
                            <tr>
                                <td><a href="#" class="text-primary"><?php echo htmlspecialchars($invoice['id']); ?></a></td>
                                <td><?php echo htmlspecialchars($invoice['customer']); ?></td>
                                <td>
                                    <?php
                                    $date = new DateTime($invoice['date']);
                                    echo $date->format('M j, Y');
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $due_date = new DateTime($invoice['dueDate']);
                                    echo $due_date->format('M j, Y');
                                    ?>
                                </td>
                                <td>₹<?php echo number_format($invoice['amount'], 0); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($invoice['type']); ?>
                                    <?php if ($invoice['type'] === 'GST'): ?>
                                        <div class="small text-muted">GST: <?php echo htmlspecialchars($invoice['gstNumber']); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?php
                                    echo $invoice['status'] === 'Paid' ? 'bg-success' :
                                        ($invoice['status'] === 'Pending' ? 'bg-warning' : 'bg-danger');
                                    ?> text-white">
                                        <i class="fas <?php
                                        echo $invoice['status'] === 'Paid' ? 'fa-check-circle' :
                                            ($invoice['status'] === 'Pending' ? 'fa-clock' : 'fa-exclamation-circle');
                                        ?> me-1"></i>
                                        <?php echo htmlspecialchars($invoice['status']); ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <form method="POST" action="?page=invoices" class="d-inline">
                                            <input type="hidden" name="action" value="view_invoice">
                                            <input type="hidden" name="invoice_id"
                                                value="<?php echo htmlspecialchars($invoice['id']); ?>">
                                            <button type="submit" class="btn btn-outline-primary btn-sm" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="?page=invoices" class="d-inline">
                                            <input type="hidden" name="action" value="download_invoice">
                                            <input type="hidden" name="invoice_id"
                                                value="<?php echo htmlspecialchars($invoice['id']); ?>">
                                            <button type="submit" class="btn btn-outline-success btn-sm" title="Download">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <script>
                // Search Functionality
                document.getElementById('invoicesSearch').addEventListener('input', function () {
                    const searchText = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#invoicesTable tbody tr');

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

                // Export table data to CSV function
                function exportTableToCSV(filename = 'table-data.csv') {
                    const rows = document.querySelectorAll("#invoicesTable tr");
                    let csv = [];
                    rows.forEach(row => {
                        let cols = Array.from(row.querySelectorAll("th, td"))
                            .map(col => `"${col.innerText.trim().replace(/"/g, '""')}"`);  // Escape quotes
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

<!-- Invoice Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">GST Invoices</h5>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="h3 font-weight-bold">
                            <?php
                            echo count(array_filter($invoices, function ($i) {
                                return $i['type'] === 'GST';
                            }));
                            ?>
                        </p>
                        <p class="text-muted">Total GST Invoices</p>
                    </div>
                    <div class="p-3 bg-primary bg-opacity-10 rounded-circle">
                        <i class="fas fa-file-invoice text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Non-GST Invoices</h5>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="h3 font-weight-bold">
                            <?php
                            echo count(array_filter($invoices, function ($i) {
                                return $i['type'] === 'Non-GST';
                            }));
                            ?>
                        </p>
                        <p class="text-muted">Total Non-GST Invoices</p>
                    </div>
                    <div class="p-3 bg-purple bg-opacity-10 rounded-circle">
                        <i class="fas fa-file-invoice text-purple"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Outstanding Amount</h5>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="h3 font-weight-bold">
                            ₹<?php
                            echo number_format(array_sum(array_map(function ($i) {
                                return $i['status'] !== 'Paid' ? $i['amount'] : 0;
                            }, $invoices)), 0);
                            ?>
                        </p>
                        <p class="text-muted">Total Pending Amount</p>
                    </div>
                    <div class="p-3 bg-warning bg-opacity-10 rounded-circle">
                        <i class="fas fa-file-invoice text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title">Quick Actions</h5>
        <p class="text-muted">Generate or request documents</p>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
            <div class="col">
                <button type="button"
                    class="btn btn-outline-primary btn-sm w-100 h-100 py-3 d-flex flex-column align-items-center gap-2"
                    data-bs-toggle="modal" data-bs-target="#generateInvoiceModal">
                    <i class="fas fa-plus text-primary"></i>
                    <span>New Invoice</span>
                </button>
            </div>
            <div class="col">
                <form method="POST" action="?page=invoices">
                    <input type="hidden" name="action" value="download_all">
                    <button type="button" id="downloadAllBtn"
                        class="btn btn-outline-success btn-sm w-100 h-100 py-3 d-flex flex-column align-items-center gap-2">
                        <i class="fas fa-download text-success"></i>
                        <span>Download All</span>
                    </button>
                    <script>
                        document.getElementById("downloadAllBtn").addEventListener("click", function () {
                            const rows = document.querySelectorAll("#invoicesTable tr");
                            let csv = [];

                            rows.forEach(row => {
                                let cols = Array.from(row.querySelectorAll("th, td"))
                                    .map(col => `"${col.innerText.trim()}"`);
                                csv.push(cols.join(","));
                            });

                            const csvContent = csv.join("\n");
                            const blob = new Blob([csvContent], { type: "text/csv" });
                            const url = URL.createObjectURL(blob);

                            const a = document.createElement("a");
                            a.href = url;
                            a.download = "all-invoices.csv";
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                        });
                    </script>


                </form>
            </div>
            <div class="col">
                <form method="POST" action="?page=invoices">
                    <input type="hidden" name="action" value="gst_reports">
                    <button id="gstReportBtn" type="button"
                        class="btn btn-outline-purple btn-sm w-100 h-100 py-3 d-flex flex-column align-items-center gap-2">
                        <i class="fas fa-file-alt text-purple"></i>
                        <span>GST Reports</span>
                    </button>

                    <script>
                        document.getElementById('gstReportBtn').addEventListener('click', () => {
                            const rows = document.querySelectorAll('#invoicesTable tbody tr');
                            let csv = 'Invoice ID,Customer,Date,Due Date,Amount,Type,Status\n';

                            rows.forEach(row => {
                                // Skip rows that have a colspan (like "No invoices found" message)
                                if (row.querySelector('td[colspan]')) return;

                                // Get the "Type" cell text, ignoring the GST number div inside it
                                let typeCell = row.cells[5];
                                let typeText = '';
                                if (typeCell) {
                                    // Get only the text node before the div, or fallback to whole text
                                    typeText = typeCell.childNodes[0].textContent.trim();
                                }

                                if (typeText === 'GST') {
                                    // Extract columns 0 to 6
                                    const data = [];
                                    for (let i = 0; i <= 6; i++) {
                                        // Remove commas so CSV format is not broken
                                        let cellText = row.cells[i].innerText.replace(/,/g, '').trim();
                                        data.push(`"${cellText}"`);
                                    }
                                    csv += data.join(',') + '\n';
                                }
                            });

                            if (csv === 'Invoice ID,Customer,Date,Due Date,Amount,Type,Status\n') {
                                alert('No GST invoices found!');
                                return;
                            }

                            // Create and download the CSV file
                            const blob = new Blob([csv], { type: 'text/csv' });
                            const url = URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = 'gst-invoices-report.csv';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                            URL.revokeObjectURL(url);
                        });
                    </script>



                </form>
            </div>
            <div class="col">
                <form method="POST" action="?page=invoices">
                    <input type="hidden" name="action" value="filter_templates">
                    <button type="submit"
                        class="btn btn-outline-warning btn-sm w-100 h-100 py-3 d-flex flex-column align-items-center gap-2">
                        <i class="fas fa-filter text-warning"></i>
                        <span>Filter Templates</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Generate Invoice Modal -->
<div class="modal fade" id="generateInvoiceModal" tabindex="-1" aria-labelledby="generateInvoiceModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generateInvoiceModalLabel">Generate New Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="?page=invoices">
                <div class="modal-body">
                    <input type="hidden" name="action" value="generate_invoice">
                    <div class="mb-3">
                        <label for="customer" class="form-label">Customer</label>
                        <select name="customer" id="customer" class="form-select" required>
                            <option value="">Select a customer</option>
                            <?php foreach ($factories as $factory): ?>
                            <option value="<?php echo htmlspecialchars($factory['name']); ?>">
                                <?php echo htmlspecialchars($factory['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount (₹)</label>
                        <input type="number" name="amount" id="amount" class="form-control" min="1" step="0.01"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Invoice Type</label>
                        <select name="type" id="type" class="form-select" required>
                            <option value="GST">GST</option>
                            <option value="Non-GST">Non-GST</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="gstNumber" class="form-label">GST Number</label>
                        <input type="text" name="gstNumber" id="gstNumber" class="form-control"
                            placeholder="e.g., 29ABCDE1234F1Z5">
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Invoice Date</label>
                        <input type="date" name="date" id="date" class="form-control" value="<?php echo $today; ?>"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="dueDate" class="form-label">Due Date</label>
                        <input type="date" name="dueDate" id="dueDate" class="form-control"
                            value="<?php echo $default_due_date; ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Generate Invoice</button>
                </div>
            </form>
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

<script src="invoice.js"></script>
<script>
    // Enable/disable GST number input based on invoice type
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('type');
        const gstNumberInput = document.getElementById('gstNumber');

        function toggleGstNumber() {
            if (typeSelect.value === 'GST') {
                gstNumberInput.disabled = false;
                gstNumberInput.required = true;
            } else {
                gstNumberInput.disabled = true;
                gstNumberInput.required = false;
                gstNumberInput.value = '';
            }
        }

        typeSelect.addEventListener('change', toggleGstNumber);
        toggleGstNumber(); // Initial check
    });
</script>