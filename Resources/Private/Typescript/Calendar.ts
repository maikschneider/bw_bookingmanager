import $ = require('jquery');
import Icons = require('TYPO3/CMS/Backend/Icons');


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
    this.$calendarWrapper = $('.bookingmanager-show-calendar');
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
    const urls = this.$calendarWrapper.attr('data-calendar-urls').split(',').map(function (item) {
      return item;
    });

    for (let i = 0; i < this.calendarUids.length; i++) {
      this.loadCalendar(urls[i], this.buildCalendarMarkup.bind(this));
    }

  }

  private loadCalendar(url, callback)
  {
    Icons.getIcon('spinner-circle', Icons.sizes.default, null, null, Icons.markupIdentifiers.inline).done((icon: string): void => {
      this.$calendarWrapper.html(icon);
      $.get(
        url,
        callback.bind(this),
        'json'
      );
    });
  }

  private buildCalendarMarkup(data)
  {
    console.log(data);
  }
}

export = new Calendar().init();
