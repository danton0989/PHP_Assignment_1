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
    article_id INTEGER PRIMARY KEY AUTOINCREMENT, 
    title TEXT NOT NULL, 
    body TEXT NOT NULL, 
    create_date TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    start_date TEXT, 
    end_date TEXT, 
    contributor_username TEXT NOT NULL, 
    FOREIGN KEY(contributor_username) REFERENCES users(username) ON DELETE CASCADE
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
    
    $stmt = $db->prepare("INSERT INTO articles (title, body, create_date, start_date, end_date, contributor_username) VALUES (:title, :body, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, DATETIME(CURRENT_TIMESTAMP, '+3 days'), :contributor_username)");
    $stmt->bindValue(':title', $title, SQLITE3_TEXT);
    $stmt->bindValue(':body', $content, SQLITE3_TEXT);
    $stmt->bindValue(':contributor_username', $username, SQLITE3_TEXT);
    $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $article_id = $_POST['article_id'];
    $stmt = $db->prepare("DELETE FROM articles WHERE article_id = :article_id AND contributor_username = :contributor_username");
    $stmt->bindValue(':article_id', $article_id, SQLITE3_INTEGER);
    $stmt->bindValue(':contributor_username', $username, SQLITE3_TEXT);
    $stmt->execute();
}

$stmt = $db->prepare("SELECT articles.article_id, articles.title, articles.body, articles.create_date, users.first_name, users.last_name 
                      FROM articles 
                      JOIN users ON articles.contributor_username = users.username 
                      WHERE articles.contributor_username = :username
                      ORDER BY articles.create_date DESC"); 
$stmt->bindValue(':username', $username, SQLITE3_TEXT);
$articles = $stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">Welcome</div>
        <ul class="nav-links" id="nav-links">
            
        </ul>
    </nav>

    
    <div class="container mt-5">
        <h1 class="accentText mb-4">Welcome, <?php echo htmlspecialchars($username); ?></h1>
        <h2 class="text">Write a New Article</h2>
        <form method="POST" class="form mb-4">
            <div class="form-group">
                <input type="text" class="form-control" name="title" placeholder="Article Title" required>
            </div>
            <div class="form-group">
                <textarea class="form-control" name="content" placeholder="Write your article here..." required></textarea>
            </div>
            <button type="submit" name="create" class="btn">Publish</button>
        </form>

        <h2 class="text">All Articles</h2>
        <?php while ($article = $articles->fetchArray(SQLITE3_ASSOC)): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h3 class="card-title"><?php echo htmlspecialchars($article['title']); ?></h3>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($article['body'])); ?></p>
                    <p class="text-muted">By <?php echo htmlspecialchars($article['first_name']); ?> <?php echo htmlspecialchars($article['last_name']); ?> on <?php echo $article['create_date']; ?></p>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>">
                        <button type="submit" name="delete" class="btn" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                    <a href="editArticle.php?article_id=<?php echo $article['article_id']; ?>" class="btn">Edit</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
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
                if (data.isAdmin) {
                    document.getElementById("nav-links").innerHTML += `<li><a href="admin.php">Admin</a></li>`;
                }
                document.getElementById("nav-links").innerHTML += `<li><a href="signout.php">Logout</a></li>`;
            }
        })
        .catch(error => console.error("Error:", error));
    }

    window.onload = getLoginStatus();
</script>
</html>