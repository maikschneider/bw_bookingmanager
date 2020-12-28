<?php

namespace Blueways\BwBookingmanager\Utility;

use Blueways\BwBookingmanager\Domain\Model\Dto\CalendarEvent;
use Blueways\BwBookingmanager\Domain\Repository\BlockslotRepository;
use Blueways\BwBookingmanager\Domain\Repository\CalendarRepository;
use Blueways\BwBookingmanager\Domain\Repository\EntryRepository;
use Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository;
use DateTime;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Lang\LanguageService;

class FullCalendarUtility
{

    /**
     * @var UriBuilder
     */
    protected $uriBuilder;

    /**
     * @var LanguageService
     */
    protected $llService;

    public function getEvents($pid, $start, $end): array
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var \Blueways\BwBookingmanager\Utility\TimeslotUtility $timeslotUtil */
        $timeslotUtil = $objectManager->get(TimeslotUtility::class);
        $startDate = new \DateTime($start);
        $endDate = new \DateTime($end);

        $calendarRepository = $objectManager->get(CalendarRepository::class);
        $timeslotRepository = $objectManager->get(TimeslotRepository::class);
        $blockslotRepository = $objectManager->get(BlockslotRepository::class);
        $entryRepository = $objectManager->get(EntryRepository::class);
        $calendars = $objectManager->get(ObjectStorage::class);
        $queryResult = $calendarRepository->findAllByPid($pid);
        if (null !== $queryResult) {
            foreach ($queryResult as $object) {
                $calendars->attach($object);
            }
        }
        //$timeslots = $timeslotUtil->getTimeslots($calendars, $startDate, $endDate);
        $timeslotEvents = $timeslotRepository->getCalendarEventsInCalendar($calendars, $startDate, $endDate);
        $blockslotEvents = $blockslotRepository->getCalendarEventsInCalendar($calendars, $startDate, $endDate);
        $holidays = $timeslotUtil->getHolidays();
        $entries = $entryRepository->findInCalendars(
            CalendarRepository::getUidsFromObjectStorage($calendars),
            $startDate,
            $endDate
        );


        $events = array_merge([], $timeslotEvents, $blockslotEvents);

        $events = $this->getOutputForBackendModule($events);

        return $events;
    }

    public function injectLlService(\TYPO3\CMS\Lang\LanguageService $llService)
    {
        $this->llService = $llService;
    }

    public function injectUriBuilder(\TYPO3\CMS\Backend\Routing\UriBuilder $uriBuilder)
    {
        $this->uriBuilder = $uriBuilder;
    }

    /**
     * @param CalendarEvent[] $events
     * @return array
     */
    private function getOutputForBackendModule(array $events): array
    {
        $fullCalendarEvents = [];

        foreach ($events as $event) {

            $event->translateTitle($this->llService);
            $fullCalendarEvents[] = $event->getFullCalendarOutput();
        }

        return $fullCalendarEvents;
    }
}
