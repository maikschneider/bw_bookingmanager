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
import interactionPlugin from '@fullcalendar/interaction';

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

  public openModal() {

    if (!this.viewState) {
      console.error('No viewState');
      return;
    }

    const html = $('<div />').attr('id', 'calendar').addClass('modalCalendar');

    // 1. create modal
    Modal.advanced({
      title: 'Title',
      content: html,
      size: Modal.sizes.large,
      callback: (modal) => {
        // 2. select newly created element
        const calendarEl = modal.find('#calendar').get(0);
        // 3. load and append loading icon
        Icons.getIcon('spinner-circle', Icons.sizes.default).done((spinner) => {
          const $spinner = $('<div>').attr('id', 'loading').html(spinner);
          $(calendarEl).after($spinner);
          // 4. render calendar
          this.renderCalendar(calendarEl);
        });
      },
      buttons: [
        {
          name: 'cancel',
          text: this.viewState.buttonCancelText,
          icon: 'actions-close',
          btnClass: 'btn-danger',
          trigger: () => {
            this.selectedEvent = null;
            Modal.currentModal.trigger('modal-dismiss');
          }
        },
        {
          name: 'save',
          text: this.viewState.buttonSaveText,
          icon: 'actions-document-save',
          btnClass: 'btn-primary',
          trigger: (e) => {
            e.preventDefault();
            if (!this.selectedEvent) {
              TYPO3.Modal.confirm('Warning', 'You may break the internet!', TYPO3.Severity.warning, [
                {
                  text: 'Break it',
                  active: true,
                  trigger: function () {
                    // break the net
                  }
                }, {
                  text: 'Abort!',
                  trigger: function () {
                    TYPO3.Modal.dismiss();
                  }
                }
              ]);
              return;
            }
            this.onSave(this.selectedEvent, this.viewState);
            this.selectedEvent = null;
            Modal.currentModal.trigger('modal-dismiss');
          }
        }
      ]
    })
  }

  public onSave(selectedEvent, viewState) {
  }

  public onEventClick(info) {

    console.info('onEventClick', info);

    const isBookableTimeslot = info.event.extendedProps.model === 'Timeslot' && info.event.extendedProps.isBookable;
    const isSavedEntry = info.event.extendedProps.model === 'Entry' && info.event.extendedProps.isSavedEntry;

    if (isBookableTimeslot || isSavedEntry) {
      info.jsEvent.preventDefault();

      this.setActiveEvent(info.event);
      this.viewState.entryStart = info.event.start;
      this.viewState.entryEnd = info.event.end;
    }
  }

  public addEvent() {
  }

  public setActiveEvent(event: EventApi) {
    if (this.selectedEvent) {
      this.selectedEvent.setExtendedProp('isSelected', false);
      this.calendar.getEventById(this.selectedEvent.id).setExtendedProp('isSelected', false);
    }
    event.setExtendedProp('isSelected', true);
    this.selectedEvent = event;
  }

  public renderCalendar(calendarEl) {

    if (!calendarEl) {
      return;
    }

    this.calendar = new Calendar(calendarEl, {
      locales: [deLocale],
      initialDate: this.viewState.entryStart ?? this.viewState.start,
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
      selectable: this.viewState.hasDirectBookingCalendar(),
      eventClick: this.onEventClick.bind(this),
      select: (info) => {

        console.group('Event selection');

        let start = info.start;
        let end = info.end;
        let allDay = info.allDay;

        // adjust start and end time of selected days for entry start time
        const calendar = this.viewState.getFirstDirectBookableCalendar();
        if (calendar && this.calendar.currentData.currentViewType === 'dayGridMonth') {
          console.info('Adjusting start/end time for entry creation in DayGridMonth');
          if (calendar.defaultEndTime) {
            start = new Date(start.getTime() + calendar.defaultStartTime * 1000);
            allDay = false;
          }
          if (calendar.defaultEndTime) {
            end = new Date(end.getTime() - 86400000 + calendar.defaultEndTime * 1000);
            allDay = false;
          }
        }

        // update viewState
        this.viewState.entryStart = start;
        this.viewState.entryEnd = end;

        console.log('checking selected event..', this.selectedEvent);

        // create new event if none
        if (!this.selectedEvent) {
          const calendar = this.viewState.getFirstDirectBookableCalendar();
          this.selectedEvent = this.calendar.addEvent({
            editable: true,
            allDay: allDay,
            start: start,
            end: end,
            id: 'virtualEvent',
            extendedProps: {
              isSelected: true,
              uid: this.viewState.events.extraParams.entryUid,
              calendar: calendar.uid
            }
          });
          console.info('no selected event exists, created new virtual', this.selectedEvent);
        }

        // move existing event
        if (this.selectedEvent) {
          this.selectedEvent.setAllDay(allDay);
          this.selectedEvent.setStart(start);
          this.selectedEvent.setEnd(end);
          console.info('moved existing selected event', this.selectedEvent);
        }

        this.calendar.unselect();

        console.groupEnd();
      },
      eventDrop: (info) => {
        this.viewState.entryStart = info.event.start;
        this.viewState.entryEnd = info.event.end;
      },
      eventResize: (info) => {
        this.viewState.entryStart = info.event.start;
        this.viewState.entryEnd = info.event.end;
      },
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

        // unhide all entries in direct booking calendar
        if (this.viewState.hasDirectBookingCalendar() && info.event.extendedProps.model === 'Entry') {
          info.event.setProp('display', 'auto');
        }

        // unhide current entry in direct booking calendar
        if (info.event.extendedProps.model === 'Entry' && info.event.extendedProps.isSavedEntry) {
            this.setActiveEvent(info.event);
        }

        // new entry with default values: mark timeslot
        if (!this.selectedEvent && info.event.extendedProps.model === 'Timeslot' && info.event.extendedProps.uid === this.viewState.timeslot && this.viewState.start === info.event.start.toISOString() && this.viewState.entryEnd === info.event.end.toISOString()) {
          this.setActiveEvent(info.event);
        }

        // hide timeslot of current Entry if its weight is 1
        if (info.event.extendedProps.model === 'Timeslot' && info.event.extendedProps.isSelectedEntryTimeslot && info.event.extendedProps.maxWeight === 1) {
          info.event.setProp('display', 'none');
        }

      },
      eventSourceSuccess: () => {

        // remove virtual event since it will come from api
        if (this.calendar.getEventById('virtualEvent')) {
          this.calendar.getEventById('virtualEvent').remove();
        }
        this.selectedEvent = null;

      },
    });

    this.calendar.render();


  }
}

export = BackendModalCalendar;
