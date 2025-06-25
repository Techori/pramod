 <?php
    session_start();
 ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Shree Unnati Wires & Traders</title>
</head>
<body>

    <?php
    include "../../_conn.php";
     function clean($input)
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    if(isset($_POST['whatAction'])){
        $Name = clean($_POST['customer_name']);
        $type = clean($_POST['type']);
        $contact = clean($_POST['customer_phone']);
        $current_date = date('Y-m-d');
        $user_name =$_SESSION['user_name'];
        $created_for = clean($_POST['created_for']);
   
         $stmt = $conn->prepare("INSERT INTO customer 
                (name, type, contact, date, created_by, created_for) 
                VALUES (?, ?, ?, ?, ?, ?)");

            $stmt->bind_param("ssisss", $Name, $type, $contact, $current_date, $user_name, $created_for);

            if($stmt->execute()){
                 $stmt->close();
                 $conn->close();
                 header('LOCATION: admin_dashboard.php?page=billing_desk');
            }

            else{
                echo "Error" . $stmt->error;
            }
    }

    ?>
</body>

</html>