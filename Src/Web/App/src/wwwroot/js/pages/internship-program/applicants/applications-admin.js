import DataTable from "datatables.net-dt";

const table = new DataTable("#applications-table", {
    pageLength: 25,
    columnDefs: [
        {
            targets: 0,
            visible: false,
        },
    ],
});