<?php
include 'new.php'; // Ensure this file contains the database connection
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = trim($_POST['user_name']);
    $email = trim($_POST['email']);
    $contact_no = trim($_POST['contact_no']);
    $passwd = trim($_POST['passwd']);

    // Hash the password before storing it
    $hashed_passwd = password_hash($passwd, PASSWORD_DEFAULT);

    // Insert into user_info
    $sql1 = "INSERT INTO user_info (contact_no) VALUES (?)";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("s", $contact_no);

    if ($stmt1->execute()) {
        $user_id = $conn->insert_id;

        // Insert into user_name
        $sql2 = "INSERT INTO user_name (user_name, user_id) VALUES (?, ?)";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("si", $user_name, $user_id);

        // Insert into user_email
        $sql3 = "INSERT INTO user_email (email, user_id) VALUES (?, ?)";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bind_param("si", $email, $user_id);

        // Insert into user_password
        $sql4 = "INSERT INTO user_password (user_id, passwd) VALUES (?, ?)";
        $stmt4 = $conn->prepare($sql4);
        $stmt4->bind_param("is", $user_id, $hashed_passwd);

        if ($stmt2->execute() && $stmt3->execute() && $stmt4->execute()) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $user_name;
            
            header("Location: home.php"); // Redirect to Home Page
            exit();
        } else {
            echo "Error in one of the insertions: " . $conn->error;
        }

        $stmt2->close();
        $stmt3->close();
        $stmt4->close();
    } else {
        echo "Error inserting into user_info: " . $stmt1->error;
    }

    $stmt1->close();
    $conn->close();
}
?>
