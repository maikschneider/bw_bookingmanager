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
        \DateTime $startDate = null,
        \DateInterval $timeSpan = null
        ){
            // default startDate is now
            if(null === $startDate) $startDate = new \DateTime('now');

            // default endDate is startDate +1 month
            if(null === $timeSpan) {
                $timeSpan = new \DateInterval('P1M');
            }

            $maxStartDate = clone $startDate;
            $maxStartDate->add($timeSpan);

            $query = $this->createQuery();
            $query->matching(
                $query->logicalAnd(
                    $query->equals('calendar', $calendar),
                    $query->greaterThanOrEqual('startDate', $startDate->format('Y-m-d H:i:s')),
                    $query->lessThanOrEqual('startDate', $maxStartDate->format('Y-m-d H:i:s'))
                )
            );

            return $query->execute();
        }
}
