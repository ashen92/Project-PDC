import { Grid } from "gridjs";

const apiEndpoint = window.location.protocol + "//" + window.location.host + "/api/intern-monitoring/students";

const grid = new Grid({
    className: {
        tbody: "gridjs-row-clickable"
    },
    columns: [
        { name: "id", hidden: true },
        "Index Number",
        "Registration Number",
        "Full name",
        "Student Email",
    ],
    server: {
        url: apiEndpoint,
        then: data => data.map(u => [
            u.id,
            u.indexNumber,
            u.registrationNumber,
            u.fullName,
            u.studentEmail
        ])
    },
    search: {
        server: {
            url: (prev, keyword) => `${prev}?q=${keyword}`
        }
    },
});
grid.render(document.getElementById("students-grid"));
grid.on("rowClick", (e, row) => {
    window.location.href = `/internship-program/monitoring/students/${row.cells[0].data}`;
});