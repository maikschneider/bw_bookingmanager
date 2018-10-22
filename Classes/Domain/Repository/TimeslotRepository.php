<?php
namespace Blueways\BwBookingmanager\Domain\Repository;

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
 * The repository for Timeslots
 */
class TimeslotRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    public function findAllPossibleByDateRange(
        \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar,
        \DateTime $startDate,
        \DateTime $endDate
    ) {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalOr([
                // no repeatable events starting during date range
                $query->logicalAnd([
                    $query->contains('calendars', $calendar->getUid()),
                    $query->equals('repeatType', \Blueways\BwBookingmanager\Domain\Model\Timeslot::REPEAT_NO),
                    $query->greaterThanOrEqual('startDate', $startDate->format('Y-m-d 00:00:00')),
                    $query->lessThanOrEqual('startDate', $endDate->format('Y-m-d 23:59:59')),
                ]),
                // repeating events that end during or after date range
                // these events can be in the past and occur in range after repeat function
                $query->logicalAnd([
                    $query->contains('calendars', $calendar->getUid()),
                    $query->greaterThan('repeatType', \Blueways\BwBookingmanager\Domain\Model\Timeslot::REPEAT_NO),
                    $query->lessThan('startDate', $endDate->format('Y-m-d 23:59:59')),
                ]),
            ])
        );

        return $query->execute();
    }

    public function findInMonth(
        \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar,
        \DateTime $dayInMonth
    ) {
        $startDate = clone $dayInMonth;
        $startDate->modify('first day of this month');

        $endDate = clone $dayInMonth;
        $endDate->modify('last day of this month');
        $endDate->setTime(23, 59, 59);

        // @TODO: This function does not return any Timeslots when called by ajax request
        // use all timeslots as a fix
        // $timeslots = $this->findAllPossibleByDateRange($calendar, $startDate, $endDate);
        $timeslots = $calendar->getTimeslots();

        $timeslotManager = new \Blueways\BwBookingmanager\Helper\TimeslotManager($timeslots, $calendar, $startDate, $endDate);

        return $timeslotManager->getTimeslots();
    }

    public function findInWeek(
        \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar,
        \DateTime $dayInWeek
    ) {
        $startDate = clone $dayInWeek;
        $startDate->modify('tomorrow');
        $startDate->modify('last monday');

        $endDate = clone $dayInWeek;
        $endDate->modify('yesterday');
        $endDate->modify('next sunday');
        $endDate->setTime(23, 59, 59);

        // @TODO: This function does not return any Timeslots when called by ajax request
        // use all timeslots as a fix
        //$timeslots = $this->findAllPossibleByDateRange($calendar, $startDate, $endDate);
        $timeslots = $calendar->getTimeslots();
        $timeslotManager = new \Blueways\BwBookingmanager\Helper\TimeslotManager($timeslots, $calendar, $startDate, $endDate);

        return $timeslotManager->getTimeslots();
    }

    public function findInDays(
        \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar,
        \DateTime $startDate,
        int $days
    ) {
        $startDate = clone $startDate;
        $startDate->setTime(0, 0, 0);

        $endDate = clone $startDate;
        $endDate->modify('+' . $days . ' days');
        $endDate->setTime(23, 59, 59);

        // @TODO: This function does not return any Timeslots when called by ajax request
        // use all timeslots as a fix
        // $timeslots = $this->findAllPossibleByDateRange($calendar, $startDate, $endDate);
        $timeslots = $calendar->getTimeslots();
        $timeslotManager = new \Blueways\BwBookingmanager\Helper\TimeslotManager($timeslots, $calendar, $startDate, $endDate);

        return $timeslotManager->getTimeslots();
    }
}
