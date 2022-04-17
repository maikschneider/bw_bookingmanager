define("TYPO3/CMS/BwBookingmanager/BackendEntryListConfirmation", ["TYPO3/CMS/Backend/Icons","TYPO3/CMS/Backend/Notification"], (__WEBPACK_EXTERNAL_MODULE_TYPO3_CMS_Backend_Icons__, __WEBPACK_EXTERNAL_MODULE_TYPO3_CMS_Backend_Notification__) => /******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./Resources/Private/TypeScript/BackendEntryListConfirmation.ts":
/*!**********************************************************************!*
  !*** ./Resources/Private/TypeScript/BackendEntryListConfirmation.ts ***!
  \**********************************************************************/
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;!(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__, exports, __webpack_require__(/*! TYPO3/CMS/Backend/Icons */ "TYPO3/CMS/Backend/Icons"), __webpack_require__(/*! TYPO3/CMS/Backend/Notification */ "TYPO3/CMS/Backend/Notification")], __WEBPACK_AMD_DEFINE_RESULT__ = (function (require, exports, Icons, Notification) {
    "use strict";
    var Identifiers;
    (function (Identifiers) {
        Identifiers["confirm"] = ".t3js-record-confirm";
        Identifiers["icon"] = ".t3js-icon";
    })(Identifiers || (Identifiers = {}));
    class BackendEntryListConfirmation {
        constructor() {
            $(() => {
                this.initialize();
            });
        }
        initialize() {
            this.styleRows();
            $(document).on('click', Identifiers.confirm, (e) => {
                e.preventDefault();
                const $anchorElement = $(e.currentTarget);
                const $iconElement = $anchorElement.find(Identifiers.icon);
                const $rowElement = $anchorElement.closest('tr[data-uid]');
                const params = $anchorElement.data('params');
                // add a spinner
                this._showSpinnerIcon($iconElement);
                // make the AJAX call to toggle the visibility
                this._call(params).always((result) => {
                    // print messages on errors
                    if (result.hasErrors) {
                        this.handleErrors(result);
                    }
                    else {
                        // adjust overlay icon
                        this.toggleRow($rowElement);
                    }
                });
            });
        }
        styleRows() {
            $('tr.t3js-entity[data-table="tx_bwbookingmanager_domain_model_entry"]').each(function (i, e) {
                const isConfirmed = $(e).find('.t3js-record-confirm').attr('data-confirmed');
                if (isConfirmed == 'no')
                    $(e).addClass('not-confirmed');
            });
        }
        /**
         * Toggle row visibility after record has been changed
         *
         * @param {JQuery} $rowElement
         */
        toggleRow($rowElement) {
            const $anchorElement = $rowElement.find(Identifiers.confirm);
            const table = $anchorElement.closest('table[data-table]').data('table');
            const params = $anchorElement.data('params');
            let nextParams;
            let nextState;
            let iconName;
            if ($anchorElement.data('confirmed') === 'no') {
                nextState = 'yes';
                nextParams = params.replace('=1', '=0');
                iconName = 'actions-edit-hide';
                $rowElement.removeClass('not-confirmed');
            }
            else {
                nextState = 'no';
                nextParams = params.replace('=0', '=1');
                iconName = 'actions-edit-unhide';
                $rowElement.addClass('not-confirmed');
            }
            $anchorElement.data('confirmed', nextState).data('params', nextParams);
            const newTitle = $anchorElement.attr('data-toggle-title');
            $anchorElement.attr('data-toggle-title', $anchorElement.attr('data-original-title'));
            $anchorElement.attr('data-original-title', newTitle);
            $anchorElement.tooltip('hide');
            const $iconElement = $anchorElement.find(Identifiers.icon);
            Icons.getIcon(iconName, Icons.sizes.small).then((icon) => {
                $iconElement.replaceWith(icon);
            });
        }
        /**
         * AJAX call to record_process route (SimpleDataHandlerController->processAjaxRequest)
         * returns a jQuery Promise to work with
         *
         * @param {Object} params
         * @returns {JQueryXHR}
         */
        _call(params) {
            return $.getJSON(TYPO3.settings.ajaxUrls.record_process, params);
        }
        /**
         * Replace the given icon with a spinner icon
         *
         * @param {Object} $iconElement
         * @private
         */
        _showSpinnerIcon($iconElement) {
            Icons.getIcon('spinner-circle-dark', Icons.sizes.small).then((icon) => {
                $iconElement.replaceWith(icon);
            });
        }
        /**
         * Handle the errors from result object
         *
         * @param {Object} result
         */
        handleErrors(result) {
            $.each(result.messages, (position, message) => {
                console.log(message);
                Notification.error(message.title, message.message);
            });
        }
    }
    return new BackendEntryListConfirmation();
}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));


