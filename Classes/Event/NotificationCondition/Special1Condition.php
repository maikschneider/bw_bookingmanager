<?php

namespace Blueways\BwBookingmanager\Event\NotificationCondition;

use Blueways\BwBookingmanager\Domain\Model\Entry;

class Special1Condition implements NotificationConditionInterface
{

    public function doSend(Entry $entry): bool
    {
        return false;
    }
}
