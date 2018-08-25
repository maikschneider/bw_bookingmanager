import Icons = require('TYPO3/CMS/Backend/Icons');

class Dashboard {

	public init()
	{
		const $loaderTarget = $('body');
		const reloadLink = 'http://www.google.de';
		const contentcontentTarget = $loaderTarget;

		Icons.getIcon('spinner-circle', Icons.sizes.default, null, null, Icons.markupIdentifiers.inline).done((icon: string): void => {
			$loaderTarget.html('<div class="modal-loading">' + icon + '</div>');
			$.get(
				reloadLink,
				(response: string): void => {
					contentcontentTarget
						.empty()
						.append(response);
					this.init();
				},
				'html'
			);
		});
	}


	public functionTest()
	{
		alert('test');
	}

}

//export = new Dashboard().init();
