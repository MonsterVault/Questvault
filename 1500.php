<?php
session_start(); // Start the session

// Define the price of the item
$item_price = 1500; // You can adjust this value as needed

// Get the referral ID from the query string if present
$referal = isset($_GET['ref']) ? $_GET['ref'] : null;

// Database connection (update credentials accordingly)
 include('db.php'); 

$current_gold = 0; // Default gold balance

// Fetch current gold if referral ID exists
if ($referal) {
    $sql = "SELECT balance FROM user WHERE referal = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $referal);
    $stmt->execute();
    $stmt->bind_result($current_gold);
    $stmt->fetch();
    $stmt->close();
}

// Handle purchase request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if referral ID is valid and if user has sufficient gold
    if ($referal && $current_gold >= $item_price) {
        // Deduct $item_price gold from the balance
        $new_balance = $current_gold - $item_price;
        $update_sql = "UPDATE user SET balance = ? WHERE referal = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ds", $new_balance, $referal); // "d" for double (decimal), "s" for string
        $update_stmt->execute();
        $update_stmt->close();

        // Check the user's invite code
        $invite_sql = "SELECT invitecode FROM user WHERE referal = ?";
        $invite_stmt = $conn->prepare($invite_sql);
        $invite_stmt->bind_param("s", $referal);
        $invite_stmt->execute();
        $invite_stmt->bind_result($invitecode);
        $invite_stmt->fetch();
        $invite_stmt->close();

        // If invitecode has value, add 35% of item_price to the balance of the user with that invitecode
        if ($invitecode) {
            // Check if the user with invitecode exists
            $referal_sql = "SELECT balance, contact FROM user WHERE referal = ?";
            $referal_stmt = $conn->prepare($referal_sql);
            $referal_stmt->bind_param("s", $invitecode);
            $referal_stmt->execute();
            $referal_stmt->store_result();

            if ($referal_stmt->num_rows > 0) {
                // Add 35% of the item price to the balance of the user with the invitecode
                $referal_stmt->bind_result($ref_balance, $contact);
                $referal_stmt->fetch();
                $new_ref_balance = $ref_balance + (0.20 * $item_price); // 35% of item price

                // Update the balance of the referred user
                $update_ref_sql = "UPDATE user SET balance = ? WHERE referal = ?";
                $update_ref_stmt = $conn->prepare($update_ref_sql);
                $update_ref_stmt->bind_param("ds", $new_ref_balance, $invitecode);
                $update_ref_stmt->execute();
                $update_ref_stmt->close();

                // Add a message about the commission to the messages table
                $message_sql = "INSERT INTO messages (message, regdate, referal) VALUES (?, ?, ?)";
                $message_stmt = $conn->prepare($message_sql);
                $message = "You received (" . (0.20 * $item_price) . ") gold commission from " . $contact;
                $regdate = date('Y-m-d');
                $message_stmt->bind_param("sss", $message, $regdate, $invitecode);
                $message_stmt->execute();
                $message_stmt->close();

                // Check if the user with the invitecode has another invitecode
                $invite_sql2 = "SELECT invitecode FROM user WHERE referal = ?";
                $invite_stmt2 = $conn->prepare($invite_sql2);
                $invite_stmt2->bind_param("s", $invitecode);
                $invite_stmt2->execute();
                $invite_stmt2->bind_result($invitecode2);
                $invite_stmt2->fetch();
                $invite_stmt2->close();

                // If second invitecode exists, add 15% of item_price to that user's balance
                if ($invitecode2) {
                    $referal_sql2 = "SELECT balance, contact FROM user WHERE referal = ?";
                    $referal_stmt2 = $conn->prepare($referal_sql2);
                    $referal_stmt2->bind_param("s", $invitecode2);
                    $referal_stmt2->execute();
                    $referal_stmt2->store_result();

                    if ($referal_stmt2->num_rows > 0) {
                        $referal_stmt2->bind_result($ref_balance2, $contact2);
                        $referal_stmt2->fetch();
                        $new_ref_balance2 = $ref_balance2 + (0.10 * $item_price); // 15% of item price

                        // Update the balance of the second referred user
                        $update_ref_sql2 = "UPDATE user SET balance = ? WHERE referal = ?";
                        $update_ref_stmt2 = $conn->prepare($update_ref_sql2);
                        $update_ref_stmt2->bind_param("ds", $new_ref_balance2, $invitecode2);
                        $update_ref_stmt2->execute();
                        $update_ref_stmt2->close();

                        // Add a message about the commission to the messages table
                        $message_sql2 = "INSERT INTO messages (message, regdate, referal) VALUES (?, ?, ?)";
                        $message_stmt2 = $conn->prepare($message_sql2);
                        $message2 = "You received (" . (0.10 * $item_price) . ") gold commission from " . $contact2;
                        $message_stmt2->bind_param("sss", $message2, $regdate, $invitecode2);
                        $message_stmt2->execute();
                        $message_stmt2->close();
                    }
                }
            }
            $referal_stmt->close();
        }

        // Insert new quest data 5 times
        $title = "Dark Wolf Incursion";
        $datestart = date('Y-m-d'); // Current date
        $dateend = date('Y-m-d', strtotime('+31 days')); // Current date + 31 days
        $status = $dateend < date('Y-m-d') ? "Close" : "Active"; // Determine status
        $reward = 15;

        // Insert the quest data 5 times
        for ($i = 0; $i < 5; $i++) {
            $insert_sql = "INSERT INTO quest (title, datestart, dateend, status, reward, referal) 
                           VALUES (?, ?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ssssds", $title, $datestart, $dateend, $status, $reward, $referal);
            $insert_stmt->execute();
            $insert_stmt->close();
        }

        // Redirect to Purchase Success page
        header("Location: purchase.php?ref=" . urlencode($referal));
        exit(); // Ensure no further code executes
    } else {
        $error_message = "Insufficient gold to complete the purchase.";
    }
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


     <!-- Top Navigation Bar -->
<div class="top-nav">
    <div class="logo-container">
        <img src="Images/treasure-chest.png" alt="Treasure Chest" class="logo"> <!-- Logo -->
        <h1 class="brand-name">Quest Vault</h1> <!-- Brand name -->
    </div>
</div>
<div class="menu-container" style="margin-top:80px">
    <p class="item-details">Are you sure to purchase this item?</p>
    <h1 class="item-details" style="margin-top:10px">Current Gold: <?php echo number_format($current_gold, 2); ?></h1> <!-- Display Current Gold -->

    <div class="item-details">
        <img src="Items/claw.png" alt="Wooden Sword" style="width: 100px;" />
    </div>
    <div class="item-details">
        <p class="item-name">Iron Claw</p>
        <p class="item-name">Price: 1500 Gold</p>
    </div>

    <!-- Purchase Form -->
    <form method="POST">
        <div class="buy-button" style="margin-top:15px">
            <button type="submit" class="btn-buy" style="width:100px; background: dodgerblue;">Purchase</button>
            <button type="button" class="btn-buy" style="width:100px" onclick="window.location.href='Main.php?ref=<?php echo urlencode($referal); ?>'">Cancel</button>
        </div>
    </form>

    <?php
    // Display error message if insufficient balance
    if (isset($error_message)) {
        echo "<p style='color: red; text-align: center;'>$error_message</p>";
    }
    ?>
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
