<?php

namespace Blueways\BwBookingmanager\Utility;

use Blueways\BwBookingmanager\Domain\Model\Dto\CalendarEvent;
use Blueways\BwBookingmanager\Domain\Repository\BlockslotRepository;
use Blueways\BwBookingmanager\Domain\Repository\CalendarRepository;
use Blueways\BwBookingmanager\Domain\Repository\EntryRepository;
use Blueways\BwBookingmanager\Domain\Repository\HolidayRepository;
use Blueways\BwBookingmanager\Domain\Repository\TimeslotRepository;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
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

    public function getEvents($pid, $start, $end, $entryUid): array
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $startDate = new \DateTime($start);
        $endDate = new \DateTime($end);

        $calendarRepository = $objectManager->get(CalendarRepository::class);
        $timeslotRepository = $objectManager->get(TimeslotRepository::class);
        $blockslotRepository = $objectManager->get(BlockslotRepository::class);
        $holidayRepository = $objectManager->get(HolidayRepository::class);
        $entryRepository = $objectManager->get(EntryRepository::class);
        $calendars = $calendarRepository->findAllByPid($pid);

        $timeslotEvents = $timeslotRepository->getCalendarEventsInCalendar($calendars, $startDate, $endDate);
        $blockslotEvents = $blockslotRepository->getCalendarEventsInCalendar($calendars, $startDate, $endDate);
        $holidayEvents = $holidayRepository->getCalendarEventsInCalendar($calendars, $startDate, $endDate);
        $entryEvents = $entryRepository->getCalendarEventsInCalendar($calendars, $startDate, $endDate);

        $events = array_merge([], $timeslotEvents, $blockslotEvents, $holidayEvents, $entryEvents);

        if ($entryUid) {
            return $this->getOutputForBackendModal($events, $entryUid);
        }
        return $this->getOutputForBackendModule($events);
    }

    /**
     * @param CalendarEvent[] $events
     * @return array
     */
    private function getOutputForBackendModule(array $events): array
    {
        $fullCalendarEvents = [];

        foreach ($events as $event) {
            $event->addBackendEditActionLink($this->uriBuilder);
            $event->addBackendModuleToolTip();
            $fullCalendarEvents[] = $event->getFullCalendarOutput();
        }

        return $fullCalendarEvents;
    }

    public function injectUriBuilder(\TYPO3\CMS\Backend\Routing\UriBuilder $uriBuilder)
    {
        $this->uriBuilder = $uriBuilder;
    }

    private function getOutputForBackendModal(array $events, $entryUid)
    {
        $fullCalendarEvents = [];

        /** @var CalendarEvent $event */
        foreach ($events as $event) {
            $event->addBackendModalLink($this->uriBuilder);
            $event->addBackendModalIsSelectedEntryTimeslot($entryUid);
            $fullCalendarEvents[] = $event->getFullCalendarOutput();
        }

        return $fullCalendarEvents;
    }
}
