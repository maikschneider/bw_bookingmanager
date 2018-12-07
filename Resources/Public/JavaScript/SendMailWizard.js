define(["require", "exports", "TYPO3/CMS/Backend/Modal", "jquery"], function (require, exports, Modal, $) {
    "use strict";
    /**
     * Module: TYPO3/CMS/BwBookingmanager/SendMailWizard
     *
     * @exports TYPO3/CMS/BwBookingmanager/SendMailWizard
     */
    var SendMailWizard = /** @class */ (function () {
        function SendMailWizard() {
        }
        SendMailWizard.prototype.init = function () {
            this.cacheElements();
            this.initEvents();
        };
        SendMailWizard.prototype.cacheElements = function () {
            this.$sendMailButton = $('#sendMailButton');
        };
        SendMailWizard.prototype.initEvents = function () {
            this.$sendMailButton.on('click', this.openModal.bind(this));
        };
        SendMailWizard.prototype.openModal = function () {
            // collect modal infos
            var wizardUri = this.$sendMailButton.data('wizard-uri');
            var modalTitle = this.$sendMailButton.data('modal-title');
            var modalCancelButtonText = this.$sendMailButton.data('modal-cancel-button-text');
            var modalSendButtonText = this.$sendMailButton.data('modal-send-button-text');
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
            });
        };
        SendMailWizard.prototype.onButtonClick = function (e) {
        };
        SendMailWizard.prototype.send = function (e) {
        };
        return SendMailWizard;
    }());
    return new SendMailWizard().init();
});
