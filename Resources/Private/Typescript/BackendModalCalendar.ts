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

    Modal.advanced({
      title: 'Title',
      content: html,
      size: Modal.sizes.large,
      callback: (modal) => {
        setTimeout(() => {
          const calendarEl = modal.find('#calendar').get(0);
          this.renderCalendar(calendarEl);
        }, 500);
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
            console.log(this.selectedEvent);
            this.onSave(this.selectedEvent, this.viewState);
            Modal.currentModal.trigger('modal-dismiss');
          }
        }
      ]
    })
  }

  public onSave(selectedEvent, viewState) {
  }

  public hasDirectBookingCalendar() {
    return this.getFirstDirectBookableCalendar() !== null;
  }

  public getFirstDirectBookableCalendar() {
    for (let i = 0; i < this.viewState.currentCalendars.length; i++) {
      if (this.viewState.currentCalendars[i].directBooking) {
        return this.viewState.currentCalendars[i];
      }
    }
    return null;
  }

  public onEventClick(info) {
    const isBookableTimeslot = info.event.extendedProps.model === 'Timeslot' && info.event.extendedProps.isBookable;
    const isSavedEntry = info.event.extendedProps.model === 'Entry' && info.event.extendedProps.isSavedEntry;

    if (isBookableTimeslot || isSavedEntry) {
      info.jsEvent.preventDefault();

      // adjust style for previous clicked event
      if (this.calendar.getEvents().length) {
        const events = this.calendar.getEvents();
        for (let i = 0; i < events.length; i++) {
          events[i].setExtendedProp('isSelected', false);
        }
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
        plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
        editable: true,
        navLinks: true,
        nowIndicator: true,
        dayMaxEvents: true,
        events: this.viewState.events,
        selectable: true,
        eventClick: this.onEventClick.bind(this),
        select: (info) => {
          console.log(info);
          if (this.selectedEvent) {
            this.selectedEvent.setAllDay(info.allDay);
            this.selectedEvent.setStart(info.start);
            this.selectedEvent.setEnd(info.end);
          } else {
            this.selectedEvent = this.calendar.addEvent({
              start: info.start,
              end: info.end,
              title: 'new event',
              extendedProps: {
                isSelected: true
              }
            })
          }
        },
        eventDrop: (info) => {
          // update selected event after drop
          if (info.event.extendedProps.uniqueId === this.selectedEvent.extendedProps.uniqueId) {
            this.selectedEvent = info.event;
          }
        },
        eventResize: (info) => {
          // update selected event after resize
          if (info.event.extendedProps.uniqueId === this.selectedEvent.extendedProps.uniqueId) {
            this.selectedEvent = info.event;
          }
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
            //info.event.setStart(this.selectedEvent.start);
            //info.event.setEnd(this.selectedEvent.end);
          }

          // new entry with default values: mark timeslot
          if (!this.selectedEvent && info.event.extendedProps.model === 'Timeslot' && info.event.extendedProps.uid === this.viewState.timeslot && this.viewState.start === info.event.start.toISOString() && this.viewState.end === info.event.end.toISOString()) {
            this.selectedEvent = info.event;
          }

          // hide timeslot of current Entry if its weight is 1
          if (info.event.extendedProps.model === 'Timeslot' && info.event.extendedProps.isSelectedEntryTimeslot && info.event.extendedProps.maxWeight === 1) {
            info.event.setProp('display', 'none');
          }

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

export = BackendModalCalendar;
