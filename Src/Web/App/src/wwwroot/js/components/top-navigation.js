import { ajax } from "../core/ajax.js";
import { on } from "../core/events.js";

function topNavigation(topNavigationElement, contentElement) {
    var self = this;
    self.topNavigationElement = topNavigationElement;
    self.contentElement = contentElement;
    on(self.topNavigationElement, "click", function (e) {
        if (e.target && e.target.nodeName === "A") {
            e.preventDefault();
            var url = e.target.getAttribute("href");
            self.loadContent(url);
        }
    });
    self.loadContent = function (url) {
        ajax({ url: url })
            .then(responseText => {
                self.contentElement.innerHTML = responseText;
            })
            .catch(error => console.error("Error retrieving page:", error));
    };
}

export { topNavigation };