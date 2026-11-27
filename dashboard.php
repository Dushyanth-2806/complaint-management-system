<?php
require_once 'config.php';
requireLogin();
if ($_SESSION['role'] === 'admin') { header("Location: admin/dashboard.php"); exit(); }

$uid = $_SESSION['user_id'];

// Count complaints per status
$counts = ['Pending' => 0, 'In Progress' => 0, 'Resolved' => 0, 'Rejected' => 0];
$stmt = mysqli_prepare($conn, "SELECT status, COUNT(*) AS c FROM complaints WHERE user_id = ? GROUP BY status");
mysqli_stmt_bind_param($stmt, "i", $uid);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) { $counts[$row['status']] = $row['c']; }
mysqli_stmt_close($stmt);

// Get this user's complaints (newest first)
$stmt = mysqli_prepare($conn, "SELECT id, title, category, priority, status, created_at FROM complaints WHERE user_id = ? ORDER BY created_at DESC");
mysqli_stmt_bind_param($stmt, "i", $uid);
mysqli_stmt_execute($stmt);
$complaints = mysqli_stmt_get_result($stmt);

$pageTitle = "My Complaints";
include 'includes/header.php';
?>

<h1 class="page-title">Hi, <?= e($_SESSION['name']) ?> 👋</h1>

<div class="stats">
    <div class="stat" style="background: var(--yellow);"><h3><?= $counts['Pending'] ?></h3><p>Pending</p></div>
    <div class="stat" style="background: var(--blue);"><h3><?= $counts['In Progress'] ?></h3><p>In Progress</p></div>
    <div class="stat" style="background: var(--green);"><h3><?= $counts['Resolved'] ?></h3><p>Resolved</p></div>
    <div class="stat" style="background: var(--red); color: #fff;"><h3><?= $counts['Rejected'] ?></h3><p>Rejected</p></div>
</div>

<div class="card">
    <div class="card-head">
        <h2>My Complaints</h2>
        <a href="submit_complaint.php" class="btn btn-small">+ New</a>
    </div>

    <?php if (mysqli_num_rows($complaints) === 0): ?>
        <p class="muted">You haven't submitted any complaints yet.</p>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>#</th><th>Title</th><th>Category</th><th>Priority</th><th>Status</th><th>Date</th><th></th></tr>
            </thead>
            <tbody>
            <?php while ($c = mysqli_fetch_assoc($complaints)): ?>
                <tr>
                    <td>#<?= $c['id'] ?></td>
                    <td><?= e($c['title']) ?></td>
                    <td><?= e($c['category']) ?></td>
                    <td><span class="badge <?= priorityClass($c['priority']) ?>"><?= e($c['priority']) ?></span></td>
                    <td><span class="badge <?= statusClass($c['status']) ?>"><?= e($c['status']) ?></span></td>
                    <td><?= date('d M Y', strtotime($c['created_at'])) ?></td>
                    <td><a href="view_complaint.php?id=<?= $c['id'] ?>" class="btn btn-small btn-blue">View</a></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php
mysqli_stmt_close($stmt);
include 'includes/footer.php';
?>
