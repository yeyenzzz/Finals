<?php
session_start();
include 'db.php';
$connectDB = connectDB();

// --- Redirect if not logged in ---
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$user_email = $_SESSION['email'];

// --- AJAX: Mark notification as read ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read_id'])) {

    $notif_id = intval($_POST['mark_read_id']);

    // Fetch user info
    $stmt = $connectDB->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $user_id = $user['id'];

    // Update notification
    $stmt = $connectDB->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $notif_id, $user_id);
    $stmt->execute();

    echo json_encode(['status' => 'success']);
    exit();
}

// --- Logout ---
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $_SESSION = [];
    session_destroy();
    header("Location: index.php", true, 303);
    exit();
}

// --- Fetch logged-in user info ---
$stmt = $connectDB->prepare("SELECT id, is_verified, firstName, lastName, date_of_birth, address, phone_number FROM users WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$user_id = $user['id'];
$is_verified = $user['is_verified'];
$firstName = $user['firstName'];
$lastName = $user['lastName'];
$date_of_birth = $user['date_of_birth'];
$address = $user['address'];
$phone_number = $user['phone_number'];

// --- Fetch notifications of type 'transfer' only ---
$stmt = $connectDB->prepare("SELECT * FROM notifications WHERE user_id = ? AND type = 'transfer' ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$notifications = $stmt->get_result();
$notificationsArray = [];
while ($row = $notifications->fetch_assoc()) {
    $notificationsArray[] = $row;
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
        <!-- Navigation Section -->
        <div class="profiles" id="profile">
            <div class="logo">
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

        <!-- Page Content -->
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
                    <a href="inbox.php" class="active">
                        <div class="Inbox"><i class="bi bi-envelope"></i> Inbox</div>
                    </a>
                    <a href="settings.php">
                        <div class="Settings"><i class="bi bi-gear"></i> Settings</div>
                    </a>
                </div>
            </div>

            <div class="transfer">
                <div class="content">
                    <h1>All Inbox</h1>
                    <div class="inbox">
                        <a href="inbox.php" style="margin-right: 50px; padding: 7px;">All</a>
                        <a href="updates.php" style="margin-right: 50px; padding: 7px;">Updates</a>
                        <a href="transaction.php" style="padding: 7px;" class="notif-active">Transactions</a>
                    </div>

                    <div class="inputsnotif" style="margin-top:20px;">
                        <?php if (!empty($notificationsArray)): ?>
                            <?php foreach ($notificationsArray as $row): ?>
                                <div class="notif-card transaction <?php echo $row['is_read'] ? 'read' : 'unread'; ?>"
                                    data-id="<?= $row['id'] ?>" data-title="<?= htmlspecialchars($row['title'], ENT_QUOTES) ?>"
                                    data-message="<?= htmlspecialchars($row['message'], ENT_QUOTES) ?>"
                                    data-sender="<?= htmlspecialchars($row['sender_name'], ENT_QUOTES) ?>"
                                    data-created="<?= htmlspecialchars($row['created_at'], ENT_QUOTES) ?>">
                                    <h3 class="title"><?= htmlspecialchars($row['title']) ?></h3>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="color:gray;">No transaction notifications available.</p>
                        <?php endif; ?>
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

    <!-- Notification Modal -->
    <div id="notifModal" class="modal-notif">
        <div class="modal-content">
            <h2 id="modalTitle"></h2>
            <p><strong>From:</strong> <span id="modalSender"></span></p>
            <div class="scrollable" id="modalMessage"></div>
            <small id="modalTime"></small>
            <button class="close-btn" onclick="closeNotifModal()">Close</button>
        </div>
    </div>

    <!-- Profile Modal -->
    <div id="profileModal" class="modal">
        <div class="modal-content" style="max-width: 490px;">
            <h2>Profile</h2>
            <div class="profile-section" style="display: flex; flex-direction: column; text-align: start;">
                Name
                <p class="items"><?= htmlspecialchars($firstName) ?> <?= htmlspecialchars($lastName) ?></p>
                Phone Number
                <p class="items"><?= htmlspecialchars($phone_number) ?></p>
                Date of Birth
                <p class="items"><?= htmlspecialchars($date_of_birth) ?></p>
                Current Address
                <p class="items"><?= htmlspecialchars($address) ?></p>
            </div>
            <div class="profile-btn">
                <?php
                if (is_null($is_verified)) {
                    echo '<button class="next-btn" onclick="showverifyID()">Verify account</button>';
                } elseif ($is_verified == 0) {
                    echo '<button class="next-btn" disabled>PENDING</button>';
                } else {
                    echo '<button class="next-btn" disabled>VERIFIED</button>';
                }
                ?>
                <button class="close-btn" onclick="closeProfile()">Close</button>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        // Reload on back-forward navigation
        window.addEventListener('pageshow', function (event) {
            if (event.persisted || (window.performance && window.performance.getEntriesByType('navigation')[0].type === 'back_forward')) {
                window.location.reload();
            }
        });

        const USER_ID = "<?= $user_id ?>";
        const USER_NAME = "<?= $firstName . ' ' . $lastName ?>";
        const USER_DOB = "<?= $date_of_birth ?>";
        const USER_ADDRESS = "<?= $address ?>";
    </script>

</body>

</html>