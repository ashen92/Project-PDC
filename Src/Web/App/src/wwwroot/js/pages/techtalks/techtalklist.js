/* eslint-disable linebreak-style */
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

on(searchQueryElement, "keyup", function (session) {
    if (session.key === "Enter") {
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

const sessionDetailsContent = $("#session-details-content");
const sessionDetailsSkeleton = $("#session-details-skeleton");
const sessionList = $("#session-list");
const sessionTitle = $("#session-title");
const sessionDescription = $("#session-description");
const sessionLocation = $("#session-location");
const sessionstartTime = $("#session-startTime");
const sessionendTime = $("#session-endTime");

let previouslySelectedItemCard = null;
let isLoading = false;

function fetchSessionDetails(sessionid) {
    fetch(`/techtalks/${sessionid}`, { method: "GET" })
        .then(response => response.json())
        .then(data => {
            sessionTitle.innerHTML = data.title;
            sessionLocation.innerHTML = data.sessionLocation;
            sessionstartTime.innerHTML = data.startTime;
            sessionendTime.innerHTML = data.endTime;
            sessionDescription.innerHTML = data.description;
            sessionDetailsSkeleton.classList.toggle("hidden");
            sessionDetailsContent.classList.toggle("hidden");
        })
        .catch(error => console.error("Error retrieving session:", error))
        .finally(() => { isLoading = false; });
}


on(sessionList, "click", function (session) {
    if (isLoading) { return; }

    const itemCard = session.target.closest(".job");

    if (itemCard && previouslySelectedItemCard !== itemCard) {

        if (previouslySelectedItemCard) {
            previouslySelectedItemCard.classList.toggle("active");
        }

        itemCard.classList.toggle("active");
        previouslySelectedItemCard = itemCard;
        sessionDetailsSkeleton.classList.toggle("hidden");
        sessionDetailsContent.classList.toggle("hidden");

        isLoading = true;
        fetchSessionDetails(itemCard.getAttribute("data-session-id"));
    }
});

on(document, "DOMContentLoaded", function () {
    const itemCard = document.querySelector(".job");
    if (itemCard) {
        itemCard.classList.toggle("active");
        previouslySelectedItemCard = itemCard;

        isLoading = true;
        fetchSessionDetails(itemCard.getAttribute("data-session-id"));
    }
});

//#endregion

//#region 
// This section handles the buttons in the details pane

const modifysessionBtn = $("#modify-session-btn");
const removesessionBtn = $("#remove-session-btn");

on(modifysessionBtn, "click", function () {
    window.location.href = `${window.location.pathname}/${previouslySelectedItemCard.getAttribute("data-session-id")}/modify`;
});

on(removesessionBtn, "click", function () {
    //console.log("Remove button clicked."); // Add this line to check if the event handler is triggered
    fetch("/techtalks/" + previouslySelectedItemCard.getAttribute("data-session-id"), { method: "DELETE" })
        .then(() => {
            //console.log("Event deleted successfully.");
            window.location.href = `${window.location.pathname}`;
        })
        .catch(error => console.error("Error deleting Techtalk:", error));
});
