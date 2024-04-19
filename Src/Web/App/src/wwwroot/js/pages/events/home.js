import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import { $ } from "../../core/dom";

const calendar = new Calendar($("#calendar-view"), {
    plugins: [dayGridPlugin],
    initialView: "dayGridMonth"
});


calendar.render();