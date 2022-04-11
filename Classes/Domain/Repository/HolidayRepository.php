<?php

namespace Blueways\BwBookingmanager\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use Blueways\BwBookingmanager\Domain\Model\Dto\HolidayCalendarEvent;
use DateTime;

/***
 * This file is part of the "Booking Manager" Extension for TYPO3 CMS.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *  (c) 2018 Maik Schneider <m.schneider@blueways.de>, blueways
 ***/

/**
 * The repository for Timeslots
 */
class HolidayRepository extends Repository
{

    public function getCalendarEventsInCalendar($calendars, \DateTime $startDate, \DateTime $endDate): array
    {
        $events = [];
        $holidays = $this->findInCalendars($calendars, $startDate, $endDate);

        if (!$holidays->count()) {
            return [];
        }

        $holidayCalendarEventClass = $this->objectManager->get(HolidayCalendarEvent::class);

        foreach ($holidays as $holiday) {
            $events[] = $holidayCalendarEventClass::createFromEntity($holiday);
        }
        return $events;
    }

    public function findInCalendars($calendars, DateTime $startDate, \DateTime $endDate)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd([
                $query->in('calendars.uid', $calendars),
                $query->greaterThanOrEqual('startDate', $startDate->getTimestamp()),
                $query->lessThanOrEqual('startDate', $endDate->getTimestamp()),
            ])
        );
        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query->execute();
    }
}
