<?php
session_start();
include "../../_conn.php";

function clean($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

if (isset($_POST['whatAction'])) {
    $Name = clean($_POST['customer_name']);
    $type = clean($_POST['type']);
    $contact = clean($_POST['customer_phone']);
    $current_date = date("Y-m-d");
    $created_by = $_SESSION['user_name'];
    $created_for = $_SESSION['user_name']; 

    $result = $conn->query("SELECT customer_Id FROM customer ORDER BY CAST(SUBSTRING(customer_Id, 6) AS UNSIGNED) DESC LIMIT 1 FOR UPDATE");

    if ($result && $row = $result->fetch_assoc()) {
        $lastId = $row['customer_Id']; 
        $num = (int) substr($lastId, 5); 
        $newNum = $num + 1;
    } else {
        $newNum = 1;
    }

    $newCustomerId = 'CUST-' . str_pad($newNum, 3, '0', STR_PAD_LEFT); // e.g., CUST-006

    // Step 2: Prepare and execute insert query
    $stmt = $conn->prepare("INSERT INTO customer (customer_Id, name, type, contact, date, created_by, created_for)
                            VALUES (?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("sssssss", $newCustomerId, $Name, $type, $contact, $current_date, $created_by, $created_for);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: vendor_dashboard.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
