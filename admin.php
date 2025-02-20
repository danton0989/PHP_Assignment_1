<?php
session_start();
$db = new SQLite3('users.db');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    die("Access denied.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve'])) {
        $stmt = $db->prepare('UPDATE users SET is_approved = 1 WHERE id = :id');
        $stmt->bindValue(':id', $_POST['user_id'], SQLITE3_INTEGER);
        $stmt->execute();
    } elseif (isset($_POST['unapprove'])) {
        $stmt = $db->prepare('UPDATE users SET is_approved = 0 WHERE id = :id');
        $stmt->bindValue(':id', $_POST['user_id'], SQLITE3_INTEGER);
        $stmt->execute();
    } elseif (isset($_POST['promote'])) {
        $stmt = $db->prepare('UPDATE users SET role = "Admin" WHERE id = :id');
        $stmt->bindValue(':id', $_POST['user_id'], SQLITE3_INTEGER);
        $stmt->execute();
    } elseif (isset($_POST['demote'])) {
        $stmt = $db->prepare('UPDATE users SET role = "Contributor" WHERE id = :id');
        $stmt->bindValue(':id', $_POST['user_id'], SQLITE3_INTEGER);
        $stmt->execute();
    }
}

$users = $db->query('SELECT id, username, role, is_approved FROM users');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-light">
    <nav class="navbar">
        <div class="logo">Welcome</div>
        <ul class="nav-links" id="nav-links">
            
        </ul>
    </nav>

    <div class="container mt-5">
        <h2 class="mb-4 text-center">Admin Panel - User Management</h2>

        <table class="table table-bordered table-striped text-center">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Approved</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetchArray(SQLITE3_ASSOC)): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td>
                            <span class="badge bg-<?= $user['role'] === 'Admin' ? 'success' : 'secondary' ?>">
                                <?= htmlspecialchars($user['role']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?= $user['is_approved'] ? 'primary' : 'warning' ?>">
                                <?= $user['is_approved'] ? 'Yes' : 'No' ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <form method="POST">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <?php if ($user['is_approved']): ?>
                                        <button type="submit" name="unapprove" class="btn btn-warning btn-sm">Unapprove</button>
                                    <?php else: ?>
                                        <button type="submit" name="approve" class="btn btn-primary btn-sm">Approve</button>
                                    <?php endif; ?>
                                </form>

                                <form method="POST">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <?php if ($user['role'] === 'Admin'): ?>
                                        <button type="submit" name="demote" class="btn btn-danger btn-sm">Demote</button>
                                    <?php else: ?>
                                        <button type="submit" name="promote" class="btn btn-success btn-sm">Promote</button>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Footer -->
    <footer class="footer">
        <p>&copy; Alzen, Daniel, Joseph</p>
    </footer>
</body>
<script>
    function getLoginStatus() {
        fetch("loginStatus.php")
        .then(response => response.json())
        .then(data => {
            if (data.loggedIn) {
                document.getElementById("nav-links").innerHTML = `<li><a href="/">Home</a></li>`;
                if (data.isApproved) {
                    document.getElementById("nav-links").innerHTML += `<li><a href="welcome.php">Post</a></li>`;
                }
                document.getElementById("nav-links").innerHTML += `<li><a href="signout.php">Logout</a></li>`;
            }
        })
        .catch(error => console.error("Error:", error));
    }

    window.onload = getLoginStatus();
</script>
</html>
