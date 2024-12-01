<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invalid - Quest Vault</title>
    <link rel="stylesheet" href="CSS/beastvault.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    
</head>
<body >
    <div class="login-container">
        <img src="Images/check.png" alt="Treasure Chest" width="40%" />
        <h1 >Purchased sucessfully!</span></h1>
        <p class="tagline"></p>

       
            
          <button 
    type="submit" 
    class="btn-login" 
    onclick="window.location.href='main.php?ref=<?php echo urlencode($_GET['ref'] ?? ''); ?>';">
   Close
</button>
           
    </div>

  
</body>
</html>
