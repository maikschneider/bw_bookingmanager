<?php

namespace Blueways\BwBookingmanager\Domain\Repository;

/***
 * This file is part of the "Booking Manager" Extension for TYPO3 CMS.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *  (c) 2018 Maik Schneider <m.schneider@blueways.de>, blueways
 ***/

/**
 * The repository for Timeslots
 */
class TimeslotRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     * @param \Blueways\BwBookingmanager\Domain\Model\Dto\DateConf $dateConf
     * @return array|\Blueways\BwBookingmanager\Domain\Model\Timeslot[]|\TYPO3\CMS\Extbase\Persistence\ObjectStorage
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     */
    public function findInRange($calendar, \Blueways\BwBookingmanager\Domain\Model\Dto\DateConf $dateConf)
    {
        $timeslots = $this->findAllPossibleByDateRange($calendar, $dateConf->start, $dateConf->end);
        $timeslotManager = new \Blueways\BwBookingmanager\Helper\TimeslotManager($timeslots, $calendar,
            $dateConf->start,
            $dateConf->end);
        $timeslots = $timeslotManager->getTimeslots();

        return $timeslots;
    }

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
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
                    $query->greaterThanOrEqual('startDate', $startDate->getTimestamp()),
                    $query->lessThanOrEqual('startDate', $endDate->getTimestamp()),
                ]),
                // repeating events that end during or after date range
                // these events can be in the past and occur in range after repeat function
                $query->logicalAnd([
                    $query->contains('calendars', $calendar->getUid()),
                    $query->greaterThan('repeatType', \Blueways\BwBookingmanager\Domain\Model\Timeslot::REPEAT_NO),
                    $query->lessThan('startDate', $endDate->getTimestamp()),
                    $query->logicalOr([
                        $query->equals('repeatEnd', 0),
                        $query->lessThanOrEqual('repeatEnd', $startDate->getTimestamp())
                    ])
                ]),
            ])
        );

        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query->execute();
    }
}
