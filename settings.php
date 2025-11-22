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

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eTapPay - Settings</title>
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
                    <a href="settings.php" class="active">
                        <div class="Settings">
                            <i class="bi bi-gear"></i> Settings
                        </div>
                    </a>
                </div>
            </div>


            <div class="settings-container">
                <h1 style="margin-bottom: 30px;">Settings</h1>

                <div class="settings-section">
                    <h2><i class="bi bi-person-gear"></i> Account Settings</h2>

                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Email Address</h3>
                            <p>Your registered email for account access and notifications</p>
                        </div>
                        <span class="setting-value"><?= htmlspecialchars($_SESSION['email']) ?></span>
                        <div class="setting-action">
                            <button class="btn-edit" onclick="editEmail()">Change</button>
                        </div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Password</h3>
                            <p>Last changed 30 days ago</p>
                        </div>
                        <span class="setting-value">••••••••</span>
                        <div class="setting-action">
                            <button class="btn-edit" onclick="changePassword()">Change</button>
                        </div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Phone Number</h3>
                            <p>Used for SMS notifications and verification</p>
                        </div>
                        <span class="setting-value">+63 9679 934 528</span>
                        <div class="setting-action">
                            <button class="btn-edit" onclick="editPhone()">Update</button>
                        </div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Two-Factor Authentication</h3>
                            <p>Add an extra layer of security to your account</p>
                        </div>
                        <div class="setting-action">
                            <div class="toggle-switch" onclick="toggle2FA(this)"></div>
                        </div>
                    </div>
                </div>

                <div class="settings-section">
                    <h2><i class="bi bi-person-vcard"></i> Personal Information</h2>

                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Full Name</h3>
                            <p>Your legal name as per ID</p>
                        </div>
                        <span class="setting-value"><?= htmlspecialchars($_SESSION['firstName']) ?> [Last Name]</span>
                        <div class="setting-action">
                            <button class="btn-edit" onclick="editName()">Edit</button>
                        </div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Date of Birth</h3>
                            <p>Cannot be changed after verification</p>
                        </div>
                        <span class="setting-value">March 12, 2005</span>
                        <div class="setting-action">
                            <button class="btn-edit" disabled style="opacity: 0.5;">Verified</button>
                        </div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Address</h3>
                            <p>Your current residential address</p>
                        </div>
                        <span class="setting-value">Sabang Lipa City</span>
                        <div class="setting-action">
                            <button class="btn-edit" onclick="editAddress()">Update</button>
                        </div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>ID Verification</h3>
                            <p>Government-issued ID verification status</p>
                        </div>
                        <span class="setting-value" style="color: #28a745;">✓ Verified</span>
                        <div class="setting-action">
                            <button class="btn-edit" onclick="viewID()">View</button>
                        </div>
                    </div>
                </div>

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