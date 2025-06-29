<?php
include 'new.php'; // Database connection
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in to submit a review.'); window.location.href='log.php';</script>";
    exit();
}

// Fetch destinations for dropdown
$destination_query = "SELECT destination_id, destination_name FROM DESTINATION";
$destination_result = $conn->query($destination_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Review</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <h2 class="text-center">Submit Your Review</h2>
    <form action="submit_r.php" method="POST">
        <div class="mb-3">
            <label for="destination" class="form-label">Select Destination:</label>
            <select name="destination_id" id="destination" class="form-control" required>
                <option value="">Choose a destination</option>
                <?php while ($row = $destination_result->fetch_assoc()) { ?>
                    <option value="<?php echo $row['destination_id']; ?>">
                        <?php echo htmlspecialchars($row['destination_name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="rating" class="form-label">Rating (1-5):</label>
            <input type="number" name="rating" id="rating" class="form-control" min="1" max="5" required>
        </div>

        <div class="mb-3">
            <label for="comment" class="form-label">Write your review:</label>
            <textarea name="comment" id="comment" class="form-control" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit Review</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
