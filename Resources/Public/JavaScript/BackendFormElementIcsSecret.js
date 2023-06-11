define("TYPO3/CMS/BwBookingmanager/BackendFormElementIcsSecret", ["jquery"], (__WEBPACK_EXTERNAL_MODULE_jquery__) => /******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./Resources/Private/TypeScript/BackendFormElementIcsSecret.ts":
/*!*********************************************************************!*
  !*** ./Resources/Private/TypeScript/BackendFormElementIcsSecret.ts ***!
  \*********************************************************************/
/***/ ((module, exports, __webpack_require__) => {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;!(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__, exports, __webpack_require__(/*! jquery */ "jquery")], __WEBPACK_AMD_DEFINE_RESULT__ = (function (require, exports, $) {
    "use strict";
    /**
     * Module: TYPO3/CMS/BwBookingmanager/BackendFormElementIcsSecret
     *
     * @exports TYPO3/CMS/BwBookingmanager/BackendFormElementIcsSecret
     */
    class BackendFormElementIcsSecret {
        constructor() {
            this.cacheDom();
            this.bindEvents();
        }
        cacheDom() {
            this.$resetButton = $('#resetButton');
            this.$copyButton = $('#copyButton');
            this.$refreshButton = $('#refreshButton');
            this.$inputUrl = $('#inputUrl');
            this.$inputSecret = $('#inputSecret');
        }
        bindEvents() {
            this.$resetButton.on('click', this.onResetButtonClick.bind(this));
            this.$copyButton.on('click', this.onCopyButtonClick.bind(this));
            this.$refreshButton.on('click', this.onRefreshButtonClick.bind(this));
            this.$inputSecret.on('input', this.onInputSecretChange.bind(this));
        }
        onResetButtonClick(e) {
            e.preventDefault();
            this.$inputSecret.val('');
            this.$inputSecret.trigger('input');
        }
        onCopyButtonClick(e) {
            e.preventDefault();
            this.$inputUrl.select();
            document.execCommand("copy");
        }
        onRefreshButtonClick(e) {
            e.preventDefault();
            this.$inputSecret.val(this.makeSecret(32));
            this.$inputSecret.trigger('input');
        }
        onInputSecretChange(e) {
            e.preventDefault();
            let url = this.$inputUrl.attr('data-url-start');
            if (this.$inputSecret.val()) {
                url += '/' + this.$inputSecret.val();
            }
            url += '.ics';
            this.$inputUrl.val(url);
        }
        makeSecret(length) {
            let result = '';
            const characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
            const charactersLength = characters.length;
            for (let i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            return result;
        }
    }
    return new BackendFormElementIcsSecret();
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
/******/ 	return __webpack_require__("./Resources/Private/TypeScript/BackendFormElementIcsSecret.ts");
/******/ })()
);;
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9UWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9bbmFtZV0vLi9SZXNvdXJjZXMvUHJpdmF0ZS9UeXBlU2NyaXB0L0JhY2tlbmRGb3JtRWxlbWVudEljc1NlY3JldC50cyIsIndlYnBhY2s6Ly9UWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9bbmFtZV0vZXh0ZXJuYWwgXCJqcXVlcnlcIiIsIndlYnBhY2s6Ly9UWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9bbmFtZV0vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vVFlQTzMvQ01TL0J3Qm9va2luZ21hbmFnZXIvW25hbWVdL3dlYnBhY2svc3RhcnR1cCJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7QUFBQSxpR0FBTyxDQUFDLG1CQUFTLEVBQUUsT0FBUyxFQUFFLDJDQUFRLENBQUMsbUNBQUU7QUFDekM7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSwyQkFBMkIsWUFBWTtBQUN2QztBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSxDQUFDO0FBQUEsa0dBQUM7Ozs7Ozs7Ozs7OztBQzVERixvRDs7Ozs7O1VDQUE7VUFDQTs7VUFFQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7O1VBRUE7VUFDQTs7VUFFQTtVQUNBO1VBQ0E7OztVQ3JCQTtVQUNBO1VBQ0E7VUFDQSIsImZpbGUiOiJSZXNvdXJjZXMvUHVibGljL0phdmFTY3JpcHQvQmFja2VuZEZvcm1FbGVtZW50SWNzU2VjcmV0LmpzIiwic291cmNlc0NvbnRlbnQiOlsiZGVmaW5lKFtcInJlcXVpcmVcIiwgXCJleHBvcnRzXCIsIFwianF1ZXJ5XCJdLCBmdW5jdGlvbiAocmVxdWlyZSwgZXhwb3J0cywgJCkge1xuICAgIFwidXNlIHN0cmljdFwiO1xuICAgIC8qKlxuICAgICAqIE1vZHVsZTogVFlQTzMvQ01TL0J3Qm9va2luZ21hbmFnZXIvQmFja2VuZEZvcm1FbGVtZW50SWNzU2VjcmV0XG4gICAgICpcbiAgICAgKiBAZXhwb3J0cyBUWVBPMy9DTVMvQndCb29raW5nbWFuYWdlci9CYWNrZW5kRm9ybUVsZW1lbnRJY3NTZWNyZXRcbiAgICAgKi9cbiAgICBjbGFzcyBCYWNrZW5kRm9ybUVsZW1lbnRJY3NTZWNyZXQge1xuICAgICAgICBjb25zdHJ1Y3RvcigpIHtcbiAgICAgICAgICAgIHRoaXMuY2FjaGVEb20oKTtcbiAgICAgICAgICAgIHRoaXMuYmluZEV2ZW50cygpO1xuICAgICAgICB9XG4gICAgICAgIGNhY2hlRG9tKCkge1xuICAgICAgICAgICAgdGhpcy4kcmVzZXRCdXR0b24gPSAkKCcjcmVzZXRCdXR0b24nKTtcbiAgICAgICAgICAgIHRoaXMuJGNvcHlCdXR0b24gPSAkKCcjY29weUJ1dHRvbicpO1xuICAgICAgICAgICAgdGhpcy4kcmVmcmVzaEJ1dHRvbiA9ICQoJyNyZWZyZXNoQnV0dG9uJyk7XG4gICAgICAgICAgICB0aGlzLiRpbnB1dFVybCA9ICQoJyNpbnB1dFVybCcpO1xuICAgICAgICAgICAgdGhpcy4kaW5wdXRTZWNyZXQgPSAkKCcjaW5wdXRTZWNyZXQnKTtcbiAgICAgICAgfVxuICAgICAgICBiaW5kRXZlbnRzKCkge1xuICAgICAgICAgICAgdGhpcy4kcmVzZXRCdXR0b24ub24oJ2NsaWNrJywgdGhpcy5vblJlc2V0QnV0dG9uQ2xpY2suYmluZCh0aGlzKSk7XG4gICAgICAgICAgICB0aGlzLiRjb3B5QnV0dG9uLm9uKCdjbGljaycsIHRoaXMub25Db3B5QnV0dG9uQ2xpY2suYmluZCh0aGlzKSk7XG4gICAgICAgICAgICB0aGlzLiRyZWZyZXNoQnV0dG9uLm9uKCdjbGljaycsIHRoaXMub25SZWZyZXNoQnV0dG9uQ2xpY2suYmluZCh0aGlzKSk7XG4gICAgICAgICAgICB0aGlzLiRpbnB1dFNlY3JldC5vbignaW5wdXQnLCB0aGlzLm9uSW5wdXRTZWNyZXRDaGFuZ2UuYmluZCh0aGlzKSk7XG4gICAgICAgIH1cbiAgICAgICAgb25SZXNldEJ1dHRvbkNsaWNrKGUpIHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIHRoaXMuJGlucHV0U2VjcmV0LnZhbCgnJyk7XG4gICAgICAgICAgICB0aGlzLiRpbnB1dFNlY3JldC50cmlnZ2VyKCdpbnB1dCcpO1xuICAgICAgICB9XG4gICAgICAgIG9uQ29weUJ1dHRvbkNsaWNrKGUpIHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIHRoaXMuJGlucHV0VXJsLnNlbGVjdCgpO1xuICAgICAgICAgICAgZG9jdW1lbnQuZXhlY0NvbW1hbmQoXCJjb3B5XCIpO1xuICAgICAgICB9XG4gICAgICAgIG9uUmVmcmVzaEJ1dHRvbkNsaWNrKGUpIHtcbiAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgICAgIHRoaXMuJGlucHV0U2VjcmV0LnZhbCh0aGlzLm1ha2VTZWNyZXQoMzIpKTtcbiAgICAgICAgICAgIHRoaXMuJGlucHV0U2VjcmV0LnRyaWdnZXIoJ2lucHV0Jyk7XG4gICAgICAgIH1cbiAgICAgICAgb25JbnB1dFNlY3JldENoYW5nZShlKSB7XG4gICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICBsZXQgdXJsID0gdGhpcy4kaW5wdXRVcmwuYXR0cignZGF0YS11cmwtc3RhcnQnKTtcbiAgICAgICAgICAgIGlmICh0aGlzLiRpbnB1dFNlY3JldC52YWwoKSkge1xuICAgICAgICAgICAgICAgIHVybCArPSAnLycgKyB0aGlzLiRpbnB1dFNlY3JldC52YWwoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIHVybCArPSAnLmljcyc7XG4gICAgICAgICAgICB0aGlzLiRpbnB1dFVybC52YWwodXJsKTtcbiAgICAgICAgfVxuICAgICAgICBtYWtlU2VjcmV0KGxlbmd0aCkge1xuICAgICAgICAgICAgbGV0IHJlc3VsdCA9ICcnO1xuICAgICAgICAgICAgY29uc3QgY2hhcmFjdGVycyA9ICdhYmNkZWZnaGlqa2xtbm9wcXJzdHV2d3h5ejAxMjM0NTY3ODknO1xuICAgICAgICAgICAgY29uc3QgY2hhcmFjdGVyc0xlbmd0aCA9IGNoYXJhY3RlcnMubGVuZ3RoO1xuICAgICAgICAgICAgZm9yIChsZXQgaSA9IDA7IGkgPCBsZW5ndGg7IGkrKykge1xuICAgICAgICAgICAgICAgIHJlc3VsdCArPSBjaGFyYWN0ZXJzLmNoYXJBdChNYXRoLmZsb29yKE1hdGgucmFuZG9tKCkgKiBjaGFyYWN0ZXJzTGVuZ3RoKSk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICByZXR1cm4gcmVzdWx0O1xuICAgICAgICB9XG4gICAgfVxuICAgIHJldHVybiBuZXcgQmFja2VuZEZvcm1FbGVtZW50SWNzU2VjcmV0KCk7XG59KTtcbiIsIm1vZHVsZS5leHBvcnRzID0gX19XRUJQQUNLX0VYVEVSTkFMX01PRFVMRV9qcXVlcnlfXzsiLCIvLyBUaGUgbW9kdWxlIGNhY2hlXG52YXIgX193ZWJwYWNrX21vZHVsZV9jYWNoZV9fID0ge307XG5cbi8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG5mdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuXHRpZihfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX19bbW9kdWxlSWRdKSB7XG5cdFx0cmV0dXJuIF9fd2VicGFja19tb2R1bGVfY2FjaGVfX1ttb2R1bGVJZF0uZXhwb3J0cztcblx0fVxuXHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuXHR2YXIgbW9kdWxlID0gX193ZWJwYWNrX21vZHVsZV9jYWNoZV9fW21vZHVsZUlkXSA9IHtcblx0XHQvLyBubyBtb2R1bGUuaWQgbmVlZGVkXG5cdFx0Ly8gbm8gbW9kdWxlLmxvYWRlZCBuZWVkZWRcblx0XHRleHBvcnRzOiB7fVxuXHR9O1xuXG5cdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuXHRfX3dlYnBhY2tfbW9kdWxlc19fW21vZHVsZUlkXShtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuXHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuXHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG59XG5cbiIsIi8vIG1vZHVsZSBleHBvcnRzIG11c3QgYmUgcmV0dXJuZWQgZnJvbSBydW50aW1lIHNvIGVudHJ5IGlubGluaW5nIGlzIGRpc2FibGVkXG4vLyBzdGFydHVwXG4vLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbnJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKFwiLi9SZXNvdXJjZXMvUHJpdmF0ZS9UeXBlU2NyaXB0L0JhY2tlbmRGb3JtRWxlbWVudEljc1NlY3JldC50c1wiKTtcbiJdLCJzb3VyY2VSb290IjoiIn0=