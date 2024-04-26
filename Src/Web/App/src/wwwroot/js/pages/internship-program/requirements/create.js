import { $, $all } from "../../../core/dom.js";
import { on } from "../../../core/events.js";
import { createTextEditors } from "../../../components/textEditor.js";

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