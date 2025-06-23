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
    } else if ((isset($_POST['email'])) && isset($_POST['password']) && isset($_POST['newPassword']) && isset($_POST['confirmPassword']) && isset($_POST['submit'])) {
        // echo "<script>console.log('Form submitted');</script>";
        $pass = $_POST['password'];
        $user_type = $_POST['user_type'];
        $newpass = $_POST['newPassword'];
        $confirmpass = $_POST['confirmPassword'];
        // $salt = bin2hex(random_bytes(16));
        // $saltedPW =  $pass . $salt;
        // $hashedPW = hash('sha256', $saltedPW);
        // echo "<p style='color:red'>HashedPW: $hashedPW</p>";
        // echo "<p style='color:red'>salt: $salt</p>";
    
        if ($newpass !== $confirmpass) {
            echo "<script>
                swal({
                    title: 'Password Mismatch',
                    text: 'The new password and confirm password do not match.',
                    icon: 'warning',
                    button: 'Ok'
                });
            </script>";
            header('location:./forgotpass.php');
            exit;
        }

        if (isset($_POST['email'])) {
            $uniq_id = $_POST['email'];
            $sql = "SELECT * FROM users WHERE email = '$uniq_id';";
        }

        $result = $conn->query($sql);
        if ($row = $result->fetch_assoc()) {

            $salt = $row['salt'];
            $saltedPW = $pass . $salt;
            $hashedPW = hash('sha256', $saltedPW);
            if ($hashedPW == $row['password']) {

                $saltedNewPW = $newpass . $salt;
                $hashedNewPW = hash('sha256', $saltedNewPW);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ? AND user_type = ?");
                $stmt->bind_param("sss", $hashedNewPW, $uniq_id, $user_type);
                $stmt->execute();
                $stmt->close();
                header("Location:./login.php");
                exit;


            } else {
                $_SESSION["currentpass_not_match"] = true;
                header('location:./forgotpass.php');
                exit;
            }
        } else {
            $_SESSION["uid_error"] = true;
            header('location:./forgotpass.php');
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

    } else if (isset($_SESSION["currentpass_not_match"])) {

        echo "<script>
                swal({
                    title: 'Current Password Not Match',
                    text: 'The current password is not match.',
                    icon: 'error',
                    button: 'Ok, understood!'
                });
            </script>";
        unset($_SESSION["currentpass_not_match"]);

    }




    ?>
    <div class="container py-5">
        <div class="row gy-4 pt-4 justify-content-center">

            <div class="col-lg-6 ">
                <div class="login-box">
                    <h4 class="fw-bold text-center">Forgot Password</h4>

                    <form action="forgotpass.php" method="POST">
                        <div class="login-fields">
                            <label for="username">User type</label>
                            <div class="dropdown-container mb-3">
                                <div class="custom-dropdown" id="dropdown">
                                    <div class="dropdown-selected" id="selected">
                                        <span class="selected-text" id="selectedText">Admin</span>
                                        <span class="dropdown-arrow" id="arrow">&#9662;</span>
                                    </div>
                                    <div class="dropdown-options" id="options">
                                        <div class="dropdown-option">Admin</div>
                                        <div class="dropdown-option">Retail Store</div>
                                        <div class="dropdown-option">Vendor</div>
                                        <div class="dropdown-option">Factory</div>
                                    </div>
                                </div>
                                <input type="hidden" name="user_type" id="userTypeInput" value="Admin">
                            </div>
                            <label for="username">Email</label>
                            <input type="email" name="email" class="form-control mb-3" placeholder="Username" required>
                            <label for="password">Current Password</label>
                            <input type="password" name="password" class="form-control mb-3" placeholder="Password"
                                required>
                            <label for="newPassword">New Password</label>
                            <input type="password" name="newPassword" class="form-control mb-3" required>
                            <label for="confirmPassword">Confirem Password</label>
                            <input type="password" name="confirmPassword" class="form-control mb-3" required>
                            <div>
                                Don't forgot password
                                <a href="./login.php">Login</a>
                            </div>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-fancy mb-4" name="submit" type="submit">submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        const dropdown = document.getElementById('dropdown');
        const options = document.getElementById('options');
        const selectedText = document.getElementById('selectedText');
        const optionItems = options.querySelectorAll('.dropdown-option');

        dropdown.addEventListener('click', () => {
            const isOpen = options.style.display === 'block';
            options.style.display = isOpen ? 'none' : 'block';
            dropdown.classList.toggle('open', !isOpen);
        });

        optionItems.forEach(option => {
            option.addEventListener('click', (e) => {
                const selectedValue = e.target.textContent;
                selectedText.textContent = selectedValue;
                document.getElementById('userTypeInput').value = selectedValue;
                options.style.display = 'none';
                dropdown.classList.remove('open');
            });
        });

        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target)) {
                options.style.display = 'none';
                dropdown.classList.remove('open');
            }
        });
    </script>
</body>

</html>