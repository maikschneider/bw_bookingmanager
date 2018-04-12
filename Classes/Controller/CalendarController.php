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

    public function initializeAction() {
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
    public function showAction(\Blueways\BwBookingmanager\Domain\Model\Calendar $calendar)
    {
        $timeslots = $this->timeslotRepository->findByDateRange($calendar);
        $this->view->assign('calendar', $calendar);
        $this->view->assign('timeslots', $timeslots);
    }
}
