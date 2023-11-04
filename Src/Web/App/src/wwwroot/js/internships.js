const parentFilters = document.getElementById("filters");

parentFilters.addEventListener("click", function (event) {
    let targetElement = event.target;

    while (targetElement !== null && !targetElement.matches("input[type='checkbox']")) {
        targetElement = targetElement.parentElement;
    }

    if (targetElement) {
        const category = targetElement.closest(".data-category").getAttribute("data-category");

        const params = new URLSearchParams(window.location.search);

        if (targetElement.checked) {
            params.append(`${category}[]`, targetElement.value);
        } else {
            params.delete(`${category}[]`, targetElement.value);
        }

        window.location.href = `${window.location.pathname}?${params.toString()}`;
    }
});
