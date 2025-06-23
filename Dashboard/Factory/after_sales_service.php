<?php
// after_sales_service.php


require_once '../../_conn.php';

// Initialize variables to track form submission status
$show_success_popup = false;
$success_message = '';

// Check if the Service Ticket form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_ticket'])) {
    // Retrieve and sanitize form data
    $customer = mysqli_real_escape_string($conn, $_POST['customer'] ?? '');
    $date = mysqli_real_escape_string($conn, $_POST['date'] ?? '');
    $issue = mysqli_real_escape_string($conn, $_POST['issue'] ?? '');
    $product = mysqli_real_escape_string($conn, $_POST['product'] ?? '');
    $priority = mysqli_real_escape_string($conn, $_POST['priority'] ?? '');
    $status = mysqli_real_escape_string($conn, $_POST['status'] ?? '');

    // Basic validation
    if (empty($customer) || empty($date) || empty($issue) || empty($product) || empty($priority) || empty($status)) {
        $error_message = 'All fields are required.';
    } else {
        // Prepare SQL statement with prepared statements for security
        $sql = "INSERT INTO tickets (customer, date, issue_description, product, priority, status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            // Bind parameters
            $stmt->bind_param("ssssss", $customer, $date, $issue, $product, $priority, $status);

            // Execute the statement
            if ($stmt->execute()) {
                $show_success_popup = true;
                $success_message = 'Ticket created successfully.';
            } else {
                $error_message = 'Error: ' . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            $error_message = 'Error preparing statement: ' . $conn->error;
        }
    }
}

// Check if the Edit Ticket form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_ticket'])) {
    $ticket_id = mysqli_real_escape_string($conn, $_POST['ticket_id'] ?? '');
    $priority = mysqli_real_escape_string($conn, $_POST['priority'] ?? '');
    $status = mysqli_real_escape_string($conn, $_POST['status'] ?? '');

    // Basic validation
    if (empty($ticket_id) || empty($priority) || empty($status)) {
        $error_message = 'All fields are required.';
    } else {
        // Prepare SQL statement for updating ticket
        $sql = "UPDATE tickets SET priority = ?, status = ?, updated_at = CURRENT_TIMESTAMP WHERE ticket_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            // Bind parameters
            $stmt->bind_param("ssi", $priority, $status, $ticket_id);

            // Execute the statement
            if ($stmt->execute()) {
                $show_success_popup = true;
                $success_message = 'Ticket updated successfully.';
            } else {
                $error_message = 'Error: ' . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            $error_message = 'Error preparing statement: ' . $conn->error;
        }
    }
}

// Card 1: Open Tickets
$open_tickets_query = "SELECT COUNT(*) as count FROM tickets WHERE status = 'Open'";
$open_tickets_result = $conn->query($open_tickets_query);
$open_tickets = $open_tickets_result ? ($open_tickets_result->fetch_assoc()['count'] ?? 0) : 0;
$open_tickets_result->free();

// Open Tickets comparison (last month)
$open_tickets_last_month_query = "SELECT COUNT(*) as count FROM tickets WHERE status = 'Open' AND created_at < CURDATE() - INTERVAL 30 DAY";
$open_tickets_last_month_result = $conn->query($open_tickets_last_month_query);
$open_tickets_last_month = $open_tickets_last_month_result ? ($open_tickets_last_month_result->fetch_assoc()['count'] ?? 0) : 0;
$open_tickets_last_month_result->free();
$open_tickets_diff = $open_tickets - $open_tickets_last_month;
$open_tickets_diff_text = $open_tickets_diff >= 0 ? "+$open_tickets_diff" : "$open_tickets_diff";
$open_tickets_diff_class = $open_tickets_diff >= 0 ? 'text-danger' : 'text-success';

// Card 2: Average Resolution Time
$avg_resolution_query = "SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours FROM tickets WHERE status IN ('Resolved', 'Closed')";
$avg_resolution_result = $conn->query($avg_resolution_query);
$avg_resolution_hours = $avg_resolution_result ? (round($avg_resolution_result->fetch_assoc()['avg_hours'] ?? 0, 1)) : 0;
$avg_resolution_result->free();

