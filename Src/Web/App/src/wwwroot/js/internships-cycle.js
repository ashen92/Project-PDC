const params = new URLSearchParams(document.location.search);

const pageName = params.get("view");
if (pageName == null || pageName == "internship-cycle") {
    document.getElementById("internship-cycle").classList.add("active");
} else {
    document.getElementById(pageName).classList.add("active");
}