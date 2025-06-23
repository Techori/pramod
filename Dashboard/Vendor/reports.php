<?php if ($page === 'reports'): ?>
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include '../../_conn.php';
    $user_name = $_SESSION['user_name'];
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <div id="pdf-content">

        <div class="container-fluid">
            <h4><i class="fas fa-chart-bar text-primary"></i> Reports & Analytics</h4>
            <p>Analyze your sales, payments, and inventory performance.</p>

            <!-- Report Controls -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-outline-primary btn-sm" onclick="exportToPDF()">
                        <i class="fas fa-download me-1"></i> Export
                    </button>
                </div>
            </div>
        </div>

        <script>
            function exportToPDF() {
                const element = document.getElementById('pdf-content');
                const opt = {
                    margin: 0.5,
                    filename: 'Vendor Report.pdf',
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2 },
                    jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
                };

                html2pdf().set(opt).from(element).save();
            }
        </script>

        <?php

        // --- 1. Total Revenue (last 30 days or total?)
        $revenueQuery = $conn->query("SELECT SUM(grand_total) AS total_revenue FROM invoice WHERE date >= CURDATE() - INTERVAL 30 DAY AND created_for = '$user_name'");
        $revenueRow = $revenueQuery->fetch_assoc();
        $totalRevenue = $revenueRow['total_revenue'] ?? 0;

        // Optional: Compare with previous month
        $prevMonthRevenueQuery = $conn->query("SELECT SUM(grand_total) AS prev_revenue FROM invoice WHERE date BETWEEN DATE_SUB(CURDATE(), INTERVAL 60 DAY) AND DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND created_for = '$user_name'");
        $prevRevenueRow = $prevMonthRevenueQuery->fetch_assoc();
        $prevRevenue = $prevRevenueRow['prev_revenue'] ?? 0;

        // Calculate revenue growth
        $revenueGrowth = 0;
        if ($prevRevenue > 0) {
            $revenueGrowth = (($totalRevenue - $prevRevenue) / $prevRevenue) * 100;
        }

        // --- 2. Pending Payments
        $pendingPaymentsQuery = $conn->query("SELECT SUM(grand_total) AS pending_total FROM invoice WHERE status = 'Pending' AND created_for = '$user_name'");
        $pendingPaymentsRow = $pendingPaymentsQuery->fetch_assoc();
        $pendingPayments = $pendingPaymentsRow['pending_total'] ?? 0;

        // --- 3. Pending Deliveries
        $pendingDeliveriesQuery = $conn->query("SELECT COUNT(*) AS pending_count FROM retail_store_stock_request WHERE status = 'Ordered' AND requested_by = '$user_name'");
        $pendingDeliveriesRow = $pendingDeliveriesQuery->fetch_assoc();
        $pendingDeliveries = $pendingDeliveriesRow['pending_count'] ?? 0;
        ?>


        <!-- Key Metrics -->
        <div class="row mb-4">
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card stat-card cards card-border shadow-sm" style="border-left: 5px solid #198754;">
                    <div class="card-body">
                        <h6 class="text-muted d-flex align-items-center"> Total Revenue
                        </h6>
                        <h3 class="fw-bold">₹<?php echo number_format($totalRevenue); ?></h3>
                        <p class="text-success">+ <?php echo round($revenueGrowth, 1); ?>% vs last month</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card stat-card cards card-border shadow-sm h-100" style="border-left: 5px solid #ffc107;">
                    <div class="card-body">
                        <h6 class="text-muted d-flex align-items-center"> Pending Payments
                        </h6>
                        <h3 class="fw-bold">₹<?php echo number_format($pendingPayments); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card stat-card cards card-border shadow-sm h-100" style="border-left: 5px solid #0d6efd;">
                    <div class="card-body">
                        <h6 class="text-muted d-flex align-items-center"> Pending Deliveries
                        </h6>
                        <h3 class="fw-bold"><?php echo $pendingDeliveries; ?> deliveries</h3>
                    </div>
                </div>
            </div>
        </div>

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
                            <h5 class="card-title">Stock</h5>
                            <div class="chart-box">
                                <canvas id="productsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm cards card-border" style="border-left: 5px solid #ffc107;">
                        <div class="card-body">
                            <h5 class="card-title">Sales by Payment Method</h5>
                            <div class="chart-box">
                                <canvas id="paymentStatusChart"></canvas>
                            </div>
                        </div>
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

    <!-- Line chart -->
    <?php

    $monthLabels = [];
    $monthlyTotals = [];

    // Get today's date and loop back 6 months
    for ($i = 5; $i >= 0; $i--) {
        $date = new DateTime();
        $date->modify("-$i months");
        $month = $date->format('m');
        $year = $date->format('Y');
        $label = $date->format('M'); // e.g., Jan, Feb

        // Add to labels
        $monthLabels[] = "'$label'";

        // Fetch sum for this month
        $stmt = $conn->prepare("SELECT SUM(grand_total) as total FROM invoice WHERE MONTH(date) = ? AND YEAR(date) = ? AND created_for = '$user_name'");
        $stmt->bind_param("ii", $month, $year);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $total = $result['total'] ?: 0;
        $monthlyTotals[] = $total;
    }

    // Output comma-separated values
    $labelsStr = implode(", ", $monthLabels);
    $totalsStr = implode(", ", $monthlyTotals);
    ?>

    <!-- Stock ckart -->
    <?php

    // Step 1: Fetch distinct categories
    $category_query = $conn->prepare("SELECT DISTINCT category FROM vendor_product WHERE product_of = ?");
    $category_query->bind_param("s", $user_name);
    $category_query->execute();
    $category_result = $category_query->get_result();

    $categoryLabels = [];
    $stockCounts = [];

    while ($row = $category_result->fetch_assoc()) {
        $category = $row['category'];
        $categoryLabels[] = $category;

        // Step 2: Fetch total stock for each category
        $stock_query = $conn->prepare("SELECT SUM(stock) as total FROM vendor_product WHERE product_of = ? AND category = ?");
        $stock_query->bind_param("ss", $user_name, $category);
        $stock_query->execute();
        $stock_result = $stock_query->get_result()->fetch_assoc();

        $stockCounts[] = (int) ($stock_result['total'] ?? 0);
    }
    ?>

    <!-- Payments methods -->
    <?php
    // Pie Chart: Get payment method counts
    $paymentLabels = [];
    $paymentCounts = [];

    $query = "SELECT payment_method, COUNT(*) AS total FROM invoice WHERE created_for = '$user_name' GROUP BY payment_method";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        $paymentLabels[] = $row['payment_method'];
        $paymentCounts[] = $row['total'];
    }
    ?>


    <script>

        // Sales Chart

        const lineCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: [<?= $labelsStr ?>],
                datasets: [{
                    label: 'Revenue',
                    data: [<?= $totalsStr ?>],
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
                            stepSize: 150000
                        }
                    }
                }
            }
        });

        // Product Chart (Pie)
        const productChartCtx = document.getElementById('productsChart').getContext('2d');
        new Chart(productChartCtx, {
            type: 'pie',
            data: {
                labels: <?= json_encode($categoryLabels) ?>,
                datasets: [{
                    data: <?= json_encode($stockCounts) ?>,
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#6f42c1']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Payment Status Chart
        const pieCtx = document.getElementById('paymentStatusChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($paymentLabels); ?>,
                datasets: [{
                    data: <?php echo json_encode($paymentCounts); ?>,
                    backgroundColor: [
                        '#0d6efd',
                        '#20c997',
                        '#ffc107',
                        '#fd7e14'
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



    </script>
<?php endif; ?>