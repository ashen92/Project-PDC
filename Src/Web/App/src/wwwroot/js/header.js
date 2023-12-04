const navbarDropdown = document.querySelector(".navbar-dropdown");
const navbarToggleShow = document.getElementById("navbar-toggle-show");
const navbarToggleHide = document.getElementById("navbar-toggle-hide");

document.querySelector(".navbar-toggler").addEventListener("click", () => {
    navbarDropdown.classList.toggle("visible");
    navbarToggleShow.classList.toggle("hidden");
    navbarToggleHide.classList.toggle("hidden");
});