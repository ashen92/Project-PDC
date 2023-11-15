
const oneTimeEndDateElement = document.getElementById("field-one-time");
const recurringRepeatElement = document.getElementById("field-recurring");

const radioOneTimeElement = document.getElementById("type-one-time");
const radioRecurringElement = document.getElementById("type-recurring");

const endBeforeDateElement = document.getElementById("end-before-one-time");
const radioRepeatElements = document.querySelectorAll("input[name='repeat-interval']");

radioOneTimeElement.addEventListener("change", function () {

    radioRepeatElements.forEach(element => {
        element.removeAttribute("required");
    });

    endBeforeDateElement.setAttribute("required", "");

    oneTimeEndDateElement.classList.add("block");
    oneTimeEndDateElement.classList.remove("hidden");
    recurringRepeatElement.classList.add("hidden");
    recurringRepeatElement.classList.remove("block");
});

radioRecurringElement.addEventListener("change", function () {

    radioRepeatElements.forEach(element => {
        element.setAttribute("required", "");
    });

    endBeforeDateElement.removeAttribute("required");

    oneTimeEndDateElement.classList.add("hidden");
    oneTimeEndDateElement.classList.remove("block");
    recurringRepeatElement.classList.add("block");
    recurringRepeatElement.classList.remove("hidden");
});