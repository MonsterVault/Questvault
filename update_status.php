<?php
include('db.php');

// Check if the request is valid
if (isset($_POST['id']) && isset($_POST['status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    // Update the status in the database
    $sql = "UPDATE recharge SET status = ? WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("si", $status, $id);
        if ($stmt->execute()) {
            echo "Status updated successfully";
        } else {
            echo "Error updating status";
        }
    } else {
        echo "Error preparing statement";
    }
} else {
    echo "Invalid request";
}
?>
