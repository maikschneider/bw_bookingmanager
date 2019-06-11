<?php

namespace Blueways\BwBookingmanager\Controller;

use Blueways\BwBookingmanager\Domain\Model\Dto\DateConf;
use Blueways\BwBookingmanager\Domain\Repository\CalendarRepository;
use Blueways\BwBookingmanager\Domain\Repository\EntryRepository;
use Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository;

/**
 * Calendar Controller for list, show view of calendar entries
 * PHP version 7.2
 *
 * @package  BwBookingManager
 * @author   Maik Schneider <m.schneider@blueways.de>
 * @license  MIT https: //opensource.org/licenses/MIT
 * @version  GIT: <git_id />
 * @link     http: //www.blueways.de
 */
class CalendarController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * CalendarRepository
     *
     * @var    \Blueways\BwBookingmanager\Domain\Repository\CalendarRepository
     * @inject
     */
    protected $calendarRepository = null;

    /**
     * @var \Blueways\BwBookingmanager\Domain\Repository\EntryRepository
     * @inject
     */
    protected $entryRepository;

    /**
     * timeslotRepository
     *
     * @var    \Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository
     * @inject
     */
    protected $timeslotRepository = null;

    public function initializeAction()
    {
        $this->entryRepository = $this->objectManager->get(EntryRepository::class);
        $this->timeslotRepository = $this->objectManager->get(TimeslotRepository::class);
        $this->calendarRepository = $this->objectManager->get(CalendarRepository::class);
    }

    /**
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function listAction()
    {
        if ((int)$this->settings['mode'] === 1) {
            $this->forward('show');
        }

        if ((int)$this->settings['mode'] === 2) {
            $this->forward('new', 'Entry');
        }

        $calendars = $this->calendarRepository->findAll();

        $this->view->setTemplate($this->settings['template']['calendar']['list']);

        $this->view->assign('calendars', $calendars);
    }

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Calendar|null $calendar
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function showAction(
        \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar = null
    ) {
        // create date from arguments and configuration
        $startDate = new \DateTime('now');
        $startDate->setTime(0, 0, 0);
        $day = $this->request->hasArgument('day') ? $this->request->getArgument('day') : null;
        $month = $this->request->hasArgument('month') ? $this->request->getArgument('month') : null;
        $year = $this->request->hasArgument('year') ? $this->request->getArgument('year') : null;
        if ($day && $month && $year) {
            $startDate = $startDate->createFromFormat('j-n-Y H:i:s', $day . '-' . $month . '-' . $year . ' 00:00:00');
        }
        $dateConf = new DateConf((int)$this->settings['dateRange'], $startDate);

        // query calendar, entries, timeslots
        $calendar = $calendar ?: $this->calendarRepository->findByUid((int)$this->settings['calendarPid']);
        $entries = $this->entryRepository->findInRange($calendar, $dateConf);
        $timeslots = $this->timeslotRepository->findInRange($calendar, $dateConf);

        // build render configuration
        $calendarConfiguration = new \Blueways\BwBookingmanager\Helper\RenderConfiguration($dateConf, $calendar);
        $calendarConfiguration->setTimeslots($timeslots);
        $calendarConfiguration->setEntries($entries);
        $configuration = $calendarConfiguration->getRenderConfiguration();

        $this->view->setTemplate($this->settings['template']['calendar']['show']);
        $this->view->assignMultiple([
            'calendar' => $calendar,
            'timeslots' => $timeslots,
            'configuration' => $configuration,
            'entries' => $entries
        ]);
    }
}
