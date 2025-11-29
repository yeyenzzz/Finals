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
<p>Please complete all fields below to check your eligibility and apply for a card.</p>
<div class="inputs">
  <form id="cardForm" class="inputs" method="post" enctype="multipart/form-data">
    <div class="Personal">
      <h1>| Credit Card Application</h1>
    </div>
    <div class="Personal"> Full Name
  <input type="text" name="full_name" value="${userData.fullName}" required readonly>
  <span class="error-msg"></span>
</div>
<div class="Personal"> Age (21+)
  <input type="number" name="age" value="${userData.age}" required readonly>
  <span class="error-msg"></span>
</div>
<div class="Personal"> Email Address
  <input type="email" name="email" value="${userData.email}" required readonly>
  <span class="error-msg"></span>
</div>
<div class="Personal"> Phone Number
  <input type="text" name="phone_number" value="${userData.phoneNumber}" required readonly>
  <span class="error-msg"></span>
</div>
<div class="Personal"> Address
  <input type="text" name="address" value="${userData.address}" required>
  <span class="error-msg"></span>
</div>
<div class="Personal"> Monthly Salary (₱)
  <input type="number" name="salary" placeholder="Monthly Salary" required>
  <span class="error-msg"></span>
</div>
<div class="Personal"> Upload Valid ID
  <input type="file" name="valid_id" required>
  <span class="error-msg"></span>
</div>
<div class="Personal"> Upload PaySlip (3 Months)
  <input type="file" name="payslip" required>
  <span class="error-msg"></span>
</div>

    <input type="hidden" name="confirm_card" value="1">

   <div class="next_prev">
  <div><button type="button" class="prev-btn" onclick="location.reload()">Cancel</button></div>
  <div><button type="button" class="next-btn" onclick="validateCardForm()">Submit</button></div>
</div>

  </form>
