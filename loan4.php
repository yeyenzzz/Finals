    <?php
    session_start();
    include 'db.php';
    $conn = connectDB();

    // Redirect if user is not logged in
    if (!isset($_SESSION['email'])) {
        header("Location: index.php");
        exit();
    }

    /* ============================
    INLINE MANUAL PAYMENT PROCESSOR
    ============================ */
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['makePayment'])) {
        header('Content-Type: application/json');

        $user_id = intval($_POST['user_id'] ?? 0);
        $amount = floatval($_POST['amount'] ?? 0);

        if ($user_id <= 0 || $amount <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
            exit();
        }

        // Fetch active loan for the user (most recent activeloan row)
        $loanStmt = $conn->prepare("
            SELECT id, user_id, loan_amount
            FROM activeloan
            WHERE user_id = ?
            ORDER BY id DESC
            LIMIT 1
        ");
        $loanStmt->bind_param("i", $user_id);
        $loanStmt->execute();
        $activeLoan = $loanStmt->get_result()->fetch_assoc();

        if (!$activeLoan) {
            echo json_encode(['status' => 'error', 'message' => 'No active loan found.']);
            exit();
        }

        $activeLoanId = intval($activeLoan['id']);
        $remainingLoan = floatval($activeLoan['loan_amount']);

        // Find corresponding loanrequest. Since activeloan has no loan_request_id, attempt best match:
        // 1) latest approved loanrequest for user
        // 2) latest loanrequest for user
        $lr = $conn->prepare("
            SELECT id, loan_amount, loan_term, payment_frequency
            FROM loanrequest
            WHERE user_id = ?
            AND status = 'Approved'
            ORDER BY id DESC
            LIMIT 1
        ");
        $lr->bind_param("i", $user_id);
        $lr->execute();
        $loanReq = $lr->get_result()->fetch_assoc();

        if (!$loanReq) {
            $lr2 = $conn->prepare("
                SELECT id, loan_amount, loan_term, payment_frequency
                FROM loanrequest
                WHERE user_id = ?
                ORDER BY id DESC
                LIMIT 1
            ");
            $lr2->bind_param("i", $user_id);
            $lr2->execute();
            $loanReq = $lr2->get_result()->fetch_assoc();
        }

        if (!$loanReq) {
            echo json_encode(['status' => 'error', 'message' => 'Loan request details not found.']);
            exit();
        }

        $loanRequestId = intval($loanReq['id']);
        $totalLoanAmount = floatval($loanReq['loan_amount']);
        $loanTerm = intval($loanReq['loan_term']);
        $frequency = $loanReq['payment_frequency'] ?? 'monthly';

        // Calculate total number of payments
        switch (strtolower($frequency)) {
            case 'monthly':
                $totalPayments = max(1, $loanTerm);
                break;
            case 'bi-weekly':
                $totalPayments = max(1, $loanTerm * 2);
                break;
            case 'quarterly':
                // loan_term likely in months; fallback to ceil(loanTerm/3)
                $totalPayments = max(1, (int) ceil($loanTerm / 3));
                break;
            default:
                $totalPayments = max(1, $loanTerm);
        }

        // Calculate per-payment amount (avoid division by zero)
        $paymentAmount = $totalPayments > 0 ? ($totalLoanAmount / $totalPayments) : $totalLoanAmount;

        // Fetch user balance
        $u = $conn->prepare("SELECT balance FROM users WHERE id = ?");
        $u->bind_param("i", $user_id);
        $u->execute();
        $userRow = $u->get_result()->fetch_assoc();

        if (!$userRow) {
            echo json_encode(['status' => 'error', 'message' => 'User not found.']);
            exit();
        }

        $currentBalance = floatval($userRow['balance']);

        if ($currentBalance < $amount) {
            echo json_encode(['status' => 'error', 'message' => 'Insufficient balance.']);
            exit();
        }

        // Start transaction
        $conn->begin_transaction();
        try {
            // Deduct user balance
            $newBalance = $currentBalance - $amount;
            $up1 = $conn->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $up1->bind_param("di", $newBalance, $user_id);
            $up1->execute();

            // Deduct loan amount
            $newLoanAmount = $remainingLoan - $amount;

            if ($newLoanAmount <= 0) {
                // Fully paid -> delete from activeloan and (optionally) mark loanrequest as paid/closed
                $delActive = $conn->prepare("DELETE FROM activeloan WHERE id = ?");
                $delActive->bind_param("i", $activeLoanId);
                $delActive->execute();

                // Optionally update loanrequest status
                $updateReq = $conn->prepare("DELETE FROM loanrequest WHERE id = ?");
                $updateReq->bind_param("i", $loanRequestId);
                $updateReq->execute();

                $conn->commit();
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Loan fully paid!',
                    'remaining' => 0,
                    'paymentsRemaining' => 0
                ]);
                exit();
            } else {
                // Update activeloan with remaining amount
                $upLoan = $conn->prepare("UPDATE activeloan SET loan_amount = ? WHERE id = ?");
                $upLoan->bind_param("di", $newLoanAmount, $activeLoanId);
                $upLoan->execute();

                // Compute remaining payments
                $paymentsRemaining = (int) ceil($newLoanAmount / $paymentAmount);

                $conn->commit();
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Payment successful!',
                    'remaining' => round($newLoanAmount, 2),
                    'paymentsRemaining' => $paymentsRemaining
                ]);
                exit();
            }

        } catch (Exception $e) {
            $conn->rollback();
            error_log("Payment error: " . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Payment failed. Try again.']);
            exit();
        }
    }

    // LOGOUT
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

    // Fetch last loan request (for UI details)
    $loanQuery = $conn->prepare("
        SELECT id, status, loan_amount, loan_type, loan_term, payment_type, payment_frequency
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
    $paymentType = $loanData['payment_type'] ?? null;
    $paymentFrequency = $loanData['payment_frequency'] ?? null;

    /* ======================================================
    ALWAYS SHOW REMAINING BALANCE + PAYMENTS REMAINING
    (Best-effort matching when activeloan lacks loan_request_id)
    ====================================================== */

    $remainingLoan = 0.00;
    $paymentsRemaining = 0;
    $totalLoanAmount = 0.00;
    $totalPayments = 0;

    // Get active loan (most recent)
    $loanCheck = $conn->prepare("
        SELECT id, user_id, loan_amount
        FROM activeloan
        WHERE user_id = ?
        ORDER BY id DESC
        LIMIT 1
    ");
    $loanCheck->bind_param("i", $user_id);
    $loanCheck->execute();
    $loanInfo = $loanCheck->get_result()->fetch_assoc();

    if ($loanInfo) {
        $remainingLoan = floatval($loanInfo['loan_amount']);

        // Try to find a matching loanrequest:
        // Prefer the latest approved loan request, else the latest request overall.
        $lr = $conn->prepare("
            SELECT id, loan_amount, loan_term, payment_frequency
            FROM loanrequest
            WHERE user_id = ?
            AND status = 'Approved'
            ORDER BY id DESC LIMIT 1
        ");
        $lr->bind_param("i", $user_id);
        $lr->execute();
        $lrInfo = $lr->get_result()->fetch_assoc();

        if (!$lrInfo) {
            $lr2 = $conn->prepare("
                SELECT id, loan_amount, loan_term, payment_frequency
                FROM loanrequest
                WHERE user_id = ?
                ORDER BY id DESC LIMIT 1
            ");
            $lr2->bind_param("i", $user_id);
            $lr2->execute();
            $lrInfo = $lr2->get_result()->fetch_assoc();
        }

        if ($lrInfo) {
            $totalLoanAmount = floatval($lrInfo['loan_amount']);
            $loanTerm = intval($lrInfo['loan_term']);
            $frequency = strtolower($lrInfo['payment_frequency'] ?? 'monthly');

            switch ($frequency) {
                case "monthly":
                    $totalPayments = max(1, $loanTerm);
                    break;
                case "bi-weekly":
                    $totalPayments = max(1, $loanTerm * 2);
                    break;
                case "quarterly":
                    $totalPayments = max(1, (int) ceil($loanTerm / 3));
                    break;
                default:
                    $totalPayments = max(1, $loanTerm);
            }

            $paymentAmount = $totalPayments > 0 ? ($totalLoanAmount / $totalPayments) : $totalLoanAmount;
            if ($paymentAmount > 0) {
                $paymentsRemaining = (int) ceil($remainingLoan / $paymentAmount);
            } else {
                $paymentsRemaining = 0;
            }
        }
    }

    // Helper: ensure numeric values for JS
    $remainingLoanJs = number_format((float) $remainingLoan, 2, '.', '');
    $paymentsRemainingJs = intval($paymentsRemaining);
    $totalLoanAmountJs = number_format((float) $totalLoanAmount, 2, '.', '');
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
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
                    echo '<button class="next-btn" onclick="showverifyID()">Verify Account</button>';
                } elseif ($is_verified == 0) {
                    echo '<button class="next-btn" disabled>PENDING</button>';
                } elseif ($is_verified == 1) {
                    echo '<button class="next-btn" disabled>VERIFIED</button>';
                }
                ?>
                <button class="close-btn" onclick="closeProfile()">Close</button>
            </div>
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
            // Pass PHP values to JS (always numeric where appropriate)
            const userData = {
                fullName: "<?= addslashes($userData['firstName'] . ' ' . $userData['lastName']) ?>",
                email: "<?= addslashes($userData['email'] ?? '') ?>",
                phoneNumber: "<?= addslashes($userData['phone_number'] ?? '') ?>"
            };
            const USER_ID = <?= intval($user_id) ?>;
            const USER_NAME = "<?= addslashes($firstName . ' ' . $lastName) ?>";
            const USER_DOB = "<?= addslashes($date_of_birth) ?>";
            const USER_ADDRESS = "<?= addslashes($address) ?>";
            const LOAN_STATUS = "<?= addslashes($loanStatus ?? '') ?>";
            const LOAN_AMOUNT = <?= $loanAmount ? floatval($loanAmount) : 'null' ?>;
            const LOAN_TERM = <?= $loanTerm ? intval($loanTerm) : 'null' ?>;
            const LOAN_TYPE = "<?= addslashes($loanType ?? '') ?>";
            const PAYMENT_TYPE = "<?= addslashes($paymentType ?? '') ?>";
            const PAYMENT_FREQUENCY = "<?= addslashes($paymentFrequency ?? '') ?>";

            // Values that always exist (computed above)
            const ACTIVE_LOAN_REMAINING = <?= $remainingLoanJs ?>;
            const ACTIVE_PAYMENTS_REMAINING = <?= $paymentsRemainingJs ?>;
            const ACTIVE_TOTAL_LOAN = <?= $totalLoanAmountJs ?>;

            // payNow function (keeps same behavior)
            

            // Build UI dynamically on page load
    
        </script>

    </body>

    </html>