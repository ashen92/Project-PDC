import { createElement } from "../core/dom";
import { on } from "../core/events";

export class Collapse {
    constructor(collapseContainerElement) {
        this.collapseContainer = collapseContainerElement;
        this.collapseContent = this.collapseContainer.querySelector(".collapse-content");

        this.collapseToggleIcon = createElement("i", { class: "i i-chevron-contract" });
        this.collapseToggleText = createElement("span", {}, ["Collapse"]);
        this.collapseToggle = createElement("button", { class: "btn btn-regular collapse-toggle" }, [
            this.collapseToggleIcon, this.collapseToggleText
        ]);

        const collapseHeader = this.collapseContainer.querySelector(".collapse-header");
        collapseHeader.appendChild(this.collapseToggle);

        on(this.collapseToggle, "click", () => {
            this.toggle();
        });
        this.isOpen = true;
    }

    open() {
        if (this.isOpen) return;

        this.collapseContent.classList.remove("hidden");
        this.collapseToggleIcon.classList.remove("i-chevron-expand");
        this.collapseToggleIcon.classList.add("i-chevron-contract");
        this.collapseToggleText.innerText = "Collapse";
        this.isOpen = true;
    }

    close() {
        if (!this.isOpen) return;

        this.collapseContent.classList.add("hidden");
        this.collapseToggleIcon.classList.remove("i-chevron-contract");
        this.collapseToggleIcon.classList.add("i-chevron-expand");
        this.collapseToggleText.innerText = "Expand";
        this.isOpen = false;
    }

    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }
}