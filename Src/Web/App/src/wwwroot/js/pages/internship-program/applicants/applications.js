import { Grid, h, html } from "gridjs";

const domain = window.location.protocol + "//" + window.location.host;
let apiEndpoint = domain;

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
        { name: "user_id", hidden: true },
        { name: "applications_status", hidden: true },
        "First name",
        "Full name",
        "Email",
        {
            name: h("span", {}, [
                "Availability",
                h("i", {
                    className: "i i-question-circle-fill",
                    title: "Indicates if the applicant is already hired or not available for hiring."
                })
            ]),
            formatter: (cell, row) => {
                if (cell === true) {
                    return html(
                        "<span class='fs-6 i i-check-circle text-success'></span><span>Available</span>"
                    );
                }
                if (row.cells[2].data === "hired") {
                    return html(
                        "<span class='fs-6 i i-check-circle-fill text-success'></span><span>Hired</span>"
                    );
                }
                return html(
                    "<span class='fs-6 i i-x-circle text-danger'></span><span>Not available</span>"
                );
            },
        },
        {
            name: "Hire",
            formatter: (cell, row) => {
                return h("button", {
                    className: "btn btn-primary",
                    onClick: (e) => {
                        let data = {
                            applicationId: row.cells[0].data
                        };
                        fetch(domain + "/api/applicants/" + row.cells[1].data + "/hire", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify(data)
                        }).then(response => {
                            if (!response.ok) {
                                throw new Error("Error hiring applicant");
                            }
                            e.target.disabled = true;
                            grid.forceRender();
                        }).catch(error => {
                            console.error("Error:", error);
                        });
                    },
                    disabled: row.cells[6].data === false
                }, "Hire");
            }
        },
        {
            name: "Reject",
            formatter: (cell, row) => {
                return h("button", {
                    className: "btn btn-outline-danger",
                    onClick: (e) => {
                        fetch(domain + "/api/applicants/" + row.cells[1].data +
                            "/applications/" + row.cells[0].data + "/reject", {
                            method: "POST",
                        }).then(response => {
                            if (!response.ok) {
                                throw new Error("Error rejecting application");
                            }
                            e.target.disabled = true;
                            grid.forceRender();
                        }).catch(error => {
                            console.error("Error:", error);
                        });
                    },
                    disabled: row.cells[2].data === "rejected"
                }, "Reject");
            }
        },
        {
            name: h("span", {}, [
                "Reset",
                h("i", {
                    className: "i i-question-circle-fill",
                    title: "Reset the hired or rejected status of the application"
                })
            ]),
            formatter: (cell, row) => {
                return h("button", {
                    className: "btn btn-secondary",
                    onClick: (e) => {
                        fetch(domain + "/api/applicants/" + row.cells[1].data +
                            "/applications/" + row.cells[0].data + "/status/reset", {
                            method: "POST",
                        }).then(response => {
                            if (!response.ok) {
                                throw new Error("Error resetting application");
                            }
                            e.target.disabled = true;
                            grid.forceRender();
                        }).catch(error => {
                            console.error("Error:", error);
                        });
                    },
                    disabled: row.cells[2].data === "pending"
                }, "Reset");
            }
        },
    ],
    server: {
        url: apiEndpoint,
        then: data => data.applications.map(a => [
            a.id, a.userId, a.status, a.userFirstName,
            a.studentFullName, a.userEmail, a.isApplicantAvailable
        ])
    },
    search: {
        server: {
            url: (prev, keyword) => `${prev}?fullName=${keyword}`
        }
    },
});
grid.render(document.getElementById("grid-applications"));
grid.on("rowClick", (e, row) => {
    if (e.target.tagName === "BUTTON" || e.target.querySelector("button")) {
        return;
    }

    if (e.target.tagName === "A" || e.target.querySelector("a")) {
        return;
    }

    window.location.href = `/internship-program/applicants/applications/${row.cells[0].data}`;
});