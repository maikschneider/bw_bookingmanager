

import Modal = require('TYPO3/CMS/Backend/Modal');
import $ = require('jquery');
import 'jquery-ui/draggable';
import 'jquery-ui/resizable';


declare global {
  interface Window {
    TYPO3: any;
  }
}


/**
* Module: TYPO3/CMS/BwBookingmanager/TimeslotDatesSelect
 *
* @exports TYPO3/CMS/BwBookingmanager/TimeslotDatesSelect
*/
class TimeslotDatesSelect {

  private trigger: JQuery;
  private currentModal: JQuery;


  /**
   * @method init
   * @desc Initilizes the timeslot dates select button element
   * @private
   */
  private init(): void
  {

  }

  private init()
  {
    console.log('ready');
  }

  private show()
  {
    const wizardUri = this.trigger.data('wizard-uri');
    const modalTitle = this.trigger.data('modal-title');
    const modalSaveButtonText = this.trigger.data('modal-save-button-text');
    const modalViewButtonText = this.trigger.data('modal-view-button-text');
    const modalCancelButtonText = this.trigger.data('modal-cancel-button-text');
    const calendarUid = $('select[name^="data[tx_bwbookingmanager_domain_model_entry]"][name$="[calendar]"] option:selected').val();

    this.currentModal = Modal.advanced({
      type: 'ajax',
      content: wizardUri,
      size: Modal.sizes.default,
      title: modalTitle,
      style: Modal.styles.light,
      ajaxCallback: this.init,
      buttons: [
        {
          text: modalViewButtonText,
          name: 'view',
          icon: 'actions-system-list-open',
          btnClass: 'btn-default btn-left',
          trigger: function () {

          }
        },
        {
          text: modalCancelButtonText,
          name: 'dismiss',
          icon: 'actions-close',
          btnClass: 'btn-default',
          dataAttributes: {
            action: 'dismiss'
          },
          trigger: function () {
            Modal.currentModal.trigger('modal-dismiss');
          }
        },
        {
          text: modalSaveButtonText,
          name: 'save',
          icon: 'actions-document-save',
          active: true,
          btnClass: 'btn-primary',
          dataAttributes: {
            action: 'save'
          },
          trigger: function () {
            Modal.currentModal.trigger('modal-dismiss');
          }
        }
      ]
    });
  }

  public initializeTrigger(): void {

    const triggerHandler: Function = (e: JQueryEventObject): void => {
      e.preventDefault();
      this.trigger = $(e.currentTarget);
      this.show();
    };

    $('.t3js-timeslotdatesselect-trigger').off('click').click(triggerHandler);
  }


}

export = new TimeslotDatesSelect();
