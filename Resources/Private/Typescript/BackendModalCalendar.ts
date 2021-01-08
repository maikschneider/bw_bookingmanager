import $ = require('jquery');
import Modal = require('TYPO3/CMS/Backend/Modal');
import Icons = require('TYPO3/CMS/Backend/Icons');
import {Calendar, EventApi} from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import deLocale from '@fullcalendar/core/locales/de';
import '../Scss/backendCalendar.scss';
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';
import {BackendCalendarViewState} from "./BackendCalendarViewState";

declare global {
  interface Window {
    TYPO3: any;
  }
}


/**
 * Module: TYPO3/CMS/BwBookingmanager/BackendModalCalendar
 *
 * @exports TYPO3/CMS/BwBookingmanager/BackendModalCalendar
 */
class BackendModalCalendar {

  public calendar: Calendar;

  public viewState: BackendCalendarViewState;

  public selectedEvent: EventApi;

  private saveRequest: any;

  public init() {
    this.initViewState();
    this.bindEvents();
  }

  public initViewState() {
    const button = document.getElementById('entry-date-select-button');

    this.viewState = new BackendCalendarViewState(button);

    console.log(this.viewState);
  }

  public bindEvents() {
    $('#entry-date-select-button').on('click', this.onEntryDateSelectButtonClick.bind(this));
  }

  public onEntryDateSelectButtonClick(e) {
    e.preventDefault();

    const html = $('<div />').attr('id', 'calendar').addClass('modalCalendar');
    const button = document.getElementById('entry-date-select-button');
    const buttonCancelText = button.getAttribute('data-modal-cancel-button-text');
    const buttonSaveText = button.getAttribute('data-modal-save-button-text');

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
          name: 'cancel',
          text: buttonCancelText,
          icon: 'actions-close',
          btnClass: 'btn-danger',
          trigger: () => {
            this.selectedEvent = null;
            Modal.currentModal.trigger('modal-dismiss')
          }
        },
        {
          name: 'save',
          text: buttonSaveText,
          icon: 'actions-document-save',
          btnClass: 'btn-primary',
          trigger: this.onModalSaveClick.bind(this)
        }
      ]
    })
  }

  public onModalSaveClick(e) {
    e.preventDefault();

    const event = this.selectedEvent;

    // save to new form
    const entryUid = this.viewState.events.extraParams.entryUid;
    $('input[name="data[tx_bwbookingmanager_domain_model_entry][' + entryUid + '][timeslot]"]').val(event.extendedProps.uid);
    $('input[name="data[tx_bwbookingmanager_domain_model_entry][' + entryUid + '][start_date]"]').val(event.start.getTime() / 1000);
    $('input[name="data[tx_bwbookingmanager_domain_model_entry][' + entryUid + '][end_date]"]').val(event.end.getTime() / 1000);
    $('select[name="data[tx_bwbookingmanager_domain_model_entry][' + entryUid + '][calendar]"]').val(event.extendedProps.calendar);

    // update date label
    const format = {
      weekday: 'short',
      month: '2-digit',
      day: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      timeZone: 'UTC'
    };
    const start = Intl.DateTimeFormat(this.viewState.language, format).format(event.start);
    const end = Intl.DateTimeFormat(this.viewState.language, format).format(event.end);
    $('#savedStartDate').html(start);
    $('#savedEndDate').html(end);

    // close
    Modal.currentModal.trigger('modal-dismiss');
  }

  public onEventClick(info) {

    const isBookableTimeslot = info.event.extendedProps.model === 'Timeslot' && info.event.extendedProps.isBookable;
    const isSavedEntry = info.event.extendedProps.model === 'Entry' && info.event.extendedProps.isSavedEntry;

    if (isBookableTimeslot || isSavedEntry) {
      info.jsEvent.preventDefault();

      // adjust style for previous clicked event
      if (this.selectedEvent) {
        this.selectedEvent.setExtendedProp('isSelected', false);
      }
      this.selectedEvent = info.event;
      this.selectedEvent.setExtendedProp('isSelected', true);

    }
  }

  public renderCalendar(calendarEl) {

    if (!calendarEl) {
      return;
    }

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
        eventClick: this.onEventClick.bind(this),
        eventClassNames: (arg) => {
          let classNames = arg.event.classNames.slice();
          if (arg.event.extendedProps.isSelected) {
            classNames.push('active');
          }
          if (arg.event.extendedProps.model === 'Entry' && arg.event.extendedProps.isSavedEntry && !arg.event.extendedProps.isSelected) {
            classNames.push('removed');
          }
          return classNames;
        },
        datesSet: () => {
          this.viewState.calendarView = this.calendar.view.type;
          this.viewState.start = this.calendar.currentData.currentDate.toISOString();

          // mark selected element for display
          if (this.selectedEvent) {
            this.selectedEvent.setExtendedProp('isSelected', true);
          }
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

          if (!this.viewState.futureEntries && info.event.extendedProps.model === 'Entry' && !info.event.extendedProps.isInPast) {
            info.event.setProp('display', 'none');
          }

          // unhide current entry
          if (info.event.extendedProps.model === 'Entry' && this.viewState.entryUid === info.event.extendedProps.uid) {
            info.event.setProp('display', 'auto');
            if (!this.selectedEvent) {
              this.selectedEvent = info.event;
            }
          }

          // hide timeslot of current Entry if its weight is 1
          if (info.event.extendedProps.model === 'Timeslot' && info.event.extendedProps.isSelectedEntryTimeslot && info.event.extendedProps.maxWeight === 1) {
            info.event.setProp('display', 'none');
          }

          console.log(this.selectedEvent);

          // @TODO: execute just once after rendering
          if (this.selectedEvent && this.selectedEvent.extendedProps.uniqueId === info.event.extendedProps.uniqueId) {
            info.event.setExtendedProp('isSelected', true);
          }

        }
      });

      this.calendar.render();
    });


  }
}

export = new BackendModalCalendar().init();
