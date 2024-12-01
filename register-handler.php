<?php
// Database connection details for XAMPP localhost
 include('db.php'); 

// Retrieve and sanitize form data
$contact = $conn->real_escape_string($_POST['contact']); // Prevent SQL Injection
$password = $conn->real_escape_string($_POST['password']); // Prevent SQL Injection
$status = "Active"; // Default status value
$balance = "0.00"; 
$dailyincome= "0.00"; 
$todays_com= "0.00"; 
$total_com= "0.00"; 
// Check if invitecode is provided and sanitize
$invitecode = isset($_POST['invitecode']) ? $conn->real_escape_string($_POST['invitecode']) : NULL;

// Function to generate a unique 10-digit referral number
function generateReferalNumber($conn) {
    $referal = rand(1000000000, 9999999999); // Generate a 10-digit number

    // Check if the referral number already exists
    $sql_check_referal = "SELECT * FROM user WHERE referal = '$referal'";
    $result = $conn->query($sql_check_referal); 

    // If the referral number exists, regenerate it
    while ($result->num_rows > 0) {
        $referal = rand(1000000000, 9999999999);
        $result = $conn->query($sql_check_referal); // Check again with the new referral number
    }

    return $referal;
}

// Generate a unique referral number
$referal = generateReferalNumber($conn);

// Check if the contact already exists
$sql_check = "SELECT * FROM user WHERE contact = '$contact'";
$result = $conn->query($sql_check);

if ($result->num_rows > 0) {
    // Contact is already registered
    header("Location: registerduplicate.html"); // Redirect to duplicate registration page
} else {
  $dateregistered = date('Y-m-d'); // Get current date and time

$sql = "INSERT INTO user (contact, password, status, referal, invitecode, balance, dateregistered, dailyincome, todays_com, total_com) 
        VALUES ('$contact', '$password', '$status', '$referal', '$invitecode', '$dateregistered', '$balance', '$dailyincome', '$todays_com', '$total_com')";


    // Execute the query
    if ($conn->query($sql) === TRUE) {
        // Redirect to Success page after successful insertion
        header("Location: Success.html"); // Redirect to success page
        exit(); // Ensure the rest of the code doesn't run
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the connection
$conn->close();
?>
