<?php

namespace Blueways\BwBookingmanager\Event;

use Blueways\BwBookingmanager\Domain\Model\Entry;

final class AfterEntryCreationEvent
{
    private Entry $entry;

    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
    }

    public function getEntry(): Entry
    {
        return $this->entry;
    }

    public function setEntry(Entry $entry): void
    {
        $this->entry = $entry;
    }
}
