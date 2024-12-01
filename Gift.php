<?php
session_start();  // Start the session to store and access session variables

 include('db.php'); 

// Capture and store the referral code from the URL if it exists
if (isset($_GET['ref']) && !empty($_GET['ref'])) {
    $_SESSION['referral_code'] = $_GET['ref']; // Store referral code in session
}

// Ensure the referral code from session is available
$referral_code = isset($_SESSION['referral_code']) ? $_SESSION['referral_code'] : '';

// Fetch user data based on the referral code stored in session
if ($referral_code) {
    $sql = "SELECT * FROM `user` WHERE referal = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $referral_code);
    $stmt->execute();
    $result = $stmt->get_result();

    // If the referral code matches a user, retrieve user data
    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        // You can store user data in session if needed
        $_SESSION['user_data'] = $user_data;
    } else {
        // Handle case where no user found for the referral code
        echo json_encode(['status' => 'error', 'message' => 'No user found for this referral code']);
        exit;
    }
}

// Get the refID from the URL (for the 'receiver')
$refID = isset($_GET['refID']) ? $_GET['refID'] : '';

// Fetch user data based on the refID (to get the contact)
if ($refID) {
    $sql = "SELECT contact FROM `user` WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $refID); // Use 'i' for integer binding
    $stmt->execute();
    $result = $stmt->get_result();

    // If the refID matches a user, retrieve the contact info
    if ($result->num_rows > 0) {
        $receiver_data = $result->fetch_assoc();
        $receiver_contact = $receiver_data['contact']; // Store the contact info
    } else {
        $receiver_contact = 'Unknown'; // Default if no user found
    }
}

if ($user_data) {
    $user_id = $user_data['id']; // Use the 'id' from the fetched user data
    $gift_url = "giftapproval.php?ref=" . urlencode($referral_code) . "&refid=" . urlencode($refID); // Use $refID for 'usrl_actualref'

    // If the user has entered an amount, append it to the gift URL
    if (isset($_POST['amount']) && !empty($_POST['amount']) && $_POST['amount'] >= 1) {
        $gift_url .= "&refamount=" . urlencode($_POST['amount']);
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gift- Quest Vault</title>
    <link rel="stylesheet" href="CSS/beastvault.css">
    <style>
        /* Card Layout Styling */
        .user-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin-top: 10px;
        }

        .user-card {
            background-color: #444;
            color: white;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .user-card:hover {
            transform: scale(1.05);
        }

        /* Button Styling */
        .view-button, .gift-button {
            display: inline-block;
            padding: 5px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .view-button:hover, .gift-button:hover {
            background-color: #0056b3;
        }

        .gift-button {
            background-color: #ff8c00;
        }

        .gift-button:hover {
            background-color: #e87c00;
        }

        /* Button Image Styling */
        .view-button img, .gift-button img {
            width: 15px;
            height: auto;
            vertical-align: middle;
        }

        .view-button span, .gift-button span {
            display: none;
        }

        /* Referral Link Styling */
        .referral-link {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            background-color: #444;
            color: white;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        /* Copy Button Styling */
        .copy-button {
            width: 100%;
            padding: 12px;
            background-color: #ff8c00;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            box-sizing: border-box;
        }

        .copy-button:hover {
            background-color: #e87c00;
        }
    </style>
</head>
<body>

<!-- Top Navigation Bar -->
<div class="top-nav">
    <div class="logo-container">
        <img src="Images/treasure-chest.png" alt="Treasure Chest" class="logo">
        <h1 class="brand-name">Quest Vault</h1>
    </div>
</div>

<div class="login-container" style="margin-top: 80px;">
    <img src="Images/gift.png" alt="Gift" width="40%" />
    <h1>Receiver: <?php echo htmlspecialchars($receiver_contact); ?></h1> <!-- Display the receiver's contact -->
   
    <div class="input-group">
        <input style="text-align: center;" type="text" id="amount" name="amount" placeholder="Enter gold amount" required>
    </div>

    <p class="tagline"></p>

    <div class="buy-button" >
    <button type="button" class="btn-buy" style="width:100px; background: dodgerblue; margin-top: 0px" onclick="sendGift()">Send</button>
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

<script>
    function sendGift() {
        var amount = document.getElementById('amount').value;
        if (parseFloat(amount) >= 50) {
            var giftUrl = "<?php echo $gift_url; ?>&refamount=" + encodeURIComponent(amount);
            window.location.href = giftUrl;
        } else {
            alert("Amount must be greater than or equal to 50 gold.");
        }
    }
</script>

</body>
</html>
