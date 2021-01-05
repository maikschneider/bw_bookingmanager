import $ = require('jquery');
import Modal = require('TYPO3/CMS/Backend/Modal');
import Icons = require('TYPO3/CMS/Backend/Icons');
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

    const viewState = JSON.parse(el.getAttribute('data-view-state'));

    this.pid = viewState.pid;
    this.events = {
      'url': TYPO3.settings.ajaxUrls['api_calendar_show'],
      'extraParams': {
        'pid': viewState.pid
      }
    };
    this.language = 'language' in viewState && viewState.language !== 'default' ? viewState.language : 'en';
    this.start = 'start' in viewState ? viewState.start : new Date();
    this.calendarView = 'calendarView' in viewState ? viewState.calendarView : 'dayGridMonth';
    this.pastEntries = 'pastEntries' in viewState && viewState.pastEntries === 'true';
    this.pastTimeslots = 'pastTimeslots' in viewState && viewState.pastTimeslots === 'true';
    this.notBookableTimeslots = 'notBookableTimeslots' in viewState && viewState.notBookableTimeslots === 'true';
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

  public doSaveViewState: boolean = true;

  private saveRequest: any;

  public init() {
    this.initCalendar();
    this.bindEvents();
  }

  public bindEvents() {
    $('a[data-changeviewstate]').on('click', this.onViewStateChangeClick.bind(this));
    $('#entry-date-select-button').on('click', this.onEntryDateSelectButtonClick.bind(this));
  }

  public onEntryDateSelectButtonClick(e) {
    e.preventDefault();

    const button = document.getElementById('entry-date-select-button');

    const html = $('<div />').attr('id', 'calendar').addClass('modalCalendar');

    this.viewState = new CalenderViewState(button);
    this.doSaveViewState = false;

    Modal.advanced({
      title: button.getAttribute('data-modal-title'),
      content: html,
      size: Modal.sizes.large,
      callback: (modal) => {
        const calendarEl = modal.find('#calendar').get(0);
        this.renderCalendar(calendarEl);
      },
      buttons: [
        {
          name: 'entry',
          text: 'Entries',
          icon: 'ext-bwbookingmanager-type-entry'
        }
      ]
    })
  }

  public onViewStateChangeClick(e) {
    const btn = $(e.currentTarget);
    btn.hasClass('active') ? btn.removeClass('active') : btn.addClass('active');
    this.viewState[btn.attr('data-changeviewstate')] = btn.hasClass('active');
    this.calendar.refetchEvents();
    this.saveViewState();
  }

  public saveViewState() {
    if (!this.doSaveViewState) {
      return;
    }
    if (this.saveRequest) {
      this.saveRequest.abort();
    }
    this.saveRequest = $.post(TYPO3.settings.ajaxUrls['api_user_setting'], {viewState: this.viewState});
  }

  public initCalendar() {
    const calendarEl = document.getElementById('calendar');

    if (!calendarEl) {
      return;
    }

    this.viewState = new CalenderViewState(calendarEl);

    this.renderCalendar(calendarEl)
  }

  public renderCalendar(calendarEl) {

    Icons.getIcon('spinner-circle', Icons.sizes.default).done((spinner) => {
      const $spinner = $('<div>').attr('id', 'loading').html(spinner);
      $(calendarEl).after($spinner);

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
          $(calendarEl).next().css('display', display);
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
            tippy(info.el, {content: info.event.extendedProps.tooltip, appendTo: $(calendarEl).closest('body').get(0)});
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
    });


  }
}

export = new BackendCalendar().init();
