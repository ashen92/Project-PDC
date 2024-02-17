import { $ } from "../core/dom.js";
import Quill from "quill";

function textEditor(textEditorElementSelector, textEditorContent, textEditorPlaceholder, textValueElementSelector) {
    let toolbarOptions = [["bold", "italic"], [{ "list": "bullet" }, { "list": "ordered" }]];
    let quill = new Quill(textEditorElementSelector, {
        formats: ["bold", "italic", "list", "bullet"],
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
        $(textValueElementSelector).value = quill.root.innerHTML;
    });
}

export { textEditor };