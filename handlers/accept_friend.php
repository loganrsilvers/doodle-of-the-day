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

$result = accept_friend_request($con, $user_id, $requester_id);
echo json_encode($result);
?>