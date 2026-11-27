<?php
require_once '../config.php';
requireAdmin();

// ---- Stats ----
$counts = ['Pending' => 0, 'In Progress' => 0, 'Resolved' => 0, 'Rejected' => 0];
$res = mysqli_query($conn, "SELECT status, COUNT(*) AS c FROM complaints GROUP BY status");
while ($row = mysqli_fetch_assoc($res)) { $counts[$row['status']] = $row['c']; }
$total = array_sum($counts);

// ---- Filters (from the URL: ?status=...&category=...&q=...) ----
$fStatus   = $_GET['status'] ?? '';
$fCategory = $_GET['category'] ?? '';
$q         = trim($_GET['q'] ?? '');

$sql = "SELECT c.id, c.title, c.category, c.priority, c.status, c.created_at, u.name AS user_name
        FROM complaints c JOIN users u ON u.id = c.user_id WHERE 1=1";
$types = '';
$params = [];

if (in_array($fStatus, ['Pending','In Progress','Resolved','Rejected'])) {
    $sql .= " AND c.status = ?"; $types .= 's'; $params[] = $fStatus;
}
if (in_array($fCategory, $CATEGORIES)) {
    $sql .= " AND c.category = ?"; $types .= 's'; $params[] = $fCategory;
}
if ($q !== '') {
    $sql .= " AND (c.title LIKE ? OR u.name LIKE ?)"; $types .= 'ss';
    $like = "%$q%"; $params[] = $like; $params[] = $like;
}
// High priority first, then newest
$sql .= " ORDER BY FIELD(c.priority,'High','Medium','Low'), c.created_at DESC";

$stmt = mysqli_prepare($conn, $sql);
if ($types !== '') { mysqli_stmt_bind_param($stmt, $types, ...$params); }
mysqli_stmt_execute($stmt);
$complaints = mysqli_stmt_get_result($stmt);

$base = '../';
$pageTitle = "Admin Panel";
include '../includes/header.php';
?>

<h1 class="page-title">Admin Panel</h1>

<div class="stats">
    <div class="stat" style="background: var(--white);"><h3><?= $total ?></h3><p>Total</p></div>
    <div class="stat" style="background: var(--yellow);"><h3><?= $counts['Pending'] ?></h3><p>Pending</p></div>
    <div class="stat" style="background: var(--blue);"><h3><?= $counts['In Progress'] ?></h3><p>In Progress</p></div>
    <div class="stat" style="background: var(--green);"><h3><?= $counts['Resolved'] ?></h3><p>Resolved</p></div>
    <div class="stat" style="background: var(--red); color:#fff;"><h3><?= $counts['Rejected'] ?></h3><p>Rejected</p></div>
</div>

<div class="card">
    <h2>All Complaints</h2>

    <form method="GET" class="filters">
        <input type="text" name="q" placeholder="Search title or user..." value="<?= e($q) ?>">
        <select name="status">
            <option value="">All Status</option>
            <?php foreach (['Pending','In Progress','Resolved','Rejected'] as $s): ?>
                <option <?= $fStatus === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>
        <select name="category">
            <option value="">All Categories</option>
            <?php foreach ($CATEGORIES as $c): ?>
                <option <?= $fCategory === $c ? 'selected' : '' ?>><?= e($c) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-small btn-blue" type="submit">Filter</button>
        <a class="btn btn-small" href="dashboard.php">Reset</a>
    </form>

    <?php if (mysqli_num_rows($complaints) === 0): ?>
        <p class="muted">No complaints match your filters.</p>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>#</th><th>Title</th><th>User</th><th>Category</th><th>Priority</th><th>Status</th><th>Date</th><th></th></tr>
            </thead>
            <tbody>
            <?php while ($c = mysqli_fetch_assoc($complaints)): ?>
                <tr>
                    <td>#<?= $c['id'] ?></td>
                    <td><?= e($c['title']) ?></td>
                    <td><?= e($c['user_name']) ?></td>
                    <td><?= e($c['category']) ?></td>
                    <td><span class="badge <?= priorityClass($c['priority']) ?>"><?= e($c['priority']) ?></span></td>
                    <td><span class="badge <?= statusClass($c['status']) ?>"><?= e($c['status']) ?></span></td>
                    <td><?= date('d M Y', strtotime($c['created_at'])) ?></td>
                    <td><a href="respond.php?id=<?= $c['id'] ?>" class="btn btn-small btn-green">Respond</a></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php
mysqli_stmt_close($stmt);
include '../includes/footer.php';
?>
