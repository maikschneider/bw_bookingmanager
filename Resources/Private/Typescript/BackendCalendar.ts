import $ = require('jquery');
import {Calendar} from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import deLocale from '@fullcalendar/core/locales/de';
import '../Scss/backendCalendar.scss';

declare global {
  interface Window {
    TYPO3: any;
  }
}


/**
 * Module: TYPO3/CMS/BwBookingmanager/BackendCalendar
 *
 * @exports TYPO3/CMS/BwBookingmanager/BackendCalendar
 */
class BackendCalendar {

  public init() {
    const calendarEl = document.getElementById('calendar');

    // construct ajax url endpoints
    const events = JSON.parse(calendarEl.getAttribute('data-events'));
    events.url = TYPO3.settings.ajaxUrls['api_calendar_show'];

    let calendar = new Calendar(calendarEl, {
      locales: [deLocale],
      timeZone: 'Europe/Berlin',
      locale: 'de',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
      },
      loading: (isLoading) => {
        const display = isLoading ? 'grid' : 'none';
        $('#loading').css('display', display);
      },
      weekNumbers: true,
      plugins: [dayGridPlugin, timeGridPlugin, listPlugin],
      navLinks: true,
      nowIndicator: true,
      dayMaxEvents: true,
      events: events
    });

    calendar.render();
  }
}

export = new BackendCalendar().init();
