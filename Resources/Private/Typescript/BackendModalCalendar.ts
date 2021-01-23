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

  public addEvent() {
  }

  public setActiveEvent(event: EventApi) {
    if (this.selectedEvent) {
      this.calendar.getEventById(this.selectedEvent.id).setExtendedProp('isSelected', false);
    }
    event.setExtendedProp('isSelected', true);
    this.selectedEvent = event;
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
        //eventClick: this.onEventClick.bind(this),
        select: (info) => {

          let start = info.start;
          let end = info.end;
          let allDay = info.allDay;

          // adjust start and end time of selected days
          const calendar = this.viewState.getFirstDirectBookableCalendar();
          if (calendar && this.calendar.currentData.currentViewType === 'dayGridMonth') {
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

          // create new event if none
          if (!this.selectedEvent) {
            console.log('no selected event');
            const calendar = this.viewState.getFirstDirectBookableCalendar();
            this.selectedEvent = this.calendar.addEvent({
              editable: true,
              allDay: allDay,
              start: start,
              end: end,
              extendedProps: {
                isSelected: true,
                uid: this.viewState.events.extraParams.entryUid,
                calendar: calendar.uid
              }
            });
            return;
          }

          // move existing event
          this.selectedEvent.setAllDay(allDay);
          this.selectedEvent.setStart(start);
          this.selectedEvent.setEnd(end);
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
        eventWillUnmount: (info) => {
          if(this.selectedEvent && info.event.id === this.selectedEvent.id) {
            console.log('unset');
            //this.selectedEvent = null;
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

          // create new event in case of defValues or previous created event
          const isNewCreated = typeof this.viewState.entryUid === 'string' && this.viewState.entryUid.substr(0, 3) === 'NEW';
          if (this.viewState.hasDirectBookingCalendar && this.viewState.entryEnd && !this.selectedEvent && isNewCreated) {
            // create new event
            console.log('create new event');
            const calendar = this.viewState.getFirstDirectBookableCalendar();
            this.selectedEvent = this.calendar.addEvent({
              start: this.viewState.entryStart,
              end: this.viewState.entryEnd,
              editable: true,
              extendedProps: {
                isSelected: true,
                uid: this.viewState.events.extraParams.entryUid,
                calendar: calendar.uid
              }
            });
          }

          if (this.viewState.hasDirectBookingCalendar && this.selectedEvent) {
            console.log('has selected event');
          } else {
            console.log('no selected event');
          }

          setTimeout(() => {
            if (this.viewState.hasDirectBookingCalendar && this.selectedEvent && this.viewState.entryEnd) {
              const allDay = this.viewState.entryStart.substr(11) === this.viewState.entryEnd.substr(11);
              this.selectedEvent.setDates(this.viewState.entryStart, this.viewState.entryEnd, {allDay: allDay});
            }
          }, 1000);

        },
      });

      this.calendar.render();



    });


  }
}

export = BackendModalCalendar;
