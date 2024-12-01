<?php
session_start();

// Database connection
 include('db.php'); 

// Get referral ID
$referal = filter_input(INPUT_GET, 'ref', FILTER_SANITIZE_STRING) ?? 'No referral ID found';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Info - Quest Vault</title>
    <link rel="stylesheet" href="CSS/beastvault.css">
    <link rel="stylesheet" href="CSS/image.css">
    <link rel="stylesheet" href="CSS/cards.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>

<!-- Top Navigation Bar -->
<div class="top-nav">
    <div class="logo-container">
        <img src="Images/treasure-chest.png" alt="Treasure Chest" class="logo"> <!-- Logo -->
        <h1 class="brand-name">Quest Vault</h1> <!-- Brand name -->
    </div>
</div>

<!-- Main Container -->
<div class="menu-container" style="margin-top: 80px;">
    <div style="text-align: center;">
        <img src="Images/Message.png" alt="Character Image" width="40%">
        <h1 class="brand-name" style="margin-top: 10px;">Messages</h1>
    </div>

   <div class="user-cards">
    <?php
    // Fetch messages from the database, including the date
    $message_sql = "SELECT id, message, regdate FROM messages WHERE referal = ? ORDER BY id DESC";
    $message_stmt = $conn->prepare($message_sql);
    $message_stmt->bind_param("s", $referal);
    $message_stmt->execute();
    $message_result = $message_stmt->get_result();

    // Loop through each message
    while ($message = $message_result->fetch_assoc()) {
        // Format the date (e.g., "MM/DD/YYYY")
        $formatted_date = date("m/d/Y", strtotime($message['regdate']));

        echo "<div class='user-card' id='message_" . $message['id'] . "'>";
        echo "<div class='card-content'>";
        echo "<div class='card-text'>";
        echo "<p>" . htmlspecialchars($message['message']) . "</p>";
        echo "</div>"; // Close card-text

        // Display the formatted date
        echo "<div class='message-date'>";
        echo "<p> " . $formatted_date . "</p>";
        echo "</div>"; // Close message-date

        echo "</div>"; // Close card-content
        echo "</div>"; // Close user-card
    }

    // Check if no messages exist
    if ($message_result->num_rows == 0) {
        echo "<p class='no-messages'>No messages found for this referral.</p>";
    }

    $message_stmt->close();
    ?>
</div>

</div>

</div>
<div class="menu-container-bottom"></div>

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
