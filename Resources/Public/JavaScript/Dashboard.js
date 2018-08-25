define(["require", "exports", "TYPO3/CMS/Backend/Icons"], function (require, exports, Icons) {
    "use strict";
    var Dashboard = /** @class */ (function () {
        function Dashboard() {
        }
        Dashboard.prototype.init = function () {
            var _this = this;
            var $loaderTarget = $('body');
            var reloadLink = 'http://www.google.de';
            var contentcontentTarget = $loaderTarget;
            Icons.getIcon('spinner-circle', Icons.sizes.default, null, null, Icons.markupIdentifiers.inline).done(function (icon) {
                $loaderTarget.html('<div class="modal-loading">' + icon + '</div>');
                $.get(reloadLink, function (response) {
                    contentcontentTarget
                        .empty()
                        .append(response);
                    _this.init();
                }, 'html');
            });
        };
        Dashboard.prototype.functionTest = function () {
            alert('test');
        };
        return Dashboard;
    }());
    return new Dashboard().functionTest();
});
