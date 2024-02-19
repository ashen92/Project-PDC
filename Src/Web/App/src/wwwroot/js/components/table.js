import { $, $all } from "../core/dom.js";
import { on } from "../core/events.js";

function makeRowsClickable(rowContainerElementSelector) {
    if (rowContainerElementSelector[0] === ".") {
        const rowContainers = $all(rowContainerElementSelector);

        for (let i = 0; i < rowContainers.length; i++) {
            on(rowContainers[i], "click", function (event) {
                const target = event.target;
                const row = target.closest("tr");

                if (target.matches("input[type='checkbox']") && target.closest("td").querySelector("input[type='checkbox']"))
                    return;

                if (target.matches("button") && target.closest("td").querySelector("button"))
                    return;

                if (row) {
                    window.location.href = row.dataset.href;
                }
            });
        }
        return;
    }

    on($(rowContainerElementSelector), "click", function (event) {
        const target = event.target;
        const row = target.closest("tr");

        if (row && !target.matches("input[type='checkbox']") && !target.closest("td").querySelector("input[type='checkbox']")) {
            window.location.href = row.dataset.href;
        }
    });
}

function makeRowsSelectable(selectAllCheckboxUniqueSelector, checkboxElements) {
    on($(selectAllCheckboxUniqueSelector), "change", function () {
        checkboxElements.forEach((checkbox) => {
            checkbox.checked = this.checked;
        });
    });
}

export { makeRowsClickable, makeRowsSelectable };