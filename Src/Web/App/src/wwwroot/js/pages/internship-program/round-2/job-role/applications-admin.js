import DataTable from "datatables.net-dt";
import { Dialog } from "../../../../components/dialog";
import { $ } from "../../../../core/dom";

const table = new DataTable("#applications-table", {
    pageLength: 25,
    columnDefs: [
        {
            targets: 0,
            visible: false,
        },
    ],
});

const dialog = new Dialog("#file-viewer");
dialog.setTitle("CV/Resume");
const pdfViewer = $("#pdf-viewer");

const url = "/internship-program/applicants/applications";

table.on("click", "tbody", (e) => {
    if (e.target.tagName === "BUTTON") {
        e.target.disabled = true;
        const id = table.row(e.target.closest("tr")).data()[0];
        if (e.target.name === "view-btn") {
            dialog.open();
            const fileId = e.target.dataset.fileId;
            if (pdfViewer.dataset.fileId !== fileId) {
                pdfViewer.data = `${url}/${id}/files/${fileId}`;
                pdfViewer.dataset.fileId = fileId;
            }
            e.target.disabled = false;
        }
    }
});