define("TYPO3/CMS/BwBookingmanager/SelectTimeslotDatesElement", [], () => /******/ (() => { // webpackBootstrap
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

/***/ "./Resources/Private/TypeScript/SelectTimeslotDatesElement.ts":
/*!********************************************************************!*
  !*** ./Resources/Private/TypeScript/SelectTimeslotDatesElement.ts ***!
  \********************************************************************/
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;!(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__, exports, __webpack_require__(/*! TYPO3/CMS/BwBookingmanager/BackendCalendarViewState */ "./Resources/Private/TypeScript/BackendCalendarViewState.ts")], __WEBPACK_AMD_DEFINE_RESULT__ = (function (require, exports, BackendCalendarViewState_1) {
    "use strict";
    /**
     * Module: TYPO3/CMS/BwBookingmanager/SelectTimeslotDatesElement
     *
     * @exports TYPO3/CMS/BwBookingmanager/SelectTimeslotDatesElement
     */
    class SelectTimeslotDatesElement {
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
            $('input[name="data[tx_bwbookingmanager_domain_model_entry][' + entryUid + '][start_date]"]').val(event.start.getTime() / 1000);
            $('input[name="data[tx_bwbookingmanager_domain_model_entry][' + entryUid + '][end_date]"]').val(event.end.getTime() / 1000);
            $('select[name="data[tx_bwbookingmanager_domain_model_entry][' + entryUid + '][calendar]"]').val(event.extendedProps.calendar);
            // update date label
            const format = {
                weekday: 'short',
                month: '2-digit',
                day: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                timeZone: 'UTC'
            };
            const start = Intl.DateTimeFormat(viewState.language, format).format(event.start);
            const end = Intl.DateTimeFormat(viewState.language, format).format(event.end);
            $('#savedStartDate').html(start);
            $('#savedEndDate').html(end);
        }
    }
    return new SelectTimeslotDatesElement();
}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));


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
/******/ 	return __webpack_require__("./Resources/Private/TypeScript/SelectTimeslotDatesElement.ts");
/******/ })()
);;
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9UWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9bbmFtZV0vLi9SZXNvdXJjZXMvUHJpdmF0ZS9UeXBlU2NyaXB0L0JhY2tlbmRDYWxlbmRhclZpZXdTdGF0ZS50cyIsIndlYnBhY2s6Ly9UWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9bbmFtZV0vLi9SZXNvdXJjZXMvUHJpdmF0ZS9UeXBlU2NyaXB0L1NlbGVjdFRpbWVzbG90RGF0ZXNFbGVtZW50LnRzIiwid2VicGFjazovL1RZUE8zL0NNUy9Cd0Jvb2tpbmdtYW5hZ2VyL1tuYW1lXS93ZWJwYWNrL2Jvb3RzdHJhcCIsIndlYnBhY2s6Ly9UWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9bbmFtZV0vd2VicGFjay9zdGFydHVwIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiI7Ozs7Ozs7OztBQUFBLGlHQUFPLENBQUMsbUJBQVMsRUFBRSxPQUFTLENBQUMsbUNBQUU7QUFDL0I7QUFDQSxJQUFJLDhDQUE2QyxDQUFDLGNBQWMsRUFBQztBQUNqRTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsYUFBYTtBQUNiO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSwyQkFBMkIsa0NBQWtDO0FBQzdEO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsSUFBSSxnQ0FBZ0M7QUFDcEMsQ0FBQztBQUFBLGtHQUFDOzs7Ozs7Ozs7OztBQzdFRixpR0FBTyxDQUFDLG1CQUFTLEVBQUUsT0FBUyxFQUFFLDRJQUFxRCxDQUFDLG1DQUFFO0FBQ3RGO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsQ0FBQztBQUFBLGtHQUFDOzs7Ozs7O1VDL0NGO1VBQ0E7O1VBRUE7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBOztVQUVBO1VBQ0E7O1VBRUE7VUFDQTtVQUNBOzs7VUNyQkE7VUFDQTtVQUNBO1VBQ0EiLCJmaWxlIjoiUmVzb3VyY2VzL1B1YmxpYy9KYXZhU2NyaXB0L1NlbGVjdFRpbWVzbG90RGF0ZXNFbGVtZW50LmpzIiwic291cmNlc0NvbnRlbnQiOlsiZGVmaW5lKFtcInJlcXVpcmVcIiwgXCJleHBvcnRzXCJdLCBmdW5jdGlvbiAocmVxdWlyZSwgZXhwb3J0cykge1xuICAgIFwidXNlIHN0cmljdFwiO1xuICAgIE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBcIl9fZXNNb2R1bGVcIiwgeyB2YWx1ZTogdHJ1ZSB9KTtcbiAgICBjbGFzcyBCYWNrZW5kQ2FsZW5kYXIge1xuICAgIH1cbiAgICBjbGFzcyBCYWNrZW5kQ2FsZW5kYXJWaWV3U3RhdGUge1xuICAgICAgICBjb25zdHJ1Y3RvcihlbCkge1xuICAgICAgICAgICAgaWYgKCFlbC5oYXNBdHRyaWJ1dGUoJ2RhdGEtdmlldy1zdGF0ZScpKSB7XG4gICAgICAgICAgICAgICAgY29uc29sZS5lcnJvcignRWxlbWVudCBkb2VzIG5vdCBoYXZlIHZpZXctc3RhdGUgYXR0cmlidXRlIScpO1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGNvbnN0IHZpZXdTdGF0ZSA9IEpTT04ucGFyc2UoZWwuZ2V0QXR0cmlidXRlKCdkYXRhLXZpZXctc3RhdGUnKSk7XG4gICAgICAgICAgICAvLyBAVE9ETzogbW9zdCBwcm9wZXJ0aWVzIGFyZSBpbiBwYXJzZWQganNvbiBmb3Igc3VyZSwgd2UgY291bGQgZXh0ZW5kXG4gICAgICAgICAgICB0aGlzLnBpZCA9IHZpZXdTdGF0ZS5waWQ7XG4gICAgICAgICAgICB0aGlzLmxhbmd1YWdlID0gJ2xhbmd1YWdlJyBpbiB2aWV3U3RhdGUgJiYgdmlld1N0YXRlLmxhbmd1YWdlICE9PSAnZGVmYXVsdCcgPyB2aWV3U3RhdGUubGFuZ3VhZ2UgOiAnZW4nO1xuICAgICAgICAgICAgdGhpcy5zdGFydCA9ICdzdGFydCcgaW4gdmlld1N0YXRlID8gdmlld1N0YXRlLnN0YXJ0IDogbmV3IERhdGUoKTtcbiAgICAgICAgICAgIHRoaXMuY2FsZW5kYXJWaWV3ID0gdmlld1N0YXRlLmNhbGVuZGFyVmlldztcbiAgICAgICAgICAgIHRoaXMucGFzdEVudHJpZXMgPSB2aWV3U3RhdGUucGFzdEVudHJpZXM7XG4gICAgICAgICAgICB0aGlzLnBhc3RUaW1lc2xvdHMgPSB2aWV3U3RhdGUucGFzdFRpbWVzbG90cztcbiAgICAgICAgICAgIHRoaXMubm90Qm9va2FibGVUaW1lc2xvdHMgPSB2aWV3U3RhdGUubm90Qm9va2FibGVUaW1lc2xvdHM7XG4gICAgICAgICAgICB0aGlzLmZ1dHVyZUVudHJpZXMgPSAnZnV0dXJlRW50cmllcycgaW4gdmlld1N0YXRlICYmIHZpZXdTdGF0ZS5mdXR1cmVFbnRyaWVzID09PSAndHJ1ZSc7XG4gICAgICAgICAgICB0aGlzLmVudHJ5VWlkID0gJ2VudHJ5VWlkJyBpbiB2aWV3U3RhdGUgPyB2aWV3U3RhdGUuZW50cnlVaWQgOiBudWxsO1xuICAgICAgICAgICAgdGhpcy5jYWxlbmRhciA9ICdjYWxlbmRhcicgaW4gdmlld1N0YXRlID8gdmlld1N0YXRlLmNhbGVuZGFyIDogbnVsbDtcbiAgICAgICAgICAgIHRoaXMudGltZXNsb3QgPSAndGltZXNsb3QnIGluIHZpZXdTdGF0ZSA/IHZpZXdTdGF0ZS50aW1lc2xvdCA6IG51bGw7XG4gICAgICAgICAgICB0aGlzLmJ1dHRvblNhdmVUZXh0ID0gJ2J1dHRvblNhdmVUZXh0JyBpbiB2aWV3U3RhdGUgPyB2aWV3U3RhdGUuYnV0dG9uU2F2ZVRleHQgOiAnJztcbiAgICAgICAgICAgIHRoaXMuYnV0dG9uQ2FuY2VsVGV4dCA9ICdidXR0b25DYW5jZWxUZXh0JyBpbiB2aWV3U3RhdGUgPyB2aWV3U3RhdGUuYnV0dG9uQ2FuY2VsVGV4dCA6ICcnO1xuICAgICAgICAgICAgdGhpcy5lbnRyeVN0YXJ0ID0gJ2VudHJ5U3RhcnQnIGluIHZpZXdTdGF0ZSA/IHZpZXdTdGF0ZS5lbnRyeVN0YXJ0IDogbnVsbDtcbiAgICAgICAgICAgIHRoaXMuZW50cnlFbmQgPSAnZW50cnlFbmQnIGluIHZpZXdTdGF0ZSA/IHZpZXdTdGF0ZS5lbnRyeUVuZCA6IG51bGw7XG4gICAgICAgICAgICB0aGlzLmN1cnJlbnRDYWxlbmRhcnMgPSB2aWV3U3RhdGUuY3VycmVudENhbGVuZGFycztcbiAgICAgICAgICAgIHRoaXMuZXZlbnRzID0ge1xuICAgICAgICAgICAgICAgICd1cmwnOiBUWVBPMy5zZXR0aW5ncy5hamF4VXJsc1snYXBpX2NhbGVuZGFyX3Nob3cnXSxcbiAgICAgICAgICAgICAgICAnZXh0cmFQYXJhbXMnOiAoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IGVudHJ5U3RhcnQgPSB0aGlzLmVudHJ5U3RhcnQgPyAobmV3IERhdGUodGhpcy5lbnRyeVN0YXJ0KSkuZ2V0VGltZSgpIC8gMTAwMCA6IG51bGw7XG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IGVudHJ5RW5kID0gdGhpcy5lbnRyeUVuZCA/IChuZXcgRGF0ZSh0aGlzLmVudHJ5RW5kKSkuZ2V0VGltZSgpIC8gMTAwMCA6IG51bGw7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAncGlkJzogdGhpcy5waWQsXG4gICAgICAgICAgICAgICAgICAgICAgICAnZW50cnlVaWQnOiB0aGlzLmVudHJ5VWlkLFxuICAgICAgICAgICAgICAgICAgICAgICAgJ2VudHJ5U3RhcnQnOiBlbnRyeVN0YXJ0LFxuICAgICAgICAgICAgICAgICAgICAgICAgJ2VudHJ5RW5kJzogZW50cnlFbmQsXG4gICAgICAgICAgICAgICAgICAgICAgICAnY2FsZW5kYXInOiB0aGlzLmNhbGVuZGFyLFxuICAgICAgICAgICAgICAgICAgICAgICAgJ3RpbWVzbG90JzogdGhpcy50aW1lc2xvdFxuICAgICAgICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH07XG4gICAgICAgIH1cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFVzZWQgaW4gQmFja2VuZE1vZHVsZUNhbGVuZGFyIHRvIHBlcnNpc3QgdGhlIGN1cnJlbnQgZGlzcGxheSBvZiB2aWV3IHR5cGUgYW5kIHNlbGVjdGVkIGRhdGVcbiAgICAgICAgICovXG4gICAgICAgIHNhdmVBc1VzZXJWaWV3KCkge1xuICAgICAgICAgICAgaWYgKHRoaXMuc2F2ZVJlcXVlc3QpIHtcbiAgICAgICAgICAgICAgICB0aGlzLnNhdmVSZXF1ZXN0LmFib3J0KCk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICB0aGlzLnNhdmVSZXF1ZXN0ID0gJC5wb3N0KFRZUE8zLnNldHRpbmdzLmFqYXhVcmxzWydhcGlfdXNlcl9zZXR0aW5nJ10sIHtcbiAgICAgICAgICAgICAgICB2aWV3U3RhdGU6IHtcbiAgICAgICAgICAgICAgICAgICAgcGlkOiB0aGlzLnBpZCxcbiAgICAgICAgICAgICAgICAgICAgc3RhcnQ6IHRoaXMuc3RhcnQsXG4gICAgICAgICAgICAgICAgICAgIGNhbGVuZGFyVmlldzogdGhpcy5jYWxlbmRhclZpZXcsXG4gICAgICAgICAgICAgICAgICAgIHBhc3RFbnRyaWVzOiB0aGlzLnBhc3RFbnRyaWVzLFxuICAgICAgICAgICAgICAgICAgICBwYXN0VGltZXNsb3RzOiB0aGlzLnBhc3RUaW1lc2xvdHMsXG4gICAgICAgICAgICAgICAgICAgIG5vdEJvb2thYmxlVGltZXNsb3RzOiB0aGlzLm5vdEJvb2thYmxlVGltZXNsb3RzLFxuICAgICAgICAgICAgICAgICAgICBmdXR1cmVFbnRyaWVzOiB0aGlzLmZ1dHVyZUVudHJpZXNcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuICAgICAgICBoYXNEaXJlY3RCb29raW5nQ2FsZW5kYXIoKSB7XG4gICAgICAgICAgICByZXR1cm4gdGhpcy5nZXRGaXJzdERpcmVjdEJvb2thYmxlQ2FsZW5kYXIoKSAhPT0gbnVsbDtcbiAgICAgICAgfVxuICAgICAgICBnZXRGaXJzdERpcmVjdEJvb2thYmxlQ2FsZW5kYXIoKSB7XG4gICAgICAgICAgICBmb3IgKGxldCBpID0gMDsgaSA8IHRoaXMuY3VycmVudENhbGVuZGFycy5sZW5ndGg7IGkrKykge1xuICAgICAgICAgICAgICAgIGlmICh0aGlzLmN1cnJlbnRDYWxlbmRhcnNbaV0uZGlyZWN0Qm9va2luZykge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4gdGhpcy5jdXJyZW50Q2FsZW5kYXJzW2ldO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHJldHVybiBudWxsO1xuICAgICAgICB9XG4gICAgfVxuICAgIGV4cG9ydHMuQmFja2VuZENhbGVuZGFyVmlld1N0YXRlID0gQmFja2VuZENhbGVuZGFyVmlld1N0YXRlO1xufSk7XG4iLCJkZWZpbmUoW1wicmVxdWlyZVwiLCBcImV4cG9ydHNcIiwgXCJUWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9CYWNrZW5kQ2FsZW5kYXJWaWV3U3RhdGVcIl0sIGZ1bmN0aW9uIChyZXF1aXJlLCBleHBvcnRzLCBCYWNrZW5kQ2FsZW5kYXJWaWV3U3RhdGVfMSkge1xuICAgIFwidXNlIHN0cmljdFwiO1xuICAgIC8qKlxuICAgICAqIE1vZHVsZTogVFlQTzMvQ01TL0J3Qm9va2luZ21hbmFnZXIvU2VsZWN0VGltZXNsb3REYXRlc0VsZW1lbnRcbiAgICAgKlxuICAgICAqIEBleHBvcnRzIFRZUE8zL0NNUy9Cd0Jvb2tpbmdtYW5hZ2VyL1NlbGVjdFRpbWVzbG90RGF0ZXNFbGVtZW50XG4gICAgICovXG4gICAgY2xhc3MgU2VsZWN0VGltZXNsb3REYXRlc0VsZW1lbnQge1xuICAgICAgICBjb25zdHJ1Y3RvcigpIHtcbiAgICAgICAgICAgIGNvbnN0IGJ1dHRvbiA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdlbnRyeS1kYXRlLXNlbGVjdC1idXR0b24nKTtcbiAgICAgICAgICAgICQoJyNlbnRyeS1kYXRlLXNlbGVjdC1idXR0b24nKS5vbignY2xpY2snLCB0aGlzLm9uQnV0dG9uQ2xpY2suYmluZCh0aGlzKSk7XG4gICAgICAgICAgICBwYXJlbnQud2luZG93LkJhY2tlbmRNb2RhbENhbGVuZGFyLm9uU2F2ZSA9IHRoaXMub25Nb2RhbFNhdmUuYmluZCh0aGlzKTtcbiAgICAgICAgfVxuICAgICAgICBvbkJ1dHRvbkNsaWNrKGUpIHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIGNvbnN0IGJ1dHRvbiA9IGUuY3VycmVudFRhcmdldDtcbiAgICAgICAgICAgIHBhcmVudC53aW5kb3cuQmFja2VuZE1vZGFsQ2FsZW5kYXIudmlld1N0YXRlID0gbmV3IEJhY2tlbmRDYWxlbmRhclZpZXdTdGF0ZV8xLkJhY2tlbmRDYWxlbmRhclZpZXdTdGF0ZShidXR0b24pO1xuICAgICAgICAgICAgcGFyZW50LndpbmRvdy5CYWNrZW5kTW9kYWxDYWxlbmRhci5vcGVuTW9kYWwoKTtcbiAgICAgICAgfVxuICAgICAgICBvbk1vZGFsU2F2ZShldmVudCwgdmlld1N0YXRlKSB7XG4gICAgICAgICAgICAvLyB1cGRhdGUgYnV0dG9uIGpzb25cbiAgICAgICAgICAgIGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdlbnRyeS1kYXRlLXNlbGVjdC1idXR0b24nKS5zZXRBdHRyaWJ1dGUoJ2RhdGEtdmlldy1zdGF0ZScsIEpTT04uc3RyaW5naWZ5KHZpZXdTdGF0ZSkpO1xuICAgICAgICAgICAgLy8gc2F2ZSB0byBuZXcgZm9ybVxuICAgICAgICAgICAgY29uc3QgZW50cnlVaWQgPSB2aWV3U3RhdGUuZW50cnlVaWQ7XG4gICAgICAgICAgICBpZiAoZXZlbnQuZXh0ZW5kZWRQcm9wcy5tb2RlbCA9PT0gJ1RpbWVzbG90Jykge1xuICAgICAgICAgICAgICAgICQoJ2lucHV0W25hbWU9XCJkYXRhW3R4X2J3Ym9va2luZ21hbmFnZXJfZG9tYWluX21vZGVsX2VudHJ5XVsnICsgZW50cnlVaWQgKyAnXVt0aW1lc2xvdF1cIl0nKS52YWwoZXZlbnQuZXh0ZW5kZWRQcm9wcy51aWQpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgJCgnaW5wdXRbbmFtZT1cImRhdGFbdHhfYndib29raW5nbWFuYWdlcl9kb21haW5fbW9kZWxfZW50cnldWycgKyBlbnRyeVVpZCArICddW3N0YXJ0X2RhdGVdXCJdJykudmFsKGV2ZW50LnN0YXJ0LmdldFRpbWUoKSAvIDEwMDApO1xuICAgICAgICAgICAgJCgnaW5wdXRbbmFtZT1cImRhdGFbdHhfYndib29raW5nbWFuYWdlcl9kb21haW5fbW9kZWxfZW50cnldWycgKyBlbnRyeVVpZCArICddW2VuZF9kYXRlXVwiXScpLnZhbChldmVudC5lbmQuZ2V0VGltZSgpIC8gMTAwMCk7XG4gICAgICAgICAgICAkKCdzZWxlY3RbbmFtZT1cImRhdGFbdHhfYndib29raW5nbWFuYWdlcl9kb21haW5fbW9kZWxfZW50cnldWycgKyBlbnRyeVVpZCArICddW2NhbGVuZGFyXVwiXScpLnZhbChldmVudC5leHRlbmRlZFByb3BzLmNhbGVuZGFyKTtcbiAgICAgICAgICAgIC8vIHVwZGF0ZSBkYXRlIGxhYmVsXG4gICAgICAgICAgICBjb25zdCBmb3JtYXQgPSB7XG4gICAgICAgICAgICAgICAgd2Vla2RheTogJ3Nob3J0JyxcbiAgICAgICAgICAgICAgICBtb250aDogJzItZGlnaXQnLFxuICAgICAgICAgICAgICAgIGRheTogJzItZGlnaXQnLFxuICAgICAgICAgICAgICAgIHllYXI6ICdudW1lcmljJyxcbiAgICAgICAgICAgICAgICBob3VyOiAnMi1kaWdpdCcsXG4gICAgICAgICAgICAgICAgbWludXRlOiAnMi1kaWdpdCcsXG4gICAgICAgICAgICAgICAgdGltZVpvbmU6ICdVVEMnXG4gICAgICAgICAgICB9O1xuICAgICAgICAgICAgY29uc3Qgc3RhcnQgPSBJbnRsLkRhdGVUaW1lRm9ybWF0KHZpZXdTdGF0ZS5sYW5ndWFnZSwgZm9ybWF0KS5mb3JtYXQoZXZlbnQuc3RhcnQpO1xuICAgICAgICAgICAgY29uc3QgZW5kID0gSW50bC5EYXRlVGltZUZvcm1hdCh2aWV3U3RhdGUubGFuZ3VhZ2UsIGZvcm1hdCkuZm9ybWF0KGV2ZW50LmVuZCk7XG4gICAgICAgICAgICAkKCcjc2F2ZWRTdGFydERhdGUnKS5odG1sKHN0YXJ0KTtcbiAgICAgICAgICAgICQoJyNzYXZlZEVuZERhdGUnKS5odG1sKGVuZCk7XG4gICAgICAgIH1cbiAgICB9XG4gICAgcmV0dXJuIG5ldyBTZWxlY3RUaW1lc2xvdERhdGVzRWxlbWVudCgpO1xufSk7XG4iLCIvLyBUaGUgbW9kdWxlIGNhY2hlXG52YXIgX193ZWJwYWNrX21vZHVsZV9jYWNoZV9fID0ge307XG5cbi8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG5mdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuXHRpZihfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX19bbW9kdWxlSWRdKSB7XG5cdFx0cmV0dXJuIF9fd2VicGFja19tb2R1bGVfY2FjaGVfX1ttb2R1bGVJZF0uZXhwb3J0cztcblx0fVxuXHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuXHR2YXIgbW9kdWxlID0gX193ZWJwYWNrX21vZHVsZV9jYWNoZV9fW21vZHVsZUlkXSA9IHtcblx0XHQvLyBubyBtb2R1bGUuaWQgbmVlZGVkXG5cdFx0Ly8gbm8gbW9kdWxlLmxvYWRlZCBuZWVkZWRcblx0XHRleHBvcnRzOiB7fVxuXHR9O1xuXG5cdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuXHRfX3dlYnBhY2tfbW9kdWxlc19fW21vZHVsZUlkXShtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuXHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuXHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG59XG5cbiIsIi8vIG1vZHVsZSBleHBvcnRzIG11c3QgYmUgcmV0dXJuZWQgZnJvbSBydW50aW1lIHNvIGVudHJ5IGlubGluaW5nIGlzIGRpc2FibGVkXG4vLyBzdGFydHVwXG4vLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbnJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKFwiLi9SZXNvdXJjZXMvUHJpdmF0ZS9UeXBlU2NyaXB0L1NlbGVjdFRpbWVzbG90RGF0ZXNFbGVtZW50LnRzXCIpO1xuIl0sInNvdXJjZVJvb3QiOiIifQ==