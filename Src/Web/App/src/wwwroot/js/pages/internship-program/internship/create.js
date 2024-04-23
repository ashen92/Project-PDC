import { $ } from "../../../core/dom.js";
import { createTextEditors } from "../../../components/textEditor.js";
import Choices from "choices.js";

createTextEditors();

if ($("#organization")) {
    const choices = new Choices("#organization", {
        itemSelectText: "",
        searchFields: ["label"],
    });
}