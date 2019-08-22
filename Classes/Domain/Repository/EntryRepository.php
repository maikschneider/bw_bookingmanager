<?php

namespace Blueways\BwBookingmanager\Domain\Repository;

use Blueways\BwBookingmanager\Domain\Model\Dto\DateConf;

/***
 * This file is part of the "Booking Manager" Extension for TYPO3 CMS.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *  (c) 2018 Maik Schneider <m.schneider@blueways.de>, blueways
 ***/

/**
 * The repository for Entries
 */
class EntryRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * @param \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar
     * @param \Blueways\BwBookingmanager\Domain\Model\Dto\DateConf $dateConf
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findInRange(
        \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar,
        DateConf $dateConf
    ) {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd([
                $query->equals('calendar', $calendar),
                $query->greaterThanOrEqual('startDate', $dateConf->start),
                $query->lessThanOrEqual('startDate', $dateConf->end),
            ])
        );
        $query->setOrderings(
            [
                'startDate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
            ]
        );

        return $query->execute();
    }

    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
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
                    'startDate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
                ]
            )
        );
        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query->execute();
    }

    /**
     * @param $feUserId
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
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
                    'startDate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
                ]
            )
        );

        return $query->execute();
    }
}
