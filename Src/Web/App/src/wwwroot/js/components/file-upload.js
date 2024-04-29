import { createElement } from "../core/dom";
import { on } from "../core/events.js";

export class FileUpload {
    constructor(
        fileUploadContainerElement, fileUploadInputElement, fileUploadLabelElement
    ) {
        this.container = fileUploadContainerElement;
        this.container.classList.add("field-file-upload");
        this.input = fileUploadInputElement;
        this.label = fileUploadLabelElement;
        this.label.classList.add("btn", "btn-secondary");

        let labelText = this.label.textContent;
        this.label.innerHTML = "";

        this.label.appendChild(createElement("i", { class: "i i-cloud-arrow-up" }));
        this.label.appendChild(createElement("span", {}, [labelText]));

        let previewLabel = "No file currently selected for upload";
        if (this.input.multiple === true) {
            previewLabel = "No files currently selected for upload";
        }
        this.preview = createElement("div", { class: "preview" }, [
            createElement("p", {}, [previewLabel])
        ]);
        this.container.appendChild(this.preview);

        on(this.input, "change", () => {
            this.#updateFileUploadPreview(this.input, this.preview);
        });
    }

    #updateFileUploadPreview(fileUploadInput, fileUploadPreview) {
        while (fileUploadPreview.firstChild) {
            fileUploadPreview.removeChild(fileUploadPreview.firstChild);
        }

        const curFiles = fileUploadInput.files;
        if (curFiles.length === 0) {
            const para = document.createElement("p");
            para.textContent = "No files currently selected for upload";
            fileUploadPreview.appendChild(para);
        } else {
            const list = document.createElement("ol");
            fileUploadPreview.appendChild(list);

            for (const file of curFiles) {
                const listItem = document.createElement("li");
                const para = document.createElement("p");

                para.innerHTML = `${file.name}. <small>${this.#calculateFileSize(file.size)}</small>`;

                if (file.type.match("image.*")) {
                    const image = document.createElement("img");
                    image.src = URL.createObjectURL(file);
                    listItem.appendChild(image);
                }

                listItem.appendChild(para);

                list.appendChild(listItem);
            }
        }
    }

    #calculateFileSize(number) {
        if (number < 1024) {
            return number + "bytes";
        } else if (number > 1024 && number < 1048576) {
            return (number / 1024).toFixed(1) + "KB";
        } else if (number > 1048576) {
            return (number / 1048576).toFixed(1) + "MB";
        }
    }
}