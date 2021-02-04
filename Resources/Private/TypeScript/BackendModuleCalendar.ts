import $ = require('jquery');
import Icons = require('TYPO3/CMS/Backend/Icons');
import {Calendar} from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import deLocale from '@fullcalendar/core/locales/de';
import '../Scss/backendCalendar.scss';
import tippy from 'tippy.js';
import interactionPlugin from '@fullcalendar/interaction';
import 'tippy.js/dist/tippy.css';
import {BackendCalendarViewState} from "./BackendCalendarViewState";
import merge from "webpack-merge";

declare global {
  interface Window {
    TYPO3: any;
  }
}


/**
 * Module: TYPO3/CMS/BwBookingmanager/BackendModuleCalendar
 *
 * @exports TYPO3/CMS/BwBookingmanager/BackendModuleCalendar
 */
class BackendModuleCalendar {

  public calendar: Calendar;

  public viewState: BackendCalendarViewState;

  public init() {
    console.log('hello!');
    this.initCalendar();
    this.bindEvents();
    return this;
  }

  public bindEvents() {
    $('a[data-changeviewstate]').on('click', this.onViewStateChangeClick.bind(this));
  }

  /**
   * Filter button click event (e.g. hide past timeslots)
   * @param e
   */
  public onViewStateChangeClick(e) {
    const btn = $(e.currentTarget);
    // change active status of buttons
    btn.hasClass('active') ? btn.removeClass('active') : btn.addClass('active');
    // update view state (e.g. pastEntries to false)
    this.viewState[btn.attr('data-changeviewstate')] = btn.hasClass('active');
    // redraw events @TODO: reloading of events from source is not necessary - find method to trigger just repaint
    this.calendar.refetchEvents();
    // upload new view state to backend user settings
    this.viewState.saveAsUserView();
  }

  public onSelect(info) {

    this.viewState.selectedStart = info.start;
    this.viewState.selectedEnd = info.end;

    $('.fc-highlight')
      .addClass('t3js-contextmenutrigger')
      .attr('data-table', 'tx_bwbookingmanager_domain_model_calendar')
      .attr('data-context', 'calendar')
      .attr('onclick', '')
      .attr('data-uid', this.viewState.pid);
  }

  public initCalendar() {
    const calendarEl = document.getElementById('calendar');

    if (!calendarEl) {
      return;
    }

    this.viewState = new BackendCalendarViewState(calendarEl);

    Icons.getIcon('spinner-circle', Icons.sizes.default).done((spinner) => {
      const $spinner = $('<div>').attr('id', 'loading').html(spinner);
      $(calendarEl).after($spinner);

      let options = {
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
        plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
        navLinks: true,
        nowIndicator: true,
        dayMaxEvents: true,
        events: this.viewState.events,
        selectable: true,
        unselectAuto: true,
        select: this.onSelect.bind(this),
        eventClick: (arg) => {
          if (arg.event.url) {
            arg.jsEvent.preventDefault();
            top.TYPO3.Backend.ContentContainer.setUrl(arg.event.url);
          }
        },
        datesSet: () => {
          this.viewState.calendarView = this.calendar.view.type;
          this.viewState.start = this.calendar.currentData.currentDate.toISOString();
          this.viewState.saveAsUserView();
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
      };

      // override options from typoscript
      options = Object.assign(options, this.viewState.calendarOptions);

      this.calendar = new Calendar(calendarEl, options);

      this.calendar.render();
    });


  }
}

export = new BackendModuleCalendar().init();
