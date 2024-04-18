import { createElement, $ } from "../../../core/dom";
import { on } from "../../../core/events";
import { Dialog } from "../../../components/dialog";
import DataTable from "datatables.net-dt";

const table = new DataTable("#user-requirements-table", {
    columnDefs: [
        { targets: 6, visible: false }
    ],
    pageLength: 25,
});

const submissionViewerDialog = new Dialog("#submission-viewer");
const submissionViewerFilePicker = $("#submission-viewer #file-picker");
const pdfViewer = $("#pdf-viewer");

table.on("click", "tbody tr td:nth-child(6) button", function (e) {
    if (e.target.dataset.id === submissionViewerFilePicker.dataset.userRequirementId) {
        submissionViewerDialog.open();
        return;
    }

    submissionViewerFilePicker.dataset.userRequirementId = e.target.dataset.id;
    submissionViewerFilePicker.innerHTML = "";

    const row = e.target.closest("tr");
    const rowData = table.row(row).data();
    const files = JSON.parse(rowData[6]);

    files.forEach((item) => {
        let option = createElement("option");
        option.value = item.id;
        option.textContent = item.name;
        submissionViewerFilePicker.appendChild(option);
    });

    submissionViewerDialog.setTitle(rowData[0] + " | " + rowData[1]);
    pdfViewer.data = `${window.location.origin}${window.location.pathname}/${e.target.dataset.id}/files/${files[0].id}`;
    submissionViewerDialog.open();
});

on(submissionViewerFilePicker, "change", function () {
    pdfViewer.data = this.value;
});