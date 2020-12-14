<?php

namespace Blueways\BwBookingmanager\Utility;

use Blueways\BwBookingmanager\Domain\Repository\CalendarRepository;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class FullCalendarUtility
{

    public function getEvents($pid, $start, $end): array
    {
        $events = [];

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var \Blueways\BwBookingmanager\Utility\TimeslotUtility $timeslotUtil */
        $timeslotUtil = $objectManager->get(TimeslotUtility::class);
        $startDate = new \DateTime($start);
        $endDate = new \DateTime($end);

        $calendarRepository = $objectManager->get(CalendarRepository::class);
        $calendars = $objectManager->get(ObjectStorage::class);
        $queryResult = $calendarRepository->findAllByPid($pid);
        if (null !== $queryResult) {
            foreach ($queryResult as $object) {
                $calendars->attach($object);
            }
        }
        $timeslots = $timeslotUtil->getTimeslots($calendars, $startDate, $endDate);
        $blockslots = $timeslotUtil->getBlockslots();
        $holidays = $timeslotUtil->getHolidays();
        $entries = $timeslotUtil->getEntries();
        /** @var \TYPO3\CMS\Backend\Routing\UriBuilder $uriBuilder */
        $uriBuilder = $objectManager->get(UriBuilder::class);

        /** @var \Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslot */
        foreach ($timeslots as $timeslot) {
            if ($timeslot->getIsBookable()) {
                $event = $timeslot->getFullCalendarEvent();
                $event['url'] = $uriBuilder->buildUriFromRoute('record_edit', $event['backendUrl'])->__toString();
                $events[] = $event;
            }
        }

        foreach ($blockslots as $blockslot) {
            $events[] = $blockslot->getFullCalendarEvent();
        }

        foreach ($holidays as $holiday) {
            $events[] = $holiday->getFullCalendarEvent();
        }

        foreach ($entries as $entry) {
            $event = $entry->getFullCalendarEvent();
            $event['url'] = $uriBuilder->buildUriFromRoute('record_edit', $event['backendUrl'])->__toString();
            $events[] = $event;
        }

        return $events;
    }
}
