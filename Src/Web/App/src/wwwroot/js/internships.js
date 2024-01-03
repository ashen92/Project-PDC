
const params = new URLSearchParams(window.location.search);

// --------------------------------------------------------------------------------------------
// This section handles the search bar

const searchBtn = document.getElementById("search-btn");
const searchQueryElement = document.getElementById("search-query");

searchBtn.addEventListener("click", () => {
    const searchQuery = searchQueryElement.value;
    if (searchQuery) {
        params.set("q", searchQuery);
        window.location.href = `${window.location.pathname}?${params.toString()}`;
    } else {
        window.location.href = `${window.location.pathname}`;
    }
});

searchQueryElement.addEventListener("keyup", (event) => {
    if (event.key === "Enter") {
        searchBtn.click();
    }
});

const query = params.get("q");

if (query) {
    searchQueryElement.value = query;
}

// --------------------------------------------------------------------------------------------
// This section handles the job list

const jobDetailsContent = document.getElementById("job-details-content");
const jobDetailsSkeleton = document.getElementById("job-details-skeleton");
const jobList = document.getElementById("job-list");
const jobTitle = document.getElementById("job-title");
const jobDescription = document.getElementById("job-description");

let previouslySelectedItemCard = null;
let isLoading = false;

const applyBtn = document.getElementById("btn-apply");
const undoApplyBtn = document.getElementById("btn-undo-apply");

function fetchJobDetails(jobId) {
    fetch(`/api/internships/${jobId}`, { method: "GET" })
        .then(response => response.json())
        .then(data => {
            jobTitle.innerHTML = data.title;
            jobDescription.innerHTML = data.description;
            jobDetailsSkeleton.classList.toggle("hidden");
            jobDetailsContent.classList.toggle("hidden");

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

jobList.addEventListener("click", (event) => {
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

document.addEventListener("DOMContentLoaded", () => {
    const itemCard = document.querySelector(".job");
    itemCard.classList.toggle("active");
    previouslySelectedItemCard = itemCard;

    isLoading = true;
    fetchJobDetails(itemCard.getAttribute("data-job-id"));
});

// --------------------------------------------------------------------------------------------
// This section handles the buttons in the details pane

const applicantsJobBtn = document.getElementById("applicants-job-btn");
const modifyJobBtn = document.getElementById("modify-job-btn");
const removeJobBtn = document.getElementById("remove-job-btn");

if (applicantsJobBtn) {
    applicantsJobBtn.addEventListener("click", () => {
        console.log(previouslySelectedItemCard.getAttribute("data-job-id"));
        window.location.href = `${window.location.pathname}/${previouslySelectedItemCard.getAttribute("data-job-id")}/applicants`;
    });
}

if (modifyJobBtn) {
    modifyJobBtn.addEventListener("click", () => {
        window.location.href = `${window.location.pathname}/${previouslySelectedItemCard.getAttribute("data-job-id")}/modify`;
    });
}

if (removeJobBtn) {
    removeJobBtn.addEventListener("click", () => {
        fetch("/internship-program/internships/" + previouslySelectedItemCard.getAttribute("data-job-id"), { method: "DELETE" })
            .then(() => {
                window.location.href = `${window.location.pathname}`;
            })
            .catch(error => console.error("Error deleting job:", error));
    });
}

// --------------------------------------------------------------------------------------------
// This section handles the applying for a internship

if (applyBtn) {
    applyBtn.addEventListener("click", () => {
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
}

if (undoApplyBtn) {
    undoApplyBtn.addEventListener("click", () => {
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
}

// --------------------------------------------------------------------------------------------
// This section handles the filtering of the job list

const filterByCompany = document.getElementById("filter-by-company");
const companyMultiSelectList = document.getElementById("company-multi-select-list");

if (filterByCompany) {
    filterByCompany.addEventListener("click", () => {
        companyMultiSelectList.classList.toggle("hidden");
    });
}

const companyMultiSelectApplyBtn = document.getElementById("company-multi-select-list-apply-btn");
const companyMultiSelectResetBtn = document.getElementById("company-multi-select-list-reset-btn");
const companyMultiSelectHideBtn = document.getElementById("company-multi-select-list-hide-btn");

let companyCheckboxes = document.querySelectorAll("#company-multi-select-list input[type=checkbox]");

if (companyMultiSelectApplyBtn) {
    companyMultiSelectResetBtn.addEventListener("click", () => {
        companyCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
    });
}

if (companyMultiSelectHideBtn) {
    companyMultiSelectHideBtn.addEventListener("click", () => {
        companyMultiSelectList.classList.toggle("hidden");
    });
}

if (companyMultiSelectResetBtn) {
    companyMultiSelectApplyBtn.addEventListener("click", () => {
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

// --------------------------------------------------------------------------------------------
// This section handles the pagination of the job list

let btnNextPage = document.getElementById("btn-next-page");
let btnPreviousPage = document.getElementById("btn-previous-page");

let currentPage = parseInt(params.get("p")) || 1;

btnNextPage.addEventListener("click", () => {
    params.set("p", currentPage + 1);
    window.location.href = `${window.location.pathname}?${params.toString()}`;
});

btnPreviousPage.addEventListener("click", () => {
    params.set("p", currentPage - 1);
    window.location.href = `${window.location.pathname}?${params.toString()}`;
});

// --------------------------------------------------------------------------------------------