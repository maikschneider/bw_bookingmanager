<?php

namespace Blueways\BwBookingmanager\Utility;

use Blueways\BwBookingmanager\Domain\Repository\CalendarRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class FullCalendarUtility
{

    public function getEvents($pid, $start, $end)
    {
        $events = [];

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
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

        /** @var \Blueways\BwBookingmanager\Domain\Model\Timeslot $timeslot */
        foreach ($timeslots as $timeslot) {
            $events[] = $timeslot->getFullCalendarEvent();
        }

        return $events;
    }
}
