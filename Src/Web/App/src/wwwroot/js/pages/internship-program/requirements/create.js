import { $ } from "../../../core/dom.js";
import { on } from "../../../core/events.js";
import { createTextEditors } from "../../../components/textEditor.js";

createTextEditors();

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