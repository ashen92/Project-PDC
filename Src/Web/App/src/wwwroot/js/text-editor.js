import Quill from "./quill.min.js";

var container = document.getElementById("text-editor-container");

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

var textData = document.getElementById("text-data");
var form = textData.closest("form");
form.addEventListener("submit", (e) => {
    e.preventDefault();
    textData.value = quill.root.innerHTML;
    form.submit();
});