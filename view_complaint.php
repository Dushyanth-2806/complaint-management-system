<?php
require_once 'config.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
$isAdmin = ($_SESSION['role'] === 'admin');

// Users can only open THEIR OWN complaints. Admins can open any.
if ($isAdmin) {
    $stmt = mysqli_prepare($conn, "SELECT c.*, u.name AS user_name, u.email AS user_email FROM complaints c JOIN users u ON u.id = c.user_id WHERE c.id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
} else {
    $stmt = mysqli_prepare($conn, "SELECT c.*, u.name AS user_name, u.email AS user_email FROM complaints c JOIN users u ON u.id = c.user_id WHERE c.id = ? AND c.user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $id, $_SESSION['user_id']);
}
mysqli_stmt_execute($stmt);
$c = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

$pageTitle = "Complaint #" . $id;
include 'includes/header.php';

if (!$c): ?>
    <div class="alert alert-error">Complaint not found, or you don't have permission to see it.</div>
    <a href="dashboard.php" class="btn">Back</a>
<?php else: ?>

<?php if (isset($_GET['new'])): ?>
    <div class="alert alert-success">Complaint submitted! We'll respond soon.</div>
<?php endif; ?>

<div class="card">
    <div class="card-head">
        <h2>#<?= $c['id'] ?> — <?= e($c['title']) ?></h2>
        <span class="badge <?= statusClass($c['status']) ?>"><?= e($c['status']) ?></span>
    </div>

    <div class="meta">
        <div><b>Category</b><br><?= e($c['category']) ?></div>
        <div><b>Priority</b><br><span class="badge <?= priorityClass($c['priority']) ?>"><?= e($c['priority']) ?></span></div>
        <div><b>Submitted</b><br><?= date('d M Y, h:i A', strtotime($c['created_at'])) ?></div>
        <div><b>Last Update</b><br><?= date('d M Y, h:i A', strtotime($c['updated_at'])) ?></div>
        <?php if ($isAdmin): ?>
            <div><b>From</b><br><?= e($c['user_name']) ?><br><small><?= e($c['user_email']) ?></small></div>
        <?php endif; ?>
    </div>

    <h3>Description</h3>
    <div class="text-box"><?= nl2br(e($c['description'])) ?></div>
</div>

<div class="card response-card">
    <h3>Admin Response</h3>
    <?php if ($c['admin_response']): ?>
        <div class="text-box response-box"><?= nl2br(e($c['admin_response'])) ?></div>
    <?php else: ?>
        <p class="muted">No response yet. Please check back later.</p>
    <?php endif; ?>
</div>

<?php if ($isAdmin): ?>
    <a href="admin/respond.php?id=<?= $c['id'] ?>" class="btn btn-green">Respond / Change Status</a>
    <a href="admin/dashboard.php" class="btn btn-blue">Back to Admin Panel</a>
<?php else: ?>
    <a href="dashboard.php" class="btn btn-blue">Back to My Complaints</a>
<?php endif; ?>

<?php endif;
include 'includes/footer.php'; ?>
