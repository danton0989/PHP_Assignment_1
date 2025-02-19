<?php
session_start();
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

$db->exec("CREATE TABLE IF NOT EXISTS articles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    content TEXT NOT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$stmt = $db->prepare("SELECT is_approved FROM users WHERE id = :id");
$stmt->bindValue(':id', $user_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);

if (!$user || $user['is_approved'] == 0) {
    echo "<div class='container text-center mt-5'><h2 class='text-warning'>Waiting for approval</h2></div>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $db->prepare("INSERT INTO articles (user_id, title, content, created_at) VALUES (:user_id, :title, :content, CURRENT_TIMESTAMP)");
    $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
    $stmt->bindValue(':title', $title, SQLITE3_TEXT);
    $stmt->bindValue(':content', $content, SQLITE3_TEXT);
    $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $article_id = $_POST['article_id'];

    $stmt = $db->prepare("DELETE FROM articles WHERE id = :id AND user_id = :user_id");
    $stmt->bindValue(':id', $article_id, SQLITE3_INTEGER);
    $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
    $stmt->execute();
}

$articles = $db->query("SELECT articles.id, articles.title, articles.content, articles.created_at, users.username 
                        FROM articles 
                        JOIN users ON articles.user_id = users.id 
                        ORDER BY articles.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="mb-4">Welcome, <?php echo htmlspecialchars($username); ?></h1>

        <h2>Write a New Article</h2>
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <input type="text" class="form-control" name="title" placeholder="Article Title" required>
            </div>
            <div class="mb-3">
                <textarea class="form-control" name="content" placeholder="Write your article here..." required></textarea>
            </div>
            <button type="submit" name="create" class="btn btn-primary">Publish</button>
        </form>

        <h2>All Articles</h2>
        <?php while ($article = $articles->fetchArray(SQLITE3_ASSOC)): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h3 class="card-title"><?php echo htmlspecialchars($article['title']); ?></h3>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
                    <p class="text-muted">By <?php echo htmlspecialchars($article['username']); ?> on <?php echo $article['created_at']; ?></p>

                    <?php if ($article['username'] == $username): ?>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">
                            <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>

                        <a href="editArticle.php?article_id=<?php echo $article['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
