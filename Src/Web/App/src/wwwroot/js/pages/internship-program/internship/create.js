import { $, $all } from "../../../core/dom.js";
import { on } from "../../../core/events.js";
import { createTextEditors } from "../../../components/textEditor.js";
import Choices from "choices.js";

createTextEditors();

let applyExternalFields = $("#apply-external-fields");
let externalWebsite = $("#external-website");

$all("#apply-method input[type='radio']").forEach(function (radio) {
    on(radio, "change", function () {
        if (radio.value === "external") {
            applyExternalFields.classList.remove("hidden");
            externalWebsite.required = true;
        } else {
            applyExternalFields.classList.add("hidden");
            externalWebsite.required = false;
        }
    });
});

if ($("#organization")) {
    const choices = new Choices("#organization", {
        itemSelectText: "",
        searchFields: ["label"],
    });
}