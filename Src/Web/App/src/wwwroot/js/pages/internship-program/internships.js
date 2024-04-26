import { $, $all } from "../../core/dom";
import { on } from "../../core/events";
import { Dialog } from "../../components/dialog";
import { Collapse } from "../../components/collapse";
import { FileUpload } from "../../components/file-upload";

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

function fetchJobDetails(jobId) {
    fetch(`/api/internships/${jobId}`, { method: "GET" })
        .then(response => response.json())
        .then(data => {
            jobTitle.innerHTML = data.title;
            jobDescription.innerHTML = data.description;
            jobDetailsSkeleton.classList.toggle("hidden");
            jobDetailsContent.classList.toggle("hidden");

            if ("applicationId" in data) {
                if (data["applicationId"] !== null) {
                    applyBtn.classList.add("hidden");
                    undoApplyBtn.classList.remove("hidden");
                    undoApplyBtn.setAttribute("data-application-id", data["applicationId"]);
                } else {
                    applyBtn.classList.remove("hidden");
                    undoApplyBtn.classList.add("hidden");
                    undoApplyBtn.removeAttribute("data-application-id");
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
    if (itemCard) {
        itemCard.classList.toggle("active");
        previouslySelectedItemCard = itemCard;

        isLoading = true;
        fetchJobDetails(itemCard.getAttribute("data-job-id"));
    }
});

//#endregion

//#region 
// This section handles the buttons in the details pane

const applicantsJobBtn = $("#applicants-job-btn");
const modifyJobBtn = $("#modify-job-btn");
const removeJobBtn = $("#remove-job-btn");

on(applicantsJobBtn, "click", function () {
    window.location.href = `${window.location.origin}/internship-program/applicants/applications?i=${previouslySelectedItemCard.getAttribute("data-job-id")}`;
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

on(undoApplyBtn, "click", function () {
    const applicationId = undoApplyBtn.getAttribute("data-application-id");
    fetch("/api/internships/" + previouslySelectedItemCard.getAttribute("data-job-id") + "/applications/" + applicationId, { method: "DELETE" })
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

const fileUpload = new FileUpload(
    $("#file-upload-container"),
    $("#file-upload-container input[type=file]"),
    $("#file-upload-container label")
);

const applicationDialog = new Dialog("#application-dialog");
applicationDialog.setTitle("Upload CV or Resume");

on(applyBtn, "click", function () {
    applicationDialog.open();
});

const fileUploadForm = $("#file-upload-form");
on(fileUploadForm, "submit", function (e) {
    e.preventDefault();
    fileUploadForm.action = `/internship-program/internships/${previouslySelectedItemCard.getAttribute("data-job-id")}/apply`;
    fileUploadForm.submit();
});

//#endregion

//#region 
// This section handles the filtering of the job list

// Filter by company
const filterByCompany = $("#filter-by-company");
if (filterByCompany) {
    const companyDialog = new Dialog("#company-popup");
    companyDialog.setTitle("Filter by Company");

    on(filterByCompany, "click", function () {
        companyDialog.open();
    });

    const companyMultiSelectApplyBtn = $("#company-popup-apply-btn");
    const companyMultiSelectResetBtn = $("#company-popup-reset-btn");

    const companyCheckboxes = $all("#company-popup input[type=checkbox]");

    on(companyMultiSelectResetBtn, "click", function () {
        companyCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
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
}

// Filter by visibility
const filterByVisibility = $("#filter-by-visibility");
if (filterByVisibility) {
    const visibilityDialog = new Dialog("#visibility-popup");
    visibilityDialog.setTitle("Filter by Visibility");

    on(filterByVisibility, "click", function () {
        visibilityDialog.open();
    });

    on($("#visibility-popup-apply-btn"), "click", () => {
        let visibility = $("#visibility-popup input[type=radio]:checked").value;
        if (visibility === "all") {
            params.delete("v");
        } else {
            params.set("v", visibility);
        }

        window.location.href = `${window.location.pathname}?${params.toString()}`;
    });
}

// Filter by approval
const filterByApproval = $("#filter-by-approval");
if (filterByApproval) {
    const approvalDialog = new Dialog("#approval-popup");
    approvalDialog.setTitle("Filter by Approval");

    on(filterByApproval, "click", function () {
        approvalDialog.open();
    });

    on($("#approval-popup-apply-btn"), "click", () => {
        let approval = $("#approval-popup input[type=radio]:checked").value;
        if (approval === "all") {
            params.delete("a");
        } else {
            params.set("a", approval);
        }

        window.location.href = `${window.location.pathname}?${params.toString()}`;
    });
}

// Default behavior on page load

let filterByCompanyParam = params.get("c");
if (filterByCompanyParam) {
    filterByCompanyParam.split(",").map(function (i) {
        const parsed = parseInt(i, 10);
        if (isNaN(parsed)) {
            return null;
        }
        return parsed;
    });
}

let filterByVisibilityParam = params.get("v");

let filterByApprovalParam = params.get("a");

on(document, "DOMContentLoaded", function () {
    if (filterByCompany && filterByCompanyParam) {
        const companyCheckboxes = $all("#company-popup input[type=checkbox]");
        filterByCompany.classList.add("selected");
        companyCheckboxes.forEach(checkbox => {
            if (filterByCompanyParam.includes(parseInt(checkbox.getAttribute("id")))) {
                checkbox.checked = true;
            }
        });
    }

    if (filterByVisibility && filterByVisibilityParam) {
        const visibilityRadioButtons = $all("#visibility-popup input[type=radio]");
        filterByVisibility.classList.add("selected");
        visibilityRadioButtons.forEach(radio => {
            if (radio.value === filterByVisibilityParam) {
                radio.checked = true;
            }
        });
    }

    if (filterByApproval && filterByApprovalParam) {
        const approvalRadioButtons = $all("#approval-popup input[type=radio]");
        filterByApproval.classList.add("selected");
        approvalRadioButtons.forEach(radio => {
            if (radio.value === filterByApprovalParam) {
                radio.checked = true;
            }
        });
    }
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

//#region Collapse

const collapseContainers = $all(".collapse-container");
const collapseInstances = [];

collapseContainers.forEach(container => {
    collapseInstances.push(new Collapse(container));
});

//#endregion