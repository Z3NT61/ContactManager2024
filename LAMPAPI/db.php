<?php
// Database connection settings
$servername = "localhost"; // Replace with your database server name
$username = "root"; // Replace with your database username
$password = "b+YXZI98+xeB"; // Replace with your database password
$dbname = "SPROJECTDB"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
