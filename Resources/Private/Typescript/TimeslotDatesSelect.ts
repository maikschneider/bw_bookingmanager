/**
*
*
*/

/// <amd-dependency path='TYPO3/CMS/Core/Contrib/imagesloaded.pkgd.min' name='ImagesLoaded'>
/// <amd-dependency path='TYPO3/CMS/Backend/Modal' name='Modal'>

import $ = require('jquery');
import 'jquery-ui/draggable';
import 'jquery-ui/resizable';
declare const Modal: any;
declare const ImagesLoaded: any;

declare global {
  interface Window {
    TYPO3: any;
  }
}


/**
* Module: TYPO3/CMS/BwBookingManager/TimeslotDatesSelect
* @exports TYPO3/CMS/BwBookingManager/TimeslotDatesSelect
*/
class TimeslotDatesSelect {

  private trigger: JQuery;


  /**
   * @method init
   * @desc Initilizes the timeslot dates select button element
   * @private
   */
  private init(): void
  {

  }

  private show()
  {
    alert('lets do it!');
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
