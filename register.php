<?php
$db = new SQLite3('users.db');
$db->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT, 
    username TEXT UNIQUE NOT NULL, 
    password TEXT NOT NULL, 
    first_name TEXT NOT NULL, 
    last_name TEXT NOT NULL, 
    registration_date TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_approved INTEGER NOT NULL DEFAULT 0,
    role TEXT CHECK(role IN ('Admin', 'Contributor')) NOT NULL DEFAULT 'Contributor'
)");

function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function is_valid_password($password) {
    return preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^\w\s]).{8,}$/', $password);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];

    if (!is_valid_email($username)) {
        echo "Invalid email address.";
        exit();
    }

    if (!is_valid_password($password)) {
        echo "Password must be at least 8 characters long and contain at least one upper case letter, one lower case letter, one numeric character, and one special character.";
        exit();
    }

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
