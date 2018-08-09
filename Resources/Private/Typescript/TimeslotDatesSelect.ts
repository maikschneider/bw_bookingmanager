

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

  private calendarDataLinks: JQuery;
  private savedLink: JQuery;
  private newDataLink: JQuery;


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
    this.calendarDataLinks.on('click', this.onDataLinkClick.bind(this));
    if(this.savedLink) this.savedLink.on('click', this.onSavedLinkClick.bind(this));
  }

  /**
   * Click on the blue button (preselected date)
   * @param {JQueryEventObject} e
   */
  private onSavedLinkClick(e: JQueryEventObject)
  {
    e.preventDefault();
    // reset day selection
    if(this.newDataLink){
      this.newDataLink = null;
      this.calendarDataLinks.removeClass('active');
      $(e.currentTarget).removeClass('old');
    }
  }

  private onDataLinkClick(e: JQueryEventObject)
  {
    e.preventDefault();
    const link = $(e.currentTarget);

    // abbort if clicked again
    if(link.hasClass('active')){

      this.newDataLink = null;
      this.calendarDataLinks.removeClass('active');
      if (this.savedLink) this.savedLink.removeClass('old');

    }
    // new data link gets selected
    else {

      this.newDataLink = link;
      this.calendarDataLinks.removeClass('active');
      link.addClass('active');
      if (this.savedLink) this.savedLink.addClass('old');

    }
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
    this.newDataLink = null;
    this.calendarTabs = this.currentModal.find('.calendar-tab');
    this.calendarViews = this.currentModal.find('.calendar-view');
    this.sidebarLinks = this.currentModal.find('a.list-group-item');
    this.viewButtonSwitch = this.currentModal.find('.btn[name="view"]');
    this.calendarDataLinks = this.currentModal.find('.calendar-data-link');
    this.savedLink = this.currentModal.find('.bw_bookingmanager__day--isSelectedDay');
    if(!this.savedLink.length) this.savedLink = null;
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
