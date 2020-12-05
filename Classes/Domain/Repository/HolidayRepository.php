<?php

namespace Blueways\BwBookingmanager\Domain\Repository;

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
class HolidayRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    public function findInCalendars(array $calendarUids, DateTime $startDate, \DateTime $endDate)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd([
                $query->in('calendars.uid', $calendarUids),
                $query->greaterThanOrEqual('startDate', $startDate->getTimestamp()),
                $query->lessThanOrEqual('startDate', $endDate->getTimestamp()),
            ])
        );
        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query->execute();
    }
}
