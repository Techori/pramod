<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Dashboard - Shree Unnati Wires & Traders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="../../public/unnati_logo.png" alt="Logo" class="img-fluid" style="width: auto; height: auto;">
            <h6 class="mb-0">Unnati Factory Portal</h6>
            <small class="text-muted" style="font-size: 0.8rem;">Manage your business</small>
        </div>
        <nav class="nav flex-column mt-2">
            <a href="?page=dashboard" class="nav-link <?php echo $page === 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="?page=production" class="nav-link <?php echo $page === 'production' ? 'active' : ''; ?>"><i class="fa-solid fa-industry"></i> Production</a>
            <a href="?page=billing_system" class="nav-link <?php echo $page === 'billing_system' ? 'active' : ''; ?>"><i class="fas fa-file-invoice"></i> Billing System</a>
            <a href="?page=supply_management" class="nav-link <?php echo $page === 'supply_management' ? 'active' : ''; ?>"><i class="fa-solid fa-clipboard-list"></i> Supply Management</a>
            <a href="?page=raw_materials" class="nav-link <?php echo $page === 'raw_materials' ? 'active' : ''; ?>"><i class="fa-solid fa-cube"></i> Raw Materials</a>
            <a href="?page=inventory" class="nav-link <?php echo $page === 'inventory' ? 'active' : ''; ?>"><i class="fa-solid fa-box"></i> Inventory</a>
            <a href="?page=workers" class="nav-link <?php echo $page === 'workers' ? 'active' : ''; ?>"><i class="fa-regular fa-user"></i> Workers</a>
            <a href="?page=expenses" class="nav-link <?php echo $page === 'expenses' ? 'active' : ''; ?>"><i class="fa-solid fa-sack-dollar"></i> Expenses</a>
            <a href="?page=after_sales_service" class="nav-link <?php echo $page === 'after_sales_service' ? 'active' : ''; ?>"><i class="fas fa-headset"></i> After-Sales Service</a>
            <a href="?page=reports" class="nav-link <?php echo $page === 'reports' ? 'active' : ''; ?>"><i class="fa-solid fa-chart-column"></i> Reports</a>
            <a href="?page=settings" class="nav-link <?php echo $page === 'settings' ? 'active' : ''; ?>"><i class="fas fa-cog"></i> Settings</a>
        </nav>
        <div class="footer">© 2025 Unnati Traders</div>
    </div>

    <!-- Overlay for Mobile -->
    <div class="overlay" id="overlay"></div>

    <!-- Header -->
    <header class="header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <button class="hamburger me-3" id="hamburger"><i class="fas fa-bars"></i></button>
            <h5 class="mb-0 fw-bold">Hey! <?php echo isset($_SESSION['user']) ? $_SESSION['user'] : 'Factory'; ?></h5>
        </div>
        <form class="d-flex" role="search" method="GET" action="search.php">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                <input class="form-control border-start-0" type="search"
                    placeholder="Search orders, products, or invoices..." aria-label="Search">
            </div>
        </form>
        <div class="d-flex align-items-center">
            <div class="dropdown me-2">
                <button class="btn btn-outline-primary btn-sm position-relative" data-bs-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    <?php
                    // Mock notifications data
                    $notifications = [
                        [
                            'title' => 'New Order Received',
                            'message' => 'Order #ORD-2854 has been placed.',
                            'time' => '2 hours ago',
                            'read' => false,
                            'color' => 'primary',
                            'icon' => 'fa-bell'
                        ],
                        [
                            'title' => 'Payment Overdue',
                            'message' => 'Invoice #INV-3845 payment is overdue.',
                            'time' => '1 day ago',
                            'read' => true,
                            'color' => 'warning',
                            'icon' => 'fa-exclamation-triangle'
                        ],
                        [
                            'title' => 'Delivery Completed',
                            'message' => 'Delivery #DEL-482 has been completed.',
                            'time' => '3 hours ago',
                            'read' => false,
                            'color' => 'success',
                            'icon' => 'fa-check-circle'
                        ]
                    ];

                    $unread = array_filter($notifications, function ($n) {
                        return !$n['read'];
                    });

                    if (count($unread) > 0):
                        ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php echo count($unread); ?>
                            <span class="visually-hidden">unread notifications</span>
                        </span>
                    <?php endif; ?>
                </button>
                <div class="dropdown-menu dropdown-menu-end notification-dropdown p-0"
                    style="width: 320px; max-height: 400px; overflow-y: auto;">
                    <div class="p-2 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Notifications</h6>
                        <?php if (count($unread) > 0): ?>
                            <button class="btn btn-link btn-sm text-decoration-none">Mark all read</button>
                        <?php endif; ?>
                    </div>
                    <div class="notifications-list">
                        <?php foreach ($notifications as $notification): ?>
                            <div
                                class="dropdown-item notification-item p-2 <?php echo $notification['read'] ? 'bg-light' : ''; ?>">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <span class="fa-stack fa-sm">
                                            <i
                                                class="fas fa-circle fa-stack-2x text-<?php echo $notification['color']; ?> opacity-25"></i>
                                            <i
                                                class="fas <?php echo $notification['icon']; ?> fa-stack-1x text-<?php echo $notification['color']; ?>"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="mb-0 fw-semibold"><?php echo htmlspecialchars($notification['title']); ?>
                                        </h6>
                                        <p class="mb-0 small"><?php echo htmlspecialchars($notification['message']); ?></p>
                                        <small
                                            class="text-muted"><?php echo htmlspecialchars($notification['time']); ?></small>
                                    </div>
                                    <?php if (!$notification['read']): ?>
                                        <div class="flex-shrink-0 ms-2">
                                            <span class="badge bg-primary rounded-pill">New</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="p-2 border-top text-center">
                        <a href="#" class="text-decoration-none small">View all notifications</a>
                    </div>
                </div>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown"><i
                        class="fas fa-user-circle"></i></button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <small class="text-muted text-center d-block"
                        style="font-size: 0.8rem;"><?php echo isset($_SESSION['user_email']) ? $_SESSION['user_email'] : 'vendor@unnati.com'; ?></small>  <!-- User email can be change according to dashboard -->
                    <li><a class="dropdown-item" href="?page=settings">Update Profile</a></li>
                    <form action="../../logout.php" method="POST" class="d-inline">
                        <input type="hidden" name="logout_btn" value="logout">
                        <button type="submit" class="dropdown-item">Logout</button>
                    </form>
                </ul>
            </div>
        </div>
    </header>

     
</html>