import { $, $all } from "../../../core/dom.js";
import { on } from "../../../core/events.js";

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
