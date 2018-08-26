define(["require", "exports", "TYPO3/CMS/Backend/Icons", "TYPO3/CMS/BwBookingmanager/Chart"], function (require, exports, Icons, Chart) {
    "use strict";
    var Dashboard = /** @class */ (function () {
        function Dashboard() {
            this.chartsUri = null;
            this.chart1canvas = null;
        }
        Dashboard.prototype.init = function () {
            this.cacheDOM();
            this.bindEvents();
            // @TODO REMOVE
            this.chart1Button.trigger('click');
        };
        Dashboard.prototype.cacheDOM = function () {
            this.chart1Button = $('#chart1-tab-0-button');
            this.chart1wrappers = $('.chart1-canvas');
        };
        Dashboard.prototype.bindEvents = function () {
            this.chart1Button.on('click', this.updateChart1.bind(this));
        };
        Dashboard.prototype.updateChart1 = function (e) {
            var uri = $(e.currentTarget).data('chart-uri');
            var $loaderTarget = this.chart1wrappers;
            this.loadChart(uri, $loaderTarget, this.initChart1);
        };
        Dashboard.prototype.loadChart = function (url, $loaderTarget, callback) {
            var _this = this;
            Icons.getIcon('spinner-circle', Icons.sizes.default, null, null, Icons.markupIdentifiers.inline).done(function (icon) {
                $loaderTarget.html(icon);
                $.get(url, callback.bind(_this), 'json');
            });
        };
        Dashboard.prototype.initChart1 = function (data) {
            for (var calendar in data.charts) {
                var $wrapper = this.chart1wrappers.filter('#' + calendar);
                var canvas = $('<canvas />').attr('width', '1000').attr('height', '200');
                $wrapper.empty().append(canvas);
                var barChart = new Chart(canvas, data.charts[calendar]);
            }
        };
        return Dashboard;
    }());
    return new Dashboard().init();
});
