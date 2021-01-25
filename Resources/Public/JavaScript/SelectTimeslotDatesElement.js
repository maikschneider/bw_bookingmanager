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
                        'calendar': this.calendar
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9UWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9bbmFtZV0vLi9SZXNvdXJjZXMvUHJpdmF0ZS9UeXBlU2NyaXB0L0JhY2tlbmRDYWxlbmRhclZpZXdTdGF0ZS50cyIsIndlYnBhY2s6Ly9UWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9bbmFtZV0vLi9SZXNvdXJjZXMvUHJpdmF0ZS9UeXBlU2NyaXB0L1NlbGVjdFRpbWVzbG90RGF0ZXNFbGVtZW50LnRzIiwid2VicGFjazovL1RZUE8zL0NNUy9Cd0Jvb2tpbmdtYW5hZ2VyL1tuYW1lXS93ZWJwYWNrL2Jvb3RzdHJhcCIsIndlYnBhY2s6Ly9UWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9bbmFtZV0vd2VicGFjay9zdGFydHVwIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiI7Ozs7Ozs7OztBQUFBLGlHQUFPLENBQUMsbUJBQVMsRUFBRSxPQUFTLENBQUMsbUNBQUU7QUFDL0I7QUFDQSxJQUFJLDhDQUE2QyxDQUFDLGNBQWMsRUFBQztBQUNqRTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGFBQWE7QUFDYjtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsMkJBQTJCLGtDQUFrQztBQUM3RDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLElBQUksZ0NBQWdDO0FBQ3BDLENBQUM7QUFBQSxrR0FBQzs7Ozs7Ozs7Ozs7QUM1RUYsaUdBQU8sQ0FBQyxtQkFBUyxFQUFFLE9BQVMsRUFBRSw0SUFBcUQsQ0FBQyxtQ0FBRTtBQUN0RjtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLENBQUM7QUFBQSxrR0FBQzs7Ozs7OztVQy9DRjtVQUNBOztVQUVBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTs7VUFFQTtVQUNBOztVQUVBO1VBQ0E7VUFDQTs7O1VDckJBO1VBQ0E7VUFDQTtVQUNBIiwiZmlsZSI6IlJlc291cmNlcy9QdWJsaWMvSmF2YVNjcmlwdC9TZWxlY3RUaW1lc2xvdERhdGVzRWxlbWVudC5qcyIsInNvdXJjZXNDb250ZW50IjpbImRlZmluZShbXCJyZXF1aXJlXCIsIFwiZXhwb3J0c1wiXSwgZnVuY3Rpb24gKHJlcXVpcmUsIGV4cG9ydHMpIHtcbiAgICBcInVzZSBzdHJpY3RcIjtcbiAgICBPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgXCJfX2VzTW9kdWxlXCIsIHsgdmFsdWU6IHRydWUgfSk7XG4gICAgY2xhc3MgQmFja2VuZENhbGVuZGFyIHtcbiAgICB9XG4gICAgY2xhc3MgQmFja2VuZENhbGVuZGFyVmlld1N0YXRlIHtcbiAgICAgICAgY29uc3RydWN0b3IoZWwpIHtcbiAgICAgICAgICAgIGlmICghZWwuaGFzQXR0cmlidXRlKCdkYXRhLXZpZXctc3RhdGUnKSkge1xuICAgICAgICAgICAgICAgIGNvbnNvbGUuZXJyb3IoJ0VsZW1lbnQgZG9lcyBub3QgaGF2ZSB2aWV3LXN0YXRlIGF0dHJpYnV0ZSEnKTtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBjb25zdCB2aWV3U3RhdGUgPSBKU09OLnBhcnNlKGVsLmdldEF0dHJpYnV0ZSgnZGF0YS12aWV3LXN0YXRlJykpO1xuICAgICAgICAgICAgLy8gQFRPRE86IG1vc3QgcHJvcGVydGllcyBhcmUgaW4gcGFyc2VkIGpzb24gZm9yIHN1cmUsIHdlIGNvdWxkIGV4dGVuZFxuICAgICAgICAgICAgdGhpcy5waWQgPSB2aWV3U3RhdGUucGlkO1xuICAgICAgICAgICAgdGhpcy5sYW5ndWFnZSA9ICdsYW5ndWFnZScgaW4gdmlld1N0YXRlICYmIHZpZXdTdGF0ZS5sYW5ndWFnZSAhPT0gJ2RlZmF1bHQnID8gdmlld1N0YXRlLmxhbmd1YWdlIDogJ2VuJztcbiAgICAgICAgICAgIHRoaXMuc3RhcnQgPSAnc3RhcnQnIGluIHZpZXdTdGF0ZSA/IHZpZXdTdGF0ZS5zdGFydCA6IG5ldyBEYXRlKCk7XG4gICAgICAgICAgICB0aGlzLmNhbGVuZGFyVmlldyA9IHZpZXdTdGF0ZS5jYWxlbmRhclZpZXc7XG4gICAgICAgICAgICB0aGlzLnBhc3RFbnRyaWVzID0gdmlld1N0YXRlLnBhc3RFbnRyaWVzO1xuICAgICAgICAgICAgdGhpcy5wYXN0VGltZXNsb3RzID0gdmlld1N0YXRlLnBhc3RUaW1lc2xvdHM7XG4gICAgICAgICAgICB0aGlzLm5vdEJvb2thYmxlVGltZXNsb3RzID0gdmlld1N0YXRlLm5vdEJvb2thYmxlVGltZXNsb3RzO1xuICAgICAgICAgICAgdGhpcy5mdXR1cmVFbnRyaWVzID0gJ2Z1dHVyZUVudHJpZXMnIGluIHZpZXdTdGF0ZSAmJiB2aWV3U3RhdGUuZnV0dXJlRW50cmllcyA9PT0gJ3RydWUnO1xuICAgICAgICAgICAgdGhpcy5lbnRyeVVpZCA9ICdlbnRyeVVpZCcgaW4gdmlld1N0YXRlID8gdmlld1N0YXRlLmVudHJ5VWlkIDogbnVsbDtcbiAgICAgICAgICAgIHRoaXMuY2FsZW5kYXIgPSAnY2FsZW5kYXInIGluIHZpZXdTdGF0ZSA/IHZpZXdTdGF0ZS5jYWxlbmRhciA6IG51bGw7XG4gICAgICAgICAgICB0aGlzLnRpbWVzbG90ID0gJ3RpbWVzbG90JyBpbiB2aWV3U3RhdGUgPyB2aWV3U3RhdGUudGltZXNsb3QgOiBudWxsO1xuICAgICAgICAgICAgdGhpcy5idXR0b25TYXZlVGV4dCA9ICdidXR0b25TYXZlVGV4dCcgaW4gdmlld1N0YXRlID8gdmlld1N0YXRlLmJ1dHRvblNhdmVUZXh0IDogJyc7XG4gICAgICAgICAgICB0aGlzLmJ1dHRvbkNhbmNlbFRleHQgPSAnYnV0dG9uQ2FuY2VsVGV4dCcgaW4gdmlld1N0YXRlID8gdmlld1N0YXRlLmJ1dHRvbkNhbmNlbFRleHQgOiAnJztcbiAgICAgICAgICAgIHRoaXMuZW50cnlTdGFydCA9ICdlbnRyeVN0YXJ0JyBpbiB2aWV3U3RhdGUgPyB2aWV3U3RhdGUuZW50cnlTdGFydCA6IG51bGw7XG4gICAgICAgICAgICB0aGlzLmVudHJ5RW5kID0gJ2VudHJ5RW5kJyBpbiB2aWV3U3RhdGUgPyB2aWV3U3RhdGUuZW50cnlFbmQgOiBudWxsO1xuICAgICAgICAgICAgdGhpcy5jdXJyZW50Q2FsZW5kYXJzID0gdmlld1N0YXRlLmN1cnJlbnRDYWxlbmRhcnM7XG4gICAgICAgICAgICB0aGlzLmV2ZW50cyA9IHtcbiAgICAgICAgICAgICAgICAndXJsJzogVFlQTzMuc2V0dGluZ3MuYWpheFVybHNbJ2FwaV9jYWxlbmRhcl9zaG93J10sXG4gICAgICAgICAgICAgICAgJ2V4dHJhUGFyYW1zJzogKCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICBjb25zdCBlbnRyeVN0YXJ0ID0gdGhpcy5lbnRyeVN0YXJ0ID8gKG5ldyBEYXRlKHRoaXMuZW50cnlTdGFydCkpLmdldFRpbWUoKSAvIDEwMDAgOiBudWxsO1xuICAgICAgICAgICAgICAgICAgICBjb25zdCBlbnRyeUVuZCA9IHRoaXMuZW50cnlFbmQgPyAobmV3IERhdGUodGhpcy5lbnRyeUVuZCkpLmdldFRpbWUoKSAvIDEwMDAgOiBudWxsO1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgJ3BpZCc6IHRoaXMucGlkLFxuICAgICAgICAgICAgICAgICAgICAgICAgJ2VudHJ5VWlkJzogdGhpcy5lbnRyeVVpZCxcbiAgICAgICAgICAgICAgICAgICAgICAgICdlbnRyeVN0YXJ0JzogZW50cnlTdGFydCxcbiAgICAgICAgICAgICAgICAgICAgICAgICdlbnRyeUVuZCc6IGVudHJ5RW5kLFxuICAgICAgICAgICAgICAgICAgICAgICAgJ2NhbGVuZGFyJzogdGhpcy5jYWxlbmRhclxuICAgICAgICAgICAgICAgICAgICB9O1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH07XG4gICAgICAgIH1cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFVzZWQgaW4gQmFja2VuZE1vZHVsZUNhbGVuZGFyIHRvIHBlcnNpc3QgdGhlIGN1cnJlbnQgZGlzcGxheSBvZiB2aWV3IHR5cGUgYW5kIHNlbGVjdGVkIGRhdGVcbiAgICAgICAgICovXG4gICAgICAgIHNhdmVBc1VzZXJWaWV3KCkge1xuICAgICAgICAgICAgaWYgKHRoaXMuc2F2ZVJlcXVlc3QpIHtcbiAgICAgICAgICAgICAgICB0aGlzLnNhdmVSZXF1ZXN0LmFib3J0KCk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICB0aGlzLnNhdmVSZXF1ZXN0ID0gJC5wb3N0KFRZUE8zLnNldHRpbmdzLmFqYXhVcmxzWydhcGlfdXNlcl9zZXR0aW5nJ10sIHtcbiAgICAgICAgICAgICAgICB2aWV3U3RhdGU6IHtcbiAgICAgICAgICAgICAgICAgICAgcGlkOiB0aGlzLnBpZCxcbiAgICAgICAgICAgICAgICAgICAgc3RhcnQ6IHRoaXMuc3RhcnQsXG4gICAgICAgICAgICAgICAgICAgIGNhbGVuZGFyVmlldzogdGhpcy5jYWxlbmRhclZpZXcsXG4gICAgICAgICAgICAgICAgICAgIHBhc3RFbnRyaWVzOiB0aGlzLnBhc3RFbnRyaWVzLFxuICAgICAgICAgICAgICAgICAgICBwYXN0VGltZXNsb3RzOiB0aGlzLnBhc3RUaW1lc2xvdHMsXG4gICAgICAgICAgICAgICAgICAgIG5vdEJvb2thYmxlVGltZXNsb3RzOiB0aGlzLm5vdEJvb2thYmxlVGltZXNsb3RzLFxuICAgICAgICAgICAgICAgICAgICBmdXR1cmVFbnRyaWVzOiB0aGlzLmZ1dHVyZUVudHJpZXNcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuICAgICAgICBoYXNEaXJlY3RCb29raW5nQ2FsZW5kYXIoKSB7XG4gICAgICAgICAgICByZXR1cm4gdGhpcy5nZXRGaXJzdERpcmVjdEJvb2thYmxlQ2FsZW5kYXIoKSAhPT0gbnVsbDtcbiAgICAgICAgfVxuICAgICAgICBnZXRGaXJzdERpcmVjdEJvb2thYmxlQ2FsZW5kYXIoKSB7XG4gICAgICAgICAgICBmb3IgKGxldCBpID0gMDsgaSA8IHRoaXMuY3VycmVudENhbGVuZGFycy5sZW5ndGg7IGkrKykge1xuICAgICAgICAgICAgICAgIGlmICh0aGlzLmN1cnJlbnRDYWxlbmRhcnNbaV0uZGlyZWN0Qm9va2luZykge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm4gdGhpcy5jdXJyZW50Q2FsZW5kYXJzW2ldO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHJldHVybiBudWxsO1xuICAgICAgICB9XG4gICAgfVxuICAgIGV4cG9ydHMuQmFja2VuZENhbGVuZGFyVmlld1N0YXRlID0gQmFja2VuZENhbGVuZGFyVmlld1N0YXRlO1xufSk7XG4iLCJkZWZpbmUoW1wicmVxdWlyZVwiLCBcImV4cG9ydHNcIiwgXCJUWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9CYWNrZW5kQ2FsZW5kYXJWaWV3U3RhdGVcIl0sIGZ1bmN0aW9uIChyZXF1aXJlLCBleHBvcnRzLCBCYWNrZW5kQ2FsZW5kYXJWaWV3U3RhdGVfMSkge1xuICAgIFwidXNlIHN0cmljdFwiO1xuICAgIC8qKlxuICAgICAqIE1vZHVsZTogVFlQTzMvQ01TL0J3Qm9va2luZ21hbmFnZXIvU2VsZWN0VGltZXNsb3REYXRlc0VsZW1lbnRcbiAgICAgKlxuICAgICAqIEBleHBvcnRzIFRZUE8zL0NNUy9Cd0Jvb2tpbmdtYW5hZ2VyL1NlbGVjdFRpbWVzbG90RGF0ZXNFbGVtZW50XG4gICAgICovXG4gICAgY2xhc3MgU2VsZWN0VGltZXNsb3REYXRlc0VsZW1lbnQge1xuICAgICAgICBjb25zdHJ1Y3RvcigpIHtcbiAgICAgICAgICAgIGNvbnN0IGJ1dHRvbiA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdlbnRyeS1kYXRlLXNlbGVjdC1idXR0b24nKTtcbiAgICAgICAgICAgICQoJyNlbnRyeS1kYXRlLXNlbGVjdC1idXR0b24nKS5vbignY2xpY2snLCB0aGlzLm9uQnV0dG9uQ2xpY2suYmluZCh0aGlzKSk7XG4gICAgICAgICAgICBwYXJlbnQud2luZG93LkJhY2tlbmRNb2RhbENhbGVuZGFyLm9uU2F2ZSA9IHRoaXMub25Nb2RhbFNhdmUuYmluZCh0aGlzKTtcbiAgICAgICAgfVxuICAgICAgICBvbkJ1dHRvbkNsaWNrKGUpIHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIGNvbnN0IGJ1dHRvbiA9IGUuY3VycmVudFRhcmdldDtcbiAgICAgICAgICAgIHBhcmVudC53aW5kb3cuQmFja2VuZE1vZGFsQ2FsZW5kYXIudmlld1N0YXRlID0gbmV3IEJhY2tlbmRDYWxlbmRhclZpZXdTdGF0ZV8xLkJhY2tlbmRDYWxlbmRhclZpZXdTdGF0ZShidXR0b24pO1xuICAgICAgICAgICAgcGFyZW50LndpbmRvdy5CYWNrZW5kTW9kYWxDYWxlbmRhci5vcGVuTW9kYWwoKTtcbiAgICAgICAgfVxuICAgICAgICBvbk1vZGFsU2F2ZShldmVudCwgdmlld1N0YXRlKSB7XG4gICAgICAgICAgICAvLyB1cGRhdGUgYnV0dG9uIGpzb25cbiAgICAgICAgICAgIGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdlbnRyeS1kYXRlLXNlbGVjdC1idXR0b24nKS5zZXRBdHRyaWJ1dGUoJ2RhdGEtdmlldy1zdGF0ZScsIEpTT04uc3RyaW5naWZ5KHZpZXdTdGF0ZSkpO1xuICAgICAgICAgICAgLy8gc2F2ZSB0byBuZXcgZm9ybVxuICAgICAgICAgICAgY29uc3QgZW50cnlVaWQgPSB2aWV3U3RhdGUuZW50cnlVaWQ7XG4gICAgICAgICAgICBpZiAoZXZlbnQuZXh0ZW5kZWRQcm9wcy5tb2RlbCA9PT0gJ1RpbWVzbG90Jykge1xuICAgICAgICAgICAgICAgICQoJ2lucHV0W25hbWU9XCJkYXRhW3R4X2J3Ym9va2luZ21hbmFnZXJfZG9tYWluX21vZGVsX2VudHJ5XVsnICsgZW50cnlVaWQgKyAnXVt0aW1lc2xvdF1cIl0nKS52YWwoZXZlbnQuZXh0ZW5kZWRQcm9wcy51aWQpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgJCgnaW5wdXRbbmFtZT1cImRhdGFbdHhfYndib29raW5nbWFuYWdlcl9kb21haW5fbW9kZWxfZW50cnldWycgKyBlbnRyeVVpZCArICddW3N0YXJ0X2RhdGVdXCJdJykudmFsKGV2ZW50LnN0YXJ0LmdldFRpbWUoKSAvIDEwMDApO1xuICAgICAgICAgICAgJCgnaW5wdXRbbmFtZT1cImRhdGFbdHhfYndib29raW5nbWFuYWdlcl9kb21haW5fbW9kZWxfZW50cnldWycgKyBlbnRyeVVpZCArICddW2VuZF9kYXRlXVwiXScpLnZhbChldmVudC5lbmQuZ2V0VGltZSgpIC8gMTAwMCk7XG4gICAgICAgICAgICAkKCdzZWxlY3RbbmFtZT1cImRhdGFbdHhfYndib29raW5nbWFuYWdlcl9kb21haW5fbW9kZWxfZW50cnldWycgKyBlbnRyeVVpZCArICddW2NhbGVuZGFyXVwiXScpLnZhbChldmVudC5leHRlbmRlZFByb3BzLmNhbGVuZGFyKTtcbiAgICAgICAgICAgIC8vIHVwZGF0ZSBkYXRlIGxhYmVsXG4gICAgICAgICAgICBjb25zdCBmb3JtYXQgPSB7XG4gICAgICAgICAgICAgICAgd2Vla2RheTogJ3Nob3J0JyxcbiAgICAgICAgICAgICAgICBtb250aDogJzItZGlnaXQnLFxuICAgICAgICAgICAgICAgIGRheTogJzItZGlnaXQnLFxuICAgICAgICAgICAgICAgIHllYXI6ICdudW1lcmljJyxcbiAgICAgICAgICAgICAgICBob3VyOiAnMi1kaWdpdCcsXG4gICAgICAgICAgICAgICAgbWludXRlOiAnMi1kaWdpdCcsXG4gICAgICAgICAgICAgICAgdGltZVpvbmU6ICdVVEMnXG4gICAgICAgICAgICB9O1xuICAgICAgICAgICAgY29uc3Qgc3RhcnQgPSBJbnRsLkRhdGVUaW1lRm9ybWF0KHZpZXdTdGF0ZS5sYW5ndWFnZSwgZm9ybWF0KS5mb3JtYXQoZXZlbnQuc3RhcnQpO1xuICAgICAgICAgICAgY29uc3QgZW5kID0gSW50bC5EYXRlVGltZUZvcm1hdCh2aWV3U3RhdGUubGFuZ3VhZ2UsIGZvcm1hdCkuZm9ybWF0KGV2ZW50LmVuZCk7XG4gICAgICAgICAgICAkKCcjc2F2ZWRTdGFydERhdGUnKS5odG1sKHN0YXJ0KTtcbiAgICAgICAgICAgICQoJyNzYXZlZEVuZERhdGUnKS5odG1sKGVuZCk7XG4gICAgICAgIH1cbiAgICB9XG4gICAgcmV0dXJuIG5ldyBTZWxlY3RUaW1lc2xvdERhdGVzRWxlbWVudCgpO1xufSk7XG4iLCIvLyBUaGUgbW9kdWxlIGNhY2hlXG52YXIgX193ZWJwYWNrX21vZHVsZV9jYWNoZV9fID0ge307XG5cbi8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG5mdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuXHRpZihfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX19bbW9kdWxlSWRdKSB7XG5cdFx0cmV0dXJuIF9fd2VicGFja19tb2R1bGVfY2FjaGVfX1ttb2R1bGVJZF0uZXhwb3J0cztcblx0fVxuXHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuXHR2YXIgbW9kdWxlID0gX193ZWJwYWNrX21vZHVsZV9jYWNoZV9fW21vZHVsZUlkXSA9IHtcblx0XHQvLyBubyBtb2R1bGUuaWQgbmVlZGVkXG5cdFx0Ly8gbm8gbW9kdWxlLmxvYWRlZCBuZWVkZWRcblx0XHRleHBvcnRzOiB7fVxuXHR9O1xuXG5cdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuXHRfX3dlYnBhY2tfbW9kdWxlc19fW21vZHVsZUlkXShtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuXHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuXHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG59XG5cbiIsIi8vIG1vZHVsZSBleHBvcnRzIG11c3QgYmUgcmV0dXJuZWQgZnJvbSBydW50aW1lIHNvIGVudHJ5IGlubGluaW5nIGlzIGRpc2FibGVkXG4vLyBzdGFydHVwXG4vLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbnJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKFwiLi9SZXNvdXJjZXMvUHJpdmF0ZS9UeXBlU2NyaXB0L1NlbGVjdFRpbWVzbG90RGF0ZXNFbGVtZW50LnRzXCIpO1xuIl0sInNvdXJjZVJvb3QiOiIifQ==