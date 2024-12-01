<?php
session_start(); // Start the session to access session data

// Get the referral ID from the query string if it's present in the URL
if (isset($_GET['ref'])) {
    $referal = $_GET['ref'];
} else {
    $referal = 'No referral ID found';
}

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
    <title>Profile - Quest Vault</title>
    <link rel="stylesheet" href="CSS/beastvault.css">
     <link rel="stylesheet" href="CSS/image.css">
</head>
<body>
<script>
    window.onload = function () {
        // Get the referral ID from the query string
        const urlParams = new URLSearchParams(window.location.search);
        const referralId = urlParams.get('ref');

        if (referralId) {
            // Function to update links with the referral ID
            const updateLinkWithReferral = (selector, baseUrl) => {
                const link = document.querySelector(`a[href='${baseUrl}']`);
                if (link) {
                    link.href = `${baseUrl}?ref=${encodeURIComponent(referralId)}`;
                }
            };

            // Update links
            updateLinkWithReferral("a[href='party.php']", "party.php");
            updateLinkWithReferral("a[href='Main.php']", "Main.php");
            updateLinkWithReferral("a[href='quest.php']", "quest.php");
             updateLinkWithReferral("a[href='200.php']", "200.php");
                updateLinkWithReferral("a[href='500.php']", "500.php");
                   updateLinkWithReferral("a[href='1500.php']", "1500.php");
                      updateLinkWithReferral("a[href='3000.php']", "3000.php");
                         updateLinkWithReferral("a[href='8000.php']", "8000n.php");
                         updateLinkWithReferral("a[href='profile.php']", "profile.php");
        }
    };
</script>

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


<!-- Images Section -->
<div class="image-gallery">
    <div>
      <a href="account.php?ref=<?php echo urlencode($referal); ?>">
            <img src="Images/account.png" alt="Account">
            <div class="image-label">Account</div>
        </a>
    </div>
    <div>
        <a href="message.php?ref=<?php echo urlencode($referal); ?>">
            <img src="Images/message.png" alt="Message">
            <div class="image-label">Message</div>
        </a>
    </div>
    <div>
        <a href="merchant.php?ref=<?php echo urlencode($referal); ?>">
            <img src="Images/pledge.png" alt="Merchant">
            <div class="image-label">Merchant</div>
        </a>
    </div>
    <div>
      <a href="rechargeinfo.php?ref=<?php echo urlencode($referal); ?>">
            <img src="Images/coin.png" alt="Recharge">
            <div class="image-label">Recharge</div>
        </a>
    </div>
 
    <div>
       <a href="party.php?ref=<?php echo urlencode($referal); ?>">
         <img src="Images/clan.png" alt="Party">
             <div class="image-label">Party</div>
        </a>
    </div>
  


    <div>
       <a href="index.html">
            <img src="Images/switch.png" alt="Switch">
            <div class="image-label">Switch</div>
        </a>
    </div>
</div>




<!-- Add this script to handle the URL passing -->
<script>
    function addRefParam(event, linkElement) {
        event.preventDefault(); // Prevent default anchor behavior

        // Get the current URL
        var currentUrl = window.location.href;

        // Append the current URL as a 'ref' parameter to the link's href
        var newUrl = linkElement.href + "?ref=" + encodeURIComponent(currentUrl);

        // Redirect to the new URL
        window.location.href = newUrl;
    }
</script>



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
