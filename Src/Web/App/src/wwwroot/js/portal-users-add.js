var userTypeRadios = document.querySelectorAll("#field-user-type input[type='radio']");
var studentField = document.querySelector("#field-student");
var partnerField = document.querySelector("#field-partner");
var genericField = document.querySelector("#field-generic");

function addRequired(field) {
    var inputs = field.querySelectorAll("input");
    inputs.forEach(function (input) {
        if (input.id != "send-email") {
            input.required = true;
        }
    });
}

function removeRequired(field) {
    var inputs = field.querySelectorAll("input");
    inputs.forEach(function (input) {
        input.required = false;
    });
}

userTypeRadios.forEach(function (radio) {
    radio.addEventListener("change", function () {
        var userType = this.value;

        // Show the appropriate field based on the selected user type
        if (userType === "student") {
            studentField.classList.remove("hidden");
            addRequired(studentField);
            partnerField.classList.add("hidden");
            removeRequired(partnerField);
            genericField.classList.add("hidden");
            removeRequired(genericField);
        } else if (userType === "partner") {
            partnerField.classList.remove("hidden");
            addRequired(partnerField);
            genericField.classList.remove("hidden");
            addRequired(genericField);
            studentField.classList.add("hidden");
            removeRequired(studentField);
        } else {
            genericField.classList.remove("hidden");
            addRequired(genericField);
            studentField.classList.add("hidden");
            removeRequired(studentField);
            partnerField.classList.add("hidden");
            removeRequired(partnerField);
        }
    });
});