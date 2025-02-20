<?php
session_start();

$response = [
    "loggedIn" => false,
    "isApproved" => false,
    "isAdmin" => false
];

if (!isset($_SESSION['user_id'])) {
    echo json_encode($response);
    exit();
} else {
    $response["loggedIn"] = true;
}

$db = new SQLite3('users.db');

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$stmt = $db->prepare("SELECT is_approved, role FROM users WHERE id = :id");
$stmt->bindValue(':id', $user_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);

if (!$user || $user['is_approved'] == 0) {
    echo json_encode($response);
    exit();
}

$response["isApproved"] = $user['is_approved'] == 1;
$response["isAdmin"] = $user['role'] === 'Admin';
echo json_encode($response);
exit();
?>