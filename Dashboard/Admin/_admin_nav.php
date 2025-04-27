<style>
    body {
      overflow-x: hidden;
    }

    .sidebar {
      width: 260px;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      background-color: #ffffff;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
      padding-top: 20px;
      overflow-y: auto;
      transition: transform 0.3s ease-in-out;
      z-index: 1000;
      box-shadow: -5px 0 15px rgba(233, 111, 3, 0.78), 0 2px 6px rgba(0, 0, 0, 0.05);
      animation: glowingEffect 2.5s infinite;
    }

    .sidebar-hidden {
      transform: translateX(-100%);
    }

    .sidebar-header .user-icon {
      background-color: #f8f9fa;
      width: 35px;
      height: 35px;
      border-radius: 50%;
      color: black;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 10px;
      font-weight: bold;
      padding: 1rem 1.5rem;
    }

    .sidebar-header img {
        width: 50px;
        height: auto;
        margin-bottom: 10px;
      }

    .sidebar-menu {
      flex-grow: 1;
      padding: 0.5rem 0;
    }

    .sidebar-menu a {
      display: flex;
      align-items: center;
      gap: 12px;
      color: black;
      text-decoration: none;
      padding: 0.75rem 1.5rem;
      transition: background 0.3s;
      font-size: 0.95rem;
    }

    .sidebar-menu a:hover,
    .sidebar-menu a.active {
      background-color: #e9ecef;
      color: #0d6efd;
      border-radius: 6px;
      font-weight: bold;
    }

    .sidebar-footer {
      padding: 0.75rem 1.5rem;
      font-size: 0.85rem;
      color: #6c757d;
      border-top: 1px solid #ddd;
    }

    .close-btn {
      background: none;
      border: none;
      color: black;
      font-size: 1.2rem;
    }

    .navbar .user-icon {
        background: #0d6efd;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }


    .main-content {
      padding: 1rem;
    }

    @media (min-width: 768px) {
      .sidebar {
        transform: none !important;
      }

      .toggle-btn {
        display: none;
      }

      .main-content {
        margin-left: 260px;
      }

      .close-btn {
        display: none;
      }
    }
</style>

<body>
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar sidebar-hidden d-md-block">
        <!-- Header -->
        <div class="sidebar-header d-flex">
            <div class="align-items-center text-center">
              <a href="./admin_dashboard.php" class="align-items-center text-decoration-none">
                <img src="../../public/unnati_logo.png" alt="Logo" class="img-fluid" style="width: auto; height: auto;">
                <h6 class="mb-0 text-dark">Unnati Vendor Portal</h6>
                <small class="text-muted text-dark" style="font-size: 0.8rem;">Manage your business</small>
              </a>
            </div>
          <button class="close-btn d-md-none" onclick="toggleSidebar()">&times;</button>
        </div>

        <?php
          $currentPage = basename($_SERVER['PHP_SELF']);
        ?>
        <!-- Menu -->
        <div class="sidebar-menu">
        <a href="./admin_dashboard.php" class="<?= $currentPage === 'admin_dashboard.php' ? 'active' : '' ?>"><i class="fas fa-table-cells-large"></i> Main Dashboard</a>
        <a href="./billing_desk.php" class="<?= $currentPage === 'billing_desk.php' ? 'active' : '' ?>"><i class="fas fa-file-invoice"></i> Billing Desk</a>
        <a href="./accounting.php" class="<?= $currentPage === 'accounting.php' ? 'active' : '' ?>"><i class="fas fa-dollar-sign"></i> Accounting</a>
        <a href="./inventory.php" class="<?= $currentPage === 'inventory.php' ? 'active' : '' ?>"><i class="fas fa-box-open"></i> Inventory</a>
        <a href="./expenses_dashboard.php" class="<?= $currentPage === 'expenses_dashboard.php' ? 'active' : '' ?>"><i class="fas fa-chart-line"></i> Expenses</a>
        <a href="./factory_stock.php" class="<?= $currentPage === 'factory_stock.php' ? 'active' : '' ?>"><i class="fas fa-warehouse"></i> Factory Stock</a>
        <a href="./retail_store.php" class="<?= $currentPage === 'retail_store.php' ? 'active' : '' ?>"><i class="fas fa-store"></i> Retail Store</a>
        <a href="./After_sales_service.php" class="<?= $currentPage === 'After_sales_service.php' ? 'active' : '' ?>"><i class="fas fa-headset"></i> After-Sales Service</a>
        <a href="./suppliers.php" class="<?= $currentPage === 'suppliers.php' ? 'active' : '' ?>"><i class="fas fa-truck-moving"></i> Suppliers</a>
        <a href="./reports.php" class="<?= $currentPage === './reports.php' ? 'active' : '' ?>"><i class="fas fa-chart-bar"></i> Reports</a>
        <a href="./user_management.php" class="<?= $currentPage === 'user_management.php' ? 'active' : '' ?>"><i class="fas fa-users-gear"></i> User Management</a>
        <a href="./settings.php" class="<?= $currentPage === 'settings.php' ? 'active' : '' ?>"><i class="fas fa-gear"></i> Settings</a>
        </div>

        <!-- Footer -->
        <div class="sidebar-footer">
        © 2025 Unnati Traders
        </div>
    </div>

    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm px-3 py-2">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <!-- Mobile Sidebar Toggle Button -->
            <div class="d-flex align-items-center">
            <button class="btn btn-primary m-2 toggle-btn d-md-none" onclick="toggleSidebar()">
                ☰
            </button>
            </div>

            <!-- Search bar -->
            <div class="d-none d-md-flex flex-grow-1 justify-content-center">
            <div class="input-group w-50">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control border-start-0" placeholder="Search..." />
                <span class="input-group-text bg-light"><kbd>⌘</kbd> + K</span>
            </div>
            </div>

            <!-- User Info & Logout -->
            <div class="d-flex align-items-center gap-3">
            <div class="position-relative">
                <i class="fas fa-bell fa-lg"></i>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
            </div>
            <div class="d-none d-md-block text-end">
                <div><strong>Admin User</strong></div>
                <small class="text-muted">Admin</small>
            </div>
            <div class="user-icon">A</div>
                <form action="../../logout.php" method="POST">
                    <button name="logout_btn" class="btn btn-outline-dark nav-right-btn fw-bold" type="submit" value="true">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <script>
        function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('sidebar-hidden');
        }
    </script>
</body>