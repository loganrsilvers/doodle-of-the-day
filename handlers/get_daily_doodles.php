<?php
// handlers/get_daily_doodles.php - Gets doodles from a specific date

function get_doodles_for_date($con, $date = null, $limit = 50) {
    if ($date === null) {
        $date = date('Y-m-d'); // Default to today
    }
    
    $query = "
        SELECT d.id, d.image_data, d.created_at, d.likes, d.prompt, u.user_name, u.user_id
        FROM doodles d
        LEFT JOIN users u ON d.user_id = u.user_id
        WHERE DATE(d.created_at) = ?
        ORDER BY d.created_at DESC
        LIMIT ?
    ";
    
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "si", $date, $limit);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $doodles = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $doodles[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $doodles;
}

function get_all_dates_with_doodles($con) {
    $query = "
        SELECT DATE(created_at) as date, COUNT(*) as count
        FROM doodles
        GROUP BY DATE(created_at)
        ORDER BY date DESC
    ";
    
    $result = mysqli_query($con, $query);
    $dates = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $dates[] = $row;
    }
    
    return $dates;
}

function get_doodle_count_for_date($con, $date) {
    $query = "SELECT COUNT(*) as count FROM doodles WHERE DATE(created_at) = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $date);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    return $row['count'];
}
?>