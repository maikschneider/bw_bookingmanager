define(["require", "exports", "jquery", "TYPO3/CMS/Backend/Icons"], function (require, exports, $, Icons) {
    "use strict";
    /**
     * Module: TYPO3/CMS/BwBookingmanager/Calendar
     *
     * @exports TYPO3/CMS/BwBookingmanager/Calendar
     */
    var Calendar = /** @class */ (function () {
        function Calendar() {
        }
        Calendar.prototype.init = function () {
            this.cacheDom();
            this.bindEvents();
            this.bindListener();
            this.onLoad();
        };
        Calendar.prototype.cacheDom = function () {
            this.$calendarWrapper = $('.bookingmanager-show-calendar');
        };
        Calendar.prototype.bindEvents = function () {
        };
        Calendar.prototype.bindListener = function () {
        };
        Calendar.prototype.onLoad = function () {
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
            var urls = this.$calendarWrapper.attr('data-calendar-urls').split(',').map(function (item) {
                return item;
            });
            for (var i = 0; i < this.calendarUids.length; i++) {
                this.loadCalendar(urls[i], this.buildCalendarMarkup.bind(this));
            }
        };
        Calendar.prototype.loadCalendar = function (url, callback) {
            var _this = this;
            Icons.getIcon('spinner-circle', Icons.sizes.default, null, null, Icons.markupIdentifiers.inline).done(function (icon) {
                _this.$calendarWrapper.html(icon);
                $.get(url, callback.bind(_this), 'json');
            });
        };
        Calendar.prototype.buildCalendarMarkup = function (data) {
            console.log(data);
        };
        return Calendar;
    }());
    return new Calendar().init();
});
