<?php
session_start(); // Start the session to access session data

// Get the referral ID from the query string if it's present in the URL
if (isset($_GET['ref'])) {
    $referal = $_GET['ref'];
} else {
    $referal = 'No referral ID found';
}

 include('db.php'); 


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Quest Vault</title>
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

    <!-- Menu Container -->
<div class="menu-container" style="margin-top: 80px">
    
</div>











</body>
</html>
