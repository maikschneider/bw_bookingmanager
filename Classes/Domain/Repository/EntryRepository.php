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
 * The repository for Entries
 */
class EntryRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    public function findInRange(
        \Blueways\BwBookingmanager\Domain\Model\Calendar $calendar,
        \DateTime $startDate,
        \DateTime $endDate
        ) {
        $query = $this->createQuery();
        $query->matching(
                $query->logicalAnd([
                    $query->equals('calendar', $calendar),
                    $query->greaterThanOrEqual('startDate', $startDate->format('Y-m-d 00:00:00')),
                    $query->lessThanOrEqual('startDate', $endDate->format('Y-m-d 23:59:59')),
                ]),
                $query->setOrderings(
                    [
                        'startDate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
                    ]
                )
            );

        return $query->execute();
    }

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
}
