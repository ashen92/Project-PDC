import { $ } from "../../core/dom";
import { on } from "../../core/events";

const navbarDropdown = $(".navbar-dropdown");
const navbarToggleShow = $("#navbar-toggle-show");
const navbarToggleHide = $("#navbar-toggle-hide");

on($(".navbar-toggler"), "click", function () {
    navbarDropdown.classList.toggle("visible");
    navbarToggleShow.classList.toggle("hidden");
    navbarToggleHide.classList.toggle("hidden");
});