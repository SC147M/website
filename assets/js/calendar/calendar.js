import {Calendar} from '@fullcalendar/core';
import timeGridPlugin from '@fullcalendar/timegrid';
import dayGridPlugin from '@fullcalendar/daygrid';
import deLocale from '@fullcalendar/core/locales/de';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';

import '@fullcalendar/core/main.css';
import '@fullcalendar/daygrid/main.css';
import '@fullcalendar/timegrid/main.css';
import '@fullcalendar/list/main.css';

document.addEventListener('DOMContentLoaded', function () {
    let calendarEl = document.getElementById('calendar');
    let calendar = new Calendar(calendarEl, {

        eventClick: function (info) {
            $('#eventModalLabel').html(info.event.title);
            $('#user').html(info.event.extendedProps.user);
            $('#created_at').html(info.event.extendedProps.created_at);
            $('#participants').html(info.event.extendedProps.participants.join(', '));
            $('#tables').html(info.event.extendedProps.tables.join(', '));

            if (moment(info.event.start).format('D.MM.') === moment(info.event.end).format('D.MM.')) {
                $('#date').html(moment(info.event.start).format('D.MM. H:mm') + ' - ' + moment(info.event.end).format('H:mm') + ' Uhr');

            } else {
                $('#date').html(moment(info.event.start).format('D.MM. H:mm') + ' Uhr - ' + moment(info.event.end).format('D.MM. H:mm') + ' Uhr');
            }

            if (info.event.extendedProps.comment !== '') {
                $('#comment').html(info.event.extendedProps.comment);
                $('#comment-wrapper').show();
            } else {
                $('#comment-wrapper').hide();
            }

            $('#eventModal').modal()
        },

        eventDrop: function(info) {
           // alert(info.event.title + " was dropped on " + info.event.start.toISOString());

            if (!confirm("Are you sure about this change?")) {
                info.revert();
            }
        },

        height: "auto",
        editable: true,
        events: '/api/events',
        locale: deLocale,
        header: {center: 'listMonth,dayGridMonth,timeGridWeek'},
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin],
        defaultView: 'listMonth',
        slotEventOverlap: false,
        scrollTime: moment().format('H:mm'),
        nowIndicator: true,
        allDaySlot: false
    });

    calendar.render();
});

