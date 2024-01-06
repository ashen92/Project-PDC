import { topNavigation } from "./components/top-navigation";
import { $ } from "./core/dom";

let topNav = $(".top-navigation");
let content = $(".page");

if (topNav && content) {
    topNav = new topNavigation(topNav, content);
}