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

class CalenderViewState {
  public pid: number;
  public calendarView: string;
  public start: any;
  public pastTimeslots: boolean;
  public notBookableTimeslots: boolean;
  public pastEntries: boolean;
  public language: string;
  public events: any;

  public constructor(el: HTMLElement) {
    this.language = el.hasAttribute('data-language') && el.getAttribute('data-language') !== 'default' ? el.getAttribute('data-language') : 'en';

    this.start = el.hasAttribute('data-start-date') && el.getAttribute('data-start-date') ? el.getAttribute('data-start-date') : new Date();

    this.calendarView = el.hasAttribute('data-start-view') && el.getAttribute('data-start-view') ? el.getAttribute('data-start-view') : 'dayGridMonth';

    this.events = JSON.parse(el.getAttribute('data-events'));
    this.events.url = TYPO3.settings.ajaxUrls['api_calendar_show'];

    this.pid = this.events.extraParams.pid;

    this.pastEntries = el.hasAttribute('data-past-entries') && el.getAttribute('data-past-entries') === 'true';
    this.pastTimeslots = el.hasAttribute('data-past-timeslots') && el.getAttribute('data-past-timeslots') === 'true';
    this.notBookableTimeslots = el.hasAttribute('data-not-bookable-timeslots') && el.getAttribute('data-not-bookable-timeslots') === 'true';
  }

}


/**
 * Module: TYPO3/CMS/BwBookingmanager/BackendCalendar
 *
 * @exports TYPO3/CMS/BwBookingmanager/BackendCalendar
 */
class BackendCalendar {

  public calendar: Calendar;

  public viewState: CalenderViewState;

  private saveRequest: any;

  public init() {
    this.renderCalendar();
    this.bindEvents();
  }

  public bindEvents() {
    $('a[data-changeviewstate]').on('click', this.onViewStateChangeClick.bind(this));
  }

  public onViewStateChangeClick(e) {
    const btn = $(e.currentTarget);
    btn.hasClass('active') ? btn.removeClass('active') : btn.addClass('active');
    this.viewState[btn.attr('data-changeviewstate')] = btn.hasClass('active');
    this.calendar.refetchEvents();
    this.saveViewState();
  }

  public saveViewState() {
    if (this.saveRequest) {
      this.saveRequest.abort();
    }
    this.saveRequest = $.post(TYPO3.settings.ajaxUrls['api_user_setting'], {viewState: this.viewState});
    console.log({viewState: this.viewState});
  }

  public renderCalendar() {
    const calendarEl = document.getElementById('calendar');

    if (!calendarEl) {
      return;
    }

    this.viewState = new CalenderViewState(calendarEl);

    this.calendar = new Calendar(calendarEl, {
      locales: [deLocale],
      initialDate: this.viewState.start,
      timeZone: 'Europe/Berlin',
      locale: this.viewState.language,
      initialView: this.viewState.calendarView,
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
      events: this.viewState.events,
      datesSet: () => {
        this.viewState.calendarView = this.calendar.view.type;
        this.viewState.start = this.calendar.currentData.currentDate.toISOString();
        this.saveViewState();
      },
      eventDidMount: (info) => {
        if (info.event.extendedProps.tooltip) {
          tippy(info.el, {content: info.event.extendedProps.tooltip});
        }

        if (!this.viewState.pastTimeslots && info.event.extendedProps.model === 'Timeslot' && info.event.extendedProps.isInPast) {
          info.event.setProp('display', 'none');
        }

        if (!this.viewState.pastEntries && info.event.extendedProps.model === 'Entry' && info.event.extendedProps.isInPast) {
          info.event.setProp('display', 'none');
        }

        if (!this.viewState.notBookableTimeslots && info.event.extendedProps.model === 'Timeslot' && !info.event.extendedProps.isInPast && !info.event.extendedProps.isBookable) {
          info.event.setProp('display', 'none');
        }
      }
    });

    this.calendar.render();
  }
}

export = new BackendCalendar().init();
