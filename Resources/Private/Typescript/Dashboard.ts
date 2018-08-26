import Icons = require('TYPO3/CMS/Backend/Icons');
import Chart = require('TYPO3/CMS/BwBookingmanager/Chart');



class Dashboard {

	chartsUri: string = null;
	chart1Button: JQuery<Element>;
	chart1wrappers: JQuery;

	chart1canvas: JQuery = null;

	public init()
	{
		this.cacheDOM();
		this.bindEvents();

		// @TODO REMOVE
		this.chart1Button.trigger('click');
	}

	private cacheDOM()
	{
		this.chart1Button = $('#chart1-tab-0-button');
		this.chart1wrappers = $('.chart1-canvas');

	}

	private bindEvents()
	{
		this.chart1Button.on('click', this.updateChart1.bind(this));
	}

	private updateChart1(e: JQueryEventObject)
	{
		const uri = $(e.currentTarget).data('chart-uri');
		const $loaderTarget = this.chart1wrappers;

		this.loadChart(uri, $loaderTarget, this.initChart1);
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

	private initChart1(data)
	{
		for(let calendar in data.charts){

			const $wrapper = this.chart1wrappers.filter('#'+calendar);

			const canvas: JQuery = $('<canvas />').attr('width', '1000').attr('height', '200');

			$wrapper.empty().append(canvas);

			const barChart = new Chart(canvas, data.charts[calendar]);

		}
	}

}

export = new Dashboard().init();
