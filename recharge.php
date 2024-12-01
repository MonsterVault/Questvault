<?php
session_start(); // Start the session

include('db.php');

// Set the time zone to Manila (Asia/Manila)
date_default_timezone_set('Asia/Manila'); 

// Get the URL parameters
$referal = isset($_GET['ref']) ? $conn->real_escape_string($_GET['ref']) : null;
$amount = isset($_GET['amount']) ? $conn->real_escape_string($_GET['amount']) : null;
$contact = isset($_GET['contact']) ? $conn->real_escape_string($_GET['contact']) : null;
$datetime = date("Y-m-d H:i:s"); // Current date and time

// Validate parameters
if ($referal && $amount && $contact) {
    // Fetch the user's contact based on the referral code
    $user_sql = "SELECT contact FROM user WHERE referal = '$referal'";
    $user_result = $conn->query($user_sql);

    if ($user_result->num_rows > 0) {
        // Get the contact from the result (assuming 'contact' is the column you're interested in)
        $user_data = $user_result->fetch_assoc();
        $username = $user_data['contact'];  // Assign 'contact' value as 'username'

        // Insert data into the recharge table
        $sql = "INSERT INTO recharge (referal, amount, contact, datetime, username) VALUES ('$referal', '$amount', '$contact', '$datetime', '$username')";
        
        if ($conn->query($sql) === TRUE) {
            // Data successfully inserted, you can redirect or show a success message here
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Error: Referral not found!";
    }
} else {
    // Redirect if any of the required parameters are missing
    header("Location: index.php?error=missing_data");
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centered Image and Button</title>
    <style>
        /* General reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    height: 100vh;
    margin: 0;
}

/* Navigation Bar */
.navbar {
    width: 100%;
    background-color: #007dfe;
    color: white;
    display: flex; /* Enables flexbox */
    justify-content: center; /* Centers content horizontally */
  
    padding: 15px;
    font-size: 24px;
    font-weight: bold;
    height: 10%;
}

/* Style for the logo */
.navbar img {
    height: 30px; /* Adjust as needed */
    width: 100px; /* Maintain aspect ratio */
    margin-right: 10px; /* Space between the image and text */
}


.container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative; /* Position the container relative to the body */
    top: -40px; /* Move it up to overlap the navigation bar */
    margin-top: 0; /* Remove the default margin */
    padding: 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    width: 400px;

    /* Background Image */
    background-image: url('ImageS/gcash.jpg');
    background-size: cover;
    background-position: center;
    color: white; /* Text color for contrast */
    z-index: 10; /* Ensure it appears above the navigation bar */
}


/* Container */
.container2 {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin-top: 10px;
    padding: 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    width: 350px;
    background-color: #00009e;
}

/* Image */
.image {
    width: 350px;
    height: auto;
    margin-bottom: 20px;
}

/* Button */
.btn {
    padding: 10px 20px;
    background-color: #00009e;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    width: 350px;
}

.btn:hover {
    background-color: #1e90ff;
}

    </style>
</head>
<body>
   <nav class="navbar">
    <img src="Images/gcashlogo.jpg" alt="Gcash Logo">
   
</nav>
    <div class="container">
      <p style="font-family: Arial ; font-size: 14px; font-weight: bold;">
    Upload or Scan QRcode on Gcash
</p>
       
        <img style="margin-top: 10px;" src="https://via.placeholder.com/150" alt="Sample Image" class="image">
         <p>
          <p style="font-family: Arial; font-size: 14px; font-weight: bold; text-align: center;">
   If having trouble please screenshot the QR code and open GCash App
</p>
       
       <button class="btn" style="margin-top: 10px;" onclick="redirectToSave()">Open Gcash App</button>


        <h3 style="margin-top: 20px;">Payment information</h3>
         <div class="container2">
               <h3 style="font-family: Arial;  font-weight: bold; text-align: center; ">
  Amount
</h3>
 <h3 style="font-family: Arial;  font-weight: bold; text-align: center; ">
  Account
</h3>
         </div>
    </div>


    <script>
function redirectToSave() {
    // Extract referral, amount, and contact from the current URL
    const params = new URLSearchParams(window.location.search);
    const ref = params.get('ref');
    const amount = params.get('amount');
    const contact = params.get('contact');

    // Redirect to the PHP script with the extracted parameters
    if (ref && amount && contact) {
      
    } else {
        alert("Missing required information!");
    }
}
</script>
</body>
</html>
