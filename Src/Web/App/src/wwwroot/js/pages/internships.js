import { $, $all } from "../core/dom";
import { on } from "../core/events";

const params = new URLSearchParams(window.location.search);

//#region
// This section handles the search bar

const searchBtn = $("#search-btn");
const searchQueryElement = $("#search-query");

on(searchBtn, "click", function () {
    const searchQuery = searchQueryElement.value;
    if (searchQuery) {
        params.set("q", searchQuery);
        window.location.href = `${window.location.pathname}?${params.toString()}`;
    } else {
        window.location.href = `${window.location.pathname}`;
    }
});

on(searchQueryElement, "keyup", function (event) {
    if (event.key === "Enter") {
        searchBtn.click();
    }
});

const query = params.get("q");

if (query) {
    searchQueryElement.value = query;
}

//#endregion

//#region
// This section handles the job list

const jobDetailsContent = $("#job-details-content");
const jobDetailsSkeleton = $("#job-details-skeleton");
const jobList = $("#job-list");
const jobTitle = $("#job-title");
const jobDescription = $("#job-description");

let previouslySelectedItemCard = null;
let isLoading = false;

const applyBtn = $("#btn-apply");
const undoApplyBtn = $("#btn-undo-apply");

function createApplyBtnIcon(btn,) {
    let icon = btn.querySelector("i");
    if (icon) {
        return;
    }

    applyBtn.classList.add("btn-icon");

    icon = document.createElement("i");
    icon.classList.add("i", "i-box-arrow-up-left", "pb-1");

    let btnText = applyBtn.firstChild;
    applyBtn.insertBefore(icon, btnText);
}

function removeApplyBtnIcon(btn) {
    let icon = btn.querySelector("i");
    if (icon) {
        icon.remove();
        btn.classList.remove("btn-icon");
        return;
    }
}

function fetchJobDetails(jobId) {
    fetch(`/api/internships/${jobId}`, { method: "GET" })
        .then(response => response.json())
        .then(data => {
            jobTitle.innerHTML = data.title;
            jobDescription.innerHTML = data.description;
            jobDetailsSkeleton.classList.toggle("hidden");
            jobDetailsContent.classList.toggle("hidden");

            if (applyBtn) {
                let applyExternal = data.applyOnExternalWebsite;
                applyBtn.dataset.applyExternal = data.applyOnExternalWebsite;
                if (applyExternal) {
                    applyBtn.dataset.externalUrl = data.externalWebsite;
                    createApplyBtnIcon(applyBtn);
                } else {
                    applyBtn.removeAttribute("data-external-url");
                    removeApplyBtnIcon(applyBtn);
                }
            }

            if ("hasApplied" in data) {
                if (data.hasApplied) {
                    applyBtn.classList.add("hidden");
                    undoApplyBtn.classList.remove("hidden");
                } else {
                    applyBtn.classList.remove("hidden");
                    undoApplyBtn.classList.add("hidden");
                }
            }
        })
        .catch(error => console.error("Error retrieving job:", error))
        .finally(() => { isLoading = false; });
}

on(jobList, "click", function (event) {
    if (isLoading) { return; }

    const itemCard = event.target.closest(".job");

    if (itemCard && previouslySelectedItemCard !== itemCard) {

        if (previouslySelectedItemCard) {
            previouslySelectedItemCard.classList.toggle("active");
        }

        itemCard.classList.toggle("active");
        previouslySelectedItemCard = itemCard;
        jobDetailsSkeleton.classList.toggle("hidden");
        jobDetailsContent.classList.toggle("hidden");

        isLoading = true;
        fetchJobDetails(itemCard.getAttribute("data-job-id"));
    }
});

on(document, "DOMContentLoaded", function () {
    const itemCard = document.querySelector(".job");
    itemCard.classList.toggle("active");
    previouslySelectedItemCard = itemCard;

    isLoading = true;
    fetchJobDetails(itemCard.getAttribute("data-job-id"));
});

//#endregion

//#region 
// This section handles the buttons in the details pane

const applicantsJobBtn = $("#applicants-job-btn");
const modifyJobBtn = $("#modify-job-btn");
const removeJobBtn = $("#remove-job-btn");

on(applicantsJobBtn, "click", function () {
    window.location.href = `http://localhost/internship-program/applicants/applications?i=${previouslySelectedItemCard.getAttribute("data-job-id")}`;
});

on(modifyJobBtn, "click", function () {
    window.location.href = `${window.location.pathname}/${previouslySelectedItemCard.getAttribute("data-job-id")}/modify`;
});

on(removeJobBtn, "click", function () {
    fetch("/internship-program/internships/" + previouslySelectedItemCard.getAttribute("data-job-id"), { method: "DELETE" })
        .then(() => {
            window.location.href = `${window.location.pathname}`;
        })
        .catch(error => console.error("Error deleting job:", error));
});

//#endregion

//#region
// This section handles the applying for a internship

on(applyBtn, "click", function () {
    if (applyBtn.dataset.applyExternal === "true") {
        window.open(applyBtn.dataset.externalUrl, "_blank", "noopener, noreferrer");
        return;
    }
    fetch("/api/internships/" + previouslySelectedItemCard.getAttribute("data-job-id") + "/apply", { method: "PUT" })
        .then(response => {
            if (response.status === 204) {
                applyBtn.classList.add("hidden");
                undoApplyBtn.classList.remove("hidden");
            } else {
                throw new Error("Error applying for job");
            }
        })
        .catch(error => console.error("Error applying for job:", error));
});

on(undoApplyBtn, "click", function () {
    fetch("/api/internships/" + previouslySelectedItemCard.getAttribute("data-job-id") + "/apply", { method: "DELETE" })
        .then(response => {
            if (response.status === 204) {
                applyBtn.classList.remove("hidden");
                undoApplyBtn.classList.add("hidden");
            } else {
                throw new Error("Error undoing application for job");
            }
        })
        .catch(error => console.error("Error undoing application for job:", error));
});

//#endregion

//#region 
// This section handles the filtering of the job list

const filterByCompany = $("#filter-by-company");
const companyMultiSelectList = $("#company-multi-select-list");

on(filterByCompany, "click", function () {
    companyMultiSelectList.classList.toggle("hidden");
});

const companyMultiSelectApplyBtn = $("#company-multi-select-list-apply-btn");
const companyMultiSelectResetBtn = $("#company-multi-select-list-reset-btn");
const companyMultiSelectHideBtn = $("#company-multi-select-list-hide-btn");

let companyCheckboxes = $all("#company-multi-select-list input[type=checkbox]");

on(companyMultiSelectResetBtn, "click", function () {
    companyCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
});

on(companyMultiSelectHideBtn, "click", function () {
    companyMultiSelectList.classList.toggle("hidden");
});

on(companyMultiSelectApplyBtn, "click", function () {
    let companies = [];
    companyCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            companies.push(checkbox.getAttribute("id"));
        }
    });

    if (companies.length > 0) {
        params.set("c", companies.join(","));
    } else {
        params.delete("c");
    }

    window.location.href = `${window.location.pathname}?${params.toString()}`;
});

//#endregion

//#region
// This section handles the pagination of the job list

let btnNextPage = $("#btn-next-page");
let btnPreviousPage = $("#btn-previous-page");

let currentPage = parseInt(params.get("p")) || 1;

on(btnNextPage, "click", function () {
    params.set("p", currentPage + 1);
    window.location.href = `${window.location.pathname}?${params.toString()}`;
});

on(btnPreviousPage, "click", function () {
    params.set("p", currentPage - 1);
    window.location.href = `${window.location.pathname}?${params.toString()}`;
});

//#endregion