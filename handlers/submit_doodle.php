<?php
session_start();
require_once "../handlers/connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_POST['doodle_image']) || empty($_POST['doodle_image'])) {
    die("No doodle submitted.");
}

$user_id = $_SESSION['user_id'];
$dataURL = $_POST['doodle_image'];

// Remove the prefix: "data:image/png;base64,"
$parts = explode(',', $dataURL);
if (count($parts) !== 2) {
    die("Invalid image data.");
}
$base64 = $parts[1];

// Decode to binary
$imageData = base64_decode($base64);
if ($imageData === false) {
    die("Failed to decode image.");
}

// Insert into DB using prepared statement (already secure!)
$query = "INSERT INTO doodles (user_id, image_data) VALUES (?, ?)";
$stmt = $con->prepare($query);
$null = NULL;
$stmt->bind_param("ib", $user_id, $null);
$stmt->send_long_data(1, $imageData);

if ($stmt->execute()) {
    header("Location: ../pages/gallery/gallery.php");
    exit;
} else {
    echo "Error saving doodle: " . $stmt->error;
}
$stmt->close();
?>