import {BackendCalendarViewState} from "TYPO3/CMS/BwBookingmanager/BackendCalendarViewState";
import {EventApi} from "@fullcalendar/core";

declare global {
  interface Window {
    TYPO3: any;
    BackendModalCalendar: any;
  }
}


/**
 * Module: TYPO3/CMS/BwBookingmanager/SelectTimeslotDatesElement
 *
 * @exports TYPO3/CMS/BwBookingmanager/SelectTimeslotDatesElement
 */
class SelectTimeslotDatesElement {

  public viewState: BackendCalendarViewState;

  public selectedEvent: EventApi;

  public constructor() {

    const button = document.getElementById('entry-date-select-button');

    $('#entry-date-select-button').on('click', this.onButtonClick.bind(this));

    parent.window.BackendModalCalendar.onSave = this.onModalSave.bind(this);

  }

  public onButtonClick(e) {
    e.preventDefault();
    const button = e.currentTarget;

    parent.window.BackendModalCalendar.viewState = this.viewState ? this.viewState : new BackendCalendarViewState(button);

    parent.window.BackendModalCalendar.openModal();
  }

  public onModalSave(event, viewState) {

    this.selectedEvent = event;
    this.viewState = viewState;

    // save to new form
    const entryUid = viewState.events.extraParams.entryUid;
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
    const start = Intl.DateTimeFormat(viewState.language, format).format(event.start);
    const end = Intl.DateTimeFormat(viewState.language, format).format(event.end);
    $('#savedStartDate').html(start);
    $('#savedEndDate').html(end);
  }

}

export = new SelectTimeslotDatesElement();
