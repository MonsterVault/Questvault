<?php
// Database configuration
$servername = "darkseagreen-quail-635236.hostingersite.com";  // Typically localhost for local development
$username = "u645382831_questvaultuser";         // Your MySQL username (default is 'root' for local)
$password = "6f/Zcr:Y";             // Your MySQL password (default is empty for local)
$dbname = "u645382831_questvault";  // Replace with your actual database name

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
