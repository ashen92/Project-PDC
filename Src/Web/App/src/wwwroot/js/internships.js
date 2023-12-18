
const params = new URLSearchParams(window.location.search);

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

const jobDetailsContent = document.getElementById("job-details-content");
const jobDetailsSkeleton = document.getElementById("job-details-skeleton");
const jobList = document.getElementById("job-list");
const jobTitle = document.getElementById("job-title");
const jobDescription = document.getElementById("job-description");

let previouslySelectedItemCard = null;

jobList.addEventListener("click", function (event) {
    let itemCard = event.target.closest(".item-card");

    if (itemCard) {
        if (previouslySelectedItemCard == itemCard) { return; }

        let jobId = itemCard.getAttribute("data-job-id");

        previouslySelectedItemCard.classList.toggle("active");
        itemCard.classList.toggle("active");
        previouslySelectedItemCard = itemCard;
        jobDetailsSkeleton.classList.toggle("hidden");
        jobDetailsContent.classList.toggle("hidden");


        fetch("/internship-program/internships/" + jobId, { method: "GET" })
            .then(response => response.json())
            .then(data => {
                jobTitle.innerHTML = data.title;
                jobDescription.innerHTML = data.description;
                jobDetailsSkeleton.classList.toggle("hidden");
                jobDetailsContent.classList.toggle("hidden");
            })
            .catch(error => console.error("Error retrieving job:", error));
    }
});

const itemCard = document.querySelector(".item-card");
document.addEventListener("DOMContentLoaded", () => {
    itemCard.classList.toggle("active");
    previouslySelectedItemCard = itemCard;

    fetch("/internship-program/internships/" + itemCard.getAttribute("data-job-id"), { method: "GET" })
        .then(response => response.json())
        .then(data => {
            jobTitle.innerHTML = data.title;
            jobDescription.innerHTML = data.description;
            jobDetailsSkeleton.classList.toggle("hidden");
            jobDetailsContent.classList.toggle("hidden");
        })
        .catch(error => console.error("Error retrieving job:", error));
});

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

const filterByCompany = document.getElementById("filter-by-company");
const companyMultiSelectList = document.getElementById("company-multi-select-list");

filterByCompany.addEventListener("click", () => {
    companyMultiSelectList.classList.toggle("hidden");
});

const companyMultiSelectApplyBtn = document.getElementById("company-multi-select-list-apply-btn");
const companyMultiSelectResetBtn = document.getElementById("company-multi-select-list-reset-btn");
const companyMultiSelectHideBtn = document.getElementById("company-multi-select-list-hide-btn");

let companyCheckboxes = document.querySelectorAll("#company-multi-select-list input[type=checkbox]");

companyMultiSelectResetBtn.addEventListener("click", () => {
    companyCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
});

companyMultiSelectHideBtn.addEventListener("click", () => {
    companyMultiSelectList.classList.toggle("hidden");
});

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