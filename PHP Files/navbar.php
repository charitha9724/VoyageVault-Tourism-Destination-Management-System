<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light px-4">
    <a class="navbar-brand fw-bold fs-3" href="home.php">VoyageVault</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="discover.php">Discover</a></li>
            <li class="nav-item"><a class="nav-link" href="package.php">Packages</a></li>
            <li class="nav-item"><a class="nav-link" href="review.php">Review</a></li>
            <li class="nav-item"><a class="nav-link" href="enq.php">Enquiry</a></li>
            <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>

            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item"><a class="nav-link text-danger fw-bold" href="logout.php">Logout</a></li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link text-primary fw-bold" href="log.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
