<?php
session_start();
include 'db.php';
$conn = connectDB();

// Handle inline status update
if (isset($_POST['action']) && isset($_POST['card_id'])) {
    $cardId = intval($_POST['card_id']);
    $action = $_POST['action'];

    // Fetch the user ID associated with the card
    $stmt = $conn->prepare("SELECT user_id FROM credit_cards WHERE id = ?");
    $stmt->bind_param("i", $cardId);
    $stmt->execute();
    $stmt->bind_result($userId);
    if (!$stmt->fetch()) {
        $stmt->close();
        die("Invalid card ID");
    }
    $stmt->close();

    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE credit_cards SET status='Approved' WHERE id=?");
        $stmt->bind_param("i", $cardId);
        $stmt->execute();
        $stmt->close();

        $notif_title = "Card Request Approved";
        $notif_msg = "Your credit card request has been approved. You can now use your eTapPay card.";
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("DELETE FROM credit_cards WHERE id=?");
        $stmt->bind_param("i", $cardId);
        $stmt->execute();
        $stmt->close();

        $notif_title = "Card Request Rejected";
        $notif_msg = "We regret to inform you that your credit card request has been rejected. Please contact support for more information.";
    } elseif ($action === 'deactivate') {
        $stmt = $conn->prepare("DELETE FROM credit_cards WHERE id=?");
        $stmt->bind_param("i", $cardId);
        $stmt->execute();
        $stmt->close();

        $notif_title = "Card Deactivated";
        $notif_msg = "Your eTapPay credit card has been deactivated. If you have any questions, please contact support.";
    }

    // Insert notification
    $notif_senderName = "eTapPay Admin";
    $notif_type = "updates";
    $stmt = $conn->prepare("
        INSERT INTO notifications (user_id, title, message, sender_name, type, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("issss", $userId, $notif_title, $notif_msg, $notif_senderName, $notif_type);
    $stmt->execute();
    $stmt->close();

    header("Location: cardrequest.php");
    exit();
}


// Fetch all card requests with user info
$query = "
    SELECT cc.id AS card_id, cc.status, cc.created_at,
           u.firstName, u.lastName, u.phone_number, u.email, u.is_verified
    FROM credit_cards cc
    JOIN users u ON cc.user_id = u.id
    ORDER BY cc.created_at DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Requests | Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@latest/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="admin.css">
</head>

<body>
    <?php if (!empty($message)): ?>
        <script>alert("<?= $message ?>");</script>
    <?php endif; ?>

    <div class="whole">
        <div class="nav-section">
            <div class="logo">
                <img src="images/logo.png" alt="" class="logo">
                <h3>eTapPay</h3>
            </div>
            <a href="admin.php">
                <div class="Dashboard"><i class="bi bi-house"></i> Dashboard</div>
            </a>
            <a href="verification.php">
                <div class="Verify"><i class="bi bi-person-check"></i> Verify Users</div>
            </a>
            <a href="cardrequest.php" class="active">
                <div class="Card"><i class="bi bi-credit-card"></i> Card Requests</div>
            </a>
            <a href="loanapplication.php">
                <div class="Loan"><i class="bi bi-cash"></i> Loan Applications</div>
            </a>
            <a href="activeloan.php">
                <div class="activeloan"><i class="bi bi-coin"></i> Active Loans</div>
            </a>
        </div>

        <div class="page">
            <div class="profile" id="profile">
                <a href="#" onclick="openModal3(event)">
                    <i class="bi bi-box-arrow-right" title="Logout" style="font-size: 25px;"></i>
                </a>
            </div>

            <div class="content-section">
                <h2>Card Registration Requests</h2>

                <!-- Search and Filter -->
                <div class="filter-bar">
                    <input type="text" placeholder="Search user or account..." class="search-box">
                    <select>
                        <option>Status (All)</option>
                        <option>Pending</option>
                        <option>Approved</option>
                        <option>Rejected</option>
                    </select>
                </div>

                <!-- Requests Table -->
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Verified</th>
                            <th>Contact No.</th>
                            <th>Email Address</th>
                            <th>Date Requested</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) ?></td>
                                <td><?= $row['is_verified'] ? 'Yes' : 'No' ?></td>
                                <td><?= htmlspecialchars($row['phone_number']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= date("M d, Y", strtotime($row['created_at'])) ?></td>
                                <td>
                                    <span
                                        class="badge <?= strtolower($row['status']) ?>"><?= ucfirst($row['status']) ?></span>
                                </td>
                                <td>
                                    <?php if ($row['status'] == 'Pending'): ?>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="card_id" value="<?= $row['card_id'] ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="approve-btn">Approve</button>
                                        </form>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="card_id" value="<?= $row['card_id'] ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="reject-btn">Reject</button>
                                        </form>
                                    <?php elseif ($row['status'] == 'Approved'): ?>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="card_id" value="<?= $row['card_id'] ?>">
                                            <input type="hidden" name="action" value="deactivate">
                                            <button type="submit" class="reject-btn">Deactivate</button>
                                        </form>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>

                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <h2>Logout</h2>
            <div class="scrollable">
                <p>Logout your account?</p>
            </div>
            <button class="confirm-btn" onclick="confirmLogout()">Logout</button>
            <button class="close-btn" onclick="closeModal3()">Close</button>
        </div>
    </div>

    <script src="script.js"></script>
</body>

</html>