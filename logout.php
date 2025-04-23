<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout_btn'])) {
    session_start();
    session_unset();
    session_destroy();

    header("Location: ./index.php");
    exit();
}
else {
    header("Location: ./index.php");
    exit();
}
?>
