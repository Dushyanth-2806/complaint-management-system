<?php
// Start session (remembers who is logged in)
session_start();

// Database settings (XAMPP defaults)
$host = "db";
$user = "root";
$pass = "rootpass";            // XAMPP default password is empty
$db   = "complaint_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");

// Complaint categories (edit this list to fit your college / apartment / company)
$CATEGORIES = ['Infrastructure', 'Hostel / Housing', 'Academics', 'Canteen / Food', 'Security', 'Cleanliness', 'IT / Network', 'Other'];

// Helper: block pages if user isn't logged in
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

// Helper: block pages if user isn't admin (used inside /admin/)
function requireAdmin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../login.php");
        exit();
    }
}

// Helper: safely print text (prevents XSS attacks)
function e($text) {
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}

// Helper: CSS class for a status badge
function statusClass($status) {
    switch ($status) {
        case 'In Progress': return 'badge-progress';
        case 'Resolved':    return 'badge-resolved';
        case 'Rejected':    return 'badge-rejected';
        default:            return 'badge-pending';
    }
}

// Helper: CSS class for a priority badge
function priorityClass($p) {
    return 'badge-' . strtolower($p);
}
?>
