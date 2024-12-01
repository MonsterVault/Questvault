<?php
// Initialize variables
$errorMessage = "";
$successMessage = "";
 include('db.php'); // Ensure the database connection is included
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Database connection details for XAMPP localhost
  


    // Retrieve and sanitize form data
    $contact = $conn->real_escape_string($_POST['contact']);
    $password = $conn->real_escape_string($_POST['password']);
    $confirmPassword = $conn->real_escape_string($_POST['confirm-password']);
    $status = "Active";
    $balance = "0.00";
    $dailyincome = "0.00";
    $todays_com = "0.00";
    $total_com = "0.00";
    $invitecode = isset($_POST['invitecode']) ? $conn->real_escape_string($_POST['invitecode']) : NULL;

    // Check if passwords match
    if ($password !== $confirmPassword) {
        $errorMessage = "Passwords do not match!";
    } else {
        // Function to generate a unique 10-digit referral number
        function generateReferalNumber($conn)
        {
            $referal = rand(1000000000, 9999999999);

            // Check if the referral number already exists
            $sql_check_referal = "SELECT * FROM user WHERE referal = '$referal'";
            $result = $conn->query($sql_check_referal);

            // If the referral number exists, regenerate it
            while ($result->num_rows > 0) {
                $referal = rand(1000000000, 9999999999);
                $result = $conn->query($sql_check_referal);
            }

            return $referal;
        }

        // Generate a unique referral number
        $referal = generateReferalNumber($conn);

        // Check if the contact already exists
        $sql_check = "SELECT * FROM user WHERE contact = '$contact'";
        $result = $conn->query($sql_check);

        if ($result->num_rows > 0) {
            $errorMessage = "Username is already taken!";
        } else {
            // Insert data into the database
             $dateregistered = date('Y-m-d');
          $sql = "INSERT INTO user (contact, password, status, referal, invitecode, balance, dateregistered, dailyincome, todays_com, total_com) 
        VALUES ('$contact', '$password', '$status', '$referal', '$invitecode',  '$balance','$dateregistered', '$dailyincome', '$todays_com', '$total_com')";

            if ($conn->query($sql) === TRUE) {
                $successMessage = "Registration successful! You can now log in.";
            } else {
                $errorMessage = "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }

    // Close the connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Quest Vault</title>
    <link rel="stylesheet" href="CSS/beastvault.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="login-container">
        <img src="Images/treasure-chest.png" alt="Treasure Chest" width="40%" />
        <h1>Welcome to <span class="brand-name">Quest Vault</span></h1>
        <p class="tagline">Create an account</p>

        <!-- Display error or success messages -->
        <?php if (!empty($errorMessage)): ?>
            <p style="color: red; text-align: center;"><?php echo $errorMessage; ?></p>
        <?php elseif (!empty($successMessage)): ?>
            <p style="color: green; text-align: center;"><?php echo $successMessage; ?></p>
        <?php endif; ?>

        <form action="" method="post">
            <div class="input-group">
                <label for="contact">Username</label>
                <input type="text" id="contact" name="contact" placeholder="Enter your username" required>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password"
                       id="password"
                       name="password"
                       placeholder="Enter your password"
                       required
                       pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                       title="Must contain at least one number, one uppercase and lowercase letter, and at least 8 characters">
            </div>

            <div class="input-group">
                <label for="confirm-password">Retype Password</label>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Retype your password" required>
            </div>

            <div class="input-group">
                <label for="referral">Referral Code</label>
                <input type="text" id="invitecode" name="invitecode" value="" readonly>
            </div>

            <button type="submit" class="btn-login">Register</button>
            <p class="signup-link">Already have an account? <a href="index.html">Login here</a></p>
        </form>
    </div>

    <script>
        // Function to get query parameters from the URL
        function getUrlParameter(name) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(name);
        }

        // On page load, set referral code if present
        window.onload = function () {
            const referralCode = getUrlParameter('ref');
            if (referralCode) {
                document.getElementById('invitecode').value = referralCode;
            }
        };
    </script>
</body>
</html>
