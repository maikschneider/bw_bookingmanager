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
            this.pid = viewState.pid;
            // @TODO: most properties are in parsed json for sure, we could extend
            this.language = 'language' in viewState && viewState.language !== 'default' ? viewState.language : 'en';
            this.start = 'start' in viewState ? viewState.start : new Date();
            this.calendarView = viewState.calendarView;
            this.pastEntries = viewState.pastEntries;
            this.pastTimeslots = viewState.pastTimeslots;
            this.notBookableTimeslots = viewState.notBookableTimeslots;
            // stuff needed in modal
            this.futureEntries = 'futureEntries' in viewState && viewState.futureEntries === 'true';
            this.entryUid = 'entryUid' in viewState ? viewState.entryUid : null;
            this.calendar = 'calendar' in viewState ? viewState.calendar : null;
            this.timeslot = 'timeslot' in viewState ? viewState.timeslot : null;
            this.buttonSaveText = 'buttonSaveText' in viewState ? viewState.buttonSaveText : '';
            this.buttonCancelText = 'buttonCancelText' in viewState ? viewState.buttonCancelText : '';
            this.entryStart = 'entryStart' in viewState ? viewState.entryStart : null;
            this.entryEnd = 'entryEnd' in viewState ? viewState.entryEnd : null;
            // stuff needed for direct booking
            this.currentCalendars = viewState.currentCalendars;
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
            const entryUid = viewState.events.extraParams.entryUid;
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9UWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9bbmFtZV0vLi9SZXNvdXJjZXMvUHJpdmF0ZS9UeXBlU2NyaXB0L0JhY2tlbmRDYWxlbmRhclZpZXdTdGF0ZS50cyIsIndlYnBhY2s6Ly9UWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9bbmFtZV0vLi9SZXNvdXJjZXMvUHJpdmF0ZS9UeXBlU2NyaXB0L1NlbGVjdFRpbWVzbG90RGF0ZXNFbGVtZW50LnRzIiwid2VicGFjazovL1RZUE8zL0NNUy9Cd0Jvb2tpbmdtYW5hZ2VyL1tuYW1lXS93ZWJwYWNrL2Jvb3RzdHJhcCIsIndlYnBhY2s6Ly9UWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9bbmFtZV0vd2VicGFjay9zdGFydHVwIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiI7Ozs7Ozs7OztBQUFBLGlHQUFPLENBQUMsbUJBQVMsRUFBRSxPQUFTLENBQUMsbUNBQUU7QUFDL0I7QUFDQSxJQUFJLDhDQUE2QyxDQUFDLGNBQWMsRUFBQztBQUNqRTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGFBQWE7QUFDYjtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsMkJBQTJCLGtDQUFrQztBQUM3RDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLElBQUksZ0NBQWdDO0FBQ3BDLENBQUM7QUFBQSxrR0FBQzs7Ozs7Ozs7Ozs7QUN6RUYsaUdBQU8sQ0FBQyxtQkFBUyxFQUFFLE9BQVMsRUFBRSw0SUFBcUQsQ0FBQyxtQ0FBRTtBQUN0RjtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLENBQUM7QUFBQSxrR0FBQzs7Ozs7OztVQy9DRjtVQUNBOztVQUVBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTs7VUFFQTtVQUNBOztVQUVBO1VBQ0E7VUFDQTs7O1VDckJBO1VBQ0E7VUFDQTtVQUNBIiwiZmlsZSI6IlJlc291cmNlcy9QdWJsaWMvSmF2YVNjcmlwdC9TZWxlY3RUaW1lc2xvdERhdGVzRWxlbWVudC5qcyIsInNvdXJjZXNDb250ZW50IjpbImRlZmluZShbXCJyZXF1aXJlXCIsIFwiZXhwb3J0c1wiXSwgZnVuY3Rpb24gKHJlcXVpcmUsIGV4cG9ydHMpIHtcbiAgICBcInVzZSBzdHJpY3RcIjtcbiAgICBPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgXCJfX2VzTW9kdWxlXCIsIHsgdmFsdWU6IHRydWUgfSk7XG4gICAgY2xhc3MgQmFja2VuZENhbGVuZGFyIHtcbiAgICB9XG4gICAgY2xhc3MgQmFja2VuZENhbGVuZGFyVmlld1N0YXRlIHtcbiAgICAgICAgY29uc3RydWN0b3IoZWwpIHtcbiAgICAgICAgICAgIGlmICghZWwuaGFzQXR0cmlidXRlKCdkYXRhLXZpZXctc3RhdGUnKSkge1xuICAgICAgICAgICAgICAgIGNvbnNvbGUuZXJyb3IoJ0VsZW1lbnQgZG9lcyBub3QgaGF2ZSB2aWV3LXN0YXRlIGF0dHJpYnV0ZSEnKTtcbiAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBjb25zdCB2aWV3U3RhdGUgPSBKU09OLnBhcnNlKGVsLmdldEF0dHJpYnV0ZSgnZGF0YS12aWV3LXN0YXRlJykpO1xuICAgICAgICAgICAgdGhpcy5waWQgPSB2aWV3U3RhdGUucGlkO1xuICAgICAgICAgICAgLy8gQFRPRE86IG1vc3QgcHJvcGVydGllcyBhcmUgaW4gcGFyc2VkIGpzb24gZm9yIHN1cmUsIHdlIGNvdWxkIGV4dGVuZFxuICAgICAgICAgICAgdGhpcy5sYW5ndWFnZSA9ICdsYW5ndWFnZScgaW4gdmlld1N0YXRlICYmIHZpZXdTdGF0ZS5sYW5ndWFnZSAhPT0gJ2RlZmF1bHQnID8gdmlld1N0YXRlLmxhbmd1YWdlIDogJ2VuJztcbiAgICAgICAgICAgIHRoaXMuc3RhcnQgPSAnc3RhcnQnIGluIHZpZXdTdGF0ZSA/IHZpZXdTdGF0ZS5zdGFydCA6IG5ldyBEYXRlKCk7XG4gICAgICAgICAgICB0aGlzLmNhbGVuZGFyVmlldyA9IHZpZXdTdGF0ZS5jYWxlbmRhclZpZXc7XG4gICAgICAgICAgICB0aGlzLnBhc3RFbnRyaWVzID0gdmlld1N0YXRlLnBhc3RFbnRyaWVzO1xuICAgICAgICAgICAgdGhpcy5wYXN0VGltZXNsb3RzID0gdmlld1N0YXRlLnBhc3RUaW1lc2xvdHM7XG4gICAgICAgICAgICB0aGlzLm5vdEJvb2thYmxlVGltZXNsb3RzID0gdmlld1N0YXRlLm5vdEJvb2thYmxlVGltZXNsb3RzO1xuICAgICAgICAgICAgLy8gc3R1ZmYgbmVlZGVkIGluIG1vZGFsXG4gICAgICAgICAgICB0aGlzLmZ1dHVyZUVudHJpZXMgPSAnZnV0dXJlRW50cmllcycgaW4gdmlld1N0YXRlICYmIHZpZXdTdGF0ZS5mdXR1cmVFbnRyaWVzID09PSAndHJ1ZSc7XG4gICAgICAgICAgICB0aGlzLmVudHJ5VWlkID0gJ2VudHJ5VWlkJyBpbiB2aWV3U3RhdGUgPyB2aWV3U3RhdGUuZW50cnlVaWQgOiBudWxsO1xuICAgICAgICAgICAgdGhpcy5jYWxlbmRhciA9ICdjYWxlbmRhcicgaW4gdmlld1N0YXRlID8gdmlld1N0YXRlLmNhbGVuZGFyIDogbnVsbDtcbiAgICAgICAgICAgIHRoaXMudGltZXNsb3QgPSAndGltZXNsb3QnIGluIHZpZXdTdGF0ZSA/IHZpZXdTdGF0ZS50aW1lc2xvdCA6IG51bGw7XG4gICAgICAgICAgICB0aGlzLmJ1dHRvblNhdmVUZXh0ID0gJ2J1dHRvblNhdmVUZXh0JyBpbiB2aWV3U3RhdGUgPyB2aWV3U3RhdGUuYnV0dG9uU2F2ZVRleHQgOiAnJztcbiAgICAgICAgICAgIHRoaXMuYnV0dG9uQ2FuY2VsVGV4dCA9ICdidXR0b25DYW5jZWxUZXh0JyBpbiB2aWV3U3RhdGUgPyB2aWV3U3RhdGUuYnV0dG9uQ2FuY2VsVGV4dCA6ICcnO1xuICAgICAgICAgICAgdGhpcy5lbnRyeVN0YXJ0ID0gJ2VudHJ5U3RhcnQnIGluIHZpZXdTdGF0ZSA/IHZpZXdTdGF0ZS5lbnRyeVN0YXJ0IDogbnVsbDtcbiAgICAgICAgICAgIHRoaXMuZW50cnlFbmQgPSAnZW50cnlFbmQnIGluIHZpZXdTdGF0ZSA/IHZpZXdTdGF0ZS5lbnRyeUVuZCA6IG51bGw7XG4gICAgICAgICAgICAvLyBzdHVmZiBuZWVkZWQgZm9yIGRpcmVjdCBib29raW5nXG4gICAgICAgICAgICB0aGlzLmN1cnJlbnRDYWxlbmRhcnMgPSB2aWV3U3RhdGUuY3VycmVudENhbGVuZGFycztcbiAgICAgICAgICAgIHRoaXMuZXZlbnRzID0ge1xuICAgICAgICAgICAgICAgICd1cmwnOiBUWVBPMy5zZXR0aW5ncy5hamF4VXJsc1snYXBpX2NhbGVuZGFyX3Nob3cnXSxcbiAgICAgICAgICAgICAgICAnZXh0cmFQYXJhbXMnOiB7XG4gICAgICAgICAgICAgICAgICAgICdwaWQnOiB2aWV3U3RhdGUucGlkXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfTtcbiAgICAgICAgICAgIGlmICh0aGlzLmVudHJ5VWlkKSB7XG4gICAgICAgICAgICAgICAgdGhpcy5ldmVudHMuZXh0cmFQYXJhbXNbJ2VudHJ5VWlkJ10gPSB0aGlzLmVudHJ5VWlkO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgICAgIC8qKlxuICAgICAgICAgKiBVc2VkIGluIEJhY2tlbmRNb2R1bGVDYWxlbmRhciB0byBwZXJzaXN0IHRoZSBjdXJyZW50IGRpc3BsYXkgb2YgdmlldyB0eXBlIGFuZCBzZWxlY3RlZCBkYXRlXG4gICAgICAgICAqL1xuICAgICAgICBzYXZlQXNVc2VyVmlldygpIHtcbiAgICAgICAgICAgIGlmICh0aGlzLnNhdmVSZXF1ZXN0KSB7XG4gICAgICAgICAgICAgICAgdGhpcy5zYXZlUmVxdWVzdC5hYm9ydCgpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgdGhpcy5zYXZlUmVxdWVzdCA9ICQucG9zdChUWVBPMy5zZXR0aW5ncy5hamF4VXJsc1snYXBpX3VzZXJfc2V0dGluZyddLCB7XG4gICAgICAgICAgICAgICAgdmlld1N0YXRlOiB7XG4gICAgICAgICAgICAgICAgICAgIHBpZDogdGhpcy5waWQsXG4gICAgICAgICAgICAgICAgICAgIHN0YXJ0OiB0aGlzLnN0YXJ0LFxuICAgICAgICAgICAgICAgICAgICBjYWxlbmRhclZpZXc6IHRoaXMuY2FsZW5kYXJWaWV3LFxuICAgICAgICAgICAgICAgICAgICBwYXN0RW50cmllczogdGhpcy5wYXN0RW50cmllcyxcbiAgICAgICAgICAgICAgICAgICAgcGFzdFRpbWVzbG90czogdGhpcy5wYXN0VGltZXNsb3RzLFxuICAgICAgICAgICAgICAgICAgICBub3RCb29rYWJsZVRpbWVzbG90czogdGhpcy5ub3RCb29rYWJsZVRpbWVzbG90cyxcbiAgICAgICAgICAgICAgICAgICAgZnV0dXJlRW50cmllczogdGhpcy5mdXR1cmVFbnRyaWVzXG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICAgICAgaGFzRGlyZWN0Qm9va2luZ0NhbGVuZGFyKCkge1xuICAgICAgICAgICAgcmV0dXJuIHRoaXMuZ2V0Rmlyc3REaXJlY3RCb29rYWJsZUNhbGVuZGFyKCkgIT09IG51bGw7XG4gICAgICAgIH1cbiAgICAgICAgZ2V0Rmlyc3REaXJlY3RCb29rYWJsZUNhbGVuZGFyKCkge1xuICAgICAgICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCB0aGlzLmN1cnJlbnRDYWxlbmRhcnMubGVuZ3RoOyBpKyspIHtcbiAgICAgICAgICAgICAgICBpZiAodGhpcy5jdXJyZW50Q2FsZW5kYXJzW2ldLmRpcmVjdEJvb2tpbmcpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHRoaXMuY3VycmVudENhbGVuZGFyc1tpXTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICByZXR1cm4gbnVsbDtcbiAgICAgICAgfVxuICAgIH1cbiAgICBleHBvcnRzLkJhY2tlbmRDYWxlbmRhclZpZXdTdGF0ZSA9IEJhY2tlbmRDYWxlbmRhclZpZXdTdGF0ZTtcbn0pO1xuIiwiZGVmaW5lKFtcInJlcXVpcmVcIiwgXCJleHBvcnRzXCIsIFwiVFlQTzMvQ01TL0J3Qm9va2luZ21hbmFnZXIvQmFja2VuZENhbGVuZGFyVmlld1N0YXRlXCJdLCBmdW5jdGlvbiAocmVxdWlyZSwgZXhwb3J0cywgQmFja2VuZENhbGVuZGFyVmlld1N0YXRlXzEpIHtcbiAgICBcInVzZSBzdHJpY3RcIjtcbiAgICAvKipcbiAgICAgKiBNb2R1bGU6IFRZUE8zL0NNUy9Cd0Jvb2tpbmdtYW5hZ2VyL1NlbGVjdFRpbWVzbG90RGF0ZXNFbGVtZW50XG4gICAgICpcbiAgICAgKiBAZXhwb3J0cyBUWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9TZWxlY3RUaW1lc2xvdERhdGVzRWxlbWVudFxuICAgICAqL1xuICAgIGNsYXNzIFNlbGVjdFRpbWVzbG90RGF0ZXNFbGVtZW50IHtcbiAgICAgICAgY29uc3RydWN0b3IoKSB7XG4gICAgICAgICAgICBjb25zdCBidXR0b24gPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZW50cnktZGF0ZS1zZWxlY3QtYnV0dG9uJyk7XG4gICAgICAgICAgICAkKCcjZW50cnktZGF0ZS1zZWxlY3QtYnV0dG9uJykub24oJ2NsaWNrJywgdGhpcy5vbkJ1dHRvbkNsaWNrLmJpbmQodGhpcykpO1xuICAgICAgICAgICAgcGFyZW50LndpbmRvdy5CYWNrZW5kTW9kYWxDYWxlbmRhci5vblNhdmUgPSB0aGlzLm9uTW9kYWxTYXZlLmJpbmQodGhpcyk7XG4gICAgICAgIH1cbiAgICAgICAgb25CdXR0b25DbGljayhlKSB7XG4gICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICBjb25zdCBidXR0b24gPSBlLmN1cnJlbnRUYXJnZXQ7XG4gICAgICAgICAgICBwYXJlbnQud2luZG93LkJhY2tlbmRNb2RhbENhbGVuZGFyLnZpZXdTdGF0ZSA9IG5ldyBCYWNrZW5kQ2FsZW5kYXJWaWV3U3RhdGVfMS5CYWNrZW5kQ2FsZW5kYXJWaWV3U3RhdGUoYnV0dG9uKTtcbiAgICAgICAgICAgIHBhcmVudC53aW5kb3cuQmFja2VuZE1vZGFsQ2FsZW5kYXIub3Blbk1vZGFsKCk7XG4gICAgICAgIH1cbiAgICAgICAgb25Nb2RhbFNhdmUoZXZlbnQsIHZpZXdTdGF0ZSkge1xuICAgICAgICAgICAgLy8gdXBkYXRlIGJ1dHRvbiBqc29uXG4gICAgICAgICAgICBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZW50cnktZGF0ZS1zZWxlY3QtYnV0dG9uJykuc2V0QXR0cmlidXRlKCdkYXRhLXZpZXctc3RhdGUnLCBKU09OLnN0cmluZ2lmeSh2aWV3U3RhdGUpKTtcbiAgICAgICAgICAgIC8vIHNhdmUgdG8gbmV3IGZvcm1cbiAgICAgICAgICAgIGNvbnN0IGVudHJ5VWlkID0gdmlld1N0YXRlLmV2ZW50cy5leHRyYVBhcmFtcy5lbnRyeVVpZDtcbiAgICAgICAgICAgIGlmIChldmVudC5leHRlbmRlZFByb3BzLm1vZGVsID09PSAnVGltZXNsb3QnKSB7XG4gICAgICAgICAgICAgICAgJCgnaW5wdXRbbmFtZT1cImRhdGFbdHhfYndib29raW5nbWFuYWdlcl9kb21haW5fbW9kZWxfZW50cnldWycgKyBlbnRyeVVpZCArICddW3RpbWVzbG90XVwiXScpLnZhbChldmVudC5leHRlbmRlZFByb3BzLnVpZCk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICAkKCdpbnB1dFtuYW1lPVwiZGF0YVt0eF9id2Jvb2tpbmdtYW5hZ2VyX2RvbWFpbl9tb2RlbF9lbnRyeV1bJyArIGVudHJ5VWlkICsgJ11bc3RhcnRfZGF0ZV1cIl0nKS52YWwoZXZlbnQuc3RhcnQuZ2V0VGltZSgpIC8gMTAwMCk7XG4gICAgICAgICAgICAkKCdpbnB1dFtuYW1lPVwiZGF0YVt0eF9id2Jvb2tpbmdtYW5hZ2VyX2RvbWFpbl9tb2RlbF9lbnRyeV1bJyArIGVudHJ5VWlkICsgJ11bZW5kX2RhdGVdXCJdJykudmFsKGV2ZW50LmVuZC5nZXRUaW1lKCkgLyAxMDAwKTtcbiAgICAgICAgICAgICQoJ3NlbGVjdFtuYW1lPVwiZGF0YVt0eF9id2Jvb2tpbmdtYW5hZ2VyX2RvbWFpbl9tb2RlbF9lbnRyeV1bJyArIGVudHJ5VWlkICsgJ11bY2FsZW5kYXJdXCJdJykudmFsKGV2ZW50LmV4dGVuZGVkUHJvcHMuY2FsZW5kYXIpO1xuICAgICAgICAgICAgLy8gdXBkYXRlIGRhdGUgbGFiZWxcbiAgICAgICAgICAgIGNvbnN0IGZvcm1hdCA9IHtcbiAgICAgICAgICAgICAgICB3ZWVrZGF5OiAnc2hvcnQnLFxuICAgICAgICAgICAgICAgIG1vbnRoOiAnMi1kaWdpdCcsXG4gICAgICAgICAgICAgICAgZGF5OiAnMi1kaWdpdCcsXG4gICAgICAgICAgICAgICAgeWVhcjogJ251bWVyaWMnLFxuICAgICAgICAgICAgICAgIGhvdXI6ICcyLWRpZ2l0JyxcbiAgICAgICAgICAgICAgICBtaW51dGU6ICcyLWRpZ2l0JyxcbiAgICAgICAgICAgICAgICB0aW1lWm9uZTogJ1VUQydcbiAgICAgICAgICAgIH07XG4gICAgICAgICAgICBjb25zdCBzdGFydCA9IEludGwuRGF0ZVRpbWVGb3JtYXQodmlld1N0YXRlLmxhbmd1YWdlLCBmb3JtYXQpLmZvcm1hdChldmVudC5zdGFydCk7XG4gICAgICAgICAgICBjb25zdCBlbmQgPSBJbnRsLkRhdGVUaW1lRm9ybWF0KHZpZXdTdGF0ZS5sYW5ndWFnZSwgZm9ybWF0KS5mb3JtYXQoZXZlbnQuZW5kKTtcbiAgICAgICAgICAgICQoJyNzYXZlZFN0YXJ0RGF0ZScpLmh0bWwoc3RhcnQpO1xuICAgICAgICAgICAgJCgnI3NhdmVkRW5kRGF0ZScpLmh0bWwoZW5kKTtcbiAgICAgICAgfVxuICAgIH1cbiAgICByZXR1cm4gbmV3IFNlbGVjdFRpbWVzbG90RGF0ZXNFbGVtZW50KCk7XG59KTtcbiIsIi8vIFRoZSBtb2R1bGUgY2FjaGVcbnZhciBfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX18gPSB7fTtcblxuLy8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbmZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG5cdGlmKF9fd2VicGFja19tb2R1bGVfY2FjaGVfX1ttb2R1bGVJZF0pIHtcblx0XHRyZXR1cm4gX193ZWJwYWNrX21vZHVsZV9jYWNoZV9fW21vZHVsZUlkXS5leHBvcnRzO1xuXHR9XG5cdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG5cdHZhciBtb2R1bGUgPSBfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX19bbW9kdWxlSWRdID0ge1xuXHRcdC8vIG5vIG1vZHVsZS5pZCBuZWVkZWRcblx0XHQvLyBubyBtb2R1bGUubG9hZGVkIG5lZWRlZFxuXHRcdGV4cG9ydHM6IHt9XG5cdH07XG5cblx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG5cdF9fd2VicGFja19tb2R1bGVzX19bbW9kdWxlSWRdKG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG5cdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG5cdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbn1cblxuIiwiLy8gbW9kdWxlIGV4cG9ydHMgbXVzdCBiZSByZXR1cm5lZCBmcm9tIHJ1bnRpbWUgc28gZW50cnkgaW5saW5pbmcgaXMgZGlzYWJsZWRcbi8vIHN0YXJ0dXBcbi8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xucmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oXCIuL1Jlc291cmNlcy9Qcml2YXRlL1R5cGVTY3JpcHQvU2VsZWN0VGltZXNsb3REYXRlc0VsZW1lbnQudHNcIik7XG4iXSwic291cmNlUm9vdCI6IiJ9