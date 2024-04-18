import DataTable from "datatables.net-dt";

const table = new DataTable("#students-table");

table.on("click", "tbody", function (e) {
    const row = e.target.closest("tr");
    if (row) {
        const id = row.dataset.id;
        window.location.href = `${window.location.href}/${id}`;
    }
});