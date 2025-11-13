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
            <a href="settings.php" class="active">
                <div class="Settings">
                    <i class="bi bi-gear"></i> Settings
                </div>
            </a>
        </div>
        <div class="page">
            <div class="profile" id="profile">
                <a href="#" onclick="openModal3(event)"><i class="bi bi-box-arrow-right" title="Logout"
                        style="font-size: 25px;"></i></a>
                <a href="#"><i class="bi bi-person-circle" title="Profile" style="font-size: 25px;"></i></a>
                <a href="#"><i class="bi bi-bell-fill" title="Notification" style="font-size: 25px;"></i></a>
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

                <div class="settings-section">
                    <h2><i class="bi bi-shield-check"></i> Security</h2>

                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Login History</h3>
                            <p>Recent login activities on your account</p>
                        </div>
                        <div class="setting-action">
                            <button class="btn-edit" onclick="viewLoginHistory()">View All</button>
                        </div>
                    </div>

                    <div style="margin-top: 15px;">
                        <div class="login-history-item">
                            <div class="device-info">
                                <i class="bi bi-laptop"></i>
                                <div>
                                    <strong>Current Device</strong>
                                    <div class="time">Taguig, Metro Manila • 2 hours ago</div>
                                </div>
                            </div>
                            <span style="color: #28a745; font-weight: bold;">Current</span>
                        </div>
                        <div class="login-history-item">
                            <div class="device-info">
                                <i class="bi bi-phone"></i>
                                <div>
                                    <strong>Other Devices</strong>
                                    <div class="time">Taguig, Metro Manila • Yesterday, 3:45 PM</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="setting-item" style="margin-top: 20px;">
                        <div class="setting-info">
                            <h3>Active Sessions</h3>
                            <p>Manage devices currently logged into your account</p>
                        </div>
                        <span class="setting-value">2 active</span>
                        <div class="setting-action">
                            <button class="btn-danger" onclick="logoutAllDevices()">Logout All</button>
                        </div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Security Questions</h3>
                            <p>For account recovery purposes</p>
                        </div>
                        <span class="setting-value">2 configured</span>
                        <div class="setting-action">
                            <button class="btn-edit" onclick="editSecurityQuestions()">Manage</button>
                        </div>
                    </div>
                </div>

                <div class="settings-section">
                    <h2><i class="bi bi-sliders"></i> Preferences</h2>

                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Language</h3>
                            <p>Choose your preferred language</p>
                        </div>
                        <span class="setting-value">English</span>
                        <div class="setting-action">
                            <button class="btn-edit" onclick="changeLanguage()">Change</button>
                        </div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Currency</h3>
                            <p>Display currency for transactions</p>
                        </div>
                        <span class="setting-value">PHP (₱)</span>
                        <div class="setting-action">
                            <button class="btn-edit" onclick="changeCurrency()">Change</button>
                        </div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Email Notifications</h3>
                            <p>Receive transaction alerts via email</p>
                        </div>
                        <div class="setting-action">
                            <div class="toggle-switch active" onclick="toggleNotification(this)"></div>
                        </div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>SMS Notifications</h3>
                            <p>Receive transaction alerts via SMS</p>
                        </div>
                        <div class="setting-action">
                            <div class="toggle-switch active" onclick="toggleNotification(this)"></div>
                        </div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Transaction Alerts</h3>
                            <p>Get notified for transactions above ₱1,000</p>
                        </div>
                        <span class="setting-value">₱1,000</span>
                        <div class="setting-action">
                            <button class="btn-edit" onclick="editThreshold()">Adjust</button>
                        </div>
                    </div>
                </div>

                <div class="settings-section">
                    <h2><i class="bi bi-lock"></i> Privacy & Data</h2>

                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Download Account Data</h3>
                            <p>Get a copy of your account information and transaction history</p>
                        </div>
                        <div class="setting-action">
                            <button class="btn-edit" onclick="downloadData()">Download</button>
                        </div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Close Account</h3>
                            <p style="color: #dc3545;">Permanently delete your eTapPay account</p>
                        </div>
                        <div class="setting-action">
                            <button class="btn-danger" onclick="closeAccount()">Close Account</button>
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
                <p>Logout your account?</p>
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