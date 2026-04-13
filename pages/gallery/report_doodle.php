<?php
session_start();
require_once "connection.php";

if (!isset($_GET['id'])) {
    die("No doodle selected.");
}

$doodle_id = (int) $_GET['id'];
$user_id = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reason = trim($_POST['reason'] ?? '');

    if ($reason === '') {
        $error = "Please enter a reason for reporting this image.";
    } else {
        $query = "INSERT INTO reports (doodle_id, user_id, reason) VALUES (?, ?, ?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("iis", $doodle_id, $user_id, $reason);
        $stmt->execute();

        $success = "Thank you. Your report has been submitted.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Report image</title>
</head>
<body>
    <h1>Report this image</h1>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <p style="color:green;"><?php echo htmlspecialchars($success); ?></p>
        <p><a href="gallery.php">Back to gallery</a></p>
    <?php else: ?>
        <p>You are reporting doodle ID: <?php echo $doodle_id; ?></p>

        <form method="post">
            <label for="reason">Why are you reporting this image?</label><br>
            <textarea id="reason" name="reason" rows="5" cols="50" required></textarea><br><br>
            <button type="submit">Submit report</button>
        </form>

        <p><a href="gallery.php">Cancel and go back</a></p>
    <?php endif; ?>
</body>
</html>
