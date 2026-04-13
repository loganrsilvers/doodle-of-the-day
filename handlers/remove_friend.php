<?php
session_start();
require_once "../handlers/connection.php";
require_once "../handlers/friends.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Must be logged in']);
    exit;
}

if (!isset($_POST['friend_id'])) {
    echo json_encode(['success' => false, 'message' => 'No friend ID provided']);
    exit;
}

$user_id = $_SESSION['user_id'];
$friend_id = (int)$_POST['friend_id'];

$result = remove_friend($con, $user_id, $friend_id);
echo json_encode($result);
?>