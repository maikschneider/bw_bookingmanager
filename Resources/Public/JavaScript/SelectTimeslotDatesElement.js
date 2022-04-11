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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9UWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9bbmFtZV0vLi9SZXNvdXJjZXMvUHJpdmF0ZS9UeXBlU2NyaXB0L0JhY2tlbmRDYWxlbmRhclZpZXdTdGF0ZS50cyIsIndlYnBhY2s6Ly9UWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9bbmFtZV0vLi9SZXNvdXJjZXMvUHJpdmF0ZS9UeXBlU2NyaXB0L1NlbGVjdFRpbWVzbG90RGF0ZXNFbGVtZW50LnRzIiwid2VicGFjazovL1RZUE8zL0NNUy9Cd0Jvb2tpbmdtYW5hZ2VyL1tuYW1lXS93ZWJwYWNrL2Jvb3RzdHJhcCIsIndlYnBhY2s6Ly9UWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9bbmFtZV0vd2VicGFjay9zdGFydHVwIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiI7Ozs7Ozs7OztBQUFBLGlHQUFPLENBQUMsbUJBQVMsRUFBRSxPQUFTLENBQUMsbUNBQUU7QUFDL0I7QUFDQSxJQUFJLDhDQUE2QyxDQUFDLGNBQWMsRUFBQztBQUNqRTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxhQUFhO0FBQ2I7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLDJCQUEyQixrQ0FBa0M7QUFDN0Q7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxJQUFJLGdDQUFnQztBQUNwQyxDQUFDO0FBQUEsa0dBQUM7Ozs7Ozs7Ozs7O0FDakZGLGlHQUFPLENBQUMsbUJBQVMsRUFBRSxPQUFTLEVBQUUsNElBQXFELENBQUMsbUNBQUU7QUFDdEY7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsQ0FBQztBQUFBLGtHQUFDOzs7Ozs7O1VDakRGO1VBQ0E7O1VBRUE7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBOztVQUVBO1VBQ0E7O1VBRUE7VUFDQTtVQUNBOzs7VUNyQkE7VUFDQTtVQUNBO1VBQ0EiLCJmaWxlIjoiUmVzb3VyY2VzL1B1YmxpYy9KYXZhU2NyaXB0L1NlbGVjdFRpbWVzbG90RGF0ZXNFbGVtZW50LmpzIiwic291cmNlc0NvbnRlbnQiOlsiZGVmaW5lKFtcInJlcXVpcmVcIiwgXCJleHBvcnRzXCJdLCBmdW5jdGlvbiAocmVxdWlyZSwgZXhwb3J0cykge1xuICAgIFwidXNlIHN0cmljdFwiO1xuICAgIE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBcIl9fZXNNb2R1bGVcIiwgeyB2YWx1ZTogdHJ1ZSB9KTtcbiAgICBjbGFzcyBCYWNrZW5kQ2FsZW5kYXIge1xuICAgIH1cbiAgICBjbGFzcyBCYWNrZW5kQ2FsZW5kYXJWaWV3U3RhdGUge1xuICAgICAgICBjb25zdHJ1Y3RvcihlbCkge1xuICAgICAgICAgICAgaWYgKCFlbC5oYXNBdHRyaWJ1dGUoJ2RhdGEtdmlldy1zdGF0ZScpKSB7XG4gICAgICAgICAgICAgICAgY29uc29sZS5lcnJvcignRWxlbWVudCBkb2VzIG5vdCBoYXZlIHZpZXctc3RhdGUgYXR0cmlidXRlIScpO1xuICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGNvbnN0IHZpZXdTdGF0ZSA9IEpTT04ucGFyc2UoZWwuZ2V0QXR0cmlidXRlKCdkYXRhLXZpZXctc3RhdGUnKSk7XG4gICAgICAgICAgICAvLyBAVE9ETzogbW9zdCBwcm9wZXJ0aWVzIGFyZSBpbiBwYXJzZWQganNvbiBmb3Igc3VyZSwgd2UgY291bGQgZXh0ZW5kXG4gICAgICAgICAgICB0aGlzLnBpZCA9IHZpZXdTdGF0ZS5waWQ7XG4gICAgICAgICAgICB0aGlzLmxhbmd1YWdlID0gJ2xhbmd1YWdlJyBpbiB2aWV3U3RhdGUgJiYgdmlld1N0YXRlLmxhbmd1YWdlICE9PSAnZGVmYXVsdCcgPyB2aWV3U3RhdGUubGFuZ3VhZ2UgOiAnZW4nO1xuICAgICAgICAgICAgdGhpcy5zdGFydCA9ICdzdGFydCcgaW4gdmlld1N0YXRlID8gdmlld1N0YXRlLnN0YXJ0IDogbmV3IERhdGUoKTtcbiAgICAgICAgICAgIHRoaXMuY2FsZW5kYXJWaWV3ID0gdmlld1N0YXRlLmNhbGVuZGFyVmlldztcbiAgICAgICAgICAgIHRoaXMucGFzdEVudHJpZXMgPSB2aWV3U3RhdGUucGFzdEVudHJpZXM7XG4gICAgICAgICAgICB0aGlzLnBhc3RUaW1lc2xvdHMgPSB2aWV3U3RhdGUucGFzdFRpbWVzbG90cztcbiAgICAgICAgICAgIHRoaXMubm90Qm9va2FibGVUaW1lc2xvdHMgPSB2aWV3U3RhdGUubm90Qm9va2FibGVUaW1lc2xvdHM7XG4gICAgICAgICAgICB0aGlzLmZ1dHVyZUVudHJpZXMgPSAnZnV0dXJlRW50cmllcycgaW4gdmlld1N0YXRlICYmIHZpZXdTdGF0ZS5mdXR1cmVFbnRyaWVzID09PSAndHJ1ZSc7XG4gICAgICAgICAgICB0aGlzLmVudHJ5VWlkID0gJ2VudHJ5VWlkJyBpbiB2aWV3U3RhdGUgPyB2aWV3U3RhdGUuZW50cnlVaWQgOiBudWxsO1xuICAgICAgICAgICAgdGhpcy5jYWxlbmRhciA9ICdjYWxlbmRhcicgaW4gdmlld1N0YXRlID8gdmlld1N0YXRlLmNhbGVuZGFyIDogbnVsbDtcbiAgICAgICAgICAgIHRoaXMudGltZXNsb3QgPSAndGltZXNsb3QnIGluIHZpZXdTdGF0ZSA/IHZpZXdTdGF0ZS50aW1lc2xvdCA6IG51bGw7XG4gICAgICAgICAgICB0aGlzLmJ1dHRvblNhdmVUZXh0ID0gJ2J1dHRvblNhdmVUZXh0JyBpbiB2aWV3U3RhdGUgPyB2aWV3U3RhdGUuYnV0dG9uU2F2ZVRleHQgOiAnJztcbiAgICAgICAgICAgIHRoaXMuYnV0dG9uQ2FuY2VsVGV4dCA9ICdidXR0b25DYW5jZWxUZXh0JyBpbiB2aWV3U3RhdGUgPyB2aWV3U3RhdGUuYnV0dG9uQ2FuY2VsVGV4dCA6ICcnO1xuICAgICAgICAgICAgdGhpcy5lbnRyeVN0YXJ0ID0gJ2VudHJ5U3RhcnQnIGluIHZpZXdTdGF0ZSA/IHZpZXdTdGF0ZS5lbnRyeVN0YXJ0IDogbnVsbDtcbiAgICAgICAgICAgIHRoaXMuZW50cnlFbmQgPSAnZW50cnlFbmQnIGluIHZpZXdTdGF0ZSA/IHZpZXdTdGF0ZS5lbnRyeUVuZCA6IG51bGw7XG4gICAgICAgICAgICB0aGlzLmN1cnJlbnRDYWxlbmRhcnMgPSB2aWV3U3RhdGUuY3VycmVudENhbGVuZGFycztcbiAgICAgICAgICAgIHRoaXMud2FybmluZ1RpdGxlID0gJ3dhcm5pbmdUaXRsZScgaW4gdmlld1N0YXRlID8gdmlld1N0YXRlLndhcm5pbmdUaXRsZSA6ICcnO1xuICAgICAgICAgICAgdGhpcy53YXJuaW5nVGV4dCA9ICd3YXJuaW5nVGV4dCcgaW4gdmlld1N0YXRlID8gdmlld1N0YXRlLndhcm5pbmdUZXh0IDogJyc7XG4gICAgICAgICAgICB0aGlzLndhcm5pbmdCdXR0b24gPSAnd2FybmluZ0J1dHRvbicgaW4gdmlld1N0YXRlID8gdmlld1N0YXRlLndhcm5pbmdCdXR0b24gOiAnJztcbiAgICAgICAgICAgIHRoaXMuY2FsZW5kYXJPcHRpb25zID0gJ2NhbGVuZGFyT3B0aW9ucycgaW4gdmlld1N0YXRlID8gdmlld1N0YXRlLmNhbGVuZGFyT3B0aW9ucyA6IHt9O1xuICAgICAgICAgICAgdGhpcy5ldmVudHMgPSB7XG4gICAgICAgICAgICAgICAgJ3VybCc6IFRZUE8zLnNldHRpbmdzLmFqYXhVcmxzWydhcGlfY2FsZW5kYXJfc2hvdyddLFxuICAgICAgICAgICAgICAgICdleHRyYVBhcmFtcyc6ICgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgZW50cnlTdGFydCA9IHRoaXMuZW50cnlTdGFydCA/IChuZXcgRGF0ZSh0aGlzLmVudHJ5U3RhcnQpKS5nZXRUaW1lKCkgLyAxMDAwIDogbnVsbDtcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgZW50cnlFbmQgPSB0aGlzLmVudHJ5RW5kID8gKG5ldyBEYXRlKHRoaXMuZW50cnlFbmQpKS5nZXRUaW1lKCkgLyAxMDAwIDogbnVsbDtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICdwaWQnOiB0aGlzLnBpZCxcbiAgICAgICAgICAgICAgICAgICAgICAgICdlbnRyeVVpZCc6IHRoaXMuZW50cnlVaWQsXG4gICAgICAgICAgICAgICAgICAgICAgICAnZW50cnlTdGFydCc6IGVudHJ5U3RhcnQsXG4gICAgICAgICAgICAgICAgICAgICAgICAnZW50cnlFbmQnOiBlbnRyeUVuZCxcbiAgICAgICAgICAgICAgICAgICAgICAgICdjYWxlbmRhcic6IHRoaXMuY2FsZW5kYXIsXG4gICAgICAgICAgICAgICAgICAgICAgICAndGltZXNsb3QnOiB0aGlzLnRpbWVzbG90XG4gICAgICAgICAgICAgICAgICAgIH07XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfTtcbiAgICAgICAgfVxuICAgICAgICAvKipcbiAgICAgICAgICogVXNlZCBpbiBCYWNrZW5kTW9kdWxlQ2FsZW5kYXIgdG8gcGVyc2lzdCB0aGUgY3VycmVudCBkaXNwbGF5IG9mIHZpZXcgdHlwZSBhbmQgc2VsZWN0ZWQgZGF0ZVxuICAgICAgICAgKi9cbiAgICAgICAgc2F2ZUFzVXNlclZpZXcoKSB7XG4gICAgICAgICAgICBpZiAodGhpcy5zYXZlUmVxdWVzdCkge1xuICAgICAgICAgICAgICAgIHRoaXMuc2F2ZVJlcXVlc3QuYWJvcnQoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHRoaXMuc2F2ZVJlcXVlc3QgPSAkLnBvc3QoVFlQTzMuc2V0dGluZ3MuYWpheFVybHNbJ2FwaV91c2VyX3NldHRpbmcnXSwge1xuICAgICAgICAgICAgICAgIHZpZXdTdGF0ZToge1xuICAgICAgICAgICAgICAgICAgICBwaWQ6IHRoaXMucGlkLFxuICAgICAgICAgICAgICAgICAgICBzdGFydDogdGhpcy5zdGFydCxcbiAgICAgICAgICAgICAgICAgICAgY2FsZW5kYXJWaWV3OiB0aGlzLmNhbGVuZGFyVmlldyxcbiAgICAgICAgICAgICAgICAgICAgcGFzdEVudHJpZXM6IHRoaXMucGFzdEVudHJpZXMsXG4gICAgICAgICAgICAgICAgICAgIHBhc3RUaW1lc2xvdHM6IHRoaXMucGFzdFRpbWVzbG90cyxcbiAgICAgICAgICAgICAgICAgICAgbm90Qm9va2FibGVUaW1lc2xvdHM6IHRoaXMubm90Qm9va2FibGVUaW1lc2xvdHMsXG4gICAgICAgICAgICAgICAgICAgIGZ1dHVyZUVudHJpZXM6IHRoaXMuZnV0dXJlRW50cmllc1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgICAgIGhhc0RpcmVjdEJvb2tpbmdDYWxlbmRhcigpIHtcbiAgICAgICAgICAgIHJldHVybiB0aGlzLmdldEZpcnN0RGlyZWN0Qm9va2FibGVDYWxlbmRhcigpICE9PSBudWxsO1xuICAgICAgICB9XG4gICAgICAgIGdldEZpcnN0RGlyZWN0Qm9va2FibGVDYWxlbmRhcigpIHtcbiAgICAgICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgdGhpcy5jdXJyZW50Q2FsZW5kYXJzLmxlbmd0aDsgaSsrKSB7XG4gICAgICAgICAgICAgICAgaWYgKHRoaXMuY3VycmVudENhbGVuZGFyc1tpXS5kaXJlY3RCb29raW5nKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybiB0aGlzLmN1cnJlbnRDYWxlbmRhcnNbaV07XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICAgICAgcmV0dXJuIG51bGw7XG4gICAgICAgIH1cbiAgICB9XG4gICAgZXhwb3J0cy5CYWNrZW5kQ2FsZW5kYXJWaWV3U3RhdGUgPSBCYWNrZW5kQ2FsZW5kYXJWaWV3U3RhdGU7XG59KTtcbiIsImRlZmluZShbXCJyZXF1aXJlXCIsIFwiZXhwb3J0c1wiLCBcIlRZUE8zL0NNUy9Cd0Jvb2tpbmdtYW5hZ2VyL0JhY2tlbmRDYWxlbmRhclZpZXdTdGF0ZVwiXSwgZnVuY3Rpb24gKHJlcXVpcmUsIGV4cG9ydHMsIEJhY2tlbmRDYWxlbmRhclZpZXdTdGF0ZV8xKSB7XG4gICAgXCJ1c2Ugc3RyaWN0XCI7XG4gICAgLyoqXG4gICAgICogTW9kdWxlOiBUWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9TZWxlY3RUaW1lc2xvdERhdGVzRWxlbWVudFxuICAgICAqXG4gICAgICogQGV4cG9ydHMgVFlQTzMvQ01TL0J3Qm9va2luZ21hbmFnZXIvU2VsZWN0VGltZXNsb3REYXRlc0VsZW1lbnRcbiAgICAgKi9cbiAgICBjbGFzcyBTZWxlY3RUaW1lc2xvdERhdGVzRWxlbWVudCB7XG4gICAgICAgIGNvbnN0cnVjdG9yKCkge1xuICAgICAgICAgICAgY29uc3QgYnV0dG9uID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2VudHJ5LWRhdGUtc2VsZWN0LWJ1dHRvbicpO1xuICAgICAgICAgICAgJCgnI2VudHJ5LWRhdGUtc2VsZWN0LWJ1dHRvbicpLm9uKCdjbGljaycsIHRoaXMub25CdXR0b25DbGljay5iaW5kKHRoaXMpKTtcbiAgICAgICAgICAgIHBhcmVudC53aW5kb3cuQmFja2VuZE1vZGFsQ2FsZW5kYXIub25TYXZlID0gdGhpcy5vbk1vZGFsU2F2ZS5iaW5kKHRoaXMpO1xuICAgICAgICB9XG4gICAgICAgIG9uQnV0dG9uQ2xpY2soZSkge1xuICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgY29uc3QgYnV0dG9uID0gZS5jdXJyZW50VGFyZ2V0O1xuICAgICAgICAgICAgcGFyZW50LndpbmRvdy5CYWNrZW5kTW9kYWxDYWxlbmRhci52aWV3U3RhdGUgPSBuZXcgQmFja2VuZENhbGVuZGFyVmlld1N0YXRlXzEuQmFja2VuZENhbGVuZGFyVmlld1N0YXRlKGJ1dHRvbik7XG4gICAgICAgICAgICBwYXJlbnQud2luZG93LkJhY2tlbmRNb2RhbENhbGVuZGFyLm9wZW5Nb2RhbCgpO1xuICAgICAgICB9XG4gICAgICAgIG9uTW9kYWxTYXZlKGV2ZW50LCB2aWV3U3RhdGUpIHtcbiAgICAgICAgICAgIC8vIHVwZGF0ZSBidXR0b24ganNvblxuICAgICAgICAgICAgZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2VudHJ5LWRhdGUtc2VsZWN0LWJ1dHRvbicpLnNldEF0dHJpYnV0ZSgnZGF0YS12aWV3LXN0YXRlJywgSlNPTi5zdHJpbmdpZnkodmlld1N0YXRlKSk7XG4gICAgICAgICAgICAvLyBzYXZlIHRvIG5ldyBmb3JtXG4gICAgICAgICAgICBjb25zdCBlbnRyeVVpZCA9IHZpZXdTdGF0ZS5lbnRyeVVpZDtcbiAgICAgICAgICAgIGlmIChldmVudC5leHRlbmRlZFByb3BzLm1vZGVsID09PSAnVGltZXNsb3QnKSB7XG4gICAgICAgICAgICAgICAgJCgnaW5wdXRbbmFtZT1cImRhdGFbdHhfYndib29raW5nbWFuYWdlcl9kb21haW5fbW9kZWxfZW50cnldWycgKyBlbnRyeVVpZCArICddW3RpbWVzbG90XVwiXScpLnZhbChldmVudC5leHRlbmRlZFByb3BzLnVpZCk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBjb25zdCBzdGFydF9kYXRlID0gbmV3IERhdGUoZXZlbnQuc3RhcnQuZ2V0VGltZSgpICsgZXZlbnQuc3RhcnQuZ2V0VGltZXpvbmVPZmZzZXQoKSAqIDYwMDAwKTtcbiAgICAgICAgICAgIGNvbnN0IGVuZF9kYXRlID0gbmV3IERhdGUoZXZlbnQuZW5kLmdldFRpbWUoKSArIGV2ZW50LmVuZC5nZXRUaW1lem9uZU9mZnNldCgpICogNjAwMDApO1xuICAgICAgICAgICAgJCgnaW5wdXRbbmFtZT1cImRhdGFbdHhfYndib29raW5nbWFuYWdlcl9kb21haW5fbW9kZWxfZW50cnldWycgKyBlbnRyeVVpZCArICddW3N0YXJ0X2RhdGVdXCJdJykudmFsKHN0YXJ0X2RhdGUuZ2V0VGltZSgpIC8gMTAwMCk7XG4gICAgICAgICAgICAkKCdpbnB1dFtuYW1lPVwiZGF0YVt0eF9id2Jvb2tpbmdtYW5hZ2VyX2RvbWFpbl9tb2RlbF9lbnRyeV1bJyArIGVudHJ5VWlkICsgJ11bZW5kX2RhdGVdXCJdJykudmFsKGVuZF9kYXRlLmdldFRpbWUoKSAvIDEwMDApO1xuICAgICAgICAgICAgJCgnc2VsZWN0W25hbWU9XCJkYXRhW3R4X2J3Ym9va2luZ21hbmFnZXJfZG9tYWluX21vZGVsX2VudHJ5XVsnICsgZW50cnlVaWQgKyAnXVtjYWxlbmRhcl1cIl0nKS52YWwoZXZlbnQuZXh0ZW5kZWRQcm9wcy5jYWxlbmRhcik7XG4gICAgICAgICAgICAvLyB1cGRhdGUgZGF0ZSBsYWJlbFxuICAgICAgICAgICAgY29uc3QgZm9ybWF0ID0ge1xuICAgICAgICAgICAgICAgIHdlZWtkYXk6IFwic2hvcnRcIixcbiAgICAgICAgICAgICAgICBtb250aDogJzItZGlnaXQnLFxuICAgICAgICAgICAgICAgIGRheTogJzItZGlnaXQnLFxuICAgICAgICAgICAgICAgIHllYXI6ICdudW1lcmljJyxcbiAgICAgICAgICAgICAgICBob3VyOiAnMi1kaWdpdCcsXG4gICAgICAgICAgICAgICAgbWludXRlOiAnMi1kaWdpdCcsXG4gICAgICAgICAgICAgICAgdGltZVpvbmU6ICdFdXJvcGUvQmVybGluJ1xuICAgICAgICAgICAgfTtcbiAgICAgICAgICAgIGNvbnN0IHN0YXJ0ID0gSW50bC5EYXRlVGltZUZvcm1hdCh2aWV3U3RhdGUubGFuZ3VhZ2UsIGZvcm1hdCkuZm9ybWF0KHN0YXJ0X2RhdGUpO1xuICAgICAgICAgICAgY29uc3QgZW5kID0gSW50bC5EYXRlVGltZUZvcm1hdCh2aWV3U3RhdGUubGFuZ3VhZ2UsIGZvcm1hdCkuZm9ybWF0KGVuZF9kYXRlKTtcbiAgICAgICAgICAgICQoJyNzYXZlZFN0YXJ0RGF0ZScpLmh0bWwoc3RhcnQpO1xuICAgICAgICAgICAgJCgnI3NhdmVkRW5kRGF0ZScpLmh0bWwoZW5kKTtcbiAgICAgICAgfVxuICAgIH1cbiAgICByZXR1cm4gbmV3IFNlbGVjdFRpbWVzbG90RGF0ZXNFbGVtZW50KCk7XG59KTtcbiIsIi8vIFRoZSBtb2R1bGUgY2FjaGVcbnZhciBfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX18gPSB7fTtcblxuLy8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbmZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG5cdGlmKF9fd2VicGFja19tb2R1bGVfY2FjaGVfX1ttb2R1bGVJZF0pIHtcblx0XHRyZXR1cm4gX193ZWJwYWNrX21vZHVsZV9jYWNoZV9fW21vZHVsZUlkXS5leHBvcnRzO1xuXHR9XG5cdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG5cdHZhciBtb2R1bGUgPSBfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX19bbW9kdWxlSWRdID0ge1xuXHRcdC8vIG5vIG1vZHVsZS5pZCBuZWVkZWRcblx0XHQvLyBubyBtb2R1bGUubG9hZGVkIG5lZWRlZFxuXHRcdGV4cG9ydHM6IHt9XG5cdH07XG5cblx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG5cdF9fd2VicGFja19tb2R1bGVzX19bbW9kdWxlSWRdKG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG5cdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG5cdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbn1cblxuIiwiLy8gbW9kdWxlIGV4cG9ydHMgbXVzdCBiZSByZXR1cm5lZCBmcm9tIHJ1bnRpbWUgc28gZW50cnkgaW5saW5pbmcgaXMgZGlzYWJsZWRcbi8vIHN0YXJ0dXBcbi8vIExvYWQgZW50cnkgbW9kdWxlIGFuZCByZXR1cm4gZXhwb3J0c1xucmV0dXJuIF9fd2VicGFja19yZXF1aXJlX18oXCIuL1Jlc291cmNlcy9Qcml2YXRlL1R5cGVTY3JpcHQvU2VsZWN0VGltZXNsb3REYXRlc0VsZW1lbnQudHNcIik7XG4iXSwic291cmNlUm9vdCI6IiJ9