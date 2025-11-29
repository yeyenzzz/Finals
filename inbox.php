<?php
session_start();

// Force no caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $_SESSION = array();
    session_destroy();
    header("Location: index.php", true, 303);
    exit();
}

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

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
                    <a href="dashboard.php">
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
                    <a href="inbox.php" class="active">
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
                    <h1>All Inbox</h1>
                    <div class="inbox">
                        <a href="inbox.php" style="margin-right: 75px; padding: 7px;" class="notif-active">All</a>
                        <a href="updates.php" style="margin-right: 75px; padding: 7px;">Updates</a>
                        <a href="transaction.php" style="padding: 7px;">Transactions</a>
                    </div>
                    <div class="inputsnotif">
                    </div>
                </div>
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