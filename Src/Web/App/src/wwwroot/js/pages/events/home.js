import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
//import listPlugin from "@fullcalendar/list";
import interactionPlugin from "@fullcalendar/interaction";
import { $ } from "../../core/dom";

const calendar = new Calendar($("#calendar-view"), {
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin], //, listPlugin
    initialView: "dayGridMonth",
    selectable: true,
    events: {
        url: "/events/all",
        method: "GET",
        failure: function () {
            alert("Failed to fetch events from the server.");
        }
    },
    eventClick: function (info) {
        var eventObj = info.event;
        var eventId= eventObj.id;
    
        if (eventObj.url) {
            alert(
                "Clicked " + eventObj.title + ".\n" +
                "Will open " + eventObj.url + " in a new tab",
            );

            window.open(eventObj.url);

            info.jsEvent.preventDefault(); // prevents browser from following link in current tab.
        } else {
            alert("Clicked " + eventObj.title);
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
        right: "dayGridMonth,timeGridWeek" //,listWeek
    }
});

calendar.render();