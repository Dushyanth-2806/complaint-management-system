<?php
require_once 'config.php';

// Already logged in? No need to register.
if (isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$errors = [];
$name = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    // 1. Validate input
    if ($name === '' || strlen($name) < 2)             $errors[] = "Please enter your full name.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))    $errors[] = "Please enter a valid email address.";
    if (strlen($password) < 6)                         $errors[] = "Password must be at least 6 characters.";
    if ($password !== $confirm)                        $errors[] = "Passwords do not match.";

    // 2. Is the email already used?
    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) $errors[] = "That email is already registered.";
        mysqli_stmt_close($stmt);
    }

    // 3. Save the user (password is HASHED, never stored as plain text)
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hash);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: login.php?registered=1");
        exit();
    }
}

$pageTitle = "Register";
include 'includes/header.php';
?>

<div class="auth-box">
    <div class="card">
        <h2>Create Account</h2>

        <?php foreach ($errors as $err): ?>
            <div class="alert alert-error"><?= e($err) ?></div>
        <?php endforeach; ?>

        <form method="POST" data-validate>
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="<?= e($name) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= e($email) ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" minlength="6" required>
            </div>
            <div class="form-group">
                <label for="confirm">Confirm Password</label>
                <input type="password" id="confirm" name="confirm" required>
            </div>
            <button type="submit" class="btn">Register</button>
        </form>

        <p class="muted">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
