import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import listPlugin from "@fullcalendar/list";
import interactionPlugin from "@fullcalendar/interaction";
import { $ } from "../../core/dom";


const calendar = new Calendar($("#calendar"), {
    plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
    initialView: "dayGridMonth",
    selectable: true,
    events: {
        url: "/techtalks/all",
        method: "GET",
        failure: function () {
            alert("Failed to fetch sessions from the server.");
        }
    },

    headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "dayGridMonth,timeGridWeek,listWeek"
    }
});

calendar.render();