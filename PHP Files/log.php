<?php
session_start();
include 'new.php';

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = trim($_POST['user_name']);
    $passwd = trim($_POST['passwd']);

    $sql = "SELECT un.user_id, un.user_name, up.passwd 
        FROM user_name un 
        JOIN user_password up ON un.user_id = up.user_id 
        WHERE un.user_name = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($passwd, $user['passwd'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['user_name'];
            header("Location: home.php");
            exit();
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container d-flex flex-column align-items-center justify-content-center vh-100">
        <h2 class="fw-bold mb-4">LOGIN PAGE</h2>

        <form action="log.php" method="POST" class="w-50">

            <?php if (!empty($error_message)) : ?>
                <div class="alert alert-danger py-1 px-2 text-center">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="user_name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="passwd" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</body>
</html>
