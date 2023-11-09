(() => {
  // src/wwwroot/js/requirement-add.js
  var oneTimeEndDateElement = document.getElementById("field-one-time");
  var recurringRepeatElement = document.getElementById("field-recurring");
  var radioOneTimeElement = document.getElementById("type-one-time");
  var radioRecurringElement = document.getElementById("type-recurring");
  var endBeforeDateElement = document.getElementById("end-before-one-time");
  var radioRepeatElements = document.querySelectorAll("input[name='repeat']");
  radioOneTimeElement.addEventListener("change", function() {
    radioRepeatElements.forEach((element) => {
      element.removeAttribute("required");
    });
    endBeforeDateElement.setAttribute("required", "");
    oneTimeEndDateElement.classList.add("block");
    oneTimeEndDateElement.classList.remove("hidden");
    recurringRepeatElement.classList.add("hidden");
    recurringRepeatElement.classList.remove("block");
  });
  radioRecurringElement.addEventListener("change", function() {
    radioRepeatElements.forEach((element) => {
      element.setAttribute("required", "");
    });
    endBeforeDateElement.removeAttribute("required");
    oneTimeEndDateElement.classList.add("hidden");
    oneTimeEndDateElement.classList.remove("block");
    recurringRepeatElement.classList.add("block");
    recurringRepeatElement.classList.remove("hidden");
  });
})();
//# sourceMappingURL=requirement-add.js.map
