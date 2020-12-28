<?php

namespace Blueways\BwBookingmanager\Domain\Model\Dto;

use Blueways\BwBookingmanager\Domain\Model\Entry;
use Blueways\BwBookingmanager\Domain\Model\Ics;

class EntryCalendarEvent extends CalendarEvent
{

    protected string $color = '';

    protected string $prename = '';

    protected string $name = '';

    public static function createFromEntity(Entry $entry): EntryCalendarEvent
    {
        $title = $entry->getPrename() . ' ' . $entry->getName();

        $event = new self();
        $event->setTitle($title);
        $event->setStart($entry->getStartDate());
        $event->setEnd($entry->getEndDate());
        $event->prename = $entry->getPrename();
        $event->name = $entry->getName();

        return $event;
    }

    public function getIcsTitle(Ics $ics): string
    {
        return $ics->getEntryTitle();
    }
}
