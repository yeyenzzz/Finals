<?php
session_start();
include 'db.php';
$conn = connectDB();

// Redirect if user is not logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $_SESSION = array();
    session_destroy();
    header("Location: index.php", true, 303);
    exit();
}

// Fetch user info
$userEmail = $_SESSION['email'];
$stmt = $conn->prepare("SELECT id, firstName, lastName, date_of_birth, address, balance, is_verified FROM users WHERE email = ?");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$stmt->bind_result($user_id, $firstName, $lastName, $date_of_birth, $address, $balance, $is_verified);
$stmt->fetch();
$stmt->close();
$userQuery = $conn->prepare("SELECT id, firstName, lastName, email, phone_number FROM users WHERE email = ?");
$userQuery->bind_param("s", $userEmail);
$userQuery->execute();
$userResult = $userQuery->get_result();
$userData = $userResult->fetch_assoc();

$loanQuery = $conn->prepare("
    SELECT id, status, loan_amount, loan_type, loan_term 
    FROM loanrequest 
    WHERE user_id = ? 
    ORDER BY id DESC LIMIT 1
");
$loanQuery->bind_param("i", $user_id);
$loanQuery->execute();
$loanResult = $loanQuery->get_result();
$loanData = $loanResult->fetch_assoc();

$loanStatus = $loanData['status'] ?? null;
$loanType = $loanData['loan_type'] ?? null;
$loanAmount = $loanData['loan_amount'] ?? null;
$loanTerm = $loanData['loan_term'] ?? null;


// Handle loan submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitLoan'])) {

    $user_id = $userData['id'];
    $monthly_salary = $_POST['monthly_salary'];
    $loan_type = $_POST['loan_type'];
    $loan_amount = $_POST['loan_amount'];
    $loan_term = $_POST['loan_term'];
    $payment_frequency = $_POST['payment_frequency'];
    $payment_type = $_POST['payment_type'];

    $uploadDir = "uploads/";

    // Ensure upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Handle Valid ID upload
    if (isset($_FILES['valid_id']) && $_FILES['valid_id']['error'] === UPLOAD_ERR_OK) {
        $valid_idName = uniqid() . "_" . basename($_FILES['valid_id']['name']);
        $validIDPath = $uploadDir . $valid_idName;
        move_uploaded_file($_FILES['valid_id']['tmp_name'], $validIDPath);
    } else {
        die("Error uploading Valid ID.");
    }

    // Handle Payslip upload
    if (isset($_FILES['payslip']) && $_FILES['payslip']['error'] === UPLOAD_ERR_OK) {
        $payslipName = uniqid() . "_" . basename($_FILES['payslip']['name']);
        $payslipPath = $uploadDir . $payslipName;
        move_uploaded_file($_FILES['payslip']['tmp_name'], $payslipPath);
    } else {
        die("Error uploading Payslip.");
    }

    // Insert into loanrequest table
    $stmt = $conn->prepare("
        INSERT INTO loanrequest
        (user_id, monthly_salary, valid_id, payslip, loan_type, loan_amount, loan_term, payment_frequency, payment_type)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "idsssisss",
        $user_id,
        $monthly_salary,
        $validIDPath,
        $payslipPath,
        $loan_type,
        $loan_amount,
        $loan_term,
        $payment_frequency,
        $payment_type
    );

    if ($stmt->execute()) {
        header("Location: loan4.php"); // redirect on success
        exit();
    } else {
        die("Database error: " . $stmt->error);
    }
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
                    <a href="loan4.php" class="active">
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
                <div class="content" id="loanContent">
                    <h1>Loan Application</h1>
                    <div class="applycard"><img src="images/save.png" alt="" class="credit-card "></div>
                    <p> Need extra funds? Apply for a loan today with simple </p>
                    <p>requirements and flexible payment options!</p>
                    <div class="inputs">
                        <div class="Personal" style="margin-top: 20px;">
                            <h3><i class="bi bi-check-circle-fill" style="color: #00226f;"></i> Quick approval
                                process
                            </h3>
                            <p>Get your application reviewed fast so you can access funds right when you need them.
                            </p>
                            <br>
                            <h3><i class="bi bi-check-circle-fill" style="color: #00226f;"></i> Flexible repayment
                                options</h3>
                            <p>Choose a payment plan that fits your budget and lifestyle.</p><br>
                            <h3><i class="bi bi-check-circle-fill" style="color: #00226f;"></i> Transparent terms
                            </h3>
                            <p>Know exactly what you’re signing up for — no hidden fees or surprises.</p>
                        </div>
                        <div class="Personal">
                        </div>
                        <div class="next_prev">
                            <div>
                                <?php
                                if (is_null($is_verified) || $is_verified == 0) {
                                    // User not verified or pending
                                    echo '<button class="next-btn" onclick="openProfile(event)">Verify account</button>';
                                } elseif ($is_verified == 1) {
                                    // User verified
                                    echo '<button type="button" class="next-btn" onclick="showLoanApplication()">Apply</button>';
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
            <button class="next-btn" onclick="">Confirm</button>
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
            // Pass PHP values to JS
            const userData = {
                fullName: "<?php echo $userData['firstName'] . ' ' . $userData['lastName']; ?>",
                email: "<?php echo $userData['email']; ?>",
                phoneNumber: "<?php echo $userData['phone_number']; ?>"
            };
            const USER_ID = "<?= $user_id ?>";
            const USER_NAME = "<?= $firstName . ' ' . $lastName ?>";
            const USER_DOB = "<?= $date_of_birth ?>";
            const USER_ADDRESS = "<?= $address ?>";
            const LOAN_STATUS = "<?= $loanStatus ?>";
            const LOAN_AMOUNT = "<?= $loanAmount ?>";
            const LOAN_TERM = "<?= $loanTerm ?>";
            const LOAN_TYPE = "<?= $loanType ?>"
        </script>

</body>

</html>