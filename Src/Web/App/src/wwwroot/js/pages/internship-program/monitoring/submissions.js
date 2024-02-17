import { $, createElement } from "../../../core/dom";
import { on } from "../../../core/events";
import { Grid, h, html } from "gridjs";

let apiEndpoint = window.location.protocol + "//" + window.location.host;

const params = new URLSearchParams(window.location.search);
const r = parseInt(params.get("r"));

if (isNaN(r)) {
    window.location.href = "/internship-program/monitoring";
}
apiEndpoint += "/api/intern-monitoring/requirements/" + r + "/user-requirements";

const submissionViewer = $("#submission-viewer");
const submissionViewerCloseBtn = $("#submission-viewer-close-btn");
const pdfViewer = $("#pdf-viewer");
const submissionViewerFilePicker = $("#submission-viewer #file-picker");

const grid = new Grid({
    className: {
        tbody: "gridjs-row-clickable"
    },
    columns: [
        { name: "id", hidden: true },
        "Index Number",
        "Full name",
        {
            name: "Status",
            formatter: (cell) => {
                if (cell === "pending") {
                    return html(
                        "<span class='fs-6 i i-dash-circle-fill text-warning'></span><span>Pending</span>"
                    );
                }
                return html(
                    "<span class='fs-6 i i-check-circle-fill text-success'></span><span>Fulfilled</span>"
                );
            },
        },
        {
            name: "Submission",
            formatter: (cell, row) => {
                return h("button", {
                    className: "btn btn-primary",
                    disabled: row.cells[3].data === "pending",
                    onClick: () => {
                        if (submissionViewer.style.display === "flex") {
                            submissionViewer.style.display = "none";
                            return;
                        }

                        if (parseInt(submissionViewerFilePicker.dataset.userRequirementId) === row.cells[0].data) {
                            submissionViewer.style.display = "flex";
                            return;
                        }

                        submissionViewerFilePicker.dataset.userRequirementId = row.cells[0].data;
                        submissionViewerFilePicker.innerHTML = "";

                        row.cells[8].data.forEach((item) => {
                            let option = createElement("option");
                            option.value = item.url;
                            option.textContent = item.name;
                            submissionViewerFilePicker.appendChild(option);
                        });

                        $("#title-bar-title").textContent = row.cells[1].data + " | " + row.cells[2].data;
                        pdfViewer.data = row.cells[8].data[0].url;
                        submissionViewer.style.display = "flex";
                    },
                    innerText: "View"
                });
            }
        },
        "Start date",
        "End date",
        { name: "fulfillMethod", hidden: true },
        { name: "files", hidden: true }
    ],
    server: {
        url: apiEndpoint,
        then: data => data.map(u => [
            u.id,
            u.indexNumber,
            u.fullName,
            u.status,
            null,
            u.startDate,
            u.endDate,
            u.fulfillMethod,
            u.files
        ])
    },
    search: {
        server: {
            url: (prev, keyword) => `${prev}?q=${keyword}`
        }
    },
});
grid.render($("#user-requirements-grid"));
grid.on("rowClick", (e, row) => {
    if (e.target.tagName === "BUTTON" || e.target.querySelector("button")) {
        return;
    }

    if (e.target.tagName === "A" || e.target.querySelector("a")) {
        return;
    }

    window.location.href = `/internship-program/monitoring/submissions/${row.cells[0].data}`;
});

on(submissionViewerFilePicker, "change", function () {
    pdfViewer.data = this.value;
});

on(submissionViewerCloseBtn, "click", () => {
    submissionViewer.style.display = "none";
});