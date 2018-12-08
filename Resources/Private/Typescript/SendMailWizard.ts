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
  private $loaderTarget: JQuery;

  public init() {
    this.cacheElements();
    this.initEvents();

  }

  private cacheElements() {
    this.$sendMailButton = $('#sendMailButton');
  }

  private initEvents() {
    this.$sendMailButton.on('click', this.onButtonClick.bind(this));
  }

  private onButtonClick(e: JQueryEventObject) {
    e.preventDefault();
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
      ajaxCallback: this.onModalOpened.bind(this),
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
          icon: 'actions-check',
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

  private onModalOpened() {
    this.$loaderTarget = this.currentModal.find('#emailPreview');

    const templateSelector = this.currentModal.find('select#emailTemplate');
    const previewUri = templateSelector.find('option:selected').data('preview-uri');
    const $closeButton = this.currentModal.find('#phoneCloseButton');

    // onload first template
    this.loadEmailPreview(previewUri);

    // bind template change event
    templateSelector.on('change', function (el) {
      const previewUri = $(el.currentTarget).find('option:selected').data('preview-uri');
      const $markerFieldset = this.currentModal.find('#markerOverrideFieldset');

      // reset override fields
      $markerFieldset.html('');
      // load first preview
      this.loadEmailPreview(previewUri, true);
    }.bind(this));

    // bind home button event
    $closeButton.on('click', this.phoneClosingAnimation.bind(this));

  }

  private phoneClosingAnimation(e: JQueryEventObject) {
    e.preventDefault();
    this.$loaderTarget.toggleClass('closeing');
  }

  private loadEmailPreview(uri) {
    Icons.getIcon('spinner-circle', Icons.sizes.default, null, null, Icons.markupIdentifiers.inline).done((icon: string): void => {
      this.$loaderTarget.html(icon);
      $.get(
        uri,
        this.showEmailPreview.bind(this, true),
        'json'
      );
    });
  }

  private showEmailPreview(createMarkerFieldset, data) {

    this.$loaderTarget.html('<iframe frameborder="0" style="width:100%; min-height:calc(100vh - 400px); margin-bottom: -5px;" src="' + data.src + '"></iframe>');

    if (createMarkerFieldset) {
      this.createMarkerFieldset(data);
    }
  }

  private createMarkerFieldset(data) {
    const $markerFieldset = this.currentModal.find('#markerOverrideFieldset');

    // template contains no markers
    if (!data.hasOwnProperty('markerContent') || !data.markerContent.length) {
      $markerFieldset.html('');
      $markerFieldset.hide();
      return;
    }

    // create input fields und bind event to update preview
    for (let i = 0; i < data.markerContent.length; i++) {
      const m = data.markerContent[i];
      let $input = (m.content && m.content.length) > 25 ? $('<textarea />') : $('<input />');
      $input
        .attr('name', 'markerOverrides[' + m.name + ']')
        .attr('id', 'markerOverrides[' + m.name + ']')
        .attr('placeholder', m.content)
        .attr('class', 'form-control')
        .bind('blur', this.onOverrideMarkerBlur.bind(this));

      $input = $input.wrap('<div class="form-control-wrap"></div>').parent();
      $input = $input.wrap('<div class="form-group"></div>').parent();
      $input.prepend('<label for="markerOverrides[' + m.name + ']">' + m.name + ' override</label>');

      $markerFieldset.append($input);
    }

    $markerFieldset.show();
  }

  private onOverrideMarkerBlur() {
    const templateSelector = this.currentModal.find('select#emailTemplate');
    const previewUri = templateSelector.find('option:selected').data('preview-uri');

    Icons.getIcon('spinner-circle', Icons.sizes.default, null, null, Icons.markupIdentifiers.inline).done((icon: string): void => {
      this.$loaderTarget.html(icon);
      $.post(
        previewUri,
        this.currentModal.find('#markerOverrideFieldset input, #markerOverrideFieldset textarea').serializeArray(),
        this.showEmailPreview.bind(this, false),
        'json'
      );
    });
  }

  private send(e: JQueryEventObject) {

  }

}

export = new SendMailWizard().init();
