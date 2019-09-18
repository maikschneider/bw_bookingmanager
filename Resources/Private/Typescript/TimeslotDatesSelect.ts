import Modal = require('TYPO3/CMS/Backend/Modal');
import $ = require('jquery');
import Icons = require('TYPO3/CMS/Backend/Icons');
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
  private multipleTimeslotsLinks: JQuery;
  private modalReloadLinks: JQuery;
  private currentCalendarViewUid: any;
  private feUserInput: JQuery;

  private calendarDataLinks: JQuery;
  private savedLink: JQuery;
  private newDataLink: JQuery;


  /**
   * @method init
   * @desc Initilizes the timeslot dates select button element
   * @private
   */
  private init(): void {
    this.cacheModalDom();
    this.bindEvents();
    this.openFirstView();
  }

  private bindEvents() {
    this.sidebarLinks.on('click', this.onSidebarLinkClick.bind(this));
    this.calendarDataLinks.on('click', this.onDataLinkClick.bind(this));
    this.dayDetailLinks.on('click', this.onDayDetailLinkClick.bind(this));
    this.dayDetailLinks.on('mouseenter', this.onDayDetailLinkMouseenter.bind(this));
    this.dayDetailLinks.on('mouseleave', this.onDayDetailLinkMouseleave.bind(this));
    this.modalReloadLinks.on('click', this.onReloadModalLinkClick.bind(this));
    if (this.savedLink) this.savedLink.on('click', this.onSavedLinkClick.bind(this));
  }

  private openFirstView() {
    // check for any active sidebar link.
    // if there is none, no calendaruid is set in the current entry -> means, new entry. so guess the calendar
    if (this.sidebarLinks.find('.active').length) return;

    const calendarToOpen = this.currentCalendarViewUid ? this.currentCalendarViewUid : this.calendarSelectField.find('option:selected').val();

    if (!calendarToOpen) return;

    this.sidebarLinks.filter('[data-calendar-uid="' + calendarToOpen + '"]').trigger('click');
  }

  /**
   * Click on the blue button (preselected date)
   * @param {JQueryEventObject} e
   */
  private onSavedLinkClick(e: JQueryEventObject) {
    e.preventDefault();
    // reset day selection
    if (this.newDataLink) {
      this.newDataLink = null;
      this.calendarDataLinks.removeClass('active');
      $(e.currentTarget).removeClass('old');
    }
  }

  private onDayDetailLinkMouseenter(e: JQueryEventObject) {
    this.dayDetailDivs.removeClass('active');
    const link = $(e.currentTarget).data('day-detail');
    this.dayDetailDivs.filter('#' + link).addClass('active');
  }

  private onDayDetailLinkMouseleave() {
    this.dayDetailDivs.removeClass('active');
    const selectedDiv = this.dayDetailDivs.filter('.daydetail--selected');
    const blueDiv = this.dayDetailDivs.filter('.daydetail--blue');

    if (selectedDiv.length) {
      $(selectedDiv).addClass('active');
    } else if (blueDiv.length) {
      $(blueDiv).addClass('active');
    }
  }


  private onDayDetailLinkClick(e: JQueryEventObject) {
    e.preventDefault();

    const daylink = $(e.currentTarget);
    const dayDetailDiv = this.dayDetailDivs.filter('#' + daylink.data('day-detail'));

    // reset if clicked again (click active data link again)
    if (daylink.hasClass('active')) {
      const activeDataLink = dayDetailDiv.find('.calendar-data-link.active');
      activeDataLink.trigger('click');
    }
    // new day -> click data link
    else {
      const firstDataLink = dayDetailDiv.find('.calendar-data-link').first();
      firstDataLink.trigger('click');
    }

  }

  private onDataLinkClick(e: JQueryEventObject) {

    e.preventDefault();
    const link = $(e.currentTarget);


    this.dayDetailDivs.removeClass('daydetail--selected');
    this.dayDetailLinks.removeClass('active');

    const daylink = this.dayDetailLinks.filter('[data-day-detail="' + link.data('day-link') + '"]');
    const dayDetailDiv = this.dayDetailDivs.filter('#' + link.data('day-link'));

    // abbort if clicked again
    if (link.hasClass('active')) {

      daylink.removeClass('active');
      this.newDataLink = null;
      this.calendarDataLinks.removeClass('active');
      this.calendarDataLinks.filter('.saved-active').addClass('active');
      this.onDayDetailLinkMouseleave();
      if (this.savedLink) this.savedLink.removeClass('old');

    }
    // new data link gets selected
    else {

      daylink.addClass('active');
      dayDetailDiv.addClass('daydetail--selected');

      // @ts-ignore
      this.newDataLink = link;
      this.calendarDataLinks.removeClass('active');
      link.addClass('active');
      if (this.savedLink) this.savedLink.addClass('old');
    }

  }

  private onViewButtonClick(e: JQueryEventObject) {
    e.preventDefault();
    this.calendarViews.toggleClass('active');
    $(e.currentTarget).toggleClass('active');
  }

  private onSidebarLinkClick(e: JQueryEventObject) {
    e.preventDefault();

    // switch sidebarlinks
    this.sidebarLinks.removeClass('active');
    e.currentTarget.classList.add('active');

    // switch tabs
    this.calendarTabs.removeClass('active');
    const tabId = e.currentTarget.getAttribute('href');
    this.calendarTabs.filter(tabId).addClass('active');

    // save uid for next reopen
    this.currentCalendarViewUid = $(e.currentTarget).data('calendar-uid');
  }

  private cacheModalDom() {
    this.newDataLink = null;
    this.calendarTabs = this.currentModal.find('.calendar-tab');
    this.calendarViews = this.currentModal.find('.calendar-view');
    this.sidebarLinks = this.currentModal.find('a.list-group-item');
    this.calendarDataLinks = this.currentModal.find('.calendar-data-link');
    this.multipleTimeslotsLinks = this.currentModal.find('.multiple-timeslots-link');
    this.savedLink = this.currentModal.find('.bw_bookingmanager__day--isSelectedDay');
    this.dayDetailLinks = this.currentModal.find('[data-day-detail]');
    this.dayDetailDivs = this.currentModal.find('.daydetail');
    this.modalReloadLinks = this.currentModal.find('.modal-reload');
    if (!this.savedLink.length) this.savedLink = null;
  }

  private cacheFormDom() {
    this.trigger = $('.t3js-timeslotdatesselect-trigger');
    this.calendarSelectField = $('select[name^="data[tx_bwbookingmanager_domain_model_entry]"][name$="[calendar]"]');
    this.hiddenStartDateField = $('input[name^="data[tx_bwbookingmanager_domain_model_entry]"][name$="[start_date]"]');
    this.hiddenEndDateField = $('input[name^="data[tx_bwbookingmanager_domain_model_entry]"][name$="[end_date]"]');
    this.hiddenTimeslotField = $('input[name^="data[tx_bwbookingmanager_domain_model_entry]"][name$="[timeslot]"]');

    this.startDateText = $('#savedStartDate');
    this.endDateText = $('#savedEndDate');
  }

  private show() {
    const wizardUri = this.trigger.data('wizard-uri');
    const modalTitle = this.trigger.data('modal-title');
    const modalSaveButtonText = this.trigger.data('modal-save-button-text');
    const modalViewButtonText = this.trigger.data('modal-view-button-text');
    const modalCancelButtonText = this.trigger.data('modal-cancel-button-text');

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
  }

  private save() {
    if (this.newDataLink) {
      this.calendarSelectField.val(this.newDataLink.data('calendar'));
      this.hiddenStartDateField.val(this.newDataLink.data('start-date'));
      this.hiddenEndDateField.val(this.newDataLink.data('end-date'));
      this.hiddenTimeslotField.val(this.newDataLink.data('timeslot'));

      this.startDateText.html(this.newDataLink.data('start-date-text'));
      this.endDateText.html(this.newDataLink.data('end-date-text'));
    }
    Modal.currentModal.trigger('modal-dismiss');
  }

  private onReloadModalLinkClick(e: JQueryEventObject) {
    e.preventDefault();
    const reloadLink = $(e.currentTarget).attr('href');

    const contentTarget = '.t3js-modal-body';
    const $loaderTarget = this.currentModal.find(contentTarget);
    Icons.getIcon('spinner-circle', Icons.sizes.default, null, null, Icons.markupIdentifiers.inline).done((icon: string): void => {
      $loaderTarget.html('<div class="modal-loading">' + icon + '</div>');
      $.get(
        reloadLink,
        (response: string): void => {
          this.currentModal.find(contentTarget)
            .empty()
            .append(response);
          this.init();
          this.currentModal.trigger('modal-loaded');
        },
        'html'
      );
    });

  }

  /**
   * Set default values that were added to the trigger.
   */
  private setDefaults() {
    if (this.trigger.data('default-start-date')) {
      this.hiddenStartDateField.val(this.trigger.data('default-start-date'));
    }
    if (this.trigger.data('default-end-date')) {
      this.hiddenEndDateField.val(this.trigger.data('default-end-date'));
    }
  }

  public initializeTrigger(): void {

    this.cacheFormDom();
    this.setDefaults();

    const triggerHandler: Function = (e: JQueryEventObject): void => {
      e.preventDefault();
      this.show();
    };

    // @ts-ignore
    this.trigger.off('click').click(triggerHandler);
  }


}

export = new TimeslotDatesSelect();
