<?php
$pageTitle = "Home";
include 'includes/header.php';
?>

<section class="hero">
    <h1>Got a problem?<br>Let's <span>fix it.</span></h1>
    <p>Submit your complaint, watch its status live, and get a real response from the admin. No more lost complaints.</p>

    <?php if (isset($_SESSION['user_id'])): ?>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="admin/dashboard.php" class="btn">Open Admin Panel</a>
        <?php else: ?>
            <a href="submit_complaint.php" class="btn">Submit a Complaint</a>
            <a href="dashboard.php" class="btn btn-blue">Track My Complaints</a>
        <?php endif; ?>
    <?php else: ?>
        <a href="register.php" class="btn">Get Started</a>
        <a href="login.php" class="btn btn-blue">Login</a>
    <?php endif; ?>
</section>

<section class="steps">
    <div class="step-card" style="background: var(--yellow);">
        <div class="step-num">01</div>
        <h3>Submit</h3>
        <p>Describe your issue, pick a category and priority.</p>
    </div>
    <div class="step-card" style="background: var(--pink);">
        <div class="step-num">02</div>
        <h3>Track</h3>
        <p>See if it's Pending, In Progress, Resolved or Rejected.</p>
    </div>
    <div class="step-card" style="background: var(--green);">
        <div class="step-num">03</div>
        <h3>Get a Reply</h3>
        <p>Read the admin's response right on your dashboard.</p>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
