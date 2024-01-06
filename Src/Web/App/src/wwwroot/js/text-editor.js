import { $ } from "./core/dom.js";
import { on } from "./core/events.js";
import Quill from "./quill.min.js";

var container = $("#text-editor-container");

var toolbarOptions = [["bold", "italic"], [{ "list": "bullet" }, { "list": "ordered" }]];
var quill = new Quill("#text-editor", {
    formats: ["bold", "italic", "list", "bullet"],
    modules: {
        toolbar: toolbarOptions
    },
    placeholder: container.dataset.placeholder,
    theme: "snow"
});

if (container.dataset.content) {
    quill.clipboard.dangerouslyPasteHTML(container.dataset.content);
}

var textData = $("#text-data");
var form = textData.closest("form");
on(form, "submit", function (e) {
    e.preventDefault();
    textData.value = quill.root.innerHTML;
    form.submit();
});