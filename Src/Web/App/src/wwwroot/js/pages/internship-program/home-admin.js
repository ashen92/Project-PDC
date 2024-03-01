import { $ } from "../../core/dom.js";
import { on } from "../../core/events.js";

on($("#job-collection-btn-container"), "click", function (e) {
    const btn = e.target;
    if (btn.tagName === "BUTTON") {
        btn.disabled = true;
        const id = btn.id;

        if (id === "start-job-collection-btn") {
            fetch("/api/internship-program/job-collection/start", { method: "PATCH" })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response);
                    }
                    location.reload();
                })
                .catch(error => {
                    console.error(error);
                    btn.disabled = false;
                });
            return;
        }

        if (id === "undo-job-collection-btn") {
            fetch("/api/internship-program/job-collection/undo", { method: "PATCH" })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response);
                    }
                    location.reload();
                })
                .catch(error => {
                    console.error(error);
                    btn.disabled = false;
                });
            return;
        }

        if (id === "end-job-collection-btn") {
            fetch("/api/internship-program/job-collection/end", { method: "PATCH" })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response);
                    }
                    location.reload();
                })
                .catch(error => {
                    console.error(error);
                    btn.disabled = false;
                });
            return;
        }

        if (id === "restart-job-collection-btn") {
            fetch("/api/internship-program/job-collection/restart", { method: "PATCH" })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response);
                    }
                    location.reload();
                })
                .catch(error => {
                    console.error(error);
                    btn.disabled = false;
                });
            return;
        }
    }
});

on($("#applying-btn-container"), "click", function (e) {
    const btn = e.target;
    if (btn.tagName === "BUTTON") {
        btn.disabled = true;
        const id = btn.id;

        if (id === "start-applying-btn") {
            fetch("/api/internship-program/applying/start", { method: "PATCH" })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response);
                    }
                    location.reload();
                })
                .catch(error => {
                    console.error(error);
                    btn.disabled = false;
                });
            return;
        }

        if (id === "undo-applying-btn") {
            fetch("/api/internship-program/applying/undo", { method: "PATCH" })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response);
                    }
                    location.reload();
                })
                .catch(error => {
                    console.error(error);
                    btn.disabled = false;
                });
            return;
        }

        if (id === "end-applying-btn") {
            fetch("/api/internship-program/applying/end", { method: "PATCH" })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response);
                    }
                    location.reload();
                })
                .catch(error => {
                    console.error(error);
                    btn.disabled = false;
                });
            return;
        }

        if (id === "restart-applying-btn") {
            fetch("/api/internship-program/applying/restart", { method: "PATCH" })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response);
                    }
                    location.reload();
                })
                .catch(error => {
                    console.error(error);
                    btn.disabled = false;
                });
            return;
        }
    }
});

on($("#interning-btn-container"), "click", function (e) {
    const btn = e.target;
    if (btn.tagName === "BUTTON") {
        btn.disabled = true;
        const id = btn.id;

        if (id === "start-interning-btn") {
            fetch("/api/internship-program/interning/start", { method: "PATCH" })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response);
                    }
                    location.reload();
                })
                .catch(error => {
                    console.error(error);
                    btn.disabled = false;
                });
            return;
        }

        if (id === "undo-interning-btn") {
            fetch("/api/internship-program/interning/undo", { method: "PATCH" })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response);
                    }
                    location.reload();
                })
                .catch(error => {
                    console.error(error);
                    btn.disabled = false;
                });
            return;
        }

        if (id === "end-interning-btn") {
            fetch("/api/internship-program/interning/end", { method: "PATCH" })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response);
                    }
                    location.reload();
                })
                .catch(error => {
                    console.error(error);
                    btn.disabled = false;
                });
            return;
        }

        if (id === "restart-interning-btn") {
            fetch("/api/internship-program/interning/restart", { method: "PATCH" })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response);
                    }
                    location.reload();
                })
                .catch(error => {
                    console.error(error);
                    btn.disabled = false;
                });
            return;
        }
    }
});