import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
//import listPlugin from "@fullcalendar/list";
import interactionPlugin from "@fullcalendar/interaction";
import { $ } from "../../core/dom";


const calendar = new Calendar($("#calendar-view"), {
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    initialView: "dayGridMonth",
    selectable: true,
    events: {
        url: "/techtalks/all",
        method: "GET",
        failure: function () {
            alert("Failed to fetch sessions from the server.");
        }
    },

    eventClick: function (info) {
        var sessionObj = info.event;
        var sessionId = sessionObj.id;
        
        if (sessionObj.title == "null") {
            let url = `${sessionId}/scheduletitle`;
            window.open(url);
        }



    },

    headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "dayGridMonth,timeGridWeek"
    }
});

calendar.render();