<?php 
    if (!(isset($_SESSION["uid"]) && isset($_SESSION["user_type"]) && isset($_SESSION["session_id"])) ) {
        $logreg = true;     
    }
    else{
        $logreg = false;
    }
?>

<style>
    .nav-right-btn:hover {
        color: black !important;
    }

    .dropdown-menu {
        background-color: black;
    }

    .dropdown-menu .dropdown-item {
        color: rgb(17, 218, 253);
    }

    .dropdown-menu .dropdown-item:hover {
        background-color: rgb(17, 218, 253);
        color: black !important;
    }

    .dropdown-part-1 .dropdown-item {
        background-color: rgb(17, 218, 253);
        color: black;
    }

</style>

<body>
    <header class="sticky-top">
        <nav class="navbar navbar-expand-lg bg-light">
            <div class="container-fluid">
                <a class="navbar-brand fs-2" href="./index.php"><b><span class="text-info">Shree Unnati</span></b></a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="./index.php"><span
                                    class="text-dark">Home</span></a>
                        </li>
                        <?php if (!$logreg) {?>
                            <li class="nav-item">
                                <a class="nav-link" href="./Dashboard/index.php"><span class="text-dark">Dashboard</span></a>
                            </li>
                        <?php } ?>
                        <li class="nav-item">
                            <a class="nav-link" href="./about_us.php"><span class="text-dark">About Us</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./product.php"><span class="text-dark">Products</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><span class="text-dark">Contact</span></a>
                        </li>
                        <li class="nav-item dropdown me-2">
                            <a class="nav-link" href="./fran.php">
                                <span class="text-dark">Franchise</span>
                            </a>
                        </li>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="#" class="btn btn-outline-info nav-right-btn">
                                Contact Us
                            </a>
                        <?php 
                            if ($logreg) {
                                echo '
                                <a href="./login.php" class="btn btn-outline-info nav-right-btn">
                                Login
                                </a>';
                            }
                            else {?>
                                    <form action="./logout.php" method="POST">
                                        <button name="logout_btn" class="btn btn-outline-info nav-right-btn" type="submit" value="true">Logout</button>
                                    </form>
                            <?php } ?>
                        </div>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <script>
    </script>
</body>