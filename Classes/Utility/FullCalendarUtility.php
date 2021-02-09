<?php

namespace Blueways\BwBookingmanager\Utility;

use Blueways\BwBookingmanager\Domain\Model\Dto\BackendCalendarViewState;
use Blueways\BwBookingmanager\Domain\Model\Dto\CalendarEvent;
use Blueways\BwBookingmanager\Domain\Model\Dto\EntryCalendarEvent;
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

    public function getEvents(BackendCalendarViewState $viewState): array
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $startDate = $viewState->getStartDate();
        $endDate = $viewState->getEndDate();
        $entryUid = $viewState->entryUid;

        $calendarRepository = $objectManager->get(CalendarRepository::class);
        $timeslotRepository = $objectManager->get(TimeslotRepository::class);
        $blockslotRepository = $objectManager->get(BlockslotRepository::class);
        $holidayRepository = $objectManager->get(HolidayRepository::class);
        $entryRepository = $objectManager->get(EntryRepository::class);
        $calendars = $calendarRepository->findAllByPid($viewState->pid);

        if (!$calendars && !$calendars->count()) {
            return [];
        }

        $timeslotEvents = $timeslotRepository->getCalendarEventsInCalendar($calendars, $startDate, $endDate);
        $blockslotEvents = $blockslotRepository->getCalendarEventsInCalendar($calendars, $startDate, $endDate);
        $holidayEvents = $holidayRepository->getCalendarEventsInCalendar($calendars, $startDate, $endDate);
        $entryEvents = $entryRepository->getCalendarEventsInCalendar($calendars, $startDate, $endDate);
        $virtualEvents = $this->getVirtualEvents($entryRepository, $entryEvents, $viewState);

        $events = array_merge([], $timeslotEvents, $blockslotEvents, $holidayEvents, $entryEvents, $virtualEvents);

        if ($entryUid) {
            return $this->getOutputForBackendModal($events, $viewState);
        }
        return $this->getOutputForBackendModule($events);
    }

    private function getVirtualEvents(
        EntryRepository $entryRepository,
        array $entryEvents,
        BackendCalendarViewState $viewState
    ): array {
        $entryUid = $viewState->entryUid;

        if (!$entryUid || !$viewState->getEntryStartDate() || !$viewState->getEntryEndDate()) {
            return [];
        }

        // NEW023820 => create new (virtual) EntryEvent
        if ($viewState->isNewModalView()) {
            $event = new EntryCalendarEvent();
            $event->setStart($viewState->getEntryStartDate());
            $event->setEnd($viewState->getEntryEndDate());
            $event->setUid($entryUid);
            $event->setCalendar($viewState->calendar);
            $event->setTimeslot($viewState->timeslot);
            return [$event];
        }
        // check if saved entry already in result
        $savedEntry = array_filter($entryEvents, static function ($event) use ($entryUid) {
            return $event->uid === (int)$entryUid;
        });
        // query entry, convert to event and add to result
        if (!count($savedEntry)) {
            $entry = $entryRepository->findByUid((int)$entryUid);
            if ($entry) {
                $event = EntryCalendarEvent::createFromEntity($entry);
                return [$event];
            }
        }

        return [];
    }

    private function getOutputForBackendModal(array $events, $viewState): array
    {
        $fullCalendarEvents = [];

        /** @var CalendarEvent $event */
        foreach ($events as $event) {
            $event->addBackendModalSettings($this->uriBuilder, $viewState);
            $fullCalendarEvents[] = $event->getFullCalendarOutput();
        }

        return $fullCalendarEvents;
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
}