// Avg Resolution Time comparison (last month)
$avg_resolution_last_month_query = "SELECT AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours FROM tickets WHERE status IN ('Resolved', 'Closed') AND updated_at < CURDATE() - INTERVAL 30 DAY";
$avg_resolution_last_month_result = $conn->query($avg_resolution_last_month_query);
$avg_resolution_last_month = $avg_resolution_last_month_result ? (round($avg_resolution_last_month_result->fetch_assoc()['avg_hours'] ?? 0, 1)) : 0;
$avg_resolution_last_month_result->free();
$avg_resolution_diff = $avg_resolution_hours - $avg_resolution_last_month;
$avg_resolution_diff_text = $avg_resolution_diff >= 0 ? "+{$avg_resolution_diff}h" : "{$avg_resolution_diff}h";
$avg_resolution_diff_class = $avg_resolution_diff >= 0 ? 'text-danger' : 'text-success';

// Card 3: Resolved This Week
$resolved_this_week_query = "SELECT COUNT(*) as count FROM tickets WHERE status IN ('Resolved', 'Closed') AND updated_at >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)";
$resolved_this_week_result = $conn->query($resolved_this_week_query);
$resolved_this_week = $resolved_this_week_result ? ($resolved_this_week_result->fetch_assoc()['count'] ?? 0) : 0;
$resolved_this_week_result->free();

// Resolved This Week comparison (last week)
$resolved_last_week_query = "SELECT COUNT(*) as count FROM tickets WHERE status IN ('Resolved', 'Closed') AND updated_at >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) + 7 DAY) AND updated_at < DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)";
$resolved_last_week_result = $conn->query($resolved_last_week_query);
$resolved_last_week = $resolved_last_week_result ? ($resolved_last_week_result->fetch_assoc()['count'] ?? 0) : 0;
$resolved_last_week_result->free();
$resolved_diff_percent = $resolved_last_week > 0 ? round(($resolved_this_week - $resolved_last_week) / $resolved_last_week * 100, 1) : ($resolved_this_week > 0 ? 100 : 0);
$resolved_diff_text = $resolved_diff_percent >= 0 ? "+{$resolved_diff_percent}%" : "{$resolved_diff_percent}%";
$resolved_diff_class = $resolved_diff_percent >= 0 ? 'text-success' : 'text-danger';

// Card 4: Customer Satisfaction
$satisfaction_query = "SELECT (COUNT(CASE WHEN TIMESTAMPDIFF(HOUR, created_at, updated_at) <= 24 THEN 1 END) / COUNT(*)) * 100 as satisfaction FROM tickets WHERE status IN ('Resolved', 'Closed')";
$satisfaction_result = $conn->query($satisfaction_query);
$satisfaction_percent = $satisfaction_result ? (round($satisfaction_result->fetch_assoc()['satisfaction'] ?? 0, 1)) : 0;
$satisfaction_result->free();

// Customer Satisfaction comparison (last month)
$satisfaction_last_month_query = "SELECT (COUNT(CASE WHEN TIMESTAMPDIFF(HOUR, created_at, updated_at) <= 24 THEN 1 END) / COUNT(*)) * 100 as satisfaction FROM tickets WHERE status IN ('Resolved', 'Closed') AND updated_at < CURDATE() - INTERVAL 30 DAY";
$satisfaction_last_month_result = $conn->query($satisfaction_last_month_query);
$satisfaction_last_month = $satisfaction_last_month_result ? (round($satisfaction_last_month_result->fetch_assoc()['satisfaction'] ?? 0, 1)) : 0;
$satisfaction_last_month_result->free();
$satisfaction_diff = $satisfaction_percent - $satisfaction_last_month;
$satisfaction_diff_text = $satisfaction_diff >= 0 ? "+{$satisfaction_diff}%" : "{$satisfaction_diff}%";
$satisfaction_diff_class = $satisfaction_diff >= 0 ? 'text-success' : 'text-danger';

// Fetch ticket counts by status for pie chart
$pie_query = "SELECT status, COUNT(*) as count FROM tickets GROUP BY status";
$pie_result = $conn->query($pie_query);
$pie_data = [
    'Open' => 0,
    'In Progress' => 0,
    'Resolved' => 0,
    'Closed' => 0
];
if ($pie_result) {
    while ($row = $pie_result->fetch_assoc()) {
        if (isset($pie_data[$row['status']])) {
            $pie_data[$row['status']] = (int) $row['count'];
        }
    }
    $pie_result->free();
}

