import DataTable from "datatables.net-dt";
import { Dialog } from "../../../components/dialog";
import { $ } from "../../../core/dom";

const table = new DataTable("#applications-table", {
    pageLength: 25,
    columnDefs: [
        {
            targets: 0,
            visible: false,
        },
    ],
});

const fileViewerDialog = new Dialog("#file-viewer");
fileViewerDialog.setTitle("CV/Resume");
const pdfViewer = $("#pdf-viewer");

const errorDialog = new Dialog("#error-dialog");
errorDialog.setTitle("Error occurred");
errorDialog.onClose(() => {
    location.reload(true);
});
let errorMessage = $("#error-dialog #error-message");

let url = new URL(window.location.href);
url.search = "";
url = url.toString();

table.on("click", "tbody", (e) => {
    if (e.target.tagName === "BUTTON") {
        e.target.disabled = true;
        const id = table.row(e.target.closest("tr")).data()[0];

        if (e.target.name === "hire-btn") {
            fetch(`${url}/${id}/hire`, {
                method: "PUT"
            }).then(response => {
                if (!(response.status === 204)) {
                    response.json().then(data => {
                        errorMessage.textContent = data.message;
                        errorDialog.open();
                    });
                    return;
                }
                location.reload(true);
            }).catch(error => {
                console.error(error);
            });
        } else if (e.target.name === "reject-btn") {
            fetch(`${url}/${id}/reject`, {
                method: "PUT"
            }).then(response => {
                if (!(response.status === 204)) {
                    throw new Error("Error occurred");
                }
                location.reload(true);
            }).catch(error => {
                console.error(error);
            });
        } else if (e.target.name === "reset-btn") {
            fetch(`${url}/${id}/reset`, {
                method: "PUT"
            }).then(response => {
                if (!(response.status === 204)) {
                    throw new Error("Error occurred");
                }
                location.reload(true);
            }).catch(error => {
                console.error(error);
            });
        } else if (e.target.name === "view-btn") {
            fileViewerDialog.open();
            const fileId = e.target.dataset.fileId;
            if (pdfViewer.dataset.fileId !== fileId) {
                pdfViewer.data = `${url}/${id}/files/${fileId}`;
                pdfViewer.dataset.fileId = fileId;
            }
            e.target.disabled = false;
        }
    }
});