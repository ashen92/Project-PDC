(() => {
  // src/wwwroot/js/internships.js
  var params = new URLSearchParams(window.location.search);
  var searchBtn = document.getElementById("search-btn");
  var searchQueryElement = document.getElementById("search-query");
  searchBtn.addEventListener("click", () => {
    const searchQuery = searchQueryElement.value;
    if (searchQuery) {
      params.set("q", searchQuery);
      window.location.href = `${window.location.pathname}?${params.toString()}`;
    }
  });
  searchQueryElement.addEventListener("keyup", (event) => {
    if (event.key === "Enter") {
      searchBtn.click();
    }
  });
  var query = params.get("q");
  if (query) {
    searchQueryElement.value = query;
  }
})();
//# sourceMappingURL=internships.js.map
