<?php
include 'new.php'; // database connection

if (isset($_POST['email'])) {
    $email = trim($_POST['email']);
    
    $stmt = $conn->prepare("SELECT 1 FROM user_email WHERE email = ?");
    $stmt->bind_param("s", $email);//This is a method that binds input parameters to the prepared SQL statement.
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "taken";
    } else {
        echo "available";
    }

    $stmt->close();
    $conn->close();
}
?>
