import { $ } from "../core/dom.js";
import Quill from "../components/quill.js";

function textEditor(textEditorElementId, textEditorContent, textEditorPlaceholder, textValueElementId) {
    let toolbarOptions = [["bold", "italic"], [{ "list": "bullet" }, { "list": "ordered" }]];
    let quill = new Quill("#".concat(textEditorElementId), {
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
        $("#" + textValueElementId).value = quill.root.innerHTML;
    });
}

export { textEditor };