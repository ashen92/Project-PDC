import { $, $all } from "../../../core/dom";
import { on } from "../../../core/events";
import { Dialog } from "../../../components/dialog";
import { FileUpload } from "../../../components/file-upload";

const fileUploadDialog = new Dialog("#file-upload-dialog");
fileUploadDialog.setTitle("Upload your CV or resume");

const applyBtns = $all("#job-roles-container .btn-container [name='apply-btn']");

let lastClickedApplyBtn = null;

applyBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
        lastClickedApplyBtn = btn;
        fileUploadDialog.open();
    });
});

const fileUpload = new FileUpload(
    $("#file-upload-container"),
    $("#file-upload-container input[type=file]"),
    $("#file-upload-container label")
);

const fileUploadForm = $("#file-upload-form");
on(fileUploadForm, "submit", function (e) {
    e.preventDefault();
    fileUploadForm.action = `${window.location.href}/job-roles/${lastClickedApplyBtn.id}/apply`;
    fileUploadForm.submit();
});

const removeBtns = $all("#job-roles-container .btn-container [name='remove-btn']");

removeBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
        fetch(`${window.location.href}/job-roles/${btn.id}/apply`, {
            method: "DELETE",
        }).then((response) => {
            if (response.status === 204) {
                window.location.reload();
            } else {
                console.error("Failed to remove from job role");
            }
        });
    });
});