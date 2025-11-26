const modal = document.getElementById("forgotModal");

function openModal() {
  modal.style.display = "flex";
}

function closeModal() {
  modal.style.display = "none";
}

const modal2 = document.getElementById("TermsModal");

function openModal2() {
  modal2.style.display = "flex";
}

function closeModal2() {
  modal2.style.display = "none";
}

const modal3 = document.getElementById("logoutModal");

function openModal3(event) {
  event.preventDefault();
  modal3.style.display = "flex";
}

function closeModal3() {
  modal3.style.display = "none";
}
const modal4 = document.getElementById("viewmodal");

function openModal4() {
  modal4.style.display = "flex";
}
function closeModal4() {
  modal4.style.display = "none";
}

const profile = document.getElementById("profileModal");

function openProfile(event) {
  event.preventDefault();
  profile.style.display = "flex";
}

function closeProfile() {
  profile.style.display = "none";
}

function confirmLogout() {
  window.location.href = "index.php";
}

const cashin = document.getElementById("cashinModal");

function openCashin() {
  cashin.style.display = "flex";
}

function closeCashin() {
  cashin.style.display = "none";
}

const card = document.getElementById("cardModal");

function openCard() {
  card.style.display = "flex";
}

function closeCard() {
  card.style.display = "none";
}

const verify = document.getElementById("verifyModal");

function openVerify() {
  verify.style.display = "flex";
}

function closeVerify() {
  verify.style.display = "none";
}

const verifyID = document.getElementById("verifyIDModal");

function showApplicationForm() {
  document.getElementById("cardContent").innerHTML = `
        <h1>Credit Card</h1>
        <p>Please complete all fields below to check your eligibility and apply for a loan.</p>
        <div class="inputs">
        <form id="cardForm" method="post" enctype="multipart/form-data" style="width: 100%;">
         <input type="hidden" name="confirm_card" value="1">
        
            <div class="Personal">
                <h1>| Credit Card Application</h1>
            </div>
            <div class="Personal"> Full Name
                <input type="text" name="full_name" value="${userData.fullName}" required>
            </div>
            <div class="Personal"> Age (21+)
                <input type="number" name="age" placeholder="Age" required>
            </div>

            <div class="Personal"> Email Address
                <input type="email" name="email"  value="${userData.email}"  required>
            </div>
            <div class="Personal"> Phone Number
                <input type="text" name="phone_number"  value="${userData.phoneNumber}" required>
            </div>
            <div class="Personal"> Address
                <input type="text" name="address" placeholder="Address" required>
            </div>
            <div class="Personal"> Monthly Salary (₱)
                <input type="number" name="salary" placeholder="Monthly Salary" required>
            </div>
            <div class="Personal"> Upload Valid ID 
                <input type="file" name="valid_id" required>
            </div>
            <div class="Personal"> Upload PaySlip (3 Months) 
                <input type="file" name="payslip" required>
            </div>

            <div class="next_prev">
                <div><button type="button" class="prev-btn" onclick="location.reload()">Cancel</button></div>
                <div><button type="button" class="next-btn" onclick="openCard()">Next</button></div>
            </div>
        </form>
        </div>
        
    `;
}

function showLoanApplication() {
  document.getElementById("loanContent").innerHTML = `
    <h1>Loan Application</h1>
    <p>Please complete all fields below to check your eligibility and apply for a loan.</p>
    <div class="inputs">
        <div class="Personal">
            <h1>| 1. Personal Information</h1>
        </div>
        <div class="Personal"> Full Name<input type="text" placeholder="Full Name" required></div>
        <div class="Personal"> Email Address<input type="email" placeholder="Email Address" required>
        </div>
        <div class="Personal"> Contact Number<input type="text" placeholder="Contact Number" required>
        </div>
        <div class="Personal"> Monthly Salary (₱)<input type="number" placeholder="Contact Number"
                required>
        </div>
        <div class="Personal">Upload Valid ID <input type="file" placeholder="Upload Valid ID" required>
        </div>
        <div class="Personal">Upload PaySlip (3 Months) <input type="file" placeholder="Upload Valid ID"
                required></div>

        <div class="next_prev">
            <div><button class="prev-btn" onclick="location.reload()">Cancel</button></div>
            <div><button class="next-btn" onclick="show2Loan()">Next</button></div>
        </div>
    </div>
    `;
}

function showverifyID() {
  document.getElementById("profileMode").innerHTML = `
      <div class="modal-content" style="max-width: 490px;">
        <h2>Profile</h2>
        <div class="profile-section" style="display: flex; flex-direction: column; text-align: start;">
            Name
            <input type="text" placeholder="">
            ID Number
            <input type="text" name="" id="">
            Date of Birth
            <input type="date" name="" id="">
            Current Address
            <input type="text" name="" id="">
            ID
            <input type="file" name="" id="">
        </div>
        <div class="profile-btn">
            <button class="next-btn">Submit</button>
            <button class="close-btn" onclick="closeVerifyID()">Close</button>
        </div>
    </div>
    `;
}

