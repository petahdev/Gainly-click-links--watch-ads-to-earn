<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: index.php");
    exit();
}

// Connect to the database
include 'connect.php';

// Get the user ID from the session
$userId = $_SESSION['user_id'];

// Check if the username and mobilenumber are set in the session
if (!isset($_SESSION['username']) || !isset($_SESSION['mobilenumber'])) {
    // If either is not set, fetch them from the database
    $query = "SELECT username, mobilenumber FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($username, $mobilenumber);
    $stmt->fetch();
    $stmt->close();

    // Store the fetched username and mobilenumber in the session
    $_SESSION['username'] = $username;
    $_SESSION['mobilenumber'] = $mobilenumber;
} else {
    // Get the username and mobilenumber from the session
    $username = $_SESSION['username'];
    $mobilenumber = $_SESSION['mobilenumber'];
}

// Fetch the user's balance from the funds table
$query = "SELECT balance FROM funds WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($balance);
$stmt->fetch();
$stmt->close();

// Set default balance if not set or is null
if ($balance === null) {
    $balance = '0.00';
}

// Get the current hour
$currentHour = (int)date('G'); // 'G' gives the hour in 24-hour format without leading zeros

// Determine the appropriate greeting
if ($currentHour >= 6 && $currentHour < 12) {
    $greeting = 'Good Morning';
} elseif ($currentHour >= 12 && $currentHour < 18) {
    $greeting = 'Good Afternoon';
} else {
    $greeting = 'Good Evening';
}

// Fetch the total click count and calculate the total amount earned
$amount_per_click = 4; // Amount in Ksh per click

// Fetch the total click count for the user
$query = "SELECT SUM(click_count) AS total_clicks FROM link_clicks WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_clicks = $row['total_clicks'] ?? 0; // Default to 0 if no clicks

// Calculate the total amount earned
$total_amount_earned = $total_clicks * $amount_per_click;

// Close the database connection
$conn->close();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw Funds</title>
    <link rel="stylesheet" href="styles1.css"> <!-- Link to your CSS file -->
    <style>
        /* Error message styling */
        .error-message {
            color: #ff4d4d;
            font-size: 14px;
            margin-top: 10px;
        }
        .form-control {
            border: 1px solid #ccc;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-control.error {
            border-color: #ff4d4d;
        }
    </style>
    <script>
        function validateForm(event) {
            var amount = document.getElementById("amount").value;
            var errorElement = document.getElementById("error-message");

            // Check if the amount is less than 2000
            if (amount < 2000) {
                event.preventDefault(); // Prevent form submission
                errorElement.innerText = "Minimum withdrawal amount is 2000 Ksh.";
                document.getElementById("amount").classList.add("error");
            } else {
                errorElement.innerText = ""; // Clear error if valid
                document.getElementById("amount").classList.remove("error");
            }
        }
    </script>
</head>
<body style="background-color: #202221;">
    <div class="container">
        <h1>Withdraw Funds</h1>
        <form action="withdraw.php" method="POST" class="withdrawal-form" onsubmit="validateForm(event)">
            <!-- Form group for mobile number -->
            <div class="form-group">
                <label for="mobilenumber">Mobile Number:</label>
                <input type="text" name="mobilenumber" 
                       placeholder="<?php echo isset($mobilenumber) ? htmlspecialchars($mobilenumber) : 'Enter your number'; ?>" 
                       class="form-control" required>
            </div>
            
            <!-- Form group for amount -->
            <div class="form-group">
                <label for="amount">Amount (Ksh):</label>
                <input type="number" id="amount" name="amount" required min="1" 
                       value="<?php echo isset($total_amount_earned) ? htmlspecialchars($total_amount_earned) : '0.00'; ?>" 
                       class="form-control" readonly>
                <div id="error-message" class="error-message"></div> <!-- Error message area -->
            </div>

            <div class="form-group">
                <input type="submit" value="Withdraw" class="btn btn-primary">
            </div>
        </form>
    </div>
</body>
</html>
