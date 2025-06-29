<?php
include 'new.php';
//send data to server
if (isset($_POST['user_name'])) {
    $user_name = trim($_POST['user_name']);
    $stmt = $conn->prepare("SELECT * FROM user_name WHERE user_name = ?");
    $stmt->bind_param("s", $user_name);
    $stmt->execute();
    $result = $stmt->get_result();

    echo $result->num_rows > 0 ? 'taken' : 'available';//sends status back to AJAX call

    $stmt->close();//close statement
    $conn->close();//close db connection
}
?>