// Fetch ticket counts by date for line chart (last 7 days)
$line_query = "SELECT DATE(created_at) as ticket_date, COUNT(*) as count FROM tickets WHERE created_at >= CURDATE() - INTERVAL 7 DAY GROUP BY ticket_date";
$line_result = $conn->query($line_query);
$line_data = [];
$line_labels = [];
// Initialize 7 days of data
for ($i = 0; $i < 7; $i++) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $line_data[$date] = 0;
    $line_labels[$date] = date('M d', strtotime($date));
}
if ($line_result) {
    while ($row = $line_result->fetch_assoc()) {
        $line_data[$row['ticket_date']] = (int) $row['count'];
    }
    $line_result->free();
}
// Sort labels and data in reverse chronological order
krsort($line_data);
krsort($line_labels);
$line_data = array_values($line_data);
$line_labels = array_values($line_labels);

// Fetch all tickets from the database
$ticket_query = "SELECT ticket_id, customer, date, issue_description, product, priority, status FROM tickets ORDER BY ticket_id DESC";
$ticket_result = $conn->query($ticket_query);
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
<!-- JavaScript to trigger the success popup and handle edit modal -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php if ($show_success_popup): ?>
            var successModal = new bootstrap.Modal(document.getElementById('successPopup'));
            successModal.show();
        <?php endif; ?>

        // Handle Edit button click to populate modal
        document.querySelectorAll('.edit-ticket-btn').forEach(button => {
            button.addEventListener('click', function () {
                const ticketId = this.dataset.ticketId;
                const priority = this.dataset.priority;
                const status = this.dataset.status;

                document.getElementById('editTicketId').value = ticketId;
                document.getElementById('editPriority').value = priority;
                document.getElementById('editStatus').value = status;
            });
        });
    });
</script>

<!-- Error Message (if any) -->
<?php if (isset($error_message)): ?>
    <div class="alert alert-danger" role="alert">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
<?php endif; ?>

<h1>After-Sales Service</h1>
<p>Manage customer service and support tickets</p>

<!-- Cards -->
<div class="row">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
            <div class="card-body">
                <h6 class="text-muted">Open Tickets</h6>
                <h3 class="fw-bold"><?php echo htmlspecialchars($open_tickets); ?></h3>
                <p class="<?php echo $open_tickets_diff_class; ?>">
                    <?php echo htmlspecialchars($open_tickets_diff_text); ?> vs last month</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
            <div class="card-body">
                <h6 class="text-muted">Avg. Resolution Time</h6>
                <h3 class="fw-bold"><?php echo htmlspecialchars($avg_resolution_hours); ?> hours</h3>
                <p class="<?php echo $avg_resolution_diff_class; ?>">
                    <?php echo htmlspecialchars($avg_resolution_diff_text); ?> vs last month</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
            <div class="card-body">
                <h6 class="text-muted">Resolved This Week</h6>
                <h3 class="fw-bold"><?php echo htmlspecialchars($resolved_this_week); ?></h3>
                <p class="<?php echo $resolved_diff_class; ?>"><?php echo htmlspecialchars($resolved_diff_text); ?> vs
                    last week</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #6f42c1;">
            <div class="card-body">
                <h6 class="text-muted">Customer Satisfaction</h6>
                <h3 class="fw-bold"><?php echo htmlspecialchars($satisfaction_percent); ?>%</h3>
                <p class="<?php echo $satisfaction_diff_class; ?>">
                    <?php echo htmlspecialchars($satisfaction_diff_text); ?> vs last month</p>
            </div>
        </div>
    </div>
</div>

<!-- Buttons
        <div class="row justify-content-center">
            <div class="col-md-3 col-sm-6 mb-4">
                <button type="button" class="btn btn-outline-primary btn-lg w-100" data-bs-toggle="modal" data-bs-target="#serviceTicket"><i class="fa-regular fa-calendar-check"></i>
                    Create Service Ticket</button>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <button type="button" class="btn btn-outline-primary btn-lg w-100" data-bs-toggle="modal" data-bs-target="#asignTechnician"><i class="fa-solid fa-user-plus"></i> Assign
                    Technician</button>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-wrench"></i> Manage
                    Parts</button>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <button type="button" class="btn btn-outline-primary btn-lg w-100"><i class="fa-solid fa-file-lines"></i>
                    Service Reports</button>
            </div>
        </div> -->

<!-- Service Ticket Form -->
<div class="modal fade" id="serviceTicket" tabindex="-1" aria-labelledby="serviceTicketLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post">
                <input type="hidden" name="create_ticket" value="1">
                <div class="modal-header">
                    <h5 class="modal-title" id="serviceTicketLabel">Service Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="customer" class="form-label">Customer</label>
                        <input type="text" class="form-control" id="customer" name="customer" required>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="issue" class="form-label">Issue</label>
                        <input type="text" class="form-control" id="issue" name="issue" required>
                    </div>
                    <div class="mb-3">
                        <label for="product" class="form-label">Product</label>
                        <input type="text" class="form-control" id="product" name="product" required>
                    </div>
                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority</label>
                        <select class="form-control" id="priority" name="priority" required>
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="Open">Open</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Resolved">Resolved</option>
                            <option value="Closed">Closed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Ticket Form -->
