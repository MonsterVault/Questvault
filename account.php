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

// Query to get contact and DateRegistered based on the referral ID
$sql = "SELECT contact, DateRegistered FROM user WHERE referal = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error); // Error handling for statement preparation
}

$stmt->bind_param("s", $referal); // Bind the referral parameter
$stmt->execute();
$stmt->bind_result($contact, $dateRegistered); // Bind the results to $contact and $dateRegistered

// Check if a contact was found
if ($stmt->fetch()) {
    // We found a contact and date registered
    $contactInfo = htmlspecialchars($contact);
    $dateRegisteredInfo = htmlspecialchars($dateRegistered); // Format the date if necessary
} else {
    // If no contact is found, set a default message
    $contactInfo = "No contact information found.";
    $dateRegisteredInfo = "No registration date found.";
}

// After fetching the contact and date, free the result set
$stmt->free_result();

// Fetch the current gold for the user with the matching referral code
$current_gold = 0.00; // Default value

if ($referal !== 'No referral ID found') {
    // Query to get the balance based on the referral ID
    $sql = "SELECT balance FROM user WHERE referal = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error); // Error handling for statement preparation
    }
    $stmt->bind_param("s", $referal);
    $stmt->execute();
    $stmt->bind_result($current_gold); // Bind the result to $current_gold
    $stmt->fetch();
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account - Quest Vault</title>
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
    <img src="Char/swordsman.png" alt="Character Image" width="40%" />
    <h1 >
        <?php
        // Display the contact info or a fallback message
        echo $contactInfo; // Display the contact fetched from the database
        ?>
    </h1>
    
    <h1 class="brand-name" style="margin-top: 10px;">Current Gold: <span style="color: white;"><?php echo number_format($current_gold, 2); ?></span></h1>
<p>Date registered: <?php echo $dateRegisteredInfo; ?></p>
<button type="submit" class="btn-login" style="margin-top:35px; background-color: dodgerblue;" onclick="window.location.href='logininfo.php?ref=<?php echo urlencode($referal); ?>';">Login Informations</button>
  <button type="submit" class="btn-login" style="margin-top:10px; background-color: dodgerblue;" onclick="window.location.href='bankinfo.php?ref=<?php echo urlencode($referal); ?>';">Bank Information</button>





</div>


<!-- Bottom Navigation Bar -->
<div class="bottom-nav">
    <div class="nav-item">
        <a href="Main.php">
            <img src="Images/sword.png" alt="Items" class="nav-icon">
        </a>
        <span class="nav-label">Items</span>
    </div>

    <div class="nav-item">
        <a href="party.php">
            <img src="Images/game.png" alt="Guild" class="nav-icon">
        </a>
        <span class="nav-label">Party</span>
    </div>
    <div class="nav-item">
        <a href="quest.php">
            <img src="Images/quest.png" alt="Quest" class="nav-icon">
        </a>
        <span class="nav-label">Quest</span>
    </div>
    <div class="nav-item">
        <a href="profile.php">
            <img src="Images/swordsman.png" alt="Profile" class="nav-icon">
        </a>
        <span class="nav-label">Profile</span>
    </div>
    <div class="nav-item">
        <a href="index.html">
            <img src="Images/switch.png" alt="Logout" class="nav-icon" />
        </a>
        <span class="nav-label">Logout</span>
    </div>
</div>

</body>
</html>