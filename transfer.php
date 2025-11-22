<?php
session_start();
include 'db.php';

$connectDB = connectDB();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}
// Logout handling
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $_SESSION = array();
    session_destroy();
    header("Location: index.php", true, 303);
    exit();
}

$user_email = $_SESSION['email'];

if (isset($_POST['transfer'])) {
    $recipient_email = trim($_POST['recipient_email']);
    $amount = floatval($_POST['amount']);
    $message = trim($_POST['message']);

    if ($amount <= 0) {
        $error = "Invalid amount.";
    } elseif ($recipient_email === $user_email) {
        $error = "You cannot send money to yourself.";
    } else {
        // Start transaction
        $connectDB->begin_transaction();

        try {
            // Get sender balance
            $stmt = $connectDB->prepare("SELECT id, balance FROM users WHERE email = ?");
            $stmt->bind_param("s", $user_email);
            $stmt->execute();
            $result = $stmt->get_result();
            $sender = $result->fetch_assoc();

            if (!$sender)
                throw new Exception("Sender not found.");
            if ($sender['balance'] < $amount)
                throw new Exception("Insufficient balance.");

            // Get recipient
            $stmt = $connectDB->prepare("SELECT id, balance FROM users WHERE email = ?");
            $stmt->bind_param("s", $recipient_email);
            $stmt->execute();
            $result = $stmt->get_result();
            $recipient = $result->fetch_assoc();

            if (!$recipient)
                throw new Exception("Recipient not found.");

            // Deduct from sender
            $new_sender_balance = $sender['balance'] - $amount;
            $stmt = $connectDB->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $stmt->bind_param("di", $new_sender_balance, $sender['id']);
            $stmt->execute();

            // Add to recipient
            $new_recipient_balance = $recipient['balance'] + $amount;
            $stmt = $connectDB->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $stmt->bind_param("di", $new_recipient_balance, $recipient['id']);
            $stmt->execute();

            // Optional: log the transaction in another table
            $stmt = $connectDB->prepare("INSERT INTO transactions (sender_id, recipient_id, amount, message, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt2 = $connectDB->prepare("INSERT INTO notifications (user_id, title, message) VALUES (?, ?, ?)");
            $notif_title = "You received ₱$amount";
            $notif_msg = "You got ₱$amount from $user_email. Message: $message";
            $stmt->bind_param("iids", $sender['id'], $recipient['id'], $amount, $message);
            $stmt2->bind_param("iss", $recipient['id'], $notif_title, $notif_msg);
            $stmt->execute();
            $stmt2->execute();

            $connectDB->commit();
            $success = "Transfer successful!";
        } catch (Exception $e) {
            $connectDB->rollback();
            $error = $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eTapPay</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@latest/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>
    <div class="whole">
        <div class="profiles" id="profile">
            <div class="logo ">
                <img src="images/logo.png" alt="" class="logo">
                <h3>eTapPay</h3>
            </div>
            <div class="profile">
                <a href="#" onclick="openModal3(event)">
                    <i class="bi bi-box-arrow-right" title="Logout" style="font-size: 25px;"></i>
                </a>
                <a href="#" onclick="openProfile(event)">
                    <i class="bi bi-person-circle" title="Profile" style="font-size: 25px;"></i>
                </a>
                <a href="#">
                    <i class="bi bi-bell-fill" title="Notification" style="font-size: 25px;"></i>
                </a>
            </div>
        </div>
        <div class="page">
            <div class="navs">
                <div class="nav-section">
                    <a href="dashboard.php">
                        <div class="Dashboard">
                            <i class="bi bi-house"></i> Dashboard
                        </div>
                    </a>
                    <a href="transfer.php" class="active">
                        <div class="Transfer">
                            <i class="bi bi-arrow-left-right"></i> Transfer
                        </div>
                    </a>
                    <a href="card.php">
                        <div class="Cards">
                            <i class="bi bi-credit-card"></i> Cards
                        </div>
                    </a>
                    <a href="loan4.php">
                        <div class="Loan">
                            <i class="bi bi-cash"></i> Loan
                        </div>
                    </a>
                    <a href="inbox.php">
                        <div class="Inbox">
                            <i class="bi bi-envelope"></i> Inbox
                        </div>
                    </a>
                    <a href="settings.php">
                        <div class="Settings">
                            <i class="bi bi-gear"></i> Settings
                        </div>
                    </a>
                </div>
            </div>
            <div class="transfer">
                <div class="content">
                    <h1>Transfer Funds</h1>

                    <?php if (!empty($error)): ?>
                        <div class="error" style="color:red;"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="success" style="color:green;"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>

                    <form class="inputs" method="POST" action="transfer.php" id="transferForm">
                        <div class="Personal">Send to
                            <input type="email" name="recipient_email" id="recipient_email" placeholder="Email"
                                required>
                        </div>
                        <div class="Personal">Amount(₱)
                            <input type="number" name="amount" id="amount" placeholder="Amount" min="1" required>
                        </div>
                        <div class="Personal">Message (Optional)
                            <input type="text" name="message" id="message" placeholder="Message for recipient">
                        </div>
                        <div class="next_prev">
                            <button type="button" id="reviewButton" class="review-btn">Review</button>
                        </div>

                        <!-- Hidden submit button -->
                        <button type="submit" name="transfer" id="hiddenSubmit" style="display:none;"></button>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <h2>Logout</h2>
            <div class="scrollable">
                <p>
                    Logout your accout?
                </p>
            </div>
            <button class="confirm-btn" onclick="window.location.href='?action=logout'">Logout</button>
            <button class="close-btn" onclick="closeModal3()">Close</button>
        </div>
    </div>
    <div id="reviewModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <h2>Review Transaction</h2>
            <div class="scrollable" style="display: flex; flex-direction: column; text-align: start;">
                <label>Send to</label>
                <input type="text" id="review_email" disabled>

                <label>Amount (₱)</label>
                <input type="text" id="review_amount" disabled>

                <label>Message</label>
                <input type="text" id="review_message" disabled>
            </div>

            <div style="margin-top:15px;">
                <button type="button" id="confirmButton" class="confirm-btn">Confirm</button>
                <button type="button" id="closeReviewModal" class="close-btn">Close</button>
            </div>
        </div>
    </div>
    <div id="profileModal" class="modal">
        <div class="modal-content" style="max-width: 490px;">
            <h2>Profile</h2>
            <div class="profile-section" style="display: flex; flex-direction: column; text-align: start;">
                Name
                <p class="items">Lorenz L. Narvaez</p>
                Number
                <p class="items">Lorenz L. Narvaez</p>
                Date of Birth
                <p class="items">Lorenz L. Narvaez</p>
                Current Address
                <p class="items">Lorenz L. Narvaez</p>
            </div>
            <button class="close-btn" onclick="closeProfile()">Close</button>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        window.addEventListener('pageshow', function (event) {
            if (event.persisted || (window.performance && window.performance.getEntriesByType('navigation')[0].type === 'back_forward')) {
                window.location.reload();
            }
        });
    </script>

</body>

</html>