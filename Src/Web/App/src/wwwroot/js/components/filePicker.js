import { on } from "../core/events.js";

function filePicker(fileInputElement, filePreviewElement) {
    on(fileInputElement, "change", function () {
        updateImageDisplay(fileInputElement, filePreviewElement);
    });
}

function updateImageDisplay(input, preview) {
    while (preview.firstChild) {
        preview.removeChild(preview.firstChild);
    }

    // TODO - Use createElement in dom.js

    const curFiles = input.files;
    if (curFiles.length === 0) {
        const para = document.createElement("p");
        para.textContent = "No files currently selected for upload";
        preview.appendChild(para);
    } else {
        const list = document.createElement("ol");
        preview.appendChild(list);

        for (const file of curFiles) {
            const listItem = document.createElement("li");
            const para = document.createElement("p");

            para.innerHTML = `${file.name}. <small>${returnFileSize(file.size)}</small>`;

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

function returnFileSize(number) {
    if (number < 1024) {
        return number + "bytes";
    } else if (number > 1024 && number < 1048576) {
        return (number / 1024).toFixed(1) + "KB";
    } else if (number > 1048576) {
        return (number / 1048576).toFixed(1) + "MB";
    }
}

export { filePicker };