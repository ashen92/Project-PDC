import { $, createElement } from "../core/dom";
import { on } from "../core/events";

export class Dialog {
    #onCloseCallback;

    constructor(dialogContainerSelector) {
        this.dialogContainer = $(dialogContainerSelector);
        this.contentDialog = this.dialogContainer.getElementsByClassName("dialog")[0];
        this.overlay = createElement("div", { class: "overlay" });

        this.dialogContainer.insertBefore(this.overlay, this.dialogContainer.firstChild);

        this.titleBarCloseBtn = createElement("button", {
            class: "btn btn-close",
            title: "Close",
            type: "button",
        }, [
            createElement("i", { class: "i i-x" })
        ]);

        this.titleBarCloseBtn.addEventListener("click", () => {
            this.close();
        });

        this.title = createElement("h5", { class: "title", id: "title" });
        this.titleBar = createElement("div", { class: "title-bar" },
            [
                this.title,
                this.titleBarCloseBtn
            ]);
        this.contentDialog.insertBefore(this.titleBar, this.contentDialog.firstChild);

        on(window, "click", function (e) {
            if (e.target === this.overlay) {
                this.titleBarCloseBtn.click();
            }
        }.bind(this));
    }

    open() {
        this.dialogContainer.style.display = "flex";
    }

    close() {
        this.dialogContainer.style.display = "none";
        if (this.#onCloseCallback) {
            this.#onCloseCallback();
        }
    }

    isOpen() {
        return this.dialogContainer.style.display === "flex";
    }

    setTitle(title) {
        this.title.innerText = title;
    }

    onClose(callback) {
        this.#onCloseCallback = callback;
    }
}
