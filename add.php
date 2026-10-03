<?php
// add.php - Daily Subarnachar Add News Item
require_once __DIR__ . '/config.php';

$title = '';
$description = '';
$categoryId = '';
$reporterId = '';
$published_on = date('Y-m-d');
$errors = [];
$successMessage = '';

// Retrieve available categories and reporters for dropdowns
try {
    $categories = $pdo->query("SELECT category_id, category_name FROM category ORDER BY category_name ASC")->fetchAll();
    $reporters = $pdo->query("SELECT reporter_id, full_name FROM reporter ORDER BY full_name ASC")->fetchAll();
} catch (PDOException $e) {
    die("Error loading form options: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $categoryId = isset($_POST['category_id']) ? trim($_POST['category_id']) : '';
    $reporterId = isset($_POST['reporter_id']) ? trim($_POST['reporter_id']) : '';
    $published_on = isset($_POST['published_on']) ? trim($_POST['published_on']) : '';

    // Validate the submitted data
    if ($title === '') {
        $errors[] = 'Title is required.';
    }
    if ($description === '') {
        $errors[] = 'Description is required.';
    }
    if ($categoryId === '') {
        $errors[] = 'Category is required.';
    }
    if ($reporterId === '') {
        $errors[] = 'Reporter is required.';
    }
    if ($published_on === '') {
        $errors[] = 'Published date is required.';
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $published_on)) {
        $errors[] = 'Published date must be in YYYY-MM-DD format.';
    }

    // Insert into database using prepared statement if no errors
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO news (title, description, category_id, reporter_id, published_on) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $categoryId, $reporterId, $published_on]);
            $successMessage = 'News article successfully published to Daily Subarnachar!';
            // Clear form fields
            $title = '';
            $description = '';
            $categoryId = '';
            $reporterId = '';
            $published_on = date('Y-m-d');
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publish News - Daily Subarnachar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Top Status Bar -->
    <div class="top-sub-bar">
        <div class="site-container top-bar-inner">
            <div class="live-badge">
                <span class="live-dot"></span>
                <span>Editorial Portal</span>
            </div>
            <div>
                <span>Desk: Subarnachar | <?php echo date('l, d F Y'); ?></span>
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
                <a href="index.php">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Home
                </a>
                <a href="add.php" class="active">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                    Add News
                </a>
            </nav>
        </div>
    </header>

    <main class="site-container">
        <div class="form-card">
            <div class="form-header">
                <h2>Add New Article</h2>
                <p>Submit a new verified news story to the Daily Subarnachar portal database.</p>
            </div>

            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    <div>
                        <strong>Success:</strong> <?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>
                        <a href="index.php" style="color: #047857; font-weight: 700; text-decoration: underline; margin-left: 8px;">View on Live Portal &rarr;</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <ul style="margin-left: 14px;">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="add.php" method="POST">
                <div class="form-group">
                    <label for="title">News Headline / Title *</label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        class="form-control" 
                        placeholder="Enter headline (e.g. Subarnachar Agriculture Fair Begins)..." 
                        value="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="category_id">News Category *</label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">-- Choose Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['category_id'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo ((string)$categoryId === (string)$cat['category_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['category_name'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="reporter_id">Assigned Reporter *</label>
                    <select id="reporter_id" name="reporter_id" class="form-control" required>
                        <option value="">-- Choose Reporter --</option>
                        <?php foreach ($reporters as $rep): ?>
                            <option value="<?php echo htmlspecialchars($rep['reporter_id'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo ((string)$reporterId === (string)$rep['reporter_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($rep['full_name'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="published_on">Publication Date *</label>
                    <input 
                        type="date" 
                        id="published_on" 
                        name="published_on" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($published_on, ENT_QUOTES, 'UTF-8'); ?>" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="description">Article Description / Story Content *</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        class="form-control" 
                        rows="5" 
                        placeholder="Write detailed original news story content here..." 
                        required
                    ><?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 14px; font-size: 1rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    Publish News Article
                </button>
            </form>
        </div>
    </main>

    <footer>
        <div class="site-container">
            <p class="notice">The text on this website is original demo content and has not been copied from a newspaper.</p>
            <p class="copyright">&copy; <?php echo date('Y'); ?> Daily Subarnachar. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
