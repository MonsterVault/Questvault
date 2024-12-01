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

// Capture the search query if it exists
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';  // Get the search query from URL (GET method)

// Modify the SQL query to filter users based on the referral code and search query
$sql = "SELECT * FROM `user` WHERE invitecode = ? AND contact LIKE ? ORDER BY contact ASC"; // SQL with placeholders

// Prepare and execute the query
if ($stmt = $conn->prepare($sql)) {
    // Add the '%' for LIKE query to search for names starting with the search query
    $searchParam = $searchQuery . '%';
    $stmt->bind_param('ss', $referral_code, $searchParam); // Bind the referral code and search parameter

    $stmt->execute();
    $result = $stmt->get_result();
    $data_found = $result->num_rows > 0;
} else {
    echo "Error in query preparation.";
}

// Check if any data was returned
if (!$data_found) {
    echo "<p>No users found with the given search query.</p>";
} else {
    echo "<p>Results found!</p>";
}

// Output the users as cards
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Party - Quest Vault</title>
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

/* Button Styling (for View and Gift) */
.view-button, .gift-button {
     justify-content: center;
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
            const links = document.querySelectorAll(selector);
            links.forEach(link => {
                // Check if the link already contains a 'ref' parameter
                const url = new URL(link.href);
                if (!url.searchParams.has('ref')) {
                    url.searchParams.append('ref', referralId);
                    link.href = url.toString();  // Update the link href with the new URL
                }
            });
        };

        // Update all links with the referral ID
        updateLinkWithReferral("a[href='party.php']", "party.php");
        updateLinkWithReferral("a[href='Main.php']", "Main.php");
        updateLinkWithReferral("a[href='quest.php']", "quest.php");
        updateLinkWithReferral("a[href='Gift.php']", "Gift.php");
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
<div class="menu-container" style="margin-top:80px">
    <h1 class="brand-name">Party</h1> <!-- Brand name -->
    <p style="text-align: justify;">The <strong>Party</strong> feature allows users to form a group with other players, often called a "party" or "team." Through this feature, users can send an invitation link to other players, which allows them to join the party. In addition to forming and managing a party, you also earn a 20% or 10% commission if a player registers and purchases an item. Gold commision with directly send to your current gold balance.</p>

    <h3 class="brand-name" style="margin-top: 10px;">Party Code</h3>

    <!-- Referral Link Section -->
    <div class="referral-section">
        <p>Here's your referral link:</p>
        <div class="referral-container">
            <input type="text" id="referralLink" value="http://localhost/Beast%20Vault/Registration.php?ref=<?php echo isset($_SESSION['referral_code']) ? $_SESSION['referral_code'] : ''; ?>" readonly class="referral-link">
            <button onclick="copyReferralLink()" class="copy-button">Copy</button>
        </div>
    </div>
    <script>
    // Function to copy the referral link to the clipboard
    function copyReferralLink() {
        const referralLink = document.getElementById('referralLink');
        referralLink.select(); // Select the text in the input
        document.execCommand('copy'); // Copy the text to clipboard
        alert('Referral link copied: ' + referralLink.value); // Show alert to confirm
    }
    </script>

    <h3 class="brand-name" style="margin-top: 30px;">Party Members</h3>
     <div class="input-group">
   <form method="get" action="party.php" style="display: flex; align-items: center; gap: 10px;">
   
        <input type="text" id="search" name="search" placeholder="Enter username" value="<?php echo htmlspecialchars($searchQuery); ?>" style="padding: 8px; font-size: 16px; margin-top:15px" />
        <input type="hidden" name="ref" value="<?php echo isset($_SESSION['referral_code']) ? $_SESSION['referral_code'] : ''; ?>" />
    <input type="submit" value="Search" style="padding: 8px 15px; font-size: 14px; background-color: #ff8c00; color: white; border: none; border-radius: 5px; cursor: pointer; width: 100px;margin-top:15px" />
   
    
</form>
 </div>
    <!-- Users Card Layout -->
   
<div class="user-cards">
    <?php
    // Display users with their contact information
    while ($row = $result->fetch_assoc()) {
        $user_id = $row['id']; // Assuming the ID column is named 'id'
        $contact = $row['contact']; // Assuming you want to display the 'contact' field
        $referral_code = isset($_SESSION['referral_code']) ? $_SESSION['referral_code'] : ''; // Get the referral code from session

        // Generate the URL for the 'Gift' page
        $gift_url = "Gift.php?ref=" . urlencode($referral_code) . "&refID=" . urlencode($user_id);

        echo "<div class='user-card'>";
        echo "<div style='display: flex; align-items: center; justify-content: space-between;'>";
        echo "<h1 style='margin: 0;'> " . htmlspecialchars($contact) . "</h1>"; // Display the user's contact safely

        // Adding the 'Gift' button with the dynamic URL
        echo "<div style='display: flex; gap: 10px;'>";
        echo "<a href='$gift_url' class='view-button' data-id='$user_id'><img src='Images/gift2.png' alt='Gift'></a>";
        echo "</div>"; // Close the button container
        echo "</div>"; // Close the flex container for contact and buttons
        echo "</div>"; // Close the user card
    }
    ?>
</div>

<script>
    document.getElementById('search').addEventListener('input', function() {
    // Get the search query
    const query = this.value.toLowerCase();

    // Get all user cards
    const userCards = document.querySelectorAll('.user-card');

    // Loop through each card and check if the contact starts with the search query
    userCards.forEach(card => {
        const contactText = card.querySelector('h1').textContent.toLowerCase(); // Get the contact text
        
        // If contact starts with the search query, show the card, otherwise hide it
        if (contactText.startsWith(query)) {
            card.style.display = ''; // Show card
        } else {
            card.style.display = 'none'; // Hide card
        }
    });
});

</script>
</div>








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

<?php
// Close the database connection
$conn->close();
?>
