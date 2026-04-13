<?php
session_start();
require_once "connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("No doodle selected.");
}

$doodle_id = (int) $_GET['id'];

// Increase likes by 1
$query = "UPDATE doodles SET likes = likes + 1 WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $doodle_id);
$stmt->execute();

// Redirect back to gallery
header("Location: gallery.php");
exit;
