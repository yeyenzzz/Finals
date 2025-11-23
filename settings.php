<?php
session_start();
include 'db.php'; // Needed for session check

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
    <title>eTapPay - Settings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@latest/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .setting-value[contenteditable="true"] {
            border-bottom: 1px dashed #000;
        }
    </style>
</head>

<body>
    <div class="whole">
        <div class="profiles" id="profile">
            <div class="logo">
                <img src="images/logo.png" alt="" class="logo">
                <h3>eTapPay</h3>
            </div>
            <div class="profile">
                <a href="#">
                    <i class="bi bi-bell-fill" title="Notification" style="font-size: 25px;"></i>
                </a>
                <a href="#" onclick="openProfile(event)">
                    <i class="bi bi-person-circle" title="Profile" style="font-size: 25px;"></i>
                </a>
                <a href="#" onclick="openModal3(event)">
                    <i class="bi bi-box-arrow-right" title="Logout" style="font-size: 25px;"></i>
                </a>

            </div>
        </div>

        <div class="page">
            <div class="navs">
                <div class="nav-section">
                    <a href="dashboard.php">
                        <div class="Dashboard"><i class="bi bi-house"></i> Dashboard</div>
                    </a>
                    <a href="transfer.php">
                        <div class="Transfer"><i class="bi bi-arrow-left-right"></i> Transfer</div>
                    </a>
                    <a href="card.php">
                        <div class="Cards"><i class="bi bi-credit-card"></i> Cards</div>
                    </a>
                    <a href="loan4.php">
                        <div class="Loan"><i class="bi bi-cash"></i> Loan</div>
                    </a>
                    <a href="inbox.php">
                        <div class="Inbox"><i class="bi bi-envelope"></i> Inbox</div>
                    </a>
                    <a href="settings.php" class="active">
                        <div class="Settings"><i class="bi bi-gear"></i> Settings</div>
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
                        <span class="setting-value"
                            data-field="email"><?= htmlspecialchars($_SESSION['email']) ?></span>
                    </div>

                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Phone Number</h3>
                            <p>Used for sending and receiving funds</p>
                        </div>
                        <span class="setting-value"
                            data-field="phone"><?= htmlspecialchars($_SESSION['phone_number'] ?? '') ?></span>
                        <div class="setting-action">
                            <button class="btn-edit">Save</button>
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
                        <span class="setting-value"
                            data-field="firstName"><?= htmlspecialchars($_SESSION['firstName'] ?? '') ?></span>
                        <span class="setting-value"
                            data-field="lastName"><?= htmlspecialchars($_SESSION['lastName'] ?? '') ?></span>
                        <div class="setting-action">
                            <button class="btn-edit">Save</button>
                        </div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Date of Birth</h3>
                            <p>Cannot be changed after verification</p>
                        </div>
                        <span class="setting-value"
                            data-field="date_of_birth"><?= htmlspecialchars($_SESSION['date_of_birth'] ?? 'Set Birthdate') ?></span>
                        <div class="setting-action">
                            <button class="btn-edit">Save</button>
                        </div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Address</h3>
                            <p>Your current residential address</p>
                        </div>
                        <span class="setting-value"
                            data-field="address"><?= htmlspecialchars($_SESSION['address'] ?? 'Set Address') ?></span>
                        <div class="setting-action">
                            <button class="btn-edit">Save</button>
                        </div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-info">
                            <h3>Valid ID</h3>
                            <p>Government-issued ID number or type</p>
                        </div>
                        <span class="setting-value"
                            data-field="valid_id"><?= htmlspecialchars($_SESSION['valid_id'] ?? '') ?></span>
                        <div class="setting-action">
                            <button class="btn-edit">Save</button>
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
            <button class="close-btn" onclick="closeProfile()">Close</button>
        </div>
    </div>

    <script>
        // Make spans editable when clicked
        document.querySelectorAll('.setting-value').forEach(span => {
            span.addEventListener('click', () => {
                span.setAttribute('contenteditable', 'true');
                span.focus();
            });
        });

        // Handle Save button
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                const item = btn.closest('.setting-item');
                const spans = item.querySelectorAll('.setting-value');
                let data = {};

                spans.forEach(span => {
                    const field = span.dataset.field;
                    if (field) data[field] = span.innerText.trim();
                });

                fetch('update_settings.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                })
                    .then(res => res.json())
                    .then(resp => {
                        if (resp.success) {
                            alert('Updated successfully!');
                            spans.forEach(span => span.removeAttribute('contenteditable'));
                        } else {
                            alert('Update failed: ' + resp.message);
                        }
                    })
                    .catch(err => console.error(err));
            });
        });
    </script>
    <script src="script.js"></script>
</body>

</html>