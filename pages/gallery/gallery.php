<?php
session_start();
require_once "../../handlers/connection.php";
require_once "../../handlers/prompt.php";
require_once "../../handlers/get_daily_doodles.php";

// Get date from URL or default to today
$filter_date = $_GET['date'] ?? date('Y-m-d');
$sort = $_GET['sort'] ?? 'newest';

$doodles = get_doodles_for_date($con, $filter_date);
$all_dates = get_all_dates_with_doodles($con);
$doodleCount = count($doodles);

// Sort the doodles
if ($sort === 'oldest') {
    usort($doodles, fn($a, $b) => strtotime($a['created_at']) - strtotime($b['created_at']));
} elseif ($sort === 'most_popular') {
    usort($doodles, fn($a, $b) => $b['likes'] - $a['likes']);
} elseif ($sort === 'least_popular') {
    usort($doodles, fn($a, $b) => $a['likes'] - $b['likes']);
}
// newest is default (already in order)

$daily_prompt = get_daily_prompt();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Doodle Gallery - <?= date('n/j/Y') ?></title>
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="gallery.css">
</head>
<body>
    <h1>Gallery - <?= htmlspecialchars($daily_prompt['full_prompt']) ?> - <?= date('n/j/Y', strtotime($filter_date)) ?></h1>
    <a href="../../index.php">Back Home</a>

    <!-- Date picker -->
    <div class="date-picker">
        <label for="dateFilter">View date:</label>
        <input type="date" id="dateFilter" value="<?= htmlspecialchars($filter_date) ?>" 
               onchange="window.location.href='?date='+this.value+'&sort=<?= $sort ?>'">
    </div>

    <!-- Sort controls -->
    <form method="get" action="gallery.php" style="margin-bottom:15px;">
        <input type="hidden" name="date" value="<?= htmlspecialchars($filter_date) ?>">
        <label for="sort">Sort by:</label>
        <select name="sort" id="sort" onchange="this.form.submit()">
            <option value="newest" <?php if ($sort === 'newest') echo 'selected'; ?>>Newest first</option>
            <option value="oldest" <?php if ($sort === 'oldest') echo 'selected'; ?>>Oldest first</option>
            <option value="most_popular" <?php if ($sort === 'most_popular') echo 'selected'; ?>>Most popular</option>
            <option value="least_popular" <?php if ($sort === 'least_popular') echo 'selected'; ?>>Least popular</option>
        </select>
    </form>

    <p>Doodles submitted: <?= $doodleCount ?></p>
    <p><small>Current prompt: <strong><?= htmlspecialchars($daily_prompt['full_prompt']) ?></strong></small></p>

    <div class="gallery">
        <?php if (empty($doodles)): ?>
            <p>No doodles for this date yet!</p>
        <?php else: ?>
            <?php foreach ($doodles as $doodle): ?>
                <?php
                    $imgBase64 = base64_encode($doodle['image_data']);
                    $imgSrc = 'data:image/png;base64,' . $imgBase64;
                    $userName = $doodle['user_name'] ?? 'Unknown';
                ?>
                <div class="gallery-item">
                    <div class="image-container">
                        <img src="<?= $imgSrc ?>" width="300" alt="Doodle">
                    </div>
                    <p>
                        <a href="../users/user-profile.php?username=<?= htmlspecialchars($userName) ?>">
                            Submitted by: <?= htmlspecialchars($userName) ?>
                        </a><br>
                        ✅ <?= htmlspecialchars($doodle['prompt']) ?><br>
                        <small><?= htmlspecialchars($doodle['created_at']) ?></small>
                    </p>

                    <form method="get" action="like_doodle.php" style="display:inline;">
                        <input type="hidden" name="id" value="<?= (int)$doodle['id'] ?>">
                        <button type="submit">👍 <?= (int)$doodle['likes'] ?></button>
                    </form>

                    <a href="report_doodle.php?id=<?= (int)$doodle['id'] ?>" style="margin-left:10px;">
                        Report this image
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <a href="../../index.php">Back Home</a>

    <script src="../../js/share-functions.js"></script>
</body>
</html>