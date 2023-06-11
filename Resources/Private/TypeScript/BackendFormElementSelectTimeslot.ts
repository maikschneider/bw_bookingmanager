import {BackendCalendarViewState} from "TYPO3/CMS/BwBookingmanager/BackendCalendarViewState";
import $ = require('jquery');

declare global {
  interface Window {
    TYPO3: any;
    BackendModalCalendar: any;
  }
}


/**
 * Module: TYPO3/CMS/BwBookingmanager/BackendFormElementSelectTimeslot
 *
 * @exports TYPO3/CMS/BwBookingmanager/BackendFormElementSelectTimeslot
 */
class BackendFormElementSelectTimeslot {

  public constructor() {

    const button = document.getElementById('entry-date-select-button');

    $('#entry-date-select-button').on('click', this.onButtonClick.bind(this));

    parent.window.BackendModalCalendar.onSave = this.onModalSave.bind(this);

  }

  public onButtonClick(e) {
    e.preventDefault();
    const button = e.currentTarget;

    parent.window.BackendModalCalendar.viewState = new BackendCalendarViewState(button);

    parent.window.BackendModalCalendar.openModal();
  }

  public onModalSave(event, viewState) {

    // update button json
    document.getElementById('entry-date-select-button').setAttribute('data-view-state', JSON.stringify(viewState));

    // save to new form
    const entryUid = viewState.entryUid;
    if (event.extendedProps.model === 'Timeslot') {
      $('input[name="data[tx_bwbookingmanager_domain_model_entry][' + entryUid + '][timeslot]"]').val(event.extendedProps.uid);
    }
    const start_date = new Date(event.start.getTime() + event.start.getTimezoneOffset() * 60000);
    const end_date = new Date(event.end.getTime() + event.end.getTimezoneOffset() * 60000);
    $('input[name="data[tx_bwbookingmanager_domain_model_entry][' + entryUid + '][start_date]"]').val(start_date.getTime() / 1000);
    $('input[name="data[tx_bwbookingmanager_domain_model_entry][' + entryUid + '][end_date]"]').val(end_date.getTime() / 1000);
    $('select[name="data[tx_bwbookingmanager_domain_model_entry][' + entryUid + '][calendar]"]').val(event.extendedProps.calendar);

    // update date label
    const format: Intl.DateTimeFormatOptions = {
      weekday: "short",
      month: '2-digit',
      day: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      timeZone: 'Europe/Berlin'
    };
    const start = Intl.DateTimeFormat(viewState.language, format).format(start_date);
    const end = Intl.DateTimeFormat(viewState.language, format).format(end_date);
    $('#savedStartDate').html(start);
    $('#savedEndDate').html(end);
  }

}

export = new BackendFormElementSelectTimeslot();
