<?php
session_start();
include 'new.php'; // Ensure this file correctly connects to your database

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Error: User not logged in.";
    exit;
}

$user_id = $_SESSION['user_id']; // Get logged-in user's ID

// Fetch user details

$query = "SELECT 
    un.user_name, 
    ue.email, 
    ui.contact_no
FROM 
    user_name un
JOIN 
    user_email ue ON un.user_id = ue.user_id
JOIN 
    user_info ui ON un.user_id = ui.user_id
WHERE 
    un.user_id = ?";


$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
} else {
    echo "Error: Failed to prepare statement.";
    exit;
}

// Fetch bookings
$query = "SELECT b.booking_id, p.package_name, p.duration, p.cost, 
                 b.start_date, b.end_date, b.booking_status, py.pay_status 
          FROM booking b
          JOIN package p ON b.package_id = p.package_id
          LEFT JOIN payment py ON b.booking_id = py.booking_id
          WHERE b.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$booking_result = $stmt->get_result();
$stmt->close();

// Fetch reviews
$query = "SELECT r.review_id, r.rating, r.r_date, r.comment1, d.destination_name 
          FROM review r
          JOIN destination d ON r.destination_id = d.destination_id
          WHERE r.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$review_result = $stmt->get_result();
$stmt->close();

// Fetch enquiries
$query = "SELECT 
    e.enquiry_id, 
    e.message, 
    e.response
FROM 
    enquiry e
WHERE 
    e.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$enquiry_result = $stmt->get_result();
$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f7fc;
        }
        .container {
            max-width: 900px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            font-weight: bold;
            background-color: #007bff;
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .table {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
        }
        .table th {
            background-color: #007bff;
            color: white;
        }
        .list-group-item {
            background-color: white;
            border-left: 5px solid #007bff;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2 class="fw-bold text-center text-primary">Profile</h2>

    <!-- User Information -->
    <div class="card p-3 mb-4">
        <div class="card-header">User Information</div>
        <div class="card-body">
            <p><strong>Name:</strong> <?= htmlspecialchars($user['user_name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Contact Number:</strong> <?= htmlspecialchars($user['contact_no']) ?></p>
        </div>
    </div>

    <!-- Booking Details -->
    <div class="card p-3 mb-4">
        <div class="card-header">My Bookings</div>
        <div class="card-body">
            <?php if ($booking_result->num_rows > 0): ?>
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Package</th>
                            <th>Duration</th>
                            <th>Cost (₹)</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Payment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($booking = $booking_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $booking['booking_id'] ?></td>
                                <td><?= htmlspecialchars($booking['package_name']) ?></td>
                                <td><?= $booking['duration'] ?> days</td>
                                <td><?= number_format($booking['cost'], 2) ?></td>
                                <td><?= $booking['start_date'] ?></td>
                                <td><?= $booking['end_date'] ?></td>
                                <td><span class="badge bg-info"><?= htmlspecialchars($booking['booking_status']) ?></span></td>
                                <td>
                                    <?php if ($booking['pay_status'] == 'Success'): ?>
                                        <span class="badge bg-success">Paid</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted">No bookings yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- User Reviews -->
    <div class="card p-3 mb-4">
        <div class="card-header">My Reviews</div>
        <div class="card-body">
            <?php if ($review_result->num_rows > 0): ?>
                <ul class="list-group">
                    <?php while ($review = $review_result->fetch_assoc()): ?>
                        <li class="list-group-item">
                            <strong>Destination:</strong> <?= htmlspecialchars($review['destination_name']) ?><br>
                            <strong>Rating:</strong> 
                            <span class="badge bg-warning text-dark"><?= $review['rating'] ?>/5</span><br>
                            <strong>Review Date:</strong> <?= $review['r_date'] ?><br>
                            <strong>Comment:</strong> <?= htmlspecialchars($review['comment1']) ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted">No reviews submitted.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- User Enquiries -->
    <div class="card p-3">
    <div class="card-header">My Enquiries</div>
    <div class="card-body">
        <?php if ($enquiry_result->num_rows > 0): ?>
            <ul class="list-group">
                <?php while ($enquiry = $enquiry_result->fetch_assoc()): ?>
                    <li class="list-group-item">
                        <strong>Enquiry:</strong> <?= htmlspecialchars($enquiry['message']) ?><br>
                        <strong>Response:</strong> 
                        <?= $enquiry['response'] ? htmlspecialchars($enquiry['response']) : '<span class="text-danger">No response yet</span>' ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted">No enquiries submitted.</p>
        <?php endif; ?>
    </div>
</div>


</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
