<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar & Footer</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">Assignment #1</div>
        <ul class="nav-links">
            <li><a href="welcome.php">Post</a></li>
            <li><a href="login.html">Login</a></li>
            <li><a href="signout.php">Sign-Out</a></li>
        </ul>
    </nav>

    <!-- MAIN CONTENT -->
    <main>
        <h1 class="accentText">Index Page</h1>
        <p class="accentText"></p>
        <div id="posts"></div>
    </main>

    <script>
        function loadPosts(posts) {
    const postsContainer = document.getElementById("posts");
    postsContainer.innerHTML = ""; // Clear previous posts

    posts.forEach(post => {
        const postElement = document.createElement("div");
        let content = post.body;

        // If content is longer than 100 characters, truncate and add "Read More" button
        if (content.length > 100) {
            content = content.substring(0, 100) + "...";
            content += `<br><a href="display.php?article_id=${post.article_id}" class="read-more">Read More</a>`;
        }

        postElement.innerHTML = `
            <h2>${post.title}</h2>
            <p><strong>By:</strong> ${post.contributor_username} | <strong>Posted on:</strong> ${post.create_date}</p>
            <p>${content}</p>
            <hr>
        `;
        postsContainer.appendChild(postElement);
    });
}


        function getPosts() {
            fetch("homepage.php")
            .then(response => response.json())
            .then(data => {
                loadPosts(data);
            })
            .catch(error => console.error("Error:", error));
        }

        // Call the function when the page loads
        window.onload = getPosts;
    </script>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; Alzen, Daniel, Joseph</p>
    </footer>
</body>
</html>
