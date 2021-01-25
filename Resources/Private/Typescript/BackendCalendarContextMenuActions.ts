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

  public static getReturnUrl(): string {
    return top.rawurlencode(top.list_frame.document.location.pathname + top.list_frame.document.location.search);
  }

  /**
   *
   * @param table
   * @param uid
   * @param model
   * @param defValsOverride
   * @param start
   * @param end
   */
  public static goToCreateForm(table, uid, model, defValsOverride, start, end) {

    start = start ? start : BackendCalendar.viewState.selectedStart.getTime() / 1000;
    end = end ? end : BackendCalendar.viewState.selectedEnd.getTime() / 1000;
    const returnUrl = BackendCalendarContextMenuActions.getReturnUrl();
    const url = top.TYPO3.settings.FormEngine.moduleUrl
      + '&edit[' + model + '][' + uid + ']=new'
      + '&defVals[' + model + '][start_date]=' + start
      + '&defVals[' + model + '][end_date]=' + end
      + defValsOverride
      + '&returnUrl=' + returnUrl;

    top.TYPO3.Backend.ContentContainer.setUrl(url);
  }

  /**
   * use calendarS (multiple possible)
   * @param table
   * @param uid
   */
  public static newBlockslot(table, uid) {
    const model = $(this).attr('data-model-name');
    const calendarUid = $(this).attr('data-def-calendar-uid');
    const defValsOverride = '&defVals[' + model + '][calendars]=tx_bwbookingmanager_domain_model_calendar_' + calendarUid;

    BackendCalendarContextMenuActions.goToCreateForm(table, uid, model, defValsOverride, null, null);
  }

  /**
   * use calendarS (multiple possible)
   * @param table
   * @param uid
   */
  public static newHoliday(table, uid) {
    const model = $(this).attr('data-model-name');
    const calendarUid = $(this).attr('data-def-calendar-uid');
    const defValsOverride = '&defVals[' + model + '][calendars]=tx_bwbookingmanager_domain_model_calendar_' + calendarUid;

    BackendCalendarContextMenuActions.goToCreateForm(table, uid, model, defValsOverride, null, null);
  }

  /**
   * @param table
   * @param uid
   */
  public static newEntry(table, uid) {
    const defaultStart = parseInt($(this).attr('data-def-calendar-start-time'));
    let defaultEnd = parseInt($(this).attr('data-def-calendar-end-time'));
    defaultEnd = defaultEnd ? -86400 + defaultEnd : 0;

    const start = BackendCalendar.viewState.selectedStart.getTime() / 1000 + defaultStart;
    const end = (BackendCalendar.viewState.selectedEnd.getTime() / 1000) + defaultEnd;

    const model = $(this).attr('data-model-name');
    const calendarUid = $(this).attr('data-def-calendar-uid');
    let defValsOverride = '&defVals[' + model + '][calendar]=' + calendarUid;
    defValsOverride += '&defVals[' + model + '][confirmed]=1';
    BackendCalendarContextMenuActions.goToCreateForm(table, uid, model, defValsOverride, start, end);
  }

  /**
   *
   * @param table
   * @param uid
   */
  public static newTimeslot(table, uid) {
    const model = $(this).attr('data-model-name');
    const calendarUid = $(this).attr('data-def-calendar-uid');
    const defValsOverride = '&defVals[' + model + '][calendar]=' + calendarUid;
    BackendCalendarContextMenuActions.goToCreateForm(table, uid, model, defValsOverride, null, null);
  }

}

export = BackendCalendarContextMenuActions;
