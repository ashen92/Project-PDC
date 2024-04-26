import { $, $all } from "../../../core/dom";

const jobRoleCheckboxes = $all("#job-roles-container input[type=checkbox]");

jobRoleCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", () => {
        if (checkbox.checked) {
            toggleCheckboxes(true);
            applyToJobRole(checkbox.id);
        } else {
            toggleCheckboxes(true);
            removeFromJobRole(checkbox.id);
        }
    });
});

function applyToJobRole(jobRoleId) {
    fetch(`${window.location.href}/job-roles/${jobRoleId}/apply`, {
        method: "PUT",
    }).then((response) => {
        toggleCheckboxes(false);
        if (response.status === 204) {
            showFlashMessage(true);
        } else {
            showFlashMessage(false);
            console.error("Failed to apply to job role");
        }
    });
}

function removeFromJobRole(jobRoleId) {
    fetch(`${window.location.href}/job-roles/${jobRoleId}/apply`, {
        method: "DELETE",
    }).then((response) => {
        toggleCheckboxes(false);
        if (response.status === 204) {
            showFlashMessage(true);
        } else {
            showFlashMessage(false);
            console.error("Failed to remove from job role");
        }
    });
}

const alertContainer = $("#alert-container");
const alertSuccess = $("#alert-container #alert-success");
const alertError = $("#alert-container #alert-error");
function showFlashMessage(isSuccess, duration = 3000) {
    if (isSuccess) {
        alertSuccess.innerText = "Changes saved successfully";
        alertSuccess.classList.remove("hidden");
        alertContainer.classList.remove("hidden");
        setTimeout(() => {
            alertSuccess.innerText = "";
            alertSuccess.classList.add("hidden");
            alertContainer.classList.add("hidden");
        }, duration);
    } else {
        alertError.innerText = "An error occurred. Please try again.";
        alertError.classList.remove("hidden");
        alertContainer.classList.remove("hidden");
        setTimeout(() => {
            alertError.innerText = "";
            alertError.classList.add("hidden");
            alertContainer.classList.add("hidden");
        }, duration);
    }
}

function toggleCheckboxes(isDisabled) {
    jobRoleCheckboxes.forEach(checkbox => {
        checkbox.disabled = isDisabled;
    });
}