define("TYPO3/CMS/BwBookingmanager/BackendFormElementSelectTimeslot", ["jquery"], (__WEBPACK_EXTERNAL_MODULE_jquery__) => /******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./Resources/Private/TypeScript/BackendCalendarViewState.ts":
/*!******************************************************************!*
  !*** ./Resources/Private/TypeScript/BackendCalendarViewState.ts ***!
  \******************************************************************/
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;!(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__, exports], __WEBPACK_AMD_DEFINE_RESULT__ = (function (require, exports) {
    "use strict";
    Object.defineProperty(exports, "__esModule", ({ value: true }));
    class BackendCalendar {
    }
    class BackendCalendarViewState {
        constructor(el) {
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
        saveAsUserView() {
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
        hasDirectBookingCalendar() {
            return this.getFirstDirectBookableCalendar() !== null;
        }
        getFirstDirectBookableCalendar() {
            for (let i = 0; i < this.currentCalendars.length; i++) {
                if (this.currentCalendars[i].directBooking) {
                    return this.currentCalendars[i];
                }
            }
            return null;
        }
    }
    exports.BackendCalendarViewState = BackendCalendarViewState;
}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));


/***/ }),

/***/ "./Resources/Private/TypeScript/BackendFormElementSelectTimeslot.ts":
/*!**************************************************************************!*
  !*** ./Resources/Private/TypeScript/BackendFormElementSelectTimeslot.ts ***!
  \**************************************************************************/
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;!(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__, exports, __webpack_require__(/*! TYPO3/CMS/BwBookingmanager/BackendCalendarViewState */ "./Resources/Private/TypeScript/BackendCalendarViewState.ts"), __webpack_require__(/*! jquery */ "jquery")], __WEBPACK_AMD_DEFINE_RESULT__ = (function (require, exports, BackendCalendarViewState_1, $) {
    "use strict";
    /**
     * Module: TYPO3/CMS/BwBookingmanager/BackendFormElementSelectTimeslot
     *
     * @exports TYPO3/CMS/BwBookingmanager/BackendFormElementSelectTimeslot
     */
    class BackendFormElementSelectTimeslot {
        constructor() {
            const button = document.getElementById('entry-date-select-button');
            $('#entry-date-select-button').on('click', this.onButtonClick.bind(this));
            parent.window.BackendModalCalendar.onSave = this.onModalSave.bind(this);
        }
        onButtonClick(e) {
            e.preventDefault();
            const button = e.currentTarget;
            parent.window.BackendModalCalendar.viewState = new BackendCalendarViewState_1.BackendCalendarViewState(button);
            parent.window.BackendModalCalendar.openModal();
        }
        onModalSave(event, viewState) {
            // update button json
            document.getElementById('entry-date-select-button').setAttribute('data-view-state', JSON.stringify(viewState));
            // save to new form
            const entryUid = viewState.entryUid;
            if (event.extendedProps.model === 'Timeslot') {
                $('input[name="data[tx_bwbookingmanager_domain_model_entry][' + entryUid + '][timeslot]"]').val(event.extendedProps.uid);
            }
            const start_date = new Date(event.start.getTime() + event.start.getTimezoneOffset() * 60000);
            const end_date = new Date(event.end.getTime() + event.end.getTimezoneOffset() * 60000);
            $('input[name="data[tx_bwbookingmanager_domain_model_entry][' + entryUid + '][start_date]"]').val(start_date.getTime() / 1000);
            $('input[name="data[tx_bwbookingmanager_domain_model_entry][' + entryUid + '][end_date]"]').val(end_date.getTime() / 1000);
            $('select[name="data[tx_bwbookingmanager_domain_model_entry][' + entryUid + '][calendar]"]').val(event.extendedProps.calendar);
            // update date label
            const format = {
                weekday: "short",
                month: '2-digit',
                day: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                timeZone: 'Europe/Berlin'
            };
            const start = Intl.DateTimeFormat(viewState.language, format).format(start_date);
            const end = Intl.DateTimeFormat(viewState.language, format).format(end_date);
            $('#savedStartDate').html(start);
            $('#savedEndDate').html(end);
        }
    }
    return new BackendFormElementSelectTimeslot();
}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));


/***/ }),

