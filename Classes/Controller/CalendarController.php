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
        )
    {
        $day = $this->request->hasArgument('day') ? $this->request->getArgument('day') : NULL;
        $month = $this->request->hasArgument('month') ? $this->request->getArgument('month') : NULL;
        $year = $this->request->hasArgument('year') ? $this->request->getArgument('year') : NULL;

        $startDate = new \DateTime('now');
        if($day && $month && $year){
            $startDate = $startDate->createFromFormat('j-n-Y', $day.'-'.$month.'-'.$year);
            $timeslots = $this->timeslotRepository->findInMonth($calendar, $startDate);
        }

        $timeslots = $this->timeslotRepository->findInMonth($calendar, $startDate);
        // $timeslots = $this->timeslotRepository->findInCurrentWeek($calendar);
        $this->view->assign('calendar', $calendar);
        $this->view->assign('timeslots', $timeslots);
    }

}
