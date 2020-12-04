<?php

namespace Blueways\BwBookingmanager\Domain\Repository;

class BlockslotRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    public function findAllInRange(array $calendarUids, \DateTime $startDate, \DateTime $endDate)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd([
                $query->in('calendars.uid', $calendarUids),
                $query->logicalNot(
                    $query->logicalOr([
                        $query->lessThanOrEqual('endDate', $startDate->getTimestamp()),
                        $query->greaterThanOrEqual('startDate', $endDate->getTimestamp()),
                    ])
                ),
            ])
        );

        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query->execute();
    }
}
