import { $, $all, createElement } from "../../../core/dom.js";
import { on, trigger } from "../../../core/events.js";
import { createTextEditors } from "../../../components/textEditor.js";

on(document, "DOMContentLoaded", function () {
    const selectedUsers = localStorage.getItem("selectUsers");
    if (selectedUsers) {
        userGroupRadio.forEach(function (element) {
            if (element.value === "select-users") {
                element.checked = true;
                trigger(element, "change");
            }
        });
    }
    localStorage.removeItem("selectUsers");
});

const userGroupRadio = $all("#user-group-radio input[name='user-group']");
const selectUsersField = $("#select-users-field");

userGroupRadio.forEach(function (element) {
    on(element, "change", function () {
        if (this.value === "select-users") {
            selectUsersField.classList.remove("hidden");
        } else {
            selectUsersField.classList.add("hidden");
        }
    });
});

on($("#select-users-btn"), "click", function () {
    localStorage.setItem("selectUsers", "true");
});

createTextEditors();

const recurringRepeatElement = $("#field-recurring");
const radioRepeatElements = $all("input[name='repeat-interval']");

const typeDiv = $("#type");

on(typeDiv, "change", function (event) {
    if (event.target.type === "radio") {
        if (event.target.value == "one-time") {
            radioRepeatElements.forEach(element => {
                element.removeAttribute("required");
            });
            recurringRepeatElement.classList.add("hidden");
            recurringRepeatElement.classList.remove("block");
        }
        else if (event.target.value == "recurring") {
            radioRepeatElements.forEach(element => {
                element.setAttribute("required", "");
            });
            recurringRepeatElement.classList.add("block");
            recurringRepeatElement.classList.remove("hidden");
        }
    }
});

const fulfillMethodDiv = $("#fulfill-method");
const fulfillMethodFileOptions = $("#fulfill-method-file-options");

on(fulfillMethodDiv, "change", function (event) {
    if (event.target.name === "fulfill-method") {
        if (event.target.checked) {
            if (event.target.id == "fulfill-method-file") {
                fulfillMethodFileOptions.classList.remove("hidden");
            }
            else if (event.target.id == "fulfill-method-text") {
                fulfillMethodFileOptions.classList.add("hidden");
            }
        }
    }
});

window.onbeforeunload = function () {
    localStorage.removeItem("selectedUserIds");
};

let form = $("#create-requirement-form");

on(form, "submit", function (event) {
    userGroupRadio.forEach(function (element) {
        if (element.checked && element.value === "select-users") {
            const selectedUserIds = JSON.parse(localStorage.getItem("selectedUserIds")) || [];
            if (selectedUserIds.length === 0) {
                event.preventDefault();
                alert("Please select users\nTODO: Show a better error message.");
            }

            let input = createElement("input", {
                type: "hidden",
                name: "user-ids",
                value: JSON.stringify(selectedUserIds)
            });
            form.appendChild(input);
        }
    });
});