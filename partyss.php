<?php
session_start(); // Start the session

// Check if the session variable is set (i.e., user is logged in)
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Get the username from session
} else {
    // Redirect to the login page if the session is not set
    header("Location: index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Party - Quest Vault</title>
    <link rel="stylesheet" href="CSS/beastvault.css">
</head>
<body>
    <script type="text/javascript">
        // Transfer PHP session data to JavaScript
        var username = "<?php echo $username; ?>"; // Get the PHP session data in JS
        localStorage.setItem("username", username); // Store in localStorage
        window.location.href = "party.html"; // Redirect to the static HTML page
    </script>
</body>
</html>
