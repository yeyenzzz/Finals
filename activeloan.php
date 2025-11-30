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

// Fetch active loans with user and loan request details
$query = "
    SELECT al.id AS activeLoanId, al.user_id, al.loan_id, al.loan_amount AS remaining_balance, al.created_at,
           u.firstName, u.lastName,
           lr.loan_type, lr.loan_amount AS requested_amount, lr.loan_term, lr.payment_type, lr.payment_frequency,
           lr.valid_id, lr.payslip
    FROM activeloan al
    JOIN users u ON al.user_id = u.id
    JOIN loanrequest lr ON al.loan_id = lr.id
    ORDER BY al.created_at DESC
";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Active Loans</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@latest/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="admin.css">
</head>

<body>
  <div class="whole">
    <!-- SIDEBAR -->
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
      <a href="loanapplication.php">
        <div class="Loan"><i class="bi bi-cash"></i> Loan Applications</div>
      </a>
      <a href="activeloan.php" class="active">
        <div class="activeloan"><i class="bi bi-coin"></i> Active Loans</div>
      </a>
    </div>

    <!-- MAIN PAGE -->
    <div class="page">
      <div class="profile" id="profile">
        <a href="#" onclick="openModal3(event)">
          <i class="bi bi-box-arrow-right" title="Logout"></i>
        </a>
      </div>

      <div class="content-section">
        <h2>Active Loans</h2>

        <table class="data-table">
          <thead>
            <tr>
              <th>User Name</th>
              <th>Account No.</th>
              <th>Remaining Balance</th>
              <th>Next Due Date</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()):
              // Determine the next due date based on payment frequency
              $createdAt = strtotime($row['created_at']);
              switch (strtolower($row['payment_frequency'])) {
                case 'bi-weekly':
                  $nextDue = date('M d, Y', strtotime('+14 days', $createdAt));
                  break;
                case 'quarterly':
                  $nextDue = date('M d, Y', strtotime('+90 days', $createdAt));
                  break;
                case 'monthly':
                default:
                  $nextDue = date('M d, Y', strtotime('+30 days', $createdAt));
                  break;
              }

              $today = date('Y-m-d');
              $isOverdue = strtotime($today) > strtotime($nextDue);
              $status = $isOverdue ? 'Overdue' : 'On-time';
              ?>
              <tr>
                <!-- Active loan info for table -->
                <td><?= htmlspecialchars($row['firstName'] . " " . $row['lastName']); ?></td>
                <td><?= htmlspecialchars($row['user_id']); ?></td>
                <td>₱<?= number_format($row['remaining_balance'], 2); ?></td>
                <td><?= $nextDue; ?></td>
                <td><?= $status; ?></td>
                <td>
                  <button class="view-btn" onclick="openModalView2(<?= $row['activeLoanId'] ?>)">View</button>
                </td>

                <!-- Hidden data for modal (loan request details) -->
                <td id="data-<?= $row['activeLoanId'] ?>" style="display:none;"
                  data-loantype="<?= htmlspecialchars($row['loan_type']); ?>"
                  data-loanamount="<?= htmlspecialchars(number_format($row['requested_amount'], 2)); ?>"
                  data-loanterm="<?= htmlspecialchars($row['loan_term']); ?>"
                  data-paymenttype="<?= htmlspecialchars($row['payment_type']); ?>"
                  data-paymentfrequency="<?= htmlspecialchars($row['payment_frequency']); ?>"
                  data-validid="<?= htmlspecialchars($row['valid_id']); ?>"
                  data-payslip="<?= htmlspecialchars($row['payslip']); ?>">
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- View Modal -->
  <div id="viewmodal" class="modalActive">
    <div class="modal1-content">
      <h2>Loan Request Details</h2>
      <p id="modalLoanType"></p>
      <p id="modalLoanAmount"></p>
      <p id="modalLoanTerm"></p>
      <p id="modalPayment"></p>

      <div style="margin-top:10px;">
        <h4>Valid ID:</h4>
        <img id="modalValidID" src="" alt="Valid ID" style="max-width:100%; border:1px solid #ccc; border-radius:5px;">
      </div>

      <div style="margin-top:10px;">
        <h4>Payslip:</h4>
        <img id="modalPayslip" src="" alt="Payslip" style="max-width:100%; border:1px solid #ccc; border-radius:5px;">
      </div>

      <button class="close-btn" onclick="closeModalView()">Close</button>
    </div>
  </div>
  <!-- Logout Modal -->
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