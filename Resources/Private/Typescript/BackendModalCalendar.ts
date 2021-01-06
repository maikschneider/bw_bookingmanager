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
  public futureEntries: boolean;
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
    this.futureEntries = 'futureEntries' in viewState && viewState.futureEntries === 'true';
  }

}


/**
 * Module: TYPO3/CMS/BwBookingmanager/BackendModalCalendar
 *
 * @exports TYPO3/CMS/BwBookingmanager/BackendModalCalendar
 */
class BackendModalCalendar {

  public calendar: Calendar;

  public viewState: CalenderViewState;

  public isModalView: boolean = false;

  public selectedEvent: EventApi;

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
    this.viewState.events.extraParams['entryUid'] = button.getAttribute('data-entry-uid');
    this.isModalView = true;

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

    // repaint newly selected event


    // close
    Modal.currentModal.trigger('modal-dismiss');
  }

  public onEventClick(info) {
    if (this.isModalView && info.event.extendedProps.model === 'Timeslot' && info.event.extendedProps.isBookable) {
      info.jsEvent.preventDefault();

      // adjust style for previous clicked event
      if (this.selectedEvent) {
        this.selectedEvent.setProp('color', 'green');
      }
      this.selectedEvent = info.event;
      this.selectedEvent.setProp('color', 'orange');

    }
  }

  public onViewStateChangeClick(e) {
    const btn = $(e.currentTarget);
    btn.hasClass('active') ? btn.removeClass('active') : btn.addClass('active');
    this.viewState[btn.attr('data-changeviewstate')] = btn.hasClass('active');
    this.calendar.refetchEvents();
    this.saveViewState();
  }

  public saveViewState() {
    if (this.isModalView) {
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
        eventClick: this.onEventClick.bind(this),
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

          // if (!this.viewState.futureEntries && info.event.extendedProps.model === 'Entry' && this.viewState.events.extraParams.entryUid === info.event.extendedProps.uid) {
          //   info.event.setProp('display', 'none');
          // }


        }
      });

      this.calendar.render();
    });


  }
}

export = new BackendModalCalendar().init();
