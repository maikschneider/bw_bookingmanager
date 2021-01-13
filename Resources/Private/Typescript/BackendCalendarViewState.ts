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

  private saveRequest: any;

  public constructor(el: HTMLElement) {

    if (!el.hasAttribute('data-view-state')) {
      console.error('Element does not have view-state attribute!');
      return;
    }

    const viewState = JSON.parse(el.getAttribute('data-view-state'));

    this.pid = viewState.pid;

    this.language = 'language' in viewState && viewState.language !== 'default' ? viewState.language : 'en';
    this.start = 'start' in viewState ? viewState.start : new Date();
    this.calendarView = 'calendarView' in viewState ? viewState.calendarView : 'dayGridMonth';
    this.pastEntries = 'pastEntries' in viewState && viewState.pastEntries === 'true';
    this.pastTimeslots = 'pastTimeslots' in viewState && viewState.pastTimeslots === 'true';
    this.notBookableTimeslots = 'notBookableTimeslots' in viewState && viewState.notBookableTimeslots === 'true';

    // stuff needed in modal
    this.futureEntries = 'futureEntries' in viewState && viewState.futureEntries === 'true';
    this.end = 'end' in viewState ? viewState.end : null;
    this.entryUid = 'entryUid' in viewState ? viewState.entryUid : null;
    this.calendar = 'calendar' in viewState ? viewState.calendar : null;
    this.timeslot = 'timeslot' in viewState ? viewState.timeslot : null;

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
