<?php
session_start();
$referal = isset($_GET['ref']) ? $_GET['ref'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gcash Payment</title>
    <style>
        /* General reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            height: 100vh;
            margin: 0;
        }

        /* Navigation Bar */
        .navbar {
            width: 100%;
            background-color: #007dfe;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px;
            font-size: 24px;
            font-weight: bold;
            height: 10%;
        }

        /* Style for the logo */
        .navbar img {
            height: 30px;
            width: 100px;
            margin-right: 10px;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            top: -40px;
            margin-top: 0;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 400px;

            /* Background Image */
            background-image: url('Images/gcash.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            z-index: 10;
        }

        .container2 {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 350px;
            background-color: #00009e;
            color: white;
        }

        .container2 input {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            background-color: #ffffff;
            color: black;
        }

        .btn {
            padding: 10px 20px;
            background-color: #007dfe;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 90%;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #1e90ff;
        }

        .note {
            margin-top: 10px;
            font-size: 12px;
            color: white;
            text-align: center;
        }
    </style>
    <script>
        function goToRecharge() {
            // Get the input values
            const amount = document.getElementById('amount').value;
            const mobile = document.getElementById('mobile').value;
            const ref = "<?php echo urlencode($referal); ?>";

            // Check if the fields are filled
            if (!amount || !mobile) {
                alert("Please fill in both the amount and mobile number.");
                return;
            }

            // Redirect to recharge.php with parameters
            const url = `recharge.php?ref=${ref}&amount=${encodeURIComponent(amount)}&mobile=${encodeURIComponent(mobile)}`;
            window.location.href = url;
        }
    </script>
</head>
<body>
    <nav class="navbar">
        <img src="Images/gcashlogo.jpg" alt="Gcash Logo">
    </nav>
    <div class="container">
        <h3 style="font-family: Arial; font-weight: bold; text-align: center; margin-top: 30px;">
            Merchant: ahahahahaha
        </h3>
        <div class="container2">
            <input type="text" id="amount" placeholder="Enter Amount">
            <input type="text" id="mobile" placeholder="Enter Mobile Number">
            <button class="btn" onclick="goToRecharge()">Next</button>
            <p class="note">
                Please enter the correct mobile number of your GCash account. Otherwise, the amount will not arrive in time.
            </p>
        </div>
    </div>
    <script>
        function goToRecharge() {
    // Get the input values
    const amount = document.getElementById('amount').value;
    const mobile = document.getElementById('mobile').value;
    const ref = "<?php echo urlencode($referal); ?>";

    // Check if the fields are filled
    if (!amount || !mobile) {
        alert("Please fill in both the amount and mobile number.");
        return;
    }

    // Redirect to recharge.php with parameters
    const url = `http://localhost/Beast%20Vault/recharge.php?ref=${ref}&amount=${encodeURIComponent(amount)}&contact=${encodeURIComponent(mobile)}`;
    window.location.href = url;
}

    </script>
</body>
</html>
