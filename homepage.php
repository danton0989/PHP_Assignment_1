<?php
    // Connect to SQLite
    $db = new SQLite3('users.db');

    $db->exec("CREATE TABLE IF NOT EXISTS articles (
        article_id INTEGER PRIMARY KEY AUTOINCREMENT, 
        title TEXT NOT NULL, 
        body TEXT NOT NULL,  -- Allows HTML content
        create_date TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Stores UTC timestamp
        start_date TEXT, -- Optional, allows scheduling
        end_date TEXT, -- Optional, allows expiration
        contributor_username TEXT NOT NULL, -- Should be an email
        FOREIGN KEY(contributor_username) REFERENCES users(username) ON DELETE CASCADE
    )");

    // Check if there are any posts
    $result = $db->querySingle("SELECT COUNT(*) as count FROM articles", true);

    // If there are no posts, insert dummy data
    if ($result['count'] == 0) {
        $SQL_insert_data = "
        INSERT INTO articles (title, body, create_date, start_date, end_date, contributor_username)
        VALUES
            ('Post 1', '<p>This is the body of Post 1. It contains some HTML content.</p>', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, DATETIME(CURRENT_TIMESTAMP, '+10 days'), 'tom@domain.com'),
            ('Post 2', '<p>This is the body of Post 2. It contains some more HTML content.</p>', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, DATETIME(CURRENT_TIMESTAMP, '+10 days'), 'ann@domain.com'),
            ('Post 3', '<p>This is the body of Post 3. Here is some additional HTML content.</p>', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, DATETIME(CURRENT_TIMESTAMP, '+10 days'), 'joe@domain.com')
        ";
        // Execute the insert query
        $db->exec($SQL_insert_data);
    }

    $query = "SELECT article_id, title, body, create_date, start_date, end_date, contributor_username 
    FROM articles
    WHERE (start_date IS NULL OR start_date <= DATETIME('now'))
    AND (end_date IS NULL OR end_date >= DATETIME('now'))
    ORDER BY create_date DESC";


    $result = $db->query($query);

    $posts = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $posts[] = $row; // Store each row as an array
    }
    
    echo json_encode($posts); // Convert posts to JSON
?>