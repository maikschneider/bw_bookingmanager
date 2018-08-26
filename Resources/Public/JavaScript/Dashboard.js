define(["require", "exports", "TYPO3/CMS/Backend/Icons", "TYPO3/CMS/BwBookingmanager/Chart"], function (require, exports, Icons, Chart) {
    "use strict";
    var Dashboard = /** @class */ (function () {
        function Dashboard() {
            this.chart1s = {};
        }
        Dashboard.prototype.init = function () {
            this.cacheDOM();
            this.bindEvents();
            // @TODO REMOVE
            this.chart1ViewButtons.first().trigger('click');
        };
        Dashboard.prototype.cacheDOM = function () {
            this.chart1ViewButtons = $('.chart1-view-button');
            this.chart1wrappers = $('.chart1-canvas');
            this.dropdownCheckIcon = $('span[data-identifier="actions-unmarkstate"]').first().clone();
            this.dropdownUnCheckIcon = $('span[data-identifier="actions-markstate"]').first().clone();
            console.log(this.dropdownCheckIcon);
            console.log(this.dropdownUnCheckIcon);
        };
        Dashboard.prototype.bindEvents = function () {
            this.chart1ViewButtons.on('click', this.updateChart1.bind(this));
        };
        Dashboard.prototype.updateChart1 = function (e) {
            var uri = $(e.currentTarget).data('chart-uri');
            var loaderTarget = this.chart1wrappers;
            var callback = this.initChart1;
            this.chart1ViewButtons.find('span.icon').replaceWith(this.dropdownUnCheckIcon);
            $(e.currentTarget).find('span.icon').replaceWith(this.dropdownCheckIcon);
            this.loadChart(uri, loaderTarget, callback);
        };
        Dashboard.prototype.loadChart = function (url, $loaderTarget, callback) {
            var _this = this;
            Icons.getIcon('spinner-circle', Icons.sizes.default, null, null, Icons.markupIdentifiers.inline).done(function (icon) {
                $loaderTarget.html(icon);
                $.get(url, callback.bind(_this), 'json');
            });
        };
        Dashboard.prototype.handleChartClick = function (e) {
            var calendarId = $(e.toElement).data('calendar');
            var chart = this.chart1s[calendarId];
            var chartElement = chart.getElementAtEvent(e);
            // @TODO: do voodoo
        };
        Dashboard.prototype.initChart1 = function (data) {
            for (var calendar in data.charts) {
                var $wrapper = this.chart1wrappers.filter('#' + calendar);
                var canvas = $('<canvas />').attr('width', '1000').attr('height', '200').attr('data-calendar', calendar);
                // hook into data to register click handler
                data.charts[calendar]['options']['onClick'] = this.handleChartClick.bind(this);
                $wrapper.empty().append(canvas);
                this.chart1s[calendar] = new Chart(canvas, data.charts[calendar]);
            }
        };
        return Dashboard;
    }());
    return new Dashboard().init();
});
