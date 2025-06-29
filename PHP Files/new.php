<?php
$servername = "localhost";
$username = "root"; // Change if needed
$password = "Rama136*"; // Set your MySQL root password here
$dbname = "VoyageVault";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
