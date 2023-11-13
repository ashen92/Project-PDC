document.addEventListener("DOMContentLoaded", () => {
    const rowContainers = document.getElementsByClassName("table-rows-clickable");

    for (let i = 0; i < rowContainers.length; i++) {
        rowContainers[i].addEventListener("click", (event) => {
            const row = event.target.closest("tr");
            if (row) {
                window.location.href = row.dataset.href;
            }
        });
    }
});