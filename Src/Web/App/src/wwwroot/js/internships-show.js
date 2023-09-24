const parentFilters = document.getElementById("filters");

parentFilters.addEventListener("click", function (event) {
    let targetElement = event.target;

    while (targetElement !== null && !targetElement.matches("input[type='checkbox']")) {
        targetElement = targetElement.parentElement;
    }

    if (targetElement) {
        const isChecked = targetElement.checked;
        const checkboxValue = targetElement.value;

        const category = targetElement.closest(".data-category").getAttribute("data-category");

        // console.log(`You clicked on a checkbox in category ${category}. Checked: ${isChecked}, Value: ${checkboxValue}`);

        const params = new URLSearchParams(window.location.search);

        if (isChecked) {
            params.append(`${category}[]`, checkboxValue);
        }else {
            params.delete(`${category}[]`, checkboxValue);
        }

        window.location.href = `${window.location.pathname}?${params.toString()}`;
    }
});
