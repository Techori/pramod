<?php
    $host="localhost";
    $user="root";
    $password="";
    $dbname="unnati-wires";
    date_default_timezone_set("Asia/Kolkata");
    
    $conn = new mysqli($host, $user, $password, $dbname)
    or die ('Could not connect to the database server' . mysqli_connect_error());