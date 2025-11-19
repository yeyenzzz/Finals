<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Online Banking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@latest/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="admin.css" />
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
                <div class="Card">
                    <i class="bi bi-credit-card"></i> Card Requests
                </div>
            </a>
            <a href="loanapplication.php">
                <div class="Loan"><i class="bi bi-cash"></i> Loan Applications</div>
            </a>
        </div>


        <div class="page">
            <div class="profile" id="profile">
                <a href="#" onclick="openModal3(event)"><i class="bi bi-box-arrow-right" title="Logout"
                        style="font-size: 25px; color;"></i></a>
            </div>


            <div class="content-section">
                <h2>Verify Users</h2>

                <!-- Search and Filter -->
                <div class="filter-bar">
                    <input type="text" placeholder="Search user or account..." class="search-box">
                    <select>
                        <option>Status (All)</option>
                        <option>Pending</option>
                        <option>Approved</option>
                        <option>Rejected</option>
                    </select>
                </div>

                <!-- Requests Table -->

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
                        <tr>
                            <td>Juan Dela Cruz</td>
                            <td>0918663069</td>
                            <td>09/10/2005</td>
                            <td>juan@gmail.com</td>
                            <td>Nov 10, 2025</td>
                            <td><span class="badge pending">Pending</span></td>
                            <td>
                                <button class="view-btn" onclick="openModal4()">View</button>
                                <button class="approve-btn">Approve</button>
                                <button class="reject-btn">Reject</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>




        <div id="logoutModal" class="modal">
            <div class="modal-content">
                <h2>Logout</h2>
                <div class="scrollable">
                    <p>Logout your accout?</p>
                </div>
                <button class="confirm-btn" onclick="confirmLogout()">Logout</button>
                <button class="close-btn" onclick="closeModal3()">Close</button>
            </div>
        </div>


        <div id="viewmodal" class="modal">
            <div class="modal1-content">
                <h1>View</h1>
                <div id="payslip">Valid ID:</div>
                <button class="close-btn" onclick="closeModal4()">Close</button>
            </div>
        </div>


        <script src="script.js"></script>
</body>

</html>