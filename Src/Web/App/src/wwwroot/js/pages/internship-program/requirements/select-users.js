import { $ } from "../../../core/dom";
import { on } from "../../../core/events";
import { Grid, h } from "gridjs";

const apiEndpoint = window.location.protocol + "//" + window.location.host +
    "/api/internship-program/participants";

const grid = new Grid({
    className: {
        tbody: "gridjs-row-clickable"
    },
    columns: [
        {
            id: "checkbox",
            name: "",
            width: "50px",
            formatter: (cell, row) => {
                const selectedRows = JSON.parse(localStorage.getItem("selectedUserIds")) || [];
                if (selectedRows.includes(row.cells[1].data)) {
                    return h("div", { className: "text-center" },
                        h("input", {
                            type: "checkbox",
                            checked: true
                        })
                    );
                } else {
                    return h("div", { className: "text-center" },
                        h("input", {
                            type: "checkbox"
                        })
                    );
                }
            }
        },
        { name: "id", hidden: true },
        "Name",
        "Email",
        "User Type"
    ],
    server: {
        url: apiEndpoint,
        then: data => data.data.map(u => [
            null,
            u.id,
            u.fullName ? u.fullName : `${u.firstName} ${u.lastName}`,
            u.studentEmail ? u.studentEmail : u.email,
            u.type
        ]),
        total: data => data.totalCount
    },
    search: {
        server: {
            url: (prev, keyword) => `${prev}?q=${keyword}`
        }
    },
    pagination: {
        limit: 50,
        server: {
            url: (prev, page) => `${prev}?page=${page}`
        }
    }
});
grid.render($("#users-grid"));

grid.on("rowClick", (e, row) => {
    const selectedRows = JSON.parse(localStorage.getItem("selectedUserIds")) || [];
    const rowId = row.cells[1].data;
    if (selectedRows.includes(rowId)) {
        const index = selectedRows.indexOf(rowId);
        selectedRows.splice(index, 1);
        e.target.parentElement.querySelector("input[type=\"checkbox\"]").checked = false;
    } else {
        selectedRows.push(rowId);
        e.target.parentElement.querySelector("input[type=\"checkbox\"]").checked = true;
    }
    localStorage.setItem("selectedUserIds", JSON.stringify(selectedRows));
});

let persistSelectedRows = false;

window.onbeforeunload = function () {
    if (persistSelectedRows) {
        return;
    }
    localStorage.removeItem("selectedUserIds");
};

on($("#btn-done"), "click", function () {
    persistSelectedRows = true;
    window.location.href = "/internship-program/requirements/create";
});