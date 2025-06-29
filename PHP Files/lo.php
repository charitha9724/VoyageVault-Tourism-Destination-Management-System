<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VoyageVault</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <!-- Header -->
<nav class="navbar navbar-light bg-light px-4">
    <a class="navbar-brand fw-bold fs-3">VoyageVault</a>
    <div>
        <a href="log.php" class="btn btn-outline-primary me-2">LOGIN</a>
        <a href="signup.php" class="btn btn-primary">SIGN UP</a>
    </div>
</nav>

<style>
    .hero-image {
    max-height: 400px;
    width: 60%;
    object-fit: cover;
    object-position: center top;
    display: block;
    margin: 0 auto;
}


    @media (min-width: 768px) {
        .hero-image {
            object-position: center 10%;
        }
    }

    @media (min-width: 992px) {
        .hero-image {
            object-position: center 0%;
        }
    }
</style>

<div class="container mt-5">
    <div class="card border-0 shadow-sm p-4 text-center">
        <p class="lead mb-4">
            <strong>Welcome to VoyageVault!</strong><br>
            Your ultimate destination management system, helping you explore and plan your dream trips effortlessly.
        </p>
        <img src="first.jpg" class="img-fluid rounded-4 shadow-lg hero-image" alt="Beautiful Destination">
    </div>
</div>


</body>
</html>
