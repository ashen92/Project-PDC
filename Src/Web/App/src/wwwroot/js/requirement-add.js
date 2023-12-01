const oneTimeEndDateElement = document.getElementById("field-one-time");
const recurringRepeatElement = document.getElementById("field-recurring");

const endBeforeDateElement = document.getElementById("end-before-one-time");
const radioRepeatElements = document.querySelectorAll("input[name='repeat-interval']");

const typeDiv = document.getElementById("type");

typeDiv.addEventListener("change", function (event) {
    if (event.target.type === "radio") {
        if (event.target.value == "one-time") {
            radioRepeatElements.forEach(element => {
                element.removeAttribute("required");
            });

            endBeforeDateElement.setAttribute("required", "");

            oneTimeEndDateElement.classList.add("block");
            oneTimeEndDateElement.classList.remove("hidden");
            recurringRepeatElement.classList.add("hidden");
            recurringRepeatElement.classList.remove("block");
        }
        else if (event.target.value == "recurring") {
            radioRepeatElements.forEach(element => {
                element.setAttribute("required", "");
            });

            endBeforeDateElement.removeAttribute("required");

            oneTimeEndDateElement.classList.add("hidden");
            oneTimeEndDateElement.classList.remove("block");
            recurringRepeatElement.classList.add("block");
            recurringRepeatElement.classList.remove("hidden");
        }
    }
});

const fulfillMethodDiv = document.getElementById("fulfill-method");
const fulfillMethodFileOptions = document.getElementById("fulfill-method-file-options");

fulfillMethodDiv.addEventListener("change", function (event) {
    if (event.target.name === "fulfill-method") {
        if (event.target.checked) {
            if (event.target.value == "file") {
                fulfillMethodFileOptions.classList.remove("hidden");
                fulfillMethodFileOptions.classList.add("block");
            }
            else {
                fulfillMethodFileOptions.classList.remove("block");
                fulfillMethodFileOptions.classList.add("hidden");
            }
        }
    }
});