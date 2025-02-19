<?php
// Connect to the database
$db = new SQLite3('users.db');

// Start output buffering to handle errors gracefully
ob_start();

// Check if the 'article_id' is in the URL and is a valid integer
if (isset($_GET['article_id']) && is_numeric($_GET['article_id'])) {
    $article_id = intval($_GET['article_id']); // Convert to integer for safety

    // Prepare and execute the SQL query
    $stmt = $db->prepare("SELECT * FROM articles WHERE article_id = :article_id");
    $stmt->bindValue(':article_id', $article_id, SQLITE3_INTEGER);
    $result = $stmt->execute();

    // Fetch the article
    $article = $result->fetchArray(SQLITE3_ASSOC);

    if (!$article) {
        $error_message = "Article Not Found.";
    }
} else {
    $error_message = "No article selected.";
}

// End output buffering and flush output
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($article) ? htmlspecialchars($article['title']) : "Article"; ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Ensure footer stays at the bottom */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        .content-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 50px 20px;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 800px;
            text-align: center;
        }

        .error-message {
            color: red;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">Assignment #1</div>
        <ul class="nav-links">
            <li><a href="index.html">Home</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="content-wrapper">
        <div class="container">
            <?php if (isset($error_message)) : ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php else : ?>
                <h1 class="accentText"><?php echo htmlspecialchars($article['title']); ?></h1>
                <p><strong>By:</strong> <span class="text"><?php echo htmlspecialchars($article['contributor_username']); ?></span> | <strong>Posted on:</strong> <span class="text"><?php echo htmlspecialchars($article['create_date']); ?></span></p>
                <hr>
                <p class="text"><?php echo nl2br(htmlspecialchars($article['body'])); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>© <?php echo date("Y"); ?> My Blog. All rights reserved.</p>
    </footer>

</body>
</html>
