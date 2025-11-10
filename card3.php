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
            <a href="card.php" class="active">
                <div class="Cards">
                    <i class="bi bi-credit-card"></i> Cards
                </div>
            </a>
            <a href="loan.php">
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
                    <h1>Credit Card</h1>
                    <p>Please complete all fields below to check your eligibility and apply for a loan.</p>
                    <div class="inputs">
                        <div class="Personal">
                            <h1>| Summary</h1>
                        </div>
                        <div class="Personal"> Full Name<input type="text" placeholder="Full Name" disabled></div>
                        <div class="Personal"> Age (21+)<input type="text" placeholder="Age" disabled></div>
                        <div class="Personal"> Email Address<input type="text" placeholder="Email Address" disabled>
                        </div>
                        <div class="Personal"> Contact Number<input type="text" placeholder="Contact Number" disabled>
                        </div>
                        <div class="Personal"> Address<input type="text" placeholder="Address" disabled>
                        </div>
                        <div class="Personal"> Monthly Salary (₱)<input type="text" placeholder="Contact Number"
                                disabled>
                        </div>
                        <div class="Personal">Upload Valid ID <input type="text" placeholder="Upload Valid ID" disabled>
                        </div>
                        <div class="Personal">Upload PaySlip (3 Months) <input type="text" placeholder="Upload Valid ID"
                                disabled></div>

                        <div class="next_prev">
                            <a href="card2.php"><button class="prev-btn">Previous</button></a>
                            <a href="#"><button class="next-btn">Submit</button></a>
                        </div>
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