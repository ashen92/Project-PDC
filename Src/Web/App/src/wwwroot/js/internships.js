
const params = new URLSearchParams(window.location.search);

const searchBtn = document.getElementById("search-btn");
const searchQueryElement = document.getElementById("search-query");

searchBtn.addEventListener("click", () => {
    const searchQuery = searchQueryElement.value;
    if (searchQuery) {
        params.set("q", searchQuery);
        window.location.href = `${window.location.pathname}?${params.toString()}`;
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

jobList.addEventListener("click", function (event) {
    let itemCard = event.target.closest(".item-card");

    if (itemCard) {
        let jobId = itemCard.getAttribute("data-job-id");

        fetch("/internship-program/internships/" + jobId, { method: "GET" })
            .then(response => response.json())
            .then(data => {
                jobTitle.innerHTML = data.title;
                jobDescription.innerHTML = data.description;
            })
            .catch(error => console.error("Error retrieving job:", error));
    }
});

const jobDetailsContent = document.getElementById("job-details-content");
const jobDetailsSkeleton = document.getElementById("job-details-skeleton");
const itemCard = document.querySelector(".item-card");
document.addEventListener("DOMContentLoaded", () => {
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