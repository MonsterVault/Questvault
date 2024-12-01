<?php
session_start();

// Get referral ID
$referal = $_GET['ref'] ?? 'No referral ID found';

// Database connection
 include('db.php'); 

$contactInfo = $dateRegisteredInfo = $accountnumber = $conaccountnumber = "";
$current_gold = 0.00;
$hideWithdrawalPassword = false;
$update_message = "";

// Fetch user details including account number
$sql = "SELECT accountholder, DateRegistered, accountpassword, accountnumber, balance FROM user WHERE referal = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $referal);
$stmt->execute();
$stmt->bind_result($accountholder, $dateRegistered, $accountpassword, $accountnumber, $current_gold);
$stmt->fetch();
$stmt->close();

if ($accountholder) {
    $contactInfo = htmlspecialchars($accountholder);
    $dateRegisteredInfo = htmlspecialchars($dateRegistered);
    $conaccountnumber = htmlspecialchars($accountnumber); // Populate account number if it exists
} else {
    $hideWithdrawalPassword = true;
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    $new_username = htmlspecialchars(strip_tags($_POST['accountholder']));
    $new_accountnumber = preg_replace("/[^0-9]/", "", $_POST['banknumber']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $current_password = $_POST['current_password'] ?? null;

    if ($new_password !== $confirm_password) {
        $update_message = "New password and confirmation do not match.";
    } elseif ($accountpassword && $current_password !== $accountpassword) {
        $update_message = "Current password is incorrect.";
    } else {
        $sql = "UPDATE user SET accountholder = ?, accountnumber = ?, accountpassword = ? WHERE referal = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $new_username, $new_accountnumber, $new_password, $referal);

        if ($stmt->execute()) {
            $update_message = '<p style="color: DodgerBlue;">Your profile has been updated successfully!</p>';
        } else {
            $update_message = "Failed to update your profile.";
        }
        $stmt->close();
    }
}

$conn->close();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>


// Generate a CSRF token if it doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Info - Quest Vault</title>
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

<!-- Main Container -->
<div class="login-container" style="margin-top: 80px; align-content: center;">
    <img src="Images/mobile-banking.png" alt="Character Image" width="40%">
    <h1>
        <?php echo $contactInfo ? $contactInfo : 'No account found'; ?>
    </h1>
    <h1 class="brand-name" style="margin-top: 10px;">Bank Information</h1>

    <!-- Update Messages -->
    <?php if (!empty($update_message)): ?>
        <p style="color: red;"><?php echo $update_message; ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <?php if (!$hideWithdrawalPassword): ?>
            <div class="input-group" style="margin-top: 25px;">
                <label for="current_password">Current withdrawal password</label>
                <input type="password" id="withdrawal_password" name="current_password" placeholder="Enter current password if set">
            </div>
        <?php endif; ?>

        <h1 style="text-align: left; margin-top: 25px;">Bank Name: GCASH</h1>
        <div class="input-group">
            <label for="new_username">Account holder</label>
            <input type="text" id="accountholder" name="accountholder" placeholder="Enter your name" value="<?php echo htmlspecialchars($contactInfo); ?>" required>
        </div>
        <div class="input-group">
            <label for="banknumber">Bank account number</label>
            <input type="text" id="banknumber" name="banknumber" placeholder="Enter account number (e.g., 09876543210)" value="<?php echo htmlspecialchars($conaccountnumber); ?>" required>
        </div>
        <div class="input-group">
            <label for="new_password">Withdrawal password</label>
            <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
        </div>
        <div class="input-group">
            <label for="confirm_password">Confirm withdrawal password</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
        </div>
        <button type="submit" class="btn-login">Update</button>
    </form>
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
