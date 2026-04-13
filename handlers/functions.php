<?php
// handlers/functions.php

function check_login($con) {
    if (isset($_SESSION['user_id'])) {
        $id = $_SESSION['user_id'];
        // SECURE: Use prepared statement
        $query = "SELECT * FROM users WHERE user_id = ? LIMIT 1";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            return $user_data;
        }
        mysqli_stmt_close($stmt);
    }

    header("Location: login.php");
    die;
}

function check_login_from_profile($con) {
    if (isset($_SESSION['user_id'])) {
        $id = $_SESSION['user_id'];
        // SECURE: Use prepared statement
        $query = "SELECT * FROM users WHERE user_id = ? LIMIT 1";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            return $user_data;
        }
        mysqli_stmt_close($stmt);
    }

    header("Location: ../../login.php");
    die;
}

function random_num($length) {
    $text = "";
    if ($length < 5) {
        $length = 5;
    }

    $len = rand(4, $length);

    for ($i = 0; $i < $len; $i++) {
        $text .= rand(0, 9);
    }

    return $text;
}

function get_user_if_logged_in($con) {
    if (isset($_SESSION['user_id'])) {
        $id = $_SESSION['user_id'];
        // SECURE: Use prepared statement
        $query = "SELECT * FROM users WHERE user_id = ? LIMIT 1";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            return $user_data;
        }
        mysqli_stmt_close($stmt);
    }

    return null;
}
?>