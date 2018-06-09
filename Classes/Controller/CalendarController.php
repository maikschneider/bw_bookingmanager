<?php
namespace Blueways\BwBookingmanager\Controller;

/***
 *
 * This file is part of the "Booking Manager" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2018 Maik Schneider <m.schneider@blueways.de>, blueways
 *
 ***/

/**
 * CalendarController
 */
class CalendarController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * calendarRepository
     *
     * @var \Blueways\BwBookingmanager\Domain\Repository\CalendarRepository
     * @inject
     */
    protected $calendarRepository = null;

    /**
     * Page uid
     *
     * @var int
     */
    protected $pageUid = 0;

    /**
     * timeslotRepository
     *
     * @var \Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository
     * @inject
     */
    protected $timeslotRepository = null;

    public function initializeAction()
    {
        $this->pageUid = (int)\TYPO3\CMS\Core\Utility\GeneralUtility::_GET('id');
        $this->timeslotRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository::class);
        $this->calendarRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Blueways\BwBookingmanager\Domain\Repository\CalendarRepository::class);
        
        // include javascript
        if($this->settings['ajax']['enable']){
            $pageRenderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Page\\PageRenderer');
            $jqueryJs = $GLOBALS['TSFE']->tmpl->getFileName($this->settings['javascript']['jquery']);
            $bookingmanagerJs = $GLOBALS['TSFE']->tmpl->getFileName($this->settings['javascript']['bookingmanager']);        
            if($jqueryJs) $pageRenderer->addJsFooterFile($jqueryJs, null, false, false, '', true);
            if($bookingmanagerJs) $pageRenderer->addJsFooterFile($bookingmanagerJs, null, true, false, '', true);
        }

        // override settings, if used as parameter from ajax call
        if ($this->request->hasArgument('settings')) {
            $newSettings = $this->request->getArgument('settings');
            $this->settings = $newSettings;
        }

    }

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $calendars = $this->calendarRepository->findAll();

        // set template
        if ($this->settings['templateLayout'] != 'default') {
            $this->view->setTemplate($this->settings['templateLayout']);
        }

        $this->view->assign('calendars', $calendars);
    }

    /**
     * action show
     *
     * @param \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     * @return void
     */
    public function showAction(
        \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar = NULL
    ) {
        // Calendar detail view gets calendar from settings
        if(!$calendar){
            $calendar = $this->calendarRepository->findByUid($this->settings['calendarPid']);
        }

        $day = $this->request->hasArgument('day') ? $this->request->getArgument('day') : null;
        $month = $this->request->hasArgument('month') ? $this->request->getArgument('month') : null;
        $year = $this->request->hasArgument('year') ? $this->request->getArgument('year') : null;

        $startDate = new \DateTime('now');
        $startDate->setTime(0,0,0);
        if ($day && $month && $year) {
            $startDate = $startDate->createFromFormat('j-n-Y H:i:s', $day . '-' . $month . '-' . $year . ' 00:00:00');
        }

        // set template
        if ($this->settings['templateLayout'] != 'default') {
            $this->view->setTemplate($this->settings['templateLayout']);
        }

        // get configuration array for template rendering
        $calendarConfiguration = new \Blueways\BwBookingmanager\Helper\RenderConfiguration($startDate);

        // get timeslots by date range
        switch($this->settings['dateRange']) {
            case 1:
                $timeslots = $this->timeslotRepository->findInWeek($calendar, $startDate);
                $calendarConfiguration->setTimeslots($timeslots);
                $configuration = $calendarConfiguration->getConfigurationForWeek();
            break;
            case 2:
                // @todo get $dayCount from flexform setting
                $days = 30;
                $timeslots = $this->timeslotRepository->findInDays($calendar, $startDate, $days);
                $calendarConfiguration->setTimeslots($timeslots);
                $configuration = $calendarConfiguration->getConfigurationForDays($days);
            break;
            default:
                $timeslots = $this->timeslotRepository->findInMonth($calendar, $startDate);
                $calendarConfiguration->setTimeslots($timeslots);
                $configuration = $calendarConfiguration->getConfigurationForMonth();
            break;            
        }

        $this->view->assign('page', $this->pageUid);
        $this->view->assign('calendar', $calendar);
        $this->view->assign('timeslots', $timeslots);
        $this->view->assign('configuration', $configuration);
    }

}
