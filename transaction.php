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
        <div class="nav-section">
            <div class="logo">
                <img src="images/logo.png" alt="" class="logo">
                <h3>eTapPay</h3>
            </div>
            <a href="dashboard.php">
                <div class="Dashboard">
                    <i class="bi bi-house"></i> Dashboard
                </div>
            </a>
            <a href="account.php">
                <div class="Account">
                    <i class="bi bi-person"></i> Account
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
        <div class="page">
            <div class="profile" id="profile">
                <a href="#" onclick="openModal3(event)"><i class="bi bi-box-arrow-right" title="Logout"
                        style="font-size: 25px; color;"></i></a>
                <a href="#"><i class="bi bi-person-circle" title="Profile" style="font-size: 25px; color;"></i></a>
                <a href="#"><i class="bi bi-bell-fill" title="Notification" style="font-size: 25px; color;"></i></a>
            </div>
            <div class="transfer">
                <div class="content">
                    <h1>All Inbox</h1>
                    <div class="inbox">
                        <a href="inbox.php" style="margin-right: 75px; padding: 7px;">All</a>
                        <a href="updates.php" style="margin-right: 75px; padding: 7px;">Updates</a>
                        <a href="transaction.php" style="padding: 7px;">Transactions</a>
                    </div>
                    <div class="inputs">
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