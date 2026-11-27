<?php
require_once 'config.php';
requireLogin();

// Admins don't submit complaints
if ($_SESSION['role'] === 'admin') { header("Location: admin/dashboard.php"); exit(); }

$errors = [];
$title = $category = $description = '';
$priority = 'Medium';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $category    = $_POST['category'] ?? '';
    $priority    = $_POST['priority'] ?? 'Medium';
    $description = trim($_POST['description'] ?? '');

    if (strlen($title) < 5)                           $errors[] = "Title must be at least 5 characters.";
    if (!in_array($category, $CATEGORIES))            $errors[] = "Please choose a valid category.";
    if (!in_array($priority, ['Low','Medium','High'])) $errors[] = "Invalid priority.";
    if (strlen($description) < 15)                    $errors[] = "Please describe the problem in at least 15 characters.";

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO complaints (user_id, title, category, description, priority) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "issss", $_SESSION['user_id'], $title, $category, $description, $priority);
        mysqli_stmt_execute($stmt);
        $newId = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        header("Location: view_complaint.php?id=$newId&new=1");
        exit();
    }
}

$pageTitle = "New Complaint";
include 'includes/header.php';
?>

<div class="card">
    <h2>Submit a Complaint</h2>

    <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?= e($err) ?></div>
    <?php endforeach; ?>

    <form method="POST" data-validate>
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" maxlength="150" value="<?= e($title) ?>" placeholder="Short summary of the problem" required>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" required>
                    <option value="">-- Select --</option>
                    <?php foreach ($CATEGORIES as $c): ?>
                        <option value="<?= e($c) ?>" <?= $category === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="priority">Priority</label>
                <select id="priority" name="priority">
                    <?php foreach (['Low','Medium','High'] as $p): ?>
                        <option value="<?= $p ?>" <?= $priority === $p ? 'selected' : '' ?>><?= $p ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" maxlength="2000" data-counter="desc-count" placeholder="Explain what happened, where, and when..." required><?= e($description) ?></textarea>
            <small class="muted"><span id="desc-count">0</span> / 2000</small>
        </div>

        <button type="submit" class="btn">Submit Complaint</button>
        <a href="dashboard.php" class="btn btn-blue">Cancel</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
