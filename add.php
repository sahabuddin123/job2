<?php
// add.php - Add a new news item form and submission handler
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
            $successMessage = 'News item successfully added!';
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
    <title>Add News - Daily City Desk</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <div class="site-container">
            <h1><a href="index.php">Daily City Desk</a></h1>
            <nav>
                <a href="index.php">Home</a>
                <a href="add.php" class="active">Add News</a>
            </nav>
        </div>
    </header>

    <main class="site-container">
        <div class="form-card">
            <h2 class="section-title">Add News Item</h2>

            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>
                    <a href="index.php" style="color: #15803d; font-weight: 600; text-decoration: underline; margin-left: 8px;">View on Home Page</a>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul style="margin-left: 20px;">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="add.php" method="POST">
                <div class="form-group">
                    <label for="title">Title *</label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        class="form-control" 
                        placeholder="Enter news title..." 
                        value="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="category_id">Category *</label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['category_id'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo ((string)$categoryId === (string)$cat['category_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['category_name'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="reporter_id">Reporter *</label>
                    <select id="reporter_id" name="reporter_id" class="form-control" required>
                        <option value="">-- Select Reporter --</option>
                        <?php foreach ($reporters as $rep): ?>
                            <option value="<?php echo htmlspecialchars($rep['reporter_id'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo ((string)$reporterId === (string)$rep['reporter_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($rep['full_name'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="published_on">Published Date *</label>
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
                    <label for="description">Description *</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        class="form-control" 
                        rows="5" 
                        placeholder="Enter news description..." 
                        required
                    ><?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%;">Submit News</button>
            </form>
        </div>
    </main>

    <footer>
        <div class="site-container">
            <p class="notice">The text on this website is original demo content and has not been copied from a newspaper.</p>
            <p>&copy; <?php echo date('Y'); ?> Daily City Desk. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
