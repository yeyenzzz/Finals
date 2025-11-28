<?php
session_start();
include 'db.php';
$connectDB = connectDB();

// Fetch only active users
$query = "
    SELECT u.id, u.firstName, u.lastName, u.phone_number, u.date_of_birth, u.email, 
           uv.id_image, u.is_verified, uv.created_at
    FROM users u
    JOIN usersvalidID uv ON u.id = uv.user_id
    WHERE u.is_verified = 1
    ORDER BY uv.created_at DESC
";
$result = $connectDB->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Banking - Active Users</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@latest/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="admin.css">
</head>

<body>
    <div class="whole">
        <!-- Sidebar Navigation -->
        <div class="nav-section">
            <div class="logo">
                <img src="images/logo.png" alt="Logo" class="logo">
                <h3>eTapPay</h3>
            </div>
            <a href="admin.php" class="active">
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
            <a href="activeloan.php">
                <div class="activeloan"><i class="bi bi-coin"></i> Active Loans</div>
            </a>
        </div>

        <!-- Main Page Content -->
        <div class="page">
            <div class="profile" id="profile">
                <a href="#" onclick="openModal3(event)">
                    <i class="bi bi-box-arrow-right" title="Logout" style="font-size: 25px;"></i>
                </a>
            </div>

            <div class="content-section">
                <h2>Active Users</h2>

                <!-- Search and Filter -->
                <div class="filter-bar">
                    <input type="text" placeholder="Search user or account..." class="search-box">
                    <select>
                        <option>Status (All)</option>
                        <option>Pending</option>
                        <option>Active</option>
                        <option>Deactivate</option>
                    </select>
                </div>

                <!-- Users Table -->
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Account No.</th>
                            <th>Contact No.</th>
                            <th>Email Address</th>
                            <th>Date Registered</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $fullName = htmlspecialchars($row['firstName'] . ' ' . $row['lastName']);
                                $accountNo = htmlspecialchars($row['id']);
                                $phone = htmlspecialchars($row['phone_number']);
                                $email = htmlspecialchars($row['email']);
                                $dateRegistered = date("M d, Y", strtotime($row['created_at']));
                                $status = $row['is_verified'] == 1 ? 'Active' : 'Pending';

                                echo "<tr>
                                        <td>{$fullName}</td>
                                        <td>{$accountNo}</td>
                                        <td>{$phone}</td>
                                        <td>{$email}</td>
                                        <td>{$dateRegistered}</td>
                                        <td>{$status}</td>
                                        <td>
                                            <button class='approve-btn'>Activate</button>
                                            <button class='reject-btn'>Deactivate</button>
                                        </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No active users found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
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
        </div>
    </div>

    <script src="script.js"></script>
</body>

</html>