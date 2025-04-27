<?php
// Include mock database
require_once 'database.php';

// Get data from database
$invoices = get_billing_invoices();
$quotations = get_quotations();
$credit_notes = get_credit_notes();
$sales_returns = get_sales_returns();
$transactions = get_transactions();
$customers = get_customers();
$payment_methods = get_payment_methods();

// Handle actions
$success_message = '';
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        if ($action === 'create_invoice') {
            // Form submission for new invoice
            $invoice_data = [
                'customer' => isset($_POST['customer']) ? trim($_POST['customer']) : '',
                'amount' => isset($_POST['amount']) ? trim($_POST['amount']) : '',
                'type' => isset($_POST['type']) ? trim($_POST['type']) : '',
                'payment' => isset($_POST['payment']) ? trim($_POST['payment']) : '',
                'date' => isset($_POST['date']) ? trim($_POST['date']) : '',
                'status' => isset($_POST['status']) ? trim($_POST['status']) : ''
            ];
            $result = save_billing_invoice($invoice_data);
            if ($result['success']) {
                $success_message = $result['message'];
                // Refresh invoices
                $invoices = get_billing_invoices();
            } else {
                $error_message = $result['message'];
            }
        } elseif ($action === 'print_invoice' && isset($_POST['invoice_id'])) {
            $success_message = "Printing store invoice {$_POST['invoice_id']}";
        } elseif ($action === 'view_invoice' && isset($_POST['invoice_id'])) {
            $success_message = "Viewing store invoice {$_POST['invoice_id']}";
        } elseif ($action === 'generate_receipt') {
            $success_message = 'Generating receipt';
        } elseif ($action === 'export_records') {
            $success_message = 'Exporting records';
        } elseif ($action === 'record_payment') {
            $success_message = 'Recording payment';
        }
    }
}

// Filter and search parameters
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) && in_array($_GET['status'], ['All', 'Paid', 'Pending', 'Overdue']) ? $_GET['status'] : 'All';
$type_filter = isset($_GET['type']) && in_array($_GET['type'], ['All', 'Retail', 'Wholesale']) ? $_GET['type'] : 'All';

// Filter invoices
$filtered_invoices = array_filter($invoices, function ($invoice) use ($search_query, $status_filter, $type_filter) {
    $matches_search = empty($search_query) || 
        stripos($invoice['id'], $search_query) !== false || 
        stripos($invoice['customer'], $search_query) !== false;
    $matches_status = $status_filter === 'All' || $invoice['status'] === $status_filter;
    $matches_type = $type_filter === 'All' || $invoice['type'] === $type_filter;
    return $matches_search && $matches_status && $matches_type;
});

// Statuses and types for filter
$statuses = ['All', 'Paid', 'Pending', 'Overdue'];
$types = ['All', 'Retail', 'Wholesale'];

// Default date
$today = '2025-04-27';
?>

