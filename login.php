<?php
session_start();

include("handlers/connection.php");
include("handlers/functions.php");

$error_message = '';

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $user_name = trim($_POST['user_name']);
    $password = $_POST['password'];

    if (!empty($user_name) && !empty($password) && !is_numeric($user_name)) {
        // SECURE: Use prepared statement
        $query = "SELECT * FROM users WHERE user_name = ? AND password = ? LIMIT 1";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "ss", $user_name, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            $_SESSION['user_id'] = $user_data['user_id'];
            $_SESSION['user_name'] = $user_data['user_name'];
            header("Location: index.php");
            die;
        } else {
            $error_message = "Wrong username or password!";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Please enter some valid information!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logan Login - Doodle of the Day</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div id="intro">
        <h1>Welcome!</h1>
        <p>This is a doodle of the day competition - log in below and then start drawing off of the prompt!</p>
        <p>Don't want to draw? <a href="pages/gallery/gallery.php">look at the gallery</a></p>
        <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
    </div>

    <div id="box">
        <form method="post">
            <div style="font-size: 20px; margin: 10px; color: white;">Login</div>
            <?php if ($error_message): ?>
                <p style="color: #ff6b6b; margin: 10px;"><?= htmlspecialchars($error_message) ?></p>
            <?php endif; ?>
            <p>First name:</p>
            <input id="text" type="text" name="user_name" required>
            <p>Password:</p>
            <input id="text" type="password" name="password" required>
            <input id="button" type="submit" name="Login" value="Login">
            <a href="signup.php">Click to Signup</a>
        </form>
    </div>
</body>
</html>