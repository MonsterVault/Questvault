<?php
session_start(); // Start the session to access session data

// Get the referral ID from the query string if it's present in the URL
if (isset($_GET['ref'])) {
    $referal = $_GET['ref'];
} else {
    $referal = 'No referral ID found';
}

// Database connection (make sure to update with your credentials)
 include('db.php'); 

// Initialize variables
$contactInfo = $dateRegisteredInfo = "No contact information found.";
$current_gold = 0.00;

// Query to get contact and DateRegistered based on the referral ID
$sql = "SELECT contact, DateRegistered FROM user WHERE referal = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $referal);
$stmt->execute();
$stmt->bind_result($contact, $dateRegistered);

if ($stmt->fetch()) {
    $contactInfo = htmlspecialchars($contact);
    $dateRegisteredInfo = htmlspecialchars($dateRegistered); // Format the date if necessary
}

$stmt->free_result();

// Fetch the current gold for the user with the matching referral code
if ($referal !== 'No referral ID found') {
    $sql = "SELECT balance FROM user WHERE referal = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $referal);
    $stmt->execute();
    $stmt->bind_result($current_gold);
    $stmt->fetch();
    $stmt->close();
}

// Handle form submission for password and username update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_username = $_POST['new_username'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check the current password
    $sql = "SELECT password FROM user WHERE referal = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $referal);
    $stmt->execute();
    $stmt->bind_result($db_password);
    $stmt->fetch();

    // Free result after fetching
    $stmt->free_result();

    // Check if current password matches
    if ($db_password === $current_password) {
        // Check if the new password and confirm password match
        if ($new_password === $confirm_password) {
            // Update the user's username and password
            $sql = "UPDATE user SET contact = ?, password = ? WHERE referal = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $new_username, $new_password, $referal);
            $stmt->execute();
            $stmt->close();

            // Display success message
           $update_message = '<p style="color: DodgerBlue;">Your profile has been updated successfully!</p>';
        } else {
            // Mismatch between new password and confirm password
            $update_message = "New password and confirmation do not match.";
        }
    } else {
        // Current password is incorrect
        $update_message = "Current password is incorrect.";
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Info - Quest Vault</title>
    <link rel="stylesheet" href="CSS/beastvault.css">
    <link rel="stylesheet" href="CSS/image.css">
</head>
<body>

<!-- Top Navigation Bar -->
<div class="top-nav">
    <div class="logo-container">
        <img src="Images/treasure-chest.png" alt="Treasure Chest" class="logo"> <!-- Logo -->
        <h1 class="brand-name">Quest Vault</h1> <!-- Brand name -->
    </div>
</div>

<!-- Menu Container -->
<div class="login-container" style="margin-top: 80px; align-content: center;">
    <img src="Images/passcode.png" alt="Character Image" width="40%" />
    <h1 >
        <?php
        // Display the contact info or a fallback message
        echo $contactInfo; // Display the contact fetched from the database
        ?>
    </h1>
    
    <h1 class="brand-name" style="margin-top: 10px;">Login Informations</span></h1>
   

    <!-- Display any update messages -->
    <?php if (isset($update_message)): ?>
        <p style="color: red;"><?php echo $update_message; ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="input-group" style="margin-top:25px">
            <label for="current_password">Current password</label>
            <input type="password" id="current_password" name="current_password" placeholder="Enter current passcode" required>
        </div>
        <div class="input-group">
            <label for="new_username" style="margin-top:35px">Username</label>
            <input type="text" id="new_username" name="new_username" placeholder="Enter new username" value="<?php echo htmlspecialchars($contactInfo); ?>" required>
        </div>
        <div class="input-group">
            <label for="new_password">New password</label>
            <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
        </div>
        <div class="input-group">
            <label for="confirm_password">Confirm password</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
        </div>
        <button type="submit" class="btn-login">Update</button>
    </form>
</div>

<!-- Bottom Navigation Bar -->

<!-- Bottom Navigation Bar -->
<div class="bottom-nav">
    <div class="nav-item">
        <a href="Main.php?ref=<?php echo urlencode($referal); ?>">
            <img src="Images/sword.png" alt="Items" class="nav-icon">
        </a>
        <span class="nav-label">Items</span>
    </div>
    <div class="nav-item">
        <a href="party.php?ref=<?php echo urlencode($referal); ?>">
            <img src="Images/game.png" alt="Guild" class="nav-icon">
        </a>
        <span class="nav-label">Party</span>
    </div>
    <div class="nav-item">
        <a href="quest.php?ref=<?php echo urlencode($referal); ?>">
            <img src="Images/quest.png" alt="Quest" class="nav-icon">
        </a>
        <span class="nav-label">Quest</span>
    </div>
    <div class="nav-item">
        <a href="profile.php?ref=<?php echo urlencode($referal); ?>">
            <img src="Images/swordsman.png" alt="Profile" class="nav-icon">
        </a>
        <span class="nav-label">Profile</span>
    </div>
    <div class="nav-item">
        <a href="index.html">
            <img src="Images/switch.png" alt="Logout" class="nav-icon">
        </a>
        <span class="nav-label">Logout</span>
    </div>
</div>

</body>
</html>
