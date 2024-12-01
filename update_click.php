<?php
session_start();

 include('db.php'); 

// Get the posted data
$quest_id = $_POST['quest_id'];
$click_date = $_POST['click_date'];
$referral_code = $_POST['referral_code'];

// Get the quest reward from the database
$quest_sql = $conn->prepare("SELECT reward, click FROM `quest` WHERE id = ? AND referal = ?");
$quest_sql->bind_param("is", $quest_id, $referral_code);
$quest_sql->execute();
$quest_result = $quest_sql->get_result();

// Check if the quest exists
if ($quest_result->num_rows > 0) {
    $quest = $quest_result->fetch_assoc();
    
    // Check if the quest has already been claimed today
    if ($quest['click'] != $click_date) {
        // Fetch the user's current balance
        $user_sql = $conn->prepare("SELECT balance FROM `user` WHERE referal = ?");
        $user_sql->bind_param("s", $referral_code);
        $user_sql->execute();
        $user_result = $user_sql->get_result();

        if ($user_result->num_rows > 0) {
            $user = $user_result->fetch_assoc();
            $current_balance = $user['balance'];
            $reward = $quest['reward'];

            // Calculate the new balance
            $new_balance = $current_balance + $reward;

            // Update the user's balance
            $update_balance_sql = $conn->prepare("UPDATE `user` SET balance = ? WHERE referal = ?");
            $update_balance_sql->bind_param("ds", $new_balance, $referral_code);
            $update_balance_sql->execute();

            // Update the quest's click field to today's date
            $update_quest_sql = $conn->prepare("UPDATE `quest` SET click = ? WHERE id = ?");
            $update_quest_sql->bind_param("si", $click_date, $quest_id);
            $update_quest_sql->execute();

            // Send a response with the updated balance
            echo json_encode([
                'status' => 'success',
                'newBalance' => $new_balance
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not found.']);
        }
    } else {
        // Quest has already been claimed today
        echo json_encode(['status' => 'claimed']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Quest not found or referral code mismatch.']);
}

$conn->close();
?>
