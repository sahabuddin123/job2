<?php
// index.php - Main news listing page
require_once __DIR__ . '/config.php';

$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    if ($searchTerm !== '') {
        $stmt = $pdo->prepare("
            SELECT n.news_id, n.title, n.description, c.category_name, r.full_name AS reporter_name, n.published_on
            FROM news n
            INNER JOIN category c ON n.category_id = c.category_id
            INNER JOIN reporter r ON n.reporter_id = r.reporter_id
            WHERE n.title LIKE ?
            ORDER BY n.published_on DESC, n.news_id DESC
        ");
        $stmt->execute(['%' . $searchTerm . '%']);
    } else {
        $stmt = $pdo->query("
            SELECT n.news_id, n.title, n.description, c.category_name, r.full_name AS reporter_name, n.published_on
            FROM news n
            INNER JOIN category c ON n.category_id = c.category_id
            INNER JOIN reporter r ON n.reporter_id = r.reporter_id
            ORDER BY n.published_on DESC, n.news_id DESC
        ");
    }
    $newsList = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error retrieving news: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily City Desk</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <div class="site-container">
            <h1><a href="index.php">Daily City Desk</a></h1>
            <nav>
                <a href="index.php" class="active">Home</a>
                <a href="add.php">Add News</a>
            </nav>
        </div>
    </header>

    <main class="site-container">

        <div class="hero-image-wrapper">
            <img src="images/news-image.jpg" alt="Daily City Desk Newsroom Banner">
        </div>

        <section class="search-section">
            <form action="index.php" method="GET" class="search-form">
                <input 
                    type="text" 
                    name="search" 
                    class="search-input" 
                    placeholder="Search news by title..." 
                    value="<?php echo htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8'); ?>"
                >
                <button type="submit" class="btn-primary">Search</button>
                <?php if ($searchTerm !== ''): ?>
                    <a href="index.php" class="btn-secondary">Clear</a>
                <?php endif; ?>
            </form>
        </section>

        <section>
            <h2 class="section-title">
                <?php echo $searchTerm !== '' ? 'Search Results for "' . htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8') . '"' : 'Latest News'; ?>
            </h2>

            <div class="news-list">
                <?php if (!empty($newsList)): ?>
                    <?php foreach ($newsList as $item): ?>
                        <article class="news-card">
                            <div class="news-card-header">
                                <h3 class="news-card-title"><?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                <div class="news-card-meta">
                                    <span class="category-badge"><?php echo htmlspecialchars($item['category_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="reporter-name">Reported by: <strong><?php echo htmlspecialchars($item['reporter_name'], ENT_QUOTES, 'UTF-8'); ?></strong></span>
                                    <time class="published-date"><?php echo htmlspecialchars($item['published_on'], ENT_QUOTES, 'UTF-8'); ?></time>
                                </div>
                            </div>
                            <?php if (!empty($item['description'])): ?>
                                <div class="news-card-body">
                                    <p><?php echo nl2br(htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8')); ?></p>
                                </div>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-news">No news articles found matching your query.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="api-section">
            <div class="api-section-header">
                <h2 class="api-section-title">Loaded from the API</h2>
            </div>
            <ul id="api-news-list" class="api-news-list">
                <li>Loading news titles from API...</li>
            </ul>
        </section>

    </main>

    <footer>
        <div class="site-container">
            <p class="notice">The text on this website is original demo content and has not been copied from a newspaper.</p>
            <p>&copy; <?php echo date('Y'); ?> Daily City Desk. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Load API data on index page using JavaScript fetch()
        fetch('api/news.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const listContainer = document.getElementById('api-news-list');
                listContainer.innerHTML = '';

                if (!Array.isArray(data) || data.length === 0) {
                    listContainer.innerHTML = '<li>No news available from API.</li>';
                    return;
                }

                data.forEach(item => {
                    const li = document.createElement('li');
                    li.className = 'api-news-item';

                    const titleSpan = document.createElement('span');
                    titleSpan.className = 'api-news-title';
                    titleSpan.textContent = item.title;

                    const catSpan = document.createElement('span');
                    catSpan.className = 'category-badge';
                    catSpan.textContent = item.category;

                    li.appendChild(titleSpan);
                    li.appendChild(catSpan);
                    listContainer.appendChild(li);
                });
            })
            .catch(error => {
                console.error('Error fetching API news:', error);
                const listContainer = document.getElementById('api-news-list');
                listContainer.innerHTML = '<li>Error loading news from API.</li>';
            });
    </script>

</body>
</html>
