<?php
session_start();
include 'new.php';

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = trim($_POST['user_name']);
    $passwd = trim($_POST['passwd']);

    // Step 1: Get user_id from user_name table
    $sql = "SELECT user_id FROM user_name WHERE user_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $user_id = $row['user_id'];
        $stmt->close();

        // Step 2: Get password from user_password table
        $sql_pass = "SELECT passwd FROM user_password WHERE user_id = ?";
        $stmt = $conn->prepare($sql_pass);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result_pass = $stmt->get_result();

        if ($result_pass->num_rows == 1) {
            $row_pass = $result_pass->fetch_assoc();
            if (password_verify($passwd, $row_pass['passwd'])) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $user_name;
                header("Location: home.php");
                exit();
            } else {
                $error_message = "Invalid username or password!";
            }
        } else {
            $error_message = "Invalid username or password!";
        }
    } else {
        $error_message = "Invalid username or password!";
    }

    $stmt->close();
    $conn->close();
}
?>
