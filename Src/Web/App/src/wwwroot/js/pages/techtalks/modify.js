import { createTextEditors } from "../../components/textEditor.js";

createTextEditors();

const removesessionBtn = $("#remove-session-btn");

on(removesessionBtn, "click", function () {
    //console.log("Remove button clicked."); // Add this line to check if the event handler is triggered
    fetch("/techtalks/" + previouslySelectedItemCard.getAttribute("data-session-id")+"/deletecompanydata", { method: "DELETE" })
        .then(() => {
            //console.log("Event deleted successfully.");
            window.location.href = `${window.location.pathname}`;
        })
        .catch(error => console.error("Error deleting Techtalk:", error));
});