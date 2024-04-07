import { $ } from "../../../../core/dom";
import { Grid } from "gridjs";

const grid = new Grid({
    className: {
        tbody: "gridjs-row-clickable"
    },
    from: $("#students-table"),
});
grid.render($("#students-grid"));
grid.on("rowClick", (e, row) => {
    window.location.href = `/internship-program/monitoring/students/${row.cells[0].data}`;
});