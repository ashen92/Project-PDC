
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

const jobList = document.getElementById("job-list");
const jobTitle = document.getElementById("job-title");
const jobDescription = document.getElementById("job-description");
let previouslySelectedItemCard = null;
jobList.addEventListener("click", function (event) {
    let itemCard = event.target.closest(".item-card");

    if (itemCard) {
        let jobId = itemCard.getAttribute("data-job-id");

        fetch("/internship-program/internships/" + jobId, { method: "GET" })
            .then(response => response.json())
            .then(data => {
                jobTitle.innerHTML = data.title;
                jobDescription.innerHTML = data.description;

                previouslySelectedItemCard.classList.remove("active");
                itemCard.classList.add("active");
                previouslySelectedItemCard = itemCard;
            })
            .catch(error => console.error("Error retrieving job:", error));
    }
});

const jobDetailsContent = document.getElementById("job-details-content");
const jobDetailsSkeleton = document.getElementById("job-details-skeleton");
const itemCard = document.querySelector(".item-card");
document.addEventListener("DOMContentLoaded", () => {
    itemCard.classList.add("active");
    previouslySelectedItemCard = itemCard;

    fetch("/internship-program/internships/" + itemCard.getAttribute("data-job-id"), { method: "GET" })
        .then(response => response.json())
        .then(data => {
            jobTitle.innerHTML = data.title;
            jobDescription.innerHTML = data.description;
            jobDetailsSkeleton.style.display = "none";
            jobDetailsContent.style.display = "block";
        })
        .catch(error => console.error("Error retrieving job:", error));
});

const applicantsJobBtn = document.getElementById("applicants-job-btn");
const modifyJobBtn = document.getElementById("modify-job-btn");
const removeJobBtn = document.getElementById("remove-job-btn");

applicantsJobBtn.addEventListener("click", () => {
    console.log(previouslySelectedItemCard.getAttribute("data-job-id"));
    window.location.href = `${window.location.pathname}/${previouslySelectedItemCard.getAttribute("data-job-id")}/applicants`;
});

modifyJobBtn.addEventListener("click", () => {
    window.location.href = `${window.location.pathname}/${previouslySelectedItemCard.getAttribute("data-job-id")}/modify`;
});

removeJobBtn.addEventListener("click", () => {
    fetch("/internship-program/internships/" + previouslySelectedItemCard.getAttribute("data-job-id"), { method: "DELETE" })
        .then(() => {
            window.location.href = `${window.location.pathname}`;
        })
        .catch(error => console.error("Error deleting job:", error));
});