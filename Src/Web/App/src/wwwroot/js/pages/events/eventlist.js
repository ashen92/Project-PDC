import { $, $all } from "../../core/dom";
import { on } from "../../core/events";




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

const eventDetailsContent = $("#event-details-content");
const eventDetailsSkeleton = $("#event-details-skeleton");
const eventList = $("#event-list");
const eventTitle = $("#event-title");
const eventDescription = $("#event-description");
const eventLocation = $("#event-location");
const eventstartTime = $("#event-startTime");
const eventendTime = $("#event-endTime");

let previouslySelectedItemCard = null;
let isLoading = false;

function fetchEventDetails(eventId) {
    fetch(`/events/${eventId}`, { method: "GET" })
        .then(response => response.json())
        .then(data => {
            eventTitle.innerHTML = data.title;
            eventLocation.innerHTML = data.eventLocation;
            eventstartTime.innerHTML = data.startTime;
            eventendTime.innerHTML = data.endTime;
            eventDescription.innerHTML = data.description;
            eventDetailsSkeleton.classList.toggle("hidden");
            eventDetailsContent.classList.toggle("hidden");
        })
        .catch(error => console.error("Error retrieving event:", error))
        .finally(() => { isLoading = false; });
}

on(eventList, "click", function (event) {
    if (isLoading) { return; }

    const itemCard = event.target.closest(".job");

    if (itemCard && previouslySelectedItemCard !== itemCard) {

        if (previouslySelectedItemCard) {
            previouslySelectedItemCard.classList.toggle("active");
        }

        itemCard.classList.toggle("active");
        previouslySelectedItemCard = itemCard;
        eventDetailsSkeleton.classList.toggle("hidden");
        eventDetailsContent.classList.toggle("hidden");

        isLoading = true;
        fetchEventDetails(itemCard.getAttribute("data-event-id"));
    }
});

on(document, "DOMContentLoaded", function () {
    const itemCard = document.querySelector(".job");
    if (itemCard) {
        itemCard.classList.toggle("active");
        previouslySelectedItemCard = itemCard;

        isLoading = true;
        fetchEventDetails(itemCard.getAttribute("data-event-id"));
    }
});

//#endregion

//#region 
// This section handles the buttons in the details pane

const modifyEventBtn = $("#modify-event-btn");
const removeEventBtn = $("#remove-event-btn");

on(modifyEventBtn, "click", function () {
    window.location.href = `${window.location.origin}/events/${previouslySelectedItemCard.getAttribute("data-event-id")}/modify`;
});

on(removeEventBtn, "click", function () {
    fetch("/events/" + previouslySelectedItemCard.getAttribute("data-event-id"), { method: "DELETE" })
        .then(() => {
            
            window.location.href = `${window.location.pathname}`;
        })
        .catch(error => console.error("Error deleting event:", error));
});
