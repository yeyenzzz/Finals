<?php
session_start();

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
            <a href="loan.php" class="active">
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
        <div class="page">
            <div class="profile" id="profile">
                <a href="#" onclick="openModal3(event)"><i class="bi bi-box-arrow-right" title="Logout"
                        style="font-size: 25px; color;"></i></a>
                <a href="#"><i class="bi bi-person-circle" title="Profile" style="font-size: 25px; color;"></i></a>
                <a href="#"><i class="bi bi-bell-fill" title="Notification" style="font-size: 25px; color;"></i></a>
            </div>
            <div class="transfer">
                <div class="content">
                    <h1>Loan Application</h1>
                    <p>Please complete all fields below to check your eligibility and apply for a loan.</p>
                    <div class="inputs">
                        <div class="Personal">
                            <h1>| 2. Loan Details</h1>
                        </div>
                        <div class="Personal"> Loan Type <select>
                                <option value="" disabled selected class="disabled">Select Loan Type</option>
                                <option value="Business Loan">Business Loan</option>
                                <option value="Personal Loan">Personal Loan</option>
                                <option value="Educational Loan">Educational Loan</option>
                            </select></div>
                        <div class="Personal"> Desired Loan Amount (₱)<input type="number"
                                placeholder="Desired Loan Amount" required></div>
                        <div class="Personal"> Loan Term (Months)<select>
                                <option value="" disabled selected class="disabled">Select Loan Term</option>
                                <option value="12">12 months</option>
                                <option value="24">24 months</option>
                                <option value="36">36 months</option>
                            </select></div>
                        <div class="Personal"> Payment Frequency<select>
                                <option value="" disabled selected class="disabled">Select Payment Frequency</option>
                                <option value="Monthly">Monthly</option>
                                <option value="Bi-weekly">Bi-weekly</option>
                                <option value="Quarterly">Quarterly</option>
                            </select></div>
                        <div class="Personal"> Payment Type<select>
                                <option value="" disabled selected class="disabled">Select Payment Type</option>
                                <option value="Manual">Manual Payment</option>
                                <option value="Automatic">Automatic Payment (Auto Debit/Auto Pay)</option>
                            </select></div>
                        <div class="next_prev">
                            <a href="loan.php"><button class="prev-btn">Previous</button></a>
                            <a href="loan3.php"><button class="next-btn">Next</button></a>
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
    <script type="text/javascript">
        function preventBack() {
            window.history.forward();
        }
        setTimeout(preventBack, 0);
        window.onunload = function () { null };
    </script>

</body>

</html>