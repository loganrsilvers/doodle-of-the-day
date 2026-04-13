<?php
session_start();
include("../../handlers/connection.php");
include("../../handlers/functions.php");
include("../../handlers/prompt.php");
include("../../handlers/friends.php");

$user_data = check_login_from_profile($con);
$daily_prompt = get_daily_prompt();
$theme = get_daily_theme();

// Get user's doodles
$user_doodles = get_user_doodles($con, $user_data['user_id']);

// Get friends
$friends = get_user_friends($con, $user_data['user_id']);
$friend_requests = get_pending_friend_requests($con, $user_data['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile - Doodle of the Day</title>
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="profile.css">
    <style>
        body.theme-purple { --theme-color: #9b59b6; }
        body.theme-blue { --theme-color: #3498db; }
        body.theme-green { --theme-color: #27ae60; }
        body.theme-orange { --theme-color: #e67e22; }
        body.theme-red { --theme-color: #e74c3c; }
        body.theme-pink { --theme-color: #ff69b4; }
        body.theme-gold { --theme-color: #f1c40f; }
    </style>
</head>
<body class="theme-<?= $theme ?>">

    <header>
        <h1>My Profile</h1>
        <a href="../../handlers/logout.php" class="logout-link">Logout</a>
    </header>

    <p class="greeting">Hello, <?= htmlspecialchars($user_data['user_name']) ?>!</p>
    
    <!-- Today's prompt -->
    <div class="today-prompt">
        <p class="prompt-label">Today's prompt:</p>
        <h2 class="prompt-word"><?= htmlspecialchars($daily_prompt['full_prompt']) ?></h2>
        <p class="prompt-date"><?= date('l, F j, Y') ?></p>
        <a href="../canvas/canvas.php" class="draw-btn">Start Drawing →</a>
    </div>

    <!-- Friend Requests -->
    <?php if (!empty($friend_requests)): ?>
        <div class="friend-requests">
            <h3>📬 Friend Requests</h3>
            <?php foreach ($friend_requests as $request): ?>
                <p>
                    <?= htmlspecialchars($request['user_name']) ?> wants to be friends
                    <button onclick="acceptFriend(<?= $request['requester_id'] ?>)">Accept</button>
                    <button onclick="rejectFriend(<?= $request['requester_id'] ?>)">Reject</button>
                </p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Friends List -->
    <?php if (!empty($friends)): ?>
        <div class="friends-section">
            <h3>👥 Friends (<?= count($friends) ?>)</h3>
            <div class="friends-grid">
                <?php foreach ($friends as $friend): ?>
                    <div class="friend-card">
                        <a href="../../pages/users/user-profile.php?username=<?= htmlspecialchars($friend['user_name']) ?>">
                            <?= htmlspecialchars($friend['user_name']) ?>
                        </a>
                        <small>Friends since: <?= date('n/j/Y', strtotime($friend['created_at'])) ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Your doodles -->
    <div class="your-doodles">
        <h3>🎨 Your Doodles (<?= count($user_doodles) ?>)</h3>
        
        <?php if (empty($user_doodles)): ?>
            <p>No doodles yet! Click "Start Drawing" above!</p>
        <?php else: ?>
            <div class="gallery">
                <?php foreach (array_slice($user_doodles, 0, 6) as $doodle): ?>
                    <?php
                        $imgBase64 = base64_encode($doodle['image_data']);
                        $imgSrc = 'data:image/png;base64,' . $imgBase64;
                    ?>
                    <div class="gallery-item">
                        <div class="image-container">
                            <img src="<?= $imgSrc ?>" width="300" alt="Doodle">
                        </div>
                        <p>
                            ✅ <?= htmlspecialchars($doodle['prompt']) ?><br>
                            <small><?= htmlspecialchars($doodle['created_at']) ?></small>
                        </p>
                        <p>👍 <?= (int)$doodle['likes'] ?> likes</p>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($user_doodles) > 6): ?>
                <a href="../../pages/gallery/gallery.php?user=<?= urlencode($user_data['user_name']) ?>">
                    View all <?= count($user_doodles) ?> doodles →
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script src="../../js/friends-functions.js"></script>
</body>
</html>