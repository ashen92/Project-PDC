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

let url = new URL(window.location.href);
url.search = "";
url = url.toString();

table.on("click", "tbody", (e) => {
    if (e.target.tagName === "BUTTON") {
        e.target.disabled = true;
        const id = table.row(e.target.closest("tr")).data()[0];

        if (e.target.name === "hire-btn") {
            fetch(`${url}/${id}/hire`, {
                method: "PUT"
            }).then(response => {
                if (!response.status === 204) {
                    throw new Error("Error occurred");
                }
                location.reload(true);
            }).catch(error => {
                console.error(error);
            }).finally(() => {
                e.target.disabled = false;
            });
        } else if (e.target.name === "reject-btn") {
            fetch(`${url}/${id}/reject`, {
                method: "PUT"
            }).then(response => {
                if (!response.status === 204) {
                    throw new Error("Error occurred");
                }
                location.reload(true);
            }).catch(error => {
                console.error(error);
            }).finally(() => {
                e.target.disabled = false;
            });
        } else if (e.target.name === "reset-btn") {
            fetch(`${url}/${id}/reset`, {
                method: "PUT"
            }).then(response => {
                if (!response.status === 204) {
                    throw new Error("Error occurred");
                }
                location.reload(true);
            }).catch(error => {
                console.error(error);
            }).finally(() => {
                e.target.disabled = false;
            });
        }
    }
});