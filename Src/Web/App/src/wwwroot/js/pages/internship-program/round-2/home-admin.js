import DataTable from "datatables.net-dt";
import 'datatables.net-select-dt';
import { Dialog } from "../../../components/dialog";
import { on } from "../../../core/events";
import { $ } from "../../../core/dom";

const table = new DataTable("#job-roles-table", {
    paging: false,
    ordering: false,
    info: false,
    select: {
        info: false,
        style: "single",
        items: "row"
    },
    columnDefs: [
        {
            targets: 0,
            visible: false
        },
        {
            targets: 1,
            className: "pl-6"
        },
    ]
});

const addDialog = new Dialog("#add-dialog");
addDialog.setTitle("Add Job Role");

on($("#add-job-role-btn"), "click", () => {
    addDialog.open();
});

on($("#add-dialog #add-cancel-btn"), "click", () => {
    addDialog.close();
});

const editDialog = new Dialog("#edit-dialog");
editDialog.setTitle("Modify Job Role");
const editJobRoleId = $("#edit-dialog #edit-job-role-id");
const editJobRoleInput = $("#edit-dialog #edit-job-role-name");
const editJobRoleBtn = $("#edit-job-role-btn");

on(editJobRoleBtn, "click", () => {
    const selected = table.rows({ selected: true }).data()
    if (selected.length === 0) {
        return;
    }

    editJobRoleId.value = selected[0][0];
    editJobRoleInput.value = selected[0][1];
    editDialog.open();
});

on($("#edit-dialog #edit-cancel-btn"), "click", () => {
    editDialog.close();
});

const deleteDialog = new Dialog("#delete-dialog");
const deleteJobRoleId = $("#delete-dialog #delete-job-role-id");
const deleteJobRoleBtn = $("#delete-job-role-btn");

on(deleteJobRoleBtn, "click", () => {
    const selected = table.rows({ selected: true }).data()
    if (selected.length === 0) {
        return;
    }

    deleteDialog.setTitle(selected[0][1]);
    deleteJobRoleId.value = selected[0][0];
    deleteDialog.open();
});

on($("#delete-dialog #delete-cancel-btn"), "click", () => {
    deleteDialog.close();
});

//#region Disable edit and delete buttons when no row is selected

table.on("select deselect", function () {
    const selected = table.rows({ selected: true }).data();
    if (selected.length === 0) {
        editJobRoleBtn.setAttribute("disabled", "disabled");
        deleteJobRoleBtn.setAttribute("disabled", "disabled");
    } else {
        editJobRoleBtn.removeAttribute("disabled");
        deleteJobRoleBtn.removeAttribute("disabled");
    }
});

//#endregion