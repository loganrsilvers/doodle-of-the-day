<?php
session_start();

    include("handlers/connection.php");
    include("handlers/functions.php");

    $user_data = get_user_if_logged_in($con);





// Build query using the ORDER BY we chose
$query = "
    SELECT d.id, d.image_data, d.created_at, d.likes, u.user_name
    FROM doodles d
    LEFT JOIN users u ON d.user_id = u.user_id
    ORDER BY d.created_at DESC
";


$result = mysqli_query($con, $query);

if (!$result) {
    die("Query error: " . mysqli_error($con));
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logan's Site</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <h1>Doodle of the Day</h1>
    <!-- if logged in, display name -->
    <?php if (isset($user_data['user_name'])): ?>
        <p> You are already logged in - Hello, <?= htmlspecialchars($user_data['user_name']) ?>!</p>
        <p><a href="pages/profile/index.php">Go to your profile</a></p>
        <p><a href="pages/gallery/gallery.php">Go to the gallery</a></
    <?php endif; ?>
    <!-- if not logged in, prompt to sign up -->
    <?php if (!isset($user_data['user_name'])): ?>
        <p>Please <a href="signup.php">sign up</a> to begin! (already have an account? <a href="login.php">log in</a>)</p>
    <?php endif; ?>

    <h2>Gallery</h2>

    <div class="gallery">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <?php
                $imgBase64 = base64_encode($row['image_data']);
                $imgSrc = 'data:image/png;base64,' . $imgBase64;
                $userName = $row['user_name'] ?? 'Unknown';
            ?>
            <div class="gallery-item">
                <img src="<?php echo $imgSrc; ?>" alt="Doodle">
                <div id="circle-container">
                    <div id="circle"></div>
                    <div id="circle2"></div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

</body>
</html>