<?php
// index.php - Daily Subarnachar Main News Portal
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
    <title>Daily Subarnachar - News Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Top Status Bar -->
    <div class="top-sub-bar">
        <div class="site-container top-bar-inner">
            <div class="live-badge">
                <span class="live-dot"></span>
                <span>Live News Portal</span>
            </div>
            <div>
                <span>Edition: Subarnachar | <?php echo date('l, d F Y'); ?></span>
            </div>
        </div>
    </div>

    <header>
        <div class="site-container header-brand-row">
            <div>
                <h1><a href="index.php">Daily Subarnachar</a></h1>
                <p class="header-tagline">Authentic Regional News & Digital Media Network</p>
            </div>
            <nav>
                <a href="index.php" class="active">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Home
                </a>
                <a href="add.php">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                    Add News
                </a>
            </nav>
        </div>
    </header>

    <main class="site-container">

        <div class="hero-image-wrapper">
            <img src="images/news-image.jpg" alt="Daily Subarnachar Official News Portal Banner">
        </div>

        <section class="search-section">
            <form action="index.php" method="GET" class="search-form">
                <div class="search-input-wrap">
                    <svg class="search-icon-svg" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input 
                        type="text" 
                        name="search" 
                        class="search-input" 
                        placeholder="Search news by title (e.g. Flood Alert)..." 
                        value="<?php echo htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8'); ?>"
                    >
                </div>
                <button type="submit" class="btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    Search
                </button>
                <?php if ($searchTerm !== ''): ?>
                    <a href="index.php" class="btn-secondary">Clear Search</a>
                <?php endif; ?>
            </form>
        </section>

        <section>
            <div class="section-header-row">
                <h2 class="section-title">
                    <?php echo $searchTerm !== '' ? 'Search Results for "' . htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8') . '"' : 'Latest News Feed'; ?>
                </h2>
                <span class="article-count-tag"><?php echo count($newsList); ?> article(s) found</span>
            </div>

            <div class="news-list">
                <?php if (!empty($newsList)): ?>
                    <?php foreach ($newsList as $item): ?>
                        <article class="news-card">
                            <div class="news-card-header">
                                <div class="news-card-meta">
                                    <span class="category-badge"><?php echo htmlspecialchars($item['category_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="reporter-badge">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                        Reported by: <strong><?php echo htmlspecialchars($item['reporter_name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                    </span>
                                    <time class="published-date">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                        <?php echo htmlspecialchars($item['published_on'], ENT_QUOTES, 'UTF-8'); ?>
                                    </time>
                                </div>
                                <h3 class="news-card-title"><?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            </div>
                            <?php if (!empty($item['description'])): ?>
                                <div class="news-card-body">
                                    <p><?php echo nl2br(htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8')); ?></p>
                                </div>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-news">No news articles found matching your search term.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Visibly Separate API Section -->
        <section class="api-section">
            <div class="api-section-header">
                <h2 class="api-section-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                    Loaded from the API
                </h2>
                <span class="api-pill">GET /api/news.php</span>
            </div>
            <ul id="api-news-list" class="api-news-list">
                <li style="color: #64748b; font-style: italic;">Fetching live news data from API...</li>
            </ul>
        </section>

    </main>

    <footer>
        <div class="site-container">
            <p class="notice">The text on this website is original demo content and has not been copied from a newspaper.</p>
            <p class="copyright">&copy; <?php echo date('Y'); ?> Daily Subarnachar. All rights reserved.</p>
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
                listContainer.innerHTML = '<li style="color: #e11d48;">Error loading news from API.</li>';
            });
    </script>

</body>
</html>
