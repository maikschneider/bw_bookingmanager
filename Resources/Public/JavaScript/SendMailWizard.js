define(["require", "exports", "TYPO3/CMS/Backend/Modal", "jquery", "TYPO3/CMS/Backend/Icons"], function (require, exports, Modal, $, Icons) {
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
            this.$sendMailButton.on('click', this.onButtonClick.bind(this));
        };
        SendMailWizard.prototype.onButtonClick = function (e) {
            e.preventDefault();
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
            });
        };
        SendMailWizard.prototype.onModalOpened = function () {
            this.$loaderTarget = this.currentModal.find('#emailPreview');
            var templateSelector = this.currentModal.find('select#emailTemplate');
            var previewUri = templateSelector.find('option:selected').data('preview-uri');
            var $closeButton = this.currentModal.find('#phoneCloseButton');
            // onload first template
            this.loadEmailPreview(previewUri);
            // bind template change event
            templateSelector.on('change', function (el) {
                var previewUri = $(el.currentTarget).find('option:selected').data('preview-uri');
                var $markerFieldset = this.currentModal.find('#markerOverrideFieldset');
                // reset override fields
                $markerFieldset.html('');
                // load first preview
                this.loadEmailPreview(previewUri, true);
            }.bind(this));
            // bind home button event
            $closeButton.on('click', this.phoneClosingAnimation.bind(this));
        };
        SendMailWizard.prototype.phoneClosingAnimation = function (e) {
            e.preventDefault();
            this.$loaderTarget.toggleClass('closeing');
        };
        SendMailWizard.prototype.loadEmailPreview = function (uri) {
            var _this = this;
            Icons.getIcon('spinner-circle', Icons.sizes.default, null, null, Icons.markupIdentifiers.inline).done(function (icon) {
                _this.$loaderTarget.html(icon);
                $.get(uri, _this.showEmailPreview.bind(_this, true), 'json');
            });
        };
        SendMailWizard.prototype.showEmailPreview = function (createMarkerFieldset, data) {
            this.$loaderTarget.html('<iframe frameborder="0" style="width:100%; min-height:calc(100vh - 400px); margin-bottom: -5px;" src="' + data.src + '"></iframe>');
            if (createMarkerFieldset) {
                this.createMarkerFieldset(data);
            }
        };
        SendMailWizard.prototype.createMarkerFieldset = function (data) {
            var $markerFieldset = this.currentModal.find('#markerOverrideFieldset');
            // template contains no markers
            if (!data.hasOwnProperty('markerContent') || !data.markerContent.length) {
                $markerFieldset.html('');
                $markerFieldset.hide();
                return;
            }
            // create input fields und bind event to update preview
            for (var i = 0; i < data.markerContent.length; i++) {
                var m = data.markerContent[i];
                var $input = (m.content && m.content.length) > 25 ? $('<textarea />') : $('<input />');
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
        };
        SendMailWizard.prototype.onOverrideMarkerBlur = function () {
            var _this = this;
            var templateSelector = this.currentModal.find('select#emailTemplate');
            var previewUri = templateSelector.find('option:selected').data('preview-uri');
            Icons.getIcon('spinner-circle', Icons.sizes.default, null, null, Icons.markupIdentifiers.inline).done(function (icon) {
                _this.$loaderTarget.html(icon);
                $.post(previewUri, _this.currentModal.find('#markerOverrideFieldset input, #markerOverrideFieldset textarea').serializeArray(), _this.showEmailPreview.bind(_this, false), 'json');
            });
        };
        SendMailWizard.prototype.send = function (e) {
        };
        return SendMailWizard;
    }());
    return new SendMailWizard().init();
});
