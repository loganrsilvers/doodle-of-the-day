<?php
session_start();
require_once "../../handlers/connection.php";
require_once "../../handlers/functions.php";
require_once "../../handlers/friends.php";

// Get username from URL
$username = $_GET['username'] ?? '';

if (empty($username)) {
    header("Location: ../../index.php");
    exit;
}

// Get user data
$profile_user = get_user_by_username($con, $username);

if (!$profile_user) {
    header("Location: ../../index.php?error=user_not_found");
    exit;
}

$user_data = get_user_if_logged_in($con);
$is_logged_in = isset($user_data);
$is_own_profile = $is_logged_in && $user_data['user_id'] == $profile_user['user_id'];
$is_friend = false;

if ($is_logged_in && !$is_own_profile) {
    $is_friend = is_friend($con, $user_data['user_id'], $profile_user['user_id']);
}

// Get all doodles from this user
$user_doodles = get_user_doodles($con, $profile_user['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($profile_user['user_name']) ?>'s Profile - Doodle of the Day</title>
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="user-profile.css">
</head>
<body>
    <header>
        <h1><?= htmlspecialchars($profile_user['user_name']) ?>'s Profile</h1>
        <?php if ($is_logged_in): ?>
            <a href="../../index.php" class="back-link">← Back to Home</a>
            <a href="../../handlers/logout.php" class="logout-link">Logout</a>
        <?php else: ?>
            <a href="../../login.php" class="login-link">Login</a>
        <?php endif; ?>
    </header>

    <div class="profile-header">
        <h2><?= htmlspecialchars($profile_user['user_name']) ?></h2>
        <p class="doodle-count">Total doodles: <?= count($user_doodles) ?></p>
        
        <?php if ($is_logged_in && !$is_own_profile): ?>
            <?php if ($is_friend): ?>
                <button class="friend-btn" onclick="removeFriend(<?= $profile_user['user_id'] ?>)">
                    ✓ Friends
                </button>
            <?php else: ?>
                <button class="friend-btn" onclick="addFriend(<?= $profile_user['user_id'] ?>)">
                    + Add Friend
                </button>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <h2>All Doodles</h2>
    
    <?php if (empty($user_doodles)): ?>
        <p class="no-doodles">No doodles yet!</p>
    <?php else: ?>
        <div class="gallery">
            <?php foreach ($user_doodles as $doodle): ?>
                <?php
                    $imgBase64 = base64_encode($doodle['image_data']);
                    $imgSrc = 'data:image/png;base64,' . $imgBase64;
                ?>
                <div class="gallery-item">
                    <div class="image-container">
                        <img src="<?= $imgSrc ?>" width="300" alt="Doodle">
                    </div>
                    <p>
                        <strong>✅ <?= htmlspecialchars($doodle['prompt']) ?></strong><br>
                        <small><?= htmlspecialchars($doodle['created_at']) ?></small>
                    </p>
                    
                    <form method="get" action="../gallery/like_doodle.php" style="display:inline;">
                        <input type="hidden" name="id" value="<?= (int)$doodle['id'] ?>">
                        <button type="submit">👍 <?= (int)$doodle['likes'] ?></button>
                    </form>
                    
                    <button class="share-btn" onclick="shareDoodle(<?= $doodle['id'] ?>, '<?= htmlspecialchars($profile_user['user_name']) ?>', <?= (int)$doodle['id'] ?>)">
                        🔗 Share
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <script src="../../js/share-functions.js"></script>
    <script src="../../js/friends-functions.js"></script>
</body>
</html>