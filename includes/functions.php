<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isOwner() {
    if (!isLoggedIn()) return false;

    global $conn;
    $uid = $_SESSION['user_id'];

    // Check if session ID exists
    if (!$uid) {
        error_log("No user ID in session.");
        return false;
    }

    // Query the database
    $res = $conn->query("SELECT * FROM users WHERE id = " . intval($uid));
    if ($res && $res->num_rows > 0) {
        $user = $res->fetch_assoc();
        error_log("User found: ID - $uid, Role - " . $user['role']); // Debug log

        return ($user['role'] === 'owner'); // Check for 'owner' role
    }

    error_log("User not found or query failed for ID: $uid.");
    return false;
}



function redirect($url) {
    header("Location: $url");
    exit;
}
?>
