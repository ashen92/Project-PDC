(() => {
  // src/wwwroot/js/internships.js
  var jobRoleSelect = document.getElementById("jobRoleSelect");
  var companySelect = document.getElementById("companySelect");
  var jobListings = document.getElementById("jobListings");
  function filterJobListings() {
    const selectedRoles = Array.from(jobRoleSelect.selectedOptions, (option) => option.value);
    const selectedCompanies = Array.from(companySelect.selectedOptions, (option) => option.value);
    jobListings.querySelectorAll(".item").forEach((listing) => {
      const roles = listing.getAttribute("data-roles").split(",");
      const companies = listing.getAttribute("data-companies").split(",");
      const roleMatched = selectedRoles.length === 0 || roles.some((role) => selectedRoles.includes(role));
      const companyMatched = selectedCompanies.length === 0 || companies.some((company) => selectedCompanies.includes(company));
      listing.style.display = roleMatched && companyMatched ? "block" : "none";
    });
  }
  jobRoleSelect.addEventListener("change", filterJobListings);
  companySelect.addEventListener("change", filterJobListings);
})();
//# sourceMappingURL=internships.js.map
