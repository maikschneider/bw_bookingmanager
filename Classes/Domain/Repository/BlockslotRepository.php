<?php

namespace Blueways\BwBookingmanager\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use Blueways\BwBookingmanager\Domain\Model\Dto\BlockslotCalendarEvent;

class BlockslotRepository extends Repository
{

    public function getCalendarEventsInCalendar($calendars, \DateTime $startDate, \DateTime $endDate): array
    {
        $events = [];
        $blockslots = $this->findAllInRange($calendars, $startDate, $endDate);

        if (!$blockslots && !$blockslots->count()) {
            return [];
        }

        $blockslotCalendarEventClass = $this->objectManager->get(BlockslotCalendarEvent::class);

        foreach ($blockslots as $blockslot) {
            $events[] = $blockslotCalendarEventClass::createFromEntity($blockslot);
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
