<style>
    body {
      overflow-x: hidden;
    }

    .sidebar {
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      width: 260px;
      background-color: #192134;
      color: white;
      display: flex;
      flex-direction: column;
      transition: transform 0.3s ease;
      z-index: 1050;
    }

    .sidebar-hidden {
      transform: translateX(-100%);
    }

    .sidebar-header {
      background: linear-gradient(to right,rgb(11, 18, 49),rgb(19, 68, 136));
      padding: 1rem 1.5rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .sidebar-header h5 {
      margin: 0;
      font-size: 1.05rem;
      font-weight: bold;
      color: white;
    }

    .sidebar-header .user-icon {
      background:rgb(39, 58, 101);
      width: 35px;
      height: 35px;
      border-radius: 50%;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 10px;
      font-weight: bold;
    }

    .sidebar-menu {
      flex-grow: 1;
      padding: 0.5rem 0;
    }

    .sidebar-menu a {
      display: flex;
      align-items: center;
      gap: 12px;
      color: #cbd5e1;
      text-decoration: none;
      padding: 0.75rem 1.5rem;
      transition: background 0.3s;
    }

    .sidebar-menu a:hover,
    .sidebar-menu a.active {
      background-color: #0e7490;
      color: white;
      border-radius: 6px;
    }

    .sidebar-footer {
      padding: 0.75rem 1.5rem;
      font-size: 0.85rem;
      color: #94a3b8;
      border-top: 1px solid #334155;
    }

    .close-btn {
      background: none;
      border: none;
      color: white;
      font-size: 1.2rem;
    }

    .navbar .user-icon {
        background: rgb(39, 58, 101);
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
        <div class="sidebar-header">
        <div class="d-flex align-items-center">
            <div class="user-icon">U</div>
            <h5>Shree Unnati Traders</h5>
        </div>
        <button class="close-btn d-md-none" onclick="toggleSidebar()">&times;</button>
        </div>

        <!-- Menu -->
        <div class="sidebar-menu">
        <a href="./admin_dashboard.php" class="active"><i class="fas fa-table-cells-large"></i> Main Dashboard</a>
        <a href="#"><i class="fas fa-file-invoice"></i> Billing Desk</a>
        <a href="#"><i class="fas fa-dollar-sign"></i> Accounting</a>
        <a href="#"><i class="fas fa-box-open"></i> Inventory</a>
        <a href="#"><i class="fas fa-chart-line"></i> Expenses</a>
        <a href="#"><i class="fas fa-warehouse"></i> Factory Stock</a>
        <a href="#"><i class="fas fa-store"></i> Retail Store</a>
        <a href="#"><i class="fas fa-headset"></i> After-Sales Service</a>
        <a href="#"><i class="fas fa-truck-moving"></i> Suppliers</a>
        <a href="#"><i class="fas fa-chart-bar"></i> Reports</a>
        <a href="#"><i class="fas fa-users-gear"></i> User Management</a>
        <a href="./setting.php"><i class="fas fa-gear"></i> Settings</a>
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
            <button class="btn btn-dark m-2 toggle-btn d-md-none" onclick="toggleSidebar()">
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
                <form action="../logout.php" method="POST">
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