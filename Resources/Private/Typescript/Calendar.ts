
import $ = require('jquery');


declare global {
  interface Window {
    TYPO3: any;
  }
}


/**
 * Module: TYPO3/CMS/BwBookingmanager/Calendar
 *
 * @exports TYPO3/CMS/BwBookingmanager/Calendar
 */
class Calendar {

  public init()
  {
    console.log('init')
  }

}

export = new Calendar().init();
