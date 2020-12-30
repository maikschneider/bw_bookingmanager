import $ = require('jquery');
import {Calendar} from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import deLocale from '@fullcalendar/core/locales/de';
import '../Scss/backendCalendar.scss';
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';

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

  public saveViewState(pid) {
    $.post(TYPO3.settings.ajaxUrls['api_user_setting'], {
      viewState: {
        pid: pid,
        calendarView: this.calendar.view.type,
        start: this.calendar.currentData.currentDate.toISOString()
      }
    });
  }

  public renderCalendar() {
    const calendarEl = document.getElementById('calendar');

    // language
    const language = calendarEl.hasAttribute('data-language') && calendarEl.getAttribute('data-language') !== 'default' ? calendarEl.getAttribute('data-language') : 'en';

    // onload date
    const startDate = calendarEl.hasAttribute('data-start-date') && calendarEl.getAttribute('data-start-date') ? calendarEl.getAttribute('data-start-date') : new Date();

    // startView
    const startView = calendarEl.hasAttribute('data-start-view') && calendarEl.getAttribute('data-start-view') ? calendarEl.getAttribute('data-start-view') : 'dayGridMonth';

    // construct ajax url endpoints
    const events = JSON.parse(calendarEl.getAttribute('data-events'));
    events.url = TYPO3.settings.ajaxUrls['api_calendar_show'];

    const pid = events.extraParams.pid;

    this.calendar = new Calendar(calendarEl, {
      locales: [deLocale],
      initialDate: startDate,
      timeZone: 'Europe/Berlin',
      locale: language,
      initialView: startView,
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
      },
      loading: (isLoading) => {
        const display = isLoading ? 'grid' : 'none';
        const opacity = isLoading ? '0.5' : '1';
        $(calendarEl).css('opacity', opacity);
        $('#loading').css('display', display);
      },
      weekNumbers: true,
      plugins: [dayGridPlugin, timeGridPlugin, listPlugin],
      navLinks: true,
      nowIndicator: true,
      dayMaxEvents: true,
      events: events,
      datesSet: () => {
        this.saveViewState(pid);
      },
      eventDidMount: (info) => {
        if (!info.event.extendedProps.tooltip) {
          return;
        }
        const tooltip = tippy(info.el, {content: info.event.extendedProps.tooltip});
      }
    });

    this.calendar.render();
  }
}

export = new BackendCalendar().init();
