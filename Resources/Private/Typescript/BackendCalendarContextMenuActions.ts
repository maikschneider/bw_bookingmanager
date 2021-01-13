import $ = require('jquery');
import BackendCalendar = require('TYPO3/CMS/BwBookingmanager/BackendModuleCalendar');

declare global {
  interface Window {
    TYPO3: any;
  }
}

/**
 * Module: TYPO3/CMS/BwBookingmanager/BackendCalendarContextMenuActions
 *
 * @exports TYPO3/CMS/BwBookingmanager/BackendCalendarContextMenuActions
 */
class BackendCalendarContextMenuActions {
  public static newEntry(table, uid) {
    console.log('!!');
  }

  public static getReturnUrl(): string {
    return top.rawurlencode(top.list_frame.document.location.pathname + top.list_frame.document.location.search);
  }

  /**
   * We use the pid as uid
   *
   * @param table
   * @param uid
   */
  public static newTimeslot(table, uid) {
    const model = $(this).attr('data-model-name');
    const calendarUid = $(this).attr('data-def-calendar-uid');
    let url = decodeURIComponent($(this).attr('data-action-url'));

    const start = BackendCalendar.viewState.selectedStart.getTime() / 1000;
    const end = BackendCalendar.viewState.selectedEnd.getTime() / 1000;

    top.TYPO3.Backend.ContentContainer.setUrl(
      top.TYPO3.settings.FormEngine.moduleUrl
      + '&edit[' + model + '][' + uid + ']=new'
      + '&defVals[' + model + '][start_date]=' + start
      + '&defVals[' + model + '][end_date]=' + end
      + '&defVals[' + model + '][calendar]=' + calendarUid
      + '&returnUrl=' + BackendCalendarContextMenuActions.getReturnUrl()
    );
  }

  public static newBlockslot(table, uid) {
    console.log('TESTTT!!');
  }

  public static newHoliday(table, uid) {
    console.log('TESTTT!!');
  }
}

export = BackendCalendarContextMenuActions;