<div class="main-content">
    <h1><i class="fas fa-file-invoice-dollar text-primary"></i> Store Billing System</h1>
    <p>Manage all store-related invoices, receipts, and payments</p>

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

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="billingTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#invoices" type="button" role="tab" aria-controls="invoices" aria-selected="true">Invoices</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="quotations-tab" data-bs-toggle="tab" data-bs-target="#quotations" type="button" role="tab" aria-controls="quotations" aria-selected="false">Quotations</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="credit-notes-tab" data-bs-toggle="tab" data-bs-target="#credit-notes" type="button" role="tab" aria-controls="credit-notes" aria-selected="false">Credit Notes</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sales-returns-tab" data-bs-toggle="tab" data-bs-target="#sales-returns" type="button" role="tab" aria-controls="sales-returns" aria-selected="false">Sales Returns</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab" aria-controls="payments" aria-selected="false">Payments</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="reports-tab" data-bs-toggle="tab" data-bs-target="#reports" type="button" role="tab" aria-controls="reports" aria-selected="false">Reports</button>
        </li>
    </ul>

    <div class="tab-content" id="billingTabContent">
        <!-- Invoices Tab -->
        <div class="tab-pane fade show active" id="invoices" role="tabpanel" aria-labelledby="invoices-tab">
            <!-- Search and Filters -->
            <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center mb-4">
                <div class="flex-grow-1">
                    <form method="GET" action="?page=billing" class="d-flex align-items-center gap-2">
                        <input type="hidden" name="page" value="billing">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                            <input
                                type="text"
                                name="search"
                                class="form-control border-start-0"
                                placeholder="Search invoices by ID or customer..."
                                value="<?php echo htmlspecialchars($search_query); ?>"
                            >
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                    </form>
                </div>
                <div class="d-flex gap-2">
                    <div>
                        <label class="form-label text-muted">Status</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($statuses as $status): ?>
                            <a href="?page=billing&status=<?php echo urlencode($status); ?>&type=<?php echo urlencode($type_filter); ?>&search=<?php echo urlencode($search_query); ?>">
                                <span class="badge <?php echo $status_filter === $status ? 'bg-primary text-white' : 'bg-light text-dark'; ?> px-3 py-1 rounded-pill">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div>
                        <label class="form-label text-muted">Type</label>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($types as $type): ?>
                            <a href="?page=billing&status=<?php echo urlencode($status_filter); ?>&type=<?php echo urlencode($type); ?>&search=<?php echo urlencode($search_query); ?>">
                                <span class="badge <?php echo $type_filter === $type ? 'bg-primary text-white' : 'bg-light text-dark'; ?> px-3 py-1 rounded-pill">
                                    <?php echo htmlspecialchars($type); ?>
                                </span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createInvoiceModal">
                    <i class="fas fa-file-invoice me-1"></i> Create Invoice
                </button>
            </div>

            <!-- Quick Actions -->
            <div class="row row-cols-1 row-cols-md-4 g-3 mb-4">
                <div class="col">
                    <button type="button" class="btn btn-primary w-100 h-100 py-4 d-flex flex-column align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createInvoiceModal">
                        <i class="fas fa-file-invoice fa-2x"></i>
                        <span>Create Store Invoice</span>
                    </button>
                </div>
                <div class="col">
                    <form method="POST" action="?page=billing">
                        <input type="hidden" name="action" value="generate_receipt">
                        <button type="submit" class="btn btn-outline-primary w-100 h-100 py-4 d-flex flex-column align-items-center gap-2">
                            <i class="fas fa-receipt fa-2x"></i>
                            <span>Generate Receipt</span>
                        </button>
                    </form>
                </div>
                <div class="col">
                    <form method="POST" action="?page=billing">
                        <input type="hidden" name="action" value="export_records">
                        <button type="submit" class="btn btn-outline-primary w-100 h-100 py-4 d-flex flex-column align-items-center gap-2">
                            <i class="fas fa-download fa-2x"></i>
                            <span>Export Records</span>
                        </button>
                    </form>
                </div>
                <div class="col">
                    <form method="POST" action="?page=billing">
                        <input type="hidden" name="action" value="record_payment">
                        <button type="submit" class="btn btn-outline-primary w-100 h-100 py-4 d-flex flex-column align-items-center gap-2">
                            <i class="fas fa-check-circle fa-2x"></i>
                            <span>Record Payment</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Store Invoices</h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary btn-sm"><i class="fas fa-sort me-1"></i> Sort</button>
                            <a href="?page=billing" class="btn btn-outline-primary btn-sm">View All</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Invoice ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Payment</th>
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
                                            <a href="?page=billing" class="btn btn-outline-primary btn-sm">Clear Filters</a>
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
                                        <td>₹<?php echo number_format($invoice['amount'], 0); ?></td>
                                        <td><?php echo htmlspecialchars($invoice['type']); ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <i class="fas <?php
                                                    echo $invoice['payment'] === 'UPI' ? 'fa-mobile text-blue' :
                                                        ($invoice['payment'] === 'Cash' ? 'fa-money-bill text-green' :
                                                        ($invoice['payment'] === 'Card' ? 'fa-credit-card text-purple' :
                                                        'fa-credit-card text-amber'));
                                                ?>"></i>
                                                <span><?php echo htmlspecialchars($invoice['payment']); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge <?php
                                                echo $invoice['status'] === 'Paid' ? 'bg-success text-white' :
                                                    ($invoice['status'] === 'Pending' ? 'bg-warning text-dark' :
                                                    'bg-danger text-white');
                                            ?>">
                                                <?php echo htmlspecialchars($invoice['status']); ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <form method="POST" action="?page=billing" class="d-inline">
                                                    <input type="hidden" name="action" value="print_invoice">
                                                    <input type="hidden" name="invoice_id" value="<?php echo htmlspecialchars($invoice['id']); ?>">
                                                    <button type="submit" class="btn btn-outline-primary btn-sm" title="Print">
                                                        <i class="fas fa-print"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" action="?page=billing" class="d-inline">
                                                    <input type="hidden" name="action" value="view_invoice">
                                                    <input type="hidden" name="invoice_id" value="<?php echo htmlspecialchars($invoice['id']); ?>">
                                                    <button type="submit" class="btn btn-outline-primary btn-sm" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quotations Tab -->
        <div class="tab-pane fade" id="quotations" role="tabpanel" aria-labelledby="quotations-tab">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5>Manage Quotations</h5>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createQuotationModal">
                    <i class="fas fa-plus me-1"></i> Create Quotation
                </button>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Quotation ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($quotations as $quotation): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($quotation['id']); ?></td>
                                    <td><?php echo htmlspecialchars($quotation['customer']); ?></td>
                                    <td>
                                        <?php
                                            $date = new DateTime($quotation['date']);
                                            echo $date->format('M j, Y');
                                        ?>
                                    </td>
                                    <td>₹<?php echo number_format($quotation['amount'], 0); ?></td>
                                    <td>
                                        <span class="badge <?php echo $quotation['status'] === 'Accepted' ? 'bg-success' : 'bg-warning'; ?> text-white">
                                            <?php echo htmlspecialchars($quotation['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Credit Notes Tab -->
        <div class="tab-pane fade" id="credit-notes" role="tabpanel" aria-labelledby="credit-notes-tab">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5>Manage Credit Notes</h5>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#issueCreditNoteModal">
                    <i class="fas fa-plus me-1"></i> Issue Credit Note
                </button>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Credit Note ID</th>
                                    <th>Invoice ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($credit_notes as $credit_note): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($credit_note['id']); ?></td>
                                    <td><?php echo htmlspecialchars($credit_note['invoice_id']); ?></td>
                                    <td><?php echo htmlspecialchars($credit_note['customer']); ?></td>
                                    <td>
                                        <?php
                                            $date = new DateTime($credit_note['date']);
                                            echo $date->format('M j, Y');
                                        ?>
                                    </td>
                                    <td>₹<?php echo number_format($credit_note['amount'], 0); ?></td>
                                    <td><?php echo htmlspecialchars($credit_note['reason']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Returns Tab -->
        <div class="tab-pane fade" id="sales-returns" role="tabpanel" aria-labelledby="sales-returns-tab">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5>Manage Sales Returns</h5>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#processReturnModal">
                    <i class="fas fa-plus me-1"></i> Process Return
                </button>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Return ID</th>
                                    <th>Invoice ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sales_returns as $return): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($return['id']); ?></td>
                                    <td><?php echo htmlspecialchars($return['invoice_id']); ?></td>
                                    <td><?php echo htmlspecialchars($return['customer']); ?></td>
                                    <td>
                                        <?php
                                            $date = new DateTime($return['date']);
                                            echo $date->format('M j, Y');
                                        ?>
                                    </td>
                                    <td>₹<?php echo number_format($return['amount'], 0); ?></td>
                                    <td><?php echo htmlspecialchars($return['reason']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments Tab -->
        <div class="tab-pane fade" id="payments" role="tabpanel" aria-labelledby="payments-tab">
            <div class="mb-4">
                <h5>Payment Methods</h5>
                <div class="row row-cols-1 row-cols-md-4 g-3">
                    <div class="col">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-credit-card text-primary me-2"></i> Payment Gateway</h6>
                                <p class="text-muted small">Accept credit/debit cards via payment gateway</p>
                                <button type="button" class="btn btn-outline-primary btn-sm">Configure</button>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-mobile text-primary me-2"></i> Digital Payment</h6>
                                <p class="text-muted small">UPI, mobile wallets and other digital options</p>
                                <button type="button" class="btn btn-outline-primary btn-sm">Configure</button>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-money-bill text-primary me-2"></i> Cash in Hand</h6>
                                <p class="text-muted small">Track cash payments and manage cash drawer</p>
                                <button type="button" class="btn btn-outline-primary btn-sm">Configure</button>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-receipt text-primary me-2"></i> Payment Reports</h6>
                                <p class="text-muted small">Generate reports on all payment methods</p>
                                <button type="button" class="btn btn-outline-primary btn-sm">View Reports</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Recent Transactions</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Invoice</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $transaction): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($transaction['id']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['invoice_id']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['customer']); ?></td>
                                    <td>₹<?php echo number_format($transaction['amount'], 0); ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1">
                                            <i class="fas <?php
                                                echo $transaction['method'] === 'UPI' ? 'fa-mobile text-blue' :
                                                    ($transaction['method'] === 'Cash' ? 'fa-money-bill text-green' :
                                                    ($transaction['method'] === 'Card' ? 'fa-credit-card text-purple' :
                                                    'fa-credit-card text-amber'));
                                            ?>"></i>
                                            <span><?php echo htmlspecialchars($transaction['method']); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                            $date = new DateTime($transaction['date']);
                                            echo $date->format('M j, Y');
                                        ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-success text-white"><?php echo htmlspecialchars($transaction['status']); ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports Tab -->
        <div class="tab-pane fade" id="reports" role="tabpanel" aria-labelledby="reports-tab">
            <div class="mb-4">
                <h5>Billing Reports</h5>
                <div class="row row-cols-1 row-cols-md-3 g-3">
                    <div class="col">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title">Sales Summary</h6>
                                <p class="text-muted small">Overview of all sales transactions</p>
                                <button type="button" class="btn btn-outline-primary btn-sm">Generate Report</button>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title">Payment Analysis</h6>
                                <p class="text-muted small">Analysis of payment methods used</p>
                                <button type="button" class="btn btn-outline-primary btn-sm">Generate Report</button>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title">Cash Flow Report</h6>
                                <p class="text-muted small">Track cash in hand and cash flow</p>
                                <button type="button" class="btn btn-outline-primary btn-sm">Generate Report</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5>Cash in Hand Report</h5>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between p-3 bg-light rounded mb-2">
                            <span class="fw-bold">Opening Balance (Today)</span>
                            <span class="fw-bold">₹15,000</span>
                        </div>
                        <div class="d-flex justify-content-between p-3 bg-light rounded mb-2">
                            <span class="fw-bold">Cash Sales</span>
                            <span class="text-success">+₹28,500</span>
                        </div>
                        <div class="d-flex justify-content-between p-3 bg-light rounded mb-2">
                            <span class="fw-bold">Cash Refunds</span>
                            <span class="text-danger">-₹3,200</span>
                        </div>
                        <div class="d-flex justify-content-between p-3 bg-light rounded mb-2">
                            <span class="fw-bold">Cash Deposits to Bank</span>
                            <span class="text-danger">-₹20,000</span>
                        </div>
                        <div class="d-flex justify-content-between p-3 bg-light rounded border border-primary">
                            <span class="fw-bold">Current Cash in Hand</span>
                            <span class="fw-bold text-lg">₹20,300</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Reminders & Collections -->
    <div class="row mt-4">
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm bg-amber-50 border-amber-200">
                <div class="card-body">
                    <h5 class="card-title text-amber-800"><i class="fas fa-exclamation-circle me-2"></i> Payment Reminders</h5>
                    <p class="text-amber-700 mb-3">3 store invoices are overdue and require immediate attention</p>
                    <button type="button" class="btn btn-outline-primary btn-sm">Send Reminders</button>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm bg-blue-50 border-blue-200">
                <div class="card-body">
                    <h5 class="card-title text-blue-800"><i class="fas fa-check-circle me-2"></i> Today's Store Collection</h5>
                    <p class="text-blue-700 mb-3">₹42,850 collected today from 5 retail customers</p>
                    <button type="button" class="btn btn-outline-primary btn-sm">View Details</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Invoice Modal -->
    <div class="modal fade" id="createInvoiceModal" tabindex="-1" aria-labelledby="createInvoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createInvoiceModalLabel">Create New Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="?page=billing">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create_invoice">
                        <div class="mb-3">
                            <label for="customer" class="form-label">Customer</label>
                            <select name="customer" id="customer" class="form-select" required>
                                <option value="">Select a customer</option>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?php echo htmlspecialchars($customer['name']); ?>">
                                        <?php echo htmlspecialchars($customer['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount (₹)</label>
                            <input type="number" name="amount" id="amount" class="form-control" min="1" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Invoice Type</label>
                            <select name="type" id="type" class="form-select" required>
                                <option value="Retail">Retail</option>
                                <option value="Wholesale">Wholesale</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="payment" class="form-label">Payment Method</label>
                            <select name="payment" id="payment" class="form-select" required>
                                <option value="">Select a payment method</option>
                                <?php foreach ($payment_methods as $method): ?>
                                    <option value="<?php echo htmlspecialchars($method['value']); ?>">
                                        <?php echo htmlspecialchars($method['label']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">Invoice Date</label>
                            <input type="date" name="date" id="date" class="form-control" value="<?php echo $today; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="Paid">Paid</option>
                                <option value="Pending">Pending</option>
                                <option value="Overdue">Overdue</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create Invoice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Placeholder Modals for Quotations, Credit Notes, Sales Returns -->
    <div class="modal fade" id="createQuotationModal" tabindex="-1" aria-labelledby="createQuotationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createQuotationModalLabel">Create New Quotation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Quotation form will be implemented here.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save Quotation</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="issueCreditNoteModal" tabindex="-1" aria-labelledby="issueCreditNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="issueCreditNoteModalLabel">Issue Credit Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Credit note form will be implemented here.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Issue Credit Note</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="processReturnModal" tabindex="-1" aria-labelledby="processReturnModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="processReturnModalLabel">Process Sales Return</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Sales return form will be implemented here.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Process Return</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-amber-50 {
    background-color: #fefae8;
}
.border-amber-200 {
    border-color: #fde68a;
}
.text-amber-700 {
    color: #b45309;
}
.text-amber-800 {
    color: #92400e;
}
.bg-blue-50 {
    background-color: #eff6ff;
}
.border-blue-200 {
    border-color: #93c5fd;
}
.text-blue-700 {
    color: #1e40af;
}
.text-blue-800 {
    color: #1e3a8a;
}
.text-blue {
    color: #0d6efd;
}
.text-green {
    color: #198754;
}
.text-purple {
    color: #6f42c1;
}
.text-amber {
    color: #f59e0b;
}
.badge {
    font-size: 0.85rem;
    padding: 4px 8px;
}
</style>