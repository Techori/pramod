<?php
session_start();
include './_conn.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shree Unnati Wires & Traders - Premium Wire Manufacturing</title>
    <script src="https://unpkg.com/sweetalert@2.1.2/dist/sweetalert.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-fancy {
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            color: white;
            border: none;
            width: 50%;
            transition: all 0.3s ease;
        }

        .btn-fancy:hover {
            background: linear-gradient(45deg, #2575fc, #6a11cb);
            transform: scale(1.05);
        }

        .process-list li {
            list-style: none;
            padding-left: 1.5rem;
            position: relative;
            margin-bottom: 0.75rem;
        }

        .process-list li::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: red;
            font-weight: bold;
        }

        .login-box {
            background-color: White;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .login-fields {
            align-items: start;
            margin-bottom: 1.5rem;
        }

        .dropdown-container {
            width: 100%;
            max-width: 600px;
        }

        .custom-dropdown {
            position: relative;
            width: 95%;
            background: white;
            border: 2px solid #ccc;
            border-radius: 8px;
            cursor: pointer;
            padding: 12px;
        }

        .dropdown-selected {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .selected-text {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .custom-dropdown.open .dropdown-arrow {
            transform: rotate(180deg);
        }

        .dropdown-arrow {
            margin-left: 10px;
            font-size: 20px;
            transition: transform 0.3s ease;
        }

        .custom-dropdown.open .dropdown-arrow {
            transform: rotate(180deg);
        }

        .dropdown-options {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: white;
            border: 1px solid #ccc;
            border-radius: 0 0 8px 8px;
            z-index: 99;
            display: none;
            max-height: 200px;
            overflow-y: auto;
        }

        .dropdown-option {
            padding: 10px;
            cursor: pointer;
        }

        .dropdown-option:hover {
            background-color: #eee;
        }

        @media (max-width: 500px) {
            .custom-dropdown {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>

<body class="bg-secondary bg-opacity-10">
    <?php
    if (isset($_SESSION["uid"]) && isset($_SESSION["user_type"]) && isset($_SESSION["session_id"])) {
        header("location:./index.php");
        exit;
    } else if ((isset($_POST['email'])) && isset($_POST['password']) && isset($_POST['submit'])) {
        // echo "<script>console.log('Form submitted');</script>";
        $pass = $_POST['password'];
        $user_type = $_POST['user_type'];
        // $salt = bin2hex(random_bytes(16));
        // $saltedPW =  $pass . $salt;
        // $hashedPW = hash('sha256', $saltedPW);
        // echo "<p style='color:red'>HashedPW: $hashedPW</p>";
        // echo "<p style='color:red'>salt: $salt</p>";
    
        if (isset($_POST['email'])) {
            $uniq_id = $_POST['email'];
            $sql = "SELECT * FROM users WHERE email = '$uniq_id' AND user_type = '$user_type';";
        }

        $result = $conn->query($sql);
        if ($row = $result->fetch_assoc()) {

            $salt = $row['salt'];
            $saltedPW = $pass . $salt;
            $hashedPW = hash('sha256', $saltedPW);
            if ($hashedPW == $row['password']) {

                $user_type = $row['user_type'];
                $email = $row["email"];
                $user_name = $row["user_name"];

                $charset = "QAZWSXEDCRFVTGBYHNUJMIKLOPqwertyuiopasdfghjklmnbvcxz1234567890";
                $session_id = "";
                for ($i = 0; $i < 25; $i++) {
                    $rand_int = rand(0, 61);
                    $session_id = $session_id . $charset[$rand_int];
                }
                $_SESSION["user_type"] = $user_type;
                $_SESSION["uid"] = $row['email'];
                $_SESSION["user_name"] = $row['user_name'];
                $_SESSION["session_id"] = $session_id;
                if (isset($_POST['remember'])) {
                    // Cookie valid for 30 days
                    setcookie("remember_email", $email, time() + (86400 * 30), "/");
                    setcookie("remember_pass", $pass, time() + (86400 * 30), "/");
                    setcookie("remember_type", $user_type, time() + (86400 * 30), "/");
                } else {
                    // Agar unchecked hai to delete kar do
                    setcookie("remember_email", "", time() - 3600, "/");
                    setcookie("remember_pass", "", time() - 3600, "/");
                    setcookie("remember_type", "", time() - 3600, "/");
                }
                header("Location:./Dashboard/");
                exit;


            } else {
                $_SESSION["uid_error"] = true;
                header('location:./login.php');
                exit;
            }
        } else {
            $_SESSION["uid_error"] = true;
            header('location:./login.php');
            exit;
        }
    }
    ?>
    <?php

    if (isset($_SESSION["already_error"])) {

        echo "<script>
                swal({
                    title: 'Login error',
                    text: 'Try again after few minutes',
                    icon: 'warning',
                    button: 'Ok'
                });
            </script>";
        unset($_SESSION["already_error"]);

    } else if (isset($_SESSION["uid_error"])) {

        echo "<script>
                swal({
                    title: 'INVALID CREDENTIAL',
                    text: 'The email Or password is wrong.',
                    icon: 'error',
                    button: 'Ok, understood!'
                });
            </script>";
        unset($_SESSION["uid_error"]);

    } else if (isset($_SESSION["something_went_wrong"])) {

        echo "<script>
                swal({
                    title: 'SOMETHING WENT WRONG!!',
                    text: 'Something went wrong \\n \\n Note: Please try again. Reload the page or clear the cache.',
                    icon: 'error',
                    button: 'Ok, understood!'
                });
            </script>";
        unset($_SESSION["something_went_wrong"]);

    }




    ?>
    <div class="container py-5">
        <div class="row align-items-start gy-4 pt-4">
            <div class="col-lg-6 pt-5">
                <h2 class="fw-bold mb-3">Welcome to Unnati Traders Management System</h2>
                <p>Access your dashboard to manage inventory, billing, and business operations efficiently.</p>
                <div class="login-box">
                    <ul class="process-list">
                        <h5>System Features:</h5>
                        <li>Comprehensive inventory management</li>
                        <li>GST & non-GST billing and invoicing</li>
                        <li>Financial tracking and reporting</li>
                        <li>Buy Now, Pay Later management</li>
                        <li>Supplier and distributor management</li>
                        <li>Real-time business analytics</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-6 ">
                <div class="login-box">
                    <h4 class="fw-bold text-center">Login to Unnati Traders</h4>
                    <p class="text-center">Access your business management dashboard</p>

                    <form action="login.php" method="POST">
                        <div class="login-fields">
                            <label for="user_type">Login As</label>
                            <select name="user_type" id="user_type" class="form-select mb-3" required>
                                <option value="Admin" <?= (isset($_COOKIE['remember_type']) && $_COOKIE['remember_type'] == 'Admin') ? 'selected' : '' ?>>Admin</option>
                                <option value="Store" <?= (isset($_COOKIE['remember_type']) && $_COOKIE['remember_type'] == 'Store') ? 'selected' : '' ?>>Store</option>
                                <option value="Vendor" <?= (isset($_COOKIE['remember_type']) && $_COOKIE['remember_type'] == 'Vendor') ? 'selected' : '' ?>>Vendor</option>
                                <option value="Factory" <?= (isset($_COOKIE['remember_type']) && $_COOKIE['remember_type'] == 'Factory') ? 'selected' : '' ?>>Factory</option>
                            </select>
                            <label for="username">Email</label>
                            <input type="email" name="email" class="form-control mb-3" placeholder="Username"
                                value="<?= isset($_COOKIE['remember_email']) ? $_COOKIE['remember_email'] : '' ?>"
                                required>

                            <label for="password">Password</label>
                            <input type="password" name="password" class="form-control mb-3" placeholder="Password"
                                value="<?= isset($_COOKIE['remember_pass']) ? $_COOKIE['remember_pass'] : '' ?>"
                                required>

                            <div style="display: flex; justify-content: space-between;">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" style="border: 2px solid #ccc"
                                        id="dropdownCheck" name="remember" <?= isset($_COOKIE['remember_email']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="dropdownCheck">Remember me</label>
                                </div>
                                <a href="./forgotpass.php">Forgot your password</a>
                            </div>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-fancy mb-4" name="submit" type="submit">Login</button>
                            <!-- <p>Demo Logins:</p>
                        <p>Admin: admin@unnati.com / admin123</p>
                        <p>Retail Store: store@unnati.com / store123</p>
                        <p>Vendor: vendor@unnati.com / vendor123</p>
                        <p>Factory: factory@unnati.com / factory123</p> -->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>