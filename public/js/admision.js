document.addEventListener("DOMContentLoaded", function () {
  const params = new URLSearchParams(window.location.search);

  if (params.get("exito") === "1") {
    const modal = new bootstrap.Modal(document.getElementById("successModal"));
    modal.show();
    window.history.replaceState({}, document.title, window.location.pathname);
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const modal = new bootstrap.Modal(document.getElementById("modalInicio"));
  modal.show();
});
