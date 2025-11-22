<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $_SESSION = array();
    session_destroy();
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
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
                    <a href="transfer.php">
                        <div class="Transfer">
                            <i class="bi bi-arrow-left-right"></i> Transfer
                        </div>
                    </a>
                    <a href="card.php" class="active">
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
                <div class="content" id="cardContent">
                    <h1>Credit Card</h1>
                    <div class="applycard"><img src="images/credit-card.png" alt="" class="credit-card "></div>
                    <p>Don't have a credit card yet? Apply now with just a few simple</p>
                    <p>requirements and start enjoying the benefits today!</p>
                    <div class="inputs">
                        <div class="Personal" style="margin-top: 20px;">
                            <h3><i class="bi bi-check-circle-fill" style="color: #00226f;"></i> Fast and easy
                                application</h3>
                            <p>Submit your documents online and get updates straight from your device</p><br>
                            <h3><i class="bi bi-check-circle-fill" style="color: #00226f;"></i> Full transparency</h3>
                            <p>See all fees and terms before you accept your credit card offer</p>
                        </div>
                        <div class="next_prev">
                            <div><button class="next-btn" onclick="showApplicationForm()">Apply</button></div>
                        </div>
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

    <div id="cardModal" class="modal">
        <div class="modal-content" style="max-width: 500px;">
            <h2>Are you sure about the details for the card application?</h2>
            <button class="next-btn" onclick="">Confirm</button>
            <button class="close-btn" onclick="closeCard()">Close</button>
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