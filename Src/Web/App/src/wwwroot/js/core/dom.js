function $(selector) {
    return document.querySelector(selector);
}

function $all(selector) {
    return document.querySelectorAll(selector);
}

function createElement(tag, attrs = {}, children = []) {
    const element = document.createElement(tag);
    Object.keys(attrs).forEach(key => element.setAttribute(key, attrs[key]));
    children.forEach(child => {
        if (typeof child === "string") {
            element.appendChild(document.createTextNode(child));
        } else {
            element.appendChild(child);
        }
    });
    return element;
}

export { $, $all, createElement };