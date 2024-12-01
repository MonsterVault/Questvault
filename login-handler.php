<?php
// Database connection details for XAMPP localhost
include('db.php');

// Sanitize the form input
$user = $conn->real_escape_string($_POST['username']);
$pass = $conn->real_escape_string($_POST['password']);

// Check if the username and password are for the admin account
if ($user === 'questvaultadmin' && $pass === 'questvaultadminpassword') {
    // Admin login successful
    session_start(); // Start a session for the admin
    $_SESSION['username'] = $user; // Store username in session for further use
    
    // Redirect to the admin page
    header("Location: adminshgcbfnsdjey58668947363jdfjsksnfjshdfnkeo3938.php");
    exit(); // Ensure the script stops after redirection
} else {
    // SQL Query to check if the username, password, and status are correct
    $sql = "SELECT * FROM user WHERE contact = '$user' AND password = '$pass' AND status = 'Active'";
    $result = $conn->query($sql);

    // Check if a matching user with active status was found
    if ($result->num_rows > 0) {
        // User found and status is Active, login successful
        session_start(); // Start a session for the user
        $_SESSION['username'] = $user; // Store username in session for further use
        
        // Fetch the user's referral ID from the database
        $user_data = $result->fetch_assoc();
        $referal = $user_data['referal']; // Replace 'referal' with the actual field name from your Users table
        $_SESSION['referal'] = $referal;

        // Redirect to the default page with the referral ID appended
        header("Location: Main.php?ref=" . $referal);
        exit(); // Ensure the script stops after redirection
    } else {
        // User not found or status is not active, login failed
        header("Location: invalid.html");
    }
}

// Close the connection
$conn->close();
?>
