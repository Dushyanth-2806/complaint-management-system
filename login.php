<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare($conn, "SELECT id, name, password, role FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);           // security: new session id after login
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['role']    = $user['role'];

        header("Location: " . ($user['role'] === 'admin' ? "admin/dashboard.php" : "dashboard.php"));
        exit();
    } else {
        $error = "Wrong email or password.";
    }
}

$pageTitle = "Login";
include 'includes/header.php';
?>

<div class="auth-box">
    <div class="card">
        <h2>Login</h2>

        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success">Account created! Please login.</div>
        <?php endif; ?>
        <?php if (isset($_GET['loggedout'])): ?>
            <div class="alert alert-success">You have been logged out.</div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= e($email) ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-blue">Login</button>
        </form>

        <p class="muted">New here? <a href="register.php">Create an account</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
