<?php
require_once __DIR__ . '/../config.php';

// $base lets pages inside /admin/ find the CSS and links ('../')
if (!isset($base)) { $base = ''; }
if (!isset($pageTitle)) { $pageTitle = 'Complaint Desk'; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | Complaint Desk</title>
    <link rel="stylesheet" href="<?= $base ?>assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <a href="<?= $base ?>index.php" class="logo">[ COMPLAINT_DESK ]</a>

    <div class="nav-links">
        <a href="<?= $base ?>index.php">Home</a>

        <?php if (isset($_SESSION['user_id'])): ?>

            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="<?= $base ?>admin/dashboard.php">Admin Panel</a>
            <?php else: ?>
                <a href="<?= $base ?>dashboard.php">My Complaints</a>
                <a href="<?= $base ?>submit_complaint.php">New Complaint</a>
            <?php endif; ?>

            <a href="<?= $base ?>logout.php">Logout (<?= e($_SESSION['name']) ?>)</a>

        <?php else: ?>
            <a href="<?= $base ?>login.php">Login</a>
            <a href="<?= $base ?>register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>

<main class="container">
