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

function confirmLogout() {
  window.location.href = "index.php";
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
