<?php

namespace Blueways\BwBookingmanager\Controller;

use Blueways\BwBookingmanager\Domain\Model\Calendar;
use Blueways\BwBookingmanager\Domain\Model\Dto\DateConf;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\JsonView;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

class ApiController extends ActionController
{

    /**
     * @var string
     */
    protected $defaultViewObjectName = JsonView::class;

    /**
     * @var \Blueways\BwBookingmanager\Domain\Repository\CalendarRepository
     * @inject
     */
    protected $calendarRepository;

    /**
     * @var \Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository
     * @inject
     */
    protected $timeslotRepository;

    /**
     * @var \Blueways\BwBookingmanager\Domain\Repository\EntryRepository
     * @inject
     */
    protected $entryRepository;

    /**
     * @var UriBuilder
     */
    protected $uriBuilder;

    public function calendarListAction()
    {
        $calendars = $this->calendarRepository->findAllIgnorePid();
        $uris = [];

        foreach ($calendars as $key => $calendar) {
            $uris[$key] = $this->uriBuilder
                ->setCreateAbsoluteUri(true)
                ->setTargetPageType(555)
                ->uriFor('calendarShow', ['calendar' => $calendar->getUid()], 'Api', 'BwBookingmanager', 'Pi1');
        }

        $this->view->assign('calendars', $calendars);
        $this->view->assign('uris', $uris);

        $this->view->setVariablesToRender(array('calendars', 'uris'));
    }

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function calendarShowAction(Calendar $calendar)
    {
        if(!$calendar) {
            $this->throwStatus(404, 'Calendar not found');
        }

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
        /** @var Calendar $calendar */
        $entries = $this->entryRepository->findInRange($calendar, $dateConf);
        $timeslots = $this->timeslotRepository->findInRange($calendar, $dateConf);

        // build render configuration
        $calendarConfiguration = new \Blueways\BwBookingmanager\Helper\RenderConfiguration($dateConf, $calendar);
        $calendarConfiguration->setTimeslots($timeslots);
        $calendarConfiguration->setEntries($entries);
        $configuration = $calendarConfiguration->getRenderConfiguration();

        $this->view->assignMultiple([
            'configuration' => $configuration,
        ]);

        $this->view->setVariablesToRender(array('configuration'));
    }
}