function show2Loan() {
  document.getElementById("loanContent").innerHTML = `
    <h1>Loan Application</h1>
    <p>Please complete all fields below to check your eligibility and apply for a loan.</p>
    <div class="inputs">
        <div class="Personal">
            <h1>| 2. Loan Details</h1>
        </div>
        <div class="Personal"> Loan Type <select>
                <option value="" disabled selected class="disabled">Select Loan Type</option>
                <option value="Business Loan">Business Loan</option>
                <option value="Personal Loan">Personal Loan</option>
                <option value="Educational Loan">Educational Loan</option>
            </select></div>
        <div class="Personal"> Desired Loan Amount (₱)<input type="number"
                placeholder="Desired Loan Amount" required></div>
        <div class="Personal"> Loan Term (Months)<select>
                <option value="" disabled selected class="disabled">Select Loan Term</option>
                <option value="12">12 months</option>
                <option value="24">24 months</option>
                <option value="36">36 months</option>
            </select></div>
        <div class="Personal"> Payment Frequency<select>
                <option value="" disabled selected class="disabled">Select Payment Frequency</option>
                <option value="Monthly">Monthly</option>
                <option value="Bi-weekly">Bi-weekly</option>
                <option value="Quarterly">Quarterly</option>
            </select></div>
        <div class="Personal"> Payment Type<select>
                <option value="" disabled selected class="disabled">Select Payment Type</option>
                <option value="Manual">Manual Payment</option>
                <option value="Automatic">Automatic Payment (Auto Debit/Auto Pay)</option>
            </select></div>
        <div class="next_prev">
            <div><button class="prev-btn" onclick="showLoanApplication()">Previous</button></div>
            <div><button class="next-btn" onclick="openCard()">Next</button></div>
        </div>
    </div>
  `;
}

document.addEventListener("DOMContentLoaded", function () {
  // Elements
  const reviewButton = document.getElementById("reviewButton");
  const confirmButton = document.getElementById("confirmButton");
  const closeReviewModal = document.getElementById("closeReviewModal");
  const reviewModal = document.getElementById("reviewModal");
  const hiddenSubmit = document.getElementById("hiddenSubmit");

  // Input fields
  const recipientInput = document.getElementById("recipient_email");
  const amountInput = document.getElementById("amount");
  const messageInput = document.getElementById("message");

  // Review modal display fields
  const reviewEmail = document.getElementById("review_email");
  const reviewAmount = document.getElementById("review_amount");
  const reviewMessage = document.getElementById("review_message");

  // --- Open Review Modal ---
  if (reviewButton) {
    reviewButton.addEventListener("click", function () {
      const email = recipientInput.value.trim();
      const amount = parseFloat(amountInput.value.trim());
      const message = messageInput.value.trim();

      if (!email || isNaN(amount) || amount <= 0) {
        alert("Please enter a valid recipient email and amount.");
        return;
      }

      // Fill modal info
      reviewEmail.value = email;
      reviewAmount.value = `₱${amount.toFixed(2)}`;
      reviewMessage.value = message || "(No message)";

      // Show modal
      reviewModal.style.display = "flex";
    });
  }

  // --- Close Review Modal ---
  if (closeReviewModal) {
    closeReviewModal.addEventListener("click", function () {
      reviewModal.style.display = "none";
    });
  }

  // --- Confirm Transfer (Submit Form) ---
  if (confirmButton) {
    confirmButton.addEventListener("click", function () {
      reviewModal.style.display = "none";
      hiddenSubmit.click();
    });
  }
});

// Transaction filter buttons
document.querySelectorAll(".filter-btn").forEach((btn) => {
  btn.addEventListener("click", () => {
    // Remove active class from all buttons
    document
      .querySelectorAll(".filter-btn")
      .forEach((b) => b.classList.remove("active"));
    btn.classList.add("active");

    const target = btn.getAttribute("data-target");
    // Hide all sections
    document
      .querySelectorAll(".transactions-section")
      .forEach((section) => (section.style.display = "none"));
    // Show the selected section
    document.getElementById(target).style.display = "block";
  });
});

// --- NOTIFICATION MODAL ---
const notifModal = document.getElementById("notifModal");
const modalTitle = document.getElementById("modalTitle");
const modalMessage = document.getElementById("modalMessage");
const modalTime = document.getElementById("modalTime");

document.querySelectorAll(".notif-card").forEach((card) => {
  card.addEventListener("click", () => {
    const notifId = card.dataset.id;

    modalTitle.textContent = card.dataset.title;
    modalMessage.textContent = card.dataset.message;
    modalTime.textContent = card.dataset.created;
    notifModal.style.display = "flex";

    if (card.classList.contains("unread")) {
      card.classList.remove("unread");
      card.classList.add("read");

      // Send AJAX POST to the same page
      fetch("transaction.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `mark_read_id=${notifId}`,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.status !== "success") {
            console.error("Failed to mark as read");
          }
        })
        .catch((err) => console.error(err));
    }
  });
});

function closeNotifModal() {
  notifModal.style.display = "none";
}
