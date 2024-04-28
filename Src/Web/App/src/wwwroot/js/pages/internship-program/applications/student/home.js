import DataTable from "datatables.net-dt";

const internshipTable = new DataTable("#internship-applications-table", {
    paging: false,
    searching: false,
    info: false,
    ordering: false,
});

const jobRoleTable = new DataTable("#job-role-applications-table", {
    paging: false,
    searching: false,
    info: false,
    ordering: false,
});