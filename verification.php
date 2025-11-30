<?php
session_start();
include 'db.php';
$connectDB = connectDB();

// Handle Approve / Reject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_id'])) {
        $userId = intval($_POST['approve_id']);
        $connectDB->query("UPDATE users SET is_verified = 1 WHERE id = $userId");
        header("Location: " . $_SERVER['PHP_SELF']);


        $notif_title = "Verification Approved";
        $notif_msg = "Your ID has been approved. You are now a verified user.";
        $notif_senderName = "eTapPay Admin";
        $notif_type = "updates";
        $stmt = $connectDB->prepare("
                INSERT INTO notifications (user_id, title, message, sender_name, type, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
        $stmt->bind_param("issss", $userId, $notif_title, $notif_msg, $notif_senderName, $notif_type);
        $stmt->execute();

        exit;

    } elseif (isset($_POST['reject_id'])) {
        $userId = intval($_POST['reject_id']);
        $connectDB->query("DELETE FROM usersvalidID WHERE user_id = $userId");
        $connectDB->query("UPDATE users SET is_verified = NULL WHERE id = $userId");
        header("Location: " . $_SERVER['PHP_SELF']);

        $notif_title = "Verification Rejected";
        $notif_msg = "Your ID has been rejected. Please upload a valid ID to get verified or make sure the details are matched.";
        $notif_senderName = "eTapPay Admin";
        $notif_type = "updates";
        $stmt = $connectDB->prepare("
                INSERT INTO notifications (user_id, title, message, sender_name, type,created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
        $stmt->bind_param("isss", $userId, $notif_title, $notif_msg, $notif_senderName, $notif_type);
        $stmt->execute();
        exit;
    }
}

// STATUS FILTER
$statusFilter = "";
if (isset($_GET['status'])) {
    if ($_GET['status'] === "0") {
        $statusFilter = "WHERE u.is_verified = 0";
    } elseif ($_GET['status'] === "1") {
        $statusFilter = "WHERE u.is_verified = 1";
    } elseif ($_GET['status'] === "rejected") {
        $statusFilter = "WHERE u.is_verified IS NULL";
    }
}

// SEARCH FILTER
$search = "";
$searchFilter = "";

if (isset($_GET['search']) && $_GET['search'] !== "") {
    $search = $connectDB->real_escape_string($_GET['search']);
    $searchFilter = " AND (
        u.firstName LIKE '%$search%' OR
        u.lastName LIKE '%$search%' OR
        CONCAT(u.firstName, ' ', u.lastName) LIKE '%$search%' OR
        u.email LIKE '%$search%' OR
        u.phone_number LIKE '%$search%'
    )";
}

// MAIN QUERY
$query = "
    SELECT u.id, u.firstName, u.lastName, u.phone_number, u.date_of_birth,
           u.email, uv.id_image, u.is_verified, uv.uploaded_at
    FROM users u
    INNER JOIN usersvalidID uv ON u.id = uv.user_id
    $statusFilter
    $searchFilter
    ORDER BY uv.uploaded_at DESC
";


$result = $connectDB->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Verify Users</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@latest/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="admin.css">
</head>

<body>
    <div class="whole">
        <div class="nav-section">
            <div class="logo">
                <img src="images/logo.png" alt="" class="logo">
                <h3>eTapPay</h3>
            </div>
            <a href="admin.php">
                <div class="Dashboard"><i class="bi bi-house"></i> Dashboard</div>
            </a>
            <a href="verification.php" class="active">
                <div class="Verify"><i class="bi bi-person-check"></i> Verify Users</div>
            </a>
            <a href="cardrequest.php">
                <div class="Card"><i class="bi bi-credit-card"></i> Card Requests</div>
            </a>
            <a href="loanapplication.php">
                <div class="Loan"><i class="bi bi-cash"></i> Loan Applications</div>
            </a>
            <a href="activeloan.php">
                <div class="activeloan"><i class="bi bi-coin"></i> Active Loans</div>
            </a>
        </div>

        <div class="page">
            <div class="profile" id="profile">
                <a href="#" onclick="openModal3(event)"><i class="bi bi-box-arrow-right" title="Logout"
                        style="font-size: 25px;"></i></a>
            </div>

            <div class="content-section">
                <h2>Verify Users</h2>

                <!-- SEARCH + FILTER BAR -->
                <form method="GET" class="filter-bar">
                    <input type="text" name="search" placeholder="Search user or account..." class="search-box"
                        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">

                    <select name="status" onchange="this.form.submit()">
                        <option value="">Status (All)</option>
                        <option value="0" <?= (isset($_GET['status']) && $_GET['status'] == "0") ? "selected" : "" ?>>
                            Pending</option>
                        <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == "1") ? "selected" : "" ?>>
                            Approved</option>
                    </select>

                    <button type="submit" style="display:none;"></button>
                </form>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Contact No.</th>
                            <th>Birthdate</th>
                            <th>Email Address</th>
                            <th>Date Requested</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) ?></td>
                                <td><?= htmlspecialchars($row['phone_number']) ?></td>
                                <td><?= htmlspecialchars($row['date_of_birth']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= date("M d, Y", strtotime($row['uploaded_at'])) ?></td>

                                <td>
                                    <?php
                                    if ($row['is_verified'] == 1) {
                                        echo '<span class="badge approved">Approved</span>';
                                    } else {
                                        echo '<span class="badge pending">Pending</span>';
                                    }
                                    ?>
                                </td>

                                <td>
                                    <button class="view-btn"
                                        onclick="openModalWithID('<?= htmlspecialchars($row['id_image'], ENT_QUOTES) ?>')">
                                        View ID
                                    </button>

                                    <?php if ($row['is_verified'] == 0): ?>
                                        <form method="post" style="display:inline">
                                            <input type="hidden" name="approve_id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="approve-btn">Approve</button>
                                        </form>

                                        <form method="post" style="display:inline">
                                            <input type="hidden" name="reject_id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="reject-btn">Reject</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
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
            <button class="confirm-btn" onclick="confirmLogout()">Logout</button>
            <button class="close-btn" onclick="closeModal3()">Close</button>
        </div>
    </div>

    <!-- View ID Modal -->
    <div id="viewmodal" class="modal">
        <div class="modal1-content">
            <h1>View ID</h1>
            <div id="idPreview"></div>
            <button class="close-btn" onclick="closeModal4()">Close</button>
        </div>
    </div>

    <script>
        function openModalWithID(imageName) {
            const modal = document.getElementById('viewmodal');
            modal.style.display = 'block';
            document.getElementById('idPreview').innerHTML =
                `<img src="uploads/valid_ids/${imageName}" style="max-width:100%;">`;
        }
        function closeModal4() {
            document.getElementById('viewmodal').style.display = 'none';
        }
    </script>

    <script src="script.js"></script>
</body>

</html>