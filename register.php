<?php
$db = new SQLite3('users.db');
$db->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT, 
    username TEXT UNIQUE NOT NULL, 
    password TEXT NOT NULL, 
    first_name TEXT NOT NULL, 
    last_name TEXT NOT NULL, 
    registration_date TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP, --CURRENT_TIMESTAMP stores UTC time (YYYY-MM-DD HH:MM:SS)
    is_approved INTEGER NOT NULL DEFAULT 0, -- 0 = not approved, 1 = approved
    role TEXT CHECK(role IN ('Admin', 'Contributor')) NOT NULL DEFAULT 'Contributor'
)");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $db->prepare('INSERT INTO users (username, password, first_name, last_name, registration_date, is_approved, role) 
    VALUES (:username, :password, :first_name, :last_name, CURRENT_TIMESTAMP, 0, "Contributor")');
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $stmt->bindValue(':password', $hashed_password, SQLITE3_TEXT);
    $stmt->bindValue(':first_name', $first_name, SQLITE3_TEXT);
    $stmt->bindValue(':last_name', $last_name, SQLITE3_TEXT);
    $stmt->execute();

    header('Location: login.html');
}
?>
