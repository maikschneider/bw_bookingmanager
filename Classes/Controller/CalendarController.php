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
     * timeslotRepository
     *
     * @var \Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository
     * @inject
     */
    protected $timeslotRepository = null;

    public function initializeAction()
    {
        $this->timeslotRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository::class);
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
        \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
    ) {
        $day = $this->request->hasArgument('day') ? $this->request->getArgument('day') : null;
        $month = $this->request->hasArgument('month') ? $this->request->getArgument('month') : null;
        $year = $this->request->hasArgument('year') ? $this->request->getArgument('year') : null;

        $startDate = new \DateTime('now');
        if ($day && $month && $year) {
            $startDate = $startDate->createFromFormat('j-n-Y', $day . '-' . $month . '-' . $year);
        }

        // set template
        if ($this->settings['templateLayout'] != 'default') {
            $this->view->setTemplate($this->settings['templateLayout']);
        }

        // get timeslots by date range
        switch($this->settings['dateRange']) {
            case 1:
                $timeslots = $this->timeslotRepository->findInWeek($calendar, $startDate);
            break;
            default:
                $timeslots = $this->timeslotRepository->findInMonth($calendar, $startDate);
            break;            
        }
        
        $this->view->assign('calendar', $calendar);
        $this->view->assign('timeslots', $timeslots);
    }

}
