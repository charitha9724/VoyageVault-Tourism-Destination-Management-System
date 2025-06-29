<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!isset($_SESSION['user_id'])) {
    header("Location: log.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VoyageVault - Home</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .image-grid img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            object-fit: cover;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .section-title {
            margin-top: 60px;
            margin-bottom: 30px;
        }
        .caption {
            text-align: center;
            margin-top: 8px;
            font-weight: 500;
            font-size: 1rem;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<!-- Random Destinations -->
<div class="container text-center section-title">
    <h2 class="fw-bold">Wander Anywhere</h2>
</div>
<div class="container">
    <div class="row g-4 image-grid">
        <div class="col-md-3 col-sm-6">
            <img src="d1.jpg" alt="Munnar, Kerala">
            <div class="caption">Munnar, Kerala</div>
        </div>
        <div class="col-md-3 col-sm-6">
            <img src="d2.jpg" alt="Goa">
            <div class="caption">Goa</div>
        </div>
        <div class="col-md-3 col-sm-6">
            <img src="d3.jpg" alt="Lonavala">
            <div class="caption">Lonavala</div>
        </div>
        <div class="col-md-3 col-sm-6">
            <img src="d4.jpg" alt="Mussoorie">
            <div class="caption">Mussoorie</div>
        </div>
    </div>
</div>

<!-- Explore Mountains -->
<div class="container text-center section-title">
    <h2 class="fw-bold">Explore Mountains</h2>
</div>
<div class="container">
    <div class="row g-4 image-grid">
        <div class="col-md-3 col-sm-6">
            <img src="m1.avif" alt="Mount Cook - New Zealand">
            <div class="caption">Mount Cook - New Zealand</div>
        </div>
        <div class="col-md-3 col-sm-6">
            <img src="m2.avif" alt="Reinefjorden - Norway">
            <div class="caption">Reinefjorden - Norway</div>
        </div>
        <div class="col-md-3 col-sm-6">
            <img src="m3.avif" alt="Mount Rainier - U.S.">
            <div class="caption">Mount Rainier - U.S.</div>
        </div>
        <div class="col-md-3 col-sm-6">
            <img src="m4.avif" alt="Machapuchare - Nepal">
            <div class="caption">Machapuchare - Nepal</div>
        </div>
    </div>
</div>

<!-- Popular Destinations -->
<div class="container text-center section-title">
    <h2 class="fw-bold">Popular Destinations</h2>
</div>
<div class="container mb-5">
    <div class="row g-4 image-grid">
        <div class="col-md-3 col-sm-6">
            <img src="p1.jpg" alt="Meghalaya">
            <div class="caption">Meghalaya</div>
        </div>
        <div class="col-md-3 col-sm-6">
            <img src="p2.jpg" alt="Andaman">
            <div class="caption">Andaman Island</div>
        </div>
        <div class="col-md-3 col-sm-6">
            <img src="p3.jpg" alt="Uttarakhand">
            <div class="caption">Uttarakhand</div>
        </div>
        <div class="col-md-3 col-sm-6">
            <img src="p4.jpg" alt="Elephant Falls">
            <div class="caption">Elephant Falls</div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
