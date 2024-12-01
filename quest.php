<?php
session_start(); // Start the session to access session data

// Database connection details for XAMPP localhost
 include('db.php'); 

// Capture and store the referral code from the URL if it exists
if (isset($_GET['ref']) && !empty($_GET['ref'])) {
    $_SESSION['referral_code'] = $_GET['ref']; // Store referral code in session
}

// Ensure the referral code from session is available
$referral_code = isset($_SESSION['referral_code']) ? $_SESSION['referral_code'] : '';

// Fetch the user balance based on the referral code
$balance_sql = "SELECT balance FROM `user` WHERE referal = '$referral_code'"; // Adjust query if necessary
$balance_result = $conn->query($balance_sql);

// Check if balance data exists
$balance = 0.00; // Default balance if not found
if ($balance_result->num_rows > 0) {
    $user_data = $balance_result->fetch_assoc();
    $balance = $user_data['balance']; // Store the balance
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Party - Quest Vault</title>
    <link rel="stylesheet" href="CSS/beastvault.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
    text-align: Left;
    transition: transform 0.3s ease;
}

.user-card:hover {
    transform: scale(1.05);
}

/* Button Styling (for View and Gift) */
.view-button, .gift-button {
     justify-content: center;
    display: inline-block;
    margin-top: 5px;
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

/* Specific button color for Gift */
.gift-button {
    background-color: #ff8c00;

}

.gift-button:hover {
    background-color: #e87c00;
}

/* Button Image Styling */
.view-button img, .gift-button img {
    width: 15px; /* Adjust size of the image */
    height: auto;
    vertical-align: middle; /* Align image with the text (if any) */
}

/* Remove text from buttons and keep only images */
.view-button span, .gift-button span {
    display: none; /* Hide text */
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
/* Adjust card layout for image and text */
.card-content {
    display: flex;
    align-items: center;
    gap: 15px; /* Space between image and text */
}

/* Image Styling */
.quest-image {
    width: 80px; /* Set desired image size */
    height: 80px;
    border-radius: 10px; /* Optional: Make the image corners rounded */
    object-fit: cover; /* Ensure the image fits within the given dimensions */
}

/* Text next to the image */
.card-text {
    flex: 1; /* Allow text to take up the remaining space */
}
/* Styling for the Hunt Button */
.hunt-button {
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.hunt-button:hover {
    background-color: #0056b3;
}

/* Styling when the button is disabled and claimed */
.hunt-button.claimed {
    background-color: gray;
    cursor: not-allowed;
}
/* Card Layout Styling */
.user-cards {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
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
    text-align: left;
    transition: transform 0.3s ease;
    display: flex;
    flex-direction: column; /* Stacked vertically */
}

/* Card Content - Align the button to the right */
.card-content {
    display: flex;
    align-items: center;
    gap: 15px; /* Space between image, text, and button */
    flex-direction: row; /* Horizontal layout for image, text, and button */
    justify-content: space-between; /* Ensures space between image+text and button */
}

/* Button Styling (Hunt Button on the right) */
.hunt-button {
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    align-self: center; /* Center the button vertically within the card */
}

.hunt-button:hover {
    background-color: #0056b3;
}

/* Styling when the button is disabled and claimed */
.hunt-button.claimed {
    background-color: gray;
    cursor: not-allowed;
}

/* Image Styling */
.quest-image {
    width: 80px; /* Set desired image size */
    height: 80px;
    border-radius: 10px; /* Optional: Make the image corners rounded */
    object-fit: cover; /* Ensure the image fits within the given dimensions */
}

/* Text next to the image */
.card-text {
    flex: 1; /* Allow text to take up the remaining space */
}

    </style>
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

<!-- Party Page Content -->
<div class="menu-container" style="margin-top: 80px;">
    <h1 class="brand-name">Quest</h1>
    <p style="text-align: justify;">Here in <strong>Quest</strong>, you can view your daily quests, track your progress, and see your daily income. Each quest provides specific rewards to help you on your journey. Simply click on an active quest to claim your rewards and take one step closer to achieving your goals!</p>
    
    <!-- Display Current Gold -->
    <h3 class="brand-name" style="margin-top: 10px;">Current Gold: <span id="current-gold"><?php echo number_format($balance, 2); ?></span></h3>

   <?php
$today_date = date('Y-m-d'); // Get today's date
$quest_sql = "SELECT * FROM `quest` WHERE referal = '$referral_code' AND click != '$today_date' AND dateend > '$today_date'";
$quest_result = $conn->query($quest_sql);

// Count the number of rows returned
$remaining_quests = $quest_result->num_rows;
?>

<h3  style="margin-top: 10px;">
    Remaining Quest: <span><?php echo $remaining_quests; ?></span>
</h3>
 
   
 

    <div class='user-cards'>
    <?php
    // Loop through each quest
    while ($quest = $quest_result->fetch_assoc()) {
        // Determine the image based on quest title
        $quest_image = 'Images/slime.png'; // Default image
        if ($quest['title'] == 'Slime Training') {
            $quest_image = 'Images/Slime.png'; 
        } if ($quest['title'] == 'Goblin Hunt') {
            $quest_image = 'Images/goblin.jpg'; 
        } if ($quest['title'] == 'Dark Wolf Incursion') {
            $quest_image = 'Images/wolf.jpg'; 
        } if ($quest['title'] == 'Purge of the Undying Knight') {
            $quest_image = 'Images/Skeleton.jpg'; 
        } if ($quest['title'] == 'Dungeon Raid') {
            $quest_image = 'Images/dungeon.jpeg'; 
        } 

        echo "<div class='user-card' id='quest_" . $quest['id'] . "'>";
        echo "<div class='card-content'>";
        echo "<img src='" . $quest_image . "' alt='" . htmlspecialchars($quest['title']) . " Icon' class='quest-image'>";
        echo "<div class='card-text'>";
        echo "<h2 style='margin-bottom: 10px;'>" . htmlspecialchars($quest['title']) . "</h2>";
        echo "<p><strong>Reward:</strong> " . number_format($quest['reward'], 2) . " Gold</p>";
        echo "</div>";

        // Check if the quest's 'click' field is not equal to today's date
        if ($quest['click'] != $today_date) {
            // Display the Hunt button
            echo "<button class='hunt-button' id='hunt_" . $quest['id'] . "' onclick='claimQuest(" . $quest['id'] . ")'>Hunt</button>";
        } else {
            // Display the Claimed button
            echo "<button class='hunt-button claimed' disabled>Claimed</button>";
        }

        echo "</div>"; // Close card-content
        echo "</div>"; // Close user-card
    }
    ?>
    </div>





<script>
  function claimQuest(questId) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'claim_quest.php', true);  // Replace with your PHP script path
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    // Prepare the data to send (including the quest_id)
    var data = 'quest_id=' + questId;

    // Define what to do when the request finishes
    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = xhr.responseText.trim();  // Get the response from PHP

            // Handle different response cases
            switch(response) {
                case 'success':
                  
                     window.location.reload(); 
                    break;
                case 'balance_error':
                    alert('Error updating balance. Please try again later.');
                    break;
                case 'reward_error':
                    alert('Error retrieving the reward data.');
                    break;
                case 'claim_error':
                    alert('Error claiming the quest. Please try again.');
                    break;
                case 'already_claimed':
                    alert('This quest has already been claimed today.');
                    break;
                case 'missing_quest_id':
                    alert('Quest ID is missing.');
                    break;
                case 'session_error':
                    alert('Session error. Please log in again.');
                    break;
                default:
                    alert('Unknown error occurred.');
                    break;
            }
        } else {
            alert('An error occurred during the AJAX request.');
        }
    };

    // Send the request
    xhr.send(data);
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


