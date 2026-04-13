<?php
session_start();

    include("handlers/connection.php");
    include("handlers/functions.php");

    if($_SERVER['REQUEST_METHOD'] == "POST")
    {
        // something was posted
        $user_name = $_POST['user_name'];
        $password = $_POST['password'];

        if (!empty($user_name) && !empty($password) && !is_numeric($user_name))
        {
            // save to database
            $user_id = random_num(length: 20);
            
            $query = "INSERT INTO users (user_id, user_name, password)
              VALUES ('$user_id', '$user_name', '$password')";

            mysqli_query($con, $query);
            header("Location: login.php");
            die;
        } else
        {
            echo "Please enter some valid information!";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>logan signup</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="signup.css">
</head>
<body>
    
    <div>
        <h1>Welcome!</h1>
        <p>this is a doodle of the day competition - sign up below to get started and add to our <a href="pages/gallery/gallery.php">gallery</a>!</p>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>

    <div id="box">
        <form method="post">
            <div style="font-size: 20px;margin: 10px; color: white;">Signup</div>

            <p>first name:</p>
            <input id="text" type="text" name="user_name">
            <p>weak password:</p>
            <input id="text" type="password" name="password">

            <input id="button" type="submit" name="Signup">

            <a href="login.php">Click to Login</a>
        </form>
    </div>

</body>
</html>