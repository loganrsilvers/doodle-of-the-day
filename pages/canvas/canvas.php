<?php
session_start();
include("../../handlers/connection.php");
include("../../handlers/functions.php");
include("../../handlers/prompt.php");

$user_data = check_login($con);
$daily_prompt = get_daily_prompt();
$theme = get_daily_theme();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doodle of the Day - Draw</title>
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="canvas.css">
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
<body class="theme-<?= htmlspecialchars($theme) ?>">

    <header>
        <h1>Doodle of the Day</h1>
        <a href="../profile/index.php">Back to Profile</a>
        <a href="../../handlers/logout.php" class="logout-link">Logout</a>
    </header>

    <p class="greeting">Hello, <?= htmlspecialchars($user_data['user_name']) ?>!</p>
    <p class="prompt-label">Today you will doodle a:</p>
    <h2 class="prompt-word"><?= htmlspecialchars($daily_prompt['full_prompt']) ?></h2>

    <div id="toolbar">
        <button type="button" id="undoBtn">↩ Undo</button>
        <div id="colorPalette">
            <button type="button" class="color-btn active" data-color="#c0392b" style="background:#c0392b;"></button>
            <button type="button" class="color-btn" data-color="#000000" style="background:#000000;"></button>
            <button type="button" class="color-btn" data-color="#3498db" style="background:#3498db;"></button>
            <button type="button" class="color-btn" data-color="#27ae60" style="background:#27ae60;"></button>
            <button type="button" class="color-btn" data-color="#f1c40f" style="background:#f1c40f;"></button>
        </div>
    </div>

    <div id="canvasContainer">
        <canvas id="drawingBoard" width="480" height="480">
            Your browser does not support the canvas element.
        </canvas>
        <div id="knob-row">
            <div class="knob"></div>
            <div class="knob"></div>
        </div>
    </div>

    <form id="doodleForm" method="post" action="../../handlers/submit_doodle.php">
        <input type="hidden" name="doodle_image" id="doodle_image">
        <button type="button" id="submitDoodleBtn">Submit Doodle</button>
    </form>

    <script src="../../js/canvas.js"></script>
</body>
</html>