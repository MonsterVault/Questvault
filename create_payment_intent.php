<?php
// Set your PayMongo Secret Key
$secret_key = 'sk_test_dwL5WWy3u2RoYE4NJNrL5Abi'; // Replace with your PayMongo Secret Key

// Initialize cURL
$ch = curl_init();

// Payment amount (in cents) and currency
$amount = 1000;  // Example: 1000 PHP cent (equivalent to 10 PHP)
$currency = 'PHP';

// PayMongo endpoint for creating a Payment Intent
$url = 'https://api.paymongo.com/v1/payment_intents';

// Prepare the data to send in the request body
$data = [
    'data' => [
        'attributes' => [
            'amount' => $amount,
            'currency' => $currency
        ]
    ]
];

// Encode data to JSON format
$json_data = json_encode($data);

// Set up the cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Basic ' . base64_encode($secret_key . ':') // Basic Auth with secret key
]);

// Execute the cURL request
$response = curl_exec($ch);

// Check if there were any errors in the cURL request
if(curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch);
    exit;
}

// Close the cURL session
curl_close($ch);

// Parse the response
$response_data = json_decode($response, true);

// If the request was successful, send back the payment intent client secret
if (isset($response_data['data']['attributes']['client_secret'])) {
    echo json_encode([
        'status' => 'success',
        'client_secret' => $response_data['data']['attributes']['client_secret']
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Payment intent creation failed']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay with PayMongo</title>
    <script src="https://paymongo.com/static/js/paymongo.js"></script> <!-- Include PayMongo's JS SDK -->
</head>
<body>
    <h1>Pay for Your Order</h1>
    <form id="payment-form">
        <label for="amount">Amount (PHP):</label>
        <input type="number" id="amount" name="amount" value="10" required>
        <br><br>
        <button type="submit" id="pay-button">Pay Now</button>
    </form>

    <script>
        const form = document.getElementById('payment-form');
        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            
            // Get the amount entered by the user
            const amount = document.getElementById('amount').value * 100; // Convert to cents
            
            // Call the backend to create a payment intent
            const response = await fetch('create_payment_intent.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ amount: amount, currency: 'PHP' })
            });

            const data = await response.json();

            if (data.status === 'success') {
                // Use the client secret returned by the backend
                const clientSecret = data.client_secret;

                // Call PayMongo's client-side SDK to handle the payment
                PayMongo.pay({
                    clientKey: 'YOUR_PUBLISHABLE_KEY', // Replace with your PayMongo Publishable Key
                    paymentIntent: clientSecret
                });
            } else {
                alert('Payment failed: ' + data.message);
            }
        });
    </script>
</body>
</html>
