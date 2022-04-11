<?php

namespace Blueways\BwBookingmanager\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use Blueways\BwBookingmanager\Domain\Model\Calendar;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use Blueways\BwBookingmanager\Domain\Model\Dto\DateConf;
use Blueways\BwBookingmanager\Domain\Model\Dto\EntryCalendarEvent;
use DateTime;

/***
 * This file is part of the "Booking Manager" Extension for TYPO3 CMS.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *  (c) 2018 Maik Schneider <m.schneider@blueways.de>, blueways
 ***/

/**
 * The repository for Entries
 */
class EntryRepository extends Repository
{
    public function findInCalendars($calendars, DateTime $startDate, \DateTime $endDate)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd([
                $query->in('calendar.uid', $calendars),
                $query->logicalNot($query->lessThan('endDate', $startDate->getTimestamp())),
                $query->logicalNot($query->greaterThan('startDate', $endDate->getTimestamp()))
            ])
        );
        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query->execute();
    }

    /**
     * @param Calendar $calendar
     * @param DateConf $dateConf
     * @param bool $respectStoragePage
     * @return array|QueryResultInterface
     * @throws InvalidQueryException
     */
    public function findInRange(
        Calendar $calendar,
        DateConf $dateConf,
        bool $respectStoragePage = true
    ) {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd([
                $query->equals('calendar', $calendar),
                $query->logicalNot($query->lessThan('endDate', $dateConf->start->getTimestamp())),
                $query->logicalNot($query->greaterThan('startDate', $dateConf->end->getTimestamp()))
            ])
        );
        $query->setOrderings(
            [
                'startDate' => QueryInterface::ORDER_ASCENDING
            ]
        );

        $query->getQuerySettings()->setRespectStoragePage($respectStoragePage);

        return $query->execute();
    }

    public function getCalendarEventsInCalendar($calendars, DateTime $startDate, DateTime $endDate): array
    {
        $events = [];
        $entries = $this->findInCalendars($calendars, $startDate, $endDate);

        if (!$entries->count()) {
            return [];
        }

        $entryCalendarEventClass = $this->objectManager->get(EntryCalendarEvent::class);

        foreach ($entries as $entry) {
            $events[] = $entryCalendarEventClass::createFromEntity($entry);
        }
        return $events;
    }

    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return array|QueryResultInterface
     * @throws InvalidQueryException
     * @deprecated
     */
    public function findAllInRange(
        \DateTime $startDate,
        \DateTime $endDate
    ) {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd([
                $query->greaterThanOrEqual('startDate', $startDate->getTimestamp()),
                $query->lessThanOrEqual('startDate', $endDate->getTimestamp()),
            ]),
            $query->setOrderings(
                [
                    'startDate' => QueryInterface::ORDER_ASCENDING
                ]
            )
        );
        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query->execute();
    }

    /**
     * @param $feUserId
     * @return array|QueryResultInterface
     * @throws InvalidQueryException
     */
    public function getByUserId($feUserId)
    {
        $now = new \DateTime();
        $now->setTime(0, 0, 0);

        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd([
                $query->equals('feUser', $feUserId),
                $query->greaterThanOrEqual('startDate', $now->getTimestamp())
            ]),
            $query->setOrderings(
                [
                    'startDate' => QueryInterface::ORDER_ASCENDING
                ]
            )
        );

        return $query->execute();
    }
}
