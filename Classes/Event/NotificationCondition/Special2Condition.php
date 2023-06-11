<?php

namespace Blueways\BwBookingmanager\Event\NotificationCondition;

use Blueways\BwBookingmanager\Domain\Model\Entry;
use Blueways\BwBookingmanager\Domain\Model\Notification;

class Special2Condition implements NotificationConditionInterface
{
    public function doSend(Entry $entry, Notification $notification): bool
    {
        return $entry->getSpecial2();
    }
}
