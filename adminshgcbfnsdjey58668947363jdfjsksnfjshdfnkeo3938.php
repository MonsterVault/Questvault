<?php
session_start(); // Start the session to access session data

// Get the referral ID from the query string if it's present in the URL
if (isset($_GET['ref'])) {
    $referal = $_GET['ref'];
} else {
    $referal = 'No referral ID found';
}

include('db.php');

// Pagination: Number of records per page
$records_per_page = 10;

// Get the current page number from the query string, default is page 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $records_per_page;

// Initialize search variables
$where_clauses = [];

// Initialize search variables
$where_clauses = [];

// Check if the form is cleared by verifying if the search fields are empty
if (isset($_GET['username']) && $_GET['username'] != '') {
    $username = $_GET['username'];
    // Search for both username and contact
    $where_clauses[] = "(username LIKE '%$username%' OR contact LIKE '%$username%')";
}
if (isset($_GET['date_from']) && $_GET['date_from'] != '') {
    $date_from = $_GET['date_from'];
    $where_clauses[] = "datetime >= '$date_from'";
}
if (isset($_GET['date_to']) && $_GET['date_to'] != '') {
    $date_to = $_GET['date_to'];
    $where_clauses[] = "datetime <= '$date_to'";
}

// Always include the 'status' filter if no status is provided in the query
$where_clauses[] = "status = ''";

// Build SQL query with conditions for search
$where_sql = '';
if (count($where_clauses) > 0) {
    $where_sql = "WHERE " . implode(' AND ', $where_clauses);
}

// Fetch data from the recharge table for the given referral ID with pagination, search filter, and order by ID descending
$sql = "SELECT * FROM recharge $where_sql ORDER BY id DESC LIMIT $start_from, $records_per_page";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Quest Vault</title>
    <link rel="stylesheet" href="CSS/admin.css">
    <style>
      
    </style>
</head>
<body>

<!-- Top Navigation Bar -->
<div class="top-nav">
    <div class="logo-container">
        <img src="Images/treasure-chest.png" alt="Treasure Chest" class="logo"> <!-- Logo -->
        <h1 class="brand-name">Quest Vault</h1> <!-- Brand name -->
    </div>
</div>

<!-- Search Form -->


<!-- Menu Container -->
<div class="menu-container" style="margin-top: 50px">
    <!-- Recharge and Withdrawals Buttons -->
    <div class="button-container">
        <button class="card-button" onclick="window.location.href='recharges.php'">Recharge</button>
        <button class="card-button" onclick="window.location.href='withdrawals.php'">Withdrawals</button>
        <button style="background-color: red;" class="card-button" onclick="window.location.href='index.html'">Logout</button>
    </div>

    <!-- Table displaying recharge data -->
    <div class="card">
        <h3>Recharge Details</h3>


<form method="GET" action="">
        <input class="input" type="text" name="username" placeholder="Username" value="<?php echo isset($username) ? $username : ''; ?>">
        <input class="input" type="date" name="date_from" value="<?php echo isset($date_from) ? $date_from : ''; ?>">
        <input  class="input" type="date" name="date_to" value="<?php echo isset($date_to) ? $date_to : ''; ?>">
        <button type="submit" class="card-button">Search</button>
        <button type="reset" class="card-button" style="background-color: #ccc;">Clear</button> <!-- Clear button -->
    </form>



        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th> <!-- New Column for Username -->
                        <th>Referral</th>
                        <th>Amount</th>
                        <th>Contact</th>
                        <th>Date and Time</th>
                        <th>Status</th> <!-- New Column for Status -->
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['username']; ?></td> <!-- Display Username -->
                            <td><?php echo $row['referal']; ?></td>
                            <td><?php echo $row['amount']; ?></td>
                            <td><?php echo $row['contact']; ?></td>
                            <td><?php echo $row['datetime']; ?></td>
                            <td><?php echo $row['status']; ?></td> <!-- Display Status -->
                           <td>
    <button class="recharged-btn" onclick="markRecharged(<?php echo $row['id']; ?>)">Recharged</button>
    <button class="decline-btn" onclick="markDeclined(<?php echo $row['id']; ?>)">Decline</button>
</td>

                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No recharge records found for this referral.</p>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <?php
        // Display pagination links
        for ($i = 1; $i <= $total_pages; $i++) {
            echo "<a href='?page=$i'>$i</a>";
        }
        ?>
    </div>
</div>

<script>
    // When the "Clear" button is clicked, reset the form and show all data
    document.querySelector('button[type="reset"]').addEventListener('click', function() {
        window.location.href = 'adminshgcbfnsdjey58668947363jdfjsksnfjshdfnkeo3938.php'; // Reload the page to show all data
    });
    // Function to mark as Recharged
function markRecharged(id) {
    if (confirm("Are you sure you want to mark this as Recharged?")) {
        updateStatus(id, 'Recharged');
    }
}

// Function to mark as Declined
function markDeclined(id) {
    if (confirm("Are you sure you want to mark this as Declined?")) {
        updateStatus(id, 'Declined');
    }
}

// Function to update the status in the database
function updateStatus(id, status) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "update_status.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if (xhr.status == 200) {
            alert("Status updated to " + status);
            location.reload(); // Reload the page to reflect the updated status
        }
    };
    xhr.send("id=" + id + "&status=" + status);
}

</script>

</body>
</html>
