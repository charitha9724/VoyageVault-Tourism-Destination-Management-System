<?php
session_start();
require 'new.php'; // Database connection

if (!isset($_SESSION['user_id']) || !isset($_GET['package_id'])) {
    header("Location: log.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$package_id = intval($_GET['package_id']);

// Fetch package details for payment amount
$package_query = "SELECT cost, duration FROM PACKAGE WHERE package_id = ?";
$stmt = $conn->prepare($package_query);
$stmt->bind_param("i", $package_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Package not found!";
    exit();
}

$package = $result->fetch_assoc();
$cost = $package['cost'];
$duration = $package['duration'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Simulated card details (In real-world apps, integrate a payment gateway)
    $card_number = $_POST['card_number'];
    $card_holder = $_POST['card_holder'];
    $expiry_date = $_POST['expiry_date'];
    $cvv = $_POST['cvv'];

    // Process booking & payment
    $booking_date = date('Y-m-d');
    $start_date = date('Y-m-d', strtotime('+1 day'));
    $end_date = date('Y-m-d', strtotime("+$duration days", strtotime($start_date)));

    // Insert into BOOKING table
    $insert_booking = "INSERT INTO BOOKING (booking_date, start_date, end_date, booking_status, user_id, package_id) 
                       VALUES (?, ?, ?, 'Confirmed', ?, ?)";
    $stmt = $conn->prepare($insert_booking);
    $stmt->bind_param("sssii", $booking_date, $start_date, $end_date, $user_id, $package_id);
    
    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error;
        exit();
    }

    $booking_id = $conn->insert_id;

    // Insert into PAYMENT table with 'Pending' status
    $insert_payment = "INSERT INTO PAYMENT (pay_amt, pay_date, pay_status, user_id, booking_id) 
                       VALUES (?, CURDATE(), 'Pending', ?, ?)";
    $stmt = $conn->prepare($insert_payment);
    $stmt->bind_param("dii", $cost, $user_id, $booking_id);
    
    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error;
        exit();
    }

    // Simulating payment success
    $update_payment = "UPDATE PAYMENT SET pay_status = 'Success' WHERE booking_id = ?";
    $stmt = $conn->prepare($update_payment);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();

    // Redirect to home.php after payment
    echo "<script>alert('Payment successful! Your booking is confirmed.'); window.location.href='home.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2 class="fw-bold text-center">Payment for Booking</h2>
    <p><strong>Amount to Pay:</strong> ₹<?= number_format($cost, 2) ?></p>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="card_number" class="form-label">Card Number:</label>
            <input type="text" id="card_number" name="card_number" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="card_holder" class="form-label">Card Holder Name:</label>
            <input type="text" id="card_holder" name="card_holder" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label for="cvv" class="form-label">CVV:</label>
            <input type="password" id="cvv" name="cvv" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Make Payment</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
