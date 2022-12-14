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
  public entryUid: number|string;
  public selectedStart: any;
  public selectedEnd: any;
  public currentCalendars: BackendCalendar[];
  public buttonSaveText: string;
  public buttonCancelText: string;
  public entryStart: any;
  public entryEnd: any;
  public warningTitle: string;
  public warningText: string;
  public warningButton: string;
  public calendarOptions: any;

  private saveRequest: any;

  public constructor(el: HTMLElement) {

    if (!el.hasAttribute('data-view-state')) {
      console.error('Element does not have view-state attribute!');
      return;
    }

    const viewState = JSON.parse(el.getAttribute('data-view-state'));

    // @TODO: most properties are in parsed json for sure, we could extend
    this.pid = viewState.pid;
    this.language = 'language' in viewState && viewState.language !== 'default' ? viewState.language : 'en';
    this.start = 'start' in viewState ? viewState.start : new Date();
    this.calendarView = viewState.calendarView;
    this.pastEntries = viewState.pastEntries;
    this.pastTimeslots = viewState.pastTimeslots;
    this.notBookableTimeslots = viewState.notBookableTimeslots;
    this.futureEntries = 'futureEntries' in viewState && viewState.futureEntries === 'true';
    this.entryUid = 'entryUid' in viewState ? viewState.entryUid : null;
    this.calendar = 'calendar' in viewState ? viewState.calendar : null;
    this.timeslot = 'timeslot' in viewState ? viewState.timeslot : null;
    this.buttonSaveText = 'buttonSaveText' in viewState ? viewState.buttonSaveText : '';
    this.buttonCancelText = 'buttonCancelText' in viewState ? viewState.buttonCancelText : '';
    this.entryStart = 'entryStart' in viewState ? viewState.entryStart : null;
    this.entryEnd = 'entryEnd' in viewState ? viewState.entryEnd : null;
    this.currentCalendars = viewState.currentCalendars;
    this.warningTitle = 'warningTitle' in viewState ? viewState.warningTitle : '';
    this.warningText = 'warningText' in viewState ? viewState.warningText : '';
    this.warningButton = 'warningButton' in viewState ? viewState.warningButton : '';
    this.calendarOptions = 'calendarOptions' in viewState ? viewState.calendarOptions : {};

    this.events = {
      'url': TYPO3.settings.ajaxUrls['api_calendar_show'],
      'extraParams': () => {
        const entryStart = this.entryStart ? (new Date(this.entryStart)).getTime() / 1000 : null;
        const entryEnd = this.entryEnd ? (new Date(this.entryEnd)).getTime() / 1000 : null;
        return {
          'pid': this.pid,
          'entryUid': this.entryUid,
          'entryStart': entryStart,
          'entryEnd': entryEnd,
          'calendar': this.calendar,
          'timeslot': this.timeslot
        };
      }
    };

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

  public hasDirectBookingCalendar() {
    return this.getFirstDirectBookableCalendar() !== null;
  }

  public getFirstDirectBookableCalendar() {
    for (let i = 0; i < this.currentCalendars.length; i++) {
      if (this.currentCalendars[i].directBooking) {
        return this.currentCalendars[i];
      }
    }
    return null;
  }

}