/***/ "jquery":
/*!*************************!*
  !*** external "jquery" ***!
  \*************************/
/***/ ((module) => {

"use strict";
module.exports = __WEBPACK_EXTERNAL_MODULE_jquery__;

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		if(__webpack_module_cache__[moduleId]) {
/******/ 			return __webpack_module_cache__[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	// module exports must be returned from runtime so entry inlining is disabled
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__("./Resources/Private/TypeScript/BackendFormElementSelectTimeslot.ts");
/******/ })()
);;
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9UWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9bbmFtZV0vLi9SZXNvdXJjZXMvUHJpdmF0ZS9UeXBlU2NyaXB0L0JhY2tlbmRDYWxlbmRhclZpZXdTdGF0ZS50cyIsIndlYnBhY2s6Ly9UWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9bbmFtZV0vLi9SZXNvdXJjZXMvUHJpdmF0ZS9UeXBlU2NyaXB0L0JhY2tlbmRGb3JtRWxlbWVudFNlbGVjdFRpbWVzbG90LnRzIiwid2VicGFjazovL1RZUE8zL0NNUy9Cd0Jvb2tpbmdtYW5hZ2VyL1tuYW1lXS9leHRlcm5hbCBcImpxdWVyeVwiIiwid2VicGFjazovL1RZUE8zL0NNUy9Cd0Jvb2tpbmdtYW5hZ2VyL1tuYW1lXS93ZWJwYWNrL2Jvb3RzdHJhcCIsIndlYnBhY2s6Ly9UWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9bbmFtZV0vd2VicGFjay9zdGFydHVwIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiI7Ozs7Ozs7OztBQUFBLGlHQUFPLENBQUMsbUJBQVMsRUFBRSxPQUFTLENBQUMsbUNBQUU7QUFDL0I7QUFDQSxJQUFJLDhDQUE2QyxDQUFDLGNBQWMsRUFBQztBQUNqRTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxhQUFhO0FBQ2I7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLDJCQUEyQixrQ0FBa0M7QUFDN0Q7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxJQUFJLGdDQUFnQztBQUNwQyxDQUFDO0FBQUEsa0dBQUM7Ozs7Ozs7Ozs7O0FDakZGLGlHQUFPLENBQUMsbUJBQVMsRUFBRSxPQUFTLEVBQUUsNElBQXFELEVBQUUsMkNBQVEsQ0FBQyxtQ0FBRTtBQUNoRztBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxDQUFDO0FBQUEsa0dBQUM7Ozs7Ozs7Ozs7OztBQ2pERixvRDs7Ozs7O1VDQUE7VUFDQTs7VUFFQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7O1VBRUE7VUFDQTs7VUFFQTtVQUNBO1VBQ0E7OztVQ3JCQTtVQUNBO1VBQ0E7VUFDQSIsImZpbGUiOiJSZXNvdXJjZXMvUHVibGljL0phdmFTY3JpcHQvQmFja2VuZEZvcm1FbGVtZW50U2VsZWN0VGltZXNsb3QuanMiLCJzb3VyY2VzQ29udGVudCI6WyJkZWZpbmUoW1wicmVxdWlyZVwiLCBcImV4cG9ydHNcIl0sIGZ1bmN0aW9uIChyZXF1aXJlLCBleHBvcnRzKSB7XG4gICAgXCJ1c2Ugc3RyaWN0XCI7XG4gICAgT2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFwiX19lc01vZHVsZVwiLCB7IHZhbHVlOiB0cnVlIH0pO1xuICAgIGNsYXNzIEJhY2tlbmRDYWxlbmRhciB7XG4gICAgfVxuICAgIGNsYXNzIEJhY2tlbmRDYWxlbmRhclZpZXdTdGF0ZSB7XG4gICAgICAgIGNvbnN0cnVjdG9yKGVsKSB7XG4gICAgICAgICAgICBpZiAoIWVsLmhhc0F0dHJpYnV0ZSgnZGF0YS12aWV3LXN0YXRlJykpIHtcbiAgICAgICAgICAgICAgICBjb25zb2xlLmVycm9yKCdFbGVtZW50IGRvZXMgbm90IGhhdmUgdmlldy1zdGF0ZSBhdHRyaWJ1dGUhJyk7XG4gICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgY29uc3Qgdmlld1N0YXRlID0gSlNPTi5wYXJzZShlbC5nZXRBdHRyaWJ1dGUoJ2RhdGEtdmlldy1zdGF0ZScpKTtcbiAgICAgICAgICAgIC8vIEBUT0RPOiBtb3N0IHByb3BlcnRpZXMgYXJlIGluIHBhcnNlZCBqc29uIGZvciBzdXJlLCB3ZSBjb3VsZCBleHRlbmRcbiAgICAgICAgICAgIHRoaXMucGlkID0gdmlld1N0YXRlLnBpZDtcbiAgICAgICAgICAgIHRoaXMubGFuZ3VhZ2UgPSAnbGFuZ3VhZ2UnIGluIHZpZXdTdGF0ZSAmJiB2aWV3U3RhdGUubGFuZ3VhZ2UgIT09ICdkZWZhdWx0JyA/IHZpZXdTdGF0ZS5sYW5ndWFnZSA6ICdlbic7XG4gICAgICAgICAgICB0aGlzLnN0YXJ0ID0gJ3N0YXJ0JyBpbiB2aWV3U3RhdGUgPyB2aWV3U3RhdGUuc3RhcnQgOiBuZXcgRGF0ZSgpO1xuICAgICAgICAgICAgdGhpcy5jYWxlbmRhclZpZXcgPSB2aWV3U3RhdGUuY2FsZW5kYXJWaWV3O1xuICAgICAgICAgICAgdGhpcy5wYXN0RW50cmllcyA9IHZpZXdTdGF0ZS5wYXN0RW50cmllcztcbiAgICAgICAgICAgIHRoaXMucGFzdFRpbWVzbG90cyA9IHZpZXdTdGF0ZS5wYXN0VGltZXNsb3RzO1xuICAgICAgICAgICAgdGhpcy5ub3RCb29rYWJsZVRpbWVzbG90cyA9IHZpZXdTdGF0ZS5ub3RCb29rYWJsZVRpbWVzbG90cztcbiAgICAgICAgICAgIHRoaXMuZnV0dXJlRW50cmllcyA9ICdmdXR1cmVFbnRyaWVzJyBpbiB2aWV3U3RhdGUgJiYgdmlld1N0YXRlLmZ1dHVyZUVudHJpZXMgPT09ICd0cnVlJztcbiAgICAgICAgICAgIHRoaXMuZW50cnlVaWQgPSAnZW50cnlVaWQnIGluIHZpZXdTdGF0ZSA/IHZpZXdTdGF0ZS5lbnRyeVVpZCA6IG51bGw7XG4gICAgICAgICAgICB0aGlzLmNhbGVuZGFyID0gJ2NhbGVuZGFyJyBpbiB2aWV3U3RhdGUgPyB2aWV3U3RhdGUuY2FsZW5kYXIgOiBudWxsO1xuICAgICAgICAgICAgdGhpcy50aW1lc2xvdCA9ICd0aW1lc2xvdCcgaW4gdmlld1N0YXRlID8gdmlld1N0YXRlLnRpbWVzbG90IDogbnVsbDtcbiAgICAgICAgICAgIHRoaXMuYnV0dG9uU2F2ZVRleHQgPSAnYnV0dG9uU2F2ZVRleHQnIGluIHZpZXdTdGF0ZSA/IHZpZXdTdGF0ZS5idXR0b25TYXZlVGV4dCA6ICcnO1xuICAgICAgICAgICAgdGhpcy5idXR0b25DYW5jZWxUZXh0ID0gJ2J1dHRvbkNhbmNlbFRleHQnIGluIHZpZXdTdGF0ZSA/IHZpZXdTdGF0ZS5idXR0b25DYW5jZWxUZXh0IDogJyc7XG4gICAgICAgICAgICB0aGlzLmVudHJ5U3RhcnQgPSAnZW50cnlTdGFydCcgaW4gdmlld1N0YXRlID8gdmlld1N0YXRlLmVudHJ5U3RhcnQgOiBudWxsO1xuICAgICAgICAgICAgdGhpcy5lbnRyeUVuZCA9ICdlbnRyeUVuZCcgaW4gdmlld1N0YXRlID8gdmlld1N0YXRlLmVudHJ5RW5kIDogbnVsbDtcbiAgICAgICAgICAgIHRoaXMuY3VycmVudENhbGVuZGFycyA9IHZpZXdTdGF0ZS5jdXJyZW50Q2FsZW5kYXJzO1xuICAgICAgICAgICAgdGhpcy53YXJuaW5nVGl0bGUgPSAnd2FybmluZ1RpdGxlJyBpbiB2aWV3U3RhdGUgPyB2aWV3U3RhdGUud2FybmluZ1RpdGxlIDogJyc7XG4gICAgICAgICAgICB0aGlzLndhcm5pbmdUZXh0ID0gJ3dhcm5pbmdUZXh0JyBpbiB2aWV3U3RhdGUgPyB2aWV3U3RhdGUud2FybmluZ1RleHQgOiAnJztcbiAgICAgICAgICAgIHRoaXMud2FybmluZ0J1dHRvbiA9ICd3YXJuaW5nQnV0dG9uJyBpbiB2aWV3U3RhdGUgPyB2aWV3U3RhdGUud2FybmluZ0J1dHRvbiA6ICcnO1xuICAgICAgICAgICAgdGhpcy5jYWxlbmRhck9wdGlvbnMgPSAnY2FsZW5kYXJPcHRpb25zJyBpbiB2aWV3U3RhdGUgPyB2aWV3U3RhdGUuY2FsZW5kYXJPcHRpb25zIDoge307XG4gICAgICAgICAgICB0aGlzLmV2ZW50cyA9IHtcbiAgICAgICAgICAgICAgICAndXJsJzogVFlQTzMuc2V0dGluZ3MuYWpheFVybHNbJ2FwaV9jYWxlbmRhcl9zaG93J10sXG4gICAgICAgICAgICAgICAgJ2V4dHJhUGFyYW1zJzogKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICBjb25zdCBlbnRyeVN0YXJ0ID0gdGhpcy5lbnRyeVN0YXJ0ID8gKG5ldyBEYXRlKHRoaXMuZW50cnlTdGFydCkpLmdldFRpbWUoKSAvIDEwMDAgOiBudWxsO1xuICAgICAgICAgICAgICAgICAgICBjb25zdCBlbnRyeUVuZCA9IHRoaXMuZW50cnlFbmQgPyAobmV3IERhdGUodGhpcy5lbnRyeUVuZCkpLmdldFRpbWUoKSAvIDEwMDAgOiBudWxsO1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgJ3BpZCc6IHRoaXMucGlkLFxuICAgICAgICAgICAgICAgICAgICAgICAgJ2VudHJ5VWlkJzogdGhpcy5lbnRyeVVpZCxcbiAgICAgICAgICAgICAgICAgICAgICAgICdlbnRyeVN0YXJ0JzogZW50cnlTdGFydCxcbiAgICAgICAgICAgICAgICAgICAgICAgICdlbnRyeUVuZCc6IGVudHJ5RW5kLFxuICAgICAgICAgICAgICAgICAgICAgICAgJ2NhbGVuZGFyJzogdGhpcy5jYWxlbmRhcixcbiAgICAgICAgICAgICAgICAgICAgICAgICd0aW1lc2xvdCc6IHRoaXMudGltZXNsb3RcbiAgICAgICAgICAgICAgICAgICAgfTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9O1xuICAgICAgICB9XG4gICAgICAgIC8qKlxuICAgICAgICAgKiBVc2VkIGluIEJhY2tlbmRNb2R1bGVDYWxlbmRhciB0byBwZXJzaXN0IHRoZSBjdXJyZW50IGRpc3BsYXkgb2YgdmlldyB0eXBlIGFuZCBzZWxlY3RlZCBkYXRlXG4gICAgICAgICAqL1xuICAgICAgICBzYXZlQXNVc2VyVmlldygpIHtcbiAgICAgICAgICAgIGlmICh0aGlzLnNhdmVSZXF1ZXN0KSB7XG4gICAgICAgICAgICAgICAgdGhpcy5zYXZlUmVxdWVzdC5hYm9ydCgpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgdGhpcy5zYXZlUmVxdWVzdCA9ICQucG9zdChUWVBPMy5zZXR0aW5ncy5hamF4VXJsc1snYXBpX3VzZXJfc2V0dGluZyddLCB7XG4gICAgICAgICAgICAgICAgdmlld1N0YXRlOiB7XG4gICAgICAgICAgICAgICAgICAgIHBpZDogdGhpcy5waWQsXG4gICAgICAgICAgICAgICAgICAgIHN0YXJ0OiB0aGlzLnN0YXJ0LFxuICAgICAgICAgICAgICAgICAgICBjYWxlbmRhclZpZXc6IHRoaXMuY2FsZW5kYXJWaWV3LFxuICAgICAgICAgICAgICAgICAgICBwYXN0RW50cmllczogdGhpcy5wYXN0RW50cmllcyxcbiAgICAgICAgICAgICAgICAgICAgcGFzdFRpbWVzbG90czogdGhpcy5wYXN0VGltZXNsb3RzLFxuICAgICAgICAgICAgICAgICAgICBub3RCb29rYWJsZVRpbWVzbG90czogdGhpcy5ub3RCb29rYWJsZVRpbWVzbG90cyxcbiAgICAgICAgICAgICAgICAgICAgZnV0dXJlRW50cmllczogdGhpcy5mdXR1cmVFbnRyaWVzXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICAgICAgaGFzRGlyZWN0Qm9va2luZ0NhbGVuZGFyKCkge1xuICAgICAgICAgICAgcmV0dXJuIHRoaXMuZ2V0Rmlyc3REaXJlY3RCb29rYWJsZUNhbGVuZGFyKCkgIT09IG51bGw7XG4gICAgICAgIH1cbiAgICAgICAgZ2V0Rmlyc3REaXJlY3RCb29rYWJsZUNhbGVuZGFyKCkge1xuICAgICAgICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCB0aGlzLmN1cnJlbnRDYWxlbmRhcnMubGVuZ3RoOyBpKyspIHtcbiAgICAgICAgICAgICAgICBpZiAodGhpcy5jdXJyZW50Q2FsZW5kYXJzW2ldLmRpcmVjdEJvb2tpbmcpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHRoaXMuY3VycmVudENhbGVuZGFyc1tpXTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICByZXR1cm4gbnVsbDtcbiAgICAgICAgfVxuICAgIH1cbiAgICBleHBvcnRzLkJhY2tlbmRDYWxlbmRhclZpZXdTdGF0ZSA9IEJhY2tlbmRDYWxlbmRhclZpZXdTdGF0ZTtcbn0pO1xuIiwiZGVmaW5lKFtcInJlcXVpcmVcIiwgXCJleHBvcnRzXCIsIFwiVFlQTzMvQ01TL0J3Qm9va2luZ21hbmFnZXIvQmFja2VuZENhbGVuZGFyVmlld1N0YXRlXCIsIFwianF1ZXJ5XCJdLCBmdW5jdGlvbiAocmVxdWlyZSwgZXhwb3J0cywgQmFja2VuZENhbGVuZGFyVmlld1N0YXRlXzEsICQpIHtcbiAgICBcInVzZSBzdHJpY3RcIjtcbiAgICAvKipcbiAgICAgKiBNb2R1bGU6IFRZUE8zL0NNUy9Cd0Jvb2tpbmdtYW5hZ2VyL0JhY2tlbmRGb3JtRWxlbWVudFNlbGVjdFRpbWVzbG90XG4gICAgICpcbiAgICAgKiBAZXhwb3J0cyBUWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9CYWNrZW5kRm9ybUVsZW1lbnRTZWxlY3RUaW1lc2xvdFxuICAgICAqL1xuICAgIGNsYXNzIEJhY2tlbmRGb3JtRWxlbWVudFNlbGVjdFRpbWVzbG90IHtcbiAgICAgICAgY29uc3RydWN0b3IoKSB7XG4gICAgICAgICAgICBjb25zdCBidXR0b24gPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZW50cnktZGF0ZS1zZWxlY3QtYnV0dG9uJyk7XG4gICAgICAgICAgICAkKCcjZW50cnktZGF0ZS1zZWxlY3QtYnV0dG9uJykub24oJ2NsaWNrJywgdGhpcy5vbkJ1dHRvbkNsaWNrLmJpbmQodGhpcykpO1xuICAgICAgICAgICAgcGFyZW50LndpbmRvdy5CYWNrZW5kTW9kYWxDYWxlbmRhci5vblNhdmUgPSB0aGlzLm9uTW9kYWxTYXZlLmJpbmQodGhpcyk7XG4gICAgICAgIH1cbiAgICAgICAgb25CdXR0b25DbGljayhlKSB7XG4gICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICBjb25zdCBidXR0b24gPSBlLmN1cnJlbnRUYXJnZXQ7XG4gICAgICAgICAgICBwYXJlbnQud2luZG93LkJhY2tlbmRNb2RhbENhbGVuZGFyLnZpZXdTdGF0ZSA9IG5ldyBCYWNrZW5kQ2FsZW5kYXJWaWV3U3RhdGVfMS5CYWNrZW5kQ2FsZW5kYXJWaWV3U3RhdGUoYnV0dG9uKTtcbiAgICAgICAgICAgIHBhcmVudC53aW5kb3cuQmFja2VuZE1vZGFsQ2FsZW5kYXIub3Blbk1vZGFsKCk7XG4gICAgICAgIH1cbiAgICAgICAgb25Nb2RhbFNhdmUoZXZlbnQsIHZpZXdTdGF0ZSkge1xuICAgICAgICAgICAgLy8gdXBkYXRlIGJ1dHRvbiBqc29uXG4gICAgICAgICAgICBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZW50cnktZGF0ZS1zZWxlY3QtYnV0dG9uJykuc2V0QXR0cmlidXRlKCdkYXRhLXZpZXctc3RhdGUnLCBKU09OLnN0cmluZ2lmeSh2aWV3U3RhdGUpKTtcbiAgICAgICAgICAgIC8vIHNhdmUgdG8gbmV3IGZvcm1cbiAgICAgICAgICAgIGNvbnN0IGVudHJ5VWlkID0gdmlld1N0YXRlLmVudHJ5VWlkO1xuICAgICAgICAgICAgaWYgKGV2ZW50LmV4dGVuZGVkUHJvcHMubW9kZWwgPT09ICdUaW1lc2xvdCcpIHtcbiAgICAgICAgICAgICAgICAkKCdpbnB1dFtuYW1lPVwiZGF0YVt0eF9id2Jvb2tpbmdtYW5hZ2VyX2RvbWFpbl9tb2RlbF9lbnRyeV1bJyArIGVudHJ5VWlkICsgJ11bdGltZXNsb3RdXCJdJykudmFsKGV2ZW50LmV4dGVuZGVkUHJvcHMudWlkKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGNvbnN0IHN0YXJ0X2RhdGUgPSBuZXcgRGF0ZShldmVudC5zdGFydC5nZXRUaW1lKCkgKyBldmVudC5zdGFydC5nZXRUaW1lem9uZU9mZnNldCgpICogNjAwMDApO1xuICAgICAgICAgICAgY29uc3QgZW5kX2RhdGUgPSBuZXcgRGF0ZShldmVudC5lbmQuZ2V0VGltZSgpICsgZXZlbnQuZW5kLmdldFRpbWV6b25lT2Zmc2V0KCkgKiA2MDAwMCk7XG4gICAgICAgICAgICAkKCdpbnB1dFtuYW1lPVwiZGF0YVt0eF9id2Jvb2tpbmdtYW5hZ2VyX2RvbWFpbl9tb2RlbF9lbnRyeV1bJyArIGVudHJ5VWlkICsgJ11bc3RhcnRfZGF0ZV1cIl0nKS52YWwoc3RhcnRfZGF0ZS5nZXRUaW1lKCkgLyAxMDAwKTtcbiAgICAgICAgICAgICQoJ2lucHV0W25hbWU9XCJkYXRhW3R4X2J3Ym9va2luZ21hbmFnZXJfZG9tYWluX21vZGVsX2VudHJ5XVsnICsgZW50cnlVaWQgKyAnXVtlbmRfZGF0ZV1cIl0nKS52YWwoZW5kX2RhdGUuZ2V0VGltZSgpIC8gMTAwMCk7XG4gICAgICAgICAgICAkKCdzZWxlY3RbbmFtZT1cImRhdGFbdHhfYndib29raW5nbWFuYWdlcl9kb21haW5fbW9kZWxfZW50cnldWycgKyBlbnRyeVVpZCArICddW2NhbGVuZGFyXVwiXScpLnZhbChldmVudC5leHRlbmRlZFByb3BzLmNhbGVuZGFyKTtcbiAgICAgICAgICAgIC8vIHVwZGF0ZSBkYXRlIGxhYmVsXG4gICAgICAgICAgICBjb25zdCBmb3JtYXQgPSB7XG4gICAgICAgICAgICAgICAgd2Vla2RheTogXCJzaG9ydFwiLFxuICAgICAgICAgICAgICAgIG1vbnRoOiAnMi1kaWdpdCcsXG4gICAgICAgICAgICAgICAgZGF5OiAnMi1kaWdpdCcsXG4gICAgICAgICAgICAgICAgeWVhcjogJ251bWVyaWMnLFxuICAgICAgICAgICAgICAgIGhvdXI6ICcyLWRpZ2l0JyxcbiAgICAgICAgICAgICAgICBtaW51dGU6ICcyLWRpZ2l0JyxcbiAgICAgICAgICAgICAgICB0aW1lWm9uZTogJ0V1cm9wZS9CZXJsaW4nXG4gICAgICAgICAgICB9O1xuICAgICAgICAgICAgY29uc3Qgc3RhcnQgPSBJbnRsLkRhdGVUaW1lRm9ybWF0KHZpZXdTdGF0ZS5sYW5ndWFnZSwgZm9ybWF0KS5mb3JtYXQoc3RhcnRfZGF0ZSk7XG4gICAgICAgICAgICBjb25zdCBlbmQgPSBJbnRsLkRhdGVUaW1lRm9ybWF0KHZpZXdTdGF0ZS5sYW5ndWFnZSwgZm9ybWF0KS5mb3JtYXQoZW5kX2RhdGUpO1xuICAgICAgICAgICAgJCgnI3NhdmVkU3RhcnREYXRlJykuaHRtbChzdGFydCk7XG4gICAgICAgICAgICAkKCcjc2F2ZWRFbmREYXRlJykuaHRtbChlbmQpO1xuICAgICAgICB9XG4gICAgfVxuICAgIHJldHVybiBuZXcgQmFja2VuZEZvcm1FbGVtZW50U2VsZWN0VGltZXNsb3QoKTtcbn0pO1xuIiwibW9kdWxlLmV4cG9ydHMgPSBfX1dFQlBBQ0tfRVhURVJOQUxfTU9EVUxFX2pxdWVyeV9fOyIsIi8vIFRoZSBtb2R1bGUgY2FjaGVcbnZhciBfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX18gPSB7fTtcblxuLy8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbmZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG5cdGlmKF9fd2VicGFja19tb2R1bGVfY2FjaGVfX1ttb2R1bGVJZF0pIHtcblx0XHRyZXR1cm4gX193ZWJwYWNrX21vZHVsZV9jYWNoZV9fW21vZHVsZUlkXS5leHBvcnRzO1xuXHR9XG5cdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG5cdHZhciBtb2R1bGUgPSBfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX19bbW9kdWxlSWRdID0ge1xuXHRcdC8vIG5vIG1vZHVsZS5pZCBuZWVkZWRcblx0XHQvLyBubyBtb2R1bGUubG9hZGVkIG5lZWRlZFxuXHRcdGV4cG9ydHM6IHt9XG5cdH07XG5cblx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG5cdF9fd2VicGFja19tb2R1bGVzX19bbW9kdWxlSWRdKG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG5cdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG5cdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbn1cblxuIiwiLy8gbW9kdWxlIGV4cG9ydHMgbXVzdCBiZSByZXR1cm5lZCBmcm9tIHJ1bnRpbWUgc28gZW50cnkgaW5saW5pbmcgaXMgZGlzYWJsZWRcbi8vIHN0YXJ0dXBcbi8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xucmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oXCIuL1Jlc291cmNlcy9Qcml2YXRlL1R5cGVTY3JpcHQvQmFja2VuZEZvcm1FbGVtZW50U2VsZWN0VGltZXNsb3QudHNcIik7XG4iXSwic291cmNlUm9vdCI6IiJ9