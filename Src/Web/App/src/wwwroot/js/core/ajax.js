function ajax({ url, method = "GET", data = null, headers = {} }) {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open(method, url, true);
        Object.keys(headers).forEach(key => xhr.setRequestHeader(key, headers[key]));
        xhr.onload = () => {
            if (xhr.status >= 200 && xhr.status < 300) {
                resolve(xhr.responseText);
            } else {
                reject(new Error(xhr.statusText));
            }
        };
        xhr.onerror = () => reject(new Error("Network Error"));
        xhr.send(data);
    });
}

export { ajax };