<?php
require_once '../config.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
$saved = false;
$error = '';

// ---- Handle the form: UPDATE status + response ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status   = $_POST['status'] ?? '';
    $response = trim($_POST['admin_response'] ?? '');

    if (!in_array($status, ['Pending','In Progress','Resolved','Rejected'])) {
        $error = "Invalid status.";
    } elseif (in_array($status, ['Resolved','Rejected']) && $response === '') {
        $error = "Please write a response before marking a complaint Resolved or Rejected.";
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE complaints SET status = ?, admin_response = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $status, $response, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $saved = true;
    }
}

// ---- Load the complaint ----
$stmt = mysqli_prepare($conn, "SELECT c.*, u.name AS user_name, u.email AS user_email FROM complaints c JOIN users u ON u.id = c.user_id WHERE c.id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$c = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

$base = '../';
$pageTitle = "Respond #" . $id;
include '../includes/header.php';

if (!$c): ?>
    <div class="alert alert-error">Complaint not found.</div>
    <a href="dashboard.php" class="btn">Back</a>
<?php else: ?>

<?php if ($saved): ?><div class="alert alert-success">Complaint updated successfully.</div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

<div class="card">
    <div class="card-head">
        <h2>#<?= $c['id'] ?> — <?= e($c['title']) ?></h2>
        <span class="badge <?= statusClass($c['status']) ?>"><?= e($c['status']) ?></span>
    </div>
    <div class="meta">
        <div><b>From</b><br><?= e($c['user_name']) ?><br><small><?= e($c['user_email']) ?></small></div>
        <div><b>Category</b><br><?= e($c['category']) ?></div>
        <div><b>Priority</b><br><span class="badge <?= priorityClass($c['priority']) ?>"><?= e($c['priority']) ?></span></div>
        <div><b>Submitted</b><br><?= date('d M Y, h:i A', strtotime($c['created_at'])) ?></div>
    </div>
    <h3>Description</h3>
    <div class="text-box"><?= nl2br(e($c['description'])) ?></div>
</div>

<div class="card">
    <h2>Your Response</h2>
    <form method="POST">
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status">
                <?php foreach (['Pending','In Progress','Resolved','Rejected'] as $s): ?>
                    <option <?= $c['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="admin_response">Message to user</label>
            <textarea id="admin_response" name="admin_response" placeholder="Explain what action was taken..."><?= e($c['admin_response']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-green">Save Update</button>
        <a href="dashboard.php" class="btn btn-blue">Back</a>
    </form>
</div>

<?php endif;
include '../includes/footer.php'; ?>
