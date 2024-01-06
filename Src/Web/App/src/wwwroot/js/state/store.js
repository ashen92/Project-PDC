let state = {

};

const listeners = [];

function getState() {
    return state;
}

function updateState(newState) {
    state = { ...state, ...newState };
    notifyListeners();
}

function subscribe(listener) {
    listeners.push(listener);
    return () => {
        const index = listeners.indexOf(listener);
        if (index > -1) {
            listeners.splice(index, 1);
        }
    };
}

function notifyListeners() {
    listeners.forEach(listener => listener());
}

export { getState, updateState, subscribe };