class BackendCalendar {
  uid: number;
  directBooking: boolean;
  defaultStartTime: any;
  defaultEndTime: any;
  minLength: number;
  minOffset: number;
}

export class BackendCalendarViewState {
  public pid: number;
  public calendarView: string;
  public start: any;
  public pastTimeslots: boolean;
  public notBookableTimeslots: boolean;
  public pastEntries: boolean;
  public futureEntries: boolean;
  public language: string;
  public events: any;
  public timeslot: number;
  public calendar: number;
  public entryUid: number;
  public end: any;
  public selectedStart: any;
  public selectedEnd: any;
  public currentCalendars: BackendCalendar[];
  public buttonSaveText: string;
  public buttonCancelText: string;

  private saveRequest: any;

  public constructor(el: HTMLElement) {

    if (!el.hasAttribute('data-view-state')) {
      console.error('Element does not have view-state attribute!');
      return;
    }

    const viewState = JSON.parse(el.getAttribute('data-view-state'));

    this.pid = viewState.pid;

    // @TODO: most properties are in parsed json for sure, we could extend
    this.language = 'language' in viewState && viewState.language !== 'default' ? viewState.language : 'en';
    this.start = 'start' in viewState ? viewState.start : new Date();
    this.calendarView = viewState.calendarView;
    this.pastEntries = viewState.pastEntries;
    this.pastTimeslots = viewState.pastTimeslots;
    this.notBookableTimeslots = viewState.notBookableTimeslots;

    // stuff needed in modal
    this.futureEntries = 'futureEntries' in viewState && viewState.futureEntries === 'true';
    this.end = 'end' in viewState ? viewState.end : null;
    this.entryUid = 'entryUid' in viewState ? viewState.entryUid : null;
    this.calendar = 'calendar' in viewState ? viewState.calendar : null;
    this.timeslot = 'timeslot' in viewState ? viewState.timeslot : null;
    this.buttonSaveText = 'buttonSaveText' in viewState ? viewState.buttonSaveText : '';
    this.buttonCancelText = 'buttonCancelText' in viewState ? viewState.buttonCancelText : '';

    // stuff needed for direct booking
    this.currentCalendars = viewState.currentCalendars;

    this.events = {
      'url': TYPO3.settings.ajaxUrls['api_calendar_show'],
      'extraParams': {
        'pid': viewState.pid
      }
    };

    if (this.entryUid) {
      this.events.extraParams['entryUid'] = this.entryUid;
    }

  }

  /**
   * Used in BackendModuleCalendar to persist the current display of view type and selected date
   */
  public saveAsUserView() {
    if (this.saveRequest) {
      this.saveRequest.abort();
    }
    this.saveRequest = $.post(TYPO3.settings.ajaxUrls['api_user_setting'], {
      viewState: {
        pid: this.pid,
        start: this.start,
        calendarView: this.calendarView,
        pastEntries: this.pastEntries,
        pastTimeslots: this.pastTimeslots,
        notBookableTimeslots: this.notBookableTimeslots,
        futureEntries: this.futureEntries
      }
    });
  }

}
