<?php
// handlers/share_doodle.php - Handles sharing doodles

session_start();
require_once "../handlers/connection.php";
require_once "../handlers/friends.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to share']);
    exit;
}

if (!isset($_POST['doodle_id'])) {
    echo json_encode(['success' => false, 'message' => 'No doodle ID provided']);
    exit;
}

$user_id = $_SESSION['user_id'];
$doodle_id = (int)$_POST['doodle_id'];

// Record the share
$query = "INSERT INTO shares (doodle_id, sharer_id) VALUES (?, ?)";
$stmt = $con->prepare($query);
$stmt->bind_param("ii", $doodle_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Link copied to clipboard!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to share']);
}

$stmt->close();
?>