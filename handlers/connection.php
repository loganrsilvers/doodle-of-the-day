<?php

$servername = "127.0.0.1:3307";
$username = "root";
$password = "";
$dbname = "login_db";

$con = new mysqli($servername,$username,$password,$dbname);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

?>