import { $, $all } from "../../core/dom";
import { on } from "../../core/events";

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
