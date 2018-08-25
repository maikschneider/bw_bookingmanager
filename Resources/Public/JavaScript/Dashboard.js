define(["require", "exports", "TYPO3/CMS/Backend/Icons"], function (require, exports, Icons) {
    "use strict";
    var Dashboard = /** @class */ (function () {
        function Dashboard() {
            this.chartsUri = null;
        }
        Dashboard.prototype.init = function () {
            this.cacheDOM();
            this.bindEvents();
        };
        Dashboard.prototype.cacheDOM = function () {
            this.chart1Button = $('#chart1-tab-0-button');
        };
        Dashboard.prototype.bindEvents = function () {
            this.chart1Button.on('click', this.onloadCharts.bind(this));
        };
        Dashboard.prototype.onloadCharts = function (e) {
            var chartUri = $(e.currentTarget).data('charts-uri');
            this.loadUrl(chartUri);
        };
        Dashboard.prototype.loadUrl = function ($url) {
            var _this = this;
            var $loaderTarget = $('.chart1-canvas');
            var contentcontentTarget = $loaderTarget;
            Icons.getIcon('spinner-circle', Icons.sizes.default, null, null, Icons.markupIdentifiers.inline).done(function (icon) {
                $loaderTarget.html('<div class="modal-loading">' + icon + '</div>');
                $.get($url, function (response) {
                    _this.initCharts(response);
                }, 'html');
            });
        };
        Dashboard.prototype.initCharts = function (data) {
            console.log(data);
        };
        return Dashboard;
    }());
    return new Dashboard().init();
});
