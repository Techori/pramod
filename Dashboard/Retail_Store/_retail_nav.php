<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Check current page for active highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retail Store Dashboard - Shree Unnati Wires & Traders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        mark.navbar-search-highlight { background-color: yellow; color: black; padding: 0 2px; border-radius: 2px; }
        .sidebar { position: fixed; top: 0; left: 0; height: 100%; width: 220px; background-color: #f8f9fa; overflow-y: auto; transition: transform 0.3s ease-in-out; z-index: 1000; border-right: 1px solid #dee2e6; }
        .nav-link { color: #333; padding: 10px 15px; border-radius: 5px; margin: 2px 10px; transition: 0.2s; }
        .nav-link:hover { background-color: #e9ecef; color: #0d6efd; }
        .nav-link.active { background-color: #0d6efd; color: white !important; font-weight: 500; }
        .sidebar-header { padding: 20px; text-align: center; border-bottom: 1px solid #dee2e6; }
        .header { margin-left: 220px; padding: 15px 25px; background: #fff; border-bottom: 1px solid #eee; }
        .footer { padding: 15px; text-align: center; font-size: 0.8rem; color: #888; }
        @media (max-width: 768px) { .sidebar { width: 250px; transform: translateX(-100%); } .sidebar.active { transform: translateX(0); } .header { margin-left: 0; } }
        .overlay { display: none; position: fixed; top: 0; left: 0; height: 100vh; width: 100vw; background: rgba(0,0,0,0.4); z-index: 999; }
    </style>
</head>

<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="../../public/unnati_logo.png" alt="Logo" class="img-fluid" style="width: auto; height: 50px; margin-bottom: 10px;">
            <h6 class="mb-0">Unnati Retail Portal</h6>
            <small class="text-muted" style="font-size: 0.75rem;">Techori Management</small>
        </div>
        <nav class="nav flex-column mt-3">
            <a href="store_dashboard.php" class="nav-link <?php echo $current_page == 'store_dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="billing.php" class="nav-link <?php echo $current_page == 'billing.php' ? 'active' : ''; ?>"><i class="fas fa-file-invoice-dollar"></i> Billing</a>
            <a href="supply.php" class="nav-link <?php echo $current_page == 'supply.php' ? 'active' : ''; ?>"><i class="fas fa-boxes"></i> Supply</a>
            
            <a href="inventory.php" class="nav-link <?php echo $current_page == 'inventory.php' ? 'active' : ''; ?>"><i class="fas fa-warehouse"></i> Inventory</a>

            <a href="inventory_purchase.php" class="nav-link <?php echo $current_page == 'inventory_purchase.php' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i> Inventory Purchase
            </a>

            <a href="customers.php" class="nav-link <?php echo $current_page == 'customers.php' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Customers</a>
            <a href="orders.php" class="nav-link <?php echo $current_page == 'orders.php' ? 'active' : ''; ?>"><i class="fas fa-shopping-cart"></i> Orders</a>
            <a href="payments.php" class="nav-link <?php echo $current_page == 'payments.php' ? 'active' : ''; ?>"><i class="fas fa-wallet"></i> Payments</a>
            <a href="after_service.php" class="nav-link <?php echo $current_page == 'after_service.php' ? 'active' : ''; ?>"><i class="fas fa-headset"></i> After-Sales</a>
            <a href="reports.php" class="nav-link <?php echo $current_page == 'reports.php' ? 'active' : ''; ?>"><i class="fas fa-chart-bar"></i> Reports</a>
            <a href="settings.php" class="nav-link <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>"><i class="fas fa-cog"></i> Settings</a>
        </nav>
        <div class="footer">© 2025 Techori / Unnati</div>
    </div>

    <div class="overlay" id="overlay"></div>

    <header class="header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <button class="hamburger btn btn-light me-3 d-md-none" id="hamburger"><i class="fas fa-bars"></i></button>
            <h5 class="mb-0 fw-bold">Hey! <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Manager'; ?></h5>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <div style="position: relative;">
                <button onclick="toggleDropdown()" style="background:none; border:none; font-size: 20px;">
                    🔔 <span id="notif-count" style="color:red; position:absolute; top:-5px; right:-5px; font-size:12px; font-weight:bold;"></span>
                </button>
                <div id="notif-dropdown" style="display:none; position:absolute; top:35px; right:0; background:#fff; border:1px solid #ccc; width:280px; max-height:350px; overflow-y:auto; z-index:1001; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-radius: 8px;">
                    <div id="notifications" style="padding:10px;"></div>
                </div>
            </div>

            <div class="dropdown">
                <button class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-user-circle"></i></button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="settings.php">Update Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <form action="../../logout.php" method="POST">
                        <input type="hidden" name="logout_btn" value="logout">
                        <button type="submit" class="dropdown-item text-danger">Logout</button>
                    </form>
                </ul>
            </div>
        </div>
    </header>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar Toggle Script
        const hamburger = document.getElementById('hamburger');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        hamburger.addEventListener('click', () => {
            sidebar.classList.add('active');
            overlay.style.display = 'block';
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.style.display = 'none';
        });

        // Notification Logic
        function toggleDropdown() {
            const dropdown = document.getElementById("notif-dropdown");
            dropdown.style.display = dropdown.style.display === "none" ? "block" : "none";
        }

        function loadNotifications() {
            fetch("../../notifications.php")
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById("notifications");
                    const countSpan = document.getElementById("notif-count");
                    container.innerHTML = "";
                    let unreadCount = 0;
                    if (data.length === 0) {
                        container.innerHTML = "<p class='text-center py-2'>No notifications</p>";
                        countSpan.textContent = "";
                        return;
                    }
                    data.forEach(n => {
                        if (!n.is_read) unreadCount++;
                        const div = document.createElement("div");
                        div.className = "p-2 border-bottom";
                        div.innerHTML = `<strong>${n.title}</strong><p class='mb-0 small'>${n.message}</p>`;
                        container.appendChild(div);
                    });
                    countSpan.textContent = unreadCount > 0 ? unreadCount : '';
                }).catch(err => console.log("Notif error"));
        }
        loadNotifications();
    </script>
</body>
</html>
