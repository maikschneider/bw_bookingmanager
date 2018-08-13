

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
  private calendarSelectField: JQuery;
  private hiddenStartDateField: JQuery;
  private hiddenEndDateField: JQuery;
  private hiddenTimeslotField: JQuery;
  private startDateText: JQuery;
  private endDateText: JQuery;
  private dayDetailLinks: JQuery;
  private dayDetailDivs: JQuery;


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
    this.calendarDataLinks.on('click', this.onDataLinkClick.bind(this));
    this.dayDetailLinks.on('mouseenter', this.onDayDetailLinkMouseenter.bind(this));
    this.dayDetailLinks.on('mouseleave', this.onDayDetailLinkMouseleave.bind(this));
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

  private onDayDetailLinkMouseenter(e: JQueryEventObject)
  {
    this.dayDetailDivs.removeClass('active');
    const link = $(e.currentTarget).data('day-detail');
    this.dayDetailDivs.filter('#'+link).addClass('active');
  }

  private onDayDetailLinkMouseleave(e: JQueryEventObject) {
      this.dayDetailDivs.removeClass('active');
      const selectedDiv = this.dayDetailDivs.filter('.daydetail--selected');
      const blueDiv = this.dayDetailDivs.filter('.daydetail--blue');

      if (selectedDiv.length) {
		  $(selectedDiv).addClass('active');
	  }
      else if(blueDiv.length){
        $(blueDiv).addClass('active');
      }
  }


  private onDataLinkClick(e: JQueryEventObject)
  {
    e.preventDefault();
    const link = $(e.currentTarget);

    this.dayDetailDivs.removeClass('daydetail--selected');

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
      // add class to make the day detail view of the new day link active after mouseleave
      this.dayDetailDivs.filter('#'+link.data('day-detail')).addClass('daydetail--selected');

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
    this.calendarDataLinks = this.currentModal.find('.calendar-data-link');
    this.savedLink = this.currentModal.find('.bw_bookingmanager__day--isSelectedDay');
    this.dayDetailLinks = this.currentModal.find('[data-day-detail]');
    this.dayDetailDivs = this.currentModal.find('.daydetail');
    if(!this.savedLink.length) this.savedLink = null;
  }

  private show()
  {
    const wizardUri = this.trigger.data('wizard-uri');
    const modalTitle = this.trigger.data('modal-title');
    const modalSaveButtonText = this.trigger.data('modal-save-button-text');
    const modalViewButtonText = this.trigger.data('modal-view-button-text');
    const modalCancelButtonText = this.trigger.data('modal-cancel-button-text');

    this.calendarSelectField = $('select[name^="data[tx_bwbookingmanager_domain_model_entry]"][name$="[calendar]"]');
    this.hiddenStartDateField = $('input[name^="data[tx_bwbookingmanager_domain_model_entry]"][name$="[start_date]"]');
    this.hiddenEndDateField = $('input[name^="data[tx_bwbookingmanager_domain_model_entry]"][name$="[end_date]"]');
    this.hiddenTimeslotField = $('input[name^="data[tx_bwbookingmanager_domain_model_entry]"][name$="[timeslot]"]');

    this.startDateText = $('#savedStartDate');
    this.endDateText = $('#savedEndDate');

    this.currentModal = Modal.advanced({
      type: 'ajax',
      content: wizardUri,
      size: Modal.sizes.large,
      title: modalTitle,
      style: Modal.styles.light,
      ajaxCallback: this.init.bind(this),
      buttons: [
        {
          text: modalViewButtonText,
          name: 'view',
          icon: 'actions-system-list-open',
          btnClass: 'btn-default btn-left',
          trigger: this.onViewButtonClick.bind(this)
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
          trigger: this.save.bind(this)
        }
      ]
    });
    //this.currentModal.addClass('JFJWFJEJFEFHIWEFHWOIFHWEIFOHEWOIFHWIFHWE');
  }

  private save()
  {
    if(this.newDataLink){
      this.calendarSelectField.val(this.newDataLink.data('calendar'));
      this.hiddenStartDateField.val(this.newDataLink.data('start-date'));
      this.hiddenEndDateField.val(this.newDataLink.data('end-date'));
      this.hiddenTimeslotField.val(this.newDataLink.data('timeslot'));

      this.startDateText.html(this.newDataLink.data('start-date-text'));
      this.endDateText.html(this.newDataLink.data('end-date-text'));
    }
    Modal.currentModal.trigger('modal-dismiss');
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
