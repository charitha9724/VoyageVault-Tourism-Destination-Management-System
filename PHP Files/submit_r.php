<?php
include 'new.php'; // Database connection
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in to submit a review.'); window.location.href='login.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $destination_id = $_POST['destination_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $r_date = date('Y-m-d'); // Current date

    // Insert review into the REVIEW table
    $stmt = $conn->prepare("INSERT INTO REVIEW (rating, r_date, comment1, user_id, destination_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issii", $rating, $r_date, $comment, $user_id, $destination_id);

    if ($stmt->execute()) {
        echo "<script>alert('Review submitted successfully!'); window.location.href='discover.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request!";
}
?>
