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

include 'db.php';
$conn = connectDB();

// Fetch user
$userEmail = $_SESSION['email'];

$stmt = $conn->prepare("SELECT id, firstName, lastName, date_of_birth, address, balance, is_verified FROM users WHERE email = ?");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$stmt->bind_result($user_id, $firstName, $lastName, $date_of_birth, $address, $balance, $is_verified);
$stmt->fetch();
$stmt->close();
$userQuery = $conn->prepare("
    SELECT id, firstName, lastName, email, phone_number, date_of_birth, address
    FROM users 
    WHERE email = ?
");
$userQuery->bind_param("s", $userEmail);
$userQuery->execute();
$userResult = $userQuery->get_result();
$userData = $userResult->fetch_assoc();

// Fetch latest credit card request (if any)
$statusQuery = $conn->prepare("
    SELECT status 
    FROM credit_cards 
    WHERE user_id = ?
    ORDER BY created_at DESC 
    LIMIT 1
");
$statusQuery->bind_param("i", $userData['id']);
$statusQuery->execute();
$statusResult = $statusQuery->get_result();
$cardStatus = $statusResult->fetch_assoc()['status'] ?? null;
$cardQuery = $conn->query("SELECT card_number, expiry_date FROM credit_cards WHERE status='Approved' ORDER BY id DESC LIMIT 1");
$cardData = $cardQuery->fetch_assoc();


// ------------------------------------------------------------
// CARD SUBMISSION HANDLER (POST → REDIRECT)
// ------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['confirm_card'])) {

    $full_name = $_POST['full_name'];
    $age = $_POST['age'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $salary = $_POST['salary'];

    // Uploads directory
    $uploadDir = "uploads/";

    $valid_id = time() . "_" . basename($_FILES['valid_id']['name']);
    $payslip = time() . "_" . basename($_FILES['payslip']['name']);

    move_uploaded_file($_FILES['valid_id']['tmp_name'], $uploadDir . $valid_id);
    move_uploaded_file($_FILES['payslip']['tmp_name'], $uploadDir . $payslip);

    // Insert record
    $stmt = $conn->prepare("
        INSERT INTO credit_cards (user_id, salary, valid_id, payslip)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->bind_param("idss", $userData['id'], $salary, $valid_id, $payslip);

    if ($stmt->execute()) {
        $_SESSION['flash'] = "Card application submitted successfully!";
    } else {
        $_SESSION['flash'] = "Failed to submit application.";
    }

    header("Location: card.php");
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
    <?php
    // FLASH MESSAGE AFTER REDIRECT
    if (!empty($_SESSION['flash'])) {
        echo "<script>alert('" . $_SESSION['flash'] . "');</script>";
        unset($_SESSION['flash']);
    }
    ?>

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
                            <div>
                                <?php
                                if (is_null($is_verified) || $is_verified == 0) {
                                    // User not verified or pending
                                    echo '<button class="next-btn" onclick="openProfile(event)">Verify account</button>';
                                } elseif ($is_verified == 1) {
                                    // User verified
                                    echo '<button type="button" class="next-btn" onclick="showApplicationForm()">Apply</button>';
                                }
                                ?>
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
            <button class="next-btn" onclick="document.getElementById('cardForm').submit();"
                name="confirm_card">Confirm</button>
            <button class="close-btn" onclick="closeCard()">Close</button>
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
            const dob = new Date("<?php echo $userData['date_of_birth']; ?>");
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const m = today.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                age--;
            }

            const userData = {
                fullName: "<?= $userData['firstName'] . ' ' . $userData['lastName'] ?>",
                email: "<?= $userData['email'] ?>",
                phoneNumber: "<?= $userData['phone_number'] ?>",
                address: "<?= addslashes($userData['address']) ?>",  // handle quotes
                age: age
            };

            const USER_ID = "<?= $user_id ?>";
            const USER_NAME = "<?= $firstName . ' ' . $lastName ?>";
            const USER_DOB = "<?= $date_of_birth ?>";
            const USER_ADDRESS = "<?= $address ?>";
            const CARD_STATUS = "<?= $cardStatus ?>";
            const CARD_NUMBER = "<?= $cardData['card_number'] ?? 'XXXX XXXX XXXX XXXX' ?>";
            const CARD_EXPIRY = "<?= $cardData['expiry_date'] ?? 'MM/YY' ?>";
        </script>

</body>

</html>