<div class="modal fade" id="editTicket" tabindex="-1" aria-labelledby="editTicketLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post">
                <input type="hidden" name="edit_ticket" value="1">
                <input type="hidden" name="ticket_id" id="editTicketId">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTicketLabel">Edit Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editPriority" class="form-label">Priority</label>
                        <select class="form-control" id="editPriority" name="priority" required>
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editStatus" class="form-label">Status</label>
                        <select class="form-control" id="editStatus" name="status" required>
                            <option value="Open">Open</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Resolved">Resolved</option>
                            <option value="Closed">Closed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Success Popup Modal -->
<div class="modal fade" id="successPopup" tabindex="-1" aria-labelledby="successPopupLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successPopupLabel">Success</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php echo htmlspecialchars($success_message ?: 'Operation successful.'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="chart-container">
    <div class="chart-box">
        <h3>Tickets by Status</h3>
        <div style="position: relative; width: 100%; max-width: 300px; margin: 0 auto;">
            <canvas id="pieChart"></canvas>
        </div>
    </div>
    <div class="chart-box">
        <h3>Tickets Created (Last 7 Days)</h3>
        <canvas id="lineChart"></canvas>
    </div>
</div>

<!-- Tickets Table -->
<div class="col-md-12 card p-3 shadow-sm my-4 table-responsive">
    <div id="allTickets">
        <h3>Tickets</h3>
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="d-flex justify-content-start">
                <div class="input-group w-100 me-2">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Search..." />
                </div>
                <button class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#serviceTicket"><i
                        class="fa-solid fa-plus"></i> New Ticket</button>
            </div>
        </div>
        <table id="supplyTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Ticket ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Issue</th>
                    <th>Product</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($ticket_result && $ticket_result->num_rows > 0): ?>
                    <?php while ($ticket = $ticket_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars('SRV-' . str_pad($ticket['ticket_id'], 3, '0', STR_PAD_LEFT)); ?>
                            </td>
                            <td><?php echo htmlspecialchars($ticket['customer']); ?></td>
                            <td><?php echo htmlspecialchars(date('d M, Y', strtotime($ticket['date']))); ?></td>
                            <td><?php echo htmlspecialchars($ticket['issue_description']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['product']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['priority']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['status']); ?></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary btn-sm edit-ticket-btn" data-bs-toggle="modal"
                                        data-bs-target="#editTicket"
                                        data-ticket-id="<?php echo htmlspecialchars($ticket['ticket_id']); ?>"
                                        data-priority="<?php echo htmlspecialchars($ticket['priority']); ?>"
                                        data-status="<?php echo htmlspecialchars($ticket['status']); ?>">
                                        <i class="fa-solid fa-edit"></i> Edit
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No tickets found.</td>
                    </tr>
                <?php endif; ?>
                <?php $ticket_result->free(); ?>
            </tbody>
        </table>
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
        </script>
    </div>
</div>

<script>
    // Pie Chart
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: ['Open', 'In Progress', 'Resolved', 'Closed'],
            datasets: [{
                data: [<?php echo $pie_data['Open']; ?>, <?php echo $pie_data['In Progress']; ?>, <?php echo $pie_data['Resolved']; ?>, <?php echo $pie_data['Closed']; ?>],
                backgroundColor: [
                    '#0d6efd',  // Blue (Open)
                    '#20c997',  // Green (In Progress)
                    '#ffc107',  // Orange (Resolved)
                    '#fd7e14',  // Orange-dark (Closed)
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#333',
                        font: { size: 14 }
                    }
                }
            }
        }
    });

    // Line Chart
    const lineCtx = document.getElementById('lineChart').getContext('2d');
    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($line_labels); ?>,
            datasets: [{
                label: 'Tickets',
                data: <?php echo json_encode($line_data); ?>,
                fill: false,
                borderColor: '#0d6efd',
                backgroundColor: '#0d6efd',
                tension: 0.3,
                pointRadius: 5,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    },
                    title: {
                        display: true,
                        text: 'Number of Tickets'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                }
            }
        }
    });
</script>