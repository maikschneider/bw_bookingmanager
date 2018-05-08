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
    public function findByDateRange(
        \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar,
        \DateTime $startDate,
        \DateTime $endDate
        ){
            $query = $this->createQuery();
            $query->matching(
                $query->logicalOr([
                    // no repeatable events starting during date range
                    $query->logicalAnd([
                        $query->equals('repeatType', \Blueways\BwBookingmanager\Domain\Model\Timeslot::REPEAT_NO),
                        $query->greaterThanOrEqual('startDate', $startDate->format('Y-m-d 00:00:00')),
                        $query->lessThanOrEqual('startDate', $endDate->format('Y-m-d 23:59:59')),
                    ]),
                    // repeating events that end during or after date range
                    $query->logicalAnd([
                        $query->greaterThan('repeatType', \Blueways\BwBookingmanager\Domain\Model\Timeslot::REPEAT_NO),
                        $query->greaterThan('endDate', $startDate->format('Y-m-d H:i:s'))
                    ])
                ])
            );

            return $query->execute();
        }

    public function findInCurrentMonth(
        \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
        ){
        $today = new \DateTime('now');
        return $this->findInMonth($calendar, $today);
    }

    public function findInCurrentWeek(
        \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
        ){
        $today = new \DateTime('now');
        return $this->findInWeek($calendar, $today);
    }

    public function findInMonth(
        \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar,
        \DateTime $dayInMonth
    ){
        $startDate = clone $dayInMonth;
        $startDate->modify('first day of this month');

        $endDate = clone $dayInMonth;
        $endDate->modify('last day of this month');

        return $this->findByDateRange($calendar, $startDate, $endDate);
    }

    public function findInWeek(
        \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar,
        \DateTime $dayInWeek
    ){
        $startDate = clone $dayInWeek;
        $startDate->modify('tomorrow');
        $startDate->modify('last monday');

        $endDate = clone $dayInWeek;
        $endDate->modify('yesterday');
        $endDate->modify('next sunday');

        return $this->findByDateRange($calendar, $startDate, $endDate);
    }
}
