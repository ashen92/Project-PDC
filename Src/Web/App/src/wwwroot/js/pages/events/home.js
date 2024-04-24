import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import listPlugin from "@fullcalendar/list";
import interactionPlugin from "@fullcalendar/interaction";
import { $ } from "../../core/dom";

const calendar = new Calendar($("#calendar-view"), {
    plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
    initialView: "dayGridMonth",
    selectable: true,
    events: {
        url: "/events/all",
        method: "GET",
        failure: function () {
            alert("Failed to fetch events from the server.");
        }
    },
    // eventContent: function (arg) {
    //     return {
    //         html: "<b>" + arg.event.title + "</b><br>" + arg.event.extendedProps.location
    //     };
    //},
    headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "dayGridMonth,timeGridWeek,listWeek"
    }
});

calendar.render();