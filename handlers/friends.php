<?php
// handlers/friends.php - Friends system functions

function add_friend($con, $user_id, $friend_id) {
    if ($user_id == $friend_id) {
        return ['success' => false, 'message' => 'You cannot add yourself as a friend!'];
    }
    
    // Check if friends table exists first
    $table_check = mysqli_query($con, "SHOW TABLES LIKE 'friends'");
    if (!$table_check || mysqli_num_rows($table_check) === 0) {
        return ['success' => false, 'message' => 'Friends system not set up yet. Contact admin.'];
    }
    
    $query = "INSERT INTO friends (user_id, friend_id, status) VALUES (?, ?, 'pending') 
              ON DUPLICATE KEY UPDATE status = 'pending'";
    
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        return ['success' => false, 'message' => 'Database error: ' . mysqli_error($con)];
    }
    
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $friend_id);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['success' => true, 'message' => 'Friend request sent!'];
    }
    
    mysqli_stmt_close($stmt);
    return ['success' => false, 'message' => 'Failed to add friend'];
}

function remove_friend($con, $user_id, $friend_id) {
    // Check if friends table exists
    $table_check = mysqli_query($con, "SHOW TABLES LIKE 'friends'");
    if (!$table_check || mysqli_num_rows($table_check) === 0) {
        return ['success' => false, 'message' => 'Friends system not set up yet.'];
    }
    
    $query = "DELETE FROM friends WHERE user_id = ? AND friend_id = ?";
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        return ['success' => false, 'message' => 'Database error: ' . mysqli_error($con)];
    }
    
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $friend_id);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return ['success' => true, 'message' => 'Friend removed'];
    }
    
    mysqli_stmt_close($stmt);
    return ['success' => false, 'message' => 'Failed to remove friend'];
}

function get_user_friends($con, $user_id) {
    // Check if friends table exists
    $table_check = mysqli_query($con, "SHOW TABLES LIKE 'friends'");
    if (!$table_check || mysqli_num_rows($table_check) === 0) {
        return []; // Return empty array if table doesn't exist
    }
    
    $query = "
        SELECT f.friend_id, u.user_name, u.user_id, f.status, f.created_at
        FROM friends f
        JOIN users u ON f.friend_id = u.user_id
        WHERE f.user_id = ? AND f.status = 'accepted'
    ";
    
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        return []; // Return empty array on error
    }
    
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $friends = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $friends[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $friends;
}

function get_pending_friend_requests($con, $user_id) {
    // Check if friends table exists
    $table_check = mysqli_query($con, "SHOW TABLES LIKE 'friends'");
    if (!$table_check || mysqli_num_rows($table_check) === 0) {
        return []; // Return empty array if table doesn't exist
    }
    
    $query = "
        SELECT f.user_id, u.user_name, u.user_id as requester_id, f.status, f.created_at
        FROM friends f
        JOIN users u ON f.user_id = u.user_id
        WHERE f.friend_id = ? AND f.status = 'pending'
    ";
    
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        return []; // Return empty array on error
    }
    
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $requests = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $requests[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $requests;
}

function accept_friend_request($con, $user_id, $requester_id) {
    // Check if friends table exists
    $table_check = mysqli_query($con, "SHOW TABLES LIKE 'friends'");
    if (!$table_check || mysqli_num_rows($table_check) === 0) {
        return ['success' => false, 'message' => 'Friends system not set up yet.'];
    }
    
    $query = "UPDATE friends SET status = 'accepted' WHERE user_id = ? AND friend_id = ?";
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        return ['success' => false, 'message' => 'Database error'];
    }
    
    mysqli_stmt_bind_param($stmt, "ii", $requester_id, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    // Also create reverse friendship
    $query2 = "INSERT INTO friends (user_id, friend_id, status) VALUES (?, ?, 'accepted') 
               ON DUPLICATE KEY UPDATE status = 'accepted'";
    $stmt2 = mysqli_prepare($con, $query2);
    if (!$stmt2) {
        return ['success' => false, 'message' => 'Database error'];
    }
    
    mysqli_stmt_bind_param($stmt2, "ii", $user_id, $requester_id);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);
    
    return ['success' => true, 'message' => 'Friend accepted!'];
}

function is_friend($con, $user_id, $other_user_id) {
    // Check if friends table exists
    $table_check = mysqli_query($con, "SHOW TABLES LIKE 'friends'");
    if (!$table_check || mysqli_num_rows($table_check) === 0) {
        return false;
    }
    
    $query = "SELECT id FROM friends 
              WHERE (user_id = ? AND friend_id = ? AND status = 'accepted') 
              OR (user_id = ? AND friend_id = ? AND status = 'accepted')
              LIMIT 1";
    
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, "iiii", $user_id, $other_user_id, $other_user_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $is_friend = mysqli_num_rows($result) > 0;
    mysqli_stmt_close($stmt);
    
    return $is_friend;
}

function get_user_by_username($con, $username) {
    $query = "SELECT * FROM users WHERE user_name = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        return null;
    }
    
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $user;
    }
    
    mysqli_stmt_close($stmt);
    return null;
}

function get_user_doodles($con, $user_id, $limit = 100) {
    $query = "
        SELECT d.id, d.image_data, d.created_at, d.likes, d.prompt, u.user_name
        FROM doodles d
        JOIN users u ON d.user_id = u.user_id
        WHERE d.user_id = ?
        ORDER BY d.created_at DESC
        LIMIT ?
    ";
    
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        return [];
    }
    
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $limit);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $doodles = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $doodles[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    return $doodles;
}
?>