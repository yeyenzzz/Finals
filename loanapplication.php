<?php
session_start();
include 'db.php';
$conn = connectDB();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");


// Redirect to login if not logged in
if (!isset($_SESSION['id'])) {
    header("Location: adminlogin.php", true, 303);
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $_SESSION = array();
    session_destroy();
    header("Location: adminlogin.php", true, 303);
    exit();
}



// Fetch loan applications
$query = "
    SELECT lr.id, u.firstName, u.lastName, u.is_verified, lr.loan_type, lr.loan_amount, 
           lr.loan_term, lr.payment_frequency, lr.payment_type, lr.status, lr.valid_id, lr.payslip, lr.created_at
    FROM loanrequest lr
    JOIN users u ON lr.user_id = u.id  WHERE lr.status = 'Pending'
    ORDER BY lr.created_at DESC
";

$result = $conn->query($query);


if (isset($_POST['approve'])) {
    $loan_id = $_POST['loan_id'];

    // fetch loan details
    $loanQuery = $conn->prepare("SELECT user_id, loan_amount, loan_type, loan_term, payment_frequency 
                                 FROM loanrequest WHERE id = ?");
    $loanQuery->bind_param("i", $loan_id);
    $loanQuery->execute();
    $loanData = $loanQuery->get_result()->fetch_assoc();
    $loanQuery->close();

    $user_id = $loanData['user_id'];
    $loan_amount = $loanData['loan_amount'];
    $loan_type = $loanData['loan_type'];
    $loan_term = $loanData['loan_term'];
    $payment_frequency = $loanData['payment_frequency'];

    // Update loan status
    $updateStatus = $conn->prepare("UPDATE loanrequest SET status='Approved' WHERE id=?");
    $updateStatus->bind_param("i", $loan_id);
    $updateStatus->execute();
    $updateStatus->close();

    // Add loan amount to user balance
    $updateBalance = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id=?");
    $updateBalance->bind_param("di", $loan_amount, $user_id);
    $updateBalance->execute();
    $updateBalance->close();

    // Insert into activeloan table
    $insertLoan = $conn->prepare("
        INSERT INTO activeloan (user_id, loan_id, loan_type, loan_amount, loan_term, payment_frequency, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $insertLoan->bind_param("iisdis", $user_id, $loan_id, $loan_type, $loan_amount, $loan_term, $payment_frequency);
    $insertLoan->execute();
    $insertLoan->close();

    // Insert notification
    $notif_title = "Loan Approved";
    $notif_msg = "Your loan application for ₱" . number_format($loan_amount, 2) . " has been approved.";
    $notif_type = "updates";
    $notif_senderName = "eTapPay Admin";

    $stmt = $conn->prepare("
        INSERT INTO notifications (user_id, title, message, sender_name, type, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("issss", $user_id, $notif_title, $notif_msg, $notif_senderName, $notif_type);
    $stmt->execute();
    $stmt->close();

    header("Location: loanapplication.php");
    exit();
}

// REJECT LOAN
if (isset($_POST['reject'])) {
    $loan_id = $_POST['loan_id'];

    // fetch user_id before deleting
    $loanQuery = $conn->prepare("SELECT user_id FROM loanrequest WHERE id = ?");
    $loanQuery->bind_param("i", $loan_id);
    $loanQuery->execute();
    $loanData = $loanQuery->get_result()->fetch_assoc();
    $loanQuery->close();

    $user_id = $loanData['user_id'];

    $delete = $conn->prepare("DELETE FROM loanrequest WHERE id=?");
    $delete->bind_param("i", $loan_id);
    $delete->execute();
    $delete->close();

    $notif_title = "Loan Rejected";
    $notif_msg = "Your loan application has been rejected. Please contact support for more information.";
    $notif_type = "updates";
    $notif_senderName = "eTapPay Admin";

    $stmt = $conn->prepare("
        INSERT INTO notifications (user_id, title, message, sender_name, type, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("issss", $user_id, $notif_title, $notif_msg, $notif_senderName, $notif_type);
    $stmt->execute();
    $stmt->close();

    header("Location: loanapplication.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Loan Applications</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@latest/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="admin.css">
    <style>

    </style>
</head>

<body>
    <div class="whole">
        <!-- Navigation -->
        <div class="nav-section">
            <div class="logo">
                <img src="images/logo.png" alt="" class="logo">
                <h3>eTapPay</h3>
            </div>
            <a href="admin.php">
                <div class="Dashboard"><i class="bi bi-house"></i> Dashboard</div>
            </a>
            <a href="verification.php">
                <div class="Verify"><i class="bi bi-person-check"></i> Verify Users</div>
            </a>
            <a href="cardrequest.php">
                <div class="Card"><i class="bi bi-credit-card"></i> Card Requests</div>
            </a>
            <a href="loanapplication.php" class="active">
                <div class="Loan"><i class="bi bi-cash"></i> Loan Applications</div>
            </a>
            <a href="activeloan.php">
                <div class="activeloan"><i class="bi bi-coin"></i> Active Loans</div>
            </a>
        </div>

        <!-- Page Content -->
        <div class="page">
            <div class="profile" id="profile">
                <a href="#" onclick="openModal3(event)">
                    <i class="bi bi-box-arrow-right" title="Logout"></i>
                </a>
            </div>
            <div class="content-section">
                <h2>Loan Application Requests</h2>

                <!-- Search and Filter -->
                <div class="filter-bar">
                    <input type="text" placeholder="Search applicant..." class="search-box" onkeyup="filterTable()">
                    <select id="statusFilter" onchange="filterTable()">
                        <option value="">Status (All)</option>
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>

                <!-- Requests Table -->
                <table class="data-table" id="loanTable">
                    <thead>
                        <tr>
                            <th>Applicant Name</th>
                            <th>Verified</th>
                            <th>Loan Type</th>
                            <th>Amount</th>
                            <th>Duration</th>
                            <th>Date Applied</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) ?></td>
                                <td><?= $row['is_verified'] ? 'Yes' : 'No' ?></td>
                                <td><?= htmlspecialchars($row['loan_type']) ?></td>
                                <td>₱<?= number_format($row['loan_amount'], 2) ?></td>
                                <td><?= htmlspecialchars($row['loan_term']) ?> months</td>
                                <td><?= date("M d, Y", strtotime($row['created_at'])) ?></td>
                                <td>
                                    <span
                                        class="badge <?= strtolower($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span>
                                </td>
                                <td>
                                    <button class="view-btn" onclick="openModalView(<?= $row['id'] ?>)">View</button>

                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="loan_id" value="<?= $row['id'] ?>">
                                        <button type="submit" name="approve" class="approve-btn">Approve</button>
                                    </form>

                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="loan_id" value="<?= $row['id'] ?>">
                                        <button type="submit" name="reject" class="reject-btn">Reject</button>
                                    </form>
                                </td>

                                <!-- Hidden data for modal -->
                                <td style="display:none;" id="data-<?= $row['id'] ?>" data-validid="<?= $row['valid_id'] ?>"
                                    data-payslip="<?= $row['payslip'] ?>"
                                    data-paymentfrequency="<?= $row['payment_frequency'] ?>"
                                    data-paymenttype="<?= $row['payment_type'] ?>">
                                </td>

                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- VIEW LOAN APPLICATION MODAL -->
    <div id="viewmodal" class="modalLoanReq">
        <div class="modal-content-loan">
            <h2>Loan Application Documents</h2>

            <div class="modal-body">
                <h3>Valid ID</h3>
                <img id="viewValidID" src="" alt="Valid ID" class="modal-img">

                <h3>Payslip</h3>
                <img id="viewPayslip" src="" alt="Payslip" class="modal-img">
            </div>

            <button class="close-btn" onclick="closeViewModal()">Close</button>
        </div>
    </div>


    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <h2>Logout</h2>
            <div class="scrollable">
                <p>Logout your account?</p>
            </div>
            <button class="confirm-btn" onclick="confirmLogoutAdmin()">Logout</button>
            <button class="close-btn" onclick="closeModal3()">Close</button>
        </div>
    </div>
    <script>
        // Ensure page reload on back navigation
        window.addEventListener('pageshow', function (event) {
            if (event.persisted || (window.performance && window.performance.getEntriesByType('navigation')[0].type === 'back_forward')) {
                window.location.reload();
            }
        });
    </script>

    <script src="script.js"></script>

</body>

</html>