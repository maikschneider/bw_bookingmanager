<?php

namespace Blueways\BwBookingmanager\Event\NotificationCondition;

use Blueways\BwBookingmanager\Domain\Model\Entry;

interface NotificationConditionInterface
{
    public function doSend(Entry $entry): bool;
}
