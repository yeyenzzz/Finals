<?php
session_start();

// Force no caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to login if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.php", true, 303);
    exit();
}

// Logout handling 
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $_SESSION = array();
    session_destroy();
    header("Location: index.php", true, 303);
    exit();
}

// Database connection
include 'db.php';
$connectDB = connectDB();

// Fetch user info including balance
$email = $_SESSION['email'];
$stmt = $connectDB->prepare("SELECT id, firstName, lastName, date_of_birth, address, balance, is_verified FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($user_id, $firstName, $lastName, $date_of_birth, $address, $balance, $is_verified);
$stmt->fetch();
$stmt->close();

// Fetch Sent Transactions
$sentQuery = $connectDB->prepare("
    SELECT t.id, t.amount, t.message, t.created_at, u.firstName AS recipient_name
    FROM transactions t
    JOIN users u ON t.recipient_id = u.id
    WHERE t.sender_id = ?
    ORDER BY t.created_at DESC
");
$sentQuery->bind_param("i", $user_id);
$sentQuery->execute();
$sentResult = $sentQuery->get_result();

// Fetch Received Transactions
$receivedQuery = $connectDB->prepare("
    SELECT t.id, t.amount, t.message, t.created_at, u.firstName AS sender_name
    FROM transactions t
    JOIN users u ON t.sender_id = u.id
    WHERE t.recipient_id = ?
    ORDER BY t.created_at DESC
");
$receivedQuery->bind_param("i", $user_id);
$receivedQuery->execute();
$receivedResult = $receivedQuery->get_result();

$depositError = "";

if (isset($_POST['depositAmount'])) {
    $amount = floatval($_POST['depositAmount']);

    if ($amount <= 0) {
        $depositError = "Please enter a valid amount.";
    } else {
        $update = $connectDB->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $update->bind_param("di", $amount, $user_id);
        $update->execute();
        $update->close();

        header("Location: dashboard.php", true, 303);
        exit();
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
                <a href="#" onclick="openProfile(event)">
                    <i class="bi bi-person-circle" title="Profile"></i>
                </a>
                <a href="#" onclick="openModal3(event)">
                    <i class="bi bi-box-arrow-right" title="Logout"></i>
                </a>
            </div>
        </div>

        <div class="page">
            <div class="navs">
                <div class="nav-section">
                    <a href="dashboard.php" class="active">
                        <div class="Dashboard">
                            <i class="bi bi-house"></i> Dashboard
                        </div>
                    </a>
                    <a href="transfer.php">
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
            <div class="handle">
                <div class="greet">
                    <div>
                        <h1>Welcome back, <?= htmlspecialchars($_SESSION['firstName']) ?>!
                        </h1>
                        <p>Monitor your balance, review transactions, and manage your finances effortlessly.</p>
                    </div>
                    <div class="cards">
                        <div class="card-design" id="card1"></div>
                        <div class="card-design" id="card2"></div>
                        <div class="card-design" id="card3"></div>
                        <div class="card-design" id="card4"></div>
                    </div>
                </div>
                <div class="section">
                    <!-- Balance -->
                    <div class="balance">
                        <h3>Account Balance</h3>
                        <p style="font-size: 30px; margin: 20px;">₱<?= number_format($balance, 2) ?></p>
                        <button class="next-btn" style="width: 100px; margin: 10px;"
                            onclick="openCashin()">Deposit</button>
                    </div>
                    <!-- Transactions with Nav Filter -->
                    <div class="transaction">
                        <h3>Transactions</h3>
                        <!-- Nav Filter -->
                        <div class="transaction-nav" style="margin-top: 10px;">
                            <button class="filter-btn active" data-target="sent">Sent Transactions</button>
                            <button class="filter-btn" data-target="received">Received Transactions</button>
                        </div>
                        <!-- Sent Transactions -->
                        <div class="transactions-section" id="sent">
                            <?php if ($sentResult->num_rows > 0): ?>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>To</th>
                                            <th>Amount</th>
                                            <th>Message</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $sentResult->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['recipient_name']) ?></td>
                                                <td class="amount-sent">-₱<?= number_format($row['amount'], 2) ?></td>
                                                <td><?= htmlspecialchars($row['message']) ?></td>
                                                <td><?= htmlspecialchars($row['created_at']) ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="no-transactions">No sent transactions yet.</div>
                            <?php endif; ?>
                        </div>
                        <!-- Received Transactions -->
                        <div class="transactions-section" id="received" style="display: none;">
                            <?php if ($receivedResult->num_rows > 0): ?>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>From</th>
                                            <th>Amount</th>
                                            <th>Message</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $receivedResult->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['sender_name']) ?></td>
                                                <td class="amount-received">+₱<?= number_format($row['amount'], 2) ?></td>
                                                <td><?= htmlspecialchars($row['message']) ?></td>
                                                <td><?= htmlspecialchars($row['created_at']) ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="no-transactions">No received transactions yet.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Modal -->
    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <h2>Logout</h2>
            <div class="scrollable">
                <p>Logout your account?</p>
            </div>
            <button class="confirm-btn" onclick="window.location.href='?action=logout'">Logout</button>
            <button class="close-btn" onclick="closeModal3()">Close</button>
        </div>
    </div>

    <div id="cashinModal" class="modal">
        <div class="modal-content" style="max-width: 500px;">
            <form method="post" action="dashboard.php">
                <h2>Deposit</h2>
                <div class="scrollable" style="display: flex; flex-direction: column; text-align: start;">
                    Amount
                    <input type="number" name="depositAmount" placeholder="Amount" style="width: 100%;" required min="1"
                        step="0.01">
                    <span style="color: red; font-size: 14px; margin-top: 5px;">
                        <?= htmlspecialchars($depositError) ?>
                    </span>
                </div>
                <button type="submit" class="next-btn" onclick="">Confirm</button>
                <button type="button" class="close-btn" onclick="closeCashin()">Close</button>
            </form>
        </div>
    </div>
    <div id="profileModal" class="modal">
        <div class="modal-content" style="max-width: 490px;">
            <h2>Profile</h2>
            <div class="profile-section" style="display: flex; flex-direction: column; text-align: start;">
                Name
                <p class="items">
                    <?= htmlspecialchars($_SESSION['firstName'] ?? '') ?>
                    <?= htmlspecialchars($_SESSION['lastName'] ?? '') ?>
                </p>
                Phone Number
                <p class="items"><?= htmlspecialchars($_SESSION['phone_number'] ?? '') ?></p>
                Date of Birth
                <p class="items"><?= htmlspecialchars($_SESSION['date_of_birth'] ?? '') ?></p>
                Current Address
                <p class="items"><?= htmlspecialchars($_SESSION['address'] ?? '') ?></p>
            </div>
            <div class="profile-btn">
                <?php
                if (is_null($is_verified)) {
                    echo '<button class="next-btn" onclick="showverifyID()">Verify account</button>';
                } elseif ($is_verified == 0) {
                    echo '<button class="next-btn" disabled>PENDING</button>';
                } elseif ($is_verified == 1) {
                    echo '<button class="next-btn" disabled>VERIFIED</button>';
                }
                ?>
                <button class="close-btn" onclick="closeProfile()">Close</button>
            </div>
        </div>
        <script src="script.js"></script>
        <script>
            // Ensure page reload on back navigation
            window.addEventListener('pageshow', function (event) {
                if (event.persisted || (window.performance && window.performance.getEntriesByType('navigation')[0].type === 'back_forward')) {
                    window.location.reload();
                }
            });
        </script>

        <script>
            const USER_ID = "<?= $user_id ?>";
            const USER_NAME = "<?= $firstName . ' ' . $lastName ?>";
            const USER_DOB = "<?= $date_of_birth ?>";
            const USER_ADDRESS = "<?= $address ?>";
        </script>


</body>

</html>