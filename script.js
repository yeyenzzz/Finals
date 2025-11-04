const modal = document.getElementById("forgotModal");
const modal2 = document.getElementById("TermsModal");
const body = document.body;

function openModal() {
  modal.style.display = "flex";
  body.classList.add("blurred");
}

function closeModal() {
  modal.style.display = "none";
  body.classList.remove("blurred");
}

function openModal2() {
  modal2.style.display = "flex";
  body.classList.add("blurred");
}

function closeModal2() {
  modal2.style.display = "none";
  body.classList.remove("blurred");
}
