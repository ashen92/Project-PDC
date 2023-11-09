
const oneTimeEndDateElement = document.getElementById("field-one-time");
const recurringRepeatElement = document.getElementById("field-recurring");

const radioOneTimeElement = document.getElementById("type-one-time");
const radioRecurringElement = document.getElementById("type-recurring");

radioOneTimeElement.addEventListener("change", function () {
    oneTimeEndDateElement.classList.add("block");
    oneTimeEndDateElement.classList.remove("hidden");
    recurringRepeatElement.classList.add("hidden");
    recurringRepeatElement.classList.remove("block");
});

radioRecurringElement.addEventListener("change", function () {
    oneTimeEndDateElement.classList.add("hidden");
    oneTimeEndDateElement.classList.remove("block");
    recurringRepeatElement.classList.add("block");
    recurringRepeatElement.classList.remove("hidden");
});