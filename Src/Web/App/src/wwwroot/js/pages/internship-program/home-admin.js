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

on($("#js-r1-btn-container"), "click", function (e) {
    const btn = e.target;
    if (btn.tagName === "BUTTON") {
        btn.disabled = true;
        const id = btn.id;

        if (id === "start-js-r1-btn") {
            fetch("/api/internship-program/job-hunt/round/1/start", { method: "PATCH" })
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

        if (id === "end-js-r1-btn") {
            fetch("/api/internship-program/job-hunt/round/1/end", { method: "PATCH" })
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

        if (id === "restart-js-r1-btn") {
            fetch("/api/internship-program/job-hunt/round/1/restart", { method: "PATCH" })
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

on($("#js-r2-btn-container"), "click", function (e) {
    const btn = e.target;
    if (btn.tagName === "BUTTON") {
        btn.disabled = true;
        const id = btn.id;

        if (id === "start-js-r2-btn") {
            fetch("/api/internship-program/job-hunt/round/2/start", { method: "PATCH" })
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

        if (id === "end-js-r2-btn") {
            fetch("/api/internship-program/job-hunt/round/2/end", { method: "PATCH" })
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

        if (id === "restart-js-r2-btn") {
            fetch("/api/internship-program/job-hunt/round/2/restart", { method: "PATCH" })
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