

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
  private calendarTabs: JQuery;
  private calendarViews: JQuery;
  private sidebarLinks: JQuery;
  private viewButtonSwitch: JQuery;


  /**
   * @method init
   * @desc Initilizes the timeslot dates select button element
   * @private
   */
  private init(): void
  {
    this.cacheDom();
    this.bindEvents();
  }

  private bindEvents()
  {
    this.sidebarLinks.on('click', this.onSidebarLinkClick.bind(this));
    this.viewButtonSwitch.on('click', this.onViewButtonClick.bind(this));
  }

  private onViewButtonClick(e: JQueryEventObject)
  {
    e.preventDefault();
    this.calendarViews.toggleClass('active');
    $(e.currentTarget).toggleClass('active');
  }

  private onSidebarLinkClick(e: JQueryEventObject)
  {
    e.preventDefault();

    this.sidebarLinks.removeClass('active');
    e.currentTarget.classList.add('active');

    this.calendarTabs.removeClass('active');
    const tabId = e.currentTarget.getAttribute('href');
    this.calendarTabs.filter(tabId).addClass('active');
  }

  private cacheDom()
  {
    this.calendarTabs = this.currentModal.find('.calendar-tab');
    this.calendarViews = this.currentModal.find('.calendar-view');
    this.sidebarLinks = this.currentModal.find('a.list-group-item');
    this.viewButtonSwitch = this.currentModal.find('.btn[name="view"]');
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
