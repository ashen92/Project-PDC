import { $all } from "../core/dom.js";
import { on } from "../core/events.js";

on(document, "DOMContentLoaded", function () {
    const rowContainers = $all(".table-rows-clickable");

    for (let i = 0; i < rowContainers.length; i++) {
        on(rowContainers[i], "click", function (event) {
            const target = event.target;
            const row = target.closest("tr");

            if (row && !target.matches("input[type='checkbox']") && !target.closest("td").querySelector("input[type='checkbox']")) {
                window.location.href = row.dataset.href;
            }
        });
    }
});