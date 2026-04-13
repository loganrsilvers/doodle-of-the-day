<?php
session_start();
require_once "../handlers/connection.php";
require_once "../handlers/friends.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Must be logged in']);
    exit;
}

if (!isset($_POST['requester_id'])) {
    echo json_encode(['success' => false, 'message' => 'No requester ID provided']);
    exit;
}

$user_id = $_SESSION['user_id'];
$requester_id = (int)$_POST['requester_id'];

// Just delete the pending request
$query = "DELETE FROM friends WHERE user_id = ? AND friend_id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "ii", $requester_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Rejected']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed']);
}

mysqli_stmt_close($stmt);
?>