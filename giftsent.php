<?php
session_start();

// Retrieve referral code from GET request (if available)
$referral_code = isset($_GET['ref']) ? $_GET['ref'] : '';

// Store the referral code in the session for future use
if ($referral_code) {
    $_SESSION['referral_code'] = $referral_code;
} else {
    // If there's no referral code in the URL, fall back to the session value
    $referral_code = $_SESSION['referral_code'] ?? '';
}

// At this point, $referral_code will have the correct value to use in the HTML
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
    !-- Top Navigation Bar -->
<div class="top-nav">
    <div class="logo-container">
        <img src="Images/treasure-chest.png" alt="Treasure Chest" class="logo">
        <h1 class="brand-name">Quest Vault</h1>
    </div>
</div>
    <div class="login-container" style="margin-top:80px">
        <img src="Images/gift.png" alt="Treasure Chest" width="40%" />
        <h1 >Sent Successfuly!</span></h1>
        <p class="tagline"> Your gift was successfully recieved.</p>

       
            
            <button type="button" class="btn-login" onclick="window.location.href='party.php?ref=<?php echo urlencode($referral_code); ?>';">Back to Party</button>
           
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
        <a href="beastvault.php">
            <img src="Images/gift.png" alt="Gift" class="nav-icon">
        </a>
        <span class="nav-label">Gift</span>
    </div>
</div>
</body>
</html>
