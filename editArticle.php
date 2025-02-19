<?php
session_start();
$db = new SQLite3('users.db');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

if (!isset($_GET['article_id'])) {
    header('Location: welcome.php');
    exit();
}

$article_id = intval($_GET['article_id']);

$stmt = $db->prepare("SELECT * FROM articles WHERE article_id = :article_id AND contributor_username = :contributor_username");
$stmt->bindValue(':article_id', $article_id, SQLITE3_INTEGER);
$stmt->bindValue(':contributor_username', $username, SQLITE3_TEXT);
$result = $stmt->execute();
$article = $result->fetchArray(SQLITE3_ASSOC);

if (!$article) {
    echo "<div class='container text-center mt-5'><h2 class='text-danger'>Article not found or you don't have permission to edit it.</h2></div>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $db->prepare("UPDATE articles SET title = :title, body = :body WHERE article_id = :article_id AND contributor_username = :username");
    $stmt->bindValue(':title', $title, SQLITE3_TEXT);
    $stmt->bindValue(':body', $content, SQLITE3_TEXT);
    $stmt->bindValue(':article_id', $article_id, SQLITE3_INTEGER);
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $stmt->execute();

    header('Location: welcome.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Article</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="mb-4">Edit Article</h1>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Title:</label>
                <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($article['title']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Content:</label>
                <textarea class="form-control" name="content" required><?php echo htmlspecialchars($article['body']); ?></textarea>
            </div>
            <button type="submit" name="update" class="btn btn-success">Update Article</button>
        </form>
        <a href="welcome.php" class="btn btn-secondary mt-3">Back to Home</a>
    </div>
</body>
</html>
