<?php

namespace Blueways\BwBookingmanager\Domain\Model\Dto;

use Blueways\BwBookingmanager\Domain\Model\Entry;

class EntryCalendarEvent extends CalendarEvent
{

    protected string $color = '';

    public static function createFromEntity(Entry $entry): EntryCalendarEvent
    {
        $title = $entry->getPrename() . ' ' . $entry->getName();

        $event = new self();
        $event->setTitle($title);
        $event->setStart($entry->getStartDate());
        $event->setEnd($entry->getEndDate());

        return $event;
    }
}
