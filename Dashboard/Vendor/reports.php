<?php if ($page === 'reports'): ?>
    <div class="container-fluid">
        <h4><i class="fas fa-chart-bar text-primary"></i> Reports & Analytics</h4>
        <p>Analyze your sales, payments, and inventory performance.</p>

        <!-- Report Controls -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div class="d-flex flex-column flex-sm-row gap-2 align-items-center">
                <div class="input-group w-auto flex-grow-1" style="max-width: 300px;">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control border-start-0" id="reportsSearch"
                        placeholder="Search reports..." id="reportSearch" oninput="filterReports()">
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-outline-primary btn-sm"
                    onclick="exportTableToCSV()">
                    <i class="fas fa-download me-1"></i> Export
                </button>
                <button class="btn btn-outline-secondary me-2" data-bs-toggle="modal"
                    data-bs-target="#shareModal">Share</button>

                <!-- Share Modal -->
                <div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content text-center">
                            <div class="modal-header">
                                <h5 class="modal-title" id="shareModalLabel">Share This Report</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-2">Copy the link below to share:</p>
                                <input type="text" class="form-control mb-3" id="shareLink"
                                    value="https://yourapp.com/report/12345" readonly>
                                <button class="btn btn-primary btn-sm" onclick="copyShareLink()">Copy Link</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#reportCreatorModal">
                <i class="fas fa-file-alt me-1"></i> Create Report
            </button>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
                <div class="card-body">
                    <h6 class="text-muted d-flex align-items-center">
                        <i class="fas fa-dollar-sign me-2 text-success"></i> Total Sales
                    </h6>
                    <h3 class="fw-bold">₹9,75,000</h3>
                    <p class="text-success"><i class="fas fa-arrow-up me-1"></i> 6.5% vs last period</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
                <div class="card-body">
                    <h6 class="text-muted d-flex align-items-center">
                        <i class="fas fa-receipt me-2 text-warning"></i> Pending Payments
                    </h6>
                    <h3 class="fw-bold">₹50,000</h3>
                    <p class="text-warning"><i class="fas fa-arrow-up me-1"></i> 2% vs last period</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #0d6efd;">
                <div class="card-body">
                    <h6 class="text-muted d-flex align-items-center">
                        <i class="fas fa-chart-line me-2 text-primary"></i> Fulfillment Rate
                    </h6>
                    <h3 class="fw-bold">92%</h3>
                    <p class="text-success"><i class="fas fa-arrow-up me-1"></i> 3% vs last period</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Reports -->
    <div class="mb-4">
        <h5 class="text-muted">Quick Access Reports</h5>
        <div class="row">
            <?php
            $report_types = [
                ['name' => 'Sales by Product', 'icon' => 'fa-chart-bar', 'color' => 'primary', 'description' => 'Breakdown of sales by product'],
                ['name' => 'Payment Summary', 'icon' => 'fa-receipt', 'color' => 'indigo', 'description' => 'Status of payments received and pending'],
                ['name' => 'Stock Supplied', 'icon' => 'fa-box', 'color' => 'success', 'description' => 'Overview of inventory supplied'],
                ['name' => 'Order Fulfillment', 'icon' => 'fa-file-alt', 'color' => 'warning', 'description' => 'Rate and status of order fulfillment'],
            ];
            foreach ($report_types as $report):
                ?>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card stat-card cards shadow-sm report-card"
                        data-report-name="<?php echo htmlspecialchars($report['name']); ?>">
                        <div class="card-body d-flex align-items-start">
                            <div class="p-2 rounded bg-light me-3">
                                <i
                                    class="fas <?php echo htmlspecialchars($report['icon']); ?> text-<?php echo htmlspecialchars($report['color']); ?>"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold"><?php echo htmlspecialchars($report['name']); ?></h6>
                                <p class="text-muted small"><?php echo htmlspecialchars($report['description']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Report Tabs -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#salesTab">Sales</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#paymentsTab">Payments</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#inventoryTab">Inventory</a>
        </li>
    </ul>
    <div class="tab-content">
        <!-- Sales Tab -->
        <div class="tab-pane fade show active" id="salesTab">
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card shadow-sm cards card-border" style="border-left: 5px solid #0d6efd;">
                        <div class="card-body">
                            <h5 class="card-title">Sales Performance</h5>
                            <p class="text-muted">Monthly sales for the last 6 months</p>
                            <div class="chart-box">
                                <canvas id="salesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm cards card-border" style="border-left: 5px solid #198754;">
                        <div class="card-body">
                            <h5 class="card-title">Sales by Product</h5>
                            <div class="chart-box">
                                <canvas id="productsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm cards card-border" style="border-left: 5px solid #ffc107;">
                        <div class="card-body">
                            <h5 class="card-title">Payment Status</h5>
                            <div class="chart-box">
                                <canvas id="paymentStatusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card shadow-sm cards card-border" style="border-left: 5px solid #dc3545;">
                        <div class="card-body">
                            <h5 class="card-title">Recent Sales Reports</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="reportsTable">
                                    <thead>
                                        <tr>
                                            <th>Report Name</th>
                                            <th>Created On</th>
                                            <th>Type</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $recent_reports = [
                                            ['name' => 'Monthly Sales Summary', 'date' => '10 Apr, 2025', 'type' => 'Sales'],
                                            ['name' => 'Product Performance', 'date' => '05 Apr, 2025', 'type' => 'Sales'],
                                        ];
                                        foreach ($recent_reports as $report):
                                            ?>
                                            <tr class="report-row"
                                                data-report-name="<?php echo htmlspecialchars($report['name']); ?>">
                                                <td><?php echo htmlspecialchars($report['name']); ?></td>
                                                <td><?php echo htmlspecialchars($report['date']); ?></td>
                                                <td><?php echo htmlspecialchars($report['type']); ?></td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <button class="btn btn-outline-primary btn-sm"
                                                            onclick="viewReport('<?php echo htmlspecialchars($report['name']); ?>')">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-outline-primary btn-sm"
                                                            onclick="exportReport('Excel', '<?php echo htmlspecialchars($report['name']); ?>')">
                                                            <i class="fas fa-download"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <script>
                                    // Search Functionality
                                    document.getElementById('reportsSearch').addEventListener('input', function () {
                                        const searchText = this.value.toLowerCase();
                                        const rows = document.querySelectorAll('#reportsTable tbody tr');

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
                </div>
            </div>
        </div>
        <!-- Payments Tab -->
        <div class="tab-pane fade" id="paymentsTab">
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card shadow-sm cards card-border" style="border-left: 5px solid #6f42c1;">
                        <div class="card-body">
                            <h5 class="card-title">Payment Reports</h5>
                            <div class="row">
                                <?php
                                $payment_reports = [
                                    ['name' => 'Payment Summary', 'icon' => 'fa-receipt', 'color' => 'primary'],
                                    ['name' => 'Pending Payments', 'icon' => 'fa-file-alt', 'color' => 'warning'],
                                    ['name' => 'Commission Report', 'icon' => 'fa-dollar-sign', 'color' => 'success'],
                                ];
                                foreach ($payment_reports as $report):
                                    ?>
                                    <div class="col-md-4 col-sm-6 mb-4">
                                        <div class="card stat-card cards shadow-sm report-card"
                                            data-report-name="<?php echo htmlspecialchars($report['name']); ?>">
                                            <div class="card-body d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="p-2 rounded bg-light me-3">
                                                        <i
                                                            class="fas <?php echo htmlspecialchars($report['icon']); ?> text-<?php echo htmlspecialchars($report['color']); ?>"></i>
                                                    </div>
                                                    <h6 class="fw-bold"><?php echo htmlspecialchars($report['name']); ?></h6>
                                                </div>
                                                <i class="fas fa-download text-muted"></i>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Inventory Tab -->
        <div class="tab-pane fade" id="inventoryTab">
            <div class="row">
                <div class="col-md-6 col-sm-6 mb-4">
                    <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
                        <div class="card-body">
                            <h6 class="text-muted">Stock Supplied</h6>
                            <h3 class="fw-bold">₹5,20,000</h3>
                            <p class="text-success"><i class="fas fa-arrow-up me-1"></i> 4% vs last period</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 mb-4">
                    <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #ffc107;">
                        <div class="card-body">
                            <h6 class="text-muted">Low Stock Alerts</h6>
                            <h3 class="fw-bold">8</h3>
                            <p class="text-warning"><i class="fas fa-arrow-up me-1"></i> +2 vs last period</p>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card shadow-sm cards card-border" style="border-left: 5px solid #0d6efd;">
                        <div class="card-body">
                            <h5 class="card-title">Inventory Reports</h5>
                            <div class="row">
                                <?php
                                $inventory_reports = [
                                    ['name' => 'Stock Supplied', 'icon' => 'fa-box', 'color' => 'primary'],
                                    ['name' => 'Stock Returns', 'icon' => 'fa-file-alt', 'color' => 'danger'],
                                ];
                                foreach ($inventory_reports as $report):
                                    ?>
                                    <div class="col-md-4 col-sm-6 mb-4">
                                        <div class="card stat-card cards shadow-sm report-card"
                                            data-report-name="<?php echo htmlspecialchars($report['name']); ?>">
                                            <div class="card-body d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="p-2 rounded bg-light me-3">
                                                        <i
                                                            class="fas <?php echo htmlspecialchars($report['icon']); ?> text-<?php echo htmlspecialchars($report['color']); ?>"></i>
                                                    </div>
                                                    <h6 class="fw-bold"><?php echo htmlspecialchars($report['name']); ?></h6>
                                                </div>
                                                <i class="fas fa-download text-muted"></i>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <!-- Report Creator Modal -->
    <div class="modal fade" id="reportCreatorModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reportCreatorForm">
                        <div class="mb-3">
                            <label for="reportType" class="form-label">Report Type</label>
                            <select class="form-select" id="reportType" required>
                                <option value="">Select a report type</option>
                                <option value="salesSummary">Sales Summary</option>
                                <option value="paymentSummary">Payment Summary</option>
                                <option value="stockSummary">Stock Summary</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="reportPeriod" class="form-label">Period</label>
                            <select class="form-select" id="reportPeriod" required>
                                <option value="thismonth">This Month</option>
                                <option value="lastmonth">Last Month</option>
                                <option value="last6months">Last 6 Months</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="generateReport()">Generate Report</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Viewer Modal -->
    <div class="modal fade" id="reportViewerModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportViewerTitle">Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="reportViewerContent">Viewing report placeholder content...</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-primary btn-sm" onclick="exportReport('Excel')">
                        <i class="fas fa-file-excel me-1"></i> Download Excel
                    </button>
                    <button class="btn btn-outline-primary btn-sm" onclick="exportReport('PDF')">
                        <i class="fas fa-file-pdf me-1"></i> Download PDF
                    </button>
                    <button class="btn btn-outline-primary btn-sm" onclick="alert('Printing report...')">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    </div>

    <style>
        .report-card:hover {
            cursor: pointer;
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.1);
        }

        .chart-box canvas {
            max-height: 300px;
        }
    </style>

    <script>
        const reportsData = <?php echo json_encode(get_reports_data()); ?>;

        function initCharts() {
            // Sales Chart
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: reportsData.sales.monthly.labels,
                    datasets: [{
                        label: 'Sales (₹)',
                        data: reportsData.sales.monthly.data,
                        borderColor: '#0d6efd',
                        backgroundColor: '#0d6efd',
                        tension: 0.3,
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return '₹' + context.parsed.y.toLocaleString('en-IN');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return '₹' + value.toLocaleString('en-IN');
                                }
                            }
                        }
                    }
                }
            });

            // Products Chart
            const productsCtx = document.getElementById('productsChart').getContext('2d');
            new Chart(productsCtx, {
                type: 'pie',
                data: {
                    labels: reportsData.sales.by_product.labels,
                    datasets: [{
                        data: reportsData.sales.by_product.data,
                        backgroundColor: reportsData.sales.by_product.colors
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return '₹' + context.parsed.toLocaleString('en-IN');
                                }
                            }
                        }
                    }
                }
            });

            // Payment Status Chart
            const paymentStatusCtx = document.getElementById('paymentStatusChart').getContext('2d');
            new Chart(paymentStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: reportsData.payments.summary.labels,
                    datasets: [{
                        data: reportsData.payments.summary.data,
                        backgroundColor: reportsData.payments.summary.colors
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return '₹' + context.parsed.toLocaleString('en-IN');
                                }
                            }
                        }
                    }
                }
            });
        }

        function updateCharts() {
            const period = document.getElementById('periodSelect').value;
            fetch(`api/reports.php?period=${period}`)
                .then(response => response.json())
                .then(data => {
                    // Mock response for now
                    alert(`Updated charts for period: ${period}`);
                })
                .catch(error => console.error('Error:', error));
        }

        function generateReport() {
            const reportType = document.getElementById('reportType').value;
            const period = document.getElementById('reportPeriod').value;

            // Get relevant data based on report type
            const reportData = reportsData[reportType === 'salesSummary' ? 'sales' :
                reportType === 'paymentSummary' ? 'payments' : 'inventory'];

            const reportContent = document.getElementById('reportViewerContent');
            reportContent.innerHTML = `
        <div class="report-summary">
            <h6 class="mb-3">Report Summary for ${period}</h6>
            ${getReportSummaryHTML(reportType, reportData)}
        </div>
    `;

            const modal = bootstrap.Modal.getInstance(document.getElementById('reportCreatorModal'));
            modal.hide();

            const viewerModal = new bootstrap.Modal(document.getElementById('reportViewerModal'));
            viewerModal.show();
        }

        function getReportSummaryHTML(type, data) {
            switch (type) {
                case 'salesSummary':
                    return `
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Total Sales</h6>
                                <p class="h4">₹${data.monthly.total.toLocaleString('en-IN')}</p>
                                <small class="text-success">
                                    <i class="fas fa-arrow-up"></i> ${data.monthly.growth}% vs last period
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            `;

                case 'paymentSummary':
                    return `
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Received Payments</h6>
                                <p class="h4">₹${data.summary.data[0].toLocaleString('en-IN')}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;

                default:
                    return `
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Stock Value</h6>
                                <p class="h4">₹${data.stock_value.total.toLocaleString('en-IN')}</p>
                                <small class="text-success">
                                    <i class="fas fa-arrow-up"></i> ${data.stock_value.growth}% vs last period
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            }
        }

        // Initialize charts when document is ready
        document.addEventListener('DOMContentLoaded', initCharts);
    </script>
<?php endif; ?>