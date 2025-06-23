<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factory Dashboard - Shree Unnati Wires & Traders</title>
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
            <a href="?page=dashboard" class="nav-link <?php echo $page === 'dashboard' ? 'active' : ''; ?>"
                style="font-size: smaller;"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="?page=production" class="nav-link <?php echo $page === 'production' ? 'active' : ''; ?>"
                style="font-size: smaller;"><i class="fa-solid fa-industry"></i> Production</a>
            <a href="?page=billing_system" class="nav-link <?php echo $page === 'billing_system' ? 'active' : ''; ?>"
                style="font-size: smaller;"><i class="fas fa-file-invoice"></i> Billing System</a>
            <a href="?page=supply_management"
                class="nav-link <?php echo $page === 'supply_management' ? 'active' : ''; ?>"
                style="font-size: smaller;"><i class="fa-solid fa-clipboard-list"></i> Supply Management</a>
            <a href="?page=raw_materials" class="nav-link <?php echo $page === 'raw_materials' ? 'active' : ''; ?>"
                style="font-size: smaller;"><i class="fa-solid fa-cube"></i> Raw Materials</a>
            <a href="?page=inventory" class="nav-link <?php echo $page === 'inventory' ? 'active' : ''; ?>"
                style="font-size: smaller;"><i class="fa-solid fa-box"></i> Inventory</a>
            <a href="?page=workers" class="nav-link <?php echo $page === 'workers' ? 'active' : ''; ?>"
                style="font-size: smaller;"><i class="fa-regular fa-user"></i> Workers</a>
            <a href="?page=expenses" class="nav-link <?php echo $page === 'expenses' ? 'active' : ''; ?>"
                style="font-size: smaller;"><i class="fa-solid fa-sack-dollar"></i> Expenses</a>
            <a href="?page=after_sales_service"
                class="nav-link <?php echo $page === 'after_sales_service' ? 'active' : ''; ?>"
                style="font-size: smaller;"><i class="fas fa-headset"></i> After-Sales Service</a>
            <a href="?page=reports" class="nav-link <?php echo $page === 'reports' ? 'active' : ''; ?>"
                style="font-size: smaller;"><i class="fa-solid fa-chart-column"></i> Reports</a>
            <a href="?page=settings" class="nav-link <?php echo $page === 'settings' ? 'active' : ''; ?>"
                style="font-size: smaller;"><i class="fas fa-cog"></i> Settings</a>
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
            <!-- Notification Bell -->
            <div style="position: relative;">
                <button onclick="toggleDropdown()" style="background:none; border:none; position:relative;">
                    🔔
                    <span id="notif-count" style="color:red; position:absolute; top:0; right:0; font-size:12px;"></span>
                </button>

                <!-- Notification Dropdown -->
                <div id="notif-dropdown"
                    style="display:none; position:absolute; top:30px; right:0; background:#fff; border:1px solid #ccc; width:300px; max-height:400px; overflow-y:auto; z-index:999;">
                    <div id="notifications" style="padding:10px;"></div>
                </div>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown"><i
                        class="fas fa-user-circle"></i></button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <small class="text-muted text-center d-block"
                        style="font-size: 0.8rem;"><?php echo isset($_SESSION['uid']) ? $_SESSION['uid'] : ''; ?></small>
                    <!-- User email can be change according to dashboard -->
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const navbarSearch = document.querySelector('.input-group input[type="search"]');

        if (navbarSearch) {
            navbarSearch.addEventListener("input", function () {
                const searchText = navbarSearch.value.trim().toLowerCase();

                // Remove old highlights
                document.querySelectorAll("mark.navbar-search-highlight").forEach(mark => {
                    const parent = mark.parentNode;
                    parent.replaceChild(document.createTextNode(mark.textContent), mark);
                    parent.normalize();
                });

                if (!searchText) return;

                let firstMatchElement = null;

                document.body.querySelectorAll("*:not(script):not(style)").forEach(el => {
                    if (el.children.length === 0 && el.textContent.toLowerCase().includes(searchText)) {
                        const regex = new RegExp(`(${searchText})`, "i");
                        el.innerHTML = el.textContent.replace(regex, '<mark class="navbar-search-highlight">$1</mark>');

                        if (!firstMatchElement) {
                            firstMatchElement = el;
                        }
                    }
                });

                if (firstMatchElement) {
                    const hiddenTab = firstMatchElement.closest(".billing-tab-content");
                    if (hiddenTab && !hiddenTab.classList.contains("active")) {
                        document.querySelectorAll(".billing-tab-content").forEach(tab => tab.classList.remove("active"));
                        hiddenTab.classList.add("active");
                    }

                    setTimeout(() => {
                        firstMatchElement.scrollIntoView({ behavior: "smooth", block: "center" });
                    }, 200);
                }
            });
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const hamburger = document.getElementById('hamburger');

        // Open sidebar on hamburger click
        hamburger.addEventListener('click', function () {
            sidebar.classList.add('active');
            overlay.style.display = 'block';
        });

        // Close sidebar on overlay click
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('active');
            overlay.style.display = 'none';
        });
    });
</script>


<style>
    .header {
        position: fixed !important;
        top: 0;
        left: 0;
        width: 87%;
        z-index: 1000;
    }

    body {
        padding-top: 35px;
        margin: 0;
    }

    mark.navbar-search-highlight {
        background-color: yellow;
        color: black;
        padding: 0 2px;
        border-radius: 2px;
    }

    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 200px;
        background-color: #f8f9fa;
        overflow-y: auto;
        transition: transform 0.3s ease-in-out;
        z-index: 1000;
    }

    /* Mobile: Hide sidebar by default */
    @media (max-width: 768px) {
        .sidebar {
            width: 50%;
            transform: translateX(-100%);
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 999;
        }

        .overlay.active {
            display: block;
        }
    }

    /* Desktop: Sidebar always visible */
    @media (min-width: 769px) {
        .overlay {
            display: none !important;
        }

        .sidebar {
            transform: translateX(0) !important;
        }
    }

    .overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 100vw;
        background-color: rgba(0, 0, 0, 0.4);
        z-index: 999;
    }
</style>

<script>
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
                    container.innerHTML = "<p>No notifications</p>";
                    countSpan.textContent = "";
                    return;
                }

                data.forEach(n => {
                    const div = document.createElement("div");
                    div.style.borderBottom = "1px solid #eee";
                    div.style.padding = "5px";

                    div.innerHTML = `
                    <strong>${n.title}</strong>
                    <p>${n.message}</p>
                    <small>${n.created_at}</small><br>
                    ${n.is_read ? '<em style="color:gray;">Read</em>' :
                            `<button onclick="markRead(${n.id})">Mark as Read</button>`}
                `;

                    if (!n.is_read) unreadCount++;
                    container.appendChild(div);
                });

                countSpan.textContent = unreadCount > 0 ? unreadCount : '';
            });
    }

    function markRead(id) {
        fetch("../../notifications.php", {
            method: "POST",
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `mark_read=1&id=${id}`
        }).then(() => loadNotifications());
    }

    loadNotifications();
    setInterval(loadNotifications, 10000); // refresh every 10s
</script>