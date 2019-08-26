define(["require", "exports"], function (require, exports) {
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
            console.log('init');
        };
        return Calendar;
    }());
    return new Calendar().init();
});