</div>
  `;
}
function validateCardForm() {
  const form = document.getElementById("cardForm");
  if (!form) return;

  const inputs = form.querySelectorAll("input");
  let valid = true;

  // Clear previous errors
  form
    .querySelectorAll(".error-msg")
    .forEach((span) => (span.textContent = ""));

  inputs.forEach((input) => {
    const errorSpan = input.nextElementSibling;

    // Check empty fields
    if (
      (input.type !== "file" && input.value.trim() === "") ||
      (input.type === "file" && input.files.length === 0)
    ) {
      if (errorSpan) {
        errorSpan.textContent = "This field is required";
        errorSpan.style.color = "red";
      }
      valid = false;
    }

    // Age validation
    if (input.name === "age" && parseInt(input.value) < 21) {
      if (errorSpan) {
        errorSpan.textContent = "You must be at least 21 years old";
        errorSpan.style.color = "red";
      }
      valid = false;
    }
  });

  // Only show modal if all fields are valid
  if (!valid) return; // do NOT show modal if any field is invalid

  const modal = document.getElementById("cardModal");
  if (modal) modal.style.display = "flex"; // show modal centered
}

function showLoanApplication() {
  document.getElementById("loanContent").innerHTML = `
    <h1>Loan Application</h1>
    <p>Please complete all fields below to check your eligibility and apply for a loan.</p>

    <form id="loanForm" class="inputs" method="POST" enctype="multipart/form-data">
      <!-- Step 1 -->
      <div id="loanStep1" class="inputs">
        <h2>1. Personal Information</h2>
        <div class="Personal">Full Name
            <input type="text" name="fullName" value="${userData.fullName}" readonly>
        </div>
        <div class="Personal">Email
            <input type="email" name="email" value="${userData.email}" readonly>
        </div>
        <div class="Personal">Phone
            <input type="text" name="phone_number" value="${userData.phoneNumber}" readonly>
        </div>
        <div class="Personal">Monthly Salary (₱)
            <input type="number" name="monthly_salary" placeholder="Enter monthly salary" required>
        </div>
        <div class="Personal">Upload Valid ID
            <input type="file" name="valid_id" accept=".jpg,.jpeg,.png,.pdf" required>
        </div>
        <div class="Personal">Upload Payslip
            <input type="file" name="payslip" accept=".jpg,.jpeg,.png,.pdf" required>
        </div>
        <div class="next_prev">
            <button type="button" class="next-btn" onclick="showStep2()">Next</button>
        </div>
      </div>

      <!-- Step 2 -->
      <div id="loanStep2" class="inputs" style="display:none;">
        <h2>2. Loan Details</h2>
        <div class="Personal">Loan Type
            <select name="loan_type" required>
                <option value="" disabled selected>Select Loan Type</option>
                <option value="Business Loan">Business Loan</option>
                <option value="Personal Loan">Personal Loan</option>
                <option value="Educational Loan">Educational Loan</option>
            </select>
        </div>
        <div class="Personal">Loan Amount (₱)
            <input type="number" name="loan_amount" placeholder="Enter loan amount" required>
        </div>
        <div class="Personal">Loan Term (Months)
            <select name="loan_term" required>
                <option value="" disabled selected>Select Loan Term</option>
                <option value="12">12 months</option>
                <option value="24">24 months</option>
                <option value="36">36 months</option>
            </select>
        </div>
        <div class="Personal">Payment Frequency
            <select name="payment_frequency" required>
                <option value="" disabled selected>Select Payment Frequency</option>
                <option value="Monthly">Monthly</option>
                <option value="Bi-weekly">Bi-weekly</option>
                <option value="Quarterly">Quarterly</option>
            </select>
        </div>
        <div class="Personal">Payment Type
          <select name="payment_type" required>
              <option value="" disabled selected>Select Payment Type</option>
              <option value="Manual">Manual</option>
              <option value="Automatic">Automatic</option>
          </select>
        </div>
        <input type="hidden" name="submitLoan" value="1">
        <div class="next_prev">
          <button type="button" class="prev-btn" onclick="showStep1()">Previous</button>
          <button type="submit" class="next-btn" name="submitLoan">Submit</button>
        </div>
      </div>
    </form>
  `;
}

function showStep2() {
  document.getElementById("loanStep1").style.display = "none";
  document.getElementById("loanStep2").style.display = "block";
}

function showStep1() {
  document.getElementById("loanStep2").style.display = "none";
  document.getElementById("loanStep1").style.display = "block";
}
let originalProfile = profile.innerHTML;

function showverifyID() {
  document.getElementById("profileModal").innerHTML = `
    <div class="modal-content" style="max-width: 490px;">
        <h2>Verify your Account</h2>
        
           <form method="POST" action="saveValidID.php" enctype="multipart/form-data">
        <div class="profile-section" style="display: flex; flex-direction: column; text-align: start;">
          Name
             <input type="text" name="name" value="${USER_NAME}" readonly>

                ID Number
                <input type="text" name="id_number" required>

                Date of Birth
                <input type="text" name="dob" value="${USER_DOB}" readonly>

                Current Address
                <input type="text" name="address" value="${USER_ADDRESS}" readonly>

                ID (Upload)
                <input type="file" name="valid_id_image" required>
          
          <!-- Hidden FK -->
          <input type="hidden" name="user_id" value="${USER_ID}">
        </div>

        <div class="profile-btn">
          <button type="submit" class="next-btn">Submit</button>
          <button type="button" class="close-btn" onclick="closeVerifyID()">Close</button>
        </div>
      </form>
      <p>Please provide your valid ID to verify your identity.</p>
        <p>Note: Accepted IDs are Passport, Driver's License, or National ID and make sure that the details are the same with the your registration information (Go to Settings to Update).</p>
    </div>
    `;
}

function closeVerifyID() {
  profile.innerHTML = originalProfile;
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
document.addEventListener("DOMContentLoaded", () => {
  const notifModal = document.getElementById("notifModal");
  const modalTitle = document.getElementById("modalTitle");
  const modalMessage = document.getElementById("modalMessage");
  const modalTime = document.getElementById("modalTime");
  const modalSender = document.getElementById("modalSender"); // NEW

  document.querySelectorAll(".notif-card").forEach((card) => {
    card.addEventListener("click", () => {
      const notifId = card.dataset.id;

      modalTitle.textContent = card.dataset.title;
      modalMessage.textContent = card.dataset.message;
      modalTime.textContent = card.dataset.created;
      modalSender.textContent = card.dataset.sender || "Unknown"; // NEW

      notifModal.style.display = "flex";

      if (card.classList.contains("unread")) {
        card.classList.remove("unread");
        card.classList.add("read");

        fetch("transaction.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `mark_read_id=${encodeURIComponent(notifId)}`,
        });
      }
    });
  });
});

function closeNotifModal() {
  notifModal.style.display = "none";
}

function openModalView(loanId) {
  const dataRow = document.getElementById("data-" + loanId);

  const validID = dataRow.getAttribute("data-validid");
  const payslip = dataRow.getAttribute("data-payslip");

  // Set images
  document.getElementById("modalValidID").src = validID;
  document.getElementById("modalPayslip").src = payslip;

  // Show modal
  document.getElementById("viewmodal").style.display = "flex";
}

function closeModalView() {
  document.getElementById("viewmodal").style.display = "none";
}

function openModalView(activeLoanId) {
  const dataRow = document.getElementById("data-" + activeLoanId);

  document.getElementById("modalLoanType").innerText =
    "Loan Type: " + dataRow.getAttribute("data-loantype");
  document.getElementById("modalLoanAmount").innerText =
    "Loan Amount: ₱" + dataRow.getAttribute("data-loanamount");
  document.getElementById("modalLoanTerm").innerText =
    "Loan Term: " + dataRow.getAttribute("data-loanterm") + " months";
  document.getElementById("modalPayment").innerText =
    "Payment: " +
    dataRow.getAttribute("data-paymenttype") +
    ", " +
    dataRow.getAttribute("data-paymentfrequency");

  document.getElementById("modalValidID").src =
    dataRow.getAttribute("data-validid");
  document.getElementById("modalPayslip").src =
    dataRow.getAttribute("data-payslip");

  document.getElementById("viewmodal").style.display = "flex";
}

function closeModalView() {
  document.getElementById("viewmodal").style.display = "none";
}
