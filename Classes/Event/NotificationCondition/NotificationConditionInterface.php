<?php

namespace Blueways\BwBookingmanager\Event\NotificationCondition;

use Blueways\BwBookingmanager\Domain\Model\Entry;
use Blueways\BwBookingmanager\Domain\Model\Notification;

interface NotificationConditionInterface
{
    public function doSend(Entry $entry, Notification $notification): bool;
}
