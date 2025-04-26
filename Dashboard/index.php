<?php
session_start();

if (!(isset($_SESSION["uid"]) && isset($_SESSION["user_type"]) && isset($_SESSION["session_id"]))) {
    header("Location: ../login.php");
    exit;
} else {
    switch ($_SESSION["user_type"]) {
        case 'Admin':
            header("Location: ./Admin/admin_dashboard.php");
            break;

        case 'Factory':
            header("Location: ./Factory/factory_dashboard.php");
            break;

        case 'Store':
            header("Location: ./Retail_Store/store_dashboard.php");
            break;

        case 'Vendor':
            header("Location: ./Vendor/vendor_dashboard.php");
            break;

        default:
            header("Location: ../login.php");
            break;
    }
    exit;
}