/***/ }),

/***/ "TYPO3/CMS/Backend/Icons":
/*!******************************************!*
  !*** external "TYPO3/CMS/Backend/Icons" ***!
  \******************************************/
/***/ ((module) => {

"use strict";
module.exports = __WEBPACK_EXTERNAL_MODULE_TYPO3_CMS_Backend_Icons__;

/***/ }),

/***/ "TYPO3/CMS/Backend/Notification":
/*!*************************************************!*
  !*** external "TYPO3/CMS/Backend/Notification" ***!
  \*************************************************/
/***/ ((module) => {

"use strict";
module.exports = __WEBPACK_EXTERNAL_MODULE_TYPO3_CMS_Backend_Notification__;

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
/******/ 	return __webpack_require__("./Resources/Private/TypeScript/BackendEntryListConfirmation.ts");
/******/ })()
);;
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9UWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9bbmFtZV0vLi9SZXNvdXJjZXMvUHJpdmF0ZS9UeXBlU2NyaXB0L0JhY2tlbmRFbnRyeUxpc3RDb25maXJtYXRpb24udHMiLCJ3ZWJwYWNrOi8vVFlQTzMvQ01TL0J3Qm9va2luZ21hbmFnZXIvW25hbWVdL2V4dGVybmFsIFwiVFlQTzMvQ01TL0JhY2tlbmQvSWNvbnNcIiIsIndlYnBhY2s6Ly9UWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9bbmFtZV0vZXh0ZXJuYWwgXCJUWVBPMy9DTVMvQmFja2VuZC9Ob3RpZmljYXRpb25cIiIsIndlYnBhY2s6Ly9UWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9bbmFtZV0vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vVFlQTzMvQ01TL0J3Qm9va2luZ21hbmFnZXIvW25hbWVdL3dlYnBhY2svc3RhcnR1cCJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7QUFBQSxpR0FBTyxDQUFDLG1CQUFTLEVBQUUsT0FBUyxFQUFFLDZFQUF5QixFQUFFLDJGQUFnQyxDQUFDLG1DQUFFO0FBQzVGO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxLQUFLLGtDQUFrQztBQUN2QztBQUNBO0FBQ0E7QUFDQTtBQUNBLGFBQWE7QUFDYjtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxpQkFBaUI7QUFDakIsYUFBYTtBQUNiO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGFBQWE7QUFDYjtBQUNBO0FBQ0E7QUFDQTtBQUNBLG1CQUFtQixPQUFPO0FBQzFCO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsYUFBYTtBQUNiO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxtQkFBbUIsT0FBTztBQUMxQixxQkFBcUI7QUFDckI7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxtQkFBbUIsT0FBTztBQUMxQjtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsYUFBYTtBQUNiO0FBQ0E7QUFDQTtBQUNBO0FBQ0EsbUJBQW1CLE9BQU87QUFDMUI7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGFBQWE7QUFDYjtBQUNBO0FBQ0E7QUFDQSxDQUFDO0FBQUEsa0dBQUM7Ozs7Ozs7Ozs7OztBQy9HRixxRTs7Ozs7Ozs7Ozs7QUNBQSw0RTs7Ozs7O1VDQUE7VUFDQTs7VUFFQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7O1VBRUE7VUFDQTs7VUFFQTtVQUNBO1VBQ0E7OztVQ3JCQTtVQUNBO1VBQ0E7VUFDQSIsImZpbGUiOiJSZXNvdXJjZXMvUHVibGljL0phdmFTY3JpcHQvQmFja2VuZEVudHJ5TGlzdENvbmZpcm1hdGlvbi5qcyIsInNvdXJjZXNDb250ZW50IjpbImRlZmluZShbXCJyZXF1aXJlXCIsIFwiZXhwb3J0c1wiLCBcIlRZUE8zL0NNUy9CYWNrZW5kL0ljb25zXCIsIFwiVFlQTzMvQ01TL0JhY2tlbmQvTm90aWZpY2F0aW9uXCJdLCBmdW5jdGlvbiAocmVxdWlyZSwgZXhwb3J0cywgSWNvbnMsIE5vdGlmaWNhdGlvbikge1xuICAgIFwidXNlIHN0cmljdFwiO1xuICAgIHZhciBJZGVudGlmaWVycztcbiAgICAoZnVuY3Rpb24gKElkZW50aWZpZXJzKSB7XG4gICAgICAgIElkZW50aWZpZXJzW1wiY29uZmlybVwiXSA9IFwiLnQzanMtcmVjb3JkLWNvbmZpcm1cIjtcbiAgICAgICAgSWRlbnRpZmllcnNbXCJpY29uXCJdID0gXCIudDNqcy1pY29uXCI7XG4gICAgfSkoSWRlbnRpZmllcnMgfHwgKElkZW50aWZpZXJzID0ge30pKTtcbiAgICBjbGFzcyBCYWNrZW5kRW50cnlMaXN0Q29uZmlybWF0aW9uIHtcbiAgICAgICAgY29uc3RydWN0b3IoKSB7XG4gICAgICAgICAgICAkKCgpID0+IHtcbiAgICAgICAgICAgICAgICB0aGlzLmluaXRpYWxpemUoKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgICAgIGluaXRpYWxpemUoKSB7XG4gICAgICAgICAgICB0aGlzLnN0eWxlUm93cygpO1xuICAgICAgICAgICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgSWRlbnRpZmllcnMuY29uZmlybSwgKGUpID0+IHtcbiAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICAgICAgY29uc3QgJGFuY2hvckVsZW1lbnQgPSAkKGUuY3VycmVudFRhcmdldCk7XG4gICAgICAgICAgICAgICAgY29uc3QgJGljb25FbGVtZW50ID0gJGFuY2hvckVsZW1lbnQuZmluZChJZGVudGlmaWVycy5pY29uKTtcbiAgICAgICAgICAgICAgICBjb25zdCAkcm93RWxlbWVudCA9ICRhbmNob3JFbGVtZW50LmNsb3Nlc3QoJ3RyW2RhdGEtdWlkXScpO1xuICAgICAgICAgICAgICAgIGNvbnN0IHBhcmFtcyA9ICRhbmNob3JFbGVtZW50LmRhdGEoJ3BhcmFtcycpO1xuICAgICAgICAgICAgICAgIC8vIGFkZCBhIHNwaW5uZXJcbiAgICAgICAgICAgICAgICB0aGlzLl9zaG93U3Bpbm5lckljb24oJGljb25FbGVtZW50KTtcbiAgICAgICAgICAgICAgICAvLyBtYWtlIHRoZSBBSkFYIGNhbGwgdG8gdG9nZ2xlIHRoZSB2aXNpYmlsaXR5XG4gICAgICAgICAgICAgICAgdGhpcy5fY2FsbChwYXJhbXMpLmFsd2F5cygocmVzdWx0KSA9PiB7XG4gICAgICAgICAgICAgICAgICAgIC8vIHByaW50IG1lc3NhZ2VzIG9uIGVycm9yc1xuICAgICAgICAgICAgICAgICAgICBpZiAocmVzdWx0Lmhhc0Vycm9ycykge1xuICAgICAgICAgICAgICAgICAgICAgICAgdGhpcy5oYW5kbGVFcnJvcnMocmVzdWx0KTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICBlbHNlIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIGFkanVzdCBvdmVybGF5IGljb25cbiAgICAgICAgICAgICAgICAgICAgICAgIHRoaXMudG9nZ2xlUm93KCRyb3dFbGVtZW50KTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICAgICAgc3R5bGVSb3dzKCkge1xuICAgICAgICAgICAgJCgndHIudDNqcy1lbnRpdHlbZGF0YS10YWJsZT1cInR4X2J3Ym9va2luZ21hbmFnZXJfZG9tYWluX21vZGVsX2VudHJ5XCJdJykuZWFjaChmdW5jdGlvbiAoaSwgZSkge1xuICAgICAgICAgICAgICAgIGNvbnN0IGlzQ29uZmlybWVkID0gJChlKS5maW5kKCcudDNqcy1yZWNvcmQtY29uZmlybScpLmF0dHIoJ2RhdGEtY29uZmlybWVkJyk7XG4gICAgICAgICAgICAgICAgaWYgKGlzQ29uZmlybWVkID09ICdubycpXG4gICAgICAgICAgICAgICAgICAgICQoZSkuYWRkQ2xhc3MoJ25vdC1jb25maXJtZWQnKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgICAgIC8qKlxuICAgICAgICAgKiBUb2dnbGUgcm93IHZpc2liaWxpdHkgYWZ0ZXIgcmVjb3JkIGhhcyBiZWVuIGNoYW5nZWRcbiAgICAgICAgICpcbiAgICAgICAgICogQHBhcmFtIHtKUXVlcnl9ICRyb3dFbGVtZW50XG4gICAgICAgICAqL1xuICAgICAgICB0b2dnbGVSb3coJHJvd0VsZW1lbnQpIHtcbiAgICAgICAgICAgIGNvbnN0ICRhbmNob3JFbGVtZW50ID0gJHJvd0VsZW1lbnQuZmluZChJZGVudGlmaWVycy5jb25maXJtKTtcbiAgICAgICAgICAgIGNvbnN0IHRhYmxlID0gJGFuY2hvckVsZW1lbnQuY2xvc2VzdCgndGFibGVbZGF0YS10YWJsZV0nKS5kYXRhKCd0YWJsZScpO1xuICAgICAgICAgICAgY29uc3QgcGFyYW1zID0gJGFuY2hvckVsZW1lbnQuZGF0YSgncGFyYW1zJyk7XG4gICAgICAgICAgICBsZXQgbmV4dFBhcmFtcztcbiAgICAgICAgICAgIGxldCBuZXh0U3RhdGU7XG4gICAgICAgICAgICBsZXQgaWNvbk5hbWU7XG4gICAgICAgICAgICBpZiAoJGFuY2hvckVsZW1lbnQuZGF0YSgnY29uZmlybWVkJykgPT09ICdubycpIHtcbiAgICAgICAgICAgICAgICBuZXh0U3RhdGUgPSAneWVzJztcbiAgICAgICAgICAgICAgICBuZXh0UGFyYW1zID0gcGFyYW1zLnJlcGxhY2UoJz0xJywgJz0wJyk7XG4gICAgICAgICAgICAgICAgaWNvbk5hbWUgPSAnYWN0aW9ucy1lZGl0LWhpZGUnO1xuICAgICAgICAgICAgICAgICRyb3dFbGVtZW50LnJlbW92ZUNsYXNzKCdub3QtY29uZmlybWVkJyk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBlbHNlIHtcbiAgICAgICAgICAgICAgICBuZXh0U3RhdGUgPSAnbm8nO1xuICAgICAgICAgICAgICAgIG5leHRQYXJhbXMgPSBwYXJhbXMucmVwbGFjZSgnPTAnLCAnPTEnKTtcbiAgICAgICAgICAgICAgICBpY29uTmFtZSA9ICdhY3Rpb25zLWVkaXQtdW5oaWRlJztcbiAgICAgICAgICAgICAgICAkcm93RWxlbWVudC5hZGRDbGFzcygnbm90LWNvbmZpcm1lZCcpO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgJGFuY2hvckVsZW1lbnQuZGF0YSgnY29uZmlybWVkJywgbmV4dFN0YXRlKS5kYXRhKCdwYXJhbXMnLCBuZXh0UGFyYW1zKTtcbiAgICAgICAgICAgIGNvbnN0IG5ld1RpdGxlID0gJGFuY2hvckVsZW1lbnQuYXR0cignZGF0YS10b2dnbGUtdGl0bGUnKTtcbiAgICAgICAgICAgICRhbmNob3JFbGVtZW50LmF0dHIoJ2RhdGEtdG9nZ2xlLXRpdGxlJywgJGFuY2hvckVsZW1lbnQuYXR0cignZGF0YS1vcmlnaW5hbC10aXRsZScpKTtcbiAgICAgICAgICAgICRhbmNob3JFbGVtZW50LmF0dHIoJ2RhdGEtb3JpZ2luYWwtdGl0bGUnLCBuZXdUaXRsZSk7XG4gICAgICAgICAgICAkYW5jaG9yRWxlbWVudC50b29sdGlwKCdoaWRlJyk7XG4gICAgICAgICAgICBjb25zdCAkaWNvbkVsZW1lbnQgPSAkYW5jaG9yRWxlbWVudC5maW5kKElkZW50aWZpZXJzLmljb24pO1xuICAgICAgICAgICAgSWNvbnMuZ2V0SWNvbihpY29uTmFtZSwgSWNvbnMuc2l6ZXMuc21hbGwpLnRoZW4oKGljb24pID0+IHtcbiAgICAgICAgICAgICAgICAkaWNvbkVsZW1lbnQucmVwbGFjZVdpdGgoaWNvbik7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuICAgICAgICAvKipcbiAgICAgICAgICogQUpBWCBjYWxsIHRvIHJlY29yZF9wcm9jZXNzIHJvdXRlIChTaW1wbGVEYXRhSGFuZGxlckNvbnRyb2xsZXItPnByb2Nlc3NBamF4UmVxdWVzdClcbiAgICAgICAgICogcmV0dXJucyBhIGpRdWVyeSBQcm9taXNlIHRvIHdvcmsgd2l0aFxuICAgICAgICAgKlxuICAgICAgICAgKiBAcGFyYW0ge09iamVjdH0gcGFyYW1zXG4gICAgICAgICAqIEByZXR1cm5zIHtKUXVlcnlYSFJ9XG4gICAgICAgICAqL1xuICAgICAgICBfY2FsbChwYXJhbXMpIHtcbiAgICAgICAgICAgIHJldHVybiAkLmdldEpTT04oVFlQTzMuc2V0dGluZ3MuYWpheFVybHMucmVjb3JkX3Byb2Nlc3MsIHBhcmFtcyk7XG4gICAgICAgIH1cbiAgICAgICAgLyoqXG4gICAgICAgICAqIFJlcGxhY2UgdGhlIGdpdmVuIGljb24gd2l0aCBhIHNwaW5uZXIgaWNvblxuICAgICAgICAgKlxuICAgICAgICAgKiBAcGFyYW0ge09iamVjdH0gJGljb25FbGVtZW50XG4gICAgICAgICAqIEBwcml2YXRlXG4gICAgICAgICAqL1xuICAgICAgICBfc2hvd1NwaW5uZXJJY29uKCRpY29uRWxlbWVudCkge1xuICAgICAgICAgICAgSWNvbnMuZ2V0SWNvbignc3Bpbm5lci1jaXJjbGUtZGFyaycsIEljb25zLnNpemVzLnNtYWxsKS50aGVuKChpY29uKSA9PiB7XG4gICAgICAgICAgICAgICAgJGljb25FbGVtZW50LnJlcGxhY2VXaXRoKGljb24pO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICAgICAgLyoqXG4gICAgICAgICAqIEhhbmRsZSB0aGUgZXJyb3JzIGZyb20gcmVzdWx0IG9iamVjdFxuICAgICAgICAgKlxuICAgICAgICAgKiBAcGFyYW0ge09iamVjdH0gcmVzdWx0XG4gICAgICAgICAqL1xuICAgICAgICBoYW5kbGVFcnJvcnMocmVzdWx0KSB7XG4gICAgICAgICAgICAkLmVhY2gocmVzdWx0Lm1lc3NhZ2VzLCAocG9zaXRpb24sIG1lc3NhZ2UpID0+IHtcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZyhtZXNzYWdlKTtcbiAgICAgICAgICAgICAgICBOb3RpZmljYXRpb24uZXJyb3IobWVzc2FnZS50aXRsZSwgbWVzc2FnZS5tZXNzYWdlKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgfVxuICAgIHJldHVybiBuZXcgQmFja2VuZEVudHJ5TGlzdENvbmZpcm1hdGlvbigpO1xufSk7XG4iLCJtb2R1bGUuZXhwb3J0cyA9IF9fV0VCUEFDS19FWFRFUk5BTF9NT0RVTEVfVFlQTzNfQ01TX0JhY2tlbmRfSWNvbnNfXzsiLCJtb2R1bGUuZXhwb3J0cyA9IF9fV0VCUEFDS19FWFRFUk5BTF9NT0RVTEVfVFlQTzNfQ01TX0JhY2tlbmRfTm90aWZpY2F0aW9uX187IiwiLy8gVGhlIG1vZHVsZSBjYWNoZVxudmFyIF9fd2VicGFja19tb2R1bGVfY2FjaGVfXyA9IHt9O1xuXG4vLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcblx0aWYoX193ZWJwYWNrX21vZHVsZV9jYWNoZV9fW21vZHVsZUlkXSkge1xuXHRcdHJldHVybiBfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX19bbW9kdWxlSWRdLmV4cG9ydHM7XG5cdH1cblx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcblx0dmFyIG1vZHVsZSA9IF9fd2VicGFja19tb2R1bGVfY2FjaGVfX1ttb2R1bGVJZF0gPSB7XG5cdFx0Ly8gbm8gbW9kdWxlLmlkIG5lZWRlZFxuXHRcdC8vIG5vIG1vZHVsZS5sb2FkZWQgbmVlZGVkXG5cdFx0ZXhwb3J0czoge31cblx0fTtcblxuXHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cblx0X193ZWJwYWNrX21vZHVsZXNfX1ttb2R1bGVJZF0obW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cblx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcblx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xufVxuXG4iLCIvLyBtb2R1bGUgZXhwb3J0cyBtdXN0IGJlIHJldHVybmVkIGZyb20gcnVudGltZSBzbyBlbnRyeSBpbmxpbmluZyBpcyBkaXNhYmxlZFxuLy8gc3RhcnR1cFxuLy8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG5yZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhcIi4vUmVzb3VyY2VzL1ByaXZhdGUvVHlwZVNjcmlwdC9CYWNrZW5kRW50cnlMaXN0Q29uZmlybWF0aW9uLnRzXCIpO1xuIl0sInNvdXJjZVJvb3QiOiIifQ==