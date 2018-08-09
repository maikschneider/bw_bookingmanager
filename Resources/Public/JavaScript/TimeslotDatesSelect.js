define(["require", "exports", "TYPO3/CMS/Backend/Modal", "jquery", "jquery-ui/draggable", "jquery-ui/resizable"], function (require, exports, Modal, $) {
    "use strict";
    /**
    * Module: TYPO3/CMS/BwBookingmanager/TimeslotDatesSelect
     *
    * @exports TYPO3/CMS/BwBookingmanager/TimeslotDatesSelect
    */
    var TimeslotDatesSelect = /** @class */ (function () {
        function TimeslotDatesSelect() {
        }
        /**
         * @method init
         * @desc Initilizes the timeslot dates select button element
         * @private
         */
        TimeslotDatesSelect.prototype.init = function () {
            this.cacheDom();
            this.bindEvents();
        };
        TimeslotDatesSelect.prototype.bindEvents = function () {
            this.sidebarLinks.on('click', this.onSidebarLinkClick.bind(this));
            this.viewButtonSwitch.on('click', this.onViewButtonClick.bind(this));
        };
        TimeslotDatesSelect.prototype.onViewButtonClick = function (e) {
            e.preventDefault();
            this.calendarViews.toggleClass('active');
            $(e.currentTarget).toggleClass('active');
        };
        TimeslotDatesSelect.prototype.onSidebarLinkClick = function (e) {
            e.preventDefault();
            this.sidebarLinks.removeClass('active');
            e.currentTarget.classList.add('active');
            this.calendarTabs.removeClass('active');
            var tabId = e.currentTarget.getAttribute('href');
            this.calendarTabs.filter(tabId).addClass('active');
        };
        TimeslotDatesSelect.prototype.cacheDom = function () {
            this.calendarTabs = this.currentModal.find('.calendar-tab');
            this.calendarViews = this.currentModal.find('.calendar-view');
            this.sidebarLinks = this.currentModal.find('a.list-group-item');
            this.viewButtonSwitch = this.currentModal.find('.btn[name="view"]');
        };
        TimeslotDatesSelect.prototype.show = function () {
            var wizardUri = this.trigger.data('wizard-uri');
            var modalTitle = this.trigger.data('modal-title');
            var modalSaveButtonText = this.trigger.data('modal-save-button-text');
            var modalViewButtonText = this.trigger.data('modal-view-button-text');
            var modalCancelButtonText = this.trigger.data('modal-cancel-button-text');
            var calendarUid = $('select[name^="data[tx_bwbookingmanager_domain_model_entry]"][name$="[calendar]"] option:selected').val();
            this.currentModal = Modal.advanced({
                type: 'ajax',
                content: wizardUri,
                size: Modal.sizes.default,
                title: modalTitle,
                style: Modal.styles.light,
                ajaxCallback: this.init.bind(this),
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
        };
        TimeslotDatesSelect.prototype.initializeTrigger = function () {
            var _this = this;
            var triggerHandler = function (e) {
                e.preventDefault();
                _this.trigger = $(e.currentTarget);
                _this.show();
            };
            $('.t3js-timeslotdatesselect-trigger').off('click').click(triggerHandler);
        };
        return TimeslotDatesSelect;
    }());
    return new TimeslotDatesSelect();
});
