<?php
session_start();

// Database connection settings
 include('db.php'); 

// Ensure that referral code exists in session
if (!isset($_SESSION['referral_code'])) {
    echo 'session_error'; // Respond if session referral_code is not set
    exit();
}

if (isset($_POST['quest_id'])) {
    $quest_id = $_POST['quest_id'];
    $referral_code = $_SESSION['referral_code']; // Referral code from session
    $today_date = date('Y-m-d');

    // Prepare the SQL statement to check if the quest is available to claim
    $check_claim_sql = $conn->prepare("SELECT * FROM `quest` WHERE id = ? AND referal = ? AND (click != ? OR click IS NULL)");
    if ($check_claim_sql === false) {
        die('Error preparing SQL statement: ' . $conn->error);
    }
    $check_claim_sql->bind_param("sss", $quest_id, $referral_code, $today_date);

    // Execute the query
    $check_claim_sql->execute();
    $check_claim_result = $check_claim_sql->get_result();

    // If the quest is available (not claimed yet)
    if ($check_claim_result->num_rows > 0) {
        // Prepare the SQL statement to update the click field
        $claim_sql = $conn->prepare("UPDATE `quest` SET click = ? WHERE id = ? AND referal = ?");
        if ($claim_sql === false) {
            die('Error preparing SQL statement: ' . $conn->error);
        }
        $claim_sql->bind_param("sss", $today_date, $quest_id, $referral_code);

        // Execute the update query
        if ($claim_sql->execute()) {
            // Retrieve the reward amount from the quest table
            $reward_query = $conn->prepare("SELECT reward FROM `quest` WHERE id = ?");
            if ($reward_query === false) {
                die('Error preparing SQL statement: ' . $conn->error);
            }
            $reward_query->bind_param("s", $quest_id);
            $reward_query->execute();
            $reward_result = $reward_query->get_result();

            // If a reward is found
            if ($reward_result->num_rows > 0) {
                $reward_row = $reward_result->fetch_assoc();
                $reward_amount = $reward_row['reward']; // Assuming reward is a numeric value

                // Update the user's balance
                $update_balance_sql = $conn->prepare("UPDATE `user` SET balance = balance + ? WHERE referal = ?");
                if ($update_balance_sql === false) {
                    die('Error preparing SQL statement: ' . $conn->error);
                }
                $update_balance_sql->bind_param("ds", $reward_amount, $referral_code);

                // Execute the update query for balance
                if ($update_balance_sql->execute()) {
                    echo 'success'; // Respond with success if everything is fine
                } else {
                    echo 'balance_error'; // Respond if there is an issue updating the balance
                }

                $update_balance_sql->close();
            } else {
                echo 'reward_error'; // Respond if no reward data is found
            }

            $reward_query->close();
        } else {
            echo 'claim_error'; // Respond if there is an issue claiming the quest
        }

        $claim_sql->close();
    } else {
        echo 'already_claimed'; // Respond if the quest has already been claimed today
    }

    $check_claim_sql->close();
} else {
    echo 'missing_quest_id'; // If no quest ID is passed, respond with an error
}

// Close the database connection
$conn->close();
?>
