/**
*
*
*/
define(["require", "exports", "jquery", "jquery-ui/draggable", "jquery-ui/resizable"], function (require, exports, $) {
    "use strict";
    /**
    * Module: TYPO3/CMS/BwBookingManager/TimeslotDatesSelect
    * @exports TYPO3/CMS/BwBookingManager/TimeslotDatesSelect
    */
    var TimeslotDatesSelect = /** @class */ (function () {
        function TimeslotDatesSelect() {
        }
        /**
         * @method init
         * @desc Initilizes the timeslot dates select button element
         * @private
         */
        TimeslotDatesSelect.prototype.init = function () {
        };
        TimeslotDatesSelect.prototype.show = function () {
            alert('lets do it!');
        };
        TimeslotDatesSelect.prototype.initializeTrigger = function () {
            var _this = this;
            var triggerHandler = function (e) {
                e.preventDefault();
                _this.trigger = $(e.currentTarget);
                _this.show();
            };
            $('.t3js-timeslotdatesselect-trigger').off('click').click(triggerHandler);
        };
        return TimeslotDatesSelect;
    }());
    return new TimeslotDatesSelect();
});
