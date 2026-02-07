// Toogle Eye
document.querySelectorAll("[data-toggle]").forEach((btn) => {
  btn.addEventListener("click", () => {
    const inputId = btn.getAttribute("data-toggle");
    const input = document.getElementById(inputId);

    if (!input) return;

    input.type = input.type === "password" ? "text" : "password";
  });
});

// ==============================
// GLOBAL FUNCTIONS (WAJIB GLOBAL)
// ==============================
window.openLogoutModal = function (event) {
  event.preventDefault();

  const modal = document.getElementById("logoutModal");
  if (!modal) {
    console.warn("Logout modal not found");
    return;
  }

  modal.classList.remove("hidden");
  modal.classList.add("flex");
};

window.closeLogoutModal = function () {
  const modal = document.getElementById("logoutModal");
  if (!modal) return;

  modal.classList.add("hidden");
  modal.classList.remove("flex");
};

function openLogoutModal(event) {
  event.preventDefault();

  const modal = document.getElementById("logoutModal");
  if (!modal) return;

  modal.classList.remove("hidden");
  modal.classList.add("flex");
}

function closeLogoutModal() {
  const modal = document.getElementById("logoutModal");
  if (!modal) return;

  modal.classList.add("hidden");
  modal.classList.remove("flex");
}
