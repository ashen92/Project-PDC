import { $, $all } from "../core/dom.js";
import Quill from "quill";

function textEditor(textEditorElement, textEditorContent, textEditorPlaceholder, textValueElement) {
    let toolbarOptions = [["bold", "italic"], [{ "list": "bullet" }, { "list": "ordered" }]];
    let quill = new Quill(textEditorElement, {
        formats: ["bold", "italic", "list"],
        modules: {
            toolbar: toolbarOptions
        },
        placeholder: textEditorPlaceholder,
        theme: "snow"
    });

    if (textEditorContent) {
        quill.clipboard.dangerouslyPasteHTML(textEditorContent);
    }

    quill.on("text-change", function () {
        textValueElement.value = quill.root.innerHTML;
    });
}

function createTextEditors() {
    const textEditorContainers = $all(".text-editor-container");
    let editors = [];
    textEditorContainers.forEach(function (container) {
        const textEditorElement = container.querySelector(".text-editor");
        const textValueElement = container.querySelector("input");
        const textEditorContent = textValueElement.value;
        const textEditorPlaceholder = textValueElement.getAttribute("placeholder");
        textEditor(textEditorElement, textEditorContent, textEditorPlaceholder, textValueElement);
        editors.push(textEditorElement);
    });
    return editors;
}

export { createTextEditors };