define(["require", "exports", "TYPO3/CMS/Backend/Icons", "TYPO3/CMS/Backend/Notification"], function (require, exports, Icons, Notification) {
    "use strict";
    var Identifiers;
    (function (Identifiers) {
        Identifiers["confirm"] = ".t3js-record-confirm";
        Identifiers["icon"] = ".t3js-icon";
    })(Identifiers || (Identifiers = {}));
    var BetterRecordList = /** @class */ (function () {
        function BetterRecordList() {
            var _this = this;
            $(function () {
                _this.initialize();
            });
        }
        BetterRecordList.prototype.initialize = function () {
            var _this = this;
            $(document).on('click', Identifiers.confirm, function (e) {
                e.preventDefault();
                var $anchorElement = $(e.currentTarget);
                var $iconElement = $anchorElement.find(Identifiers.icon);
                var $rowElement = $anchorElement.closest('tr[data-uid]');
                var params = $anchorElement.data('params');
                // add a spinner
                _this._showSpinnerIcon($iconElement);
                // make the AJAX call to toggle the visibility
                _this._call(params).always(function (result) {
                    // print messages on errors
                    console.log(result);
                    if (result.hasErrors) {
                        _this.handleErrors(result);
                    }
                    else {
                        // adjust overlay icon
                        _this.toggleRow($rowElement);
                    }
                });
            });
        };
        /**
         * Toggle row visibility after record has been changed
         *
         * @param {JQuery} $rowElement
         */
        BetterRecordList.prototype.toggleRow = function ($rowElement) {
            console.log('toggle console');
            var $anchorElement = $rowElement.find(Identifiers.confirm);
            var table = $anchorElement.closest('table[data-table]').data('table');
            var params = $anchorElement.data('params');
            var nextParams;
            var nextState;
            var iconName;
            if ($anchorElement.data('confirmed') === 'no') {
                nextState = 'yes';
                nextParams = params.replace('=1', '=0');
                iconName = 'actions-edit-unhide';
            }
            else {
                nextState = 'no';
                nextParams = params.replace('=0', '=1');
                iconName = 'actions-edit-hide';
            }
            $anchorElement.data('confirmed', nextState).data('params', nextParams);
            var $iconElement = $anchorElement.find(Identifiers.icon);
            Icons.getIcon(iconName, Icons.sizes.small).done(function (icon) {
                $iconElement.replaceWith(icon);
            });
            $rowElement.fadeTo('fast', 0.4, function () {
                $rowElement.fadeTo('fast', 1);
            });
        };
        /**
         * AJAX call to record_process route (SimpleDataHandlerController->processAjaxRequest)
         * returns a jQuery Promise to work with
         *
         * @param {Object} params
         * @returns {JQueryXHR}
         */
        BetterRecordList.prototype._call = function (params) {
            return $.getJSON(TYPO3.settings.ajaxUrls.record_process, params);
        };
        /**
         * Replace the given icon with a spinner icon
         *
         * @param {Object} $iconElement
         * @private
         */
        BetterRecordList.prototype._showSpinnerIcon = function ($iconElement) {
            Icons.getIcon('spinner-circle-dark', Icons.sizes.small).done(function (icon) {
                $iconElement.replaceWith(icon);
            });
        };
        /**
         * Handle the errors from result object
         *
         * @param {Object} result
         */
        BetterRecordList.prototype.handleErrors = function (result) {
            $.each(result.messages, function (position, message) {
                console.log(message);
                Notification.error(message.title, message.message);
            });
        };
        return BetterRecordList;
    }());
    return new BetterRecordList();
});
