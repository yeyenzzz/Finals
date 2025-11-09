<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online banking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@latest/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>
    <div class="whole">
        <div class="nav-section">
            <div class="Logo">LOGO</div>
            <a href="" class="active">
                <div class="Dashboard">
                    <i class="bi bi-house"></i> Dashboard
                </div>
            </a>
            <a href="">
                <div class="Account">
                    <i class="bi bi-person"></i> Account
                </div>
            </a>
            <a href="">
                <div class="Cards">
                    <i class="bi bi-credit-card"></i> Cards
                </div>
            </a>
            <a href="">
                <div class="Loan">
                    <i class="bi bi-cash"></i> Loan
                </div>
            </a>
            <a href="">
                <div class="Inbox">
                    <i class="bi bi-envelope"></i> Inbox
                </div>
            </a>
            <a href="">
                <div class="Settings">
                    <i class="bi bi-gear"></i> Settings
                </div>
            </a>
        </div>
        <div class="page">
            <div class="profile" id="profile">
                <a href="#" onclick="openModal3(event)"><i class="bi bi-box-arrow-right" title="Logout"
                        style="font-size: 25px; color;"></i></a>
                <a href="#"><i class="bi bi-person-circle" title="Profile" style="font-size: 25px; color;"></i></a>
                <a href="#"><i class="bi bi-bell-fill" title="Notification" style="font-size: 25px; color;"></i></a>
            </div>
            <div class="greet">
                <div>
                    <h1>Welcome back</h1>
                    <p>Monitor your balance, review transactions, and manage your finances effortlessly.</p>
                </div>
                <div class="cards">
                    <div class="card-design" id="card1"></div>
                    <div class="card-design" id="card2"></div>
                    <div class="card-design" id="card3"></div>
                </div>
            </div>
            <div class="section">
                <div class="balance">
                    <h3>Account Balance</h3>
                </div>
                <div class="transaction">
                    <h3>Transactions</h3>
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
            <button class="confirm-btn" onclick="confirmLogout()">Logout</button>
            <button class="close-btn" onclick="closeModal3()">Close</button>
        </div>
    </div>
    <script src="script.js"></script>
</body>

</html>