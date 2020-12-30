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

  public calendar: Calendar;

  public init() {
    this.renderCalendar();
    this.bindEvents();
  }

  public bindEvents() {
  }

  public renderCalendar() {
    const calendarEl = document.getElementById('calendar');

    // language
    const language = calendarEl.hasAttribute('data-language') && calendarEl.getAttribute('data-language') !== 'default' ? calendarEl.getAttribute('data-language') : 'en';

    // onload date
    const startDate = calendarEl.hasAttribute('data-start-date') && calendarEl.getAttribute('data-start-date') ? calendarEl.getAttribute('data-start-date') : new Date();

    // construct ajax url endpoints
    const events = JSON.parse(calendarEl.getAttribute('data-events'));
    events.url = TYPO3.settings.ajaxUrls['api_calendar_show'];

    this.calendar = new Calendar(calendarEl, {
      locales: [deLocale],
      initialDate: startDate,
      timeZone: 'Europe/Berlin',
      locale: language,
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

    this.calendar.render();
  }
}

export = new BackendCalendar().init();
