<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'new.php'; // Include database connection

if (!isset($_SESSION['user_id'])) {
    header("Location: log.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Handling form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = trim($_POST['message']);

    if (!empty($message)) {
        $response = 'Pending'; // Default response for the enquiry

        // Insert into enquiry table with 'Pending' as the response
        $stmt = $conn->prepare("INSERT INTO enquiry (message, response, user_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $message, $response, $user_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Enquiry submitted successfully.";
        } else {
            $_SESSION['error'] = "Error submitting enquiry: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Message cannot be empty!";
    }

    header("Location: enq.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VoyageVault - Enquiries</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2 class="fw-bold text-center">Submit Your Enquiry</h2>

    <!-- Display success/error messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php elseif (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form method="POST" action="" class="w-50 mx-auto mt-4">
        <div class="mb-3">
            <label for="message" class="form-label">Message:</label>
            <textarea id="message" name="message" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-100">Submit</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
