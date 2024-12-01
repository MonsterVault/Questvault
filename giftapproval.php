<?php
session_start();

 include('db.php'); 

// Retrieve values from URL (GET method)
$referral_code = isset($_GET['ref']) ? $_GET['ref'] : '';
$refid = isset($_GET['refid']) ? filter_var($_GET['refid'], FILTER_VALIDATE_INT) : 0;

// Store the referral code in the session
if ($referral_code) {
    $_SESSION['referral_code'] = $referral_code;
}
$referral_code = $_SESSION['referral_code'] ?? '';

// Fetch recipient contact info
$receiver_contact = 'Unknown';
if ($refid) {
    $stmt = $conn->prepare("SELECT contact FROM `user` WHERE id = ?");
    $stmt->bind_param("i", $refid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $receiver_data = $result->fetch_assoc();
        $receiver_contact = $receiver_data['contact'];
    }
    $stmt->close();
}

// Fetch sender contact info
$sender_contact = 'Unknown';
if ($referral_code) {
    $stmt = $conn->prepare("SELECT contact FROM `user` WHERE referal = ?");
    $stmt->bind_param("s", $referral_code);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $sender_data = $result->fetch_assoc();
        $sender_contact = $sender_data['contact'];
    }
    $stmt->close();
}

// Process the POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = isset($_POST['refamount']) ? filter_var($_POST['refamount'], FILTER_VALIDATE_INT) : 0;

    if ($amount > 0 && $refid > 0 && $referral_code) {
        $conn->begin_transaction();
        try {
            // Deduct from sender
            $stmt = $conn->prepare("UPDATE user SET balance = balance - ? WHERE referal = ? AND balance >= ?");
            $stmt->bind_param("isi", $amount, $referral_code, $amount);
            $stmt->execute();
            if ($stmt->affected_rows === 0) {
                throw new Exception("Insufficient balance or invalid referral.");
            }
            $stmt->close();

            // Add to recipient
            $stmt = $conn->prepare("UPDATE user SET balance = balance + ? WHERE id = ?");
            $stmt->bind_param("ii", $amount, $refid);
            $stmt->execute();
            if ($stmt->affected_rows === 0) {
                throw new Exception("Invalid recipient.");
            }
            $stmt->close();

            // Fetch recipient referral code
            $stmt = $conn->prepare("SELECT referal FROM user WHERE id = ?");
            $stmt->bind_param("i", $refid);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $receiver_data = $result->fetch_assoc();
                $receiver_referal = $receiver_data['referal'];
            } else {
                throw new Exception("Recipient referral not found.");
            }
            $stmt->close();

            // Insert message for recipient
            $today_date = date('Y-m-d');
            $receiver_message = "You received a gift worth ($amount) gold from ($sender_contact).";
            $stmt = $conn->prepare("INSERT INTO messages (message, date, referal) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $receiver_message, $today_date, $receiver_referal);
            if (!$stmt->execute()) {
                throw new Exception("Failed to log recipient message.");
            }
            $stmt->close();

            // Insert message for sender
            $sender_message = "You sent a gift worth ($amount) gold to ($receiver_contact).";
            $stmt = $conn->prepare("INSERT INTO messages (message, date, referal) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $sender_message, $today_date, $referral_code);
            if (!$stmt->execute()) {
                throw new Exception("Failed to log sender message.");
            }
            $stmt->close();

            // Commit transaction
            $conn->commit();

            // Redirect to giftsent.php with referral code
            header("Location: giftsent.php?ref=" . urlencode($referral_code));
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Transaction error: " . $e->getMessage());
            echo "<script>
                alert('An error occurred: " . addslashes($e->getMessage()) . "');
                window.history.back();
            </script>";
        }
    } else {
        echo "<script>alert('Invalid input. Please check your amount or referral code.'); window.history.back();</script>";
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gift - Quest Vault</title>
    <link rel="stylesheet" href="CSS/beastvault.css">
   
</head>
<body>
<!-- Top Navigation Bar -->
<div class="top-nav">
    <div class="logo-container">
        <img src="Images/treasure-chest.png" alt="Treasure Chest" class="logo">
        <h1 class="brand-name">Quest Vault</h1>
    </div>
</div>

<div class="menu-container" style="margin-top:80px">
    <div class="item-details">
        <img src="Images/coin.png" alt="Wooden Sword" style="width: 100px;" />
    </div>
    <?php
// Assuming this is in giftapproval.php

// Retrieve the amount from the URL (GET method)
$amount = isset($_GET['refamount']) ? filter_var($_GET['refamount'], FILTER_VALIDATE_INT) : 0;

?>
    <h1 class="item-details">Are you sure to send <?php echo $amount ? $amount : '0'; ?> gold coins to <?php echo $receiver_contact ? $receiver_contact : 'Unknown'; ?>?</h1>
  <div class="buy-button" style="margin-top:15px">
    <form method="POST" action="giftapproval.php?ref=<?php echo $referral_code; ?>&refid=<?php echo $refid; ?>&refamount=<?php echo $amount; ?>">
        <input type="hidden" name="refid" value="<?php echo $refid; ?>">
        <input type="hidden" name="refamount" value="<?php echo $amount; ?>">
      
        <button type="submit" class="btn-buy" style="width:100px; background: dodgerblue;">Confirm</button>
<button type="button" class="btn-buy" style="width:100px" onclick="window.location.href='party.php?ref=<?php echo urlencode($referral_code); ?>'">Cancel</button>
   
    </form>
    

     </div>
</div>

<!-- Bottom Navigation Bar -->
<div class="bottom-nav">
    <div class="nav-item">
        <a href="Main.php?ref=<?php echo urlencode($referral_code); ?>">
            <img src="Images/sword.png" alt="Items" class="nav-icon">
        </a>
        <span class="nav-label">Items</span>
    </div>
    <div class="nav-item">
        <a href="party.php?ref=<?php echo urlencode($referral_code); ?>">
            <img src="Images/game.png" alt="Guild" class="nav-icon">
        </a>
        <span class="nav-label">Party</span>
    </div>
    <div class="nav-item">
        <a href="quest.php?ref=<?php echo urlencode($referral_code); ?>">
            <img src="Images/quest.png" alt="Quest" class="nav-icon">
        </a>
        <span class="nav-label">Quest</span>
    </div>
    <div class="nav-item">
        <a href="profile.php?ref=<?php echo urlencode($referral_code); ?>">
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
