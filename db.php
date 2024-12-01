<?php
// Database configuration
$servername = "localhost";  // Typically localhost for local development
$username = "root";         // Your MySQL username (default is 'root' for local)
$password = "";             // Your MySQL password (default is empty for local)
$dbname = "questvaultdb";  // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    // You can print a message or log the connection if needed
    // echo "Connected successfully";
}
?>
