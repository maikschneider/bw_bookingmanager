
import $ = require('jquery');


declare global {
  interface Window {
    TYPO3: any;
  }
}


/**
 * Module: TYPO3/CMS/BwBookingmanager/Calendar
 *
 * @exports TYPO3/CMS/BwBookingmanager/Calendar
 */
class Calendar {
  private $calendarWrapper: JQuery;
  private calendarUids: number[];
  private feUser: number;

  public init()
  {
    this.cacheDom();
    this.bindEvents();
    this.bindListener();
    this.onLoad();
  }

  private cacheDom() {

  }

  private bindEvents() {

  }

  private bindListener() {

  }

  private onLoad() {

    // check for calendar wrapper
    if (!this.$calendarWrapper.length) {
      return;
    }

    // parse calendar uids
    this.calendarUids = this.$calendarWrapper.attr('data-calendar-uids').split(',').map(function (item) {
      return parseInt(item);
    });

    // parese feUser
    if (this.$calendarWrapper.attr('data-fe-user') && parseInt(this.$calendarWrapper.attr('data-fe-user')) > 0) {
      this.feUser = parseInt(this.$calendarWrapper.attr('data-fe-user'));
    }

    // start building the calendar
    for (let i = 0; i < this.calendarUids.length; i++) {
      const url = '/api/calendar/' + this.calendarUids[i] + '.json';
      //$.BookingManager.loadCalendar(this.calendarUids[i], this._buildCalendarMarkup.bind(this, url));
    }

  }
}

export = new Calendar().init();
