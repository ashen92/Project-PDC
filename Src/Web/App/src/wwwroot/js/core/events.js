function on(element, eventType, handler) {
    if (element) {
        element.addEventListener(eventType, handler);
    }
}

function off(element, eventType, handler) {
    if (element) {
        element.removeEventListener(eventType, handler);
    }
}

function trigger(element, eventType) {
    if (element) {
        const event = new Event(eventType);
        element.dispatchEvent(event);
    }
}

export { on, off, trigger };