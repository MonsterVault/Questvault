<?php
session_start(); // Start the session to access session data

// Get the referral ID from the query string if it's present in the URL
if (isset($_GET['ref'])) {
    $referal = $_GET['ref'];
} else {
    $referal = 'No referral ID found';
}

 include('db.php'); 

// Fetch the current gold for the user with the matching referral code
$current_gold = 0.00; // Default value

if ($referal !== 'No referral ID found') {
    $sql = "SELECT balance FROM user WHERE referal = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $referal);
    $stmt->execute();
    $stmt->bind_result($current_gold); // Bind the result to $current_gold
    $stmt->fetch();
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Quest Vault</title>
    <link rel="stylesheet" href="CSS/beastvault.css">



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
                         updateLinkWithReferral("a[href='8000.php']", "8000.php");
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
<div class="menu-container" style="margin-top: 80px">
    <h1 class="brand-name">Instructions</h1> <!-- Brand name -->
  <p style="text-align: justify;">  When you purchase an item in <strong>Quest Vault</strong>, its stats directly influence your daily profit. Each weapon has specific attributes that contribute to the daily earnings of your account.</p>
    
    <h1 class="brand-name" style="margin-top :10px">Current Gold: <?php echo number_format($current_gold, 2); ?></h1> <!-- Display Current Gold -->
<div class="item-container">
    <div class="item-image">
        <img src="Items/woodensword.png" alt="Wooden Sword" />
    </div>
    <div class="item-details">
        <p class="item-name">Wooden Sword</p>
        <p class="item-info">2 Gold/ Slime</p>
        <p class="item-daily">5 Slimes/ day</p>
        <p class="item-daily">Item Duration: 30 days</p>
        <p class="item-name">Price: 200 Gold</p>
    </div>
  <div class="buy-button">
     <a href="200.php"> <button class="btn-buy">Buy</button></a>
   
</div>


</div>
<div class="item-container">
    <div class="item-image">
        <img src="Items/sword.png" alt="Wooden Sword" />
    </div>
    <div class="item-details">
        <p class="item-name">Iron Sword</p>
        <p class="item-info">5 Gold/ Goblin</p>
        <p class="item-daily">5 Goblins/ day</p>
         <p class="item-daily">Item Duration: 30 days</p>
          <p class="item-name">Price: 500 Gold</p>
    </div>
    <div class="buy-button">
         <a href="500.php">  <button class="btn-buy">Buy</button></a>
    </div>
</div>
    <!-- Item Display -->
    <div class="item-container">
    <div class="item-image">
        <img src="Items/claw.png" alt="Wooden Sword" />
    </div>
    <div class="item-details">
        <p class="item-name">Iron Claw</p>
        <p class="item-info">15 Gold/ Dark Wolves</p>
        <p class="item-daily">5 Dark Wolves/ day</p>
         <p class="item-daily">Item Duration: 30 days</p>
          <p class="item-name">Price: 1,500 Gold</p>
    </div>
    <div class="buy-button">
        <a href="1500.php">  <button class="btn-buy">Buy</button></a>
    </div>
</div>
<div class="item-container">
    <div class="item-image">
        <img src="Items/staff.png" alt="Wooden Sword" />
    </div>
    <div class="item-details">
        <p class="item-name">Elemental Staff</p>
        <p class="item-info">15 Gold/ Skeleton Knight</p>
        <p class="item-daily">10 Skeleton Knights/ day</p>
         <p class="item-daily">Item Duration: 30 days</p>
          <p class="item-name">Price: 3,000 Gold</p>
    </div>
    <div class="buy-button">
       <a href="3000.php">  <button class="btn-buy">Buy</button></a>
    </div>
</div> 
<div class="item-container">
    <div class="item-image">
        <img src="Items/teleport.png" alt="Wooden Sword" />
    </div>
    <div class="item-details">
        <p class="item-name">Abyssal Warpstone</p>
         <p class="item-info">20 Gold/ Dungeon</p>
        <p class="item-daily">20 Dungeons/ day</p>
         <p class="item-daily">Item Duration: 30 days</p>
          <p class="item-name">Price: 8,000 Gold</p>
    </div>
    <div class="buy-button">
        <a href="8000.php">  <button class="btn-buy">Buy</button></a>
    </div>
</div> 
<div class="item-container">
    <div class="item-image">
        <img src="Items/unknown (1).png" alt="Wooden Sword" />
    </div>
    <div class="item-details">
        <p class="item-name">Coming Soon</p>
         <p class="item-info">--</p>
        <p class="item-daily">--</p>
         <p class="item-daily">--</p>
          <p class="item-name">Price: (?) Gold</p>
    </div>
   
</div> 



</div>
<div class="menu-container-bottom"></div>

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
