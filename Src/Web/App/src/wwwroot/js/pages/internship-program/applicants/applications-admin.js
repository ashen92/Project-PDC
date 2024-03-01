import { Grid } from "gridjs";

let apiEndpoint = window.location.protocol + "//" + window.location.host;

const params = new URLSearchParams(window.location.search);
const i = parseInt(params.get("i"));

if (isNaN(i)) {
    apiEndpoint += "/api/applications";
} else {
    apiEndpoint += "/api/internships/" + i + "/applications";
}

const grid = new Grid({
    columns: [
        { name: "id", hidden: true },
        "First name",
        "Full name",
        "Email",
        "Status",
    ],
    server: {
        url: apiEndpoint,
        then: data => data.applications.map(a => [a.id, a.userFirstName, a.studentFullName, a.userEmail, a.status])
    },
    search: {
        server: {
            url: (prev, keyword) => `${prev}?fullName=${keyword}`
        }
    },
});
grid.render(document.getElementById("grid-applications"));
grid.on("rowClick", (e, row) => {
    window.location.href = `/internship-program/applicants/applications/${row.cells[0].data}`;
});