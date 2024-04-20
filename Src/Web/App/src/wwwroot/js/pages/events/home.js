import { Calendar } from "@fullcalendar/core";
import interactionPlugin from "@fullcalendar/interaction";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import listPlugin from "@fullcalendar/list";
import { $ } from "../../core/dom";

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');


const calendar = new Calendar($("#calendar-view"), {
    plugins: [dayGridPlugin,timeGridPlugin, listPlugin,interactionPlugin, list],
    initialView: "dayGridMonth",
    selectable: true,
    //events: '/events.php'
    headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "dayGridMonth,timeGridWeek,listWeek"
    }
});

calendar.render();
