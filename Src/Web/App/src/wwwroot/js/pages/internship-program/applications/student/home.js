import DataTable from "datatables.net-dt";

const table = new DataTable("#applications-table", {
    paging: false,
    searching: false,
    info: false,
    ordering: false,
});