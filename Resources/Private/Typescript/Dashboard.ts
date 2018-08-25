import Icons = require('TYPO3/CMS/Backend/Icons');

class Dashboard {

	chartsUri: string = null
	chart1Button: JQuery<Element>

	public init()
	{
		this.cacheDOM();
		this.bindEvents();
	}

	private cacheDOM()
	{
		this.chart1Button = $('#chart1-tab-0-button');
	}

	private bindEvents()
	{
		this.chart1Button.on('click', this.onloadCharts.bind(this));
	}

	private onloadCharts(e: JQueryEventObject)
	{
		const chartUri = $(e.currentTarget).data('charts-uri');
		this.loadUrl(chartUri);
	}

	private loadUrl($url: string)
	{
		const $loaderTarget = $('.chart1-canvas');
		const contentcontentTarget = $loaderTarget;

		Icons.getIcon('spinner-circle', Icons.sizes.default, null, null, Icons.markupIdentifiers.inline).done((icon: string): void => {
			$loaderTarget.html('<div class="modal-loading">' + icon + '</div>');
			$.get(
				$url,
				(response: string): void => {
					this.initCharts(response)
				},
				'html'
			);
		});
	}

	private initCharts(data)
	{
		console.log(data);
	}

}

export = new Dashboard().init();
