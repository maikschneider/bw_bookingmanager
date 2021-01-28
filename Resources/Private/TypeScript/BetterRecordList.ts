import Icons = require('TYPO3/CMS/Backend/Icons');
import PersistentStorage = require("TYPO3/CMS/Backend/Storage/Persistent");
import ResponseInterface = require("TYPO3/CMS/Backend/AjaxDataHandler/ResponseInterface");
import MessageInterface = require("TYPO3/CMS/Backend/AjaxDataHandler/MessageInterface");
import Notification = require('TYPO3/CMS/Backend/Notification');
import Tooltip = require('TYPO3/CMS/Backend/Tooltip');

enum Identifiers {
	confirm = '.t3js-record-confirm',
	icon = '.t3js-icon'
}

class BetterRecordList{



	constructor() {
		$((): void => {
			this.initialize();
		});
	}

	public initialize()
	{
		this.styleRows();

		$(document).on('click', Identifiers.confirm, (e: JQueryEventObject): void => {
			e.preventDefault();
			const $anchorElement = $(e.currentTarget);
			const $iconElement = $anchorElement.find(Identifiers.icon);
			const $rowElement = $anchorElement.closest('tr[data-uid]');
			const params = $anchorElement.data('params');

			// add a spinner
			this._showSpinnerIcon($iconElement);

			// make the AJAX call to toggle the visibility
			this._call(params).always((result): void => {
				// print messages on errors
				if (result.hasErrors) {
					this.handleErrors(result);
				} else {
					// adjust overlay icon
					this.toggleRow($rowElement);
				}
			});
		})
	}

	private styleRows()
	{
		$('tr.t3js-entity[data-table="tx_bwbookingmanager_domain_model_entry"]').each(function(i, e){
			const isConfirmed = $(e).find('.t3js-record-confirm').attr('data-confirmed');
			if(isConfirmed=='no') $(e).addClass('not-confirmed');
		});
	}

	/**
	 * Toggle row visibility after record has been changed
	 *
	 * @param {JQuery} $rowElement
	 */
	private toggleRow($rowElement: JQuery<Element>): void {

		const $anchorElement = $rowElement.find(Identifiers.confirm);
		const table = $anchorElement.closest('table[data-table]').data('table');
		const params = $anchorElement.data('params');
		let nextParams;
		let nextState;
		let iconName;

		if ($anchorElement.data('confirmed') === 'no') {
			nextState = 'yes';
			nextParams = params.replace('=1', '=0');
			iconName = 'actions-edit-hide';
			$rowElement.removeClass('not-confirmed');
		} else {
			nextState = 'no';
			nextParams = params.replace('=0', '=1');
			iconName = 'actions-edit-unhide';
			$rowElement.addClass('not-confirmed');
		}
		$anchorElement.data('confirmed', nextState).data('params', nextParams);

		const newTitle = $anchorElement.attr('data-toggle-title');
		$anchorElement.attr('data-toggle-title', $anchorElement.attr('data-original-title'));
		$anchorElement.attr('data-original-title', newTitle);
		$anchorElement.tooltip('hide');

		const $iconElement = $anchorElement.find(Identifiers.icon);
		Icons.getIcon(iconName, Icons.sizes.small).done((icon: string): void => {
			$iconElement.replaceWith(icon);
		});

	}

	/**
	 * AJAX call to record_process route (SimpleDataHandlerController->processAjaxRequest)
	 * returns a jQuery Promise to work with
	 *
	 * @param {Object} params
	 * @returns {JQueryXHR}
	 */
	private _call(params: Object): JQueryXHR {
		return $.getJSON(TYPO3.settings.ajaxUrls.record_process, params);
	}

	/**
	 * Replace the given icon with a spinner icon
	 *
	 * @param {Object} $iconElement
	 * @private
	 */
	private _showSpinnerIcon($iconElement: JQuery<Element>): void {
		Icons.getIcon('spinner-circle-dark', Icons.sizes.small).done((icon: string): void => {
			$iconElement.replaceWith(icon);
		});
	}

	/**
	 * Handle the errors from result object
	 *
	 * @param {Object} result
	 */
	private handleErrors(result): void {
		$.each(result.messages, (position: number, message): void => {
			console.log(message);
			Notification.error(message.title, message.message);
		});
	}
}

export = new BetterRecordList()
