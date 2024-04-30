import { $, $all } from "../../core/dom";
import { on } from "../../core/events";
import { Dialog } from "../../components/dialog";
import DataTable from "datatables.net-dt";

const table = new DataTable("#users-table");

table.on("click", "tbody", function (e) {
    const row = e.target.closest("td");
    if (row) {
        const id = row.dataset.id;
        window.location.href = `${window.location.href}/${id}`;
    }
});

const commandBarButtons = $all(".command-bar button");

let tblSelectAllCheckbox = $("thead input[type=checkbox]");

on(tblSelectAllCheckbox, "change", function () {
    if (this.checked) {
        commandBarButtons.forEach((button) => {
            button.removeAttribute("disabled");
        });
    } else {
        commandBarButtons.forEach((button) => {
            button.setAttribute("disabled", "disabled");
        });
    }
});

const tblBody = $("tbody");

on(tblBody, "click", function (e) {
    const target = e.target;
    if (target.tagName === "INPUT") {
        const checked = target.checked;
        if (checked) {
            commandBarButtons.forEach((button) => {
                button.removeAttribute("disabled");
            });
        } else {
            const checkedRows = $all("tbody input[type='checkbox']:checked");
            if (checkedRows.length === 0) {
                commandBarButtons.forEach((button) => {
                    button.setAttribute("disabled", "disabled");
                });
            }
        }
    }
});

function sendResetPasswordRequest(ids) {
    const url = "/api/users";
    const method = "PUT";
    let data = [];
    ids.forEach((id) => {
        data.push({
            id: parseInt(id),
            isActive: false,
        });
    });
    data = JSON.stringify(data);
    const headers = {
        "Content-Type": "application/json",
    };

    return fetch(url, { method, headers, body: data });
}

commandBarButtons.forEach((button) => {
    on(button, "click", function () {
        const action = this.getAttribute("data-action");
        if (action === "reset") {
            const checkedRows = $all("tbody input[type='checkbox']:checked");
            const ids = [];
            checkedRows.forEach((checkbox) => {
                ids.push(checkbox.getAttribute("id"));
            });
            console.log(ids);
            sendResetPasswordRequest(ids)
                .then((response) => {
                    if (response.status === 204) {
                        location.reload();
                    } else {
                        throw new Error("Error resetting password");
                    }
                })
                .catch((error) => console.error("Error resetting password:", error));
        }
    });
});



const removeUserBtn = $("#remove-user-btn");
const activateUserBtn = $("#activate-user-btn");
const deactivateUserBtn = $("#deactivate-user-btn");
const addtoGroupBtn = $("#addto-group-btn");

on(removeUserBtn, "click", function () {
    const checkedRows = $all("tbody input[type='checkbox']:checked");


    checkedRows.forEach((checkbox) => {
        fetch(`/portal/users/${checkbox.id}`, { method: "DELETE" })
            .then(() => {
                window.location.href = `${window.location.pathname}`;
            })
            .catch(error => console.error("Error deleting User:", error));
    });
});

on(activateUserBtn, "click", function () {
    const checkedRows = $all("tbody input[type='checkbox']:checked");


    checkedRows.forEach((checkbox) => {
        fetch(`/portal/users/${checkbox.id}/activate`, { method: "GET" })
            .then(() => {
                window.location.href = `${window.location.pathname}`;
            })
            .catch(error => console.error("Error activating User:", error));
    });
});

on(deactivateUserBtn, "click", function () {
    const checkedRows = $all("tbody input[type='checkbox']:checked");


    checkedRows.forEach((checkbox) => {
        fetch(`/portal/users/${checkbox.id}/deactivate`, { method: "GET" })
            .then(() => {
                window.location.href = `${window.location.pathname}`;
            })
            .catch(error => console.error("Error deactivating User:", error));
    });
});

if (addtoGroupBtn) {
    const usergroupDialog = new Dialog("#usergroup-popup");
    usergroupDialog.setTitle("Add selected users to group");

    on(addtoGroupBtn, "click", function () {
        usergroupDialog.open();

    });

    const usergroupMultiSelectAddtoBtn = $("#usergroup-popup-addto-btn");
    const usergroupMultiSelectResetBtn = $("#usergroup-popup-reset-btn");

    const usergroupCheckboxes = $all("#usergroup-popup input[type=checkbox]");
    const checkedRows = $all("#usergroup-popup input[type='checkbox']:checked");


    on(usergroupMultiSelectResetBtn, "click", function () {
        usergroupCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
    });

    on(usergroupMultiSelectAddtoBtn, "click", function () {
        const checkedUsersRows = $all("#user-table input[type='checkbox']")
        let userRowsArray = Array.from(checkedUsersRows);
        let usergroups = [];
        let userids = [];
        userRowsArray.forEach(checkbox => {
            if (checkbox.checked) {
                userids.push(checkbox.getAttribute("id"));
            }
        })
        usergroupCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                usergroups.push(checkbox.getAttribute("id"));
            }
        });

        usergroups.forEach((groupid) => {
            userids.forEach((userid) => {
                fetch(`/portal/user-add-member/${userid}/${groupid}`, { method: "GET" })
                    .then(() => {
                        window.location.href = `${window.location.pathname}`;
                    })
                    .catch(error => console.error("Error adding user to a group:", error));
            })
        });
    });
}












