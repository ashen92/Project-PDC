import { $, $all } from "../../../core/dom.js";
import { on } from "../../../core/events.js";
import Choices from "choices.js";

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

if ($("#org")) {
    const choices = new Choices("#org", {
        itemSelectText: "",
        searchFields: ["label"],
    });
}