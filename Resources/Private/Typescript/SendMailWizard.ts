import Modal = require('TYPO3/CMS/Backend/Modal');
import $ = require('jquery');
import Icons = require('TYPO3/CMS/Backend/Icons');


declare global {
  interface Window {
    TYPO3: any;
  }
}


/**
 * Module: TYPO3/CMS/BwBookingmanager/SendMailWizard
 *
 * @exports TYPO3/CMS/BwBookingmanager/SendMailWizard
 */
class SendMailWizard {

  private $sendMailButton: JQuery;
  private currentModal: JQuery;

  public init() {
    this.cacheElements()
    this.initEvents()

  }

  private cacheElements() {
    this.$sendMailButton = $('#sendMailButton')

  }

  private initEvents() {
    this.$sendMailButton.on('click', this.openModal.bind(this));
  }

  private openModal() {
    // collect modal infos
    const wizardUri = this.$sendMailButton.data('wizard-uri');
    const modalTitle = this.$sendMailButton.data('modal-title');
    const modalCancelButtonText = this.$sendMailButton.data('modal-cancel-button-text');
    const modalSendButtonText = this.$sendMailButton.data('modal-send-button-text');

    this.currentModal = Modal.advanced({
      type: 'ajax',
      content: wizardUri,
      size: Modal.sizes.large,
      title: modalTitle,
      style: Modal.styles.light,
      ajaxCallback: this.onButtonClick.bind(this),
      buttons: [
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
          text: modalSendButtonText,
          name: 'save',
          icon: 'actions-document-save',
          active: true,
          btnClass: 'btn-primary',
          dataAttributes: {
            action: 'save'
          },
          trigger: this.send.bind(this)
        }
      ]

    })
  }

  private onButtonClick(e: JQueryEventObject) {

  }

  private send(e: JQueryEventObject) {

  }

}

export = new SendMailWizard().init();
