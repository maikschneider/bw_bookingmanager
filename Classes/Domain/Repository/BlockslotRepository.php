<?php

namespace Blueways\BwBookingmanager\Domain\Repository;

use Blueways\BwBookingmanager\Domain\Model\Dto\BlockslotCalendarEvent;

class BlockslotRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    public function getCalendarEventsInCalendar($calendars, \DateTime $startDate, \DateTime $endDate): array
    {
        $events = [];
        $blockslots = $this->findAllInRange($calendars, $startDate, $endDate);

        if (!$blockslots->count()) {
            return [];
        }

        foreach ($blockslots as $blockslot) {
            $events[] = BlockslotCalendarEvent::createFromEntity($blockslot);
        }
        return $events;
    }

    public function findAllInRange($calendars, \DateTime $startDate, \DateTime $endDate)
    {
        $query = $this->createQuery();
        $query->matching(
            $query->logicalAnd([
                $query->in('calendars.uid', $calendars),
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
