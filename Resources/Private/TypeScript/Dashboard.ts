import Icons = require('TYPO3/CMS/Backend/Icons');
import Chart = require('TYPO3/CMS/BwBookingmanager/Chart');



class Dashboard {

	chart1ViewButtons: JQuery;
	chart1wrappers: JQuery;
	chart1NavButtons: JQuery;
	chart1s: Object = {};

	public init()
	{
		this.cacheDOM();
		this.bindEvents();

		// @TODO REMOVE
		this.chart1ViewButtons.first().trigger('click');
	}

	private cacheDOM()
	{
		this.chart1ViewButtons = $('.chart1-view-button');
		this.chart1wrappers = $('.chart1-canvas');
		this.chart1NavButtons = $('.chart1-nav-button');
	}

	private bindEvents()
	{
		this.chart1ViewButtons.on('click', this.onChart1ViewChange.bind(this));
		this.chart1NavButtons.on('click', this.onChart1NavChange.bind(this));
	}

	private onChart1ViewChange(e: JQueryEventObject)
	{
		const uri = $(e.currentTarget).attr('data-chart-uri');
		const loaderTarget = this.chart1wrappers;
		const callback = this.initChart1;

		this.chart1ViewButtons.removeClass('active');
		$(e.currentTarget).addClass('active');

		this.loadChart(uri, loaderTarget, callback);
	}

	private onChart1NavChange(e: JQueryEventObject)
	{
		const uri = $(e.currentTarget).attr('data-chart-uri');
		const loaderTarget = this.chart1wrappers;
		const callback = this.initChart1;

		this.loadChart(uri, loaderTarget, callback);
	}

	private loadChart(url: string, $loaderTarget, callback)
	{
		Icons.getIcon('spinner-circle', Icons.sizes.default, null, null, Icons.markupIdentifiers.inline).done((icon: string): void => {
			$loaderTarget.html(icon);
			$.get(
				url,
				callback.bind(this),
				'json'
			);
		});
	}

	public handleChartClick(e: MouseEvent)
	{
		const calendarId  = $(e.currentTarget).attr('data-calendar');

		const chart = this.chart1s[calendarId];
		const chartElement = chart.getElementAtEvent(e);

		console.log(chartElement);

		// @TODO: do voodoo
	}

	private initChart1(data)
	{
		// set new links
		this.chart1NavButtons.filter('.prev').attr('data-chart-uri', data.prevLink);
		this.chart1NavButtons.filter('.next').attr('data-chart-uri', data.nextLink);

		for(let calendar in data.charts){

			const $wrapper = this.chart1wrappers.filter('#'+calendar);

			const canvas: JQuery = $('<canvas />').attr('width', '1000').attr('height', '300').attr('data-calendar', calendar);

			// hook into data to register click handler
			data.charts[calendar]['options']['onClick'] = this.handleChartClick.bind(this);

			$wrapper.empty().append(canvas);

			this.chart1s[calendar] = new Chart(canvas, data.charts[calendar]);

		}

	}

}

export = new Dashboard().init();